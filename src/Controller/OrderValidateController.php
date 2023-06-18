<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Order;
use App\ClassPhp\Cart;

class OrderValidateController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/commande/merci/{stripeSessionId}', name: 'app_order_validate')]
    public function index($stripeSessionId, Cart $cart): Response
    {
        $cart->getFull();
        $cart->delete();
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);
        if(!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('home');
        }
        if(!$order->getIsPaid()){
            $order->setIsPaid(1);
            $this->entityManager->flush();
        }
        
        // Envoyer un email à notre client pour lui confirmer sa commande
        // Afficher les quelques informations de la commande de l'utilisateur

        return $this->render('order_validate/index.html.twig', [
            'order' => $order,
        ]);
    }
}
