@extends('layout.Layout')

@section('titulo', 'Perfiles')

@section('contenido')

<div id="app" 
    class="clientes"
    data-perfiles='@json($perfilesConPermisos)'
    data-permisos='@json($permisos)'
    data-exito='@json(session("exito"))'
    data-error='@json(session("error"))'>
    
    <div class="modulo-encabezado">
        <form method="GET" action="{{ route('perfiles.index') }}" class="cont-buscador">
            <i class="fa-solid fa-magnifying-glass buscador-icono"></i>
            <input type="text" name="busqueda" class="inputBusqueda" placeholder="Buscar perfiles..." value="{{ $busqueda ?? '' }}">
        </form>
        <button class="btn action-btn" @click.prevent="modalCrear"><i class="fa fa-plus"></i> Nuevo Perfil</button>
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
                    
                    <form :id="'form-eliminar-' + perfil.perfil_id" :action="routeEliminar(perfil.perfil_id)" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="button" @click.prevent="abrirModalEliminar(perfil.perfil_id)" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            <tr v-if="!perfiles.length">
                <td colspan="5" style="text-align:center; padding:1rem;">No hay perfiles registrados.</td>
            </tr>
        </tbody>
    </table>

    <modal-componente 
        v-model:mostrar="mostrarModal"
        :titulo="tipoForm === 'crear' ? 'Nuevo Perfil' : 'Editar Perfil'"
        :subtitulo="tipoForm === 'crear' ? 'Completa los datos del nuevo perfil' : 'Modifica los datos del perfil'"
        :texto-confirmacion="tipoForm === 'crear' ? 'Crear Perfil' : 'Guardar Cambios'"
        @confirmar="guardar">

        <form id="form" class="form centrado" @submit.prevent="guardar" :action="formAction" method="POST" novalidate>
            @csrf
            <input v-if="tipoForm === 'editar'" type="hidden" name="_method" value="PUT">
            <input v-if="tipoForm === 'crear'" type="hidden" name="status" value="ACTIVO">
            <input type="hidden" name="super_usuario" value="0">

            <div class="campo">
                <label class="etiqueta" for="clave">Clave</label>
                <input class="input" :class="{'input-error': errors.clave}" type="text" name="clave" id="clave" v-model="formPerfil.clave">
                <span v-if="errors.clave" class="error-message">@{{ errors.clave }}</span>
            </div>

            <div class="campo">
                <label class="etiqueta" for="nombre">Nombre</label>
                <input class="input" :class="{'input-error': errors.nombre}" type="text" name="nombre" id="nombre" v-model="formPerfil.nombre">
                <span v-if="errors.nombre" class="error-message">@{{ errors.nombre }}</span>
            </div>

            <div class="campo">
                <label class="etiqueta" for="descripcion">Descripción</label>
                <textarea class="input" :class="{'input-error': errors.descripcion}" name="descripcion" id="descripcion" v-model="formPerfil.descripcion" rows="3"></textarea>
                <span v-if="errors.descripcion" class="error-message">@{{ errors.descripcion }}</span>
            </div>
            
            <div class="campo" v-if="tipoForm === 'editar'">
                <label class="etiqueta" for="status">Status</label>
                <select class="input" name="status" v-model="formPerfil.status">
                    <option value="ACTIVO">ACTIVO</option>
                    <option value="ELIMINADO">ELIMINADO</option>
                </select>
            </div>

            <div class="campo">
                <label class="etiqueta">Permisos</label>
                <div class="contenedor-checklist">
                    <div v-for="permiso in permisos" :key="permiso.permiso_id" class="item-contenedor">
                        <input type="checkbox" name="permisos[]" :id="'permiso-' + permiso.permiso_id" :value="permiso.permiso_id" v-model="formPerfil.permisos">
                        <label :for="'permiso-' + permiso.permiso_id">
                            <span class="titulo-item">@{{ permiso.titulo }}</span>
                            <span class="descripcion-item">@{{ permiso.descripcion }}</span>
                        </label>
                    </div>
                </div>
            </div>
        </form>
    </modal-componente>

    <modal-componente 
        v-model:mostrar="mostrarModalEliminar"
        titulo="Confirmar Eliminación"
        texto-confirmacion="Sí, Eliminar"
        @confirmar="ejecutarEliminacion">
        ¿Estás seguro de que deseas eliminar este perfil?.<strong> Esta acción no se puede deshacer.</strong>
    </modal-componente>

    <alerta-componente
        :mostrar="alerta.mostrar"
        :tipo="alerta.tipo"
        :titulo="alerta.titulo"
        :mensaje="alerta.mensaje"
    >
    </alerta-componente>

