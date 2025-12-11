<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="{{ asset('css/normalizacion.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}?v={{ config('app.version') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 576 512'><path fill='currentColor' d='M128 160h320v192H128V160zm400 96c0 26.5-21.5 48-48 48s-48-21.5-48-48s21.5-48 48-48s48 21.5 48 48zM96 128a32 32 0 0 0 -32 32v192a32 32 0 0 0 32 32h384a32 32 0 0 0 32-32V160a32 32 0 0 0 -32-32H96zM48 96h480c26.5 0 48 21.5 48 48v224c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V144c0-26.5 21.5-48 48-48z'/></svg>" type="image/svg+xml">
</head>
<body class="login centrado">
<main class="main">
    <div class="card card-login">
        <form class="form centrado" action="/login" method="POST">
            @csrf
            <div class="logo">
                <i class="fa fa-ticket icono-blanco"></i>
            </div>
            <h1 class="titulo">Sistema de Tickets</h1>
            <div class="subtitulo">Ingresa tus credenciales para acceder</div>
            <div class="campo">
                <label class="etiqueta" for="email">Email</label>
                <input class="input" type="email" name="email" id="email" value="{{ old('email') }}" placeholder="usuario@empresa.com">
                @error('email')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
            <div class="campo">
                <label class="etiqueta" for="contrasena">Contraseña</label>
                <input class="input" type="password" name="contrasena" id="contrasena" placeholder="********">
                @error('contrasena')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
            <button class="btn btn-login" type="submit">Iniciar Sesión</button>
            @if (session('error'))
            <div class="error">
                <span>{{ session('error') }}</span>
            </div>
            @endif
        </form>
    </div>
</main>
</body>
</html>