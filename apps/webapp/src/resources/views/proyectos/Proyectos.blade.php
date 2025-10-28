@extends('layout.Layout')

@section('titulo', 'Gestor de Proyectos')

@section('contenido')
<div id="app">
    {{-- ======= ENCABEZADO ======= --}}
    <div class="modulo-encabezado">
        <form id="formBuscar" @submit.prevent="buscar" class="cont-buscador">
            <i class="fa-solid fa-magnifying-glass buscador-icono"></i>
            <input type="text" id="busqueda" name="busqueda" v-model="busqueda" class="inputBusqueda" placeholder="Buscar proyectos...">
        </form>
        <button class="btn-primary" @click="modalCrear"> + Nuevo proyecto </button>
    </div>

    {{-- ======= TABLA DE PROYECTOS ======= --}}
    <table class="tabla">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Cliente</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th class="acciones">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="proyecto in proyectos" :key="proyecto.proyecto_id">
                <td>@{{ proyecto.nombre }}</td>

                <td>@{{ proyecto.cliente?.nombre }}</td>

                <td>@{{ proyecto.descripcion }}</td>
                <td>
                    <span class="badge" :class="proyecto.status === 'ACTIVO' ? 'badge-active' : 'badge-inactive'">
                        @{{ proyecto.status }}
                    </span>
                </td>
                <td class="acciones">
                    <button @click.prevent="mostrarHistorial(proyecto.proyecto_id)" title="Historial">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </button>
                    <button @click.prevent="modalToggle(proyecto.proyecto_id, 'activar')" title="Activar / Desactivar">
                        <i class="fa-solid fa-power-off"></i>
                    </button>
                    <button @click.prevent="modalEditar(proyecto.proyecto_id)" title="Editar">
                        <i class="fa-solid fa-pen"></i>
                    </button>
                    <button @click.prevent="modalToggle(proyecto.proyecto_id, 'eliminar')" title="Eliminar">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
            </tr>
            <tr v-if="proyectos.length === 0">
                <td colspan="6" class="text-center text-gray-500">No hay proyectos registrados.</td>
            </tr>
        </tbody>
    </table>

    {{-- ======= MODALES ======= --}}
    <modal-componente
        v-model:mostrar="mostrarModal"
        :titulo="tipoForm === 'crear' ? 'Nuevo proyecto' : 'Editar proyecto'"
        :subtitulo="tipoForm === 'crear' ? 'Completa los datos del nuevo proyecto' : 'Modifica los datos del proyecto'"
        :texto-confirmacion="tipoForm === 'crear' ? 'Guardar proyecto' : 'Guardar Cambios'"
        @confirmar="tipoForm === 'crear' ? crearProyecto() : actualizarProyecto()">

        <form id="formproyecto" class="form centrado" @submit.prevent>
            <div class="campo">
                <label class="etiqueta" for="nombre">Nombre</label>
                <input class="input" type="text" id="nombre" v-model="formproyecto.nombre">
                <span class="error" v-if="erroresModal.nombre">@{{ erroresModal.nombre[0] }}</span>
            </div>

            <div class="campo">
                <label class="etiqueta" for="descripcion">Descripción</label>
                <textarea class="input" id="descripcion" v-model="formproyecto.descripcion"></textarea>
                <span class="error" v-if="erroresModal.descripcion">@{{ erroresModal.descripcion[0] }}</span>
            </div>

            <div class="campo">
                <label class="etiqueta" for="cliente">Cliente</label>
                <select class="input" v-model="formproyecto.cliente_id" id="cliente">
                    <option value="" disabled>Selecciona un cliente</option>
                    <option v-for="cliente in clientes" :key="cliente.cliente_id" :value="cliente.cliente_id">
                        @{{ cliente.nombre }}
                    </option>
                </select>
                <span class="error" v-if="erroresModal.cliente_id">@{{ erroresModal.cliente_id[0] }}</span>
            </div>

            <div class="campo">
                <label class="etiqueta">Usuarios</label>
                <div class="lista-usuarios">
                    <div v-for="usuario in usuariosDisponibles" :key="usuario.usuario_id" class="usuario-item">
                        <input
                            type="checkbox"
                            :id="'usuario-' + usuario.usuario_id"
                            :value="usuario.usuario_id"
                            v-model="formproyecto.usuarios">
                        <label :for="'usuario-' + usuario.usuario_id">
                            @{{ usuario.usuario }}
                        </label>
                    </div>
                </div>
                <span class="error" v-if="erroresModal.usuarios">@{{ erroresModal.usuarios[0] }}</span>
            </div>

        </form>
    </modal-componente>

    <script type="text/x-template" id="alert-template">
        <div v-if="mostrar" :class="['alerta', tipo === 'success' ? 'alert-success' : tipo === 'error' ? 'alert-error' : 'alert-info']" style="position:fixed; top:1rem; right:1rem; z-index:9999;">
            <div style="padding:0.75rem 1rem; border-radius:6px; box-shadow:0 2px 6px rgba(0,0,0,0.12); background:#fff; display:flex; align-items:center; gap:0.5rem;">
                <strong v-if="tipo === 'success'">OK</strong>
                <strong v-else-if="tipo === 'error'">Error</strong>
                <strong v-else>Info</strong>
                <span>@{{ mensaje }}</span>
                <button @click="close" style="margin-left:0.5rem; background:transparent; border:0; cursor:pointer;">✕</button>
            </div>
        </div>
    </script>

    <alerta-componente :mostrar="alerta.mostrar" :tipo="alerta.tipo" :mensaje="alerta.mensaje"></alerta-componente>

    <modal-componente
    v-model:mostrar="mostrarModalHistorial"
    titulo="Historial del Proyecto"
    subtitulo="Usuarios asignados y registros de actividad"
    texto-confirmacion="Cerrar"
    :mostrar-botones="false">

    <div class="modal-historial space-y-4">
        <h4>Usuarios asignados</h4>
        <ul v-if="usuariosProyecto.length > 0" class="list-disc pl-5">
            <li v-for="usuario in usuariosProyecto" :key="usuario.usuario_id">
                @{{ usuario.usuario }} (@{{ usuario.email }})
            </li>
        </ul>
        <p v-else class="text-gray-500">No hay usuarios asignados.</p>

        <h4>Logs del proyecto</h4>
        <ul v-if="logsProyecto.length > 0" class="space-y-1">
            <li v-for="log in logsProyecto" :key="log.id">
                <small class="text-gray-500 text-sm">@{{ log.fecha }} — @{{ log.usuario }}</small>
                <br>
                <small class="text-gray-500 text-sm">@{{ log.accion }}</small>
                <hr>
            </li>
        </ul>
        <p v-else class="text-gray-500">No hay registros de actividad.</p>
    </div>
