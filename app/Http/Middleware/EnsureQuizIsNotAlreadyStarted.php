<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\QuizSubmission;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureQuizIsNotAlreadyStarted
{
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        $user = $request->user();
        $quiz = $request->route()->parameter("quiz");

        $submission = QuizSubmission::query()->where(["quiz_id" => $quiz->id, "user_id" => $user->id])->first();

        if ($submission) {
            return redirect(route("submissions.show", $submission->id));
        }

        return $next($request);
    }
}
