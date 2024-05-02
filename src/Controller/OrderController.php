<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Form\OrderType;
use App\Entity\OrderDetail;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    /*
    * 1ere etape du tunnel d'achat
    * choix de l'adresse de livraison et du transporteur
    */

    #[Route('/commande/livraison', name: 'app_order')]
    public function index(): Response
    {
        
        $addresses = $this->getUser()->getAddresses();
        
        if (count($addresses) === 0){
            return $this->redirectToRoute('app_account_address_form');
        }
        $form = $this->createForm(OrderType::class,  null, [
            'addresses'=> $addresses,
            'action'=> $this->generateUrl('app_order_sumary')

        ]);


        return $this->render('order/index.html.twig', [
            'deliveryForm'=> $form->createView(),
        ]
        );
    }

    /*
    * 2eme etape du tunnel d'achat
    * Recap de la commande l'utilisateur
    * insertion en bdd
    *preparation du paiement vers stripe
    */

    #[Route('/commande/recapitulatif', name: 'app_order_sumary')]
    public function add(Request $request, Cart $cart, EntityManagerInterface $entityManager): Response
    {

        if ($request->isMethod('POST') != 'POST') {

            return $this->redirectToRoute('app_cart');
        }

        $products = $cart->getCart();
        $form = $this->createForm(OrderType::class,  null, [
            'addresses'=> $this->getUser()->getAddresses()    
        ]);

        $form->handleRequest($request);

        // Si formulaire soumis, on stock les données en bdd
        if ($form->isSubmitted() && $form->isValid()) {

            // récuperation de l'addresse de l'utilisateur
            $addressObj = $form->get('addresses')->getData();

            $address = $addressObj->getFirstname().' '.$addressObj->getLastname(). '<br>';
            $address .= $addressObj->getAddress(). '<br>';
            $address .= $addressObj->getPostal().''.$addressObj->getCity(). '<br>';
            $address .= $addressObj->getCountry(). '<br>';
            $address .= $addressObj->getPhone();

            // récupération des données de commande de l'utilisateur
            $order = new Order();
            $order->setCreatedAt(new \DateTime());
            $order->setState(1);
            $order->setCarrierName($form->get('carriers')->getData()->getName());
            $order->setCarrierPrice($form->get('carriers')->getData()->getPrice());
            $order->setDelivery($address);

            // récupération du panier de l'utilisateur
            foreach ($products as $product) {
                $orderDetail = new OrderDetail();
                $orderDetail->setProductName($product['object']->getName());
                $orderDetail->setProductIllustration($product['object']->getIllustration());
                $orderDetail->setProductQuantity($product['qty']);
                $orderDetail->setProductPrice($product['object']->getPrice());
                $orderDetail->setProductTva($product['object']->getTva());
                $order->addOrderDetail($orderDetail);

            }
            $entityManager->persist($order);
            $entityManager->flush();

         
        }
        

        return $this->render('order/sumary.html.twig', [
            'choices'=> $form->getData(),
            'cart' => $products,
            'totalWt'=> $cart->getTotalWt(),
        ]);
    }
}

