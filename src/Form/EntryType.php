<?php

namespace App\Form;

use App\Entity\Entry;
use App\Entity\Product;
use App\Entity\Seller;
use App\Entity\Shift;
use App\Repository\ProductRepository;
use App\Repository\SellerRepository;
use App\Repository\ShiftRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntryType extends AbstractType
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
            ->add('shift', EntityType::class, [
                'label' => 'Schicht',
                'class' => Shift::class,
                'query_builder' => function (ShiftRepository $repository) {
                    return $repository->createQueryBuilder('s')
                        ->where('s.deleted = :bool')
                        ->setParameter('bool', false);
                },
                'choice_label' => 'time',
                'attr' => ['class' => 'form-control']
            ])
            ->add('product', EntityType::class, [
                'label' => 'Produkt',
                'class' => Product::class,
                'query_builder' => function (ProductRepository $repository) {
                    return $repository->createQueryBuilder('p')
                        ->where('p.deleted = :bool')
                        ->setParameter('bool', false);
                },
                'choice_label' => 'name',
                'attr' => ['class' => 'form-control']
            ])
            ->add('seller', EntityType::class, [
                'label' => 'Verkäufer',
                'class' => Seller::class,
                'query_builder' => function (SellerRepository $repository) {
                    return $repository->createQueryBuilder('s')
                        ->where('s.deleted = :bool')
                        ->setParameter('bool', false);
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
            'data_class' => Entry::class,
        ]);
    }
}
