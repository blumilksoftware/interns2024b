<?php

declare(strict_types=1);

use App\Http\Controllers\QuestionAnswerController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuizQuestionController;
use Illuminate\Support\Facades\Route;
use Inertia\Response;

Route::get("/", fn(): Response => inertia("Welcome"));

Route::post("/quizzes/{quiz}/lock", [QuizController::class, "lock"]);
Route::post("/answers/{answer}/correct", [QuestionAnswerController::class, "markAsCorrect"]);
Route::resource("quizzes", QuizController::class)->except(["create", "edit"]);
Route::resource("quizzes.questions", QuizQuestionController::class)->except(["create", "edit"])->shallow();
Route::resource("questions.answers", QuestionAnswerController::class)->except(["create", "edit"])->shallow();
