<?php

namespace App\Controller;

use App\Entity\ListItem;
use App\Entity\ShoppingList;
use App\Form\FoodItemGroupPlacementType;
use App\Form\ShoppingListType;
use App\Repository\FoodItemRepository;
use App\Repository\ListItemRepository;
use App\Repository\ProductPlacementRepository;
use App\Repository\ShoppingListRepository;
use App\Repository\SupermarketRepository;
use App\Service\MapBuilder;
use App\Service\PathFinder;
use App\Service\RoutedListItemDto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
            'unpickedItems' => $listItemRepository->findByShoppingList($shoppingList),
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
            'supermarkets' => $supermarketRepository->findAll(),
            'lastUsedSupermarketId' => $this->getUser()->getLastUsedSupermarket()?->getId(),
        ]);
    }


    #[Route('/active/{supermarketId}/{showModal}', name: 'app_shopping_list_active', methods: ['GET'], defaults: ['showModal' => false])]
    public function activeList(
        ShoppingListRepository $shoppingListRepository,
        PathFinder $pathFinder,
        ProductPlacementRepository $productPlacementRepository,
        FoodItemRepository $foodItemRepository,
        EntityManagerInterface $em,
        SupermarketRepository $supermarketRepository,
        MapBuilder $mapBuilder,
        Request $request,
        int $supermarketId,
        bool $showModal,
    ): Response {
        $shoppingList = $shoppingListRepository->findOneByUser($this->getUser());
        if(!$shoppingList) {
            $this->addFlash('warning', 'You have nothing in your shopping list!');
            $this->redirectToRoute('app_home');
        }

        $supermarket = $supermarketRepository->find($supermarketId);

        // here we want to look for any items that do not have a mapped location
        // if those items have a category, we look for any item in this category in this supermarket that is mapped
        // if we find one, we create and persist a new ProductPlacement for this item, type=category, and then run the pathfinder
        // foreach ($shoppingList->getListItems() as $listItem) {
        //     $placement = $productPlacementRepository->findOneBy([
        //         'foodItem' => $listItem->getFoodItem(),
        //         'supermarket' => $supermarket,
        //     ]);

        //     if(!$placement){
        //         $category = $listItem->getFoodItem()->getCategory();
        //         if($category) {
        //             $similarPlacement = $productPlacementRepository->findOnePlacedByCategoryInSupermarket($category, $supermarket);
        //             if($similarPlacement) {
        //                 $newPlacement = new ProductPlacement();
        //                 $newPlacement->setFoodItem($listItem->getFoodItem());
        //                 $newPlacement->setSupermarket($supermarket);
        //                 $newPlacement->setEdge($similarPlacement->getEdge());
        //                 $newPlacement->setType(PlacementType::CATEGORY);
        //                 $newPlacement->setAisleSide($similarPlacement->getAisleSide());
        //                 $em->persist($newPlacement);
        //             }
        //         }
        //     }
        // }

        $this->getUser()->setLastUsedSupermarket($supermarket);
        $em->flush();
        
        $orderedList = $pathFinder->orderShoppingList($shoppingList, $supermarket, $request->query->get('startNode', null));
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
    #[Route('/shoppinglist/ajax/pick/{id}', name: 'app_shopping_pick', methods: ['POST'])]
    public function pick(ListItem $item, EntityManagerInterface $em): JsonResponse
    {
        $item->markPicked();
        $em->flush();

        return new JsonResponse(['ok' => true]);
    }
}