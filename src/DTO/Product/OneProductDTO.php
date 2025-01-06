<?php

declare(strict_types=1);

namespace App\DTO\Product;

class OneProductDTO
{
    public function __construct(private readonly int $id)
    {

    }

    public function getId(): int
    {
        return $this->id;
    }

}