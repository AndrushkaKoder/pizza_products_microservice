<?php

namespace App\DTO\Category;

class OneCategoryDTO
{
    public function __construct(private readonly int $id)
    {
    }


    public function getId(): int
    {
        return $this->id;
    }

}