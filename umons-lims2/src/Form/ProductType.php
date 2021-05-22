<?php

namespace App\Form;

use App\Entity\Location;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
                'attr' => [
                    'id'=>'field_product_ncas'
                ]
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom du produit',
                'attr' => [
                    'id'=>'field_product_name'
                ]
            ])
            ->add('volume', TextType::class, ['label' => 'Volume'])
            ->add('mass', TextType::class, ['label' => 'Masse'])
            ->add('concentration', TextType::class, ['label' => 'Concentration'])
            ->add('location', EntityType::class, [
                'label' => 'Position',
                'class' => Location::class,
                'choice_label' => function ($category) {
                    return $category->getDisplayName();
                }
            ])
//            ->add('chemical_safeties', EntityType::class) # TODO add type
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
