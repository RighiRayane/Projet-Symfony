<?php

namespace App\Twig;

use App\Classe\Cart;
use Twig\TwigFilter;
use App\Entity\Category;
use Twig\Extension\AbstractExtension;
use App\Repository\CategoryRepository;
use Twig\Extension\GlobalsInterface;

class AppExtensions extends AbstractExtension implements GlobalsInterface
{
    private $cart;
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository, Cart $cart)
    {
        $this->categoryRepository = $categoryRepository;
        $this->cart = $cart;

    }

    public function getFilters()
    {
        return [
            new TwigFilter("price", [$this,"formatPrice"]),
        ];
    }

    public function formatPrice($price)
    {
        return number_format($price ,2 ,",").'€';
    }

    public function getGlobals(): array
    {
        return [
            'allCategories' => $this->categoryRepository->findAll(),
            'fullCartQuantity' => $this->cart->fullQuantity()
        ];
    }

}