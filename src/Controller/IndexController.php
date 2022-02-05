<?php

namespace App\Controller;

use App\Entity\Entry;
use App\Entity\Product;
use App\Entity\Week;
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

    #[Route(
        '/evaluation/{week}',
        name: 'evaluation',
        requirements: ['week' => '\d{1,2}'],
        defaults: ['week' => 0]
    )]
    public function evaluation(
        int $week,
        Request $request,
        DateService $dateService,
        EntityManagerInterface $entityManager
    ): Response {
        if ($week > 52) {
            throw new NotFoundHttpException();
        }

        if ($week === 0) {
            $week = (int)(new DateTime())->format('W');
            return $this->redirectToRoute('evaluation', ['week' => $week]);
        }

        if ($request->isMethod('POST')) {
            $productId = (int) $request->request->get('product-id');
            $blackMoney = (float) $request->request->get('black-money');
            $realMoney = (float) $request->request->get('real-money');

            if (null === $product = $entityManager->getRepository(Product::class)->find($productId)) {
                return $this->redirectToRoute('evaluation', ['week' => $week]);
            }

            /** @var Week $weekEntity */
            $weekEntity = $entityManager->getRepository(Week::class)->findOneBy(['week' => $week, 'year' => 2022, 'product' => $product]);
            if ($weekEntity === null) {
                $weekEntity = new Week();
                $weekEntity->setProduct($product);
            }

            $weekEntity->setWeek($week);
            $weekEntity->setBlackMoney($blackMoney);
            $weekEntity->setRealMoney($realMoney);

            $entityManager->persist($weekEntity);
            $entityManager->flush();

            $this->addFlash('success', 'Der Eintrag wurde erfolgreich hinzugefügt');
            return $this->redirectToRoute('evaluation', ['week' => $week]);
        }

        [$startDate, $endDate] = $dateService->getStartAndEndOfWeekFromWeekNumber($week);

        $products = $entityManager->getRepository(Product::class)->findBy(['deleted' => false]);
        $entries = $entityManager->getRepository(Entry::class)->findEntriesByWeek($startDate, $endDate);
        $weekEntries = $entityManager->getRepository(Week::class)->findBy(['week' => $week, 'year' => 2022]);

        $weekEntriesArray = [];
        foreach ($weekEntries as $weekEntry) {
            $weekEntriesArray[$weekEntry->getProduct()->getId()] = [
                'blackMoney' => $weekEntry->getBlackMoney(),
                'realMoney' => $weekEntry->getRealMoney(),
            ];
        }

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

        return $this->render('index/evaluation.html.twig', [
            'results' => $results,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'week' => $week,
            'weekEntries' => $weekEntriesArray
        ]);
    }
}
