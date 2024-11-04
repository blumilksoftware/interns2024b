<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zresetuj hasło</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap">
</head>

<style>
    * {
        font-family: 'Poppins', sans-serif;
        text-decoration: none;
    }

    header {
        display: flex;
        align-items: center;
        gap: .3rem;
    }

    main {
        display: flex;
        flex-direction: column;
        align-items: center;
        row-gap: 1rem;
    }

    .box {
        padding: .8rem 1.2rem;
        border-radius: 1rem;
    }

    .button {
        background-color: #262C89;
        color: white;
        font-weight: bold;
        transition: background-color 0.2s;
        margin: 2rem;
    }

    .button:hover {
        background-color: #141647;
    }

    .alter-link {
        background-color: #e2e4ff;
        overflow: hidden;
        text-overflow: ellipsis;
        width: 100%;
    }

    .warning {
        background-color: #FFEBEB;
        color: #B32F2F;
        width: 100%;
    }

    .end-title {
        font-size: 0.875rem;
        color: #6b7280;
        margin: 2rem;
    }
</style>

<body>
    <header>
        <svg width="50" height="50" alt="{{ config('app.name') }} Logo" viewBox="0 0 24 24" fill="none"
            xmlns="http://www.w3.org/2000/svg"
        >
            <path
                d="M7.02484 14.3074C6.60832 14.3074 6.3744 13.828 6.63076 13.4997L15.3851 2.28913C15.7308 1.84642 16.4312 2.22227 16.2534 2.75511L13.6884 10.4434C13.6668 10.5082 13.715 10.575 13.7832 10.575H16.4951C16.9018 10.575 17.1384 11.0347 16.902 11.3656L9.30015 22.0091C8.96935 22.4723 8.24533 22.1111 8.41638 21.5683L10.6633 14.4375C10.6836 14.373 10.6355 14.3074 10.5679 14.3074H7.02484Z"
                fill="#262C89"
            >
            </path>
            <path fill-rule="evenodd" clip-rule="evenodd"
                d="M12.2545 17.8224V6.2803L15.3847 2.28313C15.731 1.84101 16.4307 2.21751 16.2525 2.75005L13.6885 10.4136C13.6668 10.4784 13.715 10.5453 13.7834 10.5453H16.4935C16.9005 10.5453 17.137 11.0056 16.9 11.3365L12.2545 17.8224Z"
                fill="#6566C2"
            >
            </path>
        </svg>
        <h1>{{ config('app.name') }}</h1>
    </header>
    <h1>Cześć {{ $user->name }},</h1>

    <p>Otrzymaliśmy prośbę o zresetowanie hasła dla Twojego konta.</p>

    <p>Aby zresetować swoje hasło, kliknij poniższy przycisk:</p>
    <main class="box">

        <a class="box button" href="{{ $url }}">Zresetuj hasło</a>

        <div class="box alter-link">
            Alternatywnie, użyj link: <a href="{{ $url }}">{{ $url }}</a>
        </div>

        <div class="end-title">{{ config('app.name') }}</div>
    </main>
</body>
</html>
