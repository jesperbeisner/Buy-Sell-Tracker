<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Entry;
use App\Entity\Product;
use App\Entity\Sale;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class EvaluationService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function getEvaluationData(DateTime $startDate, DateTime $endDate): array
    {
        $data = [];

        $productRepository = $this->entityManager->getRepository(Product::class);
        $products = $productRepository->findBy(['deleted' => false], ['name' => 'ASC']);

        foreach ($products as $product) {
            $data[$product->getId()] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'purchases' => ['amount' => 0, 'price' => 0],
                'sales' => ['amount' => 0, 'blackMoney' => 0, 'realMoney' => 0]
            ];
        }

        $purchases = $this->entityManager->getRepository(Entry::class)->findEntriesByWeek($startDate, $endDate);
        $sales = $this->entityManager->getRepository(Sale::class)->findSalesByWeek($startDate, $endDate);

        foreach ($purchases as $purchase) {
            if (array_key_exists($purchase['id'], $data)) {
                $data[$purchase['id']]['purchases']['amount'] = $purchase['amount'];
                $data[$purchase['id']]['purchases']['price'] = $purchase['price'];
            }
        }

        foreach ($sales as $sale) {
            if (array_key_exists($sale['id'], $data)) {
                $data[$sale['id']]['sales']['amount'] = $sale['amount'];
                $data[$sale['id']]['sales']['blackMoney'] = $sale['blackMoney'];
                $data[$sale['id']]['sales']['realMoney'] = $sale['realMoney'];
            }
        }

        return $data;
    }
}
