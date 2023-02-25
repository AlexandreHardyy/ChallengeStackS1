<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RGPDController extends AbstractController
{
    #[Route('/terms', name: 'app_terms')]
    public function index(): Response
    {
        return $this->render('front/rgpd/index.html.twig', [
            'controller_name' => 'NavigationController',
        ]);
    }

    #[Route('/privacy', name: 'app_privacy')]
    public function privacy(): Response
    {
        return $this->render('front/rgpd/privacy.html.twig', [
            'controller_name' => 'NavigationController',
        ]);
    }

    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('front/rgpd/about.html.twig', [
            'controller_name' => 'NavigationController',
        ]);
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(): Response
    {
        return $this->render('front/rgpd/contact.html.twig', [
            'controller_name' => 'NavigationController',
        ]);
    }
}
