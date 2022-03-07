<?php

namespace App\Form;

use App\Entity\Purchase;
use App\Entity\Product;
use App\Entity\Fraction;
use App\Entity\Shift;
use App\Repository\ProductRepository;
use App\Repository\FractionRepository;
use App\Repository\ShiftRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount', NumberType::class, [
                'label' => 'Anzahl',
                'attr' => ['class' => 'form-control']
            ])
            ->add('price', NumberType::class, [
                'label' => 'Preis/Stück',
                'attr' => ['class' => 'form-control']
            ])
            ->add('created', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy',
                'html5' => false,
                'years' => [2022],
                'label' => 'Datum',
                'attr' => ['class' => 'form-control']
            ])
            ->add('shift', EntityType::class, [
                'label' => 'Schicht',
                'class' => Shift::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'form-control']
            ])
            ->add('product', EntityType::class, [
                'label' => 'Produkt',
                'class' => Product::class,
                'query_builder' => function (ProductRepository $repository) {
                    return $repository->createQueryBuilder('p')
                        ->orderBy('p.name', 'ASC');
                },
                'choice_label' => 'name',
                'attr' => ['class' => 'form-control']
            ])
            ->add('fraction', EntityType::class, [
                'label' => 'Verkäufer',
                'class' => Fraction::class,
                'query_builder' => function (FractionRepository $repository) {
                    return $repository->createQueryBuilder('s')
                        ->orderBy('s.name', 'ASC');
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
            'data_class' => Purchase::class,
        ]);
    }
}
