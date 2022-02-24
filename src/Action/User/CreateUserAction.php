<?php

declare(strict_types=1);

namespace App\Action\User;

use App\Entity\User;
use App\Result\CreateUserResult;
use App\Result\Result;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class CreateUserAction
{
    public function __construct(
        private RequestStack $requestStack,
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $entityManager,
    ) {}

    public function __invoke(): CreateUserResult
    {
        $request = $this->requestStack->getCurrentRequest();

        $username = $request->request->get('username');
        $password = $request->request->get('password');

        if ($username === null || $password === null) {
            return new CreateUserResult(Result::FAILURE, 'Username und Passwort müssen beide gesetzt sein!');
        }

        if (strlen($username) < 3 || strlen($username) > 50) {
            return new CreateUserResult(Result::FAILURE, 'Der Username muss mindestens 3 und maximal 50 Zeichen lang sein!');
        }

        if (strlen($password) < 8) {
            return new CreateUserResult(Result::FAILURE, 'Das Passwort muss mindestens 8 Zeichen lang sein!');
        }

        if (null !== $this->entityManager->getRepository(User::class)->findByUsername($username)) {
            return new CreateUserResult(Result::FAILURE, 'Ein User mit diesem Usernamen existiert bereits!');
        }

        $user = new User();
        $user->setUsername($username);
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new CreateUserResult(Result::SUCCESS, 'Der User wurde erfolgreich angelegt!');
    }
}
