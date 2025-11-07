@extends('layout.Layout')

@section('titulo', 'Perfiles')

@section('contenido')

<div id="app"
    class="clientes"
    data-permisos='@json($permisos ?? [])'
    data-exito='@json(session("exito"))'
    data-error='@json(session("error"))'>

    <div class="modulo-encabezado">
        <div class="cont-buscador">
            <i class="fa-solid fa-magnifying-glass buscador-icono"></i>
            <input type="text" v-model="busqueda" @input="buscarPerfiles" class="input-busqueda" placeholder="Buscar perfiles...">
        </div>
        <button class="btn primary-btn" @click.prevent="modalCrear()"><i class="fa fa-plus"></i> Nuevo Perfil</button>
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
                    <button @click.prevent="modalEditar(perfil.perfil_id)" title="Editar"><i class="fa-solid fa-pen"></i></button>
                    <button @click.prevent="abrirModalEliminar(perfil.perfil_id)" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                </td>
            </tr>
            <tr v-if="!perfiles.length">
                <td colspan="5" style="text-align:center; padding:1rem;">No hay perfiles registrados.</td>
            </tr>
        </tbody>
    </table>

    <paginador-componente :links="links" @navigate="cargarPagina"></paginador-componente>

    <!-- MODAL CREAR / EDITAR -->
    <modal-componente
        v-model:mostrar="mostrarModal"
        :titulo="tipoForm === 'crear' ? 'Nuevo Perfil' : 'Editar Perfil'"
        :subtitulo="tipoForm === 'crear' ? 'Completa los datos del nuevo perfil' : 'Modifica los datos del perfil'"
        :texto-confirmacion="tipoForm === 'crear' ? 'Crear Perfil' : 'Guardar Cambios'"
        @confirmar="guardar">

        <form id="form" class="form centrado" @submit.prevent="guardar" novalidate>
            <div class="campo">
                <label class="etiqueta" for="clave">Clave</label>
                <input class="input" :class="{'input-error': errors.clave}" type="text" id="clave" v-model="formPerfil.clave">
                <span v-if="errors.clave" class="error-message">@{{ errors.clave }}</span>
            </div>

            <div class="campo">
                <label class="etiqueta" for="nombre">Nombre</label>
                <input class="input" :class="{'input-error': errors.nombre}" type="text" id="nombre" v-model="formPerfil.nombre">
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
        ¿Estás seguro de que deseas eliminar este perfil?.<strong> Esta acción no se puede deshacer.</strong>
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
            const el = document.getElementById('app');
            return {
                perfiles: [],
                permisos: JSON.parse(el.dataset.permisos || '[]'),
                links: [],
                exito: JSON.parse(el.dataset.exito || 'null'),
                error: JSON.parse(el.dataset.error || 'null'),

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

            async fetchPerfiles(url = "{{ route('perfiles.listarRest') }}") {
                try {
                    const res = await fetch(url);
                    const data = await res.json();

                    this.perfiles = Array.isArray(data.perfiles) ?
                        data.perfiles :
                        data.perfiles?.data || [];
                    this.links = data.links || data.perfiles?.links || [];
                    this.permisos = data.permisos || this.permisos;
                } catch (error) {
                    console.error(error);
                    this.mostrarAlerta('error', 'No se pudieron cargar los perfiles.');
                }
            },

            async guardar() {
                if (!this.validarFormulario()) return;

                const url =
                    this.tipoForm === 'crear' ?
                    "{{ route('perfiles.agregarRest') }}" :
                    `/perfiles/editar/${this.formPerfil.perfil_id}`;

                const method = this.tipoForm === 'crear' ? 'POST' : 'PUT';

                try {
                    const body = {
                        clave: this.formPerfil.clave,
                        nombre: this.formPerfil.nombre,
                        descripcion: this.formPerfil.descripcion,
                        status: this.formPerfil.status,
                        permisos: this.formPerfil.permisos
                    };

                    const res = await fetch(url, {
                        method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(body)
                    });

                    if (res.status === 201) {
                        this.mostrarModal = false;
                        await this.fetchPerfiles();
                        this.mostrarAlerta('exito', 'Perfil creado correctamente');
                    } else if (res.status === 204) {
                        this.mostrarModal = false;
                        await this.fetchPerfiles();
                        this.mostrarAlerta('exito', 'Perfil actualizado correctamente');
                    } else if (res.status === 422) {
                        const data = await res.json();
                        this.errors = data.errors;
                        this.mostrarAlerta('error', 'Revisa los campos del formulario.');
                    } else {
                        const data = await res.json().catch(() => ({}));
                        this.mostrarAlerta('error', data.message || 'Ocurrió un error al guardar el perfil.');
                    }
                } catch (error) {
                    console.error(error);
                    this.mostrarAlerta('error', 'Ocurrió un error al guardar el perfil.');
                }
            },

            async eliminarPerfil() {
                if (!this.perfilAEliminar) return;

                try {
                    const res = await fetch(`/perfiles/eliminar/${this.perfilAEliminar}`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    if (res.status === 204) {
                        this.mostrarModalEliminar = false;
                        await this.fetchPerfiles();
                        this.mostrarAlerta('exito', 'Perfil eliminado correctamente');
                    } else {
                        const data = await res.json().catch(() => ({}));
                        this.mostrarAlerta('error', data.message || 'No se pudo eliminar el perfil.');
                    }
                } catch (error) {
                    console.error(error);
                    this.mostrarAlerta('error', 'No se pudo eliminar el perfil.');
                }
            },

            cargarPagina(url) {
                if (url) this.fetchPerfiles(url);
            },

            async buscarPerfiles() {
                const url = `{{ route('perfiles.listarRest') }}?busqueda=${encodeURIComponent(this.busqueda)}`;
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
                    perfil_id: perfil.perfil_id,
                    clave: perfil.clave,
                    nombre: perfil.nombre,
                    descripcion: perfil.descripcion,
                    status: perfil.status ?? 'ACTIVO',
                    permisos: Array.isArray(perfil.permisos) ? [...perfil.permisos] : []
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