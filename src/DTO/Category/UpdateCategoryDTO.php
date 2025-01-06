<?php

namespace App\DTO\Category;

class UpdateCategoryDTO
{
    public function __construct(
        private readonly int $id,
        private readonly string $title,
        private readonly bool $active = true
    )
    {
    }

    public function getId(): int
    {
        return $this->id;
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