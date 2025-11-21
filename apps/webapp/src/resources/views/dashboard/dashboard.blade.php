@extends('layout.Layout')
@section('titulo', 'Dashboard')

@section('contenido')
<div id="dashboard-root">
    <div class="fila-tarjetas">
        <div class="tarjeta-estadistica" v-for="(card, index) in cards" :key="index">
            <div class="tarjeta-contenido">
                <div class="tarjeta-encabezado">
                    <div class="tarjeta-titulo">@{{ card.titulo }}</div>
                    <i :class="card.icono + ' tarjeta-icono'" :style="{ color: card.color }"></i>
                </div>
                <div class="tarjeta-cuerpo">
                    <div class="tarjeta-valor">@{{ card.valor }}</div>
                    <div class="tarjeta-descripcion">@{{ card.descripcion }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="cuadricula-graficas">
        <!-- Tickets por Estado -->
        <div class="tarjeta-grafica">
            <h3>Tickets por Estado</h3>
            <div class="lienzo-grafica" v-if="estado.labels && estado.labels.length">
                <canvas ref="chartEstado"></canvas>
            </div>
            <div v-else class="mensaje-vacio">No hay datos suficientes para graficar</div>
        </div>

        <!-- Tickets por Prioridad -->
        <div class="tarjeta-grafica">
            <h3>Tickets por Prioridad</h3>
            <div class="lienzo-grafica" v-if="prioridad.labels && prioridad.labels.length">
                <canvas ref="chartPrioridad"></canvas>
            </div>
            <div v-else class="mensaje-vacio">No hay datos suficientes para graficar</div>
        </div>

        <!-- Top 5 Clientes -->
        <div class="tarjeta-grafica grafica-ancho-completo">
            <h3>Top 5 Clientes por Tickets</h3>
            <div class="lienzo-grafica" v-if="topClientes.labels && topClientes.labels.length">
                <canvas ref="chartTopClientes"></canvas>
            </div>
            <div v-else class="mensaje-vacio">No hay datos suficientes para graficar</div>
        </div>
    </div>
</div>

<script>
const { createApp, ref, onMounted } = Vue;

createApp({
    setup() {
        const estado = ref({{ Js::from($ticketsPorEstado) }});
        const prioridad = ref({{ Js::from($ticketsPorPrioridad) }});
        const topClientes = ref({{ Js::from($topClientes) }});

        const cards = ref([
            { titulo: "Total Tickets", valor: "{{ $cards['total'] }}", descripcion: "Todos los tickets del sistema", icono: "fas fa-chart-line", color: "#3b82f6" },
            { titulo: "Tickets Activos", valor: "{{ $cards['activos'] }}", descripcion: "En proceso o pendientes", icono: "far fa-clock", color: "#f59e0b" },
            { titulo: "Tickets Cerrados", valor: "{{ $cards['cerrados'] }}", descripcion: "Finalizados exitosamente", icono: "far fa-check-circle", color: "#10b981" },
            { titulo: "Tickets Urgentes", valor: "{{ $cards['urgentes'] }}", descripcion: "Requieren atención inmediata", icono: "fa fa-exclamation", color: "#ef4444" }
        ]);

        const chartEstado = ref(null);
        const chartPrioridad = ref(null);
        const chartTopClientes = ref(null);

        const colorsEstado = ['#8b5cf6', '#69404aff', '#56df83ff', '#fdac17ff', '#3b82f6', '#ec5a5aff'];
        const colorsPrioridad = ['#ef4444', '#f59e0b', '#37af63','#ec0e0e'];
        const colorTopClientes = '#5695fa';
        const yAxisOptions = {
            beginAtZero: true,
            ticks: { stepSize: 1, callback: v => (v % 1 === 0) ? v : null },
            grid: { drawBorder: false }
        };

        onMounted(() => {
            Chart.register(ChartDataLabels);

            if (chartEstado.value && estado.value.labels?.length) {
                new Chart(chartEstado.value, {
                    type: 'pie',
                    data: {
                        labels: estado.value.labels,
                        datasets: [{
                            data: estado.value.data,
                            backgroundColor: colorsEstado,
                            borderColor: '#fff',
                            hoverBorderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: { padding: 60 },
                        plugins: {
                            legend: { display: false },
                            datalabels: {
                                formatter: (value, ctx) => {
                                    const total = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                    const pct = (value * 100 / total).toFixed(0) + "%";
                                    return `${ctx.chart.data.labels[ctx.dataIndex]} ${pct}`;
                                },
                                color: (ctx) => colorsEstado[ctx.dataIndex],
                                font: { weight: '600', size: 13 },
                                anchor: 'end',
                                align: 'end',
                                offset: 8
                            }
                        }
                    }
                });
            }

            if (chartPrioridad.value && prioridad.value.labels?.length) {
                new Chart(chartPrioridad.value, {
                    type: 'bar',
                    data: {
                        labels: prioridad.value.labels,
                        datasets: [{
                            data: prioridad.value.data,
                            backgroundColor: colorsPrioridad
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: { y: yAxisOptions, x: { grid: { display: false } } }
                    }
                });
            }

            if (chartTopClientes.value && topClientes.value.labels?.length) {
                new Chart(chartTopClientes.value, {
                    type: 'bar',
                    data: {
                        labels: topClientes.value.labels,
                        datasets: [{
                            data: topClientes.value.data,
                            backgroundColor: colorTopClientes
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { y: yAxisOptions, x: { grid: { display: false } } }
                    }
                });
            }
        });

        return { cards, estado, prioridad, topClientes, chartEstado, chartPrioridad, chartTopClientes };
    }
}).mount('#dashboard-root');
</script>
@endsection