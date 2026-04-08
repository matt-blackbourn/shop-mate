<?php

namespace App\Controller;

use App\Entity\ListFoodOrder;
use App\Entity\ListMember;
use App\Entity\ProductPlacement;
use App\Entity\ShoppingList;
use App\Entity\ShoppingSession;
use App\Entity\Supermarket;
use App\Enum\ListMemberRole;
use App\Enum\ListType;
use App\Enum\PlacementType;
use App\Enum\SupermarketType;
use App\Form\FoodItemGroupPlacementType;
use App\Form\ShoppingListType;
use App\Repository\FoodItemRepository;
use App\Repository\ListFoodOrderRepository;
use App\Repository\ListInviteRepository;
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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/shoppinglist')]
final class ShoppingListController extends AbstractController
{
    #[Route('/edit/{id}', name: 'app_shoppinglist_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        EntityManagerInterface $em, 
        ListItemRepository $listItemRepository,
        SupermarketRepository $supermarketRepository,
        ShoppingList $shoppingList,
        ShoppingListRepository $shoppingListRepository,
        ListInviteRepository $inviteRepository,
    ): Response
    {
        // if we dont render the picked items, doctrine will delete them when we flush
        // so we cache them here and manually add them back afer form submission
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

            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }
        }

        $itemGroups = [];
        foreach($shoppingListRepository->findItemGroupsByUser($this->getUser()) as $group) {
            foreach($group->getListItems() as $item){
                $itemGroups[$group->getId()]['name'] = $group->getName();
                $itemGroups[$group->getId()]['items'][] = [
                    'foodId' => $item->getFoodItem()->getId(),
                    'label' => $item->getFoodItem()->getName(),
                    'quantity' => $item->getQuantity(),
                    'notes' => $item->getNotes(),
                ];
            }
        }

        $invites = $inviteRepository->findBy([
            'email' => $this->getUser()->getEmail(),
            'status' => 'pending'
        ]);
        $invite = count($invites) > 0 ? $invites[0] : null;

        $supermarkets = $supermarketRepository->findActiveMappedSupermarkets($this->getUser());
        $noSupermaketOption = new Supermarket();
        $noSupermaketOption->setId(0);
        $noSupermaketOption->setType(SupermarketType::NONE);

        if($this->getUser()->getDefaultSupermarket()){
            array_splice($supermarkets, 1, 0, [$noSupermaketOption]);
        } else {
            array_unshift($supermarkets, $noSupermaketOption);
        }
        
        return $this->render('shopping_list/edit.html.twig', [
            'itemGroups' => $itemGroups,
            'form' => $form->createView(),
            'supermarkets' => $supermarkets,
            'shoppingList' => $shoppingList,
            'invite' => $invite,
            'availableLists' => $shoppingListRepository->findForUser($this->getUser()),
        ]);
    }

    #[Route('/active/{listId}/{supermarketId}/{showModal}', name: 'app_shopping_list_active', methods: ['GET'], defaults: ['showModal' => false])]
    public function active(
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
        ListFoodOrderRepository $listFoodOrderRepository,
        int $supermarketId,
        int $listId,
        bool $showModal,
    ): Response {
        $shoppingList = $shoppingListRepository->find($listId);
        $supermarket = $supermarketId ? $supermarketRepository->find($supermarketId) : null;
        
        // find any open sessions for this list and supermarket, and close them (we open a new session once the first item is picked)
        $openSessions = $shoppingSessionRepository->findRecentActiveByListAndSupermarket($listId, $supermarketId);
        if($openSessions) {
            foreach($openSessions as $session) {
                $lastPicked = $listItemRepository->findLastPickedInSession($session);
                $session->setCompletedAt($lastPicked?->getPickedAt() ?? new \DateTimeImmutable());
            }
        }

        $em->flush();

        // fallback behaviour (no routing, no map, no placements)
        if (!$supermarket) {
            $supermarket = $supermarketRepository->findWithMostPlacements();
            $orderedList = $pathFinder->orderShoppingList($shoppingList, $supermarket, null, true);
            if(count($orderedList) === 0) {
                $this->addFlash('warning', 'No items in your shopping list!');
                return $this->redirectToRoute('app_shoppinglist_edit', ['id' => $listId]);
            }

            // create an initial order if there is no existing order for this list
            $listFoodOrder = $listFoodOrderRepository->findBy(['list' => $shoppingList]);
            if(count($listFoodOrder) === 0) {
                $position = 1000;
                foreach($orderedList as $dto) {
                    $order = new ListFoodOrder();
                    $order->setList($shoppingList);
                    $order->setFoodItem($dto->item->getFoodItem());
                    $order->setPosition($position);
                    $em->persist($order);
                    $position += 1000;
                }
                $em->flush();
            } else {
                $orderedItems = $listItemRepository->findUnpickedByShoppingListInOrder($shoppingList);

                // Get ordered list in the correct format for the template
                $orderedList = [];
                foreach($orderedItems as $item) {   
                    $orderedList[] = new RoutedListItemDto(
                        item: $item,
                        placement: null,
                        targetNodeId: null,
                        distanceFromPrevious: 0,
                        path: []
                    );
                }
            }

            return $this->render('shopping_list/active.html.twig', [
                'form' => null,
                'orderedList' => $orderedList,
                'shoppingList' => $shoppingList,
                'supermarket' => null,
                'placementStatus' => [],
                'nodes' => [],
                'edges' => [],
                'shelves' => [],
                'viewBox' => null,
                'segments' => [],
                'hasSession' => $shoppingSessionRepository->findCurrentSession($shoppingList->getId(), $supermarketId) !== null,
                'initialOrder' => array_map(fn($item) => $item->item->getFoodItem()->getId(), $orderedList),
            ]);
        }


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
            $this->addFlash('warning', 'No items in your shopping list!');
            return $this->redirectToRoute('app_shoppinglist_edit', ['id' => $listId]);
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
            'method' => 'POST',
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
            'hasSession' => $shoppingSessionRepository->findCurrentSession($shoppingList->getId(), $supermarketId) !== null,
            'initialOrder' => [],
        ]);
    }

    // maybe needs to go in list item controller later
    #[Route('/ajax/pick', name: 'app_shopping_pick', methods: ['POST'])]
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

        if (!$listItemId) {
            return new JsonResponse(['error' => 'Invalid payload'], 400);
        }

        // fetch entities
        $item = $listItemRepository->find($listItemId);
        $supermarket = $supermarketRepository->find($supermarketId);
        $session = $shoppingSessionRepository->findCurrentSession($item->getShoppingList()->getId(), $supermarketId);

        // Create session if none exists
        if (!$session) {
            $session = new ShoppingSession();
            $session->setShoppingList($item->getShoppingList());
            $session->setStartedAt(new \DateTimeImmutable());
            $session->setSupermarket($supermarket);
            $session->setCurrentNode($supermarket?->getEntranceNode());
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
    #[Route('/unpick', name: 'app_shopping_unpick', methods: ['POST'])]
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
            return $this->redirectToRoute('app_shopping_list_active', [
                'supermarketId' => $supermarketId, 
                'listId' => $shoppingListId,
            ]);
        }

        $lastPicked = $listItemRepository->findLastPickedInSession($session);
        if(!$lastPicked){
            return $this->redirectToRoute('app_shopping_list_active', [
                'supermarketId' => $supermarketId, 
                'listId' => $shoppingListId,
            ]);
        }

        $lastPicked->setPickedAt(null);
        $em->flush();

        return $this->redirectToRoute('app_shopping_list_active', [
            'supermarketId' => $supermarketId,
            'listId' => $shoppingListId,
        ]);
    }

    
    #[Route('/default', name: 'app_shopping_list_set_default', methods: ['POST'])]
    public function setDefault(
        Request $request,
        EntityManagerInterface $em,
        ShoppingListRepository $repo,
    ): Response {
        $user = $this->getUser();
        $id = $request->request->get('list_id');

        $list = $repo->find($id);
        if (!$list) {
            throw $this->createNotFoundException();
        }

        $user->setDefaultList($list);
        $em->flush();

        return $this->redirectToRoute('app_shoppinglist_edit', ['id' => $request->request->get('current_list')]);
    }
    
    #[Route('/add-group', name: 'app_shopping_list_item_group', methods: ['POST'])]
    public function addItemGroup(
        Request $request,
        EntityManagerInterface $em,
        ShoppingListRepository $repo,
    ): Response {
        $user = $this->getUser();
        $groupName = $request->request->get('name');

        $list = $repo->findOneBy(['name' => $groupName, 'owner' => $user]);
        if ($list) {
            throw $this->createNotFoundException(); // this will do, avoid duplicates
        }

        $list = new ShoppingList();
        $list->setDateCreated(new \DateTimeImmutable());
        $list->setOwner($user);
        $list->setName($groupName);
        $list->setType(ListType::ITEM_GROUP);
        $em->persist($list);
        
        $member = new ListMember();
        $member->setUser($user);
        $member->setShoppingList($list);
        $member->setRole(ListMemberRole::OWNER);
        $em->persist($member);
        
        $em->flush();

        $this->addFlash('success', "{$groupName} created - add items and save to quick-add them to your shopping list");

        return $this->redirectToRoute('app_shoppinglist_edit', ['id' => $list->getId()]);
    }
    
    #[Route('/switch', name: 'app_shopping_list_switch', methods: ['POST'])]
    public function switch(
        Request $request,
    ): Response {
        return $this->redirectToRoute('app_shoppinglist_edit', ['id' => $request->request->get('list_id')]);
    }
}