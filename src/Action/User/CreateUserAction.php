<?php

declare(strict_types=1);

namespace App\Action\User;

use App\Action\AbstractAction;
use App\Action\ActionInterface;
use App\Entity\User;
use App\Notifier\DiscordNotifier;
use App\Result\ActionResult;
use App\Result\Result;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;

final class CreateUserAction extends AbstractAction implements ActionInterface
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

        $username = $request->request->get('username');
        $password = $request->request->get('password');

        if ($username === null || $password === null) {
            return new ActionResult(Result::FAILURE, 'Username und Passwort müssen beide gesetzt sein!');
        }

        $username = (string) $username;
        $password = (string) $password;

        if (strlen($username) < 3 || strlen($username) > 50) {
            return new ActionResult(Result::FAILURE, 'Der Username muss mindestens 2 und darf maximal 50 Zeichen lang sein!');
        }

        if (strlen($password) < 8) {
            return new ActionResult(Result::FAILURE, 'Das Passwort muss mindestens 8 Zeichen lang sein!');
        }

        if (null !== $this->entityManager->getRepository(User::class)->findByUsername($username)) {
            return new ActionResult(Result::FAILURE, 'Ein User mit diesem Usernamen existiert bereits!');
        }

        $user = new User();
        $user->setUsername($username);
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->discordNotifier->send(
            "User created",
            "The new user '{$user->getUsername()}' was successfully created",
            DiscordNotifier::COLOR_GREEN
        );

        return new ActionResult(Result::SUCCESS, 'Der User wurde erfolgreich angelegt!');
    }
}
