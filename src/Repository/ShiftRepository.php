<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Shift;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Shift|null find($id, $lockMode = null, $lockVersion = null)
 * @method Shift|null findOneBy(array $criteria, array $orderBy = null)
 * @method Shift[]    findAll()
 * @method Shift[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShiftRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Shift::class);
    }

    public function findByName(string $name): ?Shift
    {
        $name = strtolower($name);

        /** @var Shift|null $result */
        $result = $this->createQueryBuilder('shift')
            ->where('LOWER(shift.name) = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();

        return $result;
    }

    /**
     * @return Shift[]
     */
    public function findAllOrderedByName(): array
    {
        /** @var Shift[] $result */
        $result = $this->createQueryBuilder('shift')
            ->orderBy('shift.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        return $result;
    }
}
