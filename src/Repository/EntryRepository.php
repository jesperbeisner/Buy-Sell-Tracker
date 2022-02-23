<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Entry;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Entry|null find($id, $lockMode = null, $lockVersion = null)
 * @method Entry|null findOneBy(array $criteria, array $orderBy = null)
 * @method Entry[]    findAll()
 * @method Entry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Entry::class);
    }

    public function findEntriesByWeek(DateTime $startDate, DateTime $endDate): array
    {
        return $this->createQueryBuilder('e')
            ->select('p.id, p.name, sum(e.amount) as amount, sum(e.amount * e.price) as price')
            ->innerJoin('e.product', 'p')
            ->andWhere('e.created > :startDate')
            ->andWhere('e.created < :endDate')
            ->andWhere('p.deleted = :status')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->setParameter('status', false)
            ->groupBy('p.id')
            ->getQuery()
            ->getResult()
        ;
    }
}
