<?php

namespace App\Controller;

use App\Entity\FloorPlan;
use App\Entity\Node;
use App\Entity\Supermarket;
use App\Enum\SupermarketType;
use App\Form\FloorPlanType;
use App\Repository\FloorPlanRepository;
use App\Repository\NodeRepository;
use App\Repository\SupermarketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/floorplan')]
final class FloorPlanController extends AbstractController
{
    #[Route(name: 'app_floor_plan_index', methods: ['GET'])]
    public function index(FloorPlanRepository $floorPlanRepository): Response
    {
        return $this->render('floor_plan/index.html.twig', [
            'floorPlans' => $floorPlanRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_floorplan_new')]
    public function new(Request $request): Response
    {
        $form = $this->createForm(FloorPlanType::class);

        return $this->render('floor_plan/store_mapper.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/nodes', name: 'app_floorplan_nodes', methods: ['GET'])]
    public function nodes(FloorPlan $floorPlan): Response
    {
        return $this->render('floor_plan/edit.html.twig', [
            'floorPlan' => $floorPlan,
        ]);
    }

    #[Route('/save', name: 'app_floorplan_save', methods: ['POST'])]
    public function save(Request $request, EntityManagerInterface $em): Response
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!$data || !isset($data['rawData'])) {
                return $this->json(['error' => 'Invalid payload'], 400);
            }

            // Create FloorPlan entity
            $floorPlan = new FloorPlan();
            $floorPlan->setRawData($data['rawData'] ?? null);
            $floorPlan->setType(SupermarketType::from($data['supermarketType']));
            $floorPlan->setSuburb($data['suburb'] ?? null);
            $floorPlan->setUser($this->getUser());
            $floorPlan->setDateCreated(new \DateTimeImmutable());

            $em->persist($floorPlan);
            $em->flush();

            return $this->json(['success' => true, 'floorPlanId' => $floorPlan->getId()]);
        } catch (\Throwable $e) {
                // Always return JSON with error details (for debugging)
            return $this->json([
                'error' => 'Server error: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    #[Route('/{id}/to-supermarket/save', name: 'app_floorplan_to_supermarket_save', methods: ['POST'])]
    public function saveFloorPlanAsSupermarket(
        Request $request,
        FloorPlan $floorPlan,
        SupermarketRepository $supermarketRepository,
        NodeRepository $nodeRepository,
        EntityManagerInterface $em
    ): Response {
        $nodesJson = $request->request->get('nodes', '[]');
        $nodes = json_decode($nodesJson, true);
    
        if (empty($nodes)) {
            $this->addFlash('warning', 'No nodes to save.');
            return $this->redirectToRoute('app_floorplan_index');
        }
    
        // 1️⃣ Create the new Supermarket
        $supermarket = new Supermarket();
        $supermarket->setName($floorPlan->getType()->value . ' - ' . $floorPlan->getSuburb());
    
        // 2️⃣ Compute bounding box + edge buffer
        $EDGE_BUFFER = 50;
        $xs = array_column($nodes, 'x');
        $ys = array_column($nodes, 'y');
    
        $minX = min($xs) - $EDGE_BUFFER;
        $minY = min($ys) - $EDGE_BUFFER;
        $maxX = max($xs) + $EDGE_BUFFER;
        $maxY = max($ys) + $EDGE_BUFFER;
    
        $supermarket->setWidth($maxX - $minX);
        $supermarket->setHeight($maxY - $minY);
    
        $supermarketRepository->save($supermarket, true);
    
        // 3️⃣ Create Node entities for each node
        foreach ($nodes as $n) {
            $node = new Node();
            $node->setSupermarket($supermarket);
            $node->setXValue($n['x']);
            $node->setYValue($n['y']);
            $em->persist($node);
        }
    
        $em->flush();
    
        $this->addFlash('success', 'Floor plan saved as a supermarket with nodes successfully!');
        return $this->redirectToRoute('app_supermarket_index');
    }


    #[Route('/{id}', name: 'app_floor_plan_show', methods: ['GET'])]
    public function show(FloorPlan $floorPlan): Response
    {
        return $this->render('floor_plan/show.html.twig', [
            'floor_plan' => $floorPlan,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_floor_plan_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FloorPlan $floorPlan, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FloorPlanType::class, $floorPlan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_floor_plan_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('floor_plan/edit.html.twig', [
            'floor_plan' => $floorPlan,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_floor_plan_delete', methods: ['POST'])]
    public function delete(Request $request, FloorPlan $floorPlan, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$floorPlan->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($floorPlan);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_floor_plan_index', [], Response::HTTP_SEE_OTHER);
    }
}
