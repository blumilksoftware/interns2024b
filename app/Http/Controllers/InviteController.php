<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\InviteQuizRequest;
use App\Http\Resources\UserResource;
use App\Models\Quiz;
use App\Models\User;
use App\Notifications\InviteUserNotification;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class InviteController extends Controller
{
    public function index(Quiz $quiz): Response
    {
        $users = User::query()->role("user")->with("school")->orderBy("id");

        return Inertia::render("Admin/Invite", [
            "users" => UserResource::collection($users->paginate(100)),
            "quiz" => $quiz,
        ]);
    }

    public function store(Quiz $quiz, InviteQuizRequest $request): RedirectResponse
    {
        $userIds = $request->input("ids", []);

        if (empty($userIds)) {
            return redirect()->back()->withErrors("Brak użytkowników do zaproszenia.");
        }

        $users = User::query()->whereIn("id", $userIds)->get();

        foreach ($users as $user) {
            $user->notify((new InviteUserNotification($quiz))->delay(now()->addHour()));
        }

        return redirect()
            ->back()
            ->with("status", "Zaproszenia zostały zaplanowane do wysłania.");
    }
}
