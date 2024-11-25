<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\UserQuiz;
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

        $userQuiz = UserQuiz::query()->where(["quiz_id" => $quiz->id, "user_id" => $user->id])->first();

        if ($userQuiz) {
            return redirect(route("userQuizzes.show", $userQuiz->id));
        }

        return $next($request);
    }
}
