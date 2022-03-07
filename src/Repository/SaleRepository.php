<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Product;
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
        $result = $this->createQueryBuilder('sale')
            ->select('product.id, product.name, sum(sale.amount) as amount, sum(sale.blackMoney) as blackMoney, sum(sale.realMoney) as realMoney')
            ->innerJoin('sale.product', 'product')
            ->andWhere('sale.created > :startDate')
            ->andWhere('sale.created < :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->groupBy('product.id')
            ->getQuery()
            ->getResult()
        ;

        return $result;
    }

    public function setDeletedProductToNull(Product $product): void
    {
        $this->createQueryBuilder('sale')
            ->update()
            ->set('sale.product', ':null')
            ->where('sale.product = :product')
            ->setParameter('null', null)
            ->setParameter('product', $product)
            ->getQuery()
            ->execute()
        ;
    }
}
