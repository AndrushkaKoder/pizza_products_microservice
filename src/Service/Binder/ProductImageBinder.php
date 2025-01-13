<?php

declare(strict_types=1);

namespace App\Service\Binder;

use Doctrine\ORM\EntityManagerInterface;

class ProductImageBinder
{

    public function __construct(
        private readonly EntityManagerInterface $manager
    )
    {
    }

    public function attach(): array
    {
        return [];
    }

    public function detach(): array
    {
        return [];
    }

}