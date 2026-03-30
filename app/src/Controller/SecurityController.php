<?php

namespace App\Controller;

use App\Entity\ListMember;
use App\Entity\ShoppingList;
use App\Enum\ListMemberRole;
use App\Repository\ShoppingListRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/post-login', name: 'app_post_login')]
    public function postLogin(EntityManagerInterface $em, ShoppingListRepository $shoppingListRepository): Response
    {
        
        $user = $this->getUser();
        
        $list = $shoppingListRepository->findOneByOwner($user);
        if (!$list) {
            // first-time setup
            $list = new ShoppingList();
            $list->setDateCreated(new \DateTimeImmutable());
            $list->setOwner($user);
            $em->persist($list);
            
            $member = new ListMember();
            $member->setUser($user);
            $member->setShoppingList($list);
            $member->setRole(ListMemberRole::OWNER);
            $em->persist($member);
            
            $em->flush();
        }
        
        return $this->redirectToRoute('app_home');
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This should never be reached.');
    }
}
