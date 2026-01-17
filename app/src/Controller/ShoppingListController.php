<?php

namespace App\Controller;

use App\Entity\ListItem;
use App\Entity\ShoppingList;
use App\Form\ShoppingListType;
use App\Repository\ListItemRepository;
use App\Repository\ShoppingListRepository;
use App\Service\PathFinder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/shoppinglist')]
final class ShoppingListController extends AbstractController
{
    #[Route(name: 'app_shopping_list_index', methods: ['GET'])]
    public function index(ShoppingListRepository $shoppingListRepository): Response
    {
        return $this->render('shopping_list/index.html.twig', [
            'shoppingLists' => $shoppingListRepository->findAllOrderedByRecent(),
        ]);
    }

    #[Route('/{id}/active', name: 'app_shopping_list_active')]
    public function active(
        ShoppingList $shoppingList,
        PathFinder $pathFinder,
    ): Response {
        $orderedList = $pathFinder->buildShoppingRoute($shoppingList);

        if(count($orderedList) === 0) {
            return $this->redirectToRoute('app_shopping_list_edit', ['id' => $shoppingList->getId()]);
        }

        return $this->render('shopping_list/active.html.twig', [
            'orderedList' => $orderedList,
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

    // maybe needs to go in list item controller later
    #[Route('/ajax/unpick/{id}', name: 'app_shopping_unpick', methods: ['POST'])]
    public function unpick(ListItem $item, EntityManagerInterface $em): JsonResponse
    {
        $item->setPicked(false);
        $item->setPickedAt(null);
        $em->flush();

        return new JsonResponse(['ok' => true]);
    }

    #[Route('/new', name: 'app_shopping_list_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $shoppingList = new ShoppingList();
        $shoppingList->setDateCreated(new \DateTimeImmutable());

        $form = $this->createForm(ShoppingListType::class, $shoppingList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($shoppingList);
            $em->flush();

            return $this->redirectToRoute('app_shopping_list_show', [
                'id' => $shoppingList->getId(),
            ]);
        }

        return $this->render('shopping_list/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_shopping_list_show', methods: ['GET'])]
    public function show(ShoppingList $shoppingList, PathFinder $pathFinder): Response
    {
        return $this->render('shopping_list/show.html.twig', [
            'shopping_list' => $shoppingList,
            'orderedList' => $pathFinder->buildShoppingRoute($shoppingList),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_shopping_list_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ShoppingList $shoppingList, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ShoppingListType::class, $shoppingList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_shopping_list_show', ['id' => $shoppingList->getId()]);
        }

        return $this->render('shopping_list/edit.html.twig', [
            'shopping_list' => $shoppingList,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_shopping_list_delete', methods: ['POST'])]
    public function delete(Request $request, ShoppingList $shoppingList, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$shoppingList->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($shoppingList);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_shopping_list_index', [], Response::HTTP_SEE_OTHER);
    }
}
