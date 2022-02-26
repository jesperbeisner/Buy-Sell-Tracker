<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Sale;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sale|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sale|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sale[]    findAll()
 * @method Sale[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SaleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sale::class);
    }

    /**
     * @return array<int, array<string, int|string>>
     */
    public function findSalesByWeek(DateTime $startDate, DateTime $endDate): array
    {
        /** @var array<int, array<string, int|string>> $result */
        $result = $this->createQueryBuilder('s')
            ->select('p.id, p.name, sum(s.amount) as amount, sum(s.blackMoney) as blackMoney, sum(s.realMoney) as realMoney')
            ->innerJoin('s.product', 'p')
            ->andWhere('s.created > :startDate')
            ->andWhere('s.created < :endDate')
            ->andWhere('p.deleted = :status')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->setParameter('status', false)
            ->groupBy('p.id')
            ->getQuery()
            ->getResult()
        ;

        return $result;
    }
}
