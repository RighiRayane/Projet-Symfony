<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaymentController extends AbstractController
{
    #[Route('/commande/paiement/{id_order}', name: 'app_payment')]
    public function index($id_order, OrderRepository $orderRepository, EntityManagerInterface $entityManager): Response
    {
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

            //recuperer l'id utilisateur + si on tape l'id de la commande dans l'url sans etre un user (redirig -> home)
        $order = $orderRepository->findOneById([
            'id' => $id_order,
            'user' => $this->getUser()
        ]);

        if(!$order){
            return $this->redirectToRoute('app_home');
        }

        $product_for_stripe = [];

        
        // afficher les données du client 
        foreach ($order->getOrderDetails() as $product ) {
            $product_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => number_format($product->getProductPriceWt() * 100, 0, '', ''),
                    'product_data' => [
                        'name' => $product->getProductName(),
                        'images' => [
                            $_ENV['DOMAIN']. '/uploads/'.$product->getProductIllustration()
                        ]
                    ]
                  ],
                  'quantity' => $product->getProductQuantity()
            ];
        }

        // Afficher le transporteur n
        $product_for_stripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => number_format($order->getCarrierPrice() * 100, 0, '', ''),
                'product_data' => [
                    'name' => $order->getCarrierName(),
                    
                ]
              ],
              'quantity' => 1,
        ];
        
        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'line_items' => [[
             $product_for_stripe
            ]],
            'mode' => 'payment',
            'success_url' => $_ENV['DOMAIN']. '/commande/merci/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $_ENV['DOMAIN']. '/cancel.html/panier/annulation',
          ]);

          $order->setStripeSessionId($checkout_session->id);
          $entityManager->flush();

          return $this->redirect($checkout_session->url);
    }

    #[Route('/commande/merci/{stripe_session_id}', name: 'app_payment_success')]
    public function success($stripe_session_id, OrderRepository $orderRepository, EntityManagerInterface $entityManager, Cart $cart): Response
    {
        $order = $orderRepository->findOneBy([
            'stripe_session_id' => $stripe_session_id,
            'user' => $this->getUser()
        ]);

        if(!$order){
            return $this->redirectToRoute('app_home');
        }

        if($order->getState() == 1){
            $order->getState(2);
            $cart->remove();
        
        $entityManager->flush();
        }
        

        return $this->render('payment/success.html.twig', [
            'order' => $order,
        ]);

    }
}
