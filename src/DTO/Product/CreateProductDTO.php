<?php

declare(strict_types=1);

namespace App\DTO\Product;

readonly class CreateProductDTO
{
    public function __construct(
        private string  $name,
        private ?string $description = null,
        private int     $price,
        private bool $active = true,
        private ?string $image = null
    )
    {
    }


    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }



}