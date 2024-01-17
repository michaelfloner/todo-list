<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\CategoryCreateRequestDTO;
use App\Entity\Category;
use App\Entity\User;
use App\Exception\ResponseException;
use App\Factory\CategoryFactory;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\Response;

readonly class CategoryService
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private EntityManagerInterface $manager,
    ) {
    }

    /**
     * @throws NonUniqueResultException
     * @throws ResponseException
     */
    public function createCategory(
        User $loggedInUser,
        CategoryCreateRequestDTO $dto,
    ): Category {
        if ($this->categoryRepository->findOneByNameAndOwner($loggedInUser, $dto->getName()) !== null) {
            throw new ResponseException('Entity with the same name already exists', Response::HTTP_CONFLICT);
        }

        $category = CategoryFactory::create($loggedInUser, $dto);
        $this->categoryRepository->saveCategory($category);

        return $category;
    }

    public function deleteCategory(
        Category $category,
    ): void {
        $this->manager->remove($category);
        $this->manager->flush();
    }
}
