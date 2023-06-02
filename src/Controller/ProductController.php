<?php

namespace App\Controller;

use App\Form\QuantityChoiceType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use App\Entity\User;
use App\ClassPhp\Cart;
use Doctrine\ORM\EntityManagerInterface;

class ProductController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/product', name: 'app_product')]
    public function index(): Response
    {
        $products = $this->entityManager->getRepository(Product::class)->findAll();
        
        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/cartP/add/{id}', name: 'add_to_cartP')]
    public function add(Cart $cart, $id)
    {   
        $cart->add($id);
        return $this->redirectToRoute('app_product');
    }
}
