<?php

declare(strict_types=1);

use App\Http\Controllers\AnswerRecordController;
use App\Http\Controllers\AuthenticateSessionController;
use App\Http\Controllers\ContestController;
use App\Http\Controllers\EmailVerifyController;
use App\Http\Controllers\PasswordResetLinkController;
use App\Http\Controllers\QuestionAnswerController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuizQuestionController;
use App\Http\Controllers\QuizSubmissionController;
use App\Http\Controllers\RegisterUserController;
use App\Http\Middleware\EnsureQuizIsNotAlreadyStarted;
use App\Models\Answer;
use App\Models\Question;
use Illuminate\Support\Facades\Route;

Route::get("/", [ContestController::class, "index"])->name("home");
Route::post("/auth/register", [RegisterUserController::class, "store"])->name("register");
Route::post("/auth/login", [AuthenticateSessionController::class, "authenticate"])->name("login");
Route::get("/auth/logout", [AuthenticateSessionController::class, "logout"])->name("logout");
Route::get("/email/verify", [EmailVerifyController::class, "create"])->middleware("auth")->name("verification.notice");
Route::get("/email/{id}/{hash}", EmailVerifyController::class)->middleware(["signed", "throttle:6,1"])->name("verification.verify");
Route::post("email/verification-notification", [EmailVerifyController::class, "send"])->middleware("auth", "throttle:6,1")->name("verification.send");

Route::middleware(["guest"])->group(function (): void {
    Route::get("/auth/forgot-password", [PasswordResetLinkController::class, "create"])->name("password.request");
    Route::post("/auth/forgot-password", [PasswordResetLinkController::class, "store"])->name("password.email");
    Route::get("/auth/password/reset/{token}", [PasswordResetLinkController::class, "resetCreate"])->name("password.reset");
});

Route::post("/auth/password/reset", [PasswordResetLinkController::class, "resetStore"])->name("password.update");

Route::group(["prefix" => "admin"], function (): void {
    Route::get("/quizzes", [QuizController::class, "index"])->name("admin.quizzes.index");
    Route::post("/quizzes", [QuizController::class, "store"])->name("admin.quizzes.store");
    Route::get("/quizzes/{quiz}", [QuizController::class, "show"])->name("admin.quizzes.show");
    Route::patch("/quizzes/{quiz}", [QuizController::class, "update"])->can("update,quiz")->name("admin.quizzes.update");
    Route::delete("/quizzes/{quiz}", [QuizController::class, "destroy"])->can("delete,quiz")->name("admin.quizzes.destroy");
    Route::post("/quizzes/{quiz}/clone/", [QuizController::class, "clone"])->name("admin.quizzes.clone");

    Route::get("/quizzes/{quiz}/questions", [QuizQuestionController::class, "index"])->name("admin.questions.index");
    Route::post("/quizzes/{quiz}/questions", [QuizQuestionController::class, "store"])->can("create," . Question::class . ",quiz")->name("admin.questions.store");
    Route::get("/questions/{question}", [QuizQuestionController::class, "show"])->name("admin.questions.show");
    Route::patch("/questions/{question}", [QuizQuestionController::class, "update"])->can("update,question")->name("admin.questions.update");
    Route::delete("/questions/{question}", [QuizQuestionController::class, "destroy"])->can("delete,question")->name("admin.questions.destroy");
    Route::post("/questions/{question}/clone/{quiz}", [QuizQuestionController::class, "clone"])->can("clone,question,quiz")->name("admin.questions.clone");

    Route::get("/questions/{question}/answers", [QuestionAnswerController::class, "index"])->name("admin.answers.index");
    Route::post("/questions/{question}/answers", [QuestionAnswerController::class, "store"])->can("create," . Answer::class . ",question")->name("admin.answers.store");
    Route::get("/answers/{answer}", [QuestionAnswerController::class, "show"])->name("admin.answers.show");
    Route::patch("/answers/{answer}", [QuestionAnswerController::class, "update"])->can("update,answer")->name("admin.answers.update");
    Route::delete("/answers/{answer}", [QuestionAnswerController::class, "destroy"])->can("delete,answer")->name("admin.answers.destroy");
    Route::post("/answers/{answer}/clone/{question}", [QuestionAnswerController::class, "clone"])->can("clone,answer,question")->name("admin.answers.clone");
    Route::post("/answers/{answer}/correct", [QuestionAnswerController::class, "markAsCorrect"])->can("update,answer")->name("admin.answers.correct");
    Route::post("/answers/{answer}/invalid", [QuestionAnswerController::class, "markAsInvalid"])->can("update,answer")->name("admin.answers.invalid");
})->middleware(["auth"]);

Route::middleware(["auth"])->group(function (): void {
    Route::post("/quizzes/{quiz}/start", [QuizController::class, "createSubmission"])->middleware(EnsureQuizIsNotAlreadyStarted::class)->can("submit,quiz")->name("quizzes.start");
    Route::get("/submissions/{quizSubmission}/", [QuizSubmissionController::class, "show"])->can("view,quizSubmission")->name("submissions.show");
    Route::patch("/answers/{answerRecord}/{answer}", [AnswerRecordController::class, "answer"])->can("answer,answerRecord,answer")->name("answers.answer");
});
