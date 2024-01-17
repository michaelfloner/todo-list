<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\LoginRequestDTO;
use App\DTO\RegisterRequestDTO;
use App\DTO\RegisterResponseDTO;
use App\DTO\TokenResponseDTO;
use App\Entity\User;
use App\Service\UserService;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use OpenApi\Attributes as OA;

#[Route('/auth', name: "auth:")]
class AuthController extends AbstractController
{
    public function __construct(
        private readonly UserService $userService
    ) {
    }

    #[Route('/register', name: 'register', methods: 'POST', format: 'json')]
    #[OA\Tag("Auth")]
    #[OA\RequestBody(
        description: 'Registration request',
        content: new Model(type: RegisterRequestDTO::class),
    )]
    #[OA\Response(
        response: 201,
        description: 'Successful response',
        content: new Model(type: RegisterResponseDTO::class)
    )]
    #[OA\Response(
        response: 400,
        description: 'Returned when invalid data posted'
    )]
    public function register(
        #[MapRequestPayload(
            acceptFormat: 'json',
            validationFailedStatusCode: Response::HTTP_BAD_REQUEST
        )] RegisterRequestDTO $dto
    ): JsonResponse {
        try {
            $user = $this->userService->createUser($dto);
        } catch (\Throwable $exception) {
            return $this->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }

        return $this->JSON(RegisterResponseDTO::create($user->getEmail()), Response::HTTP_CREATED);
    }

    #[Route('/login', name: 'login', methods: 'POST', format: 'json')]
    #[OA\Tag("Auth")]
    #[OA\RequestBody(
        description: 'Login request',
        content: new Model(type: LoginRequestDTO::class)
    )]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new Model(type: TokenResponseDTO::class)
    )]
    #[OA\Response(
        response: 400,
        description: 'Returned when user is not found / entered wrong credentials'
    )]
    #[OA\Response(
        response: 401,
        description: 'Returned when user is not found / entered wrong credentials'
    )]
    #[OA\Response(
        response: 403,
        description: 'Unauthorized'
    )]
    public function login(#[CurrentUser] ?User $user, JWTTokenManagerInterface $JWTTokenManager): JsonResponse
    {
        if (null === $user) {
             return $this->json([
                 'message' => 'missing credentials',
             ], Response::HTTP_UNAUTHORIZED);
        }

        $token = $JWTTokenManager->create($user);

        return $this->json(TokenResponseDTO::create($token));
    }
}
