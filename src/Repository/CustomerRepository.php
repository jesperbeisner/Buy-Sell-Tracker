<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\Fraction;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Customer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer[]    findAll()
 * @method Customer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    public function findByName(string $name): ?Customer
    {
        $name = strtolower($name);

        /** @var Customer|null $result */
        $result = $this->createQueryBuilder('customer')
            ->where('LOWER(customer.name) = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();

        return $result;
    }

    public function setDeletedProductToNull(Product $product): void
    {
        $this->createQueryBuilder('customer')
            ->update()
            ->set('customer.product', ':null')
            ->where('customer.product = :product')
            ->setParameter('null', null)
            ->setParameter('product', $product)
            ->getQuery()
            ->execute()
        ;
    }

    public function setDeletedFractionToNull(Fraction $fraction): void
    {
        $this->createQueryBuilder('customer')
            ->update()
            ->set('customer.fraction', ':null')
            ->where('customer.fraction = :fraction')
            ->setParameter('null', null)
            ->setParameter('fraction', $fraction)
            ->getQuery()
            ->execute()
        ;
    }
}
