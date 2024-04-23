<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use Doctrine\DBAL\Types\FloatType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\TextEditorType;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Produit')
            ->setEntityLabelInPlural('Produits')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        $required = true;

        if ($required == 'edit'){
        $required = false;
        }

        return [
            TextField::new('name')->setLabel('Nom')->setHelp('Nom du produit'),
            SlugField::new('slug')->setTargetFieldName('name')->setLabel('URL'),
            TextEditorField::new('description')->setLabel('Description')->setHelp('Description du produit'),
            ImageField::new('illustration')->setLabel('Image')
                        ->setHelp('Image du produit en 600x600')
                        ->setUploadedFileNamePattern('[day]-[month]-[year]-[slug]-[contenthash.[extension]')
                        ->setBasePath('/uploads')
                        ->setUploadDir('/public/uploads')
                        ->setRequired($required),
            NumberField::new('price')->setLabel('Prix H.T')->setHelp('Prix du produit sans le sigle €'),
            ChoiceField::new('tva')->setLabel('Taux T.V.A')->setChoices([
                '5.5%' => '5.5',
                '10%' => '10',
                '20%' => '20'
            ]),
            AssociationField::new('category')->setLabel('Catégorie associée'),
        ];
    }
}
