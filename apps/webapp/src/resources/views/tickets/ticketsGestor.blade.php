@extends('layout.Layout')

@section('title', 'Tickets')

@section('contenido')
    <div id="app">
        <loader-global :visible="loading"></loader-global>

        <div class="modulo-encabezado">
            <div class="items-busqueda">
                <div class="cont-buscador">
                    <i class="fa-solid fa-magnifying-glass buscador-icono"></i>
                    <input type="text" name="ticket" id="ticket" class="input input-busqueda" v-model="busqueda.titulo" @change="buscar()" placeholder="Buscar Tickets..."></input>
                </div>
                <select id="busquedaCliente" class="input select-busqueda" v-model="busqueda.cliente_id">
                    <option value="">Todos</option>
                    <option v-for="cliente in clientes" :value="cliente.cliente_id">@{{ cliente.nombre }}</option>
                </select>
                <select id="busquedaPrioridad" class="input select-busqueda" v-model="busqueda.prioridad">
                    <option value="">Todas</option>
                    <option v-for="prioridad in prioridades" :value="prioridad">@{{ prioridad }}</option>
                </select>
                <button class="btn primary-btn btn-buscar" @click.prevent="buscar()">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
                <button class="btn secondary-btn btn-limpiar-filtros" @click.prevent="limpiarFiltros()">
                    <i class="fa-solid fa-eraser"></i>
                </button>
            </div>
            <button class="primary-btn" @click.prevent="modalCrear()" :disabled="loading">
                <i class="fa-solid fa-plus"></i>
                Nuevo Ticket 
            </button>
        </div>

        <table class="tabla">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Cliente</th>
                    <th>Proyecto</th>
                    <th>Asignado</th>
                    <th>Estado</th>
                    <th>Prioridad</th>
                    <th>Creado</th>
                    <th class="acciones">Accion</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="ticket in tickets" :key="ticket.ticketId">
                    <td>@{{ ticket.serieFolio }}</td>
                    <td>
                        @{{ ticket.titulo }} 
                        <br>
                        <small class="etiqueta-gris">@{{ ticket.etiqueta }}</small>
                    </td>
                    <td>@{{ ticket.cliente }}</td>
                    <td>@{{ ticket.proyecto }}</td>
                    <td>@{{ ticket.usuarioAsignado }}</td>
                    <td><span class="badge" :class="this.STATUS_BADGES[ticket.status]">@{{ ticket.status }}</span></td>
                    <td><span class="badge" :class="this.PRIORIDAD_BADGES[ticket.prioridad]">@{{ ticket.prioridad }}</span></td>
                    <td>@{{ ticket.registroFecha }}</td>
                    <td class="acciones">
                        <div class="acciones-contenedor">
                            <button @click.prevent="mostrarTicket(ticket.ticketId)" title="Ver Ticket">
                                <i class="fa fa-eye"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <tr v-if="tickets.length === 0">
                    <td colspan="9">No hay tickets registrados.</td>
                </tr>
            </tbody>
        </table>

        <modal-componente
        v-model:mostrar="modalRegistro.mostrar"
        :titulo="modalRegistro.tipo === 'crear' ? 'Nuevo Ticket' : 'Editar Ticket'"
        :subtitulo="modalRegistro.tipo === 'crear' ? 'Completa los datos del nuevo Ticket' : 'Modifica los datos del Ticket'"
        :texto-confirmacion="modalRegistro.tipo === 'crear' ? 'Crear Ticket' : 'Guardar Cambios'"
        clase-modal="modal-base"
        @confirmar="modalRegistro.tipo === 'crear' ? agregar() : editar()">

            <form id="form" class="form centrado">
                <div class="campo">
                    <label class="etiqueta" for="titulo">Título</label>
                    <input class="input" type="text" name="titulo" id="titulo" v-model="formTicket.titulo">
                    <span class="error" v-if="erroresRegistro.titulo">@{{ erroresRegistro.titulo[0] }}</span>
                </div>

                <div class="campo">
                    <label class="etiqueta" for="descripcion">Descripción</label>
                    <textarea class="input" name="descripcion" id="descripcion" v-model="formTicket.descripcion"></textarea>
                    <span class="error" v-if="erroresRegistro.descripcion">@{{ erroresRegistro.descripcion[0] }}</span>
                </div>

                <div class="grid-2-col">
                    <div class="campo">
                        <label class="etiqueta" for="cliente">Cliente</label>
                        <select class="input" name="cliente" id="cliente" v-model="formTicket.cliente_id" @change="seleccionarCliente()">
                            <option :value="null" disabled>Selecciona cliente</option>
                            <option v-for="cliente in clientes" :key="cliente.cliente_id" :value="cliente.cliente_id">
                                @{{ cliente.nombre }}    
                            </option>    
                        </select>
                        <span class="error" v-if="erroresRegistro.cliente_id">@{{ erroresRegistro.cliente_id[0] }}</span>
                    </div>
                    <div class="campo">
                        <label class="etiqueta" for="proyecto">Proyecto</label>
                        <select class="input" name="proyecto" id="proyecto" v-model="formTicket.proyecto_id" :disabled="!formTicket.cliente_id">
                            <option :value="null" disabled>Selecciona proyecto</option>
                            <option v-for="proyecto in proyectosCliente" :key="proyecto.proyecto_id" :value="proyecto.proyecto_id">
                                @{{ proyecto.nombre }}
                            </option>    
                        </select>
                        <span class="error" v-if="erroresRegistro.proyecto_id">@{{ erroresRegistro.proyecto_id[0] }}</span>
                    </div>
                </div>

                <div class="campo"> 
                    <label class="etiqueta" for="etiqueta">Categoría</label> 
                    <select class="input" name="etiqueta" id="etiqueta" v-model="formTicket.etiqueta_id">
                        <option :value="null" disabled>Selecciona categoría</option> 
                        <option v-for="etiqueta in etiquetas" :key="etiqueta.etiquetaId" :value="etiqueta.etiquetaId">
                            @{{ etiqueta.titulo }}
                        </option>
                    </select>
                    <span class="error" v-if="erroresRegistro.etiqueta_id">@{{ erroresRegistro.etiqueta_id[0] }}</span>
                </div>

                <div class="grid-2-col">

                    <div class="campo">
                        <label class="etiqueta" for="prioridad">Prioridad</label>
                        <select class="input" name="prioridad" id="prioridad" v-model="formTicket.prioridad">
                            <option value="" disabled>Selecciona prioridad</option>
                            <option v-for="prioridad in prioridades" :value="prioridad">@{{ prioridad }}</option>
                        </select>
                        <span class="error" v-if="erroresRegistro.prioridad">@{{ erroresRegistro.prioridad[0] }}</span>
                    </div>

                    <div class="campo" v-if="modalRegistro.tipo === 'crear' && formTicket.titulo && formTicket.descripcion && formTicket.cliente_id && formTicket.proyecto_id && formTicket.etiqueta_id && formTicket.prioridad">
                         <label class="etiqueta" for="usuario">Usuario Asignado</label>
                         <select class="input" name="usuario" id="usuario" v-model="formTicket.usuario_asignado_id">
                            <option :value="null" disabled>Selecciona un usuario</option>
                            <option v-for="usuario in usuarios" :value="usuario.usuarioId">@{{ usuario.usuario }}</option>
                         </select>
                         <span class="error" v-if="erroresRegistro.usuario_asignado_id">@{{ erroresRegistro.usuario_asignado_id[0] }}</span>
                    </div>

                    <div class="campo" v-if="modalRegistro.tipo === 'editar'"> 
                        <label class="etiqueta" for="status">Estado</label>
                        <select class="input" name="status" id="status" v-model="formTicket.status">
                            <option v-for="status in estados" :value="status">@{{ status }}</option>
                        </select>
                        <span class="error" v-if="erroresRegistro.status">@{{ erroresRegistro.status[0] }}</span>
                    </div>

                </div>                

            </form>
        </modal-componente>
        <modal-componente
            v-model:mostrar="modalVer.mostrar"
            :titulo="ticket ? ticket.titulo : 'Detalle'"
            :subtitulo="ticket ? ('#' + ticket.serieFolio + ' • ' + ticket.proyecto + ' • ' + ticket.etiqueta) : ''"
            :mostrar-botones="false"
            clase-modal="modal-grande modal-vista-detalle"
            @close="limparModalVer"
        >

            <button class="btn secondary-btn edit-btn" @click.prevent="modalEditar(ticket.ticketId)" title="Editar Ticket">
                <i class="fa fa-pen-to-square"></i> Editar
            </button>

            <div v-if="ticket" class="vista-detalle-contenedor">
                <div class="vista-detalle-header-status grid-3-col"> 
                    <div class="campo"> 
                        <label class="etiqueta">Estado</label>
                        <select class="input" v-model="ticket.status" @change="editarStatus()">
                            <option v-for="estado in estados" :value="estado">@{{ estado }}</option>
                        </select>
                    </div>
                    <div class="campo">
                        <label class="etiqueta">Prioridad</label>
                        <select class="input" v-model="ticket.prioridad" @change="editarPrioridad()">
                            <option v-for="prioridad in prioridades" :value="prioridad">@{{ prioridad }}</option>
                        </select>
                    </div>
                    <div class="campo">
                        <label class="etiqueta">Asignado a</label>
                        <select class="input" v-model="ticket.usuarioAsignadoId" @change="editarAsignacion()">
                            <option value="">-- Sin asignar --</option> 
                            <option v-for="usuario in usuarios" :value="usuario.usuarioId" :key="usuario.usuarioId">
                                @{{ usuario.usuario }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="vista-ticket-secciones-nav">
                    <button :class="{'activo': modalVer.seccion === 'descripcion'}" @click="modalVer.seccion = 'descripcion'">
                        <i class="fa-solid fa-align-left"></i> Descripción
                    </button>
                    <button :class="{'activo': modalVer.seccion === 'comentarios'}" @click="modalVer.seccion = 'comentarios'">
                        <i class="fa-solid fa-comments"></i> Comentarios
                        <span class="badge-contador">@{{ ticketFeedback ? ticketFeedback.length : 0 }}</span>
                    </button>
                    <button :class="{'activo': modalVer.seccion === 'historial'}" @click="modalVer.seccion = 'historial'">
                        <i class="fa-solid fa-clock-rotate-left"></i> Historial
                    </button>
                </div>

                <div class="vista-ticket-secciones-contenido">

                    <div v-if="modalVer.seccion === 'descripcion'" class="seccion-descripcion">
                        <div class="descripcion-contenido" v-text="ticket.descripcion"></div>
                        <div class="descripcion-fechas">
                            <span><strong>Creado:</strong> @{{ ticket.registroFecha }}</span>
                            <span v-if="ticket.actualizacionFecha"><strong>Actualizado:</strong> @{{ ticket.actualizacionFecha }}</span>
                        </div>
                    </div>

                    <div v-if="modalVer.seccion === 'comentarios'" class="seccion-comentarios">
                        <div class="lista-comentarios">
                             <p v-if="!ticketFeedback || !ticketFeedback.length" class="empty-state">
                                No hay comentarios aún.
                             </p>
                            <div v-for="comentario in ticketFeedback" :key="comentario.feedbackId" class="comentario-item">
                                <div class="comentario-avatar">@{{ comentario.usuario ? comentario.usuario.substring(0, 1) : 'U' }}</div>
                                <div class="comentario-cuerpo">
                                    <div class="comentario-header">
                                        <strong>@{{ comentario.usuario }}</strong>
                                        <span class="comentario-fecha">@{{ comentario.registroFecha }}</span>
                                    </div>
                                    <div class="comentario-texto" v-text="comentario.comentario"></div>
                                </div>
                            </div>
                        </div>

                        <div class="nuevo-comentario-form">
                            <textarea class="input" v-model="formFeedback.comentario" placeholder="Escribe un comentario..."></textarea>
                            <span class="error" v-if="erroresRegistroFeedback.comentario">@{{ erroresRegistroFeedback.comentario[0] }}</span>
                            <button class="btn primary-btn btn-enviar-comentario" @click.prevent="agregarFeedback" title="Enviar Comentario">
                                <i class="fa-solid fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>

                    <div v-if="modalVer.seccion === 'historial'" class="seccion-historial">
                        <p v-if="!ticketHistorial || !ticketHistorial.length" class="empty-state">
                            No hay historial de cambios.
                        </p>
                        <ul class="lista-historial">
                            <li v-for="item in ticketHistorial" :key="item.logTicketId">
                                <span class="historial-fecha-usuario">@{{ item.registroFecha }}</span>
                                <span class="historial-cambio">
                                    @{{ item.descripcion }}:
                                </span>
                            </li>
                        </ul>
                    </div>

                </div>

                <div class="vista-ticket-footer">
                    <button class="btn primary-btn" @click="modalVer.mostrar = false">Cerrar</button>
                </div>

            </div>
        </modal-componente>
        <modal-componente
            v-model:mostrar="modalVer.mostrar"
            :titulo="ticket ? ticket.titulo : 'Detalle'"
            :subtitulo="ticket ? ('#' + ticket.serieFolio + ' • ' + ticket.proyecto + ' • ' + ticket.etiqueta) : ''"
            :mostrar-botones="false"
            clase-modal="modal-grande modal-vista-detalle"
            @close="limparModalVer"
        >

            <button class="btn secondary-btn edit-btn" @click.prevent="modalEditar(ticket.ticketId)" title="Editar Ticket">
                <i class="fa fa-pen-to-square"></i> Editar
            </button>

            <div v-if="ticket" class="vista-detalle-contenedor">
                <div class="vista-detalle-header-status grid-3-col"> 
                    <div class="campo"> 
                        <label class="etiqueta">Estado</label>
                        <select class="input" v-model="ticket.status" @change="editarStatus()">
                            <option v-for="estado in estados" :value="estado">@{{ estado }}</option>
                        </select>
                    </div>
                    <div class="campo">
                        <label class="etiqueta">Prioridad</label>
                        <select class="input" v-model="ticket.prioridad" @change="editarPrioridad()">
                            <option v-for="prioridad in prioridades" :value="prioridad">@{{ prioridad }}</option>
                        </select>
                    </div>
                    <div class="campo">
                        <label class="etiqueta">Asignado a</label>
                        <select class="input" v-model="ticket.usuarioAsignadoId" @change="editarAsignacion()">
                            <option value="">-- Sin asignar --</option> 
                            <option v-for="usuario in usuarios" :value="usuario.usuarioId" :key="usuario.usuarioId">
                                @{{ usuario.usuario }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="vista-ticket-secciones-nav">
                    <button :class="{'activo': modalVer.seccion === 'descripcion'}" @click="modalVer.seccion = 'descripcion'">
                        <i class="fa-solid fa-align-left"></i> Descripción
                    </button>
                    <button :class="{'activo': modalVer.seccion === 'comentarios'}" @click="modalVer.seccion = 'comentarios'">
                        <i class="fa-solid fa-comments"></i> Comentarios
                        <span class="badge-contador">@{{ ticketFeedback ? ticketFeedback.length : 0 }}</span>
                    </button>
                    <button :class="{'activo': modalVer.seccion === 'historial'}" @click="modalVer.seccion = 'historial'">
                        <i class="fa-solid fa-clock-rotate-left"></i> Historial
                    </button>
                </div>

                <div class="vista-ticket-secciones-contenido">

                    <div v-if="modalVer.seccion === 'descripcion'" class="seccion-descripcion">
                        <div class="descripcion-contenido" v-text="ticket.descripcion"></div>
                        <div class="descripcion-fechas">
                            <span><strong>Creado:</strong> @{{ ticket.registroFecha }}</span>
                            <span v-if="ticket.actualizacionFecha"><strong>Actualizado:</strong> @{{ ticket.actualizacionFecha }}</span>
                        </div>
                    </div>

                    <div v-if="modalVer.seccion === 'comentarios'" class="seccion-comentarios">
                        <div class="lista-comentarios">
                             <p v-if="!ticketFeedback || !ticketFeedback.length" class="empty-state">
                                No hay comentarios aún.
                             </p>
                            <div v-for="comentario in ticketFeedback" :key="comentario.feedbackId" class="comentario-item">
                                <div class="comentario-avatar">@{{ comentario.usuario ? comentario.usuario.substring(0, 1) : 'U' }}</div>
                                <div class="comentario-cuerpo">
                                    <div class="comentario-header">
                                        <strong>@{{ comentario.usuario }}</strong>
                                        <span class="comentario-fecha">@{{ comentario.registroFecha }}</span>
                                    </div>
                                    <div class="comentario-texto" v-text="comentario.comentario"></div>
                                </div>
                            </div>
                        </div>

                        <div class="nuevo-comentario-form">
                            <textarea class="input" v-model="formFeedback.comentario" placeholder="Escribe un comentario..." maxlength="500"></textarea>
                            <span class="error" v-if="erroresRegistroFeedback.comentario"></span>
                            <button class="btn primary-btn btn-enviar-comentario" @click.prevent="agregarFeedback" title="Enviar Comentario" :disabled="loading">
                                <i class="fa-solid fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>

                    <div v-if="modalVer.seccion === 'historial'" class="seccion-historial">
                        <div class="contenedor-scroll-modal">
                            <ul v-if="ticketHistorial && ticketHistorial.length > 0" class="">
                                <li v-for="log in ticketHistorial" :key="log.logTicketId">
                                    <small class="historial-fecha-usuario">@{{ log.registroFecha }} — @{{ log.usuario }}</small>
                                    <small class="historial-cambio" v-html="log.descripcion ? log.descripcion.replace(/\n/g, '<br>') : ''"></small>
                                    <hr>
                                </li>
                            </ul>
                            <p v-else class="empty-state">No hay historial de cambios.</p>
                        </div>
                    </div>

                </div>

                <div class="vista-ticket-footer">
                    <button class="btn primary-btn" @click="modalVer.mostrar = false">Cerrar</button>
                </div>

            </div>
        </modal-componente>

        <alerta-componente
        :mostrar="alerta.mostrar"
        :tipo="alerta.tipo"
        :titulo="alerta.titulo"
        :mensaje="alerta.mensaje"/>
    </div>

    <script>
        const app = Vue.createApp({
            data() {
                return {
                    tickets: {{ Js::from($tickets) }},
                    ticket: null,
                    ticketFeedback: null,
                    ticketHistorial: [],
                    etiquetas: {{ JS::from($etiquetas) }},
                    prioridades: [BAJA, MEDIA, ALTA, URGENTE],
                    estados: [ABIERTO,EN_PROGRESO,ATENDIDO,CERRADO,INFO_REQUERIDA,CANCELADO],
                    clientes: {{ JS::from($clientes) }},
                    proyectos: {{ JS::from($proyectos) }},
                    proyectosCliente: [],
                    usuarios: {{ JS::from($usuarios) }},
                    loading: false,
                    alerta: {
                        mostrar: false,
                        tipo: '',
                        titulo: '',
                        mensaje: ''
                    },
                    modalRegistro: {
                        tipo: 'crear',
                        mostrar: false
                    },
                    modalVer: {
                        mostrar: false,
                        seccion: 'descripcion'
                    },
                    formTicket: {
                        cliente_id: null,
                        proyecto_id: null,
                        etiqueta_id: null,
                        usuario_asignado_id: null,
                        titulo: '',
                        descripcion: '',
                        prioridad: '',
                        status: '',
                    },
                    formFeedback: {
                        comentario: ''
                    },
                    params: new URLSearchParams(),
                    busqueda: {
                        titulo: '',
                        cliente_id: '',
                        prioridad: ''
                    },
                    erroresRegistro: {},
                    erroresRegistroFeedback: {}, 
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    STATUS_BADGES: {
                        [ABIERTO]: 'status-abierto',
                        [EN_PROGRESO]: 'status-progreso',
                        [ATENDIDO]: 'status-atendido',
                        [CERRADO]: 'status-cerrado',
                        [INFO_REQUERIDA]: 'status-requerida',
                        [CANCELADO]: 'status-cancelado'
                    },
                    PRIORIDAD_BADGES: {
                        [BAJA]: 'prioridad-baja',
                        [MEDIA]: 'prioridad-media',
                        [ALTA]: 'prioridad-alta',
                        [URGENTE]: 'prioridad-urgente'
                    }
                }
            },
            methods: {
                limpiarRegistro(){
                    this.formTicket.cliente_id = null,
                    this.formTicket.proyecto_id = null,
                    this.formTicket.etiqueta_id = null,
                    this.formTicket.usuario_asignado_id = null,
                    this.formTicket.titulo = '',
                    this.formTicket.descripcion = '',
                    this.formTicket.prioridad = '',
                    this.formTicket.status = ''
                },
                limparModalVer(){
                    this.ticket = null;
                    this.ticketFeedback = null;
                    this.ticketHistorial = []; // Limpiar historial
                    this.formFeedback.comentario = '';
                    this.erroresRegistroFeedback = {};
                    this.modalVer.mostrar = false;
                },
                cargarForm(){
                    this.formTicket = {
                        cliente_id: this.ticket.clienteId,
                        proyecto_id: this.ticket.proyectoId,
                        etiqueta_id: this.ticket.etiquetaId,
                        usuario_asignado_id: this.ticket.usuarioAsignadoId,
                        titulo: this.ticket.titulo,
                        descripcion: this.ticket.descripcion,
                        prioridad: this.ticket.prioridad,
                        status: this.ticket.status,
                    }
                },
                mostrarAlerta(tipo, titulo, mensaje) {
                    this.alerta.tipo = tipo;
                    this.alerta.titulo = titulo;
                    this.alerta.mensaje = mensaje;
                    this.alerta.mostrar = true;
                    setTimeout(() => {
                        this.alerta.mostrar = false;
                    }, 3000);
                },
                limpiarFiltros() {
                    this.busqueda = {
                        titulo: '',
                        cliente_id: '',
                        prioridad: ''
                    };
                    this.buscar();
                },
                async buscar(){
                    this.loading = true;
                    try {
                        if(this.busqueda){
                            this.params.append('titulo', this.busqueda.titulo)
                            this.params.append('cliente_id', this.busqueda.cliente_id)
                            this.params.append('prioridad', this.busqueda.prioridad)
                        }
                        const response = await fetch('tickets/listado-rest?' + this.params.toString(), {
                            method: 'GET', headers: this.headers
                        })

                        if (!response.ok) {
                            throw new Error('Error al buscar los tickets: ' + response.status);
                        }
                        const data = await response.json();
                        this.tickets = data;
                    } catch (error) {
                        this.mostrarAlerta('error', 'Error', 'Ocurrio un error al buscar los tickets')
                    } finally {
                        this.loading = false;
                    }
                },

                modalCrear(){
                    this.loading = true;
                    this.modalRegistro.tipo = 'crear';
                    this.limpiarRegistro();
                    this.modalRegistro.mostrar = true;
                    this.loading = false;
                },
                modalEditar(id){
                    this.loading = true;
                    if (this.modalVer.mostrar) {
                        this.modalVer.mostrar = false;
                    }
                    this.modalRegistro.tipo = 'editar';
                    this.erroresRegistro = {};
                    this.cargarForm();
                    this.actualizarProyectos(this.formTicket.cliente_id);
                    this.modalRegistro.mostrar = true;
                    this.loading = false;
                },
                async listarTickets(){
                    this.loading = true;
                    try {
                        const response = await fetch('/tickets/listado-rest?' + this.params.toString(), {
                            method: 'GET', headers: this.headers
                        })
                        if (!response.ok) {
                            throw new Error('Error al listar los tickets: ' + response.status);                        
                        }

                        const data = await response.json();
                        this.tickets = data;
                        this.modalRegistro.mostrar = false;
                    } catch (error) {
                        this.mostrarAlerta('error', 'Error', 'Ocurrio un error al listar los tickets')
                    } finally {
                        this.loading = false;
                    }
                },
                async mostrarTicket(id){
                    this.loading = true;
                    await this.obtenerTicket(id); 
                    this.loading = false;
                    this.ticketHistorial;
                    if(this.ticket){
                        this.modalVer.seccion = 'descripcion'; 
                        this.formFeedback.comentario = ''; 
                        this.erroresRegistroFeedback = {}; 
                        this.modalVer.mostrar = true; 
                    }
                },
                async obtenerTicket(id){
                    this.loading = true;
                    try {
                        const response = await fetch(`/tickets/${id}/detalle-rest`, {
                            method: 'GET', headers: this.headers
                        })

                        if (!response.ok) {
                            throw new Error('Error al obtener el ticket: ' + response.status);
                        }

                        const data = await response.json();
                        this.ticket = data['ticket'];
                        this.ticketFeedback = data['feedback'];
                        this.ticketHistorial = data['logs'];
                    } catch (error) {
                        this.mostrarAlerta('error', 'Error', 'Ocurrio un error al obtener el ticket')
                    } finally {
                        this.loading = false;
                    }
                },
                async agregar(){
                    this.loading = true;
                    this.erroresRegistro = {};
                    try {
                        const response = await fetch('/tickets/registro-rest', {
                            method: 'POST', headers: this.headers, body: JSON.stringify(this.formTicket)
                        })
                        const data = await response.json().catch(() => ({}));

                        if (!response.ok) {
                            if (response.status === 422) {
                                this.erroresRegistro = data.errors;
                                this.mostrarAlerta('error', 'Error', data.message || 'Error de validación');
                            } else {
                                const mensaje = data.mensaje || data.message || (response.status === 403 ? 'No tienes permiso para realizar esta acción.' : 'Ocurrió un error al crear el ticket');
                                this.mostrarAlerta('error', 'Error', mensaje);
                            }
                            return;
                        }

                        this.modalRegistro.mostrar = false;
                        this.listarTickets();
                        this.mostrarAlerta('exito', 'Exito', data.mensaje || 'Ticket creado')
                    }  catch (error) {
                        this.mostrarAlerta('error', 'Error', 'Ocurrio un error de red al crear el ticket')
                    } finally {
                        this.loading = false;
                    }
                },
                async editar(){
                    this.loading = true;
                    this.erroresRegistro = {};
                    try {
                        const cliente = this.clientes.find(c => c.cliente_id === this.formTicket.cliente_id);
                        const proyectosDisponibles = this.proyectosCliente.length ? this.proyectosCliente : this.proyectos;
                        const proyecto = proyectosDisponibles.find(p => p.proyecto_id === this.formTicket.proyecto_id);
                        const etiqueta = this.etiquetas.find(e => e.etiquetaId === this.formTicket.etiqueta_id);
                        const usuario = this.usuarios.find(u => u.usuarioId === this.formTicket.usuario_asignado_id);

                        let payload = {
                            ...this.formTicket,
                            cliente: cliente ? cliente.nombre : null,
                            proyecto: proyecto ? proyecto.nombre : null,
                            etiqueta: etiqueta ? etiqueta.titulo : null,
                            usuario_asignado: usuario ? usuario.usuario : null
                        };

                        const response = await fetch(`/tickets/${this.ticket.ticketId}/edicion-rest/`, {
                            method: 'PATCH',
                            headers: this.headers,
                            body: JSON.stringify(payload)
                        });
                        const data = await response.json().catch(() => ({}));

                        if (!response.ok) {
                            if (response.status === 422) {
                                this.erroresRegistro = data.errors;
                                this.mostrarAlerta('error', 'Error', data.message || 'Error de validación');
                            } else {
                                const mensaje = data.mensaje || data.message || (response.status === 403 ? 'No tienes permiso para realizar esta acción.' : 'Ocurrió un error al editar el ticket');
                                this.mostrarAlerta('error', 'Error', mensaje);
                            }
                            return;
                        }

                        this.modalRegistro.mostrar = false;
                        this.listarTickets();
                        this.mostrarAlerta('exito', 'Exito', data.mensaje || 'Ticket editado')
                    }  catch (error) {
                        this.mostrarAlerta('error', 'Error', 'Ocurrio un error de red al editar el ticket')
                    } finally {
                        this.loading = false;
                    }
                },
                async editarStatus() {
                    this.loading = true;
                    try {
                        const response = await fetch(`/tickets/${this.ticket.ticketId}/status-rest/`, {
                            method: 'PATCH', headers: this.headers, body: JSON.stringify({status: this.ticket.status})
                        })
                        const data = await response.json().catch(() => ({}));

                        if (!response.ok) {
                            if (response.status === 422) {
                                this.erroresRegistro = data.errors;
                                this.mostrarAlerta('error', 'Error', data.message || 'Error de validación');
                            } else {
                                const mensaje = data.mensaje || data.message || (response.status === 403 ? 'No tienes permiso para realizar esta acción.' : 'Ocurrió un error al cambiar el status');
                                this.mostrarAlerta('error', 'Error', mensaje);
                            }
                            return;
                        }
                        this.mostrarAlerta('exito', 'Exito', data.mensaje || 'Estado editado')
                    } catch (error) {
                        this.mostrarAlerta('error', 'Error', 'Ocurrió un error de red al cambiar el status');
                    } finally {                        
                        this.loading = false;
                        await this.obtenerTicket(this.ticket.ticketId);
                        this.listarTickets();
                    }
                },
                async editarPrioridad() {
                    this.loading = true;
                    try {
                        const response = await fetch(`/tickets/${this.ticket.ticketId}/prioridad-rest/`, {
                            method: 'PATCH', headers: this.headers, body: JSON.stringify({prioridad: this.ticket.prioridad})
                        })
                        const data = await response.json().catch(() => ({}));

                        if (!response.ok) {
                            if (response.status === 422) {
                                this.erroresRegistro = data.errors;
                                this.mostrarAlerta('error', 'Error', data.message || 'Error de validación');
                            } else {
                                const mensaje = data.mensaje || data.message || (response.status === 403 ? 'No tienes permiso para realizar esta acción.' : 'Ocurrió un error al cambiar la prioridad');
                                this.mostrarAlerta('error', 'Error', mensaje);
                            }
                            return;
                        }
                        this.mostrarAlerta('exito', 'Exito', data.mensaje || 'Prioridad editada')
                    } catch (error) {
                        this.mostrarAlerta('error', 'Error', 'Ocurrió un error de red al cambiar la prioridad');
                    } finally {                        
                        this.loading = false;
                        await this.obtenerTicket(this.ticket.ticketId);
                        this.listarTickets();
                    }
                },
                async editarAsignacion() {
                    this.loading = true;
                    try {
                        const response = await fetch(`/tickets/${this.ticket.ticketId}/asignacion-rest/`, {
                            method: 'PATCH', headers: this.headers, body: JSON.stringify({usuario_asignado_id: this.ticket.usuarioAsignadoId})
                        })
                        const data = await response.json().catch(() => ({}));

                        if (!response.ok) {
                            if (response.status === 422) {
                                this.erroresRegistro = data.errors;
                                this.mostrarAlerta('error', 'Error', data.message || 'Error de validación');
                            } else {
                                const mensaje = data.mensaje || data.message || (response.status === 403 ? 'No tienes permiso para realizar esta acción.' : 'Ocurrió un error al cambiar la asignacion');
                                this.mostrarAlerta('error', 'Error', mensaje);
                            }
                            return;
                        }
                        this.mostrarAlerta('exito', 'Exito', data.mensaje || 'Asignacion editada')
                    } catch (error) {
                        this.mostrarAlerta('error', 'Error', 'Ocurrió un error de red al cambiar la asignacion');
                    } finally {                        
                        this.loading = false;
                        await this.obtenerTicket(this.ticket.ticketId);
                        this.listarTickets();
                    }
                },
                async agregarFeedback(){
                    this.loading = true;
                    this.erroresRegistroFeedback = {};
                    try {
                        const response = await fetch(`/tickets/${this.ticket.ticketId}/feedback-rest`, {
                            method: 'POST', headers: this.headers, body: JSON.stringify(this.formFeedback)
                        })
                        const data = await response.json().catch(() => ({}));

                        if (!response.ok) {
                            if (response.status === 422) {
                                this.erroresRegistroFeedback = data.errors;
                                this.mostrarAlerta('error', 'Error', data.message || 'Error de validación');
                            } else {
                                const mensaje = data.mensaje || data.message || (response.status === 403 ? 'No tienes permiso para realizar esta acción.' : 'Ocurrió un error al agregar el comentario');
                                this.mostrarAlerta('error', 'Error', mensaje);
                            }
                            return;
                        }

                        this.formFeedback.comentario = '';
                        await this.obtenerTicket(this.ticket.ticketId); 

                        this.mostrarAlerta('exito', 'Exito', data.mensaje || 'Comentario Agregado')
                    } catch (error){
                        this.mostrarAlerta('error', 'Error', 'Ocurrio un error de red al agregar el comentario')
                    } finally {
                        this.loading = false;
                    } 
                },
                actualizarProyectos(cliente_id){
                    this.proyectosCliente = this.proyectos.filter(p => p.cliente_id === cliente_id);
                },
                seleccionarCliente(){
                    this.actualizarProyectos(this.formTicket.cliente_id);
                    this.formTicket.proyecto_id = null;
                }
            },  
        })
        app.component('modal-componente', modal)
        app.component('alerta-componente', alerta)
        app.component('loader-global', loader);
        app.mount('#app')
    </script>
@endsection