</modal-componente>


    <modal-componente
        v-model:mostrar="mostrarModalStatus"
        :titulo="tipoForm === 'eliminar' ? 'Eliminar proyecto' : 'Cambiar estado del proyecto'"
        :subtitulo="tipoForm === 'eliminar'
        ? '¿Estás seguro de eliminar este proyecto?'
        : '¿Deseas activar/desactivar este proyecto?'"
        texto-confirmacion="Confirmar"
        @confirmar="cambiarEstado">

        <form id="formEstado">
            <p>
                <strong>@{{ proyectoSeleccionado?.nombre }}</strong> — Estado actual:
                <span class="badge" :class="proyectoSeleccionado?.status === 'ACTIVO' ? 'badge-active' : 'badge-inactive'">
                    @{{ proyectoSeleccionado?.status }}
                </span>
            </p>

            <div class="campo" v-if="tipoForm === 'eliminar'">
                <label class="etiqueta" for="motivo">Motivo</label>

                <textarea class="input" id="motivo_eliminacion" v-model="formEliminar.motivo_eliminacion" placeholder="Describe el motivo de eliminación..."></textarea>

                <span class="error" v-if="erroresModal.motivo">@{{ erroresModal.motivo[0] }}</span>
            </div>
        </form>
    </modal-componente>

</div>

