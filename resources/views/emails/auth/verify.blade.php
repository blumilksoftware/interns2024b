<!DOCTYPE html>
<html lang="pl">
<style>
    * { margin: 0; padding: 0; }
    a { text-decoration: none; }
    p { padding: 14px 0; }
    table { width: 100%; border-spacing: 0; margin-top: 8px; }
    .button { background-color: #262C89; color: white; padding: 12px 20px; border-radius: 16px; }
    .footer { color: #6b7280; margin-top: 16px; }
</style>

<div style="font-family: sans-serif; color: #3D4852;">
    <table><tr><td style="vertical-align: middle; width: 50px;">
        <img style="margin: 5px;" src="{{ asset('logo.png') }}" alt="interns2024b Logo" height="40" width="40"></td><td>
        <h2>interns2024b</h2>
    </td></tr></table>

    <div style="padding: 16px 48px;">
        <h3>Cześć {{ $user->name }},</h3>

        <p>Aby zweryfikować swoje konto, kliknij poniższy przycisk:</p>
        
        <table style="margin: 24px auto;"><tr><td style="text-align: center;">
            <b><a class="button" href="{{ $url }}">Zweryfikuj e-mail</a></b>
        </td></tr></table>

        <span style="color: #B32F2F; font-size: .9rem;">
            Jeśli to nie Ty wysłałeś/aś tę prośbę, po prostu zignoruj tę wiadomość.
        </span>

        <p class="footer">{{ config('app.name') }}</p>

        <hr style="margin: 16px 0; color: #6b7280">

        <p>
            <span style="color: #6b7280;">Alternatywnie, użyj link:</span>
            <a href="{{ $url }}">{{ $url }}</a>
        </p>
    </div>
</div>
</html>
