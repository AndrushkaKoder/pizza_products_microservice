<?php

namespace App\Controller;

use App\DTO\Category\CreateCategoryDTO;
use App\DTO\Category\OneCategoryDTO;
use App\DTO\Category\UpdateCategoryDTO;
use App\Service\CategoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class CategoryController extends AbstractController
{
    public function __construct(private readonly CategoryService $service)
    {
    }


    #[Route('/categories', name: 'categories.index', methods: 'GET')]
    public function index(): JsonResponse
    {
        return new JsonResponse($this->service->getAll());
    }


    #[Route('/categories/{id}', name: 'categories.show', methods: 'GET')]
    public function show(int $id): JsonResponse
    {
        return new JsonResponse($this->service->getOne(new OneCategoryDTO($id)));
    }


    #[Route('/categories/{id}', name: 'categories.update', methods: 'PUT')]
    public function update(int $id, Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        if ($checkFiles = $this->service->checkRequestFields($requestData)) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Missing fields: ' . implode(', ', $checkFiles)
            ], 400);
        }

        $categoryDTO = new UpdateCategoryDTO($id, ...$requestData);

        return new JsonResponse($this->service->update($categoryDTO));
    }


    #[Route('/categories/create', name: 'categories.create', methods: 'POST')]
    public function store(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        if ($checkFiles = $this->service->checkRequestFields($requestData)) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Missing fields: ' . implode(', ', $checkFiles)
            ], 400);
        }

        return new JsonResponse($this->service->create(new CreateCategoryDTO(...$requestData)));
    }


    #[Route('/categories/{id}', name: 'categories.delete', methods: 'DELETE')]
    public function delete(int $id): JsonResponse
    {
        $deleted = $this->service->delete(new OneCategoryDTO($id));

        return new JsonResponse([
            'success' => $deleted,
            'message' => $deleted ? 'Category was deleted' : 'Category not found'
        ], $deleted ? 200 : 400);
    }
}
