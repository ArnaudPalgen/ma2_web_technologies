<?php

namespace App\Form;

use App\Entity\Location;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Positive;

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
            ->add('size', TextType::class, ['label' => 'Masse / Volume'])
            ->add('concentration', PercentType::class, [
                'label' => 'Concentration',
                'scale' => 2,
                'attr' => [
                    'max' => 100
                ],
                'constraints' => [new Positive()],
            ])
            ->add('count', IntegerType::class, [
                'label' => 'Quantité',
                'mapped' => false,
                'data' => 1,
                'constraints' => [new Positive()],
                'attr' => [
                    'min' => 1
                ]
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
                'data' => false,
            ])
            ->add('cid', HiddenType::class, [
                'mapped' => false
            ])
//            ->add('hazards', HiddenType::class, [
//                'empty_data' => null,
//                'mapped' => false,
//            ])
        ;


        $builder->get('concentration')
            ->addModelTransformer(new CallbackTransformer(
                function ($c) {
                    return $c / 10000;
                },
                function ($c) {
                    return $c * 10000;
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
