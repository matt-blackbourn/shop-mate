<?php

namespace App\Controller;

use App\Entity\Edge;
use App\Entity\Node;
use App\Entity\ProductLocation;
use App\Entity\Supermarket;
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
class SupermarketController extends AbstractController
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

    #[Route('/{id}/draw/nodes', name: 'app_supermarket_draw_nodes', methods: ['GET'])]
    public function nodes(Request $request, Supermarket $supermarket, EntityManagerInterface $em): Response
    {
        return $this->render('supermarket/draw.html.twig', [
            'supermarket' => $supermarket,
        ]);
    }

    #[Route('/{id}/draw/edges', name: 'app_supermarket_draw_edges', methods: ['GET'])]
    public function edges(
        Request $request, 
        Supermarket $supermarket, 
        NodeRepository $nodeRepository, 
       
    ): Response
    {
        $nodes = $nodeRepository->findBySupermarket($supermarket);
        $data = []; 
        foreach ($nodes as $node) {
            $data[] = [
                'id' => $node->getId(),
                'x' => $node->getXValue(),
                'y' => $node->getYValue()
            ];
        }

        return $this->render('supermarket/edges.html.twig', [
            'supermarket' => $supermarket,
            'nodes' => $data,
        ]);
    }

    #[Route('/ajax/{id}/edges/save', methods: ['POST'])]
    public function saveEdgesBulk(
        Supermarket $supermarket,
        Request $request,
        EntityManagerInterface $em,
        EdgeRepository $edgeRepo,
        NodeRepository $nodeRepository, 
    ) {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $this->json(['error' => 'Invalid payload'], 400);
        }

        // OPTIONAL: clear existing edges first
        $existingEdges = $edgeRepo->findBy(['supermarket' => $supermarket]);
        foreach ($existingEdges as $edge) {
            $em->remove($edge);
        }

        foreach ($data as $item) {
            if (!isset($item['from'], $item['to'])) {
                continue;
            }

            $from = $nodeRepository->find($item['from']);
            $to   = $nodeRepository->find($item['to']);

            if (!$from || !$to) {
                continue;
            }

            // Safety: ensure nodes belong to this supermarket
            if ($from->getSupermarket() !== $supermarket ||
                $to->getSupermarket() !== $supermarket) {
                continue;
            }

            $edge = new Edge();
            $edge->setStart($from);
            $edge->setEnd($to);
            $edge->setSupermarket($supermarket);

            $em->persist($edge);
        }

        $em->flush();

        return $this->json([
            'status' => 'ok',
            'edges_saved' => count($data),
        ]);
 
    }

    #[Route('/ajax/{id}/nodes/save', methods: ['POST'])]
    public function saveNodesBulk(
        Supermarket $supermarket,
        Request $request,
        EntityManagerInterface $em
    ) {
        $data = json_decode($request->getContent(), true);

        foreach ($data['nodes'] as $key => $nodeData) {
            $node = new Node();
            $node->setSupermarket($supermarket);
            $node->setXValue($nodeData['x']);
            $node->setYValue($nodeData['y']);

            if($key === 0) {
                $supermarket->setEntranceNode($node);
            }

            $em->persist($node);
        }

        $em->flush();

        return $this->json(['status' => 'ok']);
    }

    #[Route('/ajax/{id}/edges/get', methods: ['GET'])]
    public function getEdgesBulk(
        Supermarket $supermarket,
    ) {
        $nodes = $supermarket->getNodes(); 

        $data = array_map(fn($n) => [
            'id' => $n->getId(),
            'x' => $n->getX(),
            'y' => $n->getY()
        ], $nodes->toArray());

        return $this->json($data);
    }

    #[Route('/ajax/{id}/edges/delete', methods: ['POST'])]
    public function deleteEdgesBulk(
        Supermarket $supermarket,
        Request $request,
        EdgeRepository $edgeRepo,
        EntityManagerInterface $em,
    ) {
        $data = json_decode($request->getContent(), true);

        foreach ($data['edges'] as $edgeId) {
            $edge = $edgeRepo->find($edgeId);
            if ($edge && $edge->getSupermarket()->getId() === $supermarket->getId()) {
                $em->remove($edge);
            }
        }

        $em->flush();

        return $this->json(['status' => 'ok']);
    }


    #[Route('/{id}/edit', name: 'app_supermarket_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Supermarket $supermarket, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SupermarketType::class, $supermarket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $image = $form->get('image')->getData();

            $filename = uniqid().'.'.$image->guessExtension();
            $image->move($this->getParameter('supermarkets_dir'), $filename);

            [$width, $height] = getimagesize(
                $this->getParameter('supermarkets_dir').'/'.$filename
            );

            $supermarket->setImagePath($filename);
            $supermarket->setWidth($width);
            $supermarket->setHeight($height);

            $em->persist($supermarket);
            $em->flush();

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
