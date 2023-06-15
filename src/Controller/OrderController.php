<?php

namespace App\Controller;

use App\Form\OrderType;
use App\ClassPhp\Cart;
use App\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class OrderController extends AbstractController
{
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

    #[Route('/commande/recapitulatif', name: 'app_order_recap')]
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
            dd($deliveryContent);
            
            $order = new Order();
            $order->setUser($this->getUser());
            $order->setCreatedAt(new \DateTime());
            $order->setCarrierName($carrier->getName());
            $oder->setCarrierPrice($carrier->getPrice());
            $order->setDelivery($deliveryContent);
            $order->setIsPaid(0);

            // register OrderDetail
            foreach($cart->getFull() as $product)
            {
                dd($product);
            }
        }

        return $this->render('order/add.html.twig',[
            'cart' => $cart->getFull(),
        ]);
    }
}
