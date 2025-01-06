<?php

namespace App\Service;

use App\DTO\Category\CategoryDTO;
use App\DTO\Category\CreateCategoryDTO;
use App\DTO\Category\OneCategoryDTO;
use App\DTO\Category\UpdateCategoryDTO;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;

class CategoryService extends AbstractService
{
    protected array $fields = [
        'title',
        'active'
    ];

    public function __construct(
        private readonly CategoryRepository     $repository,
        private readonly EntityManagerInterface $manager
    )
    {
    }


    public function getAll(): array
    {
        $categories = $this->repository->getActive();

        return array_map(fn($category) => (new CategoryDTO($category))->toArray(), $categories);
    }


    public function getOne(OneCategoryDTO $dto): array
    {
        $category = $this->repository->findOneBy([
            'id' => $dto->getId(),
            'active' => true
        ]);

        return !$category ? [] : (new CategoryDTO($category))->toArray();
    }


    public function update(UpdateCategoryDTO $dto): array
    {
        $category = $this->repository->findOneBy([
            'id' => $dto->getId(),
            'active' => true
        ]);

        if (!$category) {
            throw new \Exception('Category not found', 400);
        }

        $category->setTitle($dto->getTitle());
        $this->manager->persist($category);
        $this->manager->flush();

        return (new CategoryDTO($category))->toArray();
    }


    public function create(CreateCategoryDTO $dto): array
    {
        $category = new Category();
        $category->setTitle($dto->getTitle())
            ->setActive($dto->getActive());

        $this->manager->persist($category);
        $this->manager->flush();

        return (new CategoryDTO($category))->toArray();
    }


    public function delete(OneCategoryDTO $dto): bool
    {
       $category = $this->repository->findOneBy([
           'id' => $dto->getId(),
           'active' => true
       ]);

       if (!$category) {
           return false;
       }

       $this->manager->remove($category);
       $this->manager->flush();

       return true;
    }


}