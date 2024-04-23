<?php

namespace App\Classe;

use Symfony\Component\HttpFoundation\RequestStack;

class Cart
{
    public function __construct(private RequestStack $requestStack)
    {

    }
    /**
     * add()
     * Fonction permettant l'ajout de produit au panier
     */
    public function add($product)
    {
        $cart = $this->getCart();

        if (isset($cart[$product->getId()])) {
            $cart[$product->getId()] = [
                'object' => $product,
                'qty' => $cart[$product->getId()]['qty'] + 1
            ];
        } else {
            $cart[$product->getId()] = [
                'object' => $product,
                'qty' => 1
            ];
        }
        $this->requestStack->getSession()->set('cart', $cart);
    }
        /**
     * add()
     * Fonction permettant de supprimer des produits du panier
     */
    public function decrease($id)
    {
        $cart = $this->getCart();
        if ($cart[$id]['qty'] > 1) {
            $cart[$id]['qty']= $cart[$id]['qty'] > -1;
        } else {
            unset($cart[$id]);
        }

        $this->requestStack->getSession()->set('cart', $cart);
    }
    /**
     * add()
     * Fonction permettant de supprimer l'ensemble du panier
     */
    public function remove()
    {
         $this->requestStack->getSession()->remove('cart');

    }
        /**
     * add()
     * Fonction permettant d'afficher le panier
     */
    public function getCart()
    {
        return $this->requestStack->getSession()->get('cart');
    }
        /**
     * add()
     * Fonction permettant d'afficher la totalité du prix du panier
     */
    public function getTotalWt()
    {
        $cart = $this->getCart();

        $price = 0;

        if (!isset($cart)) {
            return 0;
        }

        foreach ($cart as $product) {
            $price = $price + ($product['object']->getPriceWt() * $product['qty']);
        }
        return $price;
    }
    /**
     * add()
     * Fonction permettant de retourner le nombre total de produit au panier
     */
    public function fullQuantity()
    {
        $cart = $this->getCart();

        $quantity = 0;
        
        if (!isset($cart)) {
            return 0;
        }

        foreach ($cart as $product) {
            $quantity = $quantity + $product['qty'];
        }
        
        return $quantity;
    }
}
