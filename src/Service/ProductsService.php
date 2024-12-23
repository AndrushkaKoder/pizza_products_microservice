<?php

namespace App\Service;

use App\DTO\Product\ProductDTO;
use App\DTO\Product\UpdateProductDTO;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;

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

    public function updateProduct(UpdateProductDTO $dto, EntityManagerInterface $manager): bool|array
    {
        $product = $this->repository->findOneBy([
            'id' => $dto->getId(),
            'active' => true
        ]);

        if (!$product) {
            return false;
        }

        $product->setName($dto->getName())
            ->setDescription($dto->getDescription())
            ->setPrice($dto->getPrice())
            ->setActive($dto->getActive());

        return false;

        /*
         * TODO доделать обноление
         */

    }

}