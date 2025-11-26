@extends('layout.Layout')

@section('titulo', 'Gestor de Perfiles')

@section('contenido')

<div id="app">
    <loader-componente :visible="loading"></loader-componente>
    <div class="modulo-encabezado">
        <div class="items-busqueda">
            <div class="cont-buscador">
                <i class="fa-solid fa-magnifying-glass buscador-icono"></i>
                <input type="text" v-model="busqueda" @change="fetchPerfiles()" class="input input-busqueda" placeholder="Buscar Perfiles..."></input>
            </div>
        </div>
        <button class="btn primary-btn" @click.prevent="modalCrear()" :disabled="loading">
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
                    <div class="acciones-contenedor">
                    <button @click.prevent="modalEditar(perfil.perfil_id)" title="Editar">
                        <i class="fa-solid fa-pen"></i>
                    </button>
                    <button @click.prevent="abrirModalEliminar(perfil.perfil_id)" title="Eliminar">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                    </div>
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
        clase-modal="modal-base"
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
        titulo="Eliminar Perfil"
        subtitulo="Confirmación requerida"
        texto-confirmacion="Sí, eliminar"
        clase-modal="modal-base"
        :deshabilitar-confirmacion="loading"
        @confirmar="eliminarPerfil">

        <div class="descripcion-item-modal">
            <p>
                ¿Estás seguro de que deseas eliminar este perfil?
                <strong>Esta acción no se puede deshacer.</strong>
            </p>
        </div>

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
                loading: false,
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
            mostrarAlerta(tipo, titulo, mensaje) {
                this.alerta.tipo = tipo;
                this.alerta.titulo = titulo;
                this.alerta.mensaje = mensaje;
                this.alerta.mostrar = true;
            },

            buscar() {
                this.fetchPerfiles();
            },

            async fetchPerfiles(url = null) {
                this.loading = true;
                try {
                    let requestUrl = url;
                    if (!requestUrl) {
                        const params = this.busqueda ? '?busqueda=' + encodeURIComponent(this.busqueda) : '';
                        requestUrl = `{{ route('perfiles.listarRest') }}${params}`;
                    } else {
                        if (this.busqueda) {
                            const separator = requestUrl.includes('?') ? '&' : '?';
                            requestUrl += separator + 'busqueda=' + encodeURIComponent(this.busqueda);
                        }
                    }

                    const res = await fetch(requestUrl);
                    const data = await res.json();

                    this.perfiles = data.perfiles || [];
                    this.links = data.links || [];
                    this.permisos = data.permisos || this.permisos;
                } catch (e) {
                    this.mostrarAlerta('error', 'Error', 'No se pudieron cargar los perfiles.');
                } finally {
                    this.loading = false;
                }
            },

            guardar() {
                this.loading = true;
                if (!this.validarFormulario()) {
                    this.loading = false;
                    this.mostrarAlerta('error', 'Datos incompletos', 'Por favor, completa todos los campos requeridos.');
                    return;
                }

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
                                this.mostrarAlerta('exito', 'Éxito', 'Perfil creado correctamente');
                                break;
                            case 204:
                                this.mostrarModal = false;
                                this.fetchPerfiles();
                                this.mostrarAlerta('exito', 'Éxito', 'Perfil actualizado correctamente');
                                break;
                            case 422:
                                return res.json().then(data => {
                                    this.errors = data.errors;
                                    this.mostrarAlerta('error', 'Datos incompletos', 'Por favor, completa todos los campos requeridos.');
                                });
                            default:
                                return res.json().then(data => {
                                    this.mostrarAlerta('error', 'Error', data.mensaje || 'Ocurrió un error al guardar.');
                                });
                        }
                    })
                    .catch(() => this.mostrarAlerta('error', 'Error', 'Error de conexión al guardar.'))
                    .finally(() => {
                        this.loading = false;
                    });
            },


            async eliminarPerfil() {
                this.loading = true;
                try {
                    if (!this.perfilAEliminar) return;

                    const res = await fetch(`/perfiles/eliminar/${this.perfilAEliminar}`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    if (res.ok) {
                        this.mostrarModalEliminar = false;
                        this.fetchPerfiles();
                        this.mostrarAlerta('exito', 'Éxito', 'Perfil eliminado correctamente');
                    } else {
                        const data = await res.json().catch(() => ({}));
                        this.mostrarAlerta('error', 'Error', data.message || 'No se pudo eliminar el perfil.');
                    }
                } catch (e) {
                    this.mostrarAlerta('error', 'Error', 'Error de conexión al eliminar.');
                } finally {
                    this.loading = false;
                }
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
                    permisos: perfil.permisos || []
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
                if (!this.formPerfil.clave) this.errors.clave = 'El campo Clave es requerido';
                if (!this.formPerfil.nombre) this.errors.nombre = 'El campo Nombre es requerido';
                if (!this.formPerfil.descripcion) this.errors.descripcion = 'El campo Descripción es requerido';
                return Object.keys(this.errors).length === 0;
            }
        },

        mounted() {
            this.fetchPerfiles();
            if (this.exito) this.mostrarAlerta('exito', this.exito);
            if (this.error) this.mostrarAlerta('error', this.error);
        }
    });

    app.component('modal-componente', modal)
    app.component('alerta-componente', alerta)
    app.component('loader-componente', loader)
    app.component('paginador-componente', paginador)
    app.mount('#app');
</script>

@endsection