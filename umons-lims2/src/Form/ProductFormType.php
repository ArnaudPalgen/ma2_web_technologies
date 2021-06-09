<?php

namespace App\Form;

use App\Entity\Location;
use App\Entity\Product;
use App\Service\CasChecker;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ProductFormType extends AbstractType
{

    private CasChecker $casChecker;

    public function __construct(CasChecker $casChecker)
    {
        $this->casChecker = $casChecker;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ncas', TextType::class, [
                'label' => 'Numéro CAS',
                'constraints' => [
                    new Callback(function ($value, ExecutionContextInterface $context) {
                        if (!$this->casChecker->isValid($value)) {
                            $context
                                ->buildViolation("Ce numéro CAS n'est pas valide.")
                                ->addViolation();
                        }

                    })
                ],
            ])
            ->add('name', TextType::class, ['label' => 'Nom du produit'])
            ->add('concentration', PercentType::class, [
                'label' => 'Concentration',
                'scale' => 2,
                'attr' => [
                    'min' => 0,
                    'max' => 100
                ],
                'constraints' => [
                    new Callback(function ($value, ExecutionContextInterface $context) {
                        $value /= 100;
                        if ($value > 100 || $value < 0) {
                            $context
                                ->buildViolation("Veuillez entrer un pourcentage compris entre 0% et 100%")
                                ->addViolation();
                        }

                    })
                ],
            ])
            ->add('size', TextType::class, ['label' => 'Masse / Volume'])
            ->add('location', EntityType::class, [
                'label' => 'Emplacement',
                'class' => Location::class,
                'choice_label' => function ($category) {
                    return $category->getDisplayName();
                }
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
            ->add('is_ignore_conflicts', HiddenType::class, [
                'empty_data' => false,
                'mapped' => false,
                'data' => false,
            ]);

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
