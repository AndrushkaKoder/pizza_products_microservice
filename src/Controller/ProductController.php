<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\Product\CreateProductDTO;
use App\DTO\Product\OneProductDTO;
use App\DTO\Product\UpdateProductDTO;
use App\Entity\Category;
use App\Entity\Product;
use App\Service\Binder\ProductCategoryBinder;
use App\Service\File\FileSaver;
use App\Service\ProductsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class ProductController extends AbstractController
{
    public function __construct(
        private readonly ProductsService $productsService,
        private readonly ProductCategoryBinder $productCategoryBinder
    )
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


    #[Route('/products/{product}/attach/{category}', name: 'products.category.attach', methods: 'GET')]
    public function attachCategory(Product $product, Category $category): JsonResponse
    {
        $attach = $this->productCategoryBinder
            ->setProduct($product)
            ->setCategory($category)
            ->attach();

        return new JsonResponse($attach);
    }


    #[Route('/products/{product}/detach/{category}', name: 'products.category.detach', methods: 'GET')]
    public function detachCategory(Product $product, Category $category): JsonResponse
    {
        $detach = $this->productCategoryBinder
            ->setProduct($product)
            ->setCategory($category)
            ->detach();

        return new JsonResponse($detach);
    }


    #[Route('/products/{id}/upload', name: 'products.add_image', methods: 'POST')]
    public function uploadImage(Product $product, Request $request): JsonResponse
    {
        $file = $request->files->get('image');

        if (!$file) {
            return new JsonResponse([
                'success' => false,
                'message' => 'No image'
            ], 400);
        }

        $fileSaver = new FileSaver($file);

        return new JsonResponse([
            'success' => $fileSaver->save()
        ]);
    }

}