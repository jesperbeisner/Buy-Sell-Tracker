<?php

declare(strict_types=1);

namespace App\Controller;

use App\Action\Purchase\DeletePurchaseAction;
use App\Action\Sale\DeleteSaleAction;
use App\Entity\Purchase;
use App\Entity\Sale;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OverviewController extends AbstractController
{
    #[Route('/overview', name: 'overview', methods: ['GET'])]
    public function overview(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_USER');

        return $this->render('overview/index.html.twig', [
            'purchases' => $entityManager->getRepository(Purchase::class)->findBy([], ['created' => 'DESC']),
            'sales' => $entityManager->getRepository(Sale::class)->findBy([], ['created' => 'DESC']),
        ]);
    }

    #[Route('/purchases/{id<\d+>}', name: 'delete-purchase', methods: ['DELETE'])]
    public function deletePurchase(int $id, DeletePurchaseAction $deletePurchaseAction): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_USER');

        $deletePurchaseAction->setId($id);
        $deletePurchaseResult = $deletePurchaseAction->execute();

        /** @var array<string, int> $data */
        $data = $deletePurchaseResult->getData();

        return new JsonResponse(['message' => $deletePurchaseResult->getMessage()], $data['code']);
    }

    #[Route('/sales/{id<\d+>}', name: 'delete-sale', methods: ['DELETE'])]
    public function deleteSale(int $id, DeleteSaleAction $deleteSaleAction): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_USER');

        $deleteSaleAction->setId($id);
        $deleteSaleResult = $deleteSaleAction->execute();

        /** @var array<string, int> $data */
        $data = $deleteSaleResult->getData();

        return new JsonResponse(['message' => $deleteSaleResult->getMessage()], $data['code']);
    }
}
