<?php

declare(strict_types=1);

namespace App\Action\Seller;

use App\Action\AbstractAction;
use App\Action\ActionInterface;
use App\Entity\Seller;
use App\Notifier\DiscordNotifier;
use App\Result\ActionResult;
use App\Result\Result;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

final class DeleteSellerAction extends AbstractAction implements ActionInterface
{
    public function __construct(
        Security $security,
        RequestStack $requestStack,
        DiscordNotifier $discordNotifier,
        private EntityManagerInterface $entityManager,
    ) {
        parent::__construct($security, $requestStack, $discordNotifier);
    }

    public function execute(): ActionResult
    {
        $request = $this->getRequest();

        if (null === $sellerId = $request->request->get('seller-id')) {
            return new ActionResult(Result::FAILURE, 'Keine Verkäufer-ID angegeben!');
        }

        if (null === $seller = $this->entityManager->getRepository(Seller::class)->find($sellerId)) {
            return new ActionResult(Result::FAILURE, 'Kein Verkäufer mit dieser ID gefunden!');
        }

        if ($seller->isDeleted()) {
            return new ActionResult(Result::FAILURE, 'Dieser Verkäufer ist bereits gelöscht!');
        }

        $oldSellerName = $seller->getName();

        $seller->delete();
        $seller->setName($seller->getName() . '_deleted_' . time());

        $this->entityManager->persist($seller);
        $this->entityManager->flush();

        $this->discordNotifier->send(
            "Seller deleted",
            "Seller '$oldSellerName' was successfully deleted",
            DiscordNotifier::COLOR_RED
        );

        return new ActionResult(Result::SUCCESS, 'Der Verkäufer wurde erfolgreich gelöscht!');
    }
}
