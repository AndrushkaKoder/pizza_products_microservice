<?php

namespace App\DTO\Category;

class CreateCategoryDTO
{
    public function __construct(
        private readonly string $title,
        private readonly bool   $active
    )
    {
    }


    public function getTitle(): string
    {
        return $this->title;
    }


    public function getActive(): bool
    {
        return $this->active;
    }

}