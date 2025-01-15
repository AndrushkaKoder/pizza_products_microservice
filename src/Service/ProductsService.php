<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\Product\CreateProductDTO;
use App\DTO\Product\OneProductDTO;
use App\DTO\Product\ProductDTO;
use App\DTO\Product\UpdateProductDTO;
use App\Entity\Image;
use App\Entity\Product;
use App\Exception\NotFoundException;
use App\Repository\ProductRepository;
use App\Service\File\FileSaver;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemPoolInterface;

readonly class ProductsService
{

    public const PRODUCTS_CACHE_KEY = 'products';
    public const PRODUCTS_CACHE_TIME = 3600 * 24;

    public function __construct(
        private ProductRepository      $repository,
        private EntityManagerInterface $manager,
        private CacheItemPoolInterface $cache
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
        $cacheItem = $this->cache->getItem(self::PRODUCTS_CACHE_KEY);

        if (!$cacheItem->isHit()) {
            $products = $this->repository->getActive();
            $productsDTOs = array_map(fn(Product $product) => (new ProductDTO($product))->toArray(), $products);
            $cacheItem->set($productsDTOs);
            $cacheItem->expiresAfter(self::PRODUCTS_CACHE_TIME);
            $this->cache->save($cacheItem);
        }

        return $cacheItem->get();
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

        /**
         * TODO обновление фото
         */

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

        $this->cache->deleteItem(self::PRODUCTS_CACHE_KEY);

        return true;
    }


    public function createNewProduct(CreateProductDTO $dto): array
    {
        $product = new Product();
        $product->setName($dto->getName())
            ->setDescription($dto->getDescription())
            ->setPrice($dto->getPrice())
            ->setActive($dto->getActive());

        if ($uploadImage = $dto->getImage()) {
            $file = (new FileSaver($uploadImage))->save();
            if (is_object($file)) {
                $image = new Image();
                $image->setName($file->getFilename());
                $image->setSource('/upload/' . $file->getFilename());
                $image->setProduct($product);
                $product->addImage($image);
                $this->manager->persist($image);
            }
        }

        $this->manager->persist($product);
        $this->manager->flush();

        $this->cache->deleteItem(self::PRODUCTS_CACHE_KEY);

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