<?php

namespace App\Repositories;

use App\DbManager;
use App\Models\User;

/**
 * Repository to load user data
 */
class UserRepo
{
    private $repository;

    public function __construct()
    {
        $this->repository = DbManager::getRepository(User::class);
    }

    /**
     * @param string $login
     * @return User|null
     * 
     * Find user by id
     */
    public function getByLogin(string $login) : ?User
    {
        return $this->repository->findOneBy(['login' => $login]);
    }
}