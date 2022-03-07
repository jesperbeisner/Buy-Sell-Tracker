<?php

declare(strict_types=1);

namespace App\Action\Shift;

use App\Action\AbstractAction;
use App\Entity\Shift;
use App\Notifier\DiscordNotifier;
use App\Result\ActionResult;
use App\Result\Result;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class CreateShiftAction extends AbstractAction
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

        if (null === $shiftName = $request->request->get('shift')) {
            return new ActionResult(Result::FAILURE, 'Keine Schicht angegeben!');
        }

        $shiftName = (string) $shiftName;

        if (strlen($shiftName) < 6) {
            return new ActionResult(Result::FAILURE, 'Der Name für eine neue Schicht muss mindestens 6 Zeichen lang sein!');
        }

        if (null !== $this->entityManager->getRepository(Shift::class)->findByName($shiftName)) {
            return new ActionResult(Result::FAILURE, 'Eine Schicht mit diesem Namen existiert bereits!');
        }

        $shift = new Shift();
        $shift->setName($shiftName);

        $this->entityManager->persist($shift);
        $this->entityManager->flush();

        $this->discordNotifier->send(
            "Shift added",
            "Shift '$shiftName' was added successfully",
            DiscordNotifier::COLOR_GREEN
        );

        return new ActionResult(Result::SUCCESS, 'Die Schicht wurde erfolgreich hinzugefügt!');
    }
}
