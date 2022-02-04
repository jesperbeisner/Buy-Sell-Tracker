<?php

namespace App\Controller;

use App\Entity\Entry;
use App\Form\EntryType;
use App\Service\DateService;
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
    public function index(EntityManagerInterface $entityManager): Response
    {
        return $this->render('index/index.html.twig', [
            'entries' => $entityManager->getRepository(Entry::class)->findAll(),
        ]);
    }

    #[Route('/add', name: 'add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EntryType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entry = $form->getViewData();
            $entityManager->persist($entry);
            $entityManager->flush();

            $this->addFlash('success', 'Der Eintrag wurde erfolgreich hinzugefügt');
            return $this->redirectToRoute('add');
        }

        return $this->render('index/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/evaluation/{week}', name: 'evaluation', requirements: ['week' => '\d{1,2}'], defaults: ['week' => 0])]
    public function evaluation(int $week, DateService $dateService): Response
    {
        if ($week > 52) {
            throw new NotFoundHttpException();
        }

        if ($week === 0) {
            $week = (int)(new DateTime())->format('W');
        }

        [$startDate, $endDate] = $dateService->getStartAndEndOfWeekFromWeekNumber($week);

        return $this->render('index/evaluation.html.twig');
    }
}
