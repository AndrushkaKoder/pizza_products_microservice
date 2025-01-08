<?php

declare(strict_types=1);

namespace App\DTO\Category;

readonly class UpdateCategoryDTO
{
    public function __construct(
        private int $id,
        private string $title,
        private bool $active = true
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