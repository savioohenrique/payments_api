<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserTypeRepositoryInterface;

Class UserRepository implements UserTypeRepositoryInterface
{
    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->user = new User();
    }

    public function findUser(String $email)
    {
        return $this->user->where('email', '=', $email)->first();
    }

    public function getAuth()
    {
        return auth('users');
    }
    
}