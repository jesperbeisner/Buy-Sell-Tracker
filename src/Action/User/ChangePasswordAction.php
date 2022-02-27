<?php

declare(strict_types=1);

namespace App\Action\User;

use App\Action\AbstractAction;
use App\Action\ActionInterface;
use App\Notifier\DiscordNotifier;
use App\Result\ActionResult;
use App\Result\Result;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;

final class ChangePasswordAction extends AbstractAction implements ActionInterface
{
    public function __construct(
        Security $security,
        RequestStack $requestStack,
        DiscordNotifier $discordNotifier,
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct($security, $requestStack, $discordNotifier);
    }

    public function execute(): ActionResult
    {
        $request = $this->getRequest();
        $user = $this->getUser();

        $newPassword = $request->request->get('new-password');
        $newPasswordRepeat = $request->request->get('new-password-repeat');

        if ($newPassword === null || $newPasswordRepeat === null) {
            return new ActionResult(Result::FAILURE, 'Beide Passwörter müssen gesetzt sein!');
        }

        if ($newPassword !== $newPasswordRepeat) {
            return new ActionResult(Result::FAILURE, 'Beide Passwörter müssen identisch sein!');
        }

        if (strlen((string) $newPassword) < 8) {
            return new ActionResult(Result::FAILURE, 'Das Passwort muss mindestens 8 Zeichen lang sein!');
        }

        $hashedPassword = $this->passwordHasher->hashPassword($user, (string) $newPassword);
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->discordNotifier->send(
            "Password changed",
            "The user has successfully changed his password",
            DiscordNotifier::COLOR_GREEN
        );

        return new ActionResult(Result::SUCCESS, 'Das Passwort wurde erfolgreich geändert!');
    }
}
