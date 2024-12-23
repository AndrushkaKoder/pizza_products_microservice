<?php

namespace App\Controller;

use App\DTO\Product\UpdateProductDTO;
use App\Service\ProductsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{

    public function __construct(private readonly ProductsService $productsService)
    {
    }

    #[Route('/products', name: 'product.all', methods: 'GET')]
    public function getAll(): JsonResponse
    {
        return new JsonResponse($this->productsService->getAllProducts());
    }

    #[Route('/products/{id}', name: 'product.one', methods: 'GET')]
    public function getOne(int $id): JsonResponse
    {
        $product = $this->productsService->getOne($id);
        return new JsonResponse($product);
    }

    #[Route('/products/{id}/update', name: 'product.update', methods: 'PUT')]
    public function update(int $id): JsonResponse
    {
        return new JsonResponse($this->productsService->updateProduct(new UpdateProductDTO()));
    }

}