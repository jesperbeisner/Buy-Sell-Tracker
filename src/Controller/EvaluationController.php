<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\DateService;
use App\Service\EvaluationService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class EvaluationController extends AbstractController
{
    #[Route('/evaluation/{week}', name: 'evaluation', requirements: ['week' => '\d{1,2}'], defaults: ['week' => 0])]
    public function evaluation(int $week, DateService $dateService, EvaluationService $evaluationService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_USER');

        if ($week > 52) {
            throw new NotFoundHttpException();
        }

        if ($week === 0) {
            $week = (int) (new DateTime())->format('W');
            return $this->redirectToRoute('evaluation', ['week' => $week]);
        }

        [$startDate, $endDate] = $dateService->getStartAndEndOfWeekFromWeekNumber($week);

        return $this->render('evaluation/index.html.twig', [
            'evaluationData' => $evaluationService->getEvaluationData($startDate, $endDate),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'week' => $week,
        ]);
    }
}
