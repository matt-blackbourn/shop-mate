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
        FoodItemRepository $foodItemRepo,
        SupermarketRepository $supermarketRepo,
        ProductPlacementRepository $productPlacementRepo,
        EntityManagerInterface $em,
    ): Response {
        // Extract supermarketId BEFORE creating form
        $formData = $request->request->all();
        $supermarketId = $formData['food_item_group_placement']['supermarketId'] ?? null;
        $supermarket = $supermarketRepo->find($supermarketId);

        if (!$supermarket) {
            throw $this->createNotFoundException('Supermarket not found');
        }

        // Now build form using it
        $form = $this->createForm(FoodItemGroupPlacementType::class, null, [
            'food_items' => $foodItemRepo->findWithPlacementInSupermarket($supermarket),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $toBeGroupedId = (int) $form->getData()['foodItemId'];
            $groupWith = $form->get('groupWithItem')->getData();

            $existingPlacement = $productPlacementRepo->findPreferredPlacement($groupWith, $supermarket);
            $groupPlacement = new ProductPlacement();
            $groupPlacement->setFoodItem($foodItemRepo->find($toBeGroupedId));
            $groupPlacement->setSupermarket($supermarket);
            $groupPlacement->setEdge($existingPlacement->getEdge());
            $groupPlacement->setAisleSide($existingPlacement->getAisleSide());
            $groupPlacement->setType(PlacementType::GROUP);
            $groupPlacement->setSuggestedBy($this->getUser());  

            $em->persist($groupPlacement);
            $em->flush();
        }
        
        return $this->redirectToRoute('app_shopping_list_active', [
            'supermarketId' => $supermarket->getId(),
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

        // need to do case where system is being overridden by users?

        // If there is a GROUP placement that is being confirmed, we upgrate that to USER type, and change the suggestedBy to the current user.
        foreach ($existingPlacements as $existing) {
            if ($existing->getType() === PlacementType::GROUP) {
                $existing->setEdge($edgeRepository->find($edgeId));
                $existing->setAisleSide($aisleSide);
                $existing->setType(PlacementType::USER);
                $existing->setSuggestedBy($this->getUser());
                $updated = true;
                break;
            }
        }

        // User always updates their own placement
        if (!$updated) {
            foreach ($existingPlacements as $existing) {
                if ($existing->getSuggestedBy()?->getId() === $this->getUser()->getId()) {
                    $existing->setEdge($edgeRepository->find($edgeId));
                    $existing->setAisleSide($aisleSide);
                    $updated = true;
                    break;
                }
            }
        }

        // Otherwise, check for identical placement → SYSTEM
        // If identical placement already exists, we can upgrade it to SYSTEM. this now the source of truth for this item, and we don't need the USER placement anymore (if it exists)
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

        // 3Otherwise create a new one
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