<script>
    const {
        createApp
    } = Vue;

    const app = createApp({
        data() {

            return {

                mostrarModalStatus: false,
                mostrarModalHistorial: false,
                formEliminar: {
                    motivo_eliminacion: ''
                },
                proyectoSeleccionado: null,
                logsProyecto: [],
                usuariosProyecto: [],


                proyectos: [],
                clientes: [],
                usuariosDisponibles: [],
                busqueda: '',
                mostrarModal: false,
                tipoForm: 'crear',
                formproyecto: {
                    proyecto_id: null,
                    nombre: '',
                    descripcion: '',
                    cliente_id: null,
                    usuarios: []
                },
                erroresModal: {},
                alerta: {
                    mostrar: false,
                    tipo: 'info',
                    mensaje: ''
                }
            }
        },

        mounted() {
            this.listarProyectos();
        },

        methods: {
            async listarProyectos() {
                try {
                    const res = await fetch(`/proyectos/listado?busqueda=${encodeURIComponent(this.busqueda)}`);
                    const data = await res.json();
                    this.proyectos = data.data || [];
                } catch (err) {
                    console.error('Error al listar proyectos:', err);
                }
            },

            mostrarAlerta(tipo, mensaje, duracion = 4000) {
                this.alerta.tipo = tipo || 'info';
                this.alerta.mensaje = mensaje || '';
                this.alerta.mostrar = true;
                if (duracion > 0) setTimeout(() => { this.alerta.mostrar = false; }, duracion);
            },

            buscar() {
                this.listarProyectos();
            },

            async modalCrear() {
                this.tipoForm = 'crear';
                this.erroresModal = {};
                this.formproyecto = {
                    proyecto_id: null,
                    nombre: '',
                    descripcion: '',
                    cliente_id: null,
                    usuarios: []
                };
                await Promise.all([this.cargarClientes(), this.cargarUsuarios()]);
                this.mostrarModal = true;
            },

            async cargarClientes() {
                try {
                    const res = await fetch('/clientes/listado');
                    const data = await res.json();
                    this.clientes = data.data || [];
                } catch (err) {
                    console.error('Error al cargar clientes:', err);
                }
            },

            async cargarUsuarios() {
                try {
                    const res = await fetch('/usuarios/listarRest');
                    const data = await res.json();

                    this.usuariosDisponibles = (data || []).map(u => ({
                        usuario_id: u.usuario_id ?? u.usuarioId,
                        usuario: u.usuario,
                        email: u.email,
                        ...u
                    }));
                } catch (err) {
                    console.error('Error al cargar usuarios:', err);
                }
            },

            async crearProyecto() {
                try {
                    const res = await fetch('/proyectos/registrar', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(this.formproyecto)
                    });
                    if (res.status === 422) {
                        const data = await res.json();
                        this.erroresModal = data.errores;
                        let msg = data.mensaje || '';
                        if (!msg && data.errores) {
                            const first = Object.values(data.errores)[0];
                            msg = Array.isArray(first) ? first[0] : first;
                        }
                        this.mostrarAlerta('error', msg || 'Error de validación');
                        return;
                    }

                    if (!res.ok) throw new Error('Error al crear el proyecto');

                    this.mostrarModal = false;
                    this.listarProyectos();
                    this.erroresModal = {};
                    this.mostrarAlerta('success', 'Proyecto creado correctamente');
                } catch (err) {
                    console.error('Error al crear proyecto:', err);
                    this.mostrarAlerta('error', 'Error al crear proyecto');
                }
            },

            async actualizarProyecto() {
                console.log('Actualizando proyecto:', this.formproyecto.proyecto_id);
                console.log('URL esperada:', `/proyectos/${this.formproyecto.proyecto_id}/actualizar`);
                console.log('Objeto enviado:', this.formproyecto);

                try {
                    const res = await fetch(`/proyectos/${this.formproyecto.proyecto_id}/actualizar`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(this.formproyecto)
                    });

                    if (res.status === 422) {
                        const data = await res.json();
                        this.erroresModal = data.errores;
                        let msg = data.mensaje || '';
                        if (!msg && data.errores) {
                            const first = Object.values(data.errores)[0];
                            msg = Array.isArray(first) ? first[0] : first;
                        }
                        this.mostrarAlerta('error', msg || 'Error de validación');
                        return;
                    }

                    if (!res.ok) throw new Error('Error al actualizar el proyecto');

                    this.mostrarModal = false;
                    this.listarProyectos();
                    this.erroresModal = {};
                    this.mostrarAlerta('success', 'Proyecto actualizado correctamente');
                } catch (err) {
                    console.error('Error al actualizar proyecto:', err);
                    this.mostrarAlerta('error', 'Error al actualizar proyecto');
                }
            },

            async mostrarHistorial(proyecto_id) {
                this.proyectoSeleccionado = this.proyectos.find(p => p.proyecto_id === proyecto_id);
                if (!this.proyectoSeleccionado) return;

                try {
                    const res = await fetch(`/proyectos/${proyecto_id}/logs`);
                    const data = await res.json();

                    this.logsProyecto = data.logs || [];
                    this.usuariosProyecto = data.usuarios || [];
                    this.mostrarModalHistorial = true;
                } catch (err) {
                    console.error('Error al obtener historial:', err);
                }
            },


            modalToggle(proyecto_id, tipo) {
                this.proyectoSeleccionado = this.proyectos.find(p => p.proyecto_id === proyecto_id);
                if (!this.proyectoSeleccionado) return;
                this.tipoForm = tipo;
                this.formEliminar.motivo_eliminacion = '';
                this.mostrarModalStatus = true;
            },

            async cambiarEstado() {
                if (!this.proyectoSeleccionado) return;

                const url =
                    this.tipoForm === 'activar' ?
                    `/proyectos/${this.proyectoSeleccionado.proyecto_id}/activar` :
                    `/proyectos/${this.proyectoSeleccionado.proyecto_id}/eliminar`;
                try {
                    const res = await fetch(url, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(this.formEliminar)
                    });

                    if (res.status === 422) {
                        const data = await res.json();
                        this.erroresModal = data.errores;
                        let msg = data.mensaje || '';
                        if (!msg && data.errores) {
                            const first = Object.values(data.errores)[0];
                            msg = Array.isArray(first) ? first[0] : first;
                        }
                        this.mostrarAlerta('error', msg || 'Error de validación');
                        return;
                    }

                    if (!res.ok) throw new Error('Error al cambiar estado');

                    this.mostrarModalStatus = false;
                    this.listarProyectos();
                    const successMsg = this.tipoForm === 'eliminar' ? 'Proyecto eliminado correctamente' : 'Estado cambiado correctamente';
                    this.mostrarAlerta('success', successMsg);
                } catch (err) {
                    console.error('Error al cambiar estado:', err);
                    this.mostrarAlerta('error', 'Error al cambiar estado');
                }
            },

            async modalEditar(proyecto_id) {
                this.tipoForm = 'editar';
                this.erroresModal = {};
                this.proyectoSeleccionado = this.proyectos.find(p => p.proyecto_id === proyecto_id);

                if (!this.proyectoSeleccionado) return;

                this.formproyecto = {
                    proyecto_id: this.proyectoSeleccionado.proyecto_id,
                    nombre: this.proyectoSeleccionado.nombre,
                    descripcion: this.proyectoSeleccionado.descripcion,
                    cliente_id: this.proyectoSeleccionado.cliente_id,
                    usuarios: [] 
                };

                await Promise.all([
                    this.cargarClientes(),
                    this.cargarUsuarios(),
                    this.cargarUsuariosAsignados(proyecto_id)
                ]);

                this.mostrarModal = true;
            },

            async cargarUsuariosAsignados(proyecto_id) {
                try {
                    const res = await fetch(`/proyectos/${proyecto_id}/usuarios`);
                    const data = await res.json();
                    const usuarios = data.data || [];
                    this.formproyecto.usuarios = usuarios.map(u => u.usuario_id);
                } catch (err) {
                    console.error('Error al cargar usuarios asignados:', err);
                }
            },


        }
    });

    app.component('modal-componente', modal);
    app.component('alerta-componente', alerta);
    app.mount('#app');
</script>


@endsection