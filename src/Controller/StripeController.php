<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Stripe;
use \Stripe\Checkout\Session;
use App\ClassPhp\Cart;
use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

class StripeController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/commande/stripe/{reference}', name: 'app_stripe')]
    public function index($reference): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByReference($reference);
        if(!$order){
            return $this->redirectToRoute('app_order');
        }

        $productStripe = [];
        $YOUR_DOMAIN = 'http://127.0.0.1:8000';
        foreach($order->getOrderDetails()->getValues() as $product)
        {
            $productObject = $this->entityManager->getRepository(Product::class)->findOneByName($product->getProduct());
            $productStripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                    'name' => $product->getProduct(),
                    'images' => [$YOUR_DOMAIN."/uploads/images/".$productObject->getIllustration()],
                    ],
                    'unit_amount' => $product->getPrice(),
                ],
                'quantity' => $product->getQuantity(),
            ];
        }

        $productStripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                'name' => $order->getCarrierName(),
                'images' => [$YOUR_DOMAIN],
                ],
                'unit_amount' => $order->getCarrierPrice() * 100,
            ],
            'quantity' => 1,
        ];

        Stripe::setApiKey('sk_test_51NJYiLC3HU3cCxJAjEbmvsNkZalVVBqkX3xOQIU0kPIq5xcM1600Zw5UlY20z2Z68575YyG7KQt2fP3MQAMCPOl3002mcCUy7K');
        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'line_items' => [$productStripe],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/commande/merci/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . '/commande/erreur/{CHECKOUT_SESSION_ID}',
        ]);

        $order->setStripeSessionId($checkout_session->id);
        $this->entityManager->flush();
        return $this->redirect($checkout_session->url);
    }
}
