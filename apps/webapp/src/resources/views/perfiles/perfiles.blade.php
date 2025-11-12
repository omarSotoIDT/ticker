@extends('layout.Layout')

@section('titulo', 'Perfiles')

@section('contenido')

<div id="app" 
    class="clientes"
    data-perfiles='@json($perfilesConPermisos)'
    data-permisos='@json($permisos)'
    data-exito='@json(session("exito"))'
    data-error='@json(session("error"))'>
    
    <loader-global :visible="loading"></loader-global>

    <div class="modulo-encabezado">
        <form method="GET" action="{{ route('perfiles.index') }}" class="cont-buscador" @submit="loading = true">
            <i class="fa-solid fa-magnifying-glass buscador-icono"></i>
            <input type="text" name="busqueda" class="inputBusqueda" placeholder="Buscar perfiles..." value="{{ $busqueda ?? '' }}">
        </form>
        <button class="btn action-btn" @click.prevent="modalCrear" :disabled="loading"><i class="fa fa-plus"></i> Nuevo Perfil</button>
    </div>

    <table class="tabla">
        <thead>
            <tr>
                <th>Clave</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Permisos</th>
                <th class="acciones">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="perfil in perfiles" :key="perfil.perfil_id">
                <td>@{{ perfil.clave }}</td>
                <td>@{{ perfil.nombre }}</td>
                <td>@{{ perfil.descripcion }}</td>
                <td>@{{ perfil.permisos.length }} permisos</td>
                <td class="acciones">
                    <button @click.prevent="modalEditar(perfil.perfil_id)" title="Editar">
                        <i class="fa-solid fa-pen"></i>
                    </button>
                    <button @click.prevent="abrirModalEliminar(perfil.perfil_id)" title="Eliminar">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
            </tr>
            <tr v-if="!perfiles.length">
                <td colspan="5" style="text-align:center; padding:1rem;">No hay perfiles registrados.</td>
            </tr>
        </tbody>
    </table>

    <paginador-componente :links="links" @navigate="fetchPerfiles"></paginador-componente>

    <!-- MODAL CREAR / EDITAR -->
    <modal-componente
        v-model:mostrar="mostrarModal"
        :titulo="tituloModalPrincipal"
        :subtitulo="subtituloModalPrincipal"
        :texto-confirmacion="textoConfirmacionPrincipal"
        @confirmar="guardar">

        <form class="form centrado" @submit.prevent="guardar" novalidate>
            <div class="campo">
                <label class="etiqueta" for="clave">Clave</label>
                <input class="input" :class="{'input-error': errors.clave}" id="clave" v-model="formPerfil.clave">
                <span v-if="errors.clave" class="error-message">@{{ errors.clave }}</span>
            </div>

            <div class="campo">
                <label class="etiqueta" for="nombre">Nombre</label>
                <input class="input" :class="{'input-error': errors.nombre}" id="nombre" v-model="formPerfil.nombre">
                <span v-if="errors.nombre" class="error-message">@{{ errors.nombre }}</span>
            </div>

            <div class="campo">
                <label class="etiqueta" for="descripcion">Descripción</label>
                <textarea class="input" :class="{'input-error': errors.descripcion}" id="descripcion" v-model="formPerfil.descripcion" rows="3"></textarea>
                <span v-if="errors.descripcion" class="error-message">@{{ errors.descripcion }}</span>
            </div>

            <div class="campo" v-if="tipoForm === 'editar'">
                <label class="etiqueta" for="status">Status</label>
                <select class="input" v-model="formPerfil.status">
                    <option value="ACTIVO">ACTIVO</option>
                    <option value="ELIMINADO">ELIMINADO</option>
                </select>
            </div>

            <div class="campo">
                <label class="etiqueta">Permisos</label>
                <div class="contenedor-checklist">
                    <div v-if="permisos && permisos.length">
                        <div v-for="permiso in permisos" :key="permiso.permiso_id" class="item-contenedor">
                            <input
                                type="checkbox"
                                :id="'permiso-' + permiso.permiso_id"
                                :value="permiso.permiso_id"
                                v-model="formPerfil.permisos">
                            <label :for="'permiso-' + permiso.permiso_id">
                                <span class="titulo-item">@{{ permiso.titulo }}</span>
                                <span class="descripcion-item">@{{ permiso.descripcion }}</span>
                            </label>
                        </div>
                    </div>
                    <p v-else class="contenido-vacio">No hay permisos registrados.</p>
                </div>
            </div>
        </form>
    </modal-componente>

    <modal-componente
        v-model:mostrar="mostrarModalEliminar"
        titulo="Confirmar Eliminación"
        :texto-confirmacion="textoConfirmacionPrincipal"        
        @confirmar="ejecutarEliminacion">
        ¿Estás seguro de que deseas eliminar este perfil?.<strong> Esta acción no se puede deshacer.</strong>
    </modal-componente>

    <alerta-componente
        v-model:mostrar="alerta.mostrar"
        :tipo="alerta.tipo"
        :titulo="alerta.titulo"
        :mensaje="alerta.mensaje">
    </alerta-componente>
</div>

