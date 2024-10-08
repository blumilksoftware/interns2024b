<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    public function update(User $currentUser, User $user): Response
    {
        if ($currentUser->hasRole("admin") && !$user->hasRole("admin|super_admin") && !$user->is_anonymized) {
            return Response::allow();
        }

        if ($currentUser->hasRole("super_admin") && !$user->is_anonymized) {
            return Response::allow();
        }

        return Response::deny("Nie masz uprawnień do edycji tego użytkownika.");
    }

    public function delete(User $currentUser, User $user): Response
    {
        if ($user->hasRole("admin") && $currentUser->hasRole("super_admin")) {
            return Response::allow();
        }

        return Response::deny("Nie można usunąć tego użytkownika.");
    }

    public function anonymize(User $currentUser, User $user): Response
    {
        if ($user->hasRole("user") && $currentUser->hasRole("super_admin")) {
            return Response::allow();
        }

        return Response::deny("Nie można zanonimizować tego użytkownika.");
    }
}
