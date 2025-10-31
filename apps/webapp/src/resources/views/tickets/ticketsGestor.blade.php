@extends('layout.Layout')

@section('title', 'Tickets')

@section('contenido')
    <div id="app">
        <div class="modulo-encabezado">
            <div class="items-busqueda">
                <div class="cont-buscador">
                    <i class="fa-solid fa-magnifying-glass buscador-icono"></i>
                    <input type="text" name="ticket" id="ticket" class="input inputBusqueda" v-model="busqueda.titulo" @change="buscar()" placeholder="Buscar tickets..."></input>
                </div>
                <select id="busquedaCliente" class="input select-busqueda" v-model="busqueda.cliente_id" @change="buscar()">
                    <option value="">Todos</option>
                    <option v-for="cliente in clientes" :value="cliente.cliente_id">@{{ cliente }}</option>
                </select>
                <select id="busquedaPrioridad" class="input select-busqueda" v-model="busqueda.prioridad" @change="buscar()">
                    <option value="">Todas</option>
                    <option v-for="prioridad in prioridades" :value="prioridad">@{{ prioridad }}</option>
                </select>
            </div>
            <button class="btn primary-btn" @click.prevent="modalCrear()">+ Nuevo Ticket</button>
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
                        @{{ ticket.titulo }} - @{{ ticket.etiqueta }}
                    </td>
                    <td>@{{ ticket.cliente }}</td>
                    <td>@{{ ticket.proyecto }}</td>
                    <td>@{{ ticket.usuarioAsignado }}</td>
                    <td>@{{ ticket.status }}</td>
                    <td>@{{ ticket.prioridad }}</td>
                    <td>@{{ ticket.registroFecha }}</td>
                    <td class="acciones">
                        <button @click.prevent="mostrarTicket(ticket.ticketId)"><i class="fa fa-eye"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>

        <modal-componente
        v-model:mostrar="modalRegistro.mostrar"
        :titulo="modalRegistro.tipo === 'crear' ? 'Nuevo Ticket' : 'Editar Ticket'"
        :subtitulo="modalRegistro.tipo === 'crear' ? 'Completa los datos del nuevo Ticket' : 'Modifica los datos del Ticket'"
        :texto-confirmacion="modalRegistro.tipo === 'crear' ? 'Crear Ticket' : 'Guardar Cambios'"
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
                        <select class="input" name="cliente" id="cliente" v-model="formTicket.cliente_id">
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
                            <option v-for="proyecto in proyectos" :key="proyecto.proyecto_id" :value="proyecto.proyecto_id">
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
            dialog-class="modal-grande modal-vista-detalle"
            @close="limparModalVer"
        >
        
            <button class="btn secondary-btn edit-btn" @click.prevent="modalEditar(ticket.ticketId)" title="Editar Ticket">
                <i class="fa fa-pen-to-square"></i> Editar
            </button>

            <div v-if="ticket" class="vista-detalle-contenedor">
                <div class="vista-detalle-header-status grid-3-col"> 
                    <div class="campo"> 
                        <label class="etiqueta">Estado</label>
                        <select class="input" v-model="ticket.status" @change="actualizarTicketInline('status', $event.target.value)">
                            <option v-for="estado in estados" :value="estado">@{{ estado }}</option>
                        </select>
                    </div>
                    <div class="campo">
                        <label class="etiqueta">Prioridad</label>
                        <select class="input" v-model="ticket.prioridad" @change="actualizarTicketInline('prioridad', $event.target.value)">
                            <option v-for="prioridad in prioridades" :value="prioridad">@{{ prioridad }}</option>
                        </select>
                    </div>
                    <div class="campo">
                        <label class="etiqueta">Asignado a</label>
                        <select class="input" v-model="ticket.usuarioAsignadoId" @change="actualizarTicketInline('usuario_asignado_id', $event.target.value)">
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
                            <li v-for="item in ticketHistorial" :key="item.id">
                                <span class="historial-fecha-usuario">@{{ item.fecha }} - <strong>@{{ item.usuario }}</strong></span>
                                <span class="historial-cambio">
                                    @{{ item.campo }}:
                                    <span class="dato-antiguo">@{{ item.valor_antiguo }}</span> &rarr; <span class="dato-nuevo">@{{ item.valor_nuevo }}</span>
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
                    estados: [ABIERTO, EN_PROGRESO, ATENDIDO, CERRADO, INFO_REQUERIDA, CANCELADO],
                    clientes: {},
                    proyectos: {},
                    usuarios: {{ JS::from($usuarios) }},
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
                async buscar(){
                    try {
                        const params = new URLSearchParams();
                        if(this.busqueda){
                            params.append('titulo', this.busqueda.titulo)
                            params.append('cliente_id', this.busqueda.cliente_id)
                            params.append('prioridad', this.busqueda.prioridad)
                        }
                        const response = await fetch('tickets/listado-rest?' + params.toString(), {
                            method: 'GET', headers: this.headers
                        })

                        if (!response.ok) {
                            throw new Error('Error al buscar los tickets: ' + response.status);
                        }
                        const data = await response.json();
                        this.tickets = data;
                    } catch (error) {
                        this.mostrarAlerta('error', 'Error', 'Ocurrio un error al buscar los tickets')
                    }
                },
                modalCrear(){
                    this.modalRegistro.tipo = 'crear';
                    this.limpiarRegistro();
                    this.modalRegistro.mostrar = true;
                },
                modalEditar(id){
                    if (this.modalVer.mostrar) {
                        this.modalVer.mostrar = false;
                    }
                    this.modalRegistro.tipo = 'editar';
                    this.erroresRegistro = {};
                    this.cargarForm();
                    this.modalRegistro.mostrar = true;
                },
                async listarTickets(){
                    try {
                        const response = await fetch('/tickets/listado-rest', {
                            method: 'GET', headers: this.headers
                        })

                        if (!response.ok) {
                            throw new Error('Error al listar los tickets: ' + response.status);                        
                        }
                        
                        const data = await response.json();
                        this.tickets = data;
                        this.busqueda = '';
                        this.modalRegistro.mostrar = false;
                    } catch (error) {
                        this.mostrarAlerta('error', 'Error', 'Ocurrio un error al listar los tickets')
                    }
                },
                async mostrarTicket(id){
                    await this.obtenerTicket(id); 
                    this.ticketHistorial;
                    if(this.ticket){
                        this.modalVer.seccion = 'descripcion'; 
                        this.formFeedback.comentario = ''; 
                        this.erroresRegistroFeedback = {}; 
                        this.modalVer.mostrar = true; 
                    }
                },
                async obtenerTicket(id){
                    try {
<<<<<<< HEAD
                        const response = await fetch(`/tickets/${id}/detalle-rest`, {
=======
                        const response = await fetch('/tickets/${id}/detalle-rest/' + id, {
>>>>>>> bebe68e (style: implementa cambios en la vista de ticketsGestor y en la hoja de estilos)
                            method: 'GET', headers: this.headers
                        })

                        if (!response.ok) {
                            throw new Error('Error al obtener el ticket: ' + response.status);
                        }
                        
                        const data = await response.json();
                        this.ticket = data['ticket'];
                        this.ticketFeedback = data['feedback'];
                    } catch (error) {
                        this.mostrarAlerta('error', 'Error', 'Ocurrio un error al obtener el ticket')
                    }
                },
                async agregar(){
                    try {
                        const response = await fetch('/tickets/registro-rest', {
                            method: 'POST', headers: this.headers, body: JSON.stringify(this.formTicket)
                        })

                        if (!response.ok) {
                            if (response.status === 422) {
                                const datosError = await response.json();
                                this.erroresRegistro = datosError.errors;
                                return;
                            }
                            throw new Error('Error al crear el ticket: ' + response.status);
                        }

                        this.modalRegistro.mostrar = false;
                        this.listarTickets();
                        this.mostrarAlerta('exito', 'Exito', 'Ticket creado')
                    } catch (error) {
                        this.mostrarAlerta('error', 'Error', 'Ocurrio un error al crear el ticket')
                    }
                },
                async editar(){
                    try {
                        const response = await fetch(`/tickets/${this.ticket.ticketId}/edicion-rest/`, {
                            method: 'PATCH', headers: this.headers, body: JSON.stringify(this.formTicket)
                        })

                        if (!response.ok) {
                            if (response.status === 422) {
                                const datosError = await response.json();
                                this.erroresRegistro = datosError.errors;
                                return;
                            }
                            throw new Error('Error al crear el ticket: ' + response.status);
                        }

                        this.modalRegistro.mostrar = false;
                        this.listarTickets();
                        this.mostrarAlerta('exito', 'Exito', 'Ticket editado')
                    } catch (error) {
                        this.mostrarAlerta('error', 'Error', 'Ocurrio un error al editar el ticket')
                    }
                },
                async actualizarTicketInline(campo, valor) {
                    try {
                        console.log(`Actualizando campo ${campo} a ${valor} para ticket ${this.ticket.ticketId}`);
                        
                        if (campo === 'usuario_asignado_id') {
                            this.ticket.usuarioAsignadoId = valor;
                            const usuarioAsignado = this.usuarios.find(u => u.usuarioId == valor);
                            this.ticket.usuarioAsignado = usuarioAsignado ? usuarioAsignado.usuario : 'Sin asignar';
                        } else {
                            this.ticket[campo] = valor;
                        }

                        this.mostrarAlerta('exito', 'Actualizado', `Campo '${campo}' actualizado.`);

                    } catch (error) {
                        this.mostrarAlerta('error', 'Error', 'Ocurrió un error al actualizar el campo');
                    }
                },
                async agregarFeedback(){
                    try {
                        const response = await fetch(`/tickets/${this.ticket.ticketId}/feedback-rest/`, {
                            method: 'POST', headers: this.headers, body: JSON.stringify()
                        })

                        if (!response.ok) {
                            if (response.status === 422) {
                                const datosError = await response.json();
                                this.erroresRegistroFeedback = datosError.errors;
                                return;
                            }
                            throw new Error('Error al agregar feedback: ' + response.status);
                        }
                        
                        this.formFeedback.comentario = '';
                        this.erroresRegistroFeedback = {};
                        await this.obtenerTicket(this.ticket.ticketId); 

                        this.mostrarAlerta('exito', 'Exito', 'Comentario Agregado')
                    } catch (error){
                        this.mostrarAlerta('error', 'Error', 'Ocurrio un error al agregar el comentario')
                    } 
                }
            }
        })
        app.component('modal-componente', modal)
        app.component('alerta-componente', alerta)
        app.mount('#app')
    </script>
@endsection