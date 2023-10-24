<?php

namespace App\Repositories;

use App\Models\User;

abstract class UserRepository extends Repository
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->setModel(User::class);
    }

}
