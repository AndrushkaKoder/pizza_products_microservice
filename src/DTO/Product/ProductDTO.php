<?php

declare(strict_types=1);

namespace App\DTO\Product;

use App\DTO\Category\CategoryDTO;
use App\Entity\Category;
use App\Entity\Product;

readonly class ProductDTO
{
    public function __construct(private Product $product)
    {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->product->getId(),
            'name' => $this->product->getName(),
            'description' => $this->product->getDescription(),
            'price' => $this->product->getPrice(),
            'categories' => array_map(function (Category $category) {
                return (new CategoryDTO($category))->toArray();
            }, $this->product->getCategories()->toArray())
        ];
    }

}