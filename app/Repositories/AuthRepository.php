<?php

namespace App\Repositories;

use App\Repositories\Interfaces\UserTypeRepositoryInterface;
use Illuminate\Support\Facades\Hash;

Class AuthRepository {

    public function validatePassword(UserTypeRepositoryInterface $userTypeRepository, Array $credentials)
    {
        $user = $userTypeRepository->findUser($credentials['email']);

        return Hash::check($credentials['password'], $user->password);
    }
    
}