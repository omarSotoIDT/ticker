var alerta = {
  template: `
      <div class="alerta" :class="{ activo: mostrar}">
        <div class="alerta-contenido">
            <i class="icono" :class="claseIcono"></i>
            <div class="mensaje">
                <span class="texto titulo-alerta">{{ titulo }}</span>
                <span class="texto">{{ mensaje }}</span>
            </div>
        </div>
    </div>
      `,
  props: {
    mostrar: { type: Boolean, default: false },
    tipo: { type: String, default: "info" },
    titulo: { type: String, default: "" },
    mensaje: { type: String, default: "" },
  },
  emits: ['update:mostrar'],
  watch: {
    mostrar(newVal) {
      if (newVal) {
        setTimeout(() => {
          this.$emit('update:mostrar', false);
        }, 5000);
      }
    }
  },
  computed: {
    claseIcono(){
      switch(this.tipo) {
        case 'exito': return 'fa fa-check alerta-exito';
        case 'error': return 'fa fa-times alerta-error';
        case 'info':  return 'fa fa-info alerta-info';
        default:      return 'fa fa-info alerta-info';
      }
    }
  }
};