<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\UserQuestion;
use Illuminate\Http\RedirectResponse;

use function redirect;

class UserQuestionController extends Controller
{
    public function answer(UserQuestion $userQuestion, Answer $answer): RedirectResponse
    {
        $userQuestion->answer()->associate($answer)->save();

        return redirect()->back();
    }
}
