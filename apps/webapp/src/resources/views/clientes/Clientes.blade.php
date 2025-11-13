@extends('layout.Layout')

@section('titulo', 'Gestor de Clientes')

@section('contenido')

<div id="app">

    {{-- ======= LOADER ======= --}}
    <loader-componente :visible="loading"></loader-componente>

    <div class="modulo-encabezado">
        <div class="items-busqueda">
            <div class="cont-buscador">
                <i class="fa-solid fa-magnifying-glass buscador-icono"></i>
                <input type="text" name="cliente" id="cliente" class="input input-busqueda" v-model="busqueda.titulo" @change="buscar()" placeholder="Buscar Clientes..."></input>
            </div>
        </div>
        <button class="primary-btn" @click.prevent="modalCrear()" :disabled="loading">
            <i class="fa-solid fa-plus"></i>
            Nuevo Cliente
        </button>
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
                    <div class="acciones-contenedor">
                        <button @click.prevent="modalEditar(cliente.cliente_id)" title="Editar"> <i class="fa-solid fa-pen"></i>
                        </button>
                        <button @click.prevent="modalToggle(cliente.cliente_id, 'activar')" title="Activar / Desactivar">
                            <i class="fa-solid fa-power-off"></i>
                        </button>
                        <button @click.prevent="modalToggle(cliente.cliente_id, 'eliminar')" title="Eliminar">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
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
        :titulo="tituloModalPrincipal"
        :subtitulo="subtituloModalPrincipal"
        :texto-confirmacion="textoConfirmacionPrincipal"
        clase-modal="modal-base"
        :deshabilitar-confirmacion="loading"
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
        :titulo="tituloModalToggle"
        :subtitulo="tituloModalToggle"
        :texto-confirmacion="textoConfirmacionToggle"
        clase-modal="modal-base"
        :deshabilitar-confirmacion="loading"
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
                busqueda: {
                    titulo: ''
                },
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
        computed: {
            tituloModalPrincipal() {
                if (this.tipoForm === 'crear') {
                    return 'Nuevo Cliente';
                } else {
                    return 'Editar Cliente';
                }
                return  '';
            },
            subtituloModalPrincipal() {
                if (this.tipoForm === 'crear') {
                    return 'Completa los datos del nuevo cliente';
                } else {
                    return 'Modifica los datos del cliente';
                }
            },
            tituloModalToggle() {
                if (this.accion === 'eliminar') {
                    return 'Eliminar Cliente';
                } else {
                    return 'Cambiar Estado';
                }
                return  '';
            },
            textoConfirmacionPrincipal() {
                if (this.loading) {
                    return 'Procesando...';
                } else if (this.tipoForm === 'crear') {
                    return 'Guardar Cliente';
                } else {
                    return 'Guardar Cambios';
                }
            },
            textoConfirmacionToggle() {
                if (this.loading) {
                    return 'Procesando...';
                }
                if (this.accion === 'eliminar') {
                    return 'Eliminar';
                } else {
                    return 'Confirmar';
                }
            }
        },
        methods: {
             mostrarAlerta(tipo, titulo, mensaje) {
                this.alerta.tipo = tipo;
                this.alerta.titulo = titulo;
                this.alerta.mensaje = mensaje;
                this.alerta.mostrar = true;
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
                    this.mostrarAlerta('exito', 'Éxito', mensaje)
                }
            },
            async listarClientes() {
                this.loading = true;
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
                } finally {
                    this.loading = false;
                }
            },
            async buscar() {
                this.loading = true;
                try {
                    const params = this.busqueda.titulo ? '?busqueda=' + encodeURIComponent(this.busqueda.titulo) : ''
                    const {
                        res,
                        data
                    } = await this.fetchJson('/clientes/listado' + params, {
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
                } finally {
                    this.loading = false;
                }
            },

            modalCrear() {
                if (this.loading) return;
                this.tipoForm = 'crear'
                this.formCliente = {
                    nombre: '',
                    descripcion: '',
                    contacto: '',
                    email: ''
                }
                this.erroresModal = {}
                this.mostrarModal = true;
            },

            modalEditar(id) {
                if (this.loading) return;

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
                        this.mostrarAlerta('error', 'Error', mensaje)
                        return
                    }

                    this.handleSuccess('mostrarModal', data && data.mensaje ? data.mensaje : null)
                } catch (e) {
                    this.mostrarAlerta('error', 'Error', 'Ocurrió un error al guardar el cliente.')
                } finally {
                    this.loading = false
                }
            },

            modalToggle(id, tipo) {
                if (this.loading) return;
                const cliente = this.clientes.find(c => c.cliente_id === id)
                if (!cliente) return
                this.cliente = cliente
                this.accion = tipo
                this.formToggle.motivo = ''
                this.erroresModal = {}
                this.mostrarToggle = true
            },

            async eliminarCliente() {
                if (this.loading || !this.formToggle.motivo || !this.formToggle.motivo.trim()) {
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
                if (this.loading) return;
                const url = `/clientes/${this.cliente.cliente_id}/status`;
                return this.realizarPeticion(url, 'PATCH');
            },

            async realizarPeticion(url, metodo, body) {
                this.loading = true;
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
                            this.mostrarAlerta('error', 'Error', primerError[0]);
                        }
                        return false;
                    }

                    if (!res.ok) {
                        let mensaje = data.error || 'Error desconocido al confirmar la acción.';
                        if (res.status >= 500) {
                            mensaje = 'Error del servidor. Intenta más tarde.';
                        }
                        this.mostrarAlerta('error', 'Error', mensaje);
                        return false;
                    }

                    this.handleSuccess('mostrarToggle', data && data.mensaje ? data.mensaje : null);
                    return true;

                } catch (e) {
                    this.mostrarAlerta('error', 'Error', 'Error general al confirmar la acción.');
                    return false;
                } finally {
                    this.loading = false;
                }
            },


            async confirmarToggle() {
                if (this.loading) return; 

                this.erroresModal = {};

                if (this.accion === 'eliminar') {
                    await this.eliminarCliente();
                } else if (this.accion === 'activar') {
                    await this.cambiarStatusCliente();
                }
            }
        }
    })

    app.component('modal-componente', modal);
    app.component('alerta-componente', alerta);
    app.component('loader-componente', loader);

    app.mount('#app')
</script>

@endsection