<script>
    const app = Vue.createApp({
        data() {
            return {
                loading: false,
                perfiles: [],
                permisos: {{Js::from($permisos ?? [])}},
                links: [],
                exito: {{Js::from(session('exito'))}},
                error: {{Js::from(session('error'))}},
                mostrarModal: false,
                mostrarModalEliminar: false,
                perfilAEliminar: null,
                tipoForm: 'crear',
                busqueda: '',
                errors: {},
                formPerfil: {
                    clave: '',
                    nombre: '',
                    descripcion: '',
                    status: 'ACTIVO',
                    permisos: [],
                    perfil_id: null
                },
                alerta: {
                    mostrar: false,
                    tipo: '',
                    titulo: '',
                    mensaje: ''
                }
            };
        },
        computed: {
            tituloModalPrincipal() {
                if (this.tipoForm === 'crear') {
                    return 'Nuevo Perfil';
                } else {
                    return 'Editar Perfil';
                }
            },
            subtituloModalPrincipal() {
                if (this.tipoForm === 'crear') {
                    return 'Completa los datos del nuevo perfil';
                } else {
                    return 'Modifica los datos del perfil';
                }
            },
            formAction() {
                if (this.tipoForm === 'crear') {
                    return this.routeGuardar;
                } else {
                    return `${this.routeActualizarBase}/${this.formPerfil.perfil_id}`;
                }
            },
            textoConfirmacionPrincipal() {
                if (this.loading) {
                    return 'Procesando...';
                }
                if (this.tipoForm === 'crear') {
                    return 'Crear Perfil';
                }
                if (this.tipoForm === 'eliminar') {
                    return 'Sí, Eliminar';
                }
                else {
                return 'Guardar Cambios';
                }
            },
        },
        methods: {
            mostrarAlerta(tipo, titulo, mensaje) {
                this.alerta.tipo = tipo;
                this.alerta.titulo = titulo;
                this.alerta.mensaje = mensaje;
                this.alerta.mostrar = true;
            },
            validateForm() {
                this.errors = {};
                if (!this.formPerfil.clave) this.errors.clave = 'El campo Clave no debe ir vacío.';
                if (!this.formPerfil.nombre) this.errors.nombre = 'El campo Nombre no debe ir vacío.';
                if (!this.formPerfil.descripcion) this.errors.descripcion = 'El campo Descripción no debe ir vacío.';
                
                if (Object.keys(this.errors).length > 0) {
                    this.mostrarAlerta('error', 'Campos incompletos', 'Por favor, revisa los campos marcados en rojo.');
                }

            // Colocamos el parámetro url = null para traer todos los perfiles, hacer búsquedas y cambiar de página sin duplicar código
            fetchPerfiles(url = null) {
                const requestUrl = url || `{{ route('perfiles.listarRest') }}${this.busqueda ? '?busqueda=' + encodeURIComponent(this.busqueda) : ''}`;

                fetch(requestUrl)
                    .then(res => res.json())
                    .then(data => {
                        this.perfiles = data.perfiles?.data || data.perfiles || [];
                        this.links = data.links || data.perfiles?.links || [];
                        this.permisos = data.permisos || this.permisos;
                    })
                    .catch(() => {
                        this.mostrarAlerta('error', 'No se pudieron cargar los perfiles.');
                    });
            },

            guardar() {
                if (this.validateForm()) {
                    this.loading = true; 
                    this.$nextTick(() => {
                        document.getElementById('form').submit();
                    });
                }
            },
            async modalCrear() {
                this.loading = true;
                await this.$nextTick();
                this.errors = {}; 
                this.tipoForm = 'crear';
                this.formPerfil = {
                    clave: '',
                    nombre: '',
                    descripcion: '',
                    status: 'ACTIVO',
                    permisos: [],
                    perfil_id: null
                };
                this.errors = {};
                this.mostrarModal = true;
                this.loading = false;
            },
            async modalEditar(perfilId) {
                this.loading = true;
                await this.$nextTick();
                this.errors = {}; 
                const perfil = this.perfiles.find(p => p.perfil_id === perfilId);
                if (!perfil) {
                    this.loading = false;
                    return;
                }
                
                this.tipoForm = 'editar';
                this.formPerfil = {
                    ...perfil,
                    permisos: [...perfil.permisos]
                };
                this.errors = {};
                this.mostrarModal = true;
                this.loading = false;
            },
            routeEliminar(perfilId) {
                return `${this.routeEliminarBase}/eliminar/${perfilId}`;
            },
            
            async abrirModalEliminar(perfilId) {
                this.loading = true;
                await this.$nextTick();
                this.perfilAEliminar = perfilId; 
                this.mostrarModalEliminar = true; 
                this.loading = false;
            },

            ejecutarEliminacion() {
                if (this.perfilAEliminar) {
                    this.loading = true;
                    this.$nextTick(() => {
                        const formId = `form-eliminar-${this.perfilAEliminar}`;
                        const form = document.getElementById(formId);
                        if (form) {
                            form.submit();
                        }
                        this.loading = false;
                        this.mostrarModalEliminar = false; 
                    });
                } else {
                    this.mostrarModalEliminar = false;
                }
            }

            validarFormulario() {
                this.errors = {};
                if (!this.formPerfil.clave) this.errors.clave = 'La clave es obligatoria';
                if (!this.formPerfil.nombre) this.errors.nombre = 'El nombre es obligatorio';
                if (!this.formPerfil.descripcion) this.errors.descripcion = 'La descripción es obligatoria';
                return Object.keys(this.errors).length === 0;
            }
        },

        mounted() {
            this.fetchPerfiles();
            if (this.exito) this.mostrarAlerta('exito', this.exito);
            if (this.error) this.mostrarAlerta('error', this.error);
        }
    });

    app.component('modal-componente', modal);
    app.component('alerta-componente', alerta);
    app.component('loader-global', loader);
    app.component('paginador-componente', paginador);
    app.mount('#app');
</script>

@endsection