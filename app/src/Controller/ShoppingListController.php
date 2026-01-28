<?php

namespace App\Controller;

use App\Entity\FoodItem;
use App\Entity\ListItem;
use App\Entity\ProductPlacement;
use App\Entity\ShoppingList;
use App\Enum\PlacementType;
use App\Form\FoodItemType;
use App\Form\ShoppingListType;
use App\Repository\EdgeRepository;
use App\Repository\ListItemRepository;
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
    public function home(Request $request, EntityManagerInterface $em, ShoppingListRepository $shoppingListRepository, ListItemRepository $listItemRepository): Response
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

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $action = $request->request->get('action');

            if($action === 'go_shopping') {
                return $this->redirectToRoute('app_home', [
                    'id' => $shoppingList->getId(),
                ]);
            }
        
            // default: save → home
            return $this->redirectToRoute('app_home');
        }

        // Create form for new food modal
        $food = new FoodItem();
        $foodForm = $this->createForm(FoodItemType::class, $food, [
            'action' => $this->generateUrl('app_food_new_modal'),
        ]);

        return $this->render('shopping_list/edit.html.twig', [
            'form' => $form->createView(),
            'foodForm' => $foodForm->createView(),
        ]);
    }



    #[Route('/active', name: 'app_shopping_list_active', methods: ['GET'])]
    public function activeList(
        ShoppingListRepository $shoppingListRepository,
        PathFinder $pathFinder,
        ProductPlacementRepository $productPlacementRepository,
        EntityManagerInterface $em,
        SupermarketRepository $supermarketRepository,
    ): Response {
        $supermarket = $supermarketRepository->find(1);
        $shoppingList = $shoppingListRepository->findOneByUser($this->getUser());
        if(!$shoppingList) {
            $this->redirectToRoute('app_home');
        }

        // here we want to look for any items that do not have a mapped location
        // if those items have a category, we look for any item in this category in this supermarket that is mapped
        // if we find one, we create and persist a new ProductPlacement for this item, type=category, and then run the pathfinder
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
                        $em->flush();
                    }
                }
            }
        }

        $orderedList = $pathFinder->orderShoppingList($shoppingList, $supermarketRepository->find(1));
        if(count($orderedList) === 0) {
            return $this->redirectToRoute('app_shopping_list_edit', ['id' => $shoppingList->getId()]);
        }

        return $this->render('shopping_list/active.html.twig', [
            'orderedList' => $orderedList,
            'shoppingList' => $shoppingList,
            'supermarket' => $supermarket,
        ]);
    }

    // maybe needs to go in list item controller later
    #[Route('/ajax/pick/{id}', name: 'app_shopping_pick', methods: ['POST'])]
    public function pick(ListItem $item, EntityManagerInterface $em): JsonResponse
    {
        $item->markPicked();
        $em->flush();

        return new JsonResponse(['ok' => true]);
    }

    #[Route(name: 'app_shopping_list_index', methods: ['GET'])]
    public function index(ShoppingListRepository $shoppingListRepository): Response
    {
        return $this->render('shopping_list/index.html.twig', [
            'shoppingLists' => $shoppingListRepository->findAllOrderedByRecent(),
        ]);
    }

    // #[Route('/new', name: 'app_shopping_list_new', methods: ['GET', 'POST'])]
    // public function new(
    //     Request $request,
    //     EntityManagerInterface $em,
    //     SupermarketRepository $supermarketRepository,
    // ): Response {
    //     $shoppingList = new ShoppingList();
    //     $shoppingList->setDateCreated(new \DateTimeImmutable());

    //     $form = $this->createForm(ShoppingListType::class, $shoppingList);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $em->persist($shoppingList);
    //         $em->flush();

    //         $action = $request->request->get('action');

    //         if($action === 'go_shopping') {
    //             return $this->redirectToRoute('app_home', [
    //                 'id' => $shoppingList->getId(),
    //             ]);
    //         }
        
    //         // default: save → home
    //         return $this->redirectToRoute('app_home');
    //     }

    //     // Create form for new food modal
    //     $food = new FoodItem();
    //     $foodForm = $this->createForm(FoodItemType::class, $food, [
    //         'action' => $this->generateUrl('app_food_new_modal'),
    //     ]);

    //     return $this->render('shopping_list/new.html.twig', [
    //         'form' => $form->createView(),
    //         'foodForm' => $foodForm->createView(),
    //     ]);
    // }




    #[Route('/{id}', name: 'app_shopping_list_show', methods: ['GET'])]
    public function show(ShoppingList $shoppingList, PathFinder $pathFinder): Response
    {
        return $this->render('shopping_list/show.html.twig', [
            'shopping_list' => $shoppingList,
            'orderedList' => $pathFinder->orderShoppingList($shoppingList),
        ]);
    }
}
