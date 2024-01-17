<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Traits\ControllerTrait;
use App\DTO\CategoryCreateRequestDTO;
use App\DTO\CategoryResponseDTO;
use App\Entity\Category;
use App\Entity\User;
use App\Service\CategoryService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/categories', name: "category:")]
class CategoryController extends AbstractController
{
    use ControllerTrait;

    public function __construct(
        private readonly CategoryService $categoryService,
    ) {
    }

    #[Route('', name: 'create', methods: 'POST', format: 'json')]
    #[OA\Tag("Category")]
    #[OA\RequestBody(
        description: 'Category request',
        content: new Model(type: CategoryCreateRequestDTO::class),
    )]
    #[OA\Response(
        response: 201,
        description: 'Successful response',
        content: new Model(type: CategoryResponseDTO::class)
    )]
    #[OA\Response(
        response: 400,
        description: 'Returned when invalid data posted'
    )]
    #[OA\Response(
        response: 401,
        description: 'Invalid JWT Token'
    )]
    #[OA\Response(
        response: 403,
        description: 'Unauthorized'
    )]
    #[OA\Response(
        response: 409,
        description: 'Conflict'
    )]
    public function createCategory(
        #[CurrentUser] User $user,
        #[MapRequestPayload(
            acceptFormat: 'json',
            validationFailedStatusCode: Response::HTTP_BAD_REQUEST
        )] CategoryCreateRequestDTO $dto,
    ): JsonResponse {
        $category = $this->categoryService->createCategory($user, $dto);

        return $this->JSON(CategoryResponseDTO::create($category), Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'get', requirements: ['id' => '\d+'], methods: 'GET', format: 'json')]
    #[OA\Tag("Category")]
    #[OA\Parameter(
        name: 'id',
        description: 'ID entity',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )
    ]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new Model(type: CategoryResponseDTO::class)
    )]
    #[OA\Response(
        response: 401,
        description: 'Invalid JWT Token'
    )]
    #[OA\Response(
        response: 403,
        description: 'Unauthorized'
    )]
    #[OA\Response(
        response: 404,
        description: 'Not found'
    )]
    #[IsGranted('view', 'category', 'Not found', Response::HTTP_NOT_FOUND)]
    public function getCategory(
        Category $category,
    ): JsonResponse {
        return $this->JSON(CategoryResponseDTO::create($category), Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'delete', requirements: ['id' => '\d+'], methods: 'DELETE', format: 'json')]
    #[OA\Parameter(
        name: 'id',
        description: 'ID entity',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )
    ]
    #[OA\Tag("Category")]
    #[OA\Response(
        response: 204,
        description: 'Successful response'
    )]
    #[OA\Response(
        response: 401,
        description: 'Unauthorized'
    )]
    #[OA\Response(
        response: 404,
        description: 'Not found'
    )]
    #[IsGranted('delete', 'category', 'Not found', Response::HTTP_NOT_FOUND)]
    public function deleteCategory(
        Category $category,
    ): JsonResponse {
        $this->categoryService->deleteCategory($category);

        return $this->emptyResponse();
    }
}
