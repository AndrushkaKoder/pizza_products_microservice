<?php

declare(strict_types=1);

namespace App\DTO\Product;

class CreateProductDTO
{
    public function __construct(
        private readonly string  $name,
        private readonly ?string $description = null,
        private readonly int     $price,
        private readonly bool $active = true
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



}