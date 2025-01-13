<?php

declare(strict_types=1);

namespace App\Service\Binder;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

class ProductCategoryBinder
{
    private Product $product;

    private Category $category;

    public function __construct(
        private EntityManagerInterface $manager
    )
    {
    }

    public function setProduct(Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function setCategory(Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function attach(): array
    {
        if ($this->product->getCategories()->contains($this->category)) {
            return [
                'success' => false,
                'message' => "Error attach: {$this->product->getName()} has category: {$this->category->getName()}"
            ];
        }

        $this->product->addCategory($this->category);
        $this->category->addProduct($this->product);

       $this->manager->persist($this->product);
       $this->manager->persist($this->category);

       $this->manager->flush();

        return [
            'success' => true,
            'message' => "Success attach {$this->product->getName()} to {$this->category->getName()}"
        ];
    }


    public function detach(): array
    {
        if ($this->product->getCategories()->contains($this->category)) {
            $this->product->removeCategory($this->category);

            $this->manager->persist($this->product);

            $this->manager->flush();

            return [
                'success' => true,
                'message' => "Category `{$this->category->getName()}` was detached"
            ];
        }

        return [
            'success' => false,
            'message' => "Category `{$this->category->getName()}` not found in `{$this->product->getName()}`"
        ];
    }

}