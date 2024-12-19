<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\UserInvitedToQuiz;
use App\Jobs\SendInviteJob;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;

class SendInvitedToQuizNotification implements ShouldQueue
{
    public function handle(UserInvitedToQuiz $event): void
    {
        $quiz = $event->quiz;
        $busName = "send_invite-" . $quiz->id . "-" . $event->invitee->id;
        $busId = Cache::get($busName);

        if ($busId !== null) {
            Bus::findBatch($busId)?->cancel();
        }

        $busId = Bus::batch([
            (new SendInviteJob($event->invitee, $quiz))->delay(Carbon::now()->addMinutes(15)),
        ])->dispatch()->id;

        Cache::set($busName, $busId);
    }
}
