<?php

declare(strict_types=1);

namespace App\Controller;

use App\Action\Fraction\CreateFractionAction;
use App\Action\Fraction\DeleteFractionAction;
use App\Action\Product\CreateProductAction;
use App\Action\Product\DeleteProductAction;
use App\Action\Shift\CreateShiftAction;
use App\Action\Shift\DeleteShiftAction;
use App\Entity\Fraction;
use App\Entity\Product;
use App\Entity\Shift;
use App\Result\Result;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SettingsController extends AbstractController
{
    #[Route('/fractions', name: 'fractions')]
    public function fractions(
        Request $request,
        EntityManagerInterface $entityManager,
        CreateFractionAction $createSellerAction,
        DeleteFractionAction $deleteSellerAction,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_SUPER_USER');

        if ($request->isMethod('POST')) {
            if (null === $action = $request->request->get('action')) {
                $this->addFlash('error', 'Keine Action angegeben');

                return $this->redirectToRoute('fractions');
            }

            if ($action === 'add') {
                $createSellerResult = $createSellerAction->execute();
                $type = $createSellerResult->getResult() === Result::SUCCESS ? 'success' : 'error';
                $this->addFlash($type, $createSellerResult->getMessage());

                return $this->redirectToRoute('fractions');
            }

            if ($action === 'delete') {
                $deleteSellerResult = $deleteSellerAction->execute();
                $type = $deleteSellerResult->getResult() === Result::SUCCESS ? 'success' : 'error';
                $this->addFlash($type, $deleteSellerResult->getMessage());

                return $this->redirectToRoute('fractions');
            }

            $this->addFlash('error', 'Keine passende Action gefunden');

            return $this->redirectToRoute('fractions');
        }

        return $this->render('settings/fractions.html.twig', [
            'fractions' => $entityManager->getRepository(Fraction::class)->findAllOrderedByName(),
        ]);
    }

    #[Route('/shifts', name: 'shifts')]
    public function shifts(
        Request $request,
        EntityManagerInterface $entityManager,
        CreateShiftAction $createShiftAction,
        DeleteShiftAction $deleteShiftAction,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_SUPER_USER');

        if ($request->isMethod('POST')) {
            if (null === $action = $request->request->get('action')) {
                $this->addFlash('error', 'Keine Action angegeben');

                return $this->redirectToRoute('shifts');
            }

            if ($action === 'add') {
                $createShiftResult = $createShiftAction->execute();
                $type = $createShiftResult->getResult() === Result::SUCCESS ? 'success' : 'error';
                $this->addFlash($type, $createShiftResult->getMessage());

                return $this->redirectToRoute('shifts');
            }

            if ($action === 'delete') {
                $deleteShiftResult = $deleteShiftAction->execute();
                $type = $deleteShiftResult->getResult() === Result::SUCCESS ? 'success' : 'error';
                $this->addFlash($type, $deleteShiftResult->getMessage());

                return $this->redirectToRoute('shifts');
            }

            $this->addFlash('error', 'Keine passende Action gefunden');

            return $this->redirectToRoute('shifts');
        }

        return $this->render('settings/shifts.html.twig', [
            'shifts' => $entityManager->getRepository(Shift::class)->findAllOrderedByName(),
        ]);
    }

    #[Route('/products', name: 'products')]
    public function products(
        Request $request,
        EntityManagerInterface $entityManager,
        CreateProductAction $createProductAction,
        DeleteProductAction $deleteProductAction,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_SUPER_USER');

        if ($request->isMethod('POST')) {
            if (null === $action = $request->request->get('action')) {
                $this->addFlash('error', 'Keine Action angegeben');

                return $this->redirectToRoute('products');
            }

            if ($action === 'add') {
                $createProductResult = $createProductAction->execute();
                $type = $createProductResult->getResult() === Result::SUCCESS ? 'success' : 'error';
                $this->addFlash($type, $createProductResult->getMessage());

                return $this->redirectToRoute('products');
            }

            if ($action === 'delete') {
                $deleteProductResult = $deleteProductAction->execute();
                $type = $deleteProductResult->getResult() === Result::SUCCESS ? 'success' : 'error';
                $this->addFlash($type, $deleteProductResult->getMessage());

                return $this->redirectToRoute('products');
            }

            $this->addFlash('error', 'Keine passende Action gefunden');

            return $this->redirectToRoute('products');
        }

        return $this->render('settings/products.html.twig', [
            'products' => $entityManager->getRepository(Product::class)->findAllOrderedByName(),
        ]);
    }
}
