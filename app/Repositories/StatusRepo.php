<?php

namespace App\Repositories;

use App\DbManager;
use App\Models\Status;

/**
 * Repository to load status data
 */
class StatusRepo
{
    private $repository;

    public function __construct()
    {
        $this->repository = DbManager::getRepository(Status::class);
    }

    /**
     * @return Status
     * 
     * load status "done"
     */
    public function getDone() : Status
    {
        return $this->repository->find(Status::STATE_DONE);
    }

    /**
     * @return Status
     * 
     * load status "new"
     */
    public function getNew() : Status
    {
        return $this->repository->find(Status::STATE_NEW);
    }

    /**
     * @return Status
     * 
     * load status by id
     */
    public function findById(int $id) : ?Status
    {
        return $this->repository->find($id);
    }
}