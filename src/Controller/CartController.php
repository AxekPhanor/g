<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\ClassPhp\Cart;

class CartController extends AbstractController
{
    #[Route('/mon-panier', name: 'app_cart')]
    public function index(Cart $cart): Response
    {
        dd($cart->get());
        return $this->render('cart/index.html.twig');
    }

    #[Route('/cart/add/{id}-{quantity}', name: 'add_to_cart')]
    public function add(Cart $cart, $id, $quantity)
    {   
        $cart->add($id, $quantity);
        return $this->redirectToRoute('app_product');
    }

    #[Route('/cart/delete', name: 'delete_cart')]
    public function delete(Cart $cart)
    {
        $cart->delete();
        return $this->redirectToRoute('app_cart');
    }
}
