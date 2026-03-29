<?php

namespace App\Controller;

use App\Entity\ListInvite;
use App\Enum\ListInviteStatus;
use App\Form\ListInviteType;
use App\Repository\ListInviteRepository;
use App\Repository\ShoppingListRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/invite')]
final class ListInviteController extends AbstractController
{
    #[Route('/send', name: 'app_list_invite_send', methods: ['POST'])]
    public function sendInvite(
        Request $request, 
        EntityManagerInterface $em,
        UserRepository $userRepository, 
        ShoppingListRepository $shoppingListRepository,
    ): Response
    {
        $email = $request->request->get('email');
        $user = $userRepository->findOneBy(['email' => $email]);
        
        if($user){
            $invite = new ListInvite();
            $invite->setEmail($email);
            $invite->setShoppingList($shoppingListRepository->findOneByOwner($this->getUser()));
            $invite->setStatus(ListInviteStatus::PENDING);
            $invite->setDateSent(new \DateTimeImmutable());
            $em->persist($invite);
            $em->flush();
        }

        $this->addFlash('success', 'Invite sent to ' . $email . '!');

        return $this->redirectToRoute('app_home');
    }

    #[Route(name: 'app_list_invite_index', methods: ['GET'])]
    public function index(ListInviteRepository $listInviteRepository): Response
    {
        return $this->render('list_invite/index.html.twig', [
            'list_invites' => $listInviteRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_list_invite_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $listInvite = new ListInvite();
        $form = $this->createForm(ListInviteType::class, $listInvite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($listInvite);
            $entityManager->flush();

            return $this->redirectToRoute('app_list_invite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('list_invite/new.html.twig', [
            'list_invite' => $listInvite,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_list_invite_show', methods: ['GET'])]
    public function show(ListInvite $listInvite): Response
    {
        return $this->render('list_invite/show.html.twig', [
            'list_invite' => $listInvite,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_list_invite_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ListInvite $listInvite, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ListInviteType::class, $listInvite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_list_invite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('list_invite/edit.html.twig', [
            'list_invite' => $listInvite,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_list_invite_delete', methods: ['POST'])]
    public function delete(Request $request, ListInvite $listInvite, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$listInvite->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($listInvite);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_list_invite_index', [], Response::HTTP_SEE_OTHER);
    }
}
