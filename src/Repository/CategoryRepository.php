<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Category;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 *
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByIdAndOwner(
        User $owner,
        int $id,
    ): ?Category {
        $queryBuilder = $this->createQueryBuilder('c');

        $result =
            $queryBuilder
                ->where('c.id = :id')
                ->andWhere('c.owner = :owner')
                ->setParameter('id', $id)
                ->setParameter('owner', $owner)
                ->getQuery()
                ->getOneOrNullResult();

        if ($result instanceof Category) {
            return $result;
        }

        return null;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByNameAndOwner(
        User $owner,
        string $name,
    ): ?Category {
        $queryBuilder = $this->createQueryBuilder('c');

        $result =
            $queryBuilder
                ->where('c.name = :name')
                ->andWhere('c.owner = :owner')
                ->setParameter('name', $name)
                ->setParameter('owner', $owner)
                ->getQuery()
                ->getOneOrNullResult();


        if ($result instanceof Category) {
            return $result;
        }

        return null;
    }

    public function saveCategory(Category $category): void
    {
        $this->getEntityManager()->persist($category);
        $this->getEntityManager()->flush();
    }
}
