<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\ClassPhp\Cart;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

class CartController extends AbstractController
{

    #[Route('/mon-panier', name: 'app_cart')]
    public function index(Cart $cart): Response
    {
        return $this->render('cart/index.html.twig', [
            'cart' => $cart->getFull()
        ]);
    }

    #[Route('/cart/add/{id}', name: 'add_to_cart')]
    public function add(Cart $cart, $id)
    {   
        $cart->add($id);
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/decrease/{id}', name: 'decrease_to_cart')]
    public function decrease(Cart $cart, $id)
    {   
        $cart->decrease($id);
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/delete', name: 'delete_cart')]
    public function delete(Cart $cart)
    {
        $cart->delete();
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/remove/{id}', name: 'remove_cart')]
    public function remove(Cart $cart, $id)
    {
        $cart->remove($id);
        return $this->redirectToRoute('app_cart');
    }
}
