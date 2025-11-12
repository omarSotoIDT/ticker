var modal = {
  template: `
    <transition name="animation-fade">
      <div v-if="mostrar" class="fondo-modal" @click.self="close">
        <div class="modal" :class="claseModal">
          <button class="cerrar-modal" @click="close">&times;</button>
          <h2 class="titulo-modal">{{ titulo }}</h2>
          <p class="subtitulo-modal">{{ subtitulo }}</p>
          <slot></slot>
          <div class="acciones-modal" v-if="mostrarBotones">
              <button @click="close" class="btn secondary-btn">Cancelar</button>
              <button @click="$emit('confirmar')" class="btn primary-btn" :disabled="deshabilitarConfirmacion">{{ textoConfirmacion }}</button>
          </div>
        </div>
      </div>
    </transition>
      `,
  props: {
    mostrar: { type: Boolean, default: false },
    titulo: { type: String, default: "" },
    subtitulo: { type: String, default: "" },
    textoConfirmacion: { type: String, default: "Aceptar" },
    mostrarBotones: { type: Boolean, default: true },
    claseModal: { type: String },
    deshabilitarConfirmacion: { type: Boolean, default: false },
  },
  emits: ["update:mostrar", "limpiar", "confirmar"],
  methods: {
    close() {
      this.$emit("update:mostrar", false);
      this.$emit("limpiar");
    },
  },
};
