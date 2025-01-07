<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\Product\CreateProductDTO;
use App\DTO\Product\OneProductDTO;
use App\DTO\Product\UpdateProductDTO;
use App\Entity\Category;
use App\Entity\Product;
use App\Service\ProductsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class ProductController extends AbstractController
{
    public function __construct(private readonly ProductsService $productsService)
    {
    }


    #[Route('/products', name: 'product.all', methods: 'GET')]
    public function index(): JsonResponse
    {
        return new JsonResponse($this->productsService->getAllProducts());
    }


    #[Route('/products/{id}', name: 'product.one', methods: 'GET')]
    public function show(int $id): JsonResponse
    {
        $product = $this->productsService->getOne(new OneProductDTO($id));

        return new JsonResponse($product);
    }


    #[Route('/products/create', name: 'products.create', methods: 'POST')]
    public function store(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $checkedFields = $this->productsService->checkRequestFields($requestData);
        if ($checkedFields) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Missing required fields: ' . implode(', ', $checkedFields)
            ], 400);
        }

        return new JsonResponse(
            $this->productsService->createNewProduct(new CreateProductDTO(...$requestData)),
            201
        );
    }


    #[Route('/products/{id}', name: 'product.update', methods: 'PUT')]
    public function update(int $id, Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        if (!$requestData) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Empty data'
            ], 400);
        }

        $checkedFields = $this->productsService->checkRequestFields($requestData);
        if ($checkedFields) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Missing required fields: ' . implode(', ', $checkedFields)
            ], 400);
        }

        $productDTO = new UpdateProductDTO(
            $id,
            ...$requestData
        );

        return new JsonResponse($this->productsService->updateProduct($productDTO));
    }


    #[Route('/products/{id}', name: 'products.delete', methods: 'DELETE')]
    public function delete(int $id): JsonResponse
    {
        $deleted = $this->productsService->deleteProduct(new OneProductDTO($id));

        return new JsonResponse([
            'success' => $deleted,
            'message' => $deleted ? 'Product was deleted' : 'Product not found'
        ], $deleted ? 200 : 400);
    }


    #[Route('/products/{product}/attach/{category}', methods: 'POST')]
    public function attachCategory(Product $product, Category $category): JsonResponse
    {
        return new JsonResponse($this->productsService->attachCategory($product, $category));
    }

}