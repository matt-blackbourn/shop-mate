<?php

namespace App\Controller;

use App\Entity\Edge;
use App\Entity\Node;
use App\Entity\ProductPlacement;
use App\Entity\Shelf;
use App\Entity\Supermarket;
use App\Enum\PlacementType;
use App\Form\SupermarketType;
use App\Repository\EdgeRepository;
use App\Repository\FoodItemRepository;
use App\Repository\NodeRepository;
use App\Repository\ProductPlacementRepository;
use App\Repository\ShelfRepository;
use App\Repository\SupermarketRepository;
use App\Service\MapBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/supermarket')]
class SupermarketController extends AbstractController
{

    #[Route('/index', name: 'app_supermarket_index', methods: ['GET'])]
    public function index(SupermarketRepository $supermarketRepository): Response
    {
        return $this->render('supermarket/index.html.twig', [
            'supermarkets' => $supermarketRepository->findAll(),
        ]);
    }

    #[Route('/map', name: 'app_supermarket_map', methods: ['GET'])]
    public function map(SupermarketRepository $supermarketRepository): Response
    {
        return $this->render('supermarket/map.html.twig');
    }


    #[Route('/new', name: 'app_supermarket_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $supermarket = new Supermarket();

        $form = $this->createForm(SupermarketType::class, $supermarket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $image = $form->get('image')->getData();

            $filename = uniqid().'.'.$image->guessExtension();
            $image->move($this->getParameter('floorplans_dir'), $filename);

            [$width, $height] = getimagesize(
                $this->getParameter('floorplans_dir').'/'.$filename
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

    
    #[Route('/{id}/edit', name: 'app_supermarket_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Supermarket $supermarket, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SupermarketType::class, $supermarket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $image = $form->get('image')->getData();

            $filename = uniqid().'.'.$image->guessExtension();
            $image->move($this->getParameter('floorplans_dir'), $filename);

            [$width, $height] = getimagesize(
                $this->getParameter('floorplans_dir').'/'.$filename
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
    

    #[Route('/{id}/admin/assign-food-edges', name: 'admin_assign_food_edges', methods: ['GET', 'POST'])]
    public function assignFoodEdges(
        Request $request,
        Supermarket $supermarket,
        FoodItemRepository $foodRepo,
        NodeRepository $nodeRepo,
        EdgeRepository $edgeRepo,
        EntityManagerInterface $em,
        ProductPlacementRepository $productPlacementRepository,
    ): Response {
        $foodItems = $foodRepo->findAll();
        $nodes = $nodeRepo->findBySupermarket($supermarket);

        // Load existing placements properly
        $placements = [];
        foreach ($foodItems as $food) {
            $placement = $productPlacementRepository->findOneBy(['foodItem' => $food, 'supermarket' => $supermarket]);
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
                    $existingPlacement = $productPlacementRepository->findOneBy(['foodItem' => $food, 'supermarket' => $supermarket]);
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
                $placement = $productPlacementRepository->findOneBy(['foodItem' => $food, 'supermarket' => $supermarket]) ?? new ProductPlacement();
                $placement->setFoodItem($food);
                $placement->setEdge($edge);
                $placement->setType(PlacementType::SYSTEM);
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
    public function nodes(Request $request, Supermarket $supermarket, MapBuilder $mapBuilder): Response
    {
        return $this->render('supermarket/nodes.html.twig', [
            'supermarket' => $supermarket,
            'nodes' => $mapBuilder->getAllNodes($supermarket),
        ]);
    }

    #[Route('/{id}/draw/edges', name: 'app_supermarket_draw_edges', methods: ['GET'])]
    public function edges(
        Request $request, 
        Supermarket $supermarket, 
        MapBuilder $mapBuilder,
    ): Response
    {
        return $this->render('supermarket/edges.html.twig', [
            'supermarket' => $supermarket,
            'nodes' => $mapBuilder->getAllNodes($supermarket),
            'edges' => $mapBuilder->getAllEdges($supermarket),
        ]);
    }

    #[Route('/{id}/draw/shelves', name: 'app_supermarket_draw_shelves', methods: ['GET'])]
    public function shelves(MapBuilder $mapBuilder, Supermarket $supermarket): Response
    {
        return $this->render('supermarket/shelves.html.twig', [
            'nodes' => $mapBuilder->getAllNodes($supermarket),
            'edges' => $mapBuilder->getAllEdges($supermarket),
            'map' => [$supermarket->getWidth(), $supermarket->getHeight()],
            'supermarket' => $supermarket,
        ]);
    }

    #[Route('/{id}/shelves/edit', name: 'app_supermarket_edit_shelves', methods: ['GET'])]
    public function shelvesEdit(MapBuilder $mapBuilder, Supermarket $supermarket): Response
    {
        return $this->render('supermarket/shelvesEdit.html.twig', [
            'nodes' => $mapBuilder->getAllNodes($supermarket),
            'edges' => $mapBuilder->getAllEdges($supermarket),
            'shelves' => $mapBuilder->getAllShelves($supermarket),
            'viewBox' => $mapBuilder->getViewBox($supermarket),
            'supermarket' => $supermarket,
        ]);
    }


    #[Route('/{id}/edges/save', methods: ['POST'])]
    public function saveEdgesBulk(
        Supermarket $supermarket,
        Request $request,
        EntityManagerInterface $em,
        EdgeRepository $edgeRepository,
        NodeRepository $nodeRepository, 
    ) {
        try{
            $raw = $request->request->get('edges');
            if (!$raw) {
                $this->addFlash('error', 'No edges submitted');
                return $this->redirectToRoute('app_supermarket_draw_edges', ['id' => $supermarket->getId()]);
            }
        
            $data = json_decode($raw, true);
            if (!is_array($data)) {
                $this->addFlash('error', 'Invalid edge data');
                return $this->redirectToRoute('app_supermarket_draw_edges', ['id' => $supermarket->getId()]);
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
    
                if($item['id']) {
                    $edge = $edgeRepository->find($item['id']);
                    $edge->setPhase($item['phase']); // phase is all we can edit here
                } else {
                    $edge = new Edge();
                    $edge->setPhase($item['phase']);
                    $edge->setSupermarket($supermarket);
                    $edge->setStart($from);
                    $edge->setEnd($to);
                    
                    $dx = $from->getXValue() - $to->getXValue();
                    $dy = $from->getYValue() - $to->getYValue();
                    $length = sqrt(($dx * $dx) + ($dy * $dy));
                    $edge->setLength($length);
        
                    $em->persist($edge);
                }
            }
    
            $em->flush();
            $this->addFlash('success', 'Edges saved successfully!');

            return $this->redirectToRoute('app_supermarket_draw_edges', [
                'id' => $supermarket->getId(),
            ]);
            
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Error saving edges: ' . $e->getMessage());

            return $this->redirectToRoute('app_supermarket_draw_edges', [
                'id' => $supermarket->getId(),
            ]);
        }
    }

    #[Route('/{id}/nodes/save', methods: ['POST'])]
    public function saveNodesBulk(
        Supermarket $supermarket,
        Request $request,
        EntityManagerInterface $em
    ) {
        try{
            $raw = $request->request->get('nodes');
            if (!$raw) {
                $this->addFlash('error', 'No nodes submitted');
                return $this->redirectToRoute('app_supermarket_draw_nodes', ['id' => $supermarket->getId()]);
            }
        
            $data = json_decode($raw, true);
            if (!is_array($data)) {
                $this->addFlash('error', 'Invalid edge data');
                return $this->redirectToRoute('app_supermarket_draw_nodes', ['id' => $supermarket->getId()]);
            }

            foreach ($data as $item) {
                if(!$item['id']) {
                    $node = new Node();
                    $node->setSupermarket($supermarket);
                    $node->setXValue($item['x']);
                    $node->setYValue($item['y']);
                    $em->persist($node);

                    // Set the first node as entrance if none set
                    if(!$supermarket->getEntranceNode()) {
                        $supermarket->setEntranceNode($node);
                    }
                } 
            }
    
            $em->flush();
            $this->addFlash('success', 'Nodes saved successfully!');

            return $this->redirectToRoute('app_supermarket_draw_nodes', [
                'id' => $supermarket->getId(),
            ]);
            
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Error saving nodes: ' . $e->getMessage());

            return $this->redirectToRoute('app_supermarket_draw_nodes', [
                'id' => $supermarket->getId(),
            ]);
        }
    }

    #[Route('/{id}/shelves/save', methods: ['POST'])]
    public function saveShelves(
        Supermarket $supermarket,
        Request $request,
        EntityManagerInterface $em,
        ShelfRepository $shelfRepository,
    ) {
        $shelvesJson = $request->request->get('shelves'); // gets the string
        $shelves = json_decode($shelvesJson, true);       // converts to array
        foreach ($shelves as $item) {
            $shelf = isset($item['id']) ? $shelfRepository->find($item['id']) : new Shelf();
            
            if($item['deleted']){
                $em->remove($shelf);
                continue;
            }

            $shelf->setSupermarket($supermarket);
            $shelf->setX($item['x']);
            $shelf->setY($item['y']);
            $shelf->setWidth($item['width']);
            $shelf->setHeight($item['height']);
            $shelf->setFullSelect($item['fullSelect'] ?? false);
            $em->persist($shelf);
        }

        $em->flush();
        $this->addFlash('success', 'Shelves saved successfully!');

        return $this->redirectToRoute('app_supermarket_edit_shelves', [
            'id' => $supermarket->getId(),
        ]);
    }

}
