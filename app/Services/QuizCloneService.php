<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;

class QuizCloneService
{
    public function cloneQuiz(Quiz $quiz): Quiz
    {
        $quizCopy = $quiz->replicate();
        $quizCopy->title = $quizCopy->title . " - kopia";
        $quizCopy->locked_at = null;
        $quizCopy->duration = null;
        $quizCopy->scheduled_at = null;
        $quizCopy->save();

        foreach ($quiz->questions as $question) {
            $this->cloneQuestion($question, $quizCopy);
        }

        return $quizCopy;
    }

    public function cloneQuestion(Question $question, Quiz $target): Question
    {
        $questionCopy = $question->replicate();
        $questionCopy->quiz()->associate($target)->save();

        foreach ($question->answers as $answer) {
            $answerCopy = $this->cloneAnswer($answer, $questionCopy);

            if ($answer->isCorrect) {
                $questionCopy->correctAnswer()->associate($answerCopy);
            }
        }

        $questionCopy->save();

        return $questionCopy;
    }

    public function cloneAnswer(Answer $answer, Question $target): Answer
    {
        $clone = $answer->replicate();
        $clone->question()->associate($target)->save();

        return $clone;
    }
}
