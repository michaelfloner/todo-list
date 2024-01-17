<?php

declare(strict_types=1);

namespace App\Factory;

use App\DTO\RegisterRequestDTO;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class UserFactory
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function createUserFromDTO(RegisterRequestDTO $dto): User
    {
        $user = new User();

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $dto->getPassword(),
        );

        $user
            ->setEmail($dto->getEmail())
            ->setPassword($hashedPassword);

        return $user;
    }
}
