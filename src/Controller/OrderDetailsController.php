<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Order;
use App\Entity\OrderDetails;
use Doctrine\ORM\EntityManagerInterface;

class OrderDetailsController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/compte/commande-details/{reference}', name: 'app_order_details')]
    public function index($reference): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByReference($reference);
        if($order->getUser() != $this->getUser()){
            return $this->redirectToRoute('app_account');
        }
        $orderDetails = $this->entityManager->getRepository(OrderDetails::class)->findBy(array('myOrder' => $order->getId()));
        return $this->render('order_details/index.html.twig',[
            'reference' => $reference,
            'order' => $order,
            'orderDetails' => $orderDetails,
        ]);
    }
}
