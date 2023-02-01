<?php

namespace App\Controller\Back;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_default')]
    public function index(): Response
    {
        //add flash message
        $this->addFlash('success', 'Welcome to the back office');
        return $this->render('back/default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

}
