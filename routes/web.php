<?php

declare(strict_types=1);

use App\Http\Controllers\AuthenticateSessionController;
use App\Http\Controllers\ContestController;
use App\Http\Controllers\EmailVerifyController;
use App\Http\Controllers\PasswordResetLinkController;
use App\Http\Controllers\QuestionAnswerController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuizQuestionController;
use App\Http\Controllers\RegisterUserController;
use App\Models\Answer;
use App\Models\Question;
use Illuminate\Support\Facades\Route;

Route::get("/", [ContestController::class, "index"])->name("home");
Route::get("/auth/register", [RegisterUserController::class, "create"])->name("register");
Route::post("/auth/register", [RegisterUserController::class, "store"])->name("register");
Route::get("/auth/login", [AuthenticateSessionController::class, "create"])->name("login");
Route::post("/auth/login", [AuthenticateSessionController::class, "authenticate"])->name("login");
Route::get("/auth/logout", [AuthenticateSessionController::class, "logout"])->name("logout");
Route::get("/email/verify", [EmailVerifyController::class, "create"])->middleware("auth")->name("verification.notice");
Route::get("/email/{id}/{hash}", EmailVerifyController::class)->middleware(["signed", "throttle:6,1"])->name("verification.verify");
Route::post("email/verification-notification", [EmailVerifyController::class, "send"])->middleware("auth", "throttle:6,1")->name("verification.send");

Route::get("/auth/forgot-password", [PasswordResetLinkController::class, "create"])->middleware("guest")->name("password.request");
Route::post("/auth/forgot-password", [PasswordResetLinkController::class, "store"])->middleware("guest")->name("password.email");
Route::get("/auth/password/reset/{token}", [PasswordResetLinkController::class, "resetCreate"])->middleware("guest")->name("password.reset");
Route::post("/auth/password/reset", [PasswordResetLinkController::class, "resetStore"])->name("password.update");

Route::get("/quizzes", [QuizController::class, "index"]);
Route::post("/quizzes", [QuizController::class, "store"]);
Route::get("/quizzes/{quiz}", [QuizController::class, "show"]);
Route::patch("/quizzes/{quiz}", [QuizController::class, "update"])->can("update,quiz");
Route::delete("/quizzes/{quiz}", [QuizController::class, "destroy"])->can("delete,quiz");
Route::post("/quizzes/{quiz}/lock", [QuizController::class, "lock"]);
Route::post("/quizzes/{quiz}/clone/", [QuizController::class, "clone"]);

Route::get("/quizzes/{quiz}/questions", [QuizQuestionController::class, "index"]);
Route::post("/quizzes/{quiz}/questions", [QuizQuestionController::class, "store"])->can("create," . Question::class . ",quiz");
Route::get("/questions/{question}", [QuizQuestionController::class, "show"]);
Route::patch("/questions/{question}", [QuizQuestionController::class, "update"])->can("update,question");
Route::delete("/questions/{question}", [QuizQuestionController::class, "destroy"])->can("delete,question");
Route::post("/questions/{question}/clone/{quiz}", [QuizQuestionController::class, "clone"])->can("clone,question,quiz");

Route::get("/questions/{question}/answers", [QuestionAnswerController::class, "index"]);
Route::post("/questions/{question}/answers", [QuestionAnswerController::class, "store"])->can("create," . Answer::class . ",question");
Route::get("/answers/{answer}", [QuestionAnswerController::class, "show"]);
Route::patch("/answers/{answer}", [QuestionAnswerController::class, "update"])->can("update,answer");
Route::delete("/answers/{answer}", [QuestionAnswerController::class, "destroy"])->can("delete,answer");
Route::post("/answers/{answer}/clone/{question}", [QuestionAnswerController::class, "clone"])->can("clone,answer,question");
Route::post("/answers/{answer}/correct", [QuestionAnswerController::class, "markAsCorrect"])->can("update,answer");
Route::post("/answers/{answer}/invalid", [QuestionAnswerController::class, "markAsInvalid"])->can("update,answer");
