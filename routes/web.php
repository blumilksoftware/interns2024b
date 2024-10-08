<?php

declare(strict_types=1);

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AnswerRecordController;
use App\Http\Controllers\AuthenticateSessionController;
use App\Http\Controllers\ContestController;
use App\Http\Controllers\EmailVerifyController;
use App\Http\Controllers\PasswordResetLinkController;
use App\Http\Controllers\ProfileUserController;
use App\Http\Controllers\QuestionAnswerController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuizQuestionController;
use App\Http\Controllers\QuizSubmissionController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\RegisterUserController;
use App\Http\Controllers\SchoolsController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\EnsureQuizIsNotAlreadyStarted;
use App\Models\Answer;
use App\Models\Question;
use Illuminate\Support\Facades\Route;

Route::get("/email/verify", [EmailVerifyController::class, "create"])->name("verification.notice");
Route::get("/email/{id}/{hash}", EmailVerifyController::class)->middleware(["auth", "throttle:6,1"])->name("verification.verify");
Route::post("/email/verification-notification", [EmailVerifyController::class, "send"])->middleware("throttle:3,60")->name("verification.send");
Route::post("/auth/logout", [AuthenticateSessionController::class, "logout"])->middleware("auth")->name("logout");

Route::middleware(["guest"])->group(function (): void {
    Route::get("/", [ContestController::class, "index"])->name("home");
    Route::post("/auth/register", [RegisterUserController::class, "store"])->name("register");
    Route::get("/auth/login", fn() => redirect("/"))->name("login");
    Route::post("/auth/login", [AuthenticateSessionController::class, "authenticate"])->name("authenticate");
    Route::get("/auth/forgot-password", [PasswordResetLinkController::class, "create"])->name("password.request");
    Route::post("/auth/forgot-password", [PasswordResetLinkController::class, "store"])->name("password.email");
});

Route::get("/auth/password/reset/{token}", [PasswordResetLinkController::class, "resetCreate"])->name("password.reset");
Route::post("/auth/password/reset", [PasswordResetLinkController::class, "resetStore"])->name("password.update");

