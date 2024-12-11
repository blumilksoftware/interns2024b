<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Quiz;
use App\Models\User;
use Illuminate\Support\Collection;

class UnassignFromQuizAction
{
    /**
     * @param Collection<User> $users
     */
    public function execute(Quiz $quiz, Collection $users): void
    {
        $assignedUsers = $quiz->assignedUsers;
        $users = User::query()->whereIn("id", $users)->get();

        $users = $users->filter(fn(User $user) => $assignedUsers->contains($user));
        $quiz->assignedUsers()->detach($users);
    }
}
