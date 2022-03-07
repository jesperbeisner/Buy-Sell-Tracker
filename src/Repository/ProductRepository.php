<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findByName(string $name): ?Product
    {
        $name = strtolower($name);

        /** @var Product|null $result */
        $result = $this->createQueryBuilder('product')
            ->where('LOWER(product.name) = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();

        return $result;
    }

    /**
     * @return Product[]
     */
    public function findAllOrderedByName(): array
    {
        /** @var Product[] $result */
        $result = $this->createQueryBuilder('product')
            ->orderBy('product.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        return $result;
    }
}
