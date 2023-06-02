<?php

namespace App\ClassPhp;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManagerInterface;


class Cart
{
    private $session;
    private $entityManager;

    public function __construct(private RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->session = $requestStack->getSession();
        $this->entityManager = $entityManager;
    }

    public function add($id)
    {
        $cart = $this->session->get('cart', []);
        if(!empty($cart[$id])) {
            $cart[$id]++;
        }
        else{
            $cart[$id] = 1;
        }
        $this->session->set('cart', $cart);
       
    }

    public function decrease($id)
    {
        $cart = $this->session->get('cart', []);
        if($cart[$id] > 1){
            $cart[$id]--;
        }
        else{
            $cart[$id] = 0;
        }
        $this->session->set('cart', $cart);
    }

    public function get()
    {
        return $this->session->get('cart');
    }

    public function remove($id){
        $cart = $this->session->get('cart', []);
        unset($cart[$id]);
        return $this->session->set('cart', $cart);
    }

    public function delete(){
        $this->session->remove('cart');
    }

    public function getFull(){
        $cartComplete = [];
        if(!empty($this->get())){
            foreach($this->get() as $id => $quantity){
                $productObject = $this->entityManager->getRepository(Product::class)->findOneById($id);
                if(!$productObject){
                    $this->delete($id);
                    continue;
                }

                $cartComplete[] = [
                    'product' => $productObject,
                    'quantity' => $quantity,
                ];
            }
        }
        return $cartComplete;
    }
}