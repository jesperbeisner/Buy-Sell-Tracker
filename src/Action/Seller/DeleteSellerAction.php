<?php

declare(strict_types=1);

namespace App\Action\Seller;

use App\Action\ActionInterface;
use App\Entity\Seller;
use App\Result\ActionResult;
use App\Result\Result;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class DeleteSellerAction implements ActionInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private EntityManagerInterface $entityManager,
    ) {}

    public function execute(): ActionResult
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null === $sellerId = $request->request->get('seller-id')) {
            return new ActionResult(Result::FAILURE, 'Keine Verkäufer-ID angegeben!');
        }

        if (null === $seller = $this->entityManager->getRepository(Seller::class)->find($sellerId)) {
            return new ActionResult(Result::FAILURE, 'Kein Verkäufer mit dieser ID gefunden!');
        }

        if ($seller->isDeleted()) {
            return new ActionResult(Result::FAILURE, 'Dieser Verkäufer ist bereits gelöscht!');
        }

        $seller->delete();
        $seller->setName($seller->getName() . '_deleted_' . time());

        $this->entityManager->persist($seller);
        $this->entityManager->flush();

        return new ActionResult(Result::SUCCESS, 'Der Verkäufer wurde erfolgreich gelöscht!');
    }
}
