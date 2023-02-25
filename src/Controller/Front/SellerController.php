<?php

namespace App\Controller\Front;

use App\Entity\SellerRequest;
use App\Form\BecomeSellerType;
use App\Repository\OrderDetailsRepository;
use App\Repository\OrderHistoryRepository;
use App\Repository\SellerRequestRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SellerController extends AbstractController
{
    #[Route('/seller', name: 'app_seller')]
    #[Security("is_granted('ROLE_USER')")]
    public function Seller(Request $request, SellerRequestRepository $sellerRequestRepository, OrderDetailsRepository $orderDetailsRepository): Response
    {
        $user = $this->getUser();
        $seller = new SellerRequest();
        $totalSellProduct = $orderDetailsRepository->findProductByUser($user);
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
            'myProducts' => $myProduct,
            'totalSellProduct' => $totalSellProduct[0][1],
        ]);
    }
}
