<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:dispatch-user-quiz-closed-event')->daily();
