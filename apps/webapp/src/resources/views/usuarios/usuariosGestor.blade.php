@extends('layout.Layout')

@section('titulo', 'Gestor de usuarios')

@section('contenido')
  <div id="app">
    {{-- Loader: Basado en Proyectos.blade.php --}}
    <loading-global :visible="loading"></loading-global>
      <div class="modulo-encabezado">
          <div class="items-busqueda">
              <div class="cont-buscador">
                  <i class="fa-solid fa-magnifying-glass buscador-icono"></i>
                  <input type="text" name="usuario" id="usuario" class="input input-busqueda" v-model="busqueda.titulo" @change="buscar()" placeholder="Buscar usuarios..."></input>
              </div>
          </div>
          <button class="btn primary-btn" @click.prevent="modalCrear()">+ Nuevo Usuario</button>
      </div>
      <table class="tabla">
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Email</th>
            <th>Perfil</th>
            <th>Estado</th>
            <th>Último acceso</th>
            <th class="acciones">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="usuario in usuarios" :key="usuario.usuarioId">
            <td>@{{ usuario.usuario }}</td>
            <td>@{{ usuario.email }}</td>
            <td>@{{ usuario.nombrePerfil }}</td>
            <td>
              <span class="badge" :class="badgeStatus(usuario.status)">@{{ usuario.status }}</span>
            </td>
            <td>@{{ usuario.acceso }}</td>
            <td class="acciones">
              <button @click.prevent="modalEditar(usuario.usuarioId)"><i class="fa fa-pen"></i></button>
              <button v-if="usuario.status === 'ACTIVO'" @click.prevent="modalEliminar(usuario.usuarioId)"><i class="fa fa-trash"></i></button>
              <button v-if="usuario.status === 'ELIMINADO'" @click.prevent="modalActivar(usuario.usuarioId)"><i class="fa fa-arrow-rotate-left"></i></button>
            </td>
          </tr>
        </tbody>
      </table>
      @if (session('error'))
        <div class="error">
          <span>{{ session('error') }}</span>
        </div>
      @endif
      <modal-componente 
        v-model:mostrar="mostrarModal" 
        :titulo="tipoForm === 'crear' ? 'Nuevo Usuario' : 'Editar Usuario'"
        :subtitulo="tipoForm === 'crear' ? 'Completa los datos del nuevo usuario' : 'Modifica los datos del usuario'" 
        :texto-Confirmacion="tipoForm === 'crear' ? 'Crear usuario' : 'Guardar Cambios'" 
        @limpiar="limpiarErrores"
        @confirmar="tipoForm === 'crear' ? crear() : tipoForm === 'editar' ? editar() : ''">

        <form id="form" class="form centrado">
          <div class="campo">
            <label class="etiqueta" for="nombre">Nombre</label>
            <input class="input" type="text" name="nombre" id="nombre" v-model="formUsuario.nombre">
            <span class="error" v-if="erroresModal.nombre">@{{ erroresModal.nombre[0] }}</span>
          </div>
          <div class="campo">
            <label class="etiqueta" for="email">Email</label>
            <input class="input" type="email" name="email" id="email" v-model="formUsuario.email">
            <span class="error" v-if="erroresModal.email">@{{ erroresModal.email[0] }}</span>
          </div>
          <div class="campo">
            <label class="etiqueta" for="pasword">Contraseña</label>
            <input class="input" type="password" name="password" id="password" v-model="formUsuario.password">
            <span class="error" v-if="erroresModal.password">@{{ erroresModal.password[0] }}</span>
          </div>
          <div class="campo">
            <label class="etiqueta" for="perfil">Perfil</label>
            <select class="input" name="perfil" id="perfil" v-model="formUsuario.perfil"></select>
              
            <span class="error" v-if="erroresModal.perfil">@{{ erroresModal.perfil[0] }}</span>
          </div>
        </form>
      </modal-componente>

      <modal-componente 
        v-model:mostrar="mostrarCambiarStatus" 
        :titulo="tipoForm === 'eliminar' ? 'Eliminar Usuario' : tipoForm === 'activar' ? 'Activar Usuario' : ''" 
        :subtitulo="tipoForm === 'eliminar' ? '¿Deseas eliminar al siguiente usuario?' : tipoForm === 'activar' ? '¿Deseas activar al siguiente usuario?' : ''" 
        texto-Confirmacion="Confirmar" 
        @limpiar="limpiarErrores"
        @confirmar="tipoForm === 'eliminar' ? eliminar() : tipoForm === 'activar' ? activar() : ''">
        <form id="form">
          <p>@{{ usuario.usuarioId }} - @{{ usuario.usuario }}</p>
          <div class="campo" v-if="tipoForm === 'eliminar'">
            <label class="etiqueta" for="motivo">Motivo</label>
            <textarea class="input" name="motivo" id="motivo" placeholder="Ingresa un motivo" v-model="formEliminar.motivo"></textarea>
            <span class="error" v-if="erroresModal.motivo">@{{ erroresModal.motivo[0] }}</span>
          </div>
        </form>
      </modal-componente>

      <alerta-componente
      :mostrar="alerta.mostrar"
      :tipo="alerta.tipo"
      :titulo="alerta.titulo"
      :mensaje="alerta.mensaje"
      >
      </alerta-componente>
    </div>
  </div>

  <script>
    const app = Vue.createApp({
      data() {
        return { 
          busqueda: '{{ request('usuario') }}',
          mostrarModal: false,
          mostrarCambiarStatus: false,
          tipoForm: 'crear',
          formUsuario: {
            nombre: '',
            email: '',
            password: '',
            perfil: '',
          },
          formEliminar: {
            motivo: ''
          },
          token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          usuarios: null,
          usuario: null,
          erroresModal: {},
          alerta: {
            mostrar: false,
            tipo: '',
            titulo: '',
            mensaje: ''
          },
          loading: false
        }
      },
      methods: {
        modalCrear(){
          this.tipoForm = 'crear';
          this.formUsuario.nombre = '';
          this.formUsuario.email = '';
          this.formUsuario.password = '';
          this.mostrarModal = true;
        },
  
        modalEditar(id){
          this.usuario = this.usuarios.find(u => u.usuarioId === id);
          if (!this.usuario) return;
          this.tipoForm = 'editar';
          this.formUsuario.nombre = this.usuario.usuario;
          this.formUsuario.email = this.usuario.email;
          this.formUsuario.password = ''; 
          this.mostrarModal = true;
        },
  
        modalEliminar(id){
          this.usuario = this.usuarios.find(u => u.usuarioId === id);
          if (!this.usuario) return;
          this.tipoForm = 'eliminar';
          this.formEliminar.motivo = '';
          this.mostrarCambiarStatus = true;
        },
  
        modalActivar(id){
          this.usuario = this.usuarios.find(u => u.usuarioId === id);
          if (!this.usuario) return;
          this.tipoForm = 'activar';
          this.mostrarCambiarStatus = true;
        },

        mostrarAlerta(tipo, titulo, mensaje){
          this.alerta.tipo = tipo;
          this.alerta.titulo = titulo;
          this.alerta.mensaje = mensaje;
          this.alerta.mostrar = true;
          setTimeout(() => {
            this.alerta.mostrar = false;
          }, 3000);
        },

        limpiarErrores(){
          this.erroresModal = {};
        },

        async listarUsuarios() {
          this.loading = true;
          try{
            const response = await fetch('/usuarios/listarRest' , {
              method: 'GET',  
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.token
              }
            })

            if(!response.ok){
              throw new Error('Error al listar usuarios: ' + response.status);
            }

            const data = await response.json();
            this.usuarios = data;
            this.busqueda = '';
          }catch(error){
            this.mostrarAlerta('error', 'Error', 'Ocurrio un error al listar los usuarios');
          } finally {
            this.loading = false;
          }
        },

        async buscar(){
          this.loading = true;
          try {
            const params = new URLSearchParams();
            if (this.busqueda) params.append('usuario', this.busqueda);
            const response = await fetch('/usuarios/listarRest?' + params.toString(), {
              method: 'GET',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.token
              }
            })

            if (!response.ok) {
              throw new Error('Error al buscar usuarios: ' + response.status);
            }

            const data = await response.json();
            this.usuarios = data;
          }catch(error) {
            this.mostrarAlerta('error', 'Error', 'Ocurrio un error al buscar');
          } finally {
            this.loading = false;
          }
        },

        async crear() {
          this.loading = true;
          try {
            const response = await fetch('/usuarios/agregarRest', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.token
              },
              body: JSON.stringify(this.formUsuario)
            })

            if(!response.ok) {
              if (response.status === 422) {
                const datosError = await response.json();
                this.erroresModal = datosError.errors;
                return;
              }
              throw new Error('Error al crear el usuario: ' + response.status);
            }

            const data = await response.json();
            this.listarUsuarios();
            this.mostrarModal = false;
            this.mostrarAlerta('exito', 'Exito', 'Usuario creado');
          }catch(error) {
            this.mostrarAlerta('error', 'Error', 'Ocurrio un error al crear el usuario');
          } finally {
            this.loading = false;
          }
        },

        async editar(){
          this.loading = true;
          try {
            const response = await fetch('/usuarios/editarRest/' + this.usuario.usuarioId, {
              method: 'PATCH',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.token
              },
              body: JSON.stringify(this.formUsuario)
            })

            if(!response.ok) {
              if (response.status === 422) {
                const datosError = await response.json();
                this.erroresModal = datosError.errors;
                return;
              }
              throw new Error('Error al editar el usuario: ' + response.status);
            }

            this.listarUsuarios();
            this.mostrarModal = false;
            this.mostrarAlerta('exito', 'Exito', 'Usuario actalizado');

          }catch(error) {
            this.mostrarAlerta('error', 'Error', 'Ocurrio un error al editar el usuario');
          } finally {
            this.loading = false;
          }
        },

        async eliminar(){
          this.loading = true;
          try {
            const response = await fetch('/usuarios/eliminarRest/' + this.usuario.usuarioId, {
              method: 'PATCH',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.token
              },
              body: JSON.stringify(this.formEliminar)
            })

            if(!response.ok) {
              if (response.status === 422) {
                const datosError = await response.json();
                this.erroresModal = datosError.errors;
                return;
              }
              throw new Error('Error al eliminar el usuario: ' + response.status);
            }

            this.listarUsuarios();
            this.mostrarCambiarStatus = false;
            this.mostrarAlerta('exito', 'Exito', 'Usuario eliminado');
          }catch(error) {
            this.mostrarAlerta('error', 'Error', 'Ocurrio un error al eliminar el usuario');
          } finally {
            this.loading = false;
          }
        },

        async activar(){
          this.loading = true;
          try {
            const response = await fetch('/usuarios/activarRest/' + this.usuario.usuarioId, {
              method: 'PATCH',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.token
              }
            })

            if(!response.ok) {
              if (response.status === 422) {
                const datosError = await response.json();
                this.erroresModal = datosError.errors;
                return;
              }
              throw new Error('Error al activar el usuario: ' + response.status);
            }

            this.listarUsuarios();
            this.mostrarCambiarStatus = false;
            this.mostrarAlerta('exito', 'Exito', 'Usuario Activado');
          }catch(error) {
            this.mostrarAlerta('error', 'Error', 'Ocurrio un error al activar el usuario');
          } finally {
            this.loading = false;
          }
        },

        badgeStatus(status) {
          if (status === 'ACTIVO') {
            return 'badge-activo';
          } else if (status === 'INACTIVO') {
            return 'badge-inactivo';
          }
        },
      },
      mounted() {
        this.listarUsuarios();
      }
    });

    app.component('modal-componente', modal)
    app.component('alerta-componente', alerta)
    app.component('loading-global', loader)
    app.mount('#app')
  </script>
@endsection