<?php

declare(strict_types=1);

namespace App\Action\User;

use App\Entity\User;
use App\Result\DeleteUserResult;
use App\Result\Result;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

final class DeleteUserAction
{
    public function __construct(
        private Security $security,
        private RequestStack $requestStack,
        private EntityManagerInterface $entityManager,
    ) {}

    public function __invoke(): DeleteUserResult
    {
        /** @var User|null $user */
        if (null === $user = $this->security->getUser()) {
            throw new Exception('How can the user be null at this point? You fucked up!');
        }

        $request = $this->requestStack->getCurrentRequest();
        if (null === $userId = $request->request->get('user')) {
            return new DeleteUserResult(Result::FAILURE, 'Kein User zum Löschen ausgewählt!');
        }

        if ($user->getId() === (int) $userId) {
            return new DeleteUserResult(Result::FAILURE, 'Du kannst deinen eigenen User nicht löschen!');
        }

        if (null === $deleteUser = $this->entityManager->getRepository(User::class)->find($userId)) {
            return new DeleteUserResult(Result::FAILURE, 'Keinen passenden User zum Löschen gefunden!');
        }

        $this->entityManager->remove($deleteUser);
        $this->entityManager->flush();

        return new DeleteUserResult(Result::SUCCESS, 'Der User wurde erfolgreich gelöscht!');
    }
}
