<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    use PaginatesResult;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function findAllPaginated($currentPage = 1)
    {
        $query = $this->createQueryBuilder('tasks')
            ->where('tasks.deleted = :del')
            ->setParameter('del', 0)
            ->orderBy('tasks.id', 'ASC')
            ->getQuery();

        $paginator = $this->paginate($query, $currentPage);

        return $paginator;
    }
}
