<?php

namespace App\Controller\Front;

use App\Entity\SellerRequest;
use App\Form\BecomeSellerType;
use App\Repository\SellerRequestRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SellerController extends AbstractController
{
    #[Route('/seller/request', name: 'app_seller')]
    #[Security("is_granted('ROLE_USER')")]
    public function becomeSeller(Request $request, SellerRequestRepository $sellerRequestRepository): Response
    {
        $user = $this->getUser();
        $seller = new SellerRequest();

        $myProduct = $user->getProducts();

        $form = $this->createForm(BecomeSellerType::class, $seller);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $seller->setUserRequest($user);
            $sellerRequestRepository->save($seller, true);

            return $this->redirectToRoute('front_app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('front/seller/index.html.twig', [
            'form' => $form->createView(),
            'myProducts' => $myProduct
        ]);
    }
}
