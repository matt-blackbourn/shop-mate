<?php

namespace App\Controller;

use App\Entity\FoodItem;
use App\Entity\ListItem;
use App\Entity\ProductPlacement;
use App\Entity\ShoppingList;
use App\Entity\Supermarket;
use App\Enum\PlacementType;
use App\Form\FoodItemType;
use App\Form\ShoppingListType;
use App\Repository\EdgeRepository;
use App\Repository\ListItemRepository;
use App\Repository\NodeRepository;
use App\Repository\PlacementTypeRepository;
use App\Repository\ProductPlacementRepository;
use App\Repository\ShoppingListRepository;
use App\Repository\SupermarketRepository;
use App\Service\PathFinder;
use App\Service\PlacementResolver;
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
            $em->persist($shoppingList);
            $em->flush();
        }

        $form = $this->createForm(ShoppingListType::class, $shoppingList, [
            'unpickedItems' => $listItemRepository->findByShoppingListOrderedByCategory($shoppingList),
        ]);
        $form->handleRequest($request);

        
        // For AJAX save: the form isn’t submitted via normal POST
        if ($request->isXmlHttpRequest() && !$form->isSubmitted()) {
            // Manually submit the form with the posted data
            $form->submit($request->request->all());
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            // $action = $request->request->get('intent');

            if ($request->request->get('intent') === 'go_shopping') {
                return $this->redirectToRoute('app_shopping_list_active', [
                    'id' => $shoppingList->getId(),
                ]);
            }

            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }
        
            // default: save → home
            // return $this->redirectToRoute('app_home');
        }

        // Create form for new food modal
        $food = new FoodItem();
        $foodForm = $this->createForm(FoodItemType::class, $food, [
            'action' => $this->generateUrl('app_food_new_modal'),
        ]);

        return $this->render('shopping_list/edit.html.twig', [
            'form' => $form->createView(),
            'foodForm' => $foodForm->createView(),
            'supermarkets' => $supermarketRepository->findAll(),
            'lastUsedSupermarketId' => $this->getUser()->getLastUsedSupermarket()?->getId(),
        ]);
    }


    #[Route('/active/{supermarketId}', name: 'app_shopping_list_active', methods: ['GET'])]
    public function activeList(
        ShoppingListRepository $shoppingListRepository,
        PathFinder $pathFinder,
        ProductPlacementRepository $productPlacementRepository,
        EntityManagerInterface $em,
        SupermarketRepository $supermarketRepository,
        NodeRepository $nodeRepository,
        EdgeRepository $edgeRepository,
        int $supermarketId,
    ): Response {
        $shoppingList = $shoppingListRepository->findOneByUser($this->getUser());
        if(!$shoppingList) {
            $this->redirectToRoute('app_home');
        }

        // here we want to look for any items that do not have a mapped location
        // if those items have a category, we look for any item in this category in this supermarket that is mapped
        // if we find one, we create and persist a new ProductPlacement for this item, type=category, and then run the pathfinder
        $supermarket = $supermarketRepository->find($supermarketId);
        foreach ($shoppingList->getListItems() as $listItem) {
            $placement = $productPlacementRepository->findOneBy([
                'foodItem' => $listItem->getFoodItem(),
                'supermarket' => $supermarket,
            ]);

            if(!$placement){
                $category = $listItem->getFoodItem()->getCategory();
                if($category) {
                    $similarPlacement = $productPlacementRepository->findOnePlacedByCategoryInSupermarket($category, $supermarket);
                    if($similarPlacement) {
                        $newPlacement = new ProductPlacement();
                        $newPlacement->setFoodItem($listItem->getFoodItem());
                        $newPlacement->setSupermarket($supermarket);
                        $newPlacement->setEdge($similarPlacement->getEdge());
                        $newPlacement->setType(PlacementType::CATEGORY);
                        $em->persist($newPlacement);
                    }
                }
            }
        }

        $this->getUser()->setLastUsedSupermarket($supermarket);
        $em->flush();

        $orderedList = $pathFinder->orderShoppingList($shoppingList, $supermarket);
        if(count($orderedList) === 0) {
            return $this->redirectToRoute('app_home');
        }

        $nodes = []; 
        foreach ($nodeRepository->findBySupermarket($supermarket) as $node) {
            $nodes[] = [
                'id' => $node->getId(),
                'x' => $node->getXValue(),
                'y' => $node->getYValue()
            ];
        }

        $edges = []; 
        foreach ($edgeRepository->findBySupermarket($supermarket) as $edge) {
            $edges[] = [
                'id' => $edge->getId(),
                'from' => $edge->getStart()->getId(),
                'to'   => $edge->getEnd()->getId(),
                'x1'   => $edge->getStart()->getXValue(),
                'y1'   => $edge->getStart()->getYValue(),
                'x2'   => $edge->getEnd()->getXValue(),
                'y2'   => $edge->getEnd()->getYValue(),
                'phase'=> $edge->getPhase(),
                'element'=> null, // placeholder for front-end use
            ];
        }

        return $this->render('shopping_list/active.html.twig', [
            'orderedList' => $orderedList,
            'shoppingList' => $shoppingList,
            'supermarket' => $supermarket,
            'nodes' => $nodes,
            'edges' => $edges,
            'map' => [$supermarket->getWidth(), $supermarket->getHeight()],
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