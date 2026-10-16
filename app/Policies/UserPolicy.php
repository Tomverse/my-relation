<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    function list(User $user): bool
    {
        return $user->isAdministrator();
    }

    function view(User $user): bool
    {
        return $this->list($user);
    }

    function create(User $user): bool
    {
        return $user->isAdministrator();
    }

    function update(User $user): bool
    {
        return $this->create($user);
    }


    function updateRole(User $user, User $target): bool
    {
        return $this->update($user) && !$this->isSelf($user, $target);
    }


    function updateEmail(User $user, User $target): bool
    {
        return $user->isAdministrator() && $this->isSelf($user, $target);
    }


    function delete(User $user, User $target): bool
    {
        return $this->update($user) && !$this->isSelf($user, $target);
    }

    private function isSelf(User $user, User $target): bool
    {
        return $user->id === $target->id;
    }
}