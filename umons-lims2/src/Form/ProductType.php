<?php

namespace App\Form;

use App\Entity\Location;
use App\Entity\Product;
use Doctrine\DBAL\Types\BooleanType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ncas', TextType::class, [
                'label' => 'Numéro CAS',
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom du produit',
            ])
            ->add('size', TextType::class, ['label' => 'Taille'])
            ->add('concentration', TextType::class, ['label' => 'Concentration'])
            ->add('count', IntegerType::class, [
                'label' => 'Quantité',
                'mapped' => false,
                'data' => 1
            ])
            ->add('location', EntityType::class, [
                'label' => 'Emplacement',
                'class' => Location::class,
                'choice_label' => function ($category) {
                    return $category->getDisplayName();
                }
            ])
            ->add('isIgnoreConflict', HiddenType::class, [
                'empty_data' => false,
                'mapped' => false,
            ])
            ->add('hazards', HiddenType::class, [
                'empty_data' => null,
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
