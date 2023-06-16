<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Stripe;
use \Stripe\Checkout\Session;
use App\ClassPhp\Cart;

class StripeController extends AbstractController
{
    #[Route('/commande/stripe', name: 'app_stripe')]
    public function index(Cart $cart): Response
    {
        $productStripe = [];
        $YOUR_DOMAIN = 'http://127.0.0.1:8000';
        foreach($cart->getFull() as $product)
        {
            $productStripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                    'name' => $product['product']->getName(),
                    'images' => [$YOUR_DOMAIN."/uploads/images/".$product['product']->getIllustration()],
                    ],
                    'unit_amount' => $product['product']->getPrice(),
                ],
                'quantity' => $product['quantity'],
            ];
            //dd($YOUR_DOMAIN."/uploads/images/".$product['product']->getIllustration());
        }
        Stripe::setApiKey('sk_test_51NJYiLC3HU3cCxJAjEbmvsNkZalVVBqkX3xOQIU0kPIq5xcM1600Zw5UlY20z2Z68575YyG7KQt2fP3MQAMCPOl3002mcCUy7K');
        $checkout_session = Session::create([
            'line_items' => [$productStripe],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/success.html',
            'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
        ]);
        return $this->redirect($checkout_session->url);
    }
}
