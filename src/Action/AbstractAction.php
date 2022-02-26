<?php

declare(strict_types=1);

namespace App\Action;

use App\Entity\User;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

abstract class AbstractAction
{
    public function __construct(
        protected Security $security,
        protected RequestStack $requestStack,
    ) {}

    protected function getUser(): User
    {
        if (null === $user = $this->security->getUser()) {
            throw new Exception("The user can not be null at this point, something went wrong!");
        }

        if ($user instanceof User) {
            return $user;
        }

        throw new Exception("The user has to be of type 'App\Entity\User::class'!");
    }

    protected function getRequest(): Request
    {
        if (null === $request = $this->requestStack->getCurrentRequest()) {
            throw new Exception("The request can not be null at this point, something went wrong!");
        }

        return $request;
    }
}
