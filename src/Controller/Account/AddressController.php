<?php

namespace App\Controller\Account;

use App\Classe\Cart;
use App\Entity\Address;
use App\Form\AddressUserType;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AddressController extends AbstractController
{
    #[Route('/compte/adresses', name: 'app_account_addresses')]
    public function index(): Response
    {
        return $this->render('account/address/index.html.twig');
    }

    #[Route('/compte/adresses/delete/{id}', name: 'app_account_address_delete')]
    public function Delete($id,AddressRepository $addressRepository, EntityManagerInterface $entityManager): Response
    {
        $address = $addressRepository->findOneById($id);
        if (!$address or $address->getUser() !== $this->getUser()) {
                return $this->redirectToRoute('app_account_addresses');
            }
        $this->addFlash(
                'success',
                'Votre adresse a bien été supprimé'
                );
        $entityManager->remove($address);
        $entityManager->flush();


        return $this->redirectToRoute('app_account_addresses');
    }

    #[Route('/compte/adresse/ajouter/{id}', name: 'app_account_address_form')]
    public function Form(Request $request, EntityManagerInterface $entityManager,AddressRepository $addressRepository,Cart $cart, $id = null): Response
    {
        if ($id) {
            $address = $addressRepository->findOneById($id);
            if (!$address or $address->getUser() !== $this->getUser()) {
                return $this->redirectToRoute('app_account_addresses');
            }
        } else {
            $address = new Address();
            $address->setUser($this->getUser());
        }
       
        $form = $this->createForm(AddressUserType::class, $address);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($address);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Votre adresse a bien été ajouté'
                );
                
                if ($cart->fullQuantity() > 0) {

                    return $this->redirectToRoute('app_order');
                }
            
        
            return $this->redirectToRoute('app_account_addresses');
        }

        
        return $this->render('account/address/form.html.twig', [
            'addressForm'=> $form->createView()
        ]);
    }
}


?>