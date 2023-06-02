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
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    

    #[Route('/mon-panier', name: 'app_cart')]
    public function index(Cart $cart): Response
    {
        $cartComplete = [];
        if(!empty($cart->get())){
            foreach($cart->get() as $id => $quantity){
                $cartComplete[] = [
                    'product' => $this->entityManager->getRepository(Product::class)->findOneById($id),
                    'quantity' => $quantity,
                ];
            }
        }
        
        return $this->render('cart/index.html.twig', [
            'cart' => $cartComplete
        ]);
    }

    #[Route('/cart/add/{id}', name: 'add_to_cart')]
    public function add(Cart $cart, $id)
    {   
        $cart->add($id);
        return $this->redirectToRoute('app_product');
    }

    #[Route('/cart/delete', name: 'delete_cart')]
    public function delete(Cart $cart)
    {
        $cart->delete();
        return $this->redirectToRoute('app_cart');
    }
}
