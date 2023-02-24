<?php

namespace App\Services;

use App\Entity\Order;
use App\Entity\Product;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;

class StripeService
{
    private $key;

    public function __construct(){
        if($_ENV['APP_ENV'] === 'dev'){
            $this->key = $_ENV['STRIPE_PUBLIC_KEY_TEST'];
        } else{
            $this->key = $_ENV['STRIPE_PUBLIC_KEY_LIVE'];
        }
    }

    /**
     * @param Product $product
     * @return PaymentIntent
     * @throws ApiErrorException
     */
    public function paymentIntent(array $products): \Stripe\PaymentIntent
    {
        $total = array_reduce($products, function($acc, $produit) { return $acc + $produit->getPrice(); }, 0);
        \Stripe\Stripe::setApiKey($this->key);
        return \Stripe\PaymentIntent::create([
            'amount'=> $total * 100,
            'current'=> Order::CURRENT,
            'payment_method_type'=> ['card']
        ]);
    }


    public function payment(
        int $amount,
        string $currency,
        string $description,
        array $stripeParameters
    )
    {
        \Stripe\Stripe::setApiKey($this->key);
        $payment_intent = null;

        if(isset($stripeParameters['stripeIntentId'])){
            $payment_intent = \Stripe\PaymentIntent::retrieve($stripeParameters['stripeIntentId']);
        }

        if($stripeParameters['stripeIntentId'] === 'succeeded'){
            //TODO
        } else{
            $payment_intent->cancel();
        }
        return $payment_intent;
    }

    /**
     * @param array $stripeParameters
     * @param Product $product
     * @return PaymentIntent|null
     */
    public function stripePayment(array $stripeParameters, Product $product): ?PaymentIntent
    {
        return $this->payment(
            $product->getPrice() * 100,
            ORDER::CURRENT,
            $product->getTitle(),
            $stripeParameters
        );
    }
}