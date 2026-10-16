<?php

namespace App\Policies;

use App\Models\User;

class ShopPolicy
{
    function list(): bool
    {
        
        return true;
    }

    function view(): bool
    {
        return $this->list();
    }

    function create(User $user): bool
    {
        return $user->isAdministrator();
    }

    function update(User $user): bool
    {
        return $this->create($user);
    }

    function delete(User $user): bool
    {
      
        return $this->update($user);
    }
}
