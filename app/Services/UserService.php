<?php

namespace App\Services;

class UserService
{
    private $user;

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }
}
