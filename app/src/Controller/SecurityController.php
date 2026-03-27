<?php

namespace App\Controller;

use App\Entity\Household;
use App\Entity\HouseholdMember;
use App\Entity\ShoppingList;
use App\Repository\HouseholdRepository;
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
    public function postLogin(EntityManagerInterface $em, HouseholdRepository $householdRepository): Response
    {
        $user = $this->getUser();

        // 🔍 Check household
        $household = $householdRepository->findOneBy(['user' => $user]);
        if (!$household) {
            // Create household
            $household = new Household();
            $household->setUser($user);
            $em->persist($household);

            $member = new HouseholdMember();
            $member->setUser($user);
            $member->setHousehold($household);
            $em->persist($member);

            // Create default list
            $list = new ShoppingList();
            $list->setHousehold($household);
            $list->setDateCreated(new \DateTimeImmutable());
            $em->persist($list);

            $em->flush();
        }

        // $invites = $inviteRepo->findBy([
        //     'email' => $currentUser->getEmail(),
        //     'status' => 'pending'
        // ]);
        // “You’ve been invited to join X’s shopping list”

        return $this->redirectToRoute('app_home');
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This should never be reached.');
    }
}
