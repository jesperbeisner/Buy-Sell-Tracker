<?php

declare(strict_types=1);

namespace App\DTO;

class Image
{
    public function __construct(
        private int $width,
        private int $height,
    ) {}

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }
}
