<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Form\RegisterUserType;
use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;

class RegisterController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function index(Request $request, EntityManagerInterface $entityManager, OrderRepository $orderRepository): Response
    {
        $user = new User();

        $form = $this->createForm(RegisterUserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Votre compte est correctement crée, veuillez vous connecter.'
            );
            
            //envoie d'un email de confirmation d'inscription
            $mail = new Mail();
            $vars = [
                'firstname'=> $user->getFirstname(),
            ];
            $mail->send($user->getEmail(), $user->getFirstname().' '.$user->getLastname(), 'Bienvenue sur la boutique française', 'welcome.html', $vars);
    
            
            return $this->redirectToRoute('app_login');
        }

        return $this->render('register/index.html.twig',[
            'registerForm' => $form->createView()
        ]);
    }
}