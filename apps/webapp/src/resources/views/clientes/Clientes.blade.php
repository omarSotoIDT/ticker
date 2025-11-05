@extends('layout.Layout')

@section('titulo', 'Gestor de Clientes')

@section('contenido')

<div id="app">
<div class="modulo-encabezado">
        <div class="items-busqueda">
            <div class="cont-buscador">
                <i class="fa-solid fa-magnifying-glass buscador-icono"></i>
                <input type="text" name="cliente" id="cliente" class="input input-busqueda" v-model="busqueda.titulo" @change="buscar()" placeholder="Buscar Clientes..."></input>
            </div>
        </div>
        <button class="btn primary-btn" @click.prevent="modalCrear()">+ Nuevo Cliente</button>
    </div>
    {{-- ======= TABLA DE CLIENTES ======= --}}
    <table class="tabla">
        <thead>
            <tr>
                <th>Nombre</th>
                <th class="columna-grande">Descripción</th>
                <th>Contacto</th>
                <th>Email</th>
                <th>Estado</th>
                <th class="acciones">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="cliente in clientes" :key="cliente.cliente_id">
                <td>@{{ cliente.nombre }}</td>
                <td class="columna-grande">@{{ cliente.descripcion }}</td>
                <td>@{{ cliente.contacto }}</td>
                <td>@{{ cliente.email }}</td>
                <td>
                    <span class="badge" :class="cliente.status === 'ACTIVO' ? 'badge-active' : 'badge-inactive'"> @{{ cliente.status }} </span>
                </td>
                <td class="acciones">
                    <button @click.prevent="modalEditar(cliente.cliente_id)" title="Editar"> <i class="fa-solid fa-pen"></i>
                    </button>
                    <button @click.prevent="modalToggle(cliente.cliente_id, 'activar')" title="Activar / Desactivar">
                        <i class="fa-solid fa-power-off"></i>
                    </button>
                    <button @click.prevent="modalToggle(cliente.cliente_id, 'eliminar')" title="Eliminar">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
            </tr>
            <tr v-if="clientes.length === 0">
                <td colspan="6" class="text-center text-gray-500">No hay clientes registrados.</td>
            </tr>
        </tbody>
    </table>
    {{-- ======= MODAL CREAR / EDITAR ======= --}}
    <modal-componente
        v-model:mostrar="mostrarModal"
        :titulo="tipoForm === 'crear' ? 'Nuevo Cliente' : 'Editar Cliente'"
        :subtitulo="tipoForm === 'crear' ? 'Completa los datos del nuevo cliente' : 'Modifica los datos del cliente'"
        :texto-confirmacion="tipoForm === 'crear' ? 'Guardar Cliente' : 'Guardar Cambios'"
        clase-modal="modal-base"
        @confirmar="guardarCliente">
        <form id="formCliente" class="form centrado" @submit.prevent>
            <div class="campo">
                <label class="etiqueta" for="nombre">Nombre</label>
                <input class="input" type="text" id="nombre" v-model="formCliente.nombre">
                <span class="error" v-if="erroresModal.nombre">@{{ erroresModal.nombre[0] }}</span>
            </div>
            <div class="campo">
                <label class="etiqueta" for="descripcion">Descripción</label>
                <textarea class="input" id="descripcion" v-model="formCliente.descripcion"></textarea>
                <span class="error" v-if="erroresModal.descripcion">@{{ erroresModal.descripcion[0] }}</span>
            </div>
            <div class="campo">
                <label class="etiqueta" for="contacto">Contacto</label>
                <input class="input" type="text" id="contacto" v-model="formCliente.contacto">
                <span class="error" v-if="erroresModal.contacto">@{{ erroresModal.contacto[0] }}</span>
            </div>
            <div class="campo">
                <label class="etiqueta" for="email">Email</label>
                <input class="input" type="email" id="email" v-model="formCliente.email">
                <span class="error" v-if="erroresModal.email">@{{ erroresModal.email[0] }}</span>
            </div>
        </form>
    </modal-componente>


    {{-- ======= MODAL ELIMINAR / CAMBIAR ESTADO ======= --}}
    <modal-componente
        v-model:mostrar="mostrarToggle"
        :titulo="accion === 'eliminar' ? 'Eliminar Cliente' : 'Cambiar Estado'"
        :subtitulo="accion === 'eliminar' ? 'Confirma la eliminación del cliente' : '¿Deseas cambiar el estado del cliente?'"
        :texto-confirmacion="accion === 'eliminar' ? 'Eliminar' : 'Confirmar'"
        clase-modal="modal-base"
        @confirmar="confirmarToggle">
        <form id="formToggle" @submit.prevent>
            <div class="campo" v-if="accion === 'eliminar'">
                <label class="etiqueta" for="motivo">Motivo</label>
                <textarea class="input" id="motivo" v-model="formToggle.motivo" placeholder="Describe el motivo de eliminación..." required></textarea>
                <span class="error" v-if="erroresModal.motivo || erroresModal.motivo_eliminacion">@{{ erroresModal.motivo ? erroresModal.motivo[0] : erroresModal.motivo_eliminacion[0] }}</span>
            </div>
        </form>
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
                clientes: [],
                cliente: null,
                mostrarModal: false,
                mostrarToggle: false,
                tipoForm: 'crear',
                accion: '',
                loading: false,
                erroresModal: {},
                token: '{{ csrf_token() }}',
                formCliente: {
                    nombre: '',
                    descripcion: '',
                    contacto: '',
                    email: ''
                },
                formToggle: {
                    motivo: ''
                },
                busqueda: '',
                alerta: {
                    mostrar: false,
                    tipo: '',
                    titulo: '',
                    mensaje: ''
                }
            }
        },
        mounted() {
            this.listarClientes()
        },
        methods: {
            mostrarAlerta(tipo, titulo, mensaje) {
                this.alerta.tipo = tipo;
                this.alerta.titulo = titulo;
                this.alerta.mensaje = mensaje;
                this.alerta.mostrar = true;
                setTimeout(() => {
                    this.alerta.mostrar = false;
                }, 3000);
            },
            async fetchJson(url, opciones = {}) {
                const res = await fetch(url, opciones)
                const data = await res.json().catch(() => ({}))
                return {
                    res,
                    data
                }
            },
            handleSuccess(modal) {
                const mensaje = arguments.length > 1 ? arguments[1] : null
                this[modal] = false
                this.erroresModal = {}
                this.listarClientes()

                if (mensaje) {
                    this.mostrarAlerta(mensaje, 'exito')
                }
            },
            async listarClientes() {
                try {
                    const {
                        res,
                        data
                    } = await this.fetchJson('/clientes/listado', {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    if (res.ok && data.data) {
                        this.clientes = data.data
                    } else {
                        this.mostrarAlerta('error', 'Error', 'Error al obtener la lista de clientes.')
                    }
                } catch (e) {
                    this.mostrarAlerta('error', 'Error', 'Error al listar clientes.');
                }
            },
            async buscar() {
                try {
                    const params = this.busqueda ? '?busqueda=' + encodeURIComponent(this.busqueda) : ''
                    const {
                        res,
                        data
                    } = await this.fetchJson('/clientes/listar' + params, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.token
                        }
                    })
                    if (res.ok && data.data) {
                        this.clientes = data.data
                    } else {
                        this.mostrarAlerta('info', 'Información', 'No se encontraron clientes para la búsqueda especificada.')
                    }
                } catch (e) {
                    this.mostrarAlerta('error', 'Error', 'Error en búsqueda de clientes.');
                }
            },

            modalCrear() {
                this.tipoForm = 'crear'
                this.formCliente = {
                    nombre: '',
                    descripcion: '',
                    contacto: '',
                    email: ''
                }
                this.erroresModal = {}
                this.mostrarModal = true
            },

            modalEditar(id) {
                const cliente = this.clientes.find(c => c.cliente_id === id)
                if (!cliente) return
                this.tipoForm = 'editar'
                this.cliente = cliente
                this.formCliente = {
                    ...cliente
                }
                this.erroresModal = {}
                this.mostrarModal = true
            },

            async guardarCliente() {
                this.loading = true
                this.erroresModal = {}
                const url = this.tipoForm === 'crear' ? '/clientes' : `/clientes/${this.cliente.cliente_id}`
                const metodo = this.tipoForm === 'crear' ? 'POST' : 'PATCH'

                try {
                    const {
                        res,
                        data
                    } = await this.fetchJson(url, {
                        method: metodo,
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.token
                        },
                        body: JSON.stringify(this.formCliente)
                    })

                    if (res.status === 422 && data.errores) {
                        this.erroresModal = data.errores

                        const primerError = Object.values(data.errores)[0]
                        if (primerError && primerError.length > 0) {
                            this.mostrarAlerta('info', 'Información', primerError[0])
                        }
                        return
                    }

                    if (!res.ok) {
                        const mensaje = data.error || (res.status >= 500 ? 'Error del servidor. Intenta más tarde.' : 'Error desconocido al guardar el cliente.')
                        this.mostrarAlerta(mensaje, 'error')
                        return
                    }

                    this.handleSuccess('mostrarModal', data && data.mensaje ? data.mensaje : null)
                } catch (e) {
                    this.mostrarAlerta('error', 'Error', 'Error general al guardar el cliente.')
                } finally {
                    this.loading = false
                }
            },

            modalToggle(id, tipo) {
                const cliente = this.clientes.find(c => c.cliente_id === id)
                if (!cliente) return
                this.cliente = cliente
                this.accion = tipo
                this.formToggle.motivo = ''
                this.erroresModal = {}
                this.mostrarToggle = true
            },

            async eliminarCliente() {
                if (!this.formToggle.motivo || !this.formToggle.motivo.trim()) {
                    this.erroresModal = {
                        motivo_eliminacion: ['Debes ingresar un motivo']
                    };
                    this.mostrarAlerta('error', 'Error', 'Debes ingresar un motivo');
                    return;
                }

                const url = `/clientes/${this.cliente.cliente_id}`;
                const body = JSON.stringify({
                    motivo_eliminacion: this.formToggle.motivo
                });

                return this.realizarPeticion(url, 'DELETE', body);
            },

            async cambiarStatusCliente() {
                const url = `/clientes/${this.cliente.cliente_id}/status`;
                return this.realizarPeticion(url, 'PATCH');
            },

            async realizarPeticion(url, metodo, body) {
                try {
                    const {
                        res,
                        data
                    } = await this.fetchJson(url, {
                        method: metodo,
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.token
                        },
                        body
                    });

                    if (res.status === 422 && data.errores) {
                        this.erroresModal = data.errores;
                        const primerError = Object.values(data.errores)[0];
                        if (primerError && primerError.length > 0) {
                            this.mostrarAlerta(primerError[0], 'error');
                        }
                        return false;
                    }

                    if (!res.ok) {
                        const mensaje = data.error || (res.status >= 500 ?
                            'Error del servidor. Intenta más tarde.' :
                            'Error desconocido al confirmar la acción.');
                        this.mostrarAlerta(mensaje, 'error');
                        return false;
                    }

                    this.handleSuccess('mostrarToggle', data && data.mensaje ? data.mensaje : null);
                    return true;

                } catch (e) {
                    this.mostrarAlerta('error', 'Error', 'Error general al confirmar la acción.');
                    return false;
                }
            },


            async confirmarToggle() {
                this.loading = true;
                this.erroresModal = {};

                if (this.accion === 'eliminar') {
                    await this.eliminarCliente();
                } else if (this.accion === 'activar') {
                    await this.cambiarStatusCliente();
                }

                this.loading = false;
            }
        }
    })


    app.component('modal-componente', modal);
    app.component('alerta-componente', alerta);

    app.mount('#app')
</script>


@endsection