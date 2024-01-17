<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\Category;
use Doctrine\Common\Collections\ArrayCollection;
use OpenApi\Attributes\Property;
use Doctrine\Common\Collections\Collection;

class CategoryResponseDTO
{
    public function __construct(
        #[Property(description: "Category id", type: "integer", example: 1)]
        public int|null $id,
        #[Property(description: "Category name", type: "string", example: "category1")]
        public string $name,
    ) {
    }

    public static function create(Category $category): self
    {
        return new self($category->getId(), $category->getName());
    }

    /**
     * @param Collection<int, Category> $categories
     * @return array<CategoryResponseDTO>
     */
    public static function createFromList(Collection $categories): array
    {
        $responses = new ArrayCollection();
        foreach ($categories as $category) {
            if (!$category instanceof Category) {
                continue;
            }

            $responses->add(
                self::create($category)
            );
        }

        return $responses->toArray();
    }
}
