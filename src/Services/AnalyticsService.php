<?php

namespace App\Services;

use App\Entity\Order;
use App\Entity\Product;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;

class AnalyticsService
{
    private mixed $Orders;
    public function __construct($Orders)
    {
        $this->Orders = $Orders;
        $this->getPercentsFromDate($Orders);
    }

    function getPercentsFromDate(): float {
        $date = new \DateTime();
        $date->modify('-7 day');
        $Va = 1;
        $Vd = 1;
        foreach ($this->Orders as $order) {
            if ($date > $order->getOrderId()->getCreatedAt()) {
                $Vd++;
            } else {
                $Va++;
            }
        }
        // taux de variation
        return (($Va - $Vd) / $Vd) * 100;
    }

}