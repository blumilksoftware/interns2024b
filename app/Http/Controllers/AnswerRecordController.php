<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\AnswerRecord;
use Illuminate\Http\RedirectResponse;

use function redirect;

class AnswerRecordController extends Controller
{
    public function answer(AnswerRecord $answerRecord, Answer $answer): RedirectResponse
    {
        $answerRecord->answer()->associate($answer)->save();

        return redirect()
            ->back()
            ->with("success", "Answer updated");
    }
}
