<!DOCTYPE html>
<html lang="pl">
<style>
    * { margin: 0; padding: 0; }
    a { text-decoration: none; }
    p { margin: 48px 0; line-height: 24px; }
    table { width: 100%; border-spacing: 0; }
    .button { background-color: #E4007D; color: white; padding: 16px 80px; border-radius: 12px; }
</style>

<div style="font-family: sans-serif; color: #3D4852; padding: 0 48px;">
    <div style="margin: 40px 0 80px 0;">
        <img src="{{ asset('logo.png') }}" alt="interns2024b Logo" height="50" width="50">
    </div>

    <div>
        <h2>Cześć, {{ $user->firstname }}!</h2>

        <p>
            Otrzymaliśmy Twoje odpowiedzi na test {{ $userQuiz->quiz->title }}. <br>
            Twój test jest obecnie sprawdzany, wyślemy powiadomienie na Twoją skrzynkę pocztową, gdy wyniki będą dostępne.
        <p>

        <p style="margin-top: 16px; ">Pozdrawiamy,<br><span style="color: #E4007D;">{{ config('app.name') }}</span></p>

        <hr style="border: 1px solid #e8ebf1">

        <table><tr><td style="padding-top: 32px; text-align: center;">
            © 2024 {{ config('app.name') }}. Wszelkie prawa zastrzeżone.
        </td></tr></table>
    </div>
</div>
</html>
