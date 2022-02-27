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

final class CreateSellerAction extends AbstractAction implements ActionInterface
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

        if (null === $sellerName = $request->request->get('seller')) {
            return new ActionResult(Result::FAILURE, 'Kein Verkäufer angegeben!');
        }

        $sellerName = (string) $sellerName;

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

        $this->discordNotifier->send(
            "Seller added",
            "Seller '$sellerName' was added successfully",
            DiscordNotifier::COLOR_GREEN
        );

        return new ActionResult(Result::SUCCESS, 'Der Verkäufer wurde erfolgreich hinzugefügt!');
    }
}
