<?php

namespace App\Controller\Back;

use App\Entity\SellerRequest;
use App\Repository\SellerRequestRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SellerController extends AbstractController
{
    #[Route('/seller', name: 'app_seller')]
    public function show(SellerRequestRepository $sellerRepository ): Response
    {
        $sellers = $sellerRepository->findAll();
        return $this->render('back/seller/index.html.twig', [
            'sellers' => $sellers,
        ]);
    }


    #[Route('/seller/{id}/validate', name: 'app_seller_validate')]
    public function validate(SellerRequest $sellerRequest, UserRepository $userRepository): Response
    {
        $user = $sellerRequest->getUserRequest();
        if (in_array("ROLE_SELLER", $user->getRoles())) {
            $user->setRoles(['']);
            $userRepository->save($user, true);
            $this->addFlash('warning', 'User devalidated');
            return $this->redirectToRoute('admin_app_seller');
        }
        $user->setRoles(['ROLE_SELLER']);
        $userRepository->save($user, true);
        
        $this->addFlash('success', 'Seller validated');

    
        return $this->redirectToRoute('admin_app_seller');
    }
}
