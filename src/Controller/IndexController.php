<?php

namespace App\Controller;

use App\Entity\Entry;
use App\Entity\Product;
use App\Entity\Sale;
use App\Entity\Week;
use App\Form\EntryType;
use App\Form\SaleType;
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
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $actionName = $request->request->get('action');

            if ($actionName === 'buy') {
                $entryId = (int)$request->request->get('entry-id');
                $entry = $entityManager->getRepository(Entry::class)->find($entryId);

                if ($entry !== null) {
                    $entityManager->remove($entry);
                    $entityManager->flush();
                    $this->addFlash('success', 'Der Eintrag wurde erfolgreich gelöscht');
                }
            }

            if ($actionName === 'sell') {
                $saleId = (int)$request->request->get('sale-id');
                $sale = $entityManager->getRepository(Sale::class)->find($saleId);

                if ($sale !== null) {
                    $entityManager->remove($sale);
                    $entityManager->flush();
                    $this->addFlash('success', 'Der Eintrag wurde erfolgreich gelöscht');
                }
            }

            return $this->redirectToRoute('index');
        }

        return $this->render('index/index.html.twig', [
            'entries' => $entityManager->getRepository(Entry::class)->findBy([], ['created' => 'DESC']),
            'sales' => $entityManager->getRepository(Sale::class)->findBy([], ['created' => 'DESC']),
        ]);
    }

    #[Route('/add', name: 'add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $buyForm = $this->createForm(EntryType::class);
        $buyForm->handleRequest($request);

        if ($buyForm->isSubmitted() && $buyForm->isValid()) {
            $entry = $buyForm->getViewData();
            $entityManager->persist($entry);
            $entityManager->flush();

            $this->addFlash('success', 'Der Eintrag wurde erfolgreich hinzugefügt');
            return $this->redirectToRoute('add');
        }

        $sellForm = $this->createForm(SaleType::class);
        $sellForm->handleRequest($request);

        if ($sellForm->isSubmitted() && $sellForm->isValid()) {
            $sale = $sellForm->getViewData();
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

    #[Route(
        '/evaluation/{week}',
        name: 'evaluation',
        requirements: ['week' => '\d{1,2}'],
        defaults: ['week' => 0]
    )]
    public function evaluation(
        int                    $week,
        Request                $request,
        DateService            $dateService,
        EntityManagerInterface $entityManager
    ): Response
    {
        if ($week > 52) {
            throw new NotFoundHttpException();
        }

        if ($week === 0) {
            $week = (int)(new DateTime())->format('W');
            return $this->redirectToRoute('evaluation', ['week' => $week]);
        }

        [$startDate, $endDate] = $dateService->getStartAndEndOfWeekFromWeekNumber($week);

        $products = $entityManager->getRepository(Product::class)->findBy(['deleted' => false], ['name' => 'ASC']);
        $entries = $entityManager->getRepository(Entry::class)->findEntriesByWeek($startDate, $endDate);
        $sales = $entityManager->getRepository(Sale::class)->findSalesByWeek($startDate, $endDate);

        return $this->render('index/evaluation.html.twig', [
            'buyResults' => $this->createBuyResultsArray($products, $entries),
            'sellResults' => $this->createSellResultsArray($products, $sales),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'week' => $week,
        ]);
    }

    private function createBuyResultsArray(array $products, array $entries): array
    {
        $results = [];
        foreach ($products as $product) {
            $results[$product->getId()] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'amount' => 0,
                'price' => 0
            ];
        }

        foreach ($entries as $entry) {
            if (array_key_exists($entry['id'], $results)) {
                $results[$entry['id']]['id'] = $entry['id'];
                $results[$entry['id']]['amount'] = $entry['amount'];
                $results[$entry['id']]['price'] = $entry['price'];
            }
        }

        return $results;
    }

    private function createSellResultsArray(array $products, array $sales): array
    {
        $results = [];
        foreach ($products as $product) {
            $results[$product->getId()] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'amount' => 0,
                'blackMoney' => 0,
                'realMoney' => 0
            ];
        }

        foreach ($sales as $sale) {
            if (array_key_exists($sale['id'], $results)) {
                $results[$sale['id']]['id'] = $sale['id'];
                $results[$sale['id']]['amount'] = $sale['amount'];
                $results[$sale['id']]['blackMoney'] = $sale['blackMoney'];
                $results[$sale['id']]['realMoney'] = $sale['realMoney'];
            }
        }

        return $results;
    }
}
