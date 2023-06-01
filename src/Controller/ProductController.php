<?php

namespace App\Controller;

use App\Form\QuantityChoiceType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Forms;

class ProductController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/product', name: 'app_product')]
    public function index(Request $request): Response
    {
        $products = $this->entityManager->getRepository(Product::class)->findAll();
        $choiceArray = array();
        $choice = "";
        $productId = null;
        //dd($products[1]->getId());
        
        for($i=0; $i<count($products); $i++) 
        {
            $choiceArray[$i] =  $this->createForm(QuantityChoiceType::class);

            $choiceArray[$i]->handleRequest($request);
            if ($choiceArray[$i]->isSubmitted() && $choiceArray[$i]->isValid()) {
                $choice = (string) $choiceArray[$i]->getData()['quantity'];
                $productId = $products[$i]->getId();
                break;
            }
            $choiceArrayView[$i] = $choiceArray[$i]->createView();
        }
        
        if($productId != null){
            return $this->redirectToRoute('add_to_cart', [
                'id' => $productId,
                'quantity' => $choice,
            ]);
        }

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'form' => $choiceArrayView,
        ]);
    }
}
