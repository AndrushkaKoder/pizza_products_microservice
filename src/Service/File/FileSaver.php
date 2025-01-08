<?php

declare(strict_types=1);

namespace App\Service\File;

use App\Entity\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileSaver
{
    public function __construct(
        private readonly UploadedFile $file
    )
    {
    }

    public function save(): bool
    {
        $originalName = preg_replace('/-+/', '_', pathinfo($this->file->getClientOriginalPath(), PATHINFO_FILENAME));
        $newFileName = $originalName . '-' . uniqid() . $this->file->guessExtension();
        $pathToDir = '/storage/' . (new \DateTime())->format('Y-m-d');

        if (!is_dir($pathToDir)) {
            mkdir($pathToDir, 775);
        }

        try {
            $this->file->move($pathToDir, $newFileName);

            $image = new Image();

            return true;
        } catch (\Exception $exception) {
            return false;
        }

    }

}