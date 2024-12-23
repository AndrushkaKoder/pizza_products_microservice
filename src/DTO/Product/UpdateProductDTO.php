<?php

namespace App\DTO\Product;

class UpdateProductDTO
{
    public function __construct(
        private readonly int    $id,
        private readonly string $name,
        private readonly string $description,
        private readonly int    $price,
        private readonly bool   $active = true
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