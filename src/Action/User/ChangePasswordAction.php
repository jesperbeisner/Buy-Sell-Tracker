<?php

declare(strict_types=1);

namespace App\Action\User;

use App\Entity\User;
use App\Result\ChangePasswordResult;
use App\Result\Result;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;

final class ChangePasswordAction
{
    public function __construct(
        private Security $security,
        private RequestStack $requestStack,
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $entityManager,
    ) {}

    public function __invoke(): ChangePasswordResult
    {
        /** @var User|null $user */
        if (null === $user = $this->security->getUser()) {
            throw new Exception('How can the user be null at this point? You fucked up!');
        }

        $request = $this->requestStack->getCurrentRequest();

        $newPassword = $request->request->get('new-password');
        $newPasswordRepeat = $request->request->get('new-password-repeat');

        if ($newPassword === null || $newPasswordRepeat === null) {
            return new ChangePasswordResult(Result::FAILURE, 'Beide Passwörter müssen gesetzt sein!');
        }

        if ($newPassword !== $newPasswordRepeat) {
            return new ChangePasswordResult(Result::FAILURE, 'Beide Passwörter müssen identisch sein!');
        }

        if (strlen($newPassword) < 8) {
            return new ChangePasswordResult(Result::FAILURE, 'Das Passwort muss mindestens 8 Zeichen lang sein!');
        }

        $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new ChangePasswordResult(Result::SUCCESS, 'Das Passwort wurde erfolgreich geändert!');
    }
}
