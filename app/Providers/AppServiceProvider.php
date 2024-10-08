<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        JsonResource::withoutWrapping();
        VerifyEmail::toMailUsing(
            fn(object $notifiable, string $url): MailMessage => (new MailMessage())
                ->subject("Weryfikacja adresu e-mail")
                ->view("emails.auth.verify", [
                    "user" => $notifiable,
                    "url" => $url,
                ]),
        );
    }
}
