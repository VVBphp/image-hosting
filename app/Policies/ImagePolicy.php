<?php

namespace App\Policies;

use App\Models\Image;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ImagePolicy
{
    use HandlesAuthorization;

    public function before(?User $user, $ability)
    {
        if (optional($user)->is_admin) {
            return true;
        }
    }

    public function viewAny(?User $user): bool
    {
        return optional($user)->is_active;
    }

    public function view(?User $user, Image $image): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->is_active;
    }

    public function delete(User $user, Image $image): bool
    {
        return $user->is_admin || $image->user_id === $user->id;
    }

    public function restore(User $user, Image $image): bool
    {
        return $user->is_admin || $image->user_id === $user->id;
    }

    public function forceDelete(User $user, Image $image): bool
    {
        return $user->is_admin;
    }
}
