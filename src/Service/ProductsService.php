<?php

namespace App\Service;

use App\DTO\ProductDTO;
use App\Entity\Product;
use App\Repository\ProductRepository;

class ProductsService
{
    public function __construct(private readonly ProductRepository $repository)
    {
    }

    public function getAllProducts(): array
    {
        return array_map(fn(Product $product) => (new ProductDTO($product))->toArray(), $this->repository->findAll());
    }

    public function getOne(int $id): array
    {
        $product = $this->repository->findOneBy([
            'id' => $id,
            'active' => true
        ]);

        return $product ? (new ProductDTO($product))->toArray() : [];
    }

}