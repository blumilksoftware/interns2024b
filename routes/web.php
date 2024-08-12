<?php

declare(strict_types=1);

use App\Http\Controllers\AuthenticateSessionController;
use App\Http\Controllers\ContestController;
use App\Http\Controllers\RegisterUserController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Inertia\Response;

Route::get("/", [ContestController::class, "index"])->name("home");
Route::get("/auth/register", [RegisterUserController::class, "create"])->name("register");
Route::post("/auth/register", [RegisterUserController::class, "store"])->name("register");
Route::get("/auth/login", [AuthenticateSessionController::class, "create"])->name("login");
Route::post("/auth/login", [AuthenticateSessionController::class, "authenticate"])->name("login");
Route::get("/email/verify", fn(): Response => inertia("Auth/Verify-Email"))->middleware("auth")->name("verification.notice");
Route::get("/email/verify/{id}/{hash}", function (EmailVerificationRequest $request) {
    $request->fulfill();

    return Redirect::route("home");
})->middleware(["auth", "signed"])->name("verification.verify");
