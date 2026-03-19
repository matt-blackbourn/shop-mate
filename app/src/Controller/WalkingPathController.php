<?php

namespace App\Controller;

use App\Entity\WalkingPath;
use App\Entity\Node;
use App\Entity\Supermarket;
use App\Enum\SupermarketType;
use App\Form\WalkingPathType;
use App\Repository\WalkingPathRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/walkingpath')]
final class WalkingPathController extends AbstractController
{
    #[Route(name: 'app_walking_path_index', methods: ['GET'])]
    public function index(WalkingPathRepository $walkingPathRepository): Response
    {
        return $this->render('walking_path/index.html.twig', [
            'walkingPaths' => $walkingPathRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_walking_path_new')]
    public function new(Request $request, WalkingPathRepository $walkingPathRepository): Response
    {
        $form = $this->createForm(WalkingPathType::class);

        return $this->render('walking_path/store_mapper.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/nodes', name: 'app_walking_path_nodes', methods: ['GET'])]
    public function nodes(WalkingPath $walkingPath): Response
    {
        return $this->render('walking_path/edit.html.twig', [
            'walkingPath' => $walkingPath,
        ]);
    }

    #[Route('/save', name: 'app_walking_path_save', methods: ['POST'])]
    public function save(Request $request, EntityManagerInterface $em): Response
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!$data || !isset($data['rawData'])) {
                return $this->json(['error' => 'Invalid payload'], 400);
            }

            // Create WalkingPath entity
            $walkingPath = new WalkingPath();
            $walkingPath->setRawData($data['rawData'] ?? null);
            $walkingPath->setType(SupermarketType::from($data['supermarketType']));
            $walkingPath->setSuburb($data['suburb'] ?? null);
            $walkingPath->setUser($this->getUser());
            $walkingPath->setDateCreated(new \DateTimeImmutable());

            $em->persist($walkingPath);
            $em->flush();

            return $this->json(['success' => true, 'walkingPathId' => $walkingPath->getId()]);
        } catch (\Throwable $e) {
                // Always return JSON with error details (for debugging)
            return $this->json([
                'error' => 'Server error: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    #[Route('/{id}/to-supermarket/save', name: 'app_walking_path_to_supermarket_save', methods: ['POST'])]
    public function saveWalkingPathAsSupermarket(
        Request $request,
        WalkingPath $walkingPath,
        EntityManagerInterface $em
    ): Response {
        $data = json_decode($request->request->get('nodes'), true);

        $supermarket = new Supermarket();
        $supermarket->setWidth($data['width']);
        $supermarket->setHeight($data['height']);
        $supermarket->setType($walkingPath->getType());
        $supermarket->setSuburb($walkingPath->getSuburb());
        $supermarket->setWalkingPath($walkingPath);
        $supermarket->setDateCreated(new \DateTimeImmutable());
        $supermarket->setScaledPathData($data['walkingPath'] ?? []);

        if($data['width'] * $data['height'] > 1000000) {
            // Large supermarket
            $supermarket->setAisleWidth(40);
            $supermarket->setShelfDepth(20);
        } else {
            // Smaller supermarket
            $supermarket->setAisleWidth(35);
            $supermarket->setShelfDepth(25);
        }

        $em->persist($supermarket);
    
        $seen = [];
        foreach ($data['nodes'] as $nodeData) {
            $key = $nodeData['x'].'-'.$nodeData['y'];
    
            // skip duplicates
            if (isset($seen[$key])) {
                continue;
            }
    
            $seen[$key] = true;
    
            $node = new Node();
            $node->setXValue($nodeData['x']);
            $node->setYValue($nodeData['y']);
            $node->setSupermarket($supermarket);
    
            $em->persist($node);
        }

        $walkingPath->setConverted(true);
        $em->flush();
    
        $this->addFlash('success', 'Floor plan saved as a supermarket with nodes successfully!');
        return $this->redirectToRoute('app_supermarket_index');
    }


    #[Route('/{id}', name: 'app_walking_path_show', methods: ['GET'])]
    public function show(WalkingPath $walkingPath): Response
    {
        return $this->render('walking_path/show.html.twig', [
            'walking_path' => $walkingPath,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_walking_path_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, WalkingPath $walkingPath, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(WalkingPathType::class, $walkingPath);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_walking_path_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('walking_path/edit.html.twig', [
            'walking_path' => $walkingPath,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_walking_path_delete', methods: ['POST'])]
    public function delete(Request $request, WalkingPath $walkingPath, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$walkingPath->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($walkingPath);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_walking_path_index', [], Response::HTTP_SEE_OTHER);
    }
}
