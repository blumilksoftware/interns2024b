<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Quiz;
use App\Models\User;
use Illuminate\Support\Collection;

class AssignToQuizAction
{
    /**
     * @param Collection<int> $userIds
     */
    public function execute(Quiz $quiz, Collection $userIds): void
    {
        $assignedUsers = $quiz->assignedUsers()->get();
        $users = User::query()->whereIn("id", $userIds)->get();

        $users = $users->filter(fn(User $user) => !$assignedUsers->contains($user));
        $quiz->assignedUsers()->attach($users);
    }
}
