<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\ImageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MapController extends AbstractController
{
    #[Route('/map', name: 'map')]
    public function map(Request $request, ImageService $imageService,): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if ($request->isMethod('POST')) {
            if (null === $action = $request->request->get('action')) {
                $this->addFlash('error', 'Keine Action vorhanden');
                return $this->redirectToRoute('map');
            }

            if ($action === 'map-save') {
                $xValue = (int) $request->request->get('x-value', -1);
                $yValue = (int) $request->request->get('y-value', -1);
                $xSize = (int) $request->request->get('x-size', 10);

                if ($xValue < 0 || $yValue < 0) {
                    $this->addFlash('error', 'Die X-Position und Y-Position müssen beide ausgefüllt sein und dürfen nicht im negativen Bereich liegen');
                    return $this->redirectToRoute('map');
                }

                $image = $imageService->getImageSize();
                if ($xValue > $image->getWidth() || $yValue > $image->getHeight()) {
                    $this->addFlash('error', 'Die X-Position oder Y-Position darf nicht größer als das Bild sein');
                    return $this->redirectToRoute('map');
                }

                $imageService->draw($xValue, $yValue, $xSize);

                $this->addFlash('success', 'Die Markierung wurde erfolgreich hinzugefügt');
                return $this->redirectToRoute('map');
            }

            if ($action === 'map-reset') {
                $imageService->regenerateMap();

                $this->addFlash('success', 'Die Karte wurde erfolgreich zurückgesetzt');
                return $this->redirectToRoute('map');
            }

            if ($action === 'map-delete') {
                $imageService->deleteLastMapEntry();

                $this->addFlash('success', 'Der letzte Eintrag wurde erfolgreich gelöscht');
                return $this->redirectToRoute('map');
            }

            $this->addFlash('error', 'Die ausgewählte Action ist nicht vorhanden');
            return $this->redirectToRoute('map');
        }

        return $this->render('map/index.html.twig', [
            'mapEditedFileTime' => $imageService->getCurrentMapEditedFileTime(),
        ]);
    }
}
