<?php

namespace App\Controller;

use App\Entity\ListItem;
use App\Entity\ProductPlacement;
use App\Entity\ShoppingList;
use App\Entity\ShoppingSession;
use App\Enum\PlacementType;
use App\Form\FoodItemGroupPlacementType;
use App\Form\ShoppingListType;
use App\Repository\FoodItemRepository;
use App\Repository\ListItemRepository;
use App\Repository\NodeRepository;
use App\Repository\ProductPlacementRepository;
use App\Repository\ShoppingListRepository;
use App\Repository\ShoppingSessionRepository;
use App\Repository\SupermarketRepository;
use App\Service\MapBuilder;
use App\Service\PathFinder;
use App\Service\PlacementResolver;
use App\Service\RoutedListItemDto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ShoppingListController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET', 'POST'])]
    public function home(
        Request $request, 
        EntityManagerInterface $em, 
        ShoppingListRepository $shoppingListRepository, 
        ListItemRepository $listItemRepository,
        SupermarketRepository $supermarketRepository,
    ): Response
    {
        $shoppingList = $shoppingListRepository->findOneByUser($this->getUser());
        if(!$shoppingList) {
            $shoppingList = new ShoppingList();
            $shoppingList->setDateCreated(new \DateTimeImmutable());
            $shoppingList->setUser($this->getUser());
            $shoppingList->setQuickAddList(false);
            $em->persist($shoppingList);
            $em->flush();
        }

        // if we dont render the picked items, doctrine will delete them when we flush
        //so we cache them here and manually add them back afer form submission
        $picked = $shoppingList->getListItems()->filter(fn($item) => $item->getPickedAt() !== null)->toArray();

        $form = $this->createForm(ShoppingListType::class, $shoppingList, [
            'unpickedItems' => $listItemRepository->findUnpickedByShoppingList($shoppingList),
        ]);
        $form->handleRequest($request);

        
        // For AJAX save: the form isn’t submitted via normal POST
        if ($request->isXmlHttpRequest() && !$form->isSubmitted()) {
            $form->submit($request->request->all()); // Manually submit the form with the posted data
        }

        if ($form->isSubmitted() && $form->isValid()) {
            // Merge picked items back into the entity
            foreach ($picked as $item) {
                $shoppingList->addListItem($item);
            }

            $em->flush();

            if ($request->request->get('intent') === 'go_shopping') {
                return $this->redirectToRoute('app_shopping_list_active', [
                    'id' => $shoppingList->getId(),
                ]);
            }

            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }
        }

        $quickAddItems = [];
        foreach($shoppingListRepository->findQuickAddListByUser($this->getUser())->getListItems() ?? [] as $item) {
            $quickAddItems[] = [
                'foodId' => $item->getFoodItem()->getId(),
                'label' => $item->getFoodItem()->getName(),
                'quantity' => $item->getQuantity(),
                'notes' => $item->getNotes(),
            ];
        }

        return $this->render('shopping_list/edit.html.twig', [
            'quickAddItems' => $quickAddItems,
            'form' => $form->createView(),
            'supermarkets' => $supermarketRepository->findActiveMappedSupermarkets($this->getUser()),
        ]);
    }


    #[Route('/active/{supermarketId}/{showModal}', name: 'app_shopping_list_active', methods: ['GET'], defaults: ['showModal' => false])]
    public function activeList(
        ShoppingListRepository $shoppingListRepository,
        PathFinder $pathFinder,
        ProductPlacementRepository $productPlacementRepository,
        ShoppingSessionRepository $shoppingSessionRepository,
        ListItemRepository $listItemRepository,
        FoodItemRepository $foodItemRepository,
        EntityManagerInterface $em,
        SupermarketRepository $supermarketRepository,
        MapBuilder $mapBuilder,
        PlacementResolver $placementResolver,
        Request $request,
        int $supermarketId,
        bool $showModal,
    ): Response {
        $shoppingList = $shoppingListRepository->findOneByUser($this->getUser());
        $supermarket = $supermarketRepository->find($supermarketId);

        // find any open sessions for this list and supermarket, and close them (we open a new session once the first item is picked)
        $openSessions = $shoppingSessionRepository->findRecentActiveByListAndSupermarket($shoppingList, $supermarket);
        if($openSessions) {
            foreach($openSessions as $session) {
                $lastPicked = $listItemRepository->findLastPickedInSession($session);
                $session->setCompletedAt($lastPicked?->getPickedAt() ?? new \DateTimeImmutable());
            }
        }

        $em->flush();

        // Now check for a current session, and get the current node if it exists, so we can start the path from there
        $currentSession = $shoppingSessionRepository->findCurrentSession($shoppingList->getId(), $supermarket->getId());
        $currentNodeId = $currentSession ? $currentSession->getCurrentNode()->getId() : null;
        
        foreach ($listItemRepository->findUnpickedByShoppingList($shoppingList) as $listItem) {
            $placement = $productPlacementRepository->findOneBy([
                'foodItem' => $listItem->getFoodItem(),
                'supermarket' => $supermarket,
            ]);

            if(!$placement){
                $inferredPlacement = $placementResolver->inferPlacement($listItem->getFoodItem()->getId(), $supermarket->getId());
                if(!$inferredPlacement) {
                    continue;
                }
                
                $newPlacement = new ProductPlacement();
                $newPlacement->setFoodItem($listItem->getFoodItem());
                $newPlacement->setSupermarket($supermarket);
                $newPlacement->setType(PlacementType::GROUP);
                $newPlacement->setEdge($inferredPlacement['edge']);
                $newPlacement->setAisleSide($inferredPlacement['aisleSide']);
                $em->persist($newPlacement);
            }
        }

        $em->flush();
        
        $orderedList = $pathFinder->orderShoppingList($shoppingList, $supermarket, $currentNodeId);
        if(count($orderedList) === 0) {
            return $this->redirectToRoute('app_home');
        }

        if($showModal){
            $unplacedItemCount = count(array_filter($orderedList, fn($item) => $item->placement === null));
            if($unplacedItemCount > 0) {
                $this->addFlash('warning', '<i class="bi bi-exclamation-triangle-fill placement-icon missing"></i>' . $unplacedItemCount . ' item(s) have not been mapped. Place them to update your list order!');
            }
        }

        $placementStatus = [];
        foreach ($orderedList as $dto) {
            $placementStatus[$dto->item->getId()] = $dto->item->getPlacementStatus()->value;
        }

        $segments = array_map(fn (RoutedListItemDto $dto) => [
            'itemId' => $dto->item->getId(),
            'placementEdgeId' => $dto->placement?->getEdge()->getId(),
            'targetNodeId' => $dto->targetNodeId,
            'pathNodes' => $dto->path, // ordered list of node IDs
        ], $orderedList);

        $foodItems = $foodItemRepository->findWithPlacementInSupermarket($supermarket);
        $form = $this->createForm(FoodItemGroupPlacementType::class, null, [
            'food_items' => $foodItems,
            'action' => $this->generateUrl('app_product_placement_group'), // submit target
            'method' => 'POST', // or GET if you prefer
        ]);
        
        return $this->render('shopping_list/active.html.twig', [
            'form' => $form->createView(),
            'orderedList' => $orderedList,
            'shoppingList' => $shoppingList,
            'supermarket' => $supermarket,
            'placementStatus' => $placementStatus,
            'nodes' => $mapBuilder->getAllNodes($supermarket),
            'edges' => $mapBuilder->getAllEdges($supermarket),
            'shelves' => $mapBuilder->getAllShelves($supermarket),
            'viewBox' => $mapBuilder->getViewBox($supermarket),
            'segments' => $segments,
        ]);
    }

    // maybe needs to go in list item controller later
    #[Route('/shoppinglist/ajax/pick', name: 'app_shopping_pick', methods: ['POST'])]
    public function pick(
        Request $request, 
        EntityManagerInterface $em, 
        ShoppingSessionRepository $shoppingSessionRepository, 
        ListItemRepository $listItemRepository,
        SupermarketRepository $supermarketRepository,
        NodeRepository $nodeRepository,
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $listItemId = $data['listItemId'] ?? null;
        $supermarketId = $data['supermarketId'] ?? null;
        $currentNodeId = $data['currentNodeId'] ?? null;

        if (!$listItemId || !$supermarketId) {
            return new JsonResponse(['error' => 'Invalid payload'], 400);
        }

        // fetch entities
        $item = $listItemRepository->find($listItemId);
        $supermarket = $supermarketRepository->find($supermarketId);
        $session = $shoppingSessionRepository->findCurrentSession($item->getShoppingList()->getId(), $supermarket->getId());

        // Create session if none exists
        if (!$session) {
            $session = new ShoppingSession();
            $session->setShoppingList($item->getShoppingList());
            $session->setStartedAt(new \DateTimeImmutable());
            $session->setSupermarket($supermarket);
            $session->setCurrentNode($supermarket->getEntranceNode());
            $em->persist($session);
        }

        // Update current node if provided (if item is being picked in order)
        // We will use that current node to render the map from the correct location in case of reloads etc
        if($currentNodeId) {
            $currentNode = $nodeRepository->find($currentNodeId);
            $session->setCurrentNode($currentNode);
        }

        // Mark item picked
        $item->setPickedAt(new \DateTimeImmutable());
        $item->setSession($session);

        $em->flush();

        return new JsonResponse(['ok' => true]);
    }

    // maybe needs to go in list item controller later
    #[Route('/shoppinglist/unpick', name: 'app_shopping_unpick', methods: ['POST'])]
    public function unpick(
        Request $request,
        ListItemRepository $listItemRepository, 
        EntityManagerInterface $em, 
        ShoppingSessionRepository $shoppingSessionRepository,
    )
    {
        $shoppingListId = $request->request->get('shoppingListId');
        $supermarketId  = $request->request->get('supermarketId');

        $session = $shoppingSessionRepository->findCurrentSession($shoppingListId, $supermarketId);
        if(!$session){
            return $this->redirectToRoute('app_shopping_list_active', ['supermarketId' => $supermarketId]);
        }

        $lastPicked = $listItemRepository->findLastPickedInSession($session);
        if(!$lastPicked){
            return $this->redirectToRoute('app_shopping_list_active', ['supermarketId' => $supermarketId]);
        }

        $lastPicked->setPickedAt(null);
        $em->flush();

        return $this->redirectToRoute('app_shopping_list_active', ['supermarketId' => $supermarketId]);
    }
}