<?php

namespace App\Controller;

use App\Entity\ListInvite;
use App\Entity\ListMember;
use App\Enum\ListInviteStatus;
use App\Enum\ListMemberRole;
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
            $invite->setToken(bin2hex(random_bytes(16))); // Generate a random token for invite link
            $em->persist($invite);
            $em->flush();
        }

        $this->addFlash('success', 'Invite sent to ' . $email . '!');

        return $this->redirectToRoute('app_home');
    }

    #[Route('/response', name: 'app_list_invite_response', methods: ['POST'])]
    public function inviteResponse(Request $request, ListInviteRepository $listInviteRepository, EntityManagerInterface $em): Response
    {
        $token = $request->request->get('token');
        $response = $request->request->get('response');
        $makeDefault = $request->request->get('make_default') === '1';
    
        // Find invite by token
        $invite = $listInviteRepository->findOneBy(['token' => $token]);
    
        if (!$invite) {
            throw $this->createNotFoundException('Invalid invite.');
        }
    
        if ($response === 'accept') {
            $invite->setStatus(ListInviteStatus::ACCEPTED);
            
            // Add user to list
            $list = $invite->getShoppingList();
            $user = $this->getUser();
            
            $member = new ListMember();
            $member->setUser($user);
            $member->setShoppingList($list);
            $member->setRole(ListMemberRole::MEMBER);
            $em->persist($member);
            
            if ($makeDefault) {
                $user->setDefaultList($list);
            }
        } else {
            $invite->setStatus(ListInviteStatus::DECLINED);
        } 

        $invite->setDateResponded(new \DateTimeImmutable());
    
        $em->flush();
    
        return $this->redirectToRoute('app_home');
    }

}
