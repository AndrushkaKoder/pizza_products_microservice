<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\Product\CreateProductDTO;
use App\DTO\Product\OneProductDTO;
use App\DTO\Product\ProductDTO;
use App\DTO\Product\UpdateProductDTO;
use App\Entity\Product;
use App\Exception\NotFoundException;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class ProductsService
{
    public function __construct(
        private ProductRepository      $repository,
        private EntityManagerInterface $manager
    )
    {
    }


    public function productFields(): array
    {
        return [
            'name',
            'description',
            'price',
            'active'
        ];
    }


    public function getAllProducts(): array
    {
        return array_map(fn(Product $product) => (new ProductDTO($product))->toArray(), $this->repository->getActive());
    }


    public function getOne(OneProductDTO $dto): array
    {
        $product = $this->repository->findOneBy([
            'id' => $dto->getId(),
            'active' => true
        ]);

        return !$product ? [] : (new ProductDTO($product))->toArray();
    }


    public function updateProduct(UpdateProductDTO $dto): array
    {
        $product = $this->repository->findOneBy([
            'id' => $dto->getId(),
        ]);

        if (!$product) {
            throw new NotFoundException('Product not found', 400);
        }

        $product->setName($dto->getName())
            ->setDescription($dto->getDescription())
            ->setPrice($dto->getPrice())
            ->setActive($dto->getActive());

        $this->manager->persist($product);
        $this->manager->flush();

        return (new ProductDTO($product))->toArray();
    }


    public function deleteProduct(OneProductDTO $dto): bool
    {
        $product = $this->repository->findOneBy([
            'id' => $dto->getId()
        ]);

        if (!$product) {
            return false;
        }

        $this->manager->remove($product);
        $this->manager->flush();
        return true;
    }


    public function createNewProduct(CreateProductDTO $dto): array
    {
        $product = new Product();
        $product->setName($dto->getName())
            ->setDescription($dto->getDescription())
            ->setPrice($dto->getPrice())
            ->setActive($dto->getActive());

        $this->manager->persist($product);
        $this->manager->flush();

        return (new ProductDTO($product))->toArray();
    }


    public function checkRequestFields(array $requestData): array
    {
        $skippedFields = [];
        foreach ($this->productFields() as $field) {
            if (!in_array($field, array_keys($requestData))) {
                $skippedFields[] = $field;
            }
        }

        return $skippedFields;
    }

}