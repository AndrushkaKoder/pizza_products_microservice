<?php

declare(strict_types=1);

namespace App\DTO\Category;

use App\Entity\Category;

readonly class CategoryDTO
{
    public function __construct(private Category $category)
    {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->category->getId(),
            'title' => $this->category->getTitle()
        ];
    }

}