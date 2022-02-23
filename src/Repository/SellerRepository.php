<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Seller;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Seller|null find($id, $lockMode = null, $lockVersion = null)
 * @method Seller|null findOneBy(array $criteria, array $orderBy = null)
 * @method Seller[]    findAll()
 * @method Seller[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SellerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Seller::class);
    }
}
