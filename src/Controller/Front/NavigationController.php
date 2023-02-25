<?php

namespace App\Controller\Front;

use App\Entity\Cart;
use App\Entity\OrderHistory;
use App\Entity\OrderState;
use App\Entity\Product;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use App\Services\StripeService;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Exception\ApiErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Order;
use App\Entity\OrderDetails;

#[Route('/cart')]
class NavigationController extends AbstractController
{

}
