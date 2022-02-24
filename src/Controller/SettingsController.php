<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Seller;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SettingsController extends AbstractController
{
    #[Route('/settings', name: 'settings')]
    public function settings(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_USER');

        if ($request->isMethod('POST')) {
            $action = $request->request->get('button');

            if ($action === 'seller') {
                $sellerName = $request->request->get('seller');
                if (null === $entityManager->getRepository(Seller::class)->findOneBy(['name' => $sellerName])) {
                    $seller = new Seller();
                    $seller->setName($sellerName);
                    $entityManager->persist($seller);
                    $entityManager->flush();

                    $this->addFlash('success', 'Der Verkäufer wurde erfolgreich hinzugefügt');
                    return $this->redirectToRoute('settings');
                }

                $this->addFlash('error', 'Ein Verkäufer mit diesem Namen existiert bereits');
                return $this->redirectToRoute('settings');
            }
        }

        return $this->render('settings/index.html.twig');
    }
}
