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
        <h2>Cześć, {{ $user->name }}!</h2>

        <p>Otrzymaliśmy prośbę o wysłanie linku aktywacyjnego do Twojego konta. Kliknij poniższy przycisk, aby zweryfikować swój adres e-mail.</p>
        
        <table style="margin: 24px auto;"><tr><td style="text-align: center;">
            <b><a class="button" href="{{ $url }}">Zweryfikuj e-mail</a></b>
        </td></tr></table>

        <p>Jeśli prośba nie została zainicjonowana przez Ciebie, możesz zignorować tę wiadomość.</p>

        <p style="margin-top: 16px; ">Pozdrawiamy,<br><span style="color: #E4007D;">{{ config('app.name') }}</span></p>

        <hr style="border: 1px solid #e8ebf1">
        
        <table><tr><td style="padding-top: 32px; text-align: center;">
            © 2024 {{ config('app.name') }}. Wszelkie prawa zastrzeżone.
        </td></tr></table>
    </div>
</div>
</html>
