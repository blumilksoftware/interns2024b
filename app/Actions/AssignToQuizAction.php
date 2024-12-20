<?php

declare(strict_types=1);

namespace App\Actions;

use App\Events\UserInvitedToQuiz;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Support\Collection;

class AssignToQuizAction
{
    /**
     * @param Collection<int> $userIds
     */
    public function execute(Quiz $quiz, Collection $userIds, User $inviter): void
    {
        $assignedUsers = $quiz->assignedUsers()->get();
        $users = User::query()->whereIn("id", $userIds)->get();

        $users = $users->filter(fn(User $user) => !$assignedUsers->contains($user));
        $quiz->assignedUsers()->attach($users);

        foreach ($users as $user) {
            event(new UserInvitedToQuiz($quiz, $user, $inviter));
        }
    }
}
