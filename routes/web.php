<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Inertia\Response;

Route::get("/", fn(): Response => inertia("Welcome"));
Route::get("/home", fn(): Response => inertia("Home"));
