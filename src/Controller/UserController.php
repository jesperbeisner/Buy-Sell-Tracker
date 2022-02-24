<?php

declare(strict_types=1);

namespace App\Controller;

use App\Action\User\ChangePasswordAction;
use App\Action\User\CreateUserAction;
use App\Action\User\DeleteUserAction;
use App\Entity\User;
use App\Result\Result;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/change-password', name: 'change-password')]
    public function changePassword(Request $request, ChangePasswordAction $changePasswordAction): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if ($request->isMethod('POST')) {
            $changePasswordResult = $changePasswordAction();
            $type = $changePasswordResult->getResult() === Result::SUCCESS ? 'success' : 'error';
            $this->addFlash($type, $changePasswordResult->getMessage());

            return $this->redirectToRoute('change-password');
        }

        return $this->render('user/change-password.html.twig');
    }

    #[Route('/user', name: 'user')]
    public function user(
        Request $request,
        EntityManagerInterface $entityManager,
        CreateUserAction $createUserAction,
        DeleteUserAction $deleteUserAction,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($request->isMethod('POST')) {
            if (null === $action = $request->request->get('action')) {
                $this->addFlash('error', 'Keine Action vorhanden');
                return $this->redirectToRoute('user');
            }

            if ($action === 'create') {
                $createUserResult = $createUserAction();
                $type = $createUserResult->getResult() === Result::SUCCESS ? 'success' : 'error';
                $this->addFlash($type, $createUserResult->getMessage());

                return $this->redirectToRoute('user');
            }

            if ($action === 'delete') {
                $createUserResult = $deleteUserAction();
                $type = $createUserResult->getResult() === Result::SUCCESS ? 'success' : 'error';
                $this->addFlash($type, $createUserResult->getMessage());

                return $this->redirectToRoute('user');
            }

            $this->addFlash('error', 'Keine passende Action vorhanden');
            return $this->redirectToRoute('user');
        }

        return $this->render('user/user.html.twig', [
            'users' => $entityManager->getRepository(User::class)->findAll(),
        ]);
    }
}
