<?php

declare(strict_types=1);

namespace App\DTO\Image;

use App\Entity\Image;

readonly class ImageDTO
{
    public function __construct(private Image $image)
    {
    }

    public function getSource(): string
    {
        return 'http://localhost:8080' . $this->image->getSource();
    }


}