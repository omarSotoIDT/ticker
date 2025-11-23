<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', 'Tickets')</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 576 512'><path fill='currentColor' d='M128 160h320v192H128V160zm400 96c0 26.5-21.5 48-48 48s-48-21.5-48-48s21.5-48 48-48s48 21.5 48 48zM96 128a32 32 0 0 0 -32 32v192a32 32 0 0 0 32 32h384a32 32 0 0 0 32-32V160a32 32 0 0 0 -32-32H96zM48 96h480c26.5 0 48 21.5 48 48v224c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V144c0-26.5 21.5-48 48-48z'/></svg>" type="image/svg+xml">
    
    {{-- Fuentes --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">

    {{-- VUE --}}
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="{{ asset('componentes/alerta.js') }}?v={{ config('app.version') }}"></script>
    <script src="{{ asset('constantes.js') }}?v={{ config('app.version') }}"></script>
    <script src="{{ asset('componentes/loader.js') }}?v={{ config('app.version') }}"></script>
    <script src="{{ asset('componentes/modal.js') }}?v={{ config('app.version') }}"></script>
    <script src="{{ asset('componentes/paginador.js') }}?v={{ config('app.version') }}"></script>
    
    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

    {{-- Estilos--}}
    <link rel="stylesheet" href="{{ asset('css/normalizacion.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}?v={{ config('app.version') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="layout">
    <!-- Sidebar -->
    <aside>
         <div class="titulo">
            <div class="logo logo-menu">
                <i class="fa-solid fa-ticket icono-blanco"></i>
            </div>
            <span>Sistema de Tickets</span>
        </div>
        <p class="titulo-enlaces">Menú Principal</p>
        <a href="/dashboard"><i class="fa fa-pie-chart"></i>Dashboard</a>
        <a href="{{ route('tickets.gestor') }}" class="{{ Route::currentRouteName() == 'tickets.gestor' ? 'active' : '' }}"><i class="fa-solid fa-ticket"></i>Tickets</a>
        <a href="{{ route('usuarios.gestor') }}" class="{{ Route::currentRouteName() == 'usuarios.gestor' ? 'active' : '' }}"><i class="fa-solid fa-users"></i>Usuarios</a>
        <a href="{{ route('perfiles.gestor') }}" class="{{ Route::currentRouteName() == 'perfiles.gestor' ? 'active' : '' }}"><i class="fa-solid fa-shield-halved"></i>Perfiles</a>
        <a href="{{ route('clientes.gestor') }}" class="{{ Route::currentRouteName() == 'clientes.gestor' ? 'active' : '' }}"><i class="fa-solid fa-file-lines"></i>Clientes</a>
        <a href="{{ route('proyectos.gestor') }}" class="{{ Route::currentRouteName() == 'proyectos.gestor' ? 'active' : '' }}"><i class="fa-solid fa-clipboard-list"></i>Proyectos</a>
        <a href="{{ route('reportes.index') }}" class="{{ Route::currentRouteName() == 'reportes.index' ? 'active' : '' }}"><i class="fa-solid fa-chart-simple"></i>Reportes</a>

        <div class="logout">
            <p class="logout-usuario">{{ Auth::user()->usuario }}</p>
            <p class="logout-email">{{ Auth::user()->email }}</p>
            <form action="/logout" method="post">
                @csrf
                <button type="submit"><i class="fa-solid fa-right-from-bracket"></i>Cerrar Sesión</button>
            </form>
        </div>
    </aside>

    <!-- Barra superior -->
    <header>
        <h1><i class="fa fa-columns"></i>   @yield('titulo', 'Tickets')</h1>
    </header>

    <!-- Contenido principal -->
    <main>
        @yield('contenido')
    </main>

</body>

</html>