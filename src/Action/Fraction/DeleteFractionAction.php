<?php

declare(strict_types=1);

namespace App\Action\Fraction;

use App\Action\AbstractAction;
use App\Entity\Fraction;
use App\Entity\Purchase;
use App\Notifier\DiscordNotifier;
use App\Result\ActionResult;
use App\Result\Result;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class DeleteFractionAction extends AbstractAction
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

        if (null === $fractionId = $request->request->get('fraction-id')) {
            return new ActionResult(Result::FAILURE, 'Keine ID angegeben!');
        }

        if (null === $fraction = $this->entityManager->getRepository(Fraction::class)->find($fractionId)) {
            return new ActionResult(Result::FAILURE, 'Keine Fraktion mit dieser ID gefunden!');
        }

        $this->entityManager->getRepository(Purchase::class)->setDeletedFractionToNull($fraction);

        $this->entityManager->remove($fraction);
        $this->entityManager->flush();

        $this->discordNotifier->send(
            "Fraction deleted",
            "Fraction '{$fraction->getName()}' was successfully deleted",
            DiscordNotifier::COLOR_RED
        );

        return new ActionResult(Result::SUCCESS, 'Die Fraktion wurde erfolgreich gelöscht!');
    }
}
