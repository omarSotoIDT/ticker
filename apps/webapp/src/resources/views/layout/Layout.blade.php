<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', 'Tickets')</title>
  {{-- Fuentes --}}
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">

  {{-- VUE --}}
  <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
  <script src="{{ asset('componentes/alerta.js') }}?v={{ config('app.version') }}"></script>
  <script src="{{ asset('constantes.js') }}?v={{ config('app.version') }}"></script>
  <script src="{{ asset('componentes/loader.js') }}?v={{ config('app.version') }}"></script>
  <script src="{{ asset('componentes/modal.js') }}?v={{ config('app.version') }}"></script>

  {{-- Estilos--}}
  <link rel="stylesheet" href="{{ asset('css/normalizacion.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/estilos.css') }}?v={{ config('app.version') }}" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <body class="layout">
        <!-- Sidebar -->
        <aside>
            <div class="titulo">
                <p>Sistema de Tickets</p>
            </div>
            <p class="titulo-enlaces">Menú Principal</p>
            <a href="/dashboard">Dashboard</a>
            <a href="{{ route('tickets.gestor') }}" class="{{ Route::currentRouteName() == 'tickets.gestor' ? 'active' : '' }}">Tickets</a>
            <a href="{{ route('usuarios.gestor') }}" class="{{ Route::currentRouteName() == 'usuarios.gestor' ? 'active' : '' }}">Usuarios</a>
            <a href="">Perfiles</a>
            <a href="">Clientes</a>
            <a 
                href="{{ route('proyectos.gestor') }}"
                class="{{ Str::startsWith(Route::currentRouteName(), 'proyectos.') ? 'active' : '' }}"
            >Proyectos</a>
            <a href="">Reportes</a>

            <div class="logout">
                <p>{{ Auth::user()->usuario }}</p>
                <P>{{ Auth::user()->email }}</P>
                <form action="/logout" method="post">
                    @csrf
                    <button type="submit">Cerrar Sesión</button>
                </form>
            </div>
        </aside>

    <!-- Contenido principal -->
    <main>
        @yield('contenido')
    </main>

</body>
</html>
