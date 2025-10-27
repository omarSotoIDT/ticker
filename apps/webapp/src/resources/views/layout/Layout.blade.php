<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', 'Tickets')</title>
    <!-- VUE -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <!-- Estilos -->
    <link rel="stylesheet" href="{{ asset('css/normalizacion.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}?v={{ config('app.version') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="layout">
    <!-- Sidebar -->
    <aside>
        <div class="titulo">
            <p class="no-wrap"><i class="fa-solid fa-ticket"></i> Sistema de Tickets</p>
        </div>
        <p class="titulo-enlaces">Menú Principal</p>
        <a href=""><i class="fa fa-pie-chart" aria-hidden="true"></i> Dashboard</a>
        <a href=""><i class="fa-solid fa-ticket"></i> Tickets</a>
        <a href=""><i class="fa-solid fa-users" aria-hidden="true"></i> Usuarios</a>
        <a href="{{ route('perfiles.index') }}"><i class="fa-solid fa-shield-halved"></i> Perfiles</a>
        <a href=""><i class="fa-solid fa-file-lines"></i> Clientes</a>
        <a href=""><i class="fa-solid fa-clipboard-list"></i> Proyectos</a>
        <a href=""><i class="fa-solid fa-chart-simple"></i> Reportes</a>
        
        <div class="logout">
            <p>Usuario</p> 
            <p>Correo</p> 
            <form action="" method="post">
                @csrf
                <button type="submit" style="text-align: left;"><i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión</button>
            </form>
        </div>
    </aside>
    <!-- Barra superior -->
    <header>
        <h1>@yield('titulo', 'Tickets')</h1>
    </header>
    <!-- Contenido principal -->
    <main>
        @yield('contenido')
    </main>
</body>
</html>