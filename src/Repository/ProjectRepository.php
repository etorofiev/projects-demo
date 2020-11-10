<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    use PaginatesResult;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function findAllPaginated($currentPage = 1)
    {
        $query = $this->createQueryBuilder('projects')
            ->where('projects.deleted = :del')
            ->setParameter('del', 0)
            ->orderBy('projects.id', 'ASC')
            ->getQuery();

        $paginator = $this->paginate($query, $currentPage);

        return $paginator;
    }
}
