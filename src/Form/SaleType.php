<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Sale;
use App\Repository\ProductRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SaleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
                'attr' => ['class' => 'form-control']
            ])
            ->add('amount', NumberType::class, [
                'label' => 'Anzahl',
                'attr' => ['class' => 'form-control']
            ])
            ->add('blackMoney', NumberType::class, [
                'label' => 'Schwarzgeld',
                'attr' => ['class' => 'form-control']
            ])
            ->add('realMoney', NumberType::class, [
                'label' => 'Echtes Geld',
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
            ->add('product', EntityType::class, [
                'label' => 'Produkt',
                'class' => Product::class,
                'query_builder' => function (ProductRepository $repository) {
                    return $repository->createQueryBuilder('p')
                        ->where('p.deleted = :bool')
                        ->setParameter('bool', false)
                        ->orderBy('p.name', 'ASC');
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
            'data_class' => Sale::class,
        ]);
    }
}
