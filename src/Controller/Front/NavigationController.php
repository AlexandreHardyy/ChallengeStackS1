<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NavigationController extends AbstractController
{
    #[Route('/terms', name: 'app_terms')]
    public function index(): Response
    {
        return $this->render('front/terms/index.html.twig', [
            'controller_name' => 'NavigationController',
        ]);
    }
}
