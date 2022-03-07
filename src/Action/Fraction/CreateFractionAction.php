<?php

declare(strict_types=1);

namespace App\Action\Fraction;

use App\Action\AbstractAction;
use App\Entity\Fraction;
use App\Notifier\DiscordNotifier;
use App\Result\ActionResult;
use App\Result\Result;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class CreateFractionAction extends AbstractAction
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

        if (null === $fractionName = $request->request->get('fraction')) {
            return new ActionResult(Result::FAILURE, 'Keine Fraktion angegeben!');
        }

        $fractionName = (string) $fractionName;

        if (strlen($fractionName) < 2) {
            return new ActionResult(Result::FAILURE, 'Der Name für eine neue Fraktion muss mindestens 2 Zeichen lang sein!');
        }

        if (null !== $this->entityManager->getRepository(Fraction::class)->findByName($fractionName)) {
            return new ActionResult(Result::FAILURE, 'Eine Fraktion mit diesem Namen existiert bereits!');
        }

        $fraction = new Fraction();
        $fraction->setName($fractionName);

        $this->entityManager->persist($fraction);
        $this->entityManager->flush();

        $this->discordNotifier->send(
            "Fraction added",
            "Fraction '$fractionName' was added successfully",
            DiscordNotifier::COLOR_GREEN
        );

        return new ActionResult(Result::SUCCESS, 'Die Fraktion wurde erfolgreich hinzugefügt!');
    }
}
