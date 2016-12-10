<?php

namespace Pstryk82\LeagueBundle\Storage;

use Doctrine\ORM\EntityManager;
use Pstryk82\LeagueBundle\Repository\LeagueProjectionRepository;

class ProjectionStorage
{
    /**
     * @var LeagueProjectionRepository
     */
    private $repository;

    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct($repository, EntityManager $entityManager)
    {
        // @todo remove repository or refactor to create a separate ProjectionStorage service for each Projection
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }

    public function save($projection)
    {
        $this->entityManager->persist($projection);
        $this->entityManager->flush();

    }

    /**
     * @param string $aggregateId
     * @param string $projectionClass
     *
     * @return mixed
     */
    public function find($aggregateId, $projectionClass)
    {
        $repo = $this->entityManager->getRepository($projectionClass);

        return $repo->find($aggregateId);
    }
}
