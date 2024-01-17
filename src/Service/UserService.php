<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\RegisterRequestDTO;
use App\Entity\User;
use App\Exception\ResponseException;
use App\Factory\UserFactory;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;

readonly class UserService
{
    public function __construct(
        private UserFactory $userFactory,
        private UserRepository $userRepository,
    ) {
    }

    public function createUser(RegisterRequestDTO $dto): User
    {
        if ($this->userRepository->findOneByEmail($dto->email) !== null) {
            throw new ResponseException('Entity with the same name already exists', Response::HTTP_CONFLICT);
        }

        $user = $this->userFactory->createUserFromDTO($dto);
        $this->userRepository->saveUser($user);

        return $user;
    }
}