Route::group(["prefix" => "admin", "middleware" => ["auth", "role:admin|super_admin"]], function (): void {
    Route::get("/quizzes", [QuizController::class, "index"])->name("admin.quizzes.index");
    Route::post("/quizzes", [QuizController::class, "store"])->name("admin.quizzes.store");
    Route::patch("/quizzes/{quiz}", [QuizController::class, "update"])->can("update,quiz")->name("admin.quizzes.update");
    Route::delete("/quizzes/{quiz}", [QuizController::class, "destroy"])->can("delete,quiz")->name("admin.quizzes.destroy");
    Route::post("/quizzes/{quiz}/clone", [QuizController::class, "clone"])->name("admin.quizzes.clone");
    Route::post("/quizzes/{quiz}/lock", [QuizController::class, "lock"])->name("admin.quizzes.lock");
    Route::post("/quizzes/{quiz}/unlock", [QuizController::class, "unlock"])->can("unlock,quiz")->name("admin.quizzes.unlock");

    Route::get("/quizzes/{quiz}/ranking", [RankingController::class, "index"])->name("admin.quizzes.ranking");
    Route::post("/quizzes/{quiz}/ranking/publish", [RankingController::class, "publish"])->name("admin.quizzes.ranking.publish");
    Route::post("/quizzes/{quiz}/ranking/unpublish", [RankingController::class, "unpublish"])->name("admin.quizzes.ranking.unpublish");

    Route::post("/quizzes/{quiz}/questions", [QuizQuestionController::class, "store"])->can("create," . Question::class . ",quiz")->name("admin.questions.store");
    Route::patch("/questions/{question}", [QuizQuestionController::class, "update"])->can("update,question")->name("admin.questions.update");
    Route::delete("/questions/{question}", [QuizQuestionController::class, "destroy"])->can("delete,question")->name("admin.questions.destroy");
    Route::post("/questions/{question}/clone/{quiz}", [QuizQuestionController::class, "clone"])->can("clone,question,quiz")->name("admin.questions.clone");

    Route::post("/questions/{question}/answers", [QuestionAnswerController::class, "store"])->can("create," . Answer::class . ",question")->name("admin.answers.store");
    Route::patch("/answers/{answer}", [QuestionAnswerController::class, "update"])->can("update,answer")->name("admin.answers.update");
    Route::delete("/answers/{answer}", [QuestionAnswerController::class, "destroy"])->can("delete,answer")->name("admin.answers.destroy");
    Route::post("/answers/{answer}/clone/{question}", [QuestionAnswerController::class, "clone"])->can("clone,answer,question")->name("admin.answers.clone");
    Route::post("/answers/{answer}/correct", [QuestionAnswerController::class, "markAsCorrect"])->can("update,answer")->name("admin.answers.correct");
    Route::post("/answers/{answer}/invalid", [QuestionAnswerController::class, "markAsInvalid"])->can("update,answer")->name("admin.answers.invalid");

    Route::get("/schools", [SchoolsController::class, "index"])->name("admin.schools.index");
    Route::post("/schools", [SchoolsController::class, "store"])->name("admin.schools.store");
    Route::patch("/schools/{school}", [SchoolsController::class, "update"])->name("admin.schools.update");
    Route::delete("/schools/{school}", [SchoolsController::class, "destroy"])->name("admin.schools.destroy");

    Route::post("/schools/fetch", [SchoolsController::class, "fetch"])->name("admin.schools.fetch");
    Route::get("/schools/status", [SchoolsController::class, "status"])->name("admin.schools.status");

    Route::get("/users", [UserController::class, "index"])->name("admin.users.index");
    Route::get("/users/{user}/edit", [UserController::class, "edit"])->name("admin.users.edit");
    Route::patch("/users/{user}", [UserController::class, "update"])->name("admin.users.update");

    Route::middleware(["role:super_admin"])->group(function (): void {
        Route::get("/admins", [AdminController::class, "index"])->name("admin.admins.index");
        Route::get("/admins/create", [AdminController::class, "create"])->name("admin.admins.create");
        Route::get("/admins/{user}/edit", [AdminController::class, "edit"])->name("admin.admins.edit");
        Route::post("/admins", [AdminController::class, "store"])->name("admin.admins.store");
        Route::patch("/admins/{user}", [AdminController::class, "update"])->name("admin.admins.update");
        Route::delete("/admins/{user}", [AdminController::class, "destroy"])->name("admin.admins.destroy");
        Route::patch("/users/{user}/anonymize", [UserController::class, "anonymize"])->name("admin.users.anonymize");
    });
});

Route::middleware(["auth", "verified"])->group(function (): void {
    Route::post("/quizzes/{quiz}/assign", [QuizController::class, "assign"])->can("assign,quiz")->name("quizzes.assign");
    Route::post("/quizzes/{quiz}/start", [QuizController::class, "createSubmission"])->middleware(EnsureQuizIsNotAlreadyStarted::class)->can("submit,quiz")->name("quizzes.start");
    Route::get("/quizzes/{quiz}/ranking", [RankingController::class, "indexUser"])->name("quizzes.ranking");
    Route::get("/submissions/{quizSubmission}/", [QuizSubmissionController::class, "show"])->can("view,quizSubmission")->name("submissions.show");
    Route::post("/submissions/{quizSubmission}/close", [QuizSubmissionController::class, "close"])->can("close,quizSubmission")->name("submissions.close");
    Route::get("/submissions/{quizSubmission}/result", [QuizSubmissionController::class, "result"])->can("result,quizSubmission")->name("submissions.result");
    Route::patch("/answers/{answerRecord}/{answer}", [AnswerRecordController::class, "answer"])->can("answer,answerRecord,answer")->name("answers.answer");
    Route::get("/dashboard", [ContestController::class, "create"])->name("dashboard");
    Route::get("/profile", [ProfileUserController::class, "create"])->name("profile");
    Route::patch("/profile/password", [ProfileUserController::class, "update"])->name("profile.password.update");
});
