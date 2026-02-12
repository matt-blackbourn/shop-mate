<?php

namespace App\Controller;

use App\Entity\ProductPlacement;
use App\Enum\PlacementType;
use App\Form\FoodItemGroupPlacementType;
use App\Repository\EdgeRepository;
use App\Repository\FoodItemRepository;
use App\Repository\ProductPlacementRepository;
use App\Repository\SupermarketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/placement')]
class ProductPlacementController extends AbstractController
{
    #[Route('/group', name: 'app_product_placement_group', methods: ['POST'])]
    public function group(
        Request $request,
        FoodItemRepository $repo
    ): Response {
        $form = $this->createForm(FoodItemGroupPlacementType::class, null, [
            'food_items' => $repo->findWithPlacementInSupermarket($supermarket),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $item = $form->get('foodItem')->getData();

        }
        
        return $this->redirectToRoute('app_shopping_list_active', [
            'supermarketId' => $item->getId(),
        ]);
    }

    #[Route('/save', name: 'app_product_placement_save', methods: ['POST'])]
    public function index(
        Request $request, 
        EntityManagerInterface $em,
        SupermarketRepository $supermarketRepository,
        FoodItemRepository $foodItemRepository,
        EdgeRepository $edgeRepository,
        ProductPlacementRepository $productPlacementRepository,
    ): Response
    {
        $placement = json_decode($request->request->get('placement'), true);

        // this needs work
        $updated = false;
        $aisleSide = $placement['aisleSide'];
        $edgeId    = $placement['edgeId'];
        $supermarket = $supermarketRepository->find($placement['supermarketId']);
        $foodItem    = $foodItemRepository->find($placement['foodItemId']);


        $existingPlacements = $productPlacementRepository->findBy([
            'supermarket' => $supermarket,
            'foodItem'    => $foodItem,
        ]);

        // 1️⃣ User always updates their own placement
        foreach ($existingPlacements as $existing) {
            if ($existing->getSuggestedBy()?->getId() === $this->getUser()->getId()) {
                $existing->setEdge($edgeRepository->find($edgeId));
                $existing->setAisleSide($aisleSide);
                $updated = true;
                $uow = $em->getUnitOfWork();
                $uow->computeChangeSets();
                
                break;
            }
        }

        // 2️⃣ Otherwise, check for identical placement → SYSTEM
        if (!$updated) {
            foreach ($existingPlacements as $existing) {
                if (
                    $existing->getEdge()->getId() === $edgeId &&
                    $existing->getAisleSide() === $aisleSide
                ) {
                    $existing->setType(PlacementType::SYSTEM);
                    $existing->setSuggestedBy(null);
                    $updated = true;
                    break;
                }
            }
        }

        // 3️⃣ Otherwise create a new one
        if (!$updated) {
            $productPlacement = new ProductPlacement();
            $productPlacement->setSupermarket($supermarketRepository->find($placement['supermarketId']));
            $productPlacement->setFoodItem($foodItemRepository->find($placement['foodItemId']));
            $productPlacement->setEdge($edgeRepository->find($edgeId));
            $productPlacement->setAisleSide($aisleSide);
            $productPlacement->setSuggestedBy($this->getUser());
            $productPlacement->setType(PlacementType::USER);
            $em->persist($productPlacement);
        }

        $em->flush();
        $this->addFlash('success', 'Product placed successfully - list order has been updated!');

        return $this->redirectToRoute('app_shopping_list_active', [
            'supermarketId' => $placement['supermarketId'],
            'startNode' => $placement['currentNodeId'], // in case we are reodering mid-shop
        ]);
    }
}
