@extends('layout.Layout')

@section('titulo', 'Gestor de Proyectos')

@section('contenido')
@include('componentes.modal')

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
                <td>@{{ proyecto.cliente }}</td>
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
        @confirmar="guardarproyecto">

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
                    <div v-for="usuario in usuariosDisponibles" :key="usuario.usuario_id">
                        <input
                            type="checkbox"
                            :id="'usuario-' + usuario.usuario_id"
                            :value="usuario.usuario_id"
                            v-model="formproyecto.usuarios">
                        <label :for="'usuario-' + usuario.usuario_id">@{{ usuario.nombre }}</label>
                    </div>
                </div>
                <span class="error" v-if="erroresModal.usuarios">@{{ erroresModal.usuarios[0] }}</span>
            </div>
        </form>
    </modal-componente>
</div>

{{-- ===== TEMPLATE DEL MODAL ===== --}}
<script type="text/x-template" id="modal-template">
    <div v-if="mostrar" class="modal-overlay">
        <div class="modal">
            <h3>@{{ titulo }}</h3>
            <p>@{{ subtitulo }}</p>
            <div class="modal-body">
                <slot></slot>
            </div>
            <div v-if="mostrarBotones !== false" class="modal-footer">
                <button class="btn-secondary" @click="$emit('update:mostrar', false)">Cancelar</button>
                <button class="btn-primary" @click="$emit('confirmar')">@{{ textoConfirmacion }}</button>
            </div>
        </div>
    </div>
</script>

{{-- ===== INICIALIZACIÓN DE VUE ===== --}}
<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
const { createApp } = Vue;

const app = createApp({
    data() {
        return {
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
            erroresModal: {}
        }
    },

    mounted() {
        this.listarProyectos();
    },

    methods: {
        listarProyectos() {
            axios.get('/proyectos/listado', { params: { busqueda: this.busqueda } })
                .then(res => this.proyectos = res.data.data)
                .catch(err => console.error(err));
        },

        buscar() { this.listarProyectos(); },

        modalCrear() {
            this.tipoForm = 'crear';
            this.erroresModal = {};
            this.formproyecto = { proyecto_id: null, nombre: '', descripcion: '', cliente_id: null, usuarios: [] };
            this.cargarClientes();
            this.cargarUsuarios();
            this.mostrarModal = true;
        },

        cargarClientes() {
            axios.get('/clientes/listado') // ✅ Ajustado
                .then(res => this.clientes = res.data.data)
                .catch(err => console.error(err));
        },

        cargarUsuarios() {
            axios.get('/usuarios/listado') // ✅ Ajustado
                .then(res => this.usuariosDisponibles = res.data.data)
                .catch(err => console.error(err));
        },

        guardarproyecto() {
            const url = this.tipoForm === 'crear' ? '/proyectos' : `/proyectos/${this.formproyecto.proyecto_id}`;
            const metodo = this.tipoForm === 'crear' ? 'post' : 'patch';
            axios[metodo](url, this.formproyecto)
                .then(() => {
                    this.mostrarModal = false;
                    this.listarProyectos();
                    this.erroresModal = {};
                })
                .catch(err => {
                    if (err.response && err.response.status === 422) {
                        this.erroresModal = err.response.data.errores;
                    } else {
                        console.error(err);
                    }
                });
        }
    }
});

app.component('modal-componente', {
    template: '#modal-template',
    props: ['mostrar', 'titulo', 'subtitulo', 'textoConfirmacion', 'mostrarBotones'],
    emits: ['update:mostrar', 'confirmar']
});

app.mount('#app');
</script>
@endsection
