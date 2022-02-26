<?php

declare(strict_types=1);

namespace App\Action\User;

use App\Action\AbstractAction;
use App\Action\ActionInterface;
use App\Entity\User;
use App\Result\ActionResult;
use App\Result\Result;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

final class DeleteUserAction extends AbstractAction implements ActionInterface
{
    public function __construct(
        Security $security,
        RequestStack $requestStack,
        private EntityManagerInterface $entityManager,
    ) {
        parent::__construct($security, $requestStack);
    }

    public function execute(): ActionResult
    {
        $request = $this->getRequest();
        $user = $this->getUser();

        if (null === $userId = $request->request->get('user')) {
            return new ActionResult(Result::FAILURE, 'Kein User zum Löschen ausgewählt!');
        }

        if ($user->getId() === (int) $userId) {
            return new ActionResult(Result::FAILURE, 'Du kannst deinen eigenen User nicht löschen!');
        }

        if (null === $deleteUser = $this->entityManager->getRepository(User::class)->find($userId)) {
            return new ActionResult(Result::FAILURE, 'Keinen passenden User zum Löschen gefunden!');
        }

        $this->entityManager->remove($deleteUser);
        $this->entityManager->flush();

        return new ActionResult(Result::SUCCESS, 'Der User wurde erfolgreich gelöscht!');
    }
}
