<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Entry;
use App\Entity\Sale;
use App\Entity\User;
use App\Form\EntryType;
use App\Form\SaleType;
use App\Service\DateService;
use App\Service\EvaluationService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('index/index.html.twig');
    }

    #[Route('/add', name: 'add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_USER');

        /** @var User $user */
        $user = $this->getUser();

        $buyForm = $this->createForm(EntryType::class);
        $buyForm->setData(new Entry());
        $buyForm->handleRequest($request);

        if ($buyForm->isSubmitted() && $buyForm->isValid()) {
            /** @var Entry $entry */
            $entry = $buyForm->getViewData();
            $entry->updateTime();
            $entry->setName($user->getUsername());

            $entityManager->persist($entry);
            $entityManager->flush();

            $this->addFlash('success', 'Der Eintrag wurde erfolgreich hinzugefügt');
            return $this->redirectToRoute('add');
        }

        $sellForm = $this->createForm(SaleType::class);
        $sellForm->setData(new Sale());
        $sellForm->handleRequest($request);

        if ($sellForm->isSubmitted() && $sellForm->isValid()) {
            /** @var Sale $sale */
            $sale = $sellForm->getViewData();
            $sale->updateTime();
            $sale->setName($user->getUsername());

            $entityManager->persist($sale);
            $entityManager->flush();

            $this->addFlash('success', 'Der Eintrag wurde erfolgreich hinzugefügt');
            return $this->redirectToRoute('add');
        }

        return $this->render('index/add.html.twig', [
            'form' => $buyForm->createView(),
            'sellForm' => $sellForm->createView(),
        ]);
    }

    #[Route('/evaluation/{week}', name: 'evaluation', requirements: ['week' => '\d{1,2}'], defaults: ['week' => 0])]
    public function evaluation(int $week, DateService $dateService, EvaluationService $evaluationService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_USER');

        if ($week > 52) {
            throw new NotFoundHttpException();
        }

        if ($week === 0) {
            $week = (int)(new DateTime())->format('W');
            return $this->redirectToRoute('evaluation', ['week' => $week]);
        }

        [$startDate, $endDate] = $dateService->getStartAndEndOfWeekFromWeekNumber($week);

        return $this->render('index/evaluation.html.twig', [
            'evaluationData' => $evaluationService->getEvaluationData($startDate, $endDate),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'week' => $week,
        ]);
    }
}
