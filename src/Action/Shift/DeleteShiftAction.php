<?php

declare(strict_types=1);

namespace App\Action\Shift;

use App\Action\AbstractAction;
use App\Entity\Purchase;
use App\Entity\Shift;
use App\Notifier\DiscordNotifier;
use App\Result\ActionResult;
use App\Result\Result;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class DeleteShiftAction extends AbstractAction
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

        if (null === $shiftId = $request->request->get('shift-id')) {
            return new ActionResult(Result::FAILURE, 'Keine ID angegeben!');
        }

        if (null === $shift = $this->entityManager->getRepository(Shift::class)->find($shiftId)) {
            return new ActionResult(Result::FAILURE, 'Keine Schicht mit dieser ID gefunden!');
        }

        $this->entityManager->getRepository(Purchase::class)->setDeletedShiftToNull($shift);

        $this->entityManager->remove($shift);
        $this->entityManager->flush();

        $this->discordNotifier->send(
            "Shift deleted",
            "Shift '{$shift->getName()}' was successfully deleted",
            DiscordNotifier::COLOR_RED
        );

        return new ActionResult(Result::SUCCESS, 'Die Schicht wurde erfolgreich gelöscht!');
    }
}
