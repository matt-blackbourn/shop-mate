<?php

namespace App\Controller;

use App\Entity\ProductPlacement;
use App\Enum\PlacementType;
use App\Repository\EdgeRepository;
use App\Repository\FoodItemRepository;
use App\Repository\SupermarketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/placement')]
class ProductPlacementController extends AbstractController
{
    #[Route('/save', name: 'app_product_placement_save', methods: ['POST'])]
    public function index(
        Request $request, 
        EntityManagerInterface $em,
        SupermarketRepository $supermarketRepository,
        FoodItemRepository $foodItemRepository,
        EdgeRepository $edgeRepository,
    ): Response
    {
        $placement = json_decode($request->request->get('placement'), true);
        
        $productPlacement = new ProductPlacement();
        $productPlacement->setSupermarket($supermarketRepository->find($placement['supermarketId']));
        $productPlacement->setFoodItem($foodItemRepository->find($placement['foodItemId']));
        $productPlacement->setEdge($edgeRepository->find($placement['edgeId']));
        $productPlacement->setAisleSide($placement['aisleSide']);
        $productPlacement->setAisleSide($placement['aisleSide']);
        $productPlacement->setSuggestedBy($this->getUser());
        $productPlacement->setType(PlacementType::USER);
        $em->persist($productPlacement);

        $em->flush();
        $this->addFlash('success', 'Product placed successfully - list order has been updated!');

        return $this->redirectToRoute('app_shopping_list_active', [
            'supermarketId' => $placement['supermarketId'],
        ]);
    }
}