</div>

<script>
    const app = Vue.createApp({
        data() {
            const appElement = document.getElementById('app');
            const exito = JSON.parse(appElement.dataset.exito || 'null');
            const error = JSON.parse(appElement.dataset.error || 'null');

            return {
                mostrarModal: false,
                tipoForm: 'crear',
                formPerfil: {
                    clave: '', nombre: '', descripcion: '', status: 'ACTIVO', permisos: [], perfil_id: null
                },
                errors: {},

                mostrarModalEliminar: false,
                perfilAEliminar: null,

                perfiles: JSON.parse(appElement.dataset.perfiles || '[]'),
                permisos: JSON.parse(appElement.dataset.permisos || '[]'),
                
                routeGuardar: "{{ route('perfiles.guardar') }}",
                routeActualizarBase: "{{ url('/perfiles') }}",
                routeEliminarBase: "{{ url('/perfiles') }}",

                alerta: {
                    mostrar: exito || error ? true : false,
                    tipo: exito ? 'exito' : (error ? 'error' : ''),
                    titulo: exito ? 'Éxito' : (error ? 'Error' : ''),
                    mensaje: exito ? exito : (error ? error : '')
                }
            };
        },
        computed: {
            formAction() {
                return this.tipoForm === 'crear'
                    ? this.routeGuardar
                    : `${this.routeActualizarBase}/${this.formPerfil.perfil_id}`;
            }
        },
        methods: {
            validateForm() {
                this.errors = {};
                if (!this.formPerfil.clave) this.errors.clave = 'El campo Clave no debe ir vacío.';
                if (!this.formPerfil.nombre) this.errors.nombre = 'El campo Nombre no debe ir vacío.';
                if (!this.formPerfil.descripcion) this.errors.descripcion = 'El campo Descripción no debe ir vacío.';
                
                if (Object.keys(this.errors).length > 0) {
                    this.alerta = {
                        mostrar: true,
                        tipo: 'error',
                        titulo: 'Campos incompletos',
                        mensaje: 'Por favor, revisa los campos marcados en rojo.'
                    };
                    setTimeout(() => { this.alerta.mostrar = false }, 3000);
                }

                return Object.keys(this.errors).length === 0;
            },
            guardar() {
                if (this.validateForm()) {
                    document.getElementById('form').submit();
                }
            },
            modalCrear() {
                this.errors = {}; 
                this.tipoForm = 'crear';
                this.formPerfil = { clave: '', nombre: '', descripcion: '', status: 'ACTIVO', permisos: [], perfil_id: null };
                this.mostrarModal = true;
            },
            modalEditar(perfilId) {
                this.errors = {}; 
                const perfil = this.perfiles.find(p => p.perfil_id === perfilId);
                if (!perfil) return;
                
                this.tipoForm = 'editar';
                this.formPerfil = {
                    clave: perfil.clave,
                    nombre: perfil.nombre,
                    descripcion: perfil.descripcion,
                    status: perfil.status,
                    permisos: perfil.permisos,
                    perfil_id: perfil.perfil_id
                };
                this.mostrarModal = true;
            },
            routeEliminar(perfilId) {
                return `${this.routeEliminarBase}/${perfilId}`;
            },

            abrirModalEliminar(perfilId) {
                this.perfilAEliminar = perfilId; 
                this.mostrarModalEliminar = true; 
            },

            ejecutarEliminacion() {
                if (this.perfilAEliminar) {
                    const formId = `form-eliminar-${this.perfilAEliminar}`;
                    const form = document.getElementById(formId);
                    if (form) {
                        form.submit();
                    }
                }
                this.mostrarModalEliminar = false; 
            }
        },
        mounted() {
            if (this.alerta.mostrar) {
                setTimeout(() => {
                    this.alerta.mostrar = false;
                }, 3000);
            }
        }
    });

    app.component('modal-componente', modal);
    app.component('alerta-componente', alerta);
    app.mount('#app');
</script>
@endsection