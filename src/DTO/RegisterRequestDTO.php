<?php

declare(strict_types=1);

namespace App\DTO;

use OpenApi\Attributes\Property;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        #[Property(description: "email", type: "string", example: "email@email.com")]
        public string $email,
        #[Assert\PasswordStrength([
            'minScore' => Assert\PasswordStrength::STRENGTH_WEAK,
        ])]
        #[Assert\NotBlank]
        #[Property(description: "password", type: "string")]
        public string $password,
    ) {
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
