<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\Image;
use App\Entity\MapPosition;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class ImageService
{
    public function __construct(
        private string $mapOriginal,
        private string $mapEdited,
        private EntityManagerInterface $entityManager,
    ) {}

    public function getImageSize(): Image
    {
        if (!file_exists($this->mapOriginal)) {
            throw new Exception('The original Map disappeared! o.O');
        }

        /** @var array<int|string, int|string> $imageSize */
        $imageSize = getimagesize($this->mapOriginal);

        return new Image((int) $imageSize[0], (int) $imageSize[1]);
    }

    public function regenerateMap(): void
    {
        if (!file_exists($this->mapOriginal)) {
            throw new Exception('The original GTA-Map disappeared! o.O');
        }

        copy($this->mapOriginal, $this->mapEdited);

        $this->entityManager->getRepository(MapPosition::class)->deleteAll();
    }

    public function getCurrentMapEditedFileTime(): string
    {
        if (!file_exists($this->mapEdited)) {
            $this->regenerateMap();
        }

        return (string) filemtime($this->mapEdited);
    }

    public function deleteLastMapEntry(): void
    {
        $mapPositionRepository = $this->entityManager->getRepository(MapPosition::class);
        if (null !== $lastMapPositionEntry = $mapPositionRepository->getLastEntry()) {
            $this->entityManager->remove($lastMapPositionEntry);
            $this->entityManager->flush();
        }

        $mapPositions = $mapPositionRepository->findAll();

        $this->regenerateMap();

        foreach ($mapPositions as $mapPosition) {
            $this->draw($mapPosition->getX(), $mapPosition->getY(), $mapPosition->getSize());
        }
    }

    public function draw(int $xValue, int $yValue, int $size = 25): void
    {
        if (!file_exists($this->mapEdited)) {
            $this->regenerateMap();
        }

        if (false === $image = imagecreatefrompng($this->mapEdited)) {
            throw new Exception('Could not create image from png!');
        }

        /** @var int $colorWhite */
        $colorWhite = imagecolorallocate($image, 255, 255, 255);
        
        /** @var int $colorBlack */
        $colorBlack = imagecolorallocate($image, 0, 0, 0);
        
        /** @var int $colorRed */
        $colorRed = imagecolorallocate($image, 255, 0, 0);

        // Draw $size pixel to down right
        imageline($image, $xValue, $yValue - 3, $xValue + $size, $yValue - 3 + $size, $colorBlack);
        imageline($image, $xValue, $yValue - 2, $xValue + $size, $yValue - 2 + $size, $colorWhite);
        imageline($image, $xValue, $yValue - 1, $xValue + $size, $yValue - 1 + $size, $colorWhite);
        imageline($image, $xValue, $yValue, $xValue + $size, $yValue + $size, $colorRed);
        imageline($image, $xValue, $yValue + 1, $xValue + $size, $yValue + 1 + $size, $colorWhite);
        imageline($image, $xValue, $yValue + 2, $xValue + $size, $yValue + 2 + $size, $colorWhite);
        imageline($image, $xValue, $yValue + 3, $xValue + $size, $yValue + 3 + $size, $colorBlack);

        // Draw $size pixel to upper left
        imageline($image, $xValue, $yValue - 3, $xValue - $size, $yValue - 3 - $size, $colorBlack);
        imageline($image, $xValue, $yValue - 2, $xValue - $size, $yValue - 2 - $size, $colorWhite);
        imageline($image, $xValue, $yValue - 1, $xValue - $size, $yValue - 1 - $size, $colorWhite);
        imageline($image, $xValue, $yValue, $xValue - $size, $yValue - $size, $colorRed);
        imageline($image, $xValue, $yValue + 1, $xValue - $size, $yValue + 1 - $size, $colorWhite);
        imageline($image, $xValue, $yValue + 2, $xValue - $size, $yValue + 2 - $size, $colorWhite);
        imageline($image, $xValue, $yValue + 3, $xValue - $size, $yValue + 3 - $size, $colorBlack);

        // Draw $size pixel to upper right
        imageline($image, $xValue, $yValue - 3, $xValue + $size, $yValue - 3 - $size, $colorBlack);
        imageline($image, $xValue, $yValue - 2, $xValue + $size, $yValue - 2 - $size, $colorWhite);
        imageline($image, $xValue, $yValue - 1, $xValue + $size, $yValue - 1 - $size, $colorWhite);
        imageline($image, $xValue, $yValue, $xValue + $size, $yValue - $size, $colorRed);
        imageline($image, $xValue, $yValue + 1, $xValue + $size, $yValue + 1 - $size, $colorWhite);
        imageline($image, $xValue, $yValue + 2, $xValue + $size, $yValue + 2 - $size, $colorWhite);
        imageline($image, $xValue, $yValue + 3, $xValue + $size, $yValue + 3 - $size, $colorBlack);

        // Draw $size pixel to down left
        imageline($image, $xValue, $yValue - 3, $xValue - $size, $yValue - 3 + $size, $colorBlack);
        imageline($image, $xValue, $yValue - 2, $xValue - $size, $yValue - 2 + $size, $colorWhite);
        imageline($image, $xValue, $yValue - 1, $xValue - $size, $yValue - 1 + $size, $colorWhite);
        imageline($image, $xValue, $yValue, $xValue - $size, $yValue + $size, $colorRed);
        imageline($image, $xValue, $yValue + 1, $xValue - $size, $yValue + 1 + $size, $colorWhite);
        imageline($image, $xValue, $yValue + 2, $xValue - $size, $yValue + 2 + $size, $colorWhite);
        imageline($image, $xValue, $yValue + 3, $xValue - $size, $yValue + 3 + $size, $colorBlack);

        if (false === imagepng($image, $this->mapEdited)) {
            throw new Exception('Image could not be saved!');
        }

        $this->createNewMapPositionEntry($xValue, $yValue, $size);
    }

    private function createNewMapPositionEntry(int $xValue, int $yValue, int $size): void
    {
        $mapPosition = new MapPosition();
        $mapPosition->setX($xValue);
        $mapPosition->setY($yValue);
        $mapPosition->setSize($size);

        $this->entityManager->persist($mapPosition);
        $this->entityManager->flush();
    }
}
