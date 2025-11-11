@extends('layout.Layout')

@section('titulo', 'Perfiles')

@section('contenido')

<div id="app" class="clientes">
    <div class="modulo-encabezado">
        <div class="cont-buscador">
            <i class="fa-solid fa-magnifying-glass buscador-icono"></i>
            <input type="text" v-model="busqueda" @input="fetchPerfiles()" class="input-busqueda" placeholder="Buscar perfiles...">
        </div>
        <button class="btn primary-btn" @click.prevent="modalCrear()">
            <i class="fa fa-plus"></i> Nuevo Perfil
        </button>
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
        :titulo="tipoForm === 'crear' ? 'Nuevo Perfil' : 'Editar Perfil'"
        :subtitulo="tipoForm === 'crear' ? 'Completa los datos del nuevo perfil' : 'Modifica los datos del perfil'"
        :texto-confirmacion="tipoForm === 'crear' ? 'Crear Perfil' : 'Guardar Cambios'"
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
        texto-confirmacion="Sí, Eliminar"
        @confirmar="eliminarPerfil">
        ¿Estás seguro de que deseas eliminar este perfil? <strong>Esta acción no se puede deshacer.</strong>
    </modal-componente>

    <alerta-componente
        :mostrar="alerta.mostrar"
        :tipo="alerta.tipo"
        :titulo="alerta.titulo"
        :mensaje="alerta.mensaje">
    </alerta-componente>
</div>

<script>
    const app = Vue.createApp({
        data() {
            return {
                perfiles: [],
                permisos: @json($permisos ?? []),
                links: [],
                exito: @json(session('exito')),
                error: @json(session('error')),
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

        methods: {
            mostrarAlerta(tipo, mensaje) {
                this.alerta = {
                    mostrar: true,
                    tipo,
                    titulo: tipo === 'exito' ? 'Éxito' : 'Error',
                    mensaje
                };
                setTimeout(() => (this.alerta.mostrar = false), 3000);
            },

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
                if (!this.validarFormulario()) return;

                const url = this.tipoForm === 'crear' ?
                    "{{ route('perfiles.agregarRest') }}" :
                    `/perfiles/editar/${this.formPerfil.perfil_id}`;
                const method = this.tipoForm === 'crear' ? 'POST' : 'PUT';
                const body = JSON.stringify(this.formPerfil);

                fetch(url, {
                        method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body
                    })
                    .then(res => {
                        switch (res.status) {
                            case 201:
                                this.mostrarModal = false;
                                this.fetchPerfiles();
                                this.mostrarAlerta('exito', 'Perfil creado correctamente');
                                break;
                            case 204:
                                this.mostrarModal = false;
                                this.fetchPerfiles();
                                this.mostrarAlerta('exito', 'Perfil actualizado correctamente');
                                break;
                            case 422:
                                return res.json().then(data => {
                                    this.errors = data.errors;
                                    this.mostrarAlerta('error', 'Revisa los campos del formulario.');
                                });
                            default:
                                return res.json().then(data => {
                                    this.mostrarAlerta('error', data.mensaje || 'Ocurrió un error al guardar.');
                                });
                        }
                    })
                    .catch(() => this.mostrarAlerta('error', 'Error de conexión al guardar.'));
            },

            eliminarPerfil() {
                if (!this.perfilAEliminar) return;

                fetch(`/perfiles/eliminar/${this.perfilAEliminar}`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(res => {
                        switch (res.status) {
                            case 204:
                                this.mostrarModalEliminar = false;
                                this.fetchPerfiles();
                                this.mostrarAlerta('exito', 'Perfil eliminado correctamente');
                                break;
                            default:
                                return res.json().then(data => {
                                    this.mostrarAlerta('error', data.message || 'No se pudo eliminar el perfil.');
                                });
                        }
                    })
                    .catch(() => this.mostrarAlerta('error', 'Error de conexión al eliminar.'));
            },

            cargarPagina(url) {
                if (!url) return;
                this.fetchPerfiles(url);
            },

            modalCrear() {
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
            },

            modalEditar(id) {
                const perfil = this.perfiles.find(p => p.perfil_id === id);
                if (!perfil) return;

                this.tipoForm = 'editar';
                this.formPerfil = {
                    ...perfil,
                    permisos: [...perfil.permisos]
                };
                this.errors = {};
                this.mostrarModal = true;
            },

            abrirModalEliminar(id) {
                this.perfilAEliminar = id;
                this.mostrarModalEliminar = true;
            },

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
    app.component('paginador-componente', paginador);
    app.mount('#app');
</script>

@endsection