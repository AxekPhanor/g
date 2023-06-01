<?php

namespace App\ClassPhp;

use Symfony\Component\HttpFoundation\RequestStack;

class Cart
{
    private $session;

    public function __construct(private RequestStack $requestStack)
    {
        $this->session = $requestStack->getSession();
    }

    public function add($id, $quantity)
    {
        $cart = $this->session->get('cart', []);

        if(!empty($cart[$id])) {
            $cart[$id]+= $quantity;
        }
        else{
            $cart[$id] = $quantity;
        }
        $this->session->set('cart', $cart);
       
    }


    public function get()
    {
        return $this->session->get('cart');
    }

    public function remove($id){
        $this->session->set('cart', [
            'id' => $id,
            'quantity' => 0
        ]);
    }

    public function delete(){
        $this->session->remove('cart');
    }
}