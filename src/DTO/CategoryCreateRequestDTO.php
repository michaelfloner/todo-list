<?php

declare(strict_types=1);

namespace App\DTO;

use OpenApi\Attributes\Property;
use Symfony\Component\Validator\Constraints as Assert;

class CategoryCreateRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Property(description: "category name", type: "string", example: "Category name")]
        public string $name,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }
}
