<script type="text/x-template" id="alerta-template">
    <div class="alerta" :class="{ activo: visible}">
        <div class="alerta-contenido">
            <i class="icono" :class="[
                tipo === 'exito' ? 'fa fa-check alerta-exito' :
                tipo === 'error' ? 'fa fa-times alerta-error' :
                tipo === 'info' ? 'fa fa-info alerta-info' :
                'fa fa-info'
            ]"></i>
            <div class="mensaje">
                <span class="texto titulo-alerta">@{{ titulo }}</span>
                <span class="texto">@{{ mensaje }}</span>
            </div>
        </div>
    </div>
</script>