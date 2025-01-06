<?php

namespace App\DTO\Category;

use App\Entity\Category;

class CategoryDTO
{
    public function __construct(private readonly Category $category)
    {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->category->getId(),
            'title' => $this->category->getTitle(),
            'active' => $this->category->getActive()
        ];
    }

}