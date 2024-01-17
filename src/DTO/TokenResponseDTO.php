<?php

declare(strict_types=1);

namespace App\DTO;

use OpenApi\Attributes\Property;

class TokenResponseDTO
{
    public function __construct(
        #[Property(description: "Access token", type: "string", example: "abcdef123456")]
        public string $token,
    ) {
    }

    public static function create(
        string $token,
    ): self {
        return new self(
            $token,
        );
    }
}
