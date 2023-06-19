<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Order;

class AccountController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/compte', name: 'app_account')]
    public function index(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);
        $passwordIsChange = false;
        $form ->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($hashedPassword);

            $user = $form->getData();
            $entityManager->persist($user);
            $entityManager->flush();
            $passwordIsChange = true;
        }
        $orders = $this->entityManager->getRepository(Order::class)->findBy(array('user' => $user->getId()));
        //TODO: Afficher du dernier au premier élément (il faudra probablement créer des fonctions de sorting)
        return $this->render('account/index.html.twig', [
            'controller_name' => 'AccountController',
            'email' => $user->getEmail(), 
            'form' => $form->createView(),
            'passwordIsChange' => $passwordIsChange,
            'orders' => $orders,
        ]);
    }
}
