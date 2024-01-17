<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 *
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }


    /**
     * @return array<Task>
     */
    public function findByOwnerAndState(
        User $owner,
        ?string $state,
    ): array {
        $queryBuilder = $this->createQueryBuilder('t');

        $queryBuilder
            ->where('t.owner = :owner')
            ->setParameter('owner', $owner);

        if ($state !== null) {
            $queryBuilder
                ->andWhere('t.state = :state')
                ->setParameter('state', $state);
        }

        $result = $queryBuilder->getQuery()->getResult();

        if (is_array($result)) {
            return $result;
        }

        return [];
    }

    public function save(Task $task): void
    {
        $this->getEntityManager()->persist($task);
        $this->getEntityManager()->flush();
    }
}
