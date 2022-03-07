<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Fraction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Fraction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fraction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fraction[]    findAll()
 * @method Fraction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FractionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fraction::class);
    }

    public function findByName(string $name): ?Fraction
    {
        $name = strtolower($name);

        /** @var Fraction|null $result */
        $result = $this->createQueryBuilder('fraction')
            ->where('LOWER(fraction.name) = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();

        return $result;
    }

    /**
     * @return Fraction[]
     */
    public function findAllOrderedByName(): array
    {
        /** @var Fraction[] $result */
        $result = $this->createQueryBuilder('fraction')
            ->orderBy('fraction.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        return $result;
    }
}
