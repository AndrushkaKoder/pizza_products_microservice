<?php

declare(strict_types=1);

namespace App\DTO\Product;

readonly class UpdateProductDTO
{
    public function __construct(
        private int    $id,
        private string $name,
        private string $description,
        private int    $price,
        private bool   $active = true
    )
    {

    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDescription(): string
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

    public function getName(): string
    {
        return $this->name;
    }

}