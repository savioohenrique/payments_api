<?php

namespace App\Repositories\Interfaces;

interface UserTypeRepositoryInterface
{
    public function findUser(string $email);
    public function getAuth();
}