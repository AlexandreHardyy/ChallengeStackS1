<?php

namespace App\Controller\Front;

use App\Entity\SellerRequest;
use App\Form\BecomeSellerType;
use App\Repository\OrderDetailsRepository;
use App\Repository\OrderHistoryRepository;
use App\Repository\SellerRequestRepository;
use App\Services\AnalyticsService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
class SellerController extends AbstractController
{
    #[Route('/seller', name: 'app_seller')]
    public function Seller(Request $request, SellerRequestRepository $sellerRequestRepository, OrderDetailsRepository $orderDetailsRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('warning', 'Vous devez être connecté pour accéder à cette page.');
            return $this->redirectToRoute('front_app_login', [], Response::HTTP_SEE_OTHER);
        }
        //dd($user->getSellerRequest() !== null);
        if ($user->getSellerRequest() !== null && !in_array('ROLE_SELLER', $this->getUser()->getRoles(), true)) {
            $this->addFlash('warning', 'Vous avez déjà fait une demande pour devenir vendeur ou votre compte est désactivé.');
            return $this->redirectToRoute('front_app_product_index', [], Response::HTTP_SEE_OTHER);
        }
        $seller = new SellerRequest();

        $form = $this->createForm(BecomeSellerType::class, $seller);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $seller->setUserRequest($user);
            $sellerRequestRepository->save($seller, true);

            return $this->redirectToRoute('front_app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        $commission = 0.7;
        $totalSellProduct = $orderDetailsRepository->findProductByUser($user);
        $analytics = new AnalyticsService($totalSellProduct);
        $myProduct = $user->getProducts();

        return $this->render('front/seller/index.html.twig', [
            'form' => $form->createView(),
            'myProducts' => $myProduct,
            'SellProducts' => $totalSellProduct,
            'commission' => $commission,
            'analytics' => $analytics->getPercentsFromDate(),
        ]);
    }
}
