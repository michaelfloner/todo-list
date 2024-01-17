<?php

declare(strict_types=1);

namespace App\DTO;

use OpenApi\Attributes\Items;
use OpenApi\Attributes\Property;
use Symfony\Component\Validator\Constraints as Assert;

class TaskRequestDTO
{
    /**
     * @param array<int>|null $categories
     */
    public function __construct(
        #[Assert\NotBlank]
        #[Property(description: "name", type: "string", example: "Task name")]
        public string $name,
        #[Property(description: "description", type: "string", example: "some description", nullable: true)]
        public ?string $description = null,
        #[Property(
            description: "categories ",
            type: "array",
            items: new Items(type: "integer"),
            example: "[1, 2, 3]",
            nullable: true
        )
        ]
        public ?array $categories = null
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return int[]|null
     */
    public function getCategories(): ?array
    {
        return $this->categories;
    }
}
