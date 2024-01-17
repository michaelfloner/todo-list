<?php

declare(strict_types=1);

namespace App\Factory;

use App\DTO\CategoryCreateRequestDTO;
use App\Entity\Category;
use App\Entity\User;

class CategoryFactory
{
    public static function create(
        User $owner,
        CategoryCreateRequestDTO $dto,
    ): Category {
        $category = new Category();

        $category
            ->setName($dto->getName())
            ->setOwner($owner);

        return $category;
    }
}
