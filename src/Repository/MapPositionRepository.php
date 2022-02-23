<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\MapPosition;
use Cassandra\Map;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MapPosition|null find($id, $lockMode = null, $lockVersion = null)
 * @method MapPosition|null findOneBy(array $criteria, array $orderBy = null)
 * @method MapPosition[]    findAll()
 * @method MapPosition[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MapPositionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MapPosition::class);
    }

    public function deleteAll(): void
    {
        $this->createQueryBuilder('mp')
            ->delete()
            ->getQuery()
            ->execute();
    }

    public function getLastEntry(): ?MapPosition
    {
        return $this->createQueryBuilder('mp')
            ->orderBy('mp.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
