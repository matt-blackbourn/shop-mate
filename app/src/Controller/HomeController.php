<?php

namespace App\Controller;

use App\Repository\ShoppingListRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ShoppingListRepository $shoppingListRepository): Response
    {
        $user = $this->getUser();
        $shoppingList = $user->getDefaultList() ?? $shoppingListRepository->findForUser($user)[0];

        // return $this->redirectToRoute('app_shoppinglist_edit', ['id' => $shoppingList->getId()]);
        return $this->redirectToRoute('app_shoppinglist_edit', ['id' => 3]);
    }
}
