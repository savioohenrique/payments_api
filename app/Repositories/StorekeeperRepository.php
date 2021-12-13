<?php

namespace App\Repositories;

use App\Models\Storekeeper;
use App\Repositories\Interfaces\UserTypeRepositoryInterface;

Class StorekeeperRepository implements UserTypeRepositoryInterface
{
    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->storekeeper = new Storekeeper();
    }

    public function findUser(String $email)
    {
        return $this->storekeeper->where('email', '=', $email)->first();
    }

    public function getAuth()
    {
        return auth('storekeepers');
    }
    
}