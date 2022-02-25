<?php

declare(strict_types=1);

namespace App\Controller;

use App\Action\Seller\CreateSellerAction;
use App\Action\Seller\DeleteSellerAction;
use App\Entity\Seller;
use App\Result\Result;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SettingsController extends AbstractController
{
    #[Route('/seller', name: 'seller')]
    public function seller(
        Request $request,
        EntityManagerInterface $entityManager,
        CreateSellerAction $createSellerAction,
        DeleteSellerAction $deleteSellerAction,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_SUPER_USER');

        if ($request->isMethod('POST')) {
            if (null === $action = $request->request->get('action')) {
                $this->addFlash('error', 'Keine Action angegeben');
                return $this->redirectToRoute('seller');
            }

            if ($action === 'add') {
                $createSellerResult = $createSellerAction();
                $type = $createSellerResult->getResult() === Result::SUCCESS ? 'success' : 'error';
                $this->addFlash($type, $createSellerResult->getMessage());

                return $this->redirectToRoute('seller');
            }

            if ($action === 'delete') {
                $deleteSellerResult = $deleteSellerAction();
                $type = $deleteSellerResult->getResult() === Result::SUCCESS ? 'success' : 'error';
                $this->addFlash($type, $deleteSellerResult->getMessage());

                return $this->redirectToRoute('seller');
            }

            $this->addFlash('error', 'Keine passende Action gefunden');
            return $this->redirectToRoute('seller');
        }

        $sellers = $entityManager->getRepository(Seller::class)->findBy(['deleted' => false]);

        return $this->render('seller/index.html.twig', [
            'sellers' => $sellers,
        ]);
    }
}
