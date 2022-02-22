<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\Image;
use Exception;
use Symfony\Component\HttpKernel\KernelInterface;

class ImageService
{
    private string $mapOriginal;
    private string $mapDir;

    public function __construct(string $mapOriginal, KernelInterface $kernel)
    {
        $this->mapOriginal = $mapOriginal;
        $this->mapDir = $kernel->getProjectDir() . '/public/img/map/';
    }

    public function getImageSize(): Image
    {
        if (!file_exists($this->mapOriginal)) {
            throw new Exception('The original GTA-Map disappeared! o.O');
        }

        $imageSize = getimagesize($this->mapOriginal);

        return new Image($imageSize[0], $imageSize[1]);
    }

    public function regenerate(): void
    {
        if (!file_exists($this->mapOriginal)) {
            throw new Exception('The original GTA-Map disappeared! o.O');
        }

        foreach (glob($this->mapDir . 'map-edited-*.png') as $imageName) {
            unlink($imageName);
        }

        copy($this->mapOriginal, $this->mapDir . 'map-edited-' . time() . '.png');
    }

    public function getCurrentMapEditedName(): string
    {
        $images = glob($this->mapDir . 'map-edited-*.png');

        if (count($images) === 0) {
            $newMapEditedName = 'map-edited-' . time() . '.png';
            copy($this->mapOriginal, $this->mapDir . $newMapEditedName);

            return $newMapEditedName;
        }

        if (count($images) === 1) {
            if (false === $startPosition = strpos($images[0], 'map-edited')) {
                throw new Exception('Not possible! Every map-edited image has map-edited in the name!');
            }

            return substr($images[0], $startPosition);
        }

        throw new Exception('Something went wrong. More than 1 map-edited image found!');
    }

    public function draw(int $xValue, int $yValue): void
    {
        $length = 25;
        $mapEdited = $this->getCurrentMapEditedName();

        $image = imagecreatefrompng($this->mapDir . $mapEdited);

        $colorWhite = imagecolorallocate($image, 255, 255, 255);
        $colorBlack = imagecolorallocate($image, 0, 0, 0);

        // Draw 25 pixel to down right
        imageline($image, $xValue, $yValue - 2, $xValue + $length, $yValue - 2 + $length, $colorBlack);
        imageline($image, $xValue, $yValue - 1, $xValue + $length, $yValue - 1 + $length, $colorWhite);
        imageline($image, $xValue, $yValue, $xValue + $length, $yValue + $length, $colorWhite);
        imageline($image, $xValue, $yValue + 1, $xValue + $length, $yValue + 1 + $length, $colorWhite);
        imageline($image, $xValue, $yValue + 2, $xValue + $length, $yValue + 2 + $length, $colorBlack);

        // Draw 25 pixel to upper left
        imageline($image, $xValue, $yValue - 2, $xValue - $length, $yValue - 2 - $length, $colorBlack);
        imageline($image, $xValue, $yValue - 1, $xValue - $length, $yValue - 1 - $length, $colorWhite);
        imageline($image, $xValue, $yValue, $xValue - $length, $yValue - $length, $colorWhite);
        imageline($image, $xValue, $yValue + 1, $xValue - $length, $yValue + 1 - $length, $colorWhite);
        imageline($image, $xValue, $yValue + 2, $xValue - $length, $yValue + 2 - $length, $colorBlack);

        // Draw 25 pixel to upper right
        imageline($image, $xValue, $yValue - 2, $xValue + $length, $yValue - 2 - $length, $colorBlack);
        imageline($image, $xValue, $yValue - 1, $xValue + $length, $yValue - 1 - $length, $colorWhite);
        imageline($image, $xValue, $yValue, $xValue + $length, $yValue - $length, $colorWhite);
        imageline($image, $xValue, $yValue + 1, $xValue + $length, $yValue + 1 - $length, $colorWhite);
        imageline($image, $xValue, $yValue + 2, $xValue + $length, $yValue + 2 - $length, $colorBlack);

        // Draw 25 pixel to down left
        imageline($image, $xValue, $yValue - 2, $xValue - $length, $yValue - 2 + $length, $colorBlack);
        imageline($image, $xValue, $yValue - 1, $xValue - $length, $yValue - 1 + $length, $colorWhite);
        imageline($image, $xValue, $yValue, $xValue - $length, $yValue + $length, $colorWhite);
        imageline($image, $xValue, $yValue + 1, $xValue - $length, $yValue + 1 + $length, $colorWhite);
        imageline($image, $xValue, $yValue + 2, $xValue - $length, $yValue + 2 + $length, $colorBlack);

        foreach (glob($this->mapDir . 'map-edited-*.png') as $imageName) {
            unlink($imageName);
        }

        imagepng($image, $this->mapDir . 'map-edited-' . time() . '.png');
    }
}
