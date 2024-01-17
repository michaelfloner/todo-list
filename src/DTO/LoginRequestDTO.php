<?php

declare(strict_types=1);

namespace App\DTO;

use OpenApi\Attributes\Property;
use Symfony\Component\Validator\Constraints as Assert;

class LoginRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        #[Property(description: "username", type: "string", example: "email@email.com")]
        public string $username,
        #[Assert\NotBlank]
        #[Property(description: "password", type: "string")]
        public string $password,
    ) {
    }
}
