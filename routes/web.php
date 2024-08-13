<?php

declare(strict_types=1);

use App\Http\Controllers\AuthenticateSessionController;
use App\Http\Controllers\ContestController;
use App\Http\Controllers\EmailVerifyController;
use App\Http\Controllers\PasswordResetLinkController;
use App\Http\Controllers\RegisterUserController;
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
