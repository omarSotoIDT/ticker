@extends('layout.Layout')

@section('titulo', 'Gestor de Proyectos')

@section('contenido')
<div id="app">
    <loader-global :visible="loading"></loader-global>

    <div class="modulo-encabezado">
        <div class="items-busqueda">
            <div class="cont-buscador">
                <i class="fa-solid fa-magnifying-glass buscador-icono"></i>
                <input type="text" name="proyecto" id="proyecto" class="input input-busqueda" v-model="busqueda.titulo" @change="buscar()" placeholder="Buscar Proyectos..."></input>
            </div>
        </div>
        <button class="btn primary-btn" @click.prevent="modalCrear()" :disabled="loading">+ Nuevo Proyecto</button>
    </div>
    <table class="tabla">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Cliente</th>
                <th class="columna-grande">Descripción</th>
                <th>Estado</th>
                <th class="acciones">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="proyecto in proyectos" :key="proyecto.proyecto_id">
                <td>@{{ proyecto.nombre }}</td>
                <td>@{{ proyecto.cliente?.nombre }}</td>
                <td class="columna-grande">@{{ proyecto.descripcion }}</td>
                <td>
                    <span class="badge" :class="proyecto.status === 'ACTIVO' ? 'badge-active' : 'badge-inactive'">
                        @{{ proyecto.status }}
                    </span>
                </td>

                <td class="acciones">
                    <div class="acciones-contenedor">
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
                    </div>
                </td>
            </tr>

            <tr v-if="proyectos.length === 0">
                <td colspan="6">No hay proyectos registrados.</td>
            </tr>
        </tbody>
    </table>

    {{-- ======= MODALES ======= --}}
    <modal-componente
        v-model:mostrar="mostrarModal"
        :titulo="tituloModalPrincipal"
        :subtitulo="subtituloModalPrincipal"
        :texto-confirmacion="textoConfirmacionPrincipal"
        clase-modal="modal-base"
        :deshabilitar-confirmacion="loading"
        @confirmar="accion">

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
                <div class="contenedor-checklist">
                    <div v-for="usuario in usuariosDisponibles" :key="usuario.usuario_id" class="item-contenedor">
                        <input
                            type="checkbox"
                            name="usuarios[]"
                            :id="'usuario-' + usuario.usuario_id"
                            :value="usuario.usuario_id"
                            v-model="formproyecto.usuarios">
                        <label :for="'usuario-' + usuario.usuario_id">
                            <span class="titulo-item">@{{ usuario.usuario }}</span>
                            <span class="descripcion-item">@{{ usuario.email }}</span>
                        </label>
                    </div>
                </div>
                <span class="error-message" v-if="erroresModal.usuarios">@{{ erroresModal.usuarios[0] }}</span>
            </div>

        </form>
    </modal-componente>

    <alerta-componente v-model:mostrar="alerta.mostrar" :tipo="alerta.tipo" :titulo="alerta.titulo" :mensaje="alerta.mensaje"></alerta-componente>

    <modal-componente
        v-model:mostrar="mostrarModalHistorial"
        titulo="Historial del Proyecto"
        subtitulo="Usuarios asignados y registros de actividad"
        texto-confirmacion="Cerrar"
        clase-modal="modal-grande modal-vista-detalle"
        :mostrar-botones="false">

        <div class="modal-historial">
            <h4>Usuarios asignados</h4>
            <ul v-if="usuariosProyecto.length > 0" class="">
                <li v-for="usuario in usuariosProyecto" :key="usuario.usuario_id">
                    @{{ usuario.usuario }} (@{{ usuario.email }})
                </li>
            </ul>
            <p v-else class="">No hay usuarios asignados.</p>

            <h4>Logs del proyecto</h4>
            <div class="contenedor-scroll-modal">
                <ul v-if="logsProyecto.length > 0" class="">
                    <li v-for="log in logsProyecto" :key="log.id">
                        <small>@{{ log.fecha }} — @{{ log.usuario }}</small>
                        <br>
                        <small>@{{ log.accion }}</small>
                        <hr>
                    </li>
                </ul>
                <p v-else class="">No hay registros de actividad.</p>
            </div>

        </div>
    </modal-componente>

    <modal-componente
        v-model:mostrar="mostrarModalStatus"
        :titulo="tituloModalStatus"
        :subtitulo="subtituloModalStatus"
        texto-confirmacion="Confirmar"
        :texto-confirmacion="textoConfirmacionPrincipal"
        clase-modal="modal-base"
        :deshabilitar-confirmacion="loading"
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
const app = Vue.createApp({
        data() {
            return {
                loading: false, 
                mostrarModalStatus: false,
                mostrarModalHistorial: false,
                formEliminar: {
                    motivo_eliminacion: ''
                },
                proyectoSeleccionado: null,
                logsProyecto: [],
                usuariosProyecto: [],
                token: '{{ csrf_token() }}',
                proyectos: [],
                clientes: [],
                usuariosDisponibles: [],
                busqueda: {
                    titulo: ''
                },
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
                    tipo: '',
                    titulo: '',
                    mensaje: ''
                },
            }
        },

        computed: {
            tituloModalPrincipal() {
                if (this.tipoForm === 'crear') {
                    return 'Nuevo proyecto';
                } else {
                    return 'Editar proyecto';
                }
            },
            subtituloModalPrincipal() {
                if (this.tipoForm === 'crear') {
                    return 'Completa los datos del nuevo proyecto';
                } else {
                    return 'Modifica los datos del proyecto';
                }
            },
            tituloModalStatus() {
                if (this.tipoForm === 'eliminar') {
                    return 'Eliminar proyecto';
                } else {
                    return 'Cambiar estado del proyecto';
                }
            },
            textoConfirmacionPrincipal() {
                if (this.loading) {
                    return 'Procesando...';
                }
                if (this.tipoForm === 'crear') {
                    return 'Guardar proyecto';
                } else {
                    return 'Guardar Cambios';
                }
            },
            subtituloModalStatus() {
                if (this.tipoForm === 'eliminar') {
                    return '¿Estás seguro de eliminar este proyecto?';
                } else {
                    return '¿Deseas activar/desactivar este proyecto?';
                }
            },
        },
        mounted() {
            this.listarProyectos();
        },
        methods: {
            
            async listarProyectos() {
                this.loading = true;
                try {
                    const res = await fetch(`/proyectos/listado?busqueda=${encodeURIComponent(this.busqueda.titulo)}`);
                    const data = await res.json();
                    this.proyectos = data.data || [];
                } catch (err) {
                    this.mostrarAlerta('error', 'error', 'Error al listar proyectos:');
                } finally {
                    this.loading = false;
                }
            },

            mostrarAlerta(tipo, titulo, mensaje) {
                this.alerta.tipo = tipo;
                this.alerta.titulo = titulo;
                this.alerta.mensaje = mensaje;
                this.alerta.mostrar = true;
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
                this.loading = true;
                try {
                    await Promise.all([this.cargarClientes(), this.cargarUsuarios()]);
                    this.mostrarModal = true;
                } finally {
                    this.loading = false;
                }
            },

            async cargarClientes() {
                try {
                    const res = await fetch('/clientes/listado');
                    const data = await res.json();
                    this.clientes = data.data || [];
                } catch (err) {
                    this.mostrarAlerta('error', 'Error','Error al cargar clientes:');
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
                    this.mostrarAlerta('error', 'Error','Error al cargar usuarios:');
                }
            },

            async crearProyecto() {
                this.loading = true;
                try {
                    const res = await fetch('/proyectos', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.token
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
                        this.mostrarAlerta('error', 'Error', msg || 'Error de validación');
                        return;
                    }

                    if (!res.ok) throw new Error('Error al crear el proyecto');

                    this.mostrarModal = false;
                    await this.listarProyectos();
                    this.erroresModal = {};
                    this.mostrarAlerta('exito', 'Exito', 'Proyecto creado correctamente');
                } catch (err) {
                    this.mostrarAlerta('error', 'Error','Error al crear proyecto');
                } finally {
                    this.loading = false;
                }
            },

            async actualizarProyecto() {
                this.loading = true;
                try {
                    const res = await fetch(`/proyectos/${this.formproyecto.proyecto_id}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.token
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
                    await this.listarProyectos();
                    this.erroresModal = {};
                    this.mostrarAlerta('exito', 'Exito', 'Proyecto actualizado correctamente');
                } catch (err) {
                    this.mostrarAlerta('error', 'Error al actualizar proyecto');
                } finally {
                    this.loading = false;
                }
            },
            accion(){
                if (this.tipoForm === 'crear') {
                    return this.crearProyecto();
                } else {
                    return this.actualizarProyecto();    
                }
            },
            async mostrarHistorial(proyecto_id) {
                this.proyectoSeleccionado = this.proyectos.find(p => p.proyecto_id === proyecto_id);
                if (!this.proyectoSeleccionado) return;

                this.loading = true;
                try {
                    const res = await fetch(`/proyectos/${proyecto_id}/logs`);
                    const data = await res.json();

                    this.logsProyecto = data.logs || [];
                    this.usuariosProyecto = data.usuarios || [];
                    this.mostrarModalHistorial = true;
                } catch (err) {
                    this.mostrarAlerta('error', 'Error','Error al obtener historial:');
                } finally {
                    this.loading = false;
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
                if (!this.proyectoSeleccionado || this.loading) return; 

                this.erroresModal = {};
                this.loading = true;

                try {
                    if (this.tipoForm === 'eliminar') {
                        await this.eliminarProyecto();
                    } else if (this.tipoForm === 'activar') {
                        await this.activarProyecto();
                    } else {
                        this.mostrarAlerta('error', 'Error','Acción no reconocida');
                        return;
                    }

                    this.mostrarModalStatus = false;
                    await this.listarProyectos();

                    const successMsg = this.tipoForm === 'eliminar' ?
                        'Proyecto eliminado correctamente' :
                        'Estado cambiado correctamente';

                    this.mostrarAlerta('exito', 'Éxito', successMsg);
                } catch (err) {
                    const mensaje = err.message || 'Error al procesar la acción.';
                    this.mostrarAlerta('error', 'Error', mensaje);
                } finally {
                    this.loading = false;
                }
            },

            async eliminarProyecto() {
                if (!this.formEliminar.motivo_eliminacion || !this.formEliminar.motivo_eliminacion.trim()) {
                    this.erroresModal = {
                        motivo_eliminacion: ['Debes ingresar un motivo']
                    };
                    this.mostrarAlerta('error', 'Error', 'Debes ingresar un motivo');
                    throw new Error('Debes ingresar un motivo');
                }

                const url = `/proyectos/${this.proyectoSeleccionado.proyecto_id}`;
                this.loading = true;
                try {
                    const res = await fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.token
                        },
                        body: JSON.stringify(this.formEliminar)
                    });

                    await this.procesarRespuesta(res, 'Error al eliminar el proyecto');
                } finally {
                    this.loading = false;
                }
            },

            async activarProyecto() {
                const url = `/proyectos/${this.proyectoSeleccionado.proyecto_id}/status`;
                this.loading = true;
                try {
                    const res = await fetch(url, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.token
                        }
                    });

                    await this.procesarRespuesta(res, 'Error al cambiar el estado del proyecto');
                } finally {
                    this.loading = false;
                }
            },

            async procesarRespuesta(res, mensajeError) {
                const data = await res.json();

                if (res.status === 422) {
                    this.erroresModal = data.errores || {};
                    const msg = data.mensaje || Object.values(data.errores)?.[0]?.[0] || 'Error de validación';
                    this.mostrarAlerta('error','Error' ,msg);
                    throw new Error(msg);
                }

                if (!res.ok) {
                   const data = await res.json().catch(() => ({}));
                    this.erroresModal = data.errores || {};
                    const msg = data.mensaje || Object.values(this.erroresModal)?.[0]?.[0] || mensajeError;
                    this.mostrarAlerta('error', 'Error', msg);
                    throw new Error(msg);
                }

                return data;
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

                this.loading = true;
                try {
                    await Promise.all([
                        this.cargarClientes(),
                        this.cargarUsuarios(),
                        this.cargarUsuariosAsignados(proyecto_id)
                    ]);
                    this.mostrarModal = true;
                } finally {
                    this.loading = false;
                }
            },

            async cargarUsuariosAsignados(proyecto_id) {
                try {
                    const res = await fetch(`/proyectos/${proyecto_id}/usuarios`);
                    const data = await res.json();
                    const usuarios = data.data || [];
                    this.formproyecto.usuarios = usuarios.map(u => u.usuario_id);
                } catch (err) {
                    this.mostrarAlerta('error', 'Error','Error al cargar usuarios asignados:');
                }
            },

            async eliminarProyecto() {
                if (!this.formEliminar.motivo_eliminacion || !this.formEliminar.motivo_eliminacion.trim()) {
                    this.erroresModal = {
                        motivo_eliminacion: ['Debes ingresar un motivo']
                    };
                    this.mostrarAlerta('error', 'Error', 'Debes ingresar un motivo');
                    throw new Error('Motivo requerido');
                }

                const url = `/proyectos/${this.proyectoSeleccionado.proyecto_id}`;
                const res = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.token
                    },
                    body: JSON.stringify(this.formEliminar)
                });

                await this.procesarRespuesta(res, 'Error al eliminar el proyecto');
            },

            async activarProyecto() {
                const url = `/proyectos/${this.proyectoSeleccionado.proyecto_id}/status`;
                const res = await fetch(url, {
                    method: 'PATCH',
                    headers: {  
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.token
                    }
                });

                await this.procesarRespuesta(res, 'Error al cambiar el estado del proyecto');
            },

            async procesarRespuesta(res, mensajeError) {
                const data = await res.json().catch(() => ({}));

                if (!res.ok) {
                    this.erroresModal = data.errores || {};
                    const msg = data.mensaje || Object.values(this.erroresModal)?.[0]?.[0] || mensajeError;
                    this.mostrarAlerta('error', 'Error', msg);
                    throw new Error(msg);
                }
            },

        }
    });

    app.component('modal-componente', modal);
    app.component('alerta-componente', alerta);
    app.component('loader-global', loader);
    app.mount('#app');
</script>


@endsection