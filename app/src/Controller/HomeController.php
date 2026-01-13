<?php

namespace App\Controller;

use App\Entity\Node;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $em): Response
    {
        // for($i = 0; $i < 72; $i++) {
        //     // Just a loop to demonstrate some logic
        //     $node = new Node();
        //     $node->setNumber($i + 1);
        //     $em->persist($node);


        // }
        // $em->flush();
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
