<?php
// src/Repository/ProjectRepository.php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\Skill;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    
    public function findByKeyword($keyword, $sortBy)
    {
        // Crée une requête de base pour récupérer les projets
        $queryBuilder = $this->createQueryBuilder('p');

        // Ajoute des conditions de recherche si un mot-clé est spécifié
        if (!empty($keyword)) {
            $queryBuilder->andWhere('p.name LIKE :keyword OR p.description LIKE :keyword')
                ->setParameter('keyword', '%' . $keyword . '%');
        }

        // Ajoute une condition de tri si le tri par date est spécifié
        if ($sortBy == 'date') {
            $queryBuilder->orderBy('p.createdAt', 'DESC');
        }

        // Exécute la requête
        $query = $queryBuilder->getQuery();
        $results = $query->getResult();

        return $results;
    }
}
