<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Fraction;
use App\Entity\Product;
use App\Entity\Purchase;
use App\Entity\Sale;
use App\Entity\Shift;
use App\Entity\User;
use App\Form\PurchaseType;
use App\Form\SaleType;
use App\Service\DateService;
use App\Service\EvaluationService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ParameterBagInterface $parameterBag): Response
    {
        $indexImage = $parameterBag->get('app.index-image');

        return $this->render('index/index.html.twig', [
            'picture' => 'Placeholder' === $indexImage ? 'placeholder.png' : $indexImage,
        ]);
    }

    #[Route('/add', name: 'add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_USER');

        /** @var User $user */
        $user = $this->getUser();

        $purchaseForm = $this->createForm(PurchaseType::class);
        $purchaseForm->setData(new Purchase());
        $purchaseForm->handleRequest($request);

        if ($purchaseForm->isSubmitted() && $purchaseForm->isValid()) {
            /** @var Purchase $purchase */
            $purchase = $purchaseForm->getViewData();
            $purchase->updateTime();
            $purchase->setName($user->getUsername());

            $entityManager->persist($purchase);
            $entityManager->flush();

            $this->addFlash('success', 'Der Eintrag wurde erfolgreich hinzugefügt');
            return $this->redirectToRoute('add');
        }

        $saleForm = $this->createForm(SaleType::class);
        $saleForm->setData(new Sale());
        $saleForm->handleRequest($request);

        if ($saleForm->isSubmitted() && $saleForm->isValid()) {
            /** @var Sale $sale */
            $sale = $saleForm->getViewData();
            $sale->updateTime();
            $sale->setName($user->getUsername());

            $entityManager->persist($sale);
            $entityManager->flush();

            $this->addFlash('success', 'Der Eintrag wurde erfolgreich hinzugefügt');
            return $this->redirectToRoute('add');
        }

        return $this->render('index/add.html.twig', [
            'purchaseForm' => $purchaseForm->createView(),
            'saleForm' => $saleForm->createView(),
            'shifts' => $entityManager->getRepository(Shift::class)->findAll(),
            'products' => $entityManager->getRepository(Product::class)->findAll(),
            'fractions' => $entityManager->getRepository(Fraction::class)->findAll(),
        ]);
    }
}
