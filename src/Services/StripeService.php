<?php

namespace App\Services;

use App\Entity\Order;
use App\Entity\Product;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;

class StripeService
{
    private mixed $key;

    public function __construct(){
        if($_ENV['APP_ENV'] === 'dev'){
            $this->key = $_ENV['STRIPE_SECRET_KEY_TEST'];
        } else{
            $this->key = $_ENV['STRIPE_SECRET_KEY_TEST'];
        }
    }

    /**
     * @param int $price
     * @param string $description
     * @return PaymentIntent
     * @throws ApiErrorException
     */
    public function paymentIntent(int $price, string $description): \Stripe\PaymentIntent
    {
        \Stripe\Stripe::setApiKey($this->key);
        return \Stripe\PaymentIntent::create([
            'amount'=> $price * 100,
            'currency'=> Order::CURRENCY,
            'payment_method_types'=> ['card'],
            'description'=>'Commande de '.$description,
        ]);
    }

    /**
     * @param array $stripeParameters
     * @return PaymentIntent|null
     * @throws ApiErrorException
     */
    public function stripePayment(
        array $stripeParameters,
    ): ?PaymentIntent
    {
        \Stripe\Stripe::setApiKey($this->key);
        $payment_intent = null;
        if(isset($stripeParameters['stripeIntendId'])){
            $payment_intent = \Stripe\PaymentIntent::retrieve($stripeParameters['stripeIntendId']);
        }

        if($stripeParameters['stripeIntendStatus'] === 'succeeded'){
            //TODO
        } else{
            $payment_intent->cancel();
        }
        return $payment_intent;
    }

    public function stripeRefund(array $stripeParameters): \Stripe\Refund
    {
        \Stripe\Stripe::setApiKey($this->key);
        return \Stripe\Refund::create([
            "charge" => $stripeParameters['chargeId'],
            "amount" => $stripeParameters['amount'] * 100,
        ]);
    }
 
}