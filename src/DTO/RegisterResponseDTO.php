<?php

declare(strict_types=1);

namespace App\DTO;

use OpenApi\Attributes\Property;

class RegisterResponseDTO
{
    public function __construct(
        #[Property(description: "User email", type: "string", example: "user@example.com")]
        public string $email,
    ) {
    }

    public static function create(string $email): self
    {
        return new self($email);
    }
}
