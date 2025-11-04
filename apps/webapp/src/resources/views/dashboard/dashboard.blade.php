@extends('layout.Layout')
@section('titulo', 'Dashboard')

@section('contenido')

<div id="dashboard-root"
    data-tickets-por-estado='@json($ticketsPorEstado ?? [])'
    data-tickets-por-prioridad='@json($ticketsPorPrioridad ?? [])'
    data-top-clientes='@json($topClientes ?? [])'>

    <div id="dashboard-app">
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
            <div class="tarjeta-grafica">
                <h3>Tickets por Estado</h3>
                <div class="lienzo-grafica">
                    <canvas ref="chartEstado" style="width:100%; height:100%;"></canvas>
                </div>
            </div>

            <div class="tarjeta-grafica">
                <h3>Tickets por Prioridad</h3>
                <div class="lienzo-grafica">
                    <canvas ref="chartPrioridad" style="width:100%; height:100%;"></canvas>
                </div>
            </div>

            <div class="tarjeta-grafica grafica-ancho-completo">
                <h3>Top 5 Clientes por Tickets</h3>
                <div class="lienzo-grafica">
                    <canvas ref="chartTopClientes" style="width:100%; height:100%;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('dashboard-root');
    if (!root) return;

    const { createApp, ref, onMounted } = Vue;

    createApp({
        setup() {
            const chartEstado = ref(null);
            const chartPrioridad = ref(null);
            const chartTopClientes = ref(null);

            const estado = JSON.parse(root.dataset.ticketsPorEstado || '{}');
            const prioridad = JSON.parse(root.dataset.ticketsPorPrioridad || '{}');
            const topClientes = JSON.parse(root.dataset.topClientes || '{}');

            const colorsEstado = ['#3b82f6', '#69404aff', '#fdac17ff', '#8b5cf6', '#ec5a5aff', '#56df83ff'];
            const colorsPrioridad = ['#f59e0b', '#ef4444', '#ec0e0eff', '#37af63ff'];
            const colorTopClientes = '#5695faff';

            const yAxisOptions = {
                beginAtZero: true,
                ticks: {
                    stepSize: 1,
                    callback: v => (v % 1 === 0) ? v : null
                },
                grid: {
                    drawBorder: false
                }
            };

            const cards = [
                {
                    titulo: "Total Tickets",
                    valor: "{{ $cards['total'] ?? 0 }}",
                    descripcion: "Todos los tickets del sistema",
                    icono: "fas fa-chart-line",
                    color: "#3b82f6"
                },
                {
                    titulo: "Tickets Activos",
                    valor: "{{ $cards['activos'] ?? 0 }}",
                    descripcion: "En proceso o pendientes",
                    icono: "far fa-clock",
                    color: "#f59e0b"
                },
                {
                    titulo: "Tickets Cerrados",
                    valor: "{{ $cards['cerrados'] ?? 0 }}",
                    descripcion: "Finalizados exitosamente",
                    icono: "far fa-check-circle",
                    color: "#10b981"
                },
                {
                    titulo: "Tickets Urgentes",
                    valor: "{{ $cards['urgentes'] ?? 0 }}",
                    descripcion: "Requieren atención inmediata",
                    icono: "fas fa-exclamation-circle",
                    color: "#ef4444"
                }
            ];

            onMounted(() => {
                Chart.register(ChartDataLabels);

                if (chartEstado.value) {
                    new Chart(chartEstado.value.getContext('2d'), {
                        type: 'pie',
                        data: {
                            labels: estado.labels || [],
                            datasets: [{
                                data: estado.data || [],
                                backgroundColor: colorsEstado,
                                borderWidth: 1,
                                borderColor: '#fff',
                                hoverBorderColor: '#fff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            aspectRatio: 1,
                            layout: { padding: 30 },
                            elements: { arc: { borderWidth: 0 } },
                            plugins: {
                                legend: { display: false },
                                datalabels: {
                                    formatter: (value, ctx) => {
                                        let label = ctx.chart.data.labels[ctx.dataIndex];
                                        let sum = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                        let percentage = (value * 100 / sum).toFixed(0) + "%";
                                        return `${label} ${percentage}`;
                                    },
                                    color: (ctx) => colorsEstado[ctx.dataIndex],
                                    font: { weight: '600', size: 13 },
                                    anchor: 'end',
                                    align: 'end',
                                    offset: 11,
                                    textAlign: 'center'
                                }
                            }
                        }
                    });
                }

                if (chartPrioridad.value) {
                    new Chart(chartPrioridad.value.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: prioridad.labels || [],
                            datasets: [{
                                label: 'Tickets',
                                data: prioridad.data || [],
                                backgroundColor: colorsPrioridad
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: {
                                y: yAxisOptions,
                                x: { grid: { display: false } }
                            }
                        }
                    });
                }

                if (chartTopClientes.value) {
                    new Chart(chartTopClientes.value.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: topClientes.labels || [],
                            datasets: [{
                                label: 'Tickets',
                                data: topClientes.data || [],
                                backgroundColor: colorTopClientes
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: {
                                y: yAxisOptions,
                                x: { grid: { display: false } }
                            }
                        }
                    });
                }
            });

            return { cards, chartEstado, chartPrioridad, chartTopClientes };
        }
    }).mount('#dashboard-app');
});
</script>
@endsection
