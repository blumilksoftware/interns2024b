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
Route::get("/email/verify/{id}/{hash}", [EmailVerifyController::class, "verify"])->name("verification.verify");
Route::get("/auth/forgot-password", [PasswordResetLinkController::class, "create"])->middleware("guest")->name("password.request");
Route::post("/auth/forgot-password", [PasswordResetLinkController::class, "store"])->middleware("guest")->name("password.email");
