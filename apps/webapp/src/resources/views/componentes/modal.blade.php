<script type="text/x-template" id="modal-template">
    <div v-if="mostrar" class="fondo-modal" @click.self="close">
        <div class="modal">
            <button class="cerrar-modal" @click="close">&times;</button>
            <h2 class="titulo-modal">@{{ titulo }}</h2>
            <p class="subtitulo-modal">@{{ subtitulo }}</p>
            <slot></slot>
            <div class="acciones-modal" v-if="mostrarBotones">
                <button @click="close" class="btn secondary-btn">Cancelar</button>
                <button @click="$emit('confirmar')" class=" btn primary-btn">@{{ textoConfirmacion }}</button>
            </div>
        </div>
    </div>
</script>
