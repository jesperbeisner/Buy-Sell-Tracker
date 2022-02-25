<?php

declare(strict_types=1);

namespace App\Action\Seller;

use App\Entity\Seller;
use App\Result\ActionResult;
use App\Result\Result;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class CreateSellerAction
{
    public function __construct(
        private RequestStack $requestStack,
        private EntityManagerInterface $entityManager,
    ) {}

    public function __invoke(): ActionResult
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null === $sellerName = $request->request->get('seller')) {
            return new ActionResult(Result::FAILURE, 'Kein Verkäufer angegeben!');
        }

        if (strlen($sellerName) < 3) {
            return new ActionResult(Result::FAILURE, 'Der Name für einen neuen Verkäufer muss mindestens 3 Zeichen lang sein!');
        }

        if (null !== $this->entityManager->getRepository(Seller::class)->findByName($sellerName)) {
            return new ActionResult(Result::FAILURE, 'Ein Verkäufer mit diesem Namen existiert bereits!');
        }

        $seller = new Seller();
        $seller->setName($sellerName);

        $this->entityManager->persist($seller);
        $this->entityManager->flush();

        return new ActionResult(Result::SUCCESS, 'Der Verkäufer wurde erfolgreich hinzugefügt!');
    }
}
