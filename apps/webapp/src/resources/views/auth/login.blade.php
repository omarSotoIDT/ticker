<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="{{ asset('css/normalizacion.css') }}?v={{ config('app.version') }}" />
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}?v={{ config('app.version') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
            <button class="primary-btn" type="submit">Iniciar Sesión</button>
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