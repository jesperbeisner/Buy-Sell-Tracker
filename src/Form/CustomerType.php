<?php

namespace App\Form;

use App\Entity\Customer;
use App\Entity\Fraction;
use App\Entity\Product;
use App\Repository\FractionRepository;
use App\Repository\ProductRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
                'attr' => ['class' => 'form-control']
            ])
            ->add('condition', NumberType::class, [
                'label' => 'Preis/Stück',
                'attr' => ['class' => 'form-control']
            ])
            ->add('product', EntityType::class, [
                'label' => 'Produkt',
                'class' => Product::class,
                'query_builder' => function (ProductRepository $repository) {
                    return $repository->createQueryBuilder('product')
                        ->orderBy('product.name', 'ASC');
                },
                'choice_label' => 'name',
                'attr' => ['class' => 'form-control']
            ])
            ->add('fraction', EntityType::class, [
                'label' => 'Fraktion',
                'class' => Fraction::class,
                'query_builder' => function (FractionRepository $repository) {
                    return $repository->createQueryBuilder('fraction')
                        ->orderBy('fraction.name', 'ASC');
                },
                'choice_label' => 'name',
                'attr' => ['class' => 'form-control']
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Speichern',
                'attr' => ['class' => 'btn btn-primary'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
