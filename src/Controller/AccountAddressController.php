<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountAddressController extends AbstractController
{
    #[Route('/compte/adresse', name: 'app_account_address')]
    public function index(): Response
    {
        return $this->render('account/address.html.twig');
    }

    #[Route('/compte/ajouter-adresse', name: 'app_account_address_add')]
    public function add()
    {
        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);
        return $this->render('account/addressAdd.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
