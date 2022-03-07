<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Fraction;
use App\Entity\Product;
use App\Entity\Purchase;
use App\Entity\Shift;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Purchase|null find($id, $lockMode = null, $lockVersion = null)
 * @method Purchase|null findOneBy(array $criteria, array $orderBy = null)
 * @method Purchase[]    findAll()
 * @method Purchase[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PurchaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Purchase::class);
    }

    /**
     * @return array<int, array<string, int|string>>
     */
    public function findEntriesByWeek(DateTime $startDate, DateTime $endDate): array
    {
        /** @var array<int, array<string, int|string>> $result */
        $result = $this->createQueryBuilder('purchase')
            ->select('product.id, product.name, sum(purchase.amount) as amount, sum(purchase.amount * purchase.price) as price')
            ->innerJoin('purchase.product', 'product')
            ->andWhere('purchase.created > :startDate')
            ->andWhere('purchase.created < :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->groupBy('product.id')
            ->getQuery()
            ->getResult();

        return $result;
    }

    public function setDeletedShiftToNull(Shift $shift): void
    {
        $this->createQueryBuilder('purchase')
            ->update()
            ->set('purchase.shift', ':null')
            ->where('purchase.shift = :shift')
            ->setParameter('null', null)
            ->setParameter('shift', $shift)
            ->getQuery()
            ->execute()
        ;
    }

    public function setDeletedProductToNull(Product $product): void
    {
        $this->createQueryBuilder('purchase')
            ->update()
            ->set('purchase.product', ':null')
            ->where('purchase.product = :product')
            ->setParameter('null', null)
            ->setParameter('product', $product)
            ->getQuery()
            ->execute()
        ;
    }

    public function setDeletedFractionToNull(Fraction $fraction): void
    {
        $this->createQueryBuilder('purchase')
            ->update()
            ->set('purchase.fraction', ':null')
            ->where('purchase.fraction = :fraction')
            ->setParameter('null', null)
            ->setParameter('fraction', $fraction)
            ->getQuery()
            ->execute()
        ;
    }
}
