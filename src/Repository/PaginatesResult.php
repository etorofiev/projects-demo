<?php

namespace App\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator;

trait PaginatesResult
{
    public function paginate($dql, $page = 1, $limit = 20)
    {
        ////TODO switch to the seek method for pagination
        $paginator = new Paginator($dql, false);

        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);

        return $paginator;
    }
}