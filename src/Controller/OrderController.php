<?php

namespace App\Controller;

use App\Form\OrderType;
use App\ClassPhp\Cart;
use App\Entity\Order;
use App\Entity\User;
use App\Entity\OrderDetails;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class OrderController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/commande', name: 'app_order')]
    public function index(Cart $cart): Response
    {
        if(!$this->getUser()->getAddresses()->getValues()){
            return $this->redirectToRoute('app_account_address_add');
        }
        $form = $this -> createForm(OrderType::class, null, [
            'user' => $this->getUser(),
        ]);

        return $this->render('order/index.html.twig',[
            'form' => $form->createView(),
            'cart' => $cart->getFull(),
        ]);
    }

    #[Route('/commande/recapitulatif', name: 'app_order_recap', methods: ['POST'])]
    public function add(Cart $cart, Request $request): Response
    {
        $form = $this -> createForm(OrderType::class, null, [
            'user' => $this->getUser(),
        ]);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            // register Order
            $carrier = $form->get('carrier')->getData();
            $delivery = $form->get('address')->getData();

            $deliveryContent = $delivery->getFirstName().' '.$delivery->getLastName();
            $deliveryContent.='<br/>'.$delivery->getPhone();
            if($delivery->getCompany()){
                $deliveryContent.='<br/>'.$delivery->getCompany();
            }
            $deliveryContent.='<br/>'.$delivery->getAddress();
            $deliveryContent.='<br/>'.$delivery->getPostal().' '.$delivery->getCity();;
            $deliveryContent.='<br/>'.$delivery->getCountry();
            
            $order = new Order();
            $date = new \DateTimeImmutable();
            $order->setUser($this->getUser());
            $order->setCreatedAt($date);
            $order->setCarrierName($carrier->getName());
            $order->setCarrierPrice($carrier->getPrice());
            $order->setDelivery($deliveryContent);
            $order->setIsPaid(0);

            $this->entityManager->persist($order);

            // register OrderDetails
            foreach($cart->getFull() as $product)
            {
                $orderDetails = new OrderDetails();
                $orderDetails->setMyOrder($order);
                $orderDetails->setProduct($product['product']->getName());
                $orderDetails->setQuantity($product['quantity']);
                $orderDetails->setPrice($product['product']->getPrice());
                $orderDetails->setTotal($product['product']->getPrice() * $product['quantity']);
                $this->entityManager->persist($orderDetails);
            }

            // adding my firstName and lastName to user
            $user = $this->getUser();
            $user->setFirstName($delivery->getFirstName());
            $user->setLastName($delivery->getLastName());
            $this->entityManager->persist($user);

            $this->entityManager->flush();

            return $this->render('order/add.html.twig',[
                'cart' => $cart->getFull(),
                'carrier' => $carrier,
                'deliveryContent' => $deliveryContent,
            ]);
        }
    }
}
