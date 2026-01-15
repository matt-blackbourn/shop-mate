<?php

namespace App\Controller;

use App\Entity\ProductLocation;
use App\Entity\Supermarket;
use App\Form\ProductlocationType;
use App\Form\SupermarketType;
use App\Repository\EdgeRepository;
use App\Repository\FoodItemRepository;
use App\Repository\NodeRepository;
use App\Repository\ProductLocationRepository;
use App\Repository\SupermarketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/supermarket')]
final class SupermarketController extends AbstractController
{
    #[Route(name: 'app_supermarket_index', methods: ['GET'])]
    public function index(SupermarketRepository $supermarketRepository): Response
    {
        return $this->render('supermarket/index.html.twig', [
            'supermarkets' => $supermarketRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_supermarket_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $supermarket = new Supermarket();
        $form = $this->createForm(SupermarketType::class, $supermarket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($supermarket);
            $entityManager->flush();

            return $this->redirectToRoute('app_supermarket_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('supermarket/new.html.twig', [
            'supermarket' => $supermarket,
            'form' => $form,
        ]);
    }
    

    #[Route('/{id}/admin/assign-food-edges', name: 'admin_assign_food_edges', methods: ['GET', 'POST'])]
public function assignFoodEdges(
    Request $request,
    Supermarket $supermarket,
    FoodItemRepository $foodRepo,
    NodeRepository $nodeRepo,
    EdgeRepository $edgeRepo,
    EntityManagerInterface $em,
    ProductLocationRepository $locationRepo,
): Response {
    $foodItems = $foodRepo->findAll();
    $nodes = $nodeRepo->findBySupermarket($supermarket);

    // Load existing placements properly
    $placements = [];
    foreach ($foodItems as $food) {
        $placement = $locationRepo->findOneByFoodAndSupermarket($food, $supermarket);
        if ($placement) {
            $placements[$food->getId()] = $placement;
        }
    }

    if ($request->isMethod('POST')) {
        $locationsData = $request->request->all('locations'); // always returns an array

        $errors = [];
    
        foreach ($locationsData as $foodId => $loc) {
            $food = $foodRepo->find($foodId);
    
            $startId = $loc['start'] ?? null;
            $endId   = $loc['end'] ?? null;
    
            if(($endId && !$startId) || ($startId && !$endId)) {
                continue;
            }

            if(!$startId && !$endId) {
                // Remove existing placement if any
                $existingPlacement = $locationRepo->findOneByFoodAndSupermarket($food, $supermarket);
                if($existingPlacement) {
                    $em->remove($existingPlacement);
                }
                continue;
            }

            $edge = $edgeRepo->findOneByNodes($startId, $endId);
            if(!$edge) {
                $errors[] = "No edge found for {$food->getName()}.";
                continue;
            }
    
            // Find existing placement for this food in this supermarket or create new
            $placement = $locationRepo->findOneByFoodAndSupermarket($food, $supermarket) ?? new ProductLocation();
            $placement->setFoodItem($food);
            $placement->setEdge($edge);
            $placement->setSupermarket($supermarket);
            $em->persist($placement);
        }
    
        $em->flush();
        
        if (empty($errors)) {
            $this->addFlash('success', 'All assignments saved.');
            return $this->redirectToRoute('admin_assign_food_edges', ['id' => $supermarket->getId()]);
        } else {
            foreach ($errors as $err) {
                $this->addFlash('error', $err);
            }
        }
    }

    return $this->render('supermarket/assign_food_edges.html.twig', [
        'foodItems' => $foodItems,
        'nodes' => $nodes,
        'placements' => $placements,
    ]);
}


    #[Route('/{id}/edit', name: 'app_supermarket_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Supermarket $supermarket, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SupermarketType::class, $supermarket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_supermarket_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('supermarket/edit.html.twig', [
            'supermarket' => $supermarket,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_supermarket_delete', methods: ['POST'])]
    public function delete(Request $request, Supermarket $supermarket, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$supermarket->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($supermarket);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_supermarket_index', [], Response::HTTP_SEE_OTHER);
    }
}
