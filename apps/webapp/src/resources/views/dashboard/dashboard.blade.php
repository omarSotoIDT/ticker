@extends('layout.Layout')
@section('titulo', 'Dashboard')

@section('contenido')

<div id="dashboard-root"
    data-tickets-por-estado='@json($ticketsPorEstado ?? [])'
    data-tickets-por-prioridad='@json($ticketsPorPrioridad ?? [])'
    data-top-clientes='@json($topClientes ?? [])'>

    <div class="fila-tarjetas">
        <div class="tarjeta-estadistica">
            <div class="tarjeta-contenido">
                <div class="tarjeta-encabezado">
                    <div class="tarjeta-titulo">Total Tickets</div>
                    <i class="fas fa-chart-line tarjeta-icono icono-total"></i>
                </div>
                <div class="tarjeta-cuerpo">
                    <div class="tarjeta-valor">{{ $cards['total'] ?? 0 }}</div>
                    <div class="tarjeta-descripcion">Todos los tickets del sistema</div>
                </div>
            </div>
        </div>

        <div class="tarjeta-estadistica">
            <div class="tarjeta-contenido">
                <div class="tarjeta-encabezado">
                    <div class="tarjeta-titulo">Tickets Activos</div>
                    <i class="far fa-clock tarjeta-icono icono-activos"></i>
                </div>
                <div class="tarjeta-cuerpo">
                    <div class="tarjeta-valor">{{ $cards['activos'] ?? 0 }}</div>
                    <div class="tarjeta-descripcion">En proceso o pendientes</div>
                </div>
            </div>
        </div>

        <div class="tarjeta-estadistica">
            <div class="tarjeta-contenido">
                <div class="tarjeta-encabezado">
                    <div class="tarjeta-titulo">Tickets Cerrados</div>
                    <i class="far fa-check-circle tarjeta-icono icono-cerrados"></i>
                </div>
                <div class="tarjeta-cuerpo">
                    <div class="tarjeta-valor">{{ $cards['cerrados'] ?? 0 }}</div>
                    <div class="tarjeta-descripcion">Finalizados exitosamente</div>
                </div>
            </div>
        </div>

        <div class="tarjeta-estadistica">
            <div class="tarjeta-contenido">
                <div class="tarjeta-encabezado">
                    <div class="tarjeta-titulo">Tickets Urgentes</div>
                    <i class="fas fa-exclamation-circle tarjeta-icono icono-urgentes"></i>
                </div>
                <div class="tarjeta-cuerpo">
                    <div class="tarjeta-valor">{{ $cards['urgentes'] ?? 0 }}</div>
                    <div class="tarjeta-descripcion">Requieren atención inmediata</div>
                </div>
            </div>
        </div>
    </div>
    <div class="cuadricula-graficas">
        <div class="tarjeta-grafica">
            <h3>Tickets por Estado</h3>
            <div class="lienzo-grafica"><canvas id="chartEstado"></canvas></div>
        </div>
        <div class="tarjeta-grafica">
            <h3>Tickets por Prioridad</h3>
            <div class="lienzo-grafica"><canvas id="chartPrioridad"></canvas></div>
        </div>
        <div class="tarjeta-grafica grafica-ancho-completo">
            <h3>Top 5 Clientes por Tickets</h3>
            <div class="lienzo-grafica"><canvas id="chartTopClientes"></canvas></div>
        </div>
    </div>

</div>

<script>
    (function() {
        Chart.register(ChartDataLabels);
        const root = document.getElementById('dashboard-root');
        if (!root) return;

        const estado = JSON.parse(root.dataset.ticketsPorEstado || '{}');
        const prioridad = JSON.parse(root.dataset.ticketsPorPrioridad || '{}');
        const topClientes = JSON.parse(root.dataset.topClientes || '{}');

        const colorsEstado = ['#3b82f6', '#69404aff', '#fdac17ff', '#8b5cf6', '#56df83ff', '#ec5a5aff'];
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

        const ctxE = document.getElementById('chartEstado')?.getContext('2d');
        if (ctxE) {
            new Chart(ctxE, {
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
                    layout: {
                        padding: 30
                    },
                    elements: {
                        arc: {
                            borderWidth: 0
                        }
                    },
                    plugins: {
                        legend: {
                            display: false,
                        },
                        datalabels: {
                            formatter: (value, ctx) => {
                                let label = ctx.chart.data.labels[ctx.dataIndex];
                                let sum = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                let percentage = (value * 100 / sum).toFixed(0) + "%";
                                return `${label} ${percentage}`;
                            },
                            color: (ctx) => colorsEstado[ctx.dataIndex],
                            font: {
                                weight: '600',
                                size: 13
                            },
                            anchor: 'end',
                            align: 'end',
                            offset: 11,
                            clamp: false,
                            clip: false,
                            textAlign: 'center'
                        }
                    }
                }

            });
        }

        const ctxP = document.getElementById('chartPrioridad')?.getContext('2d');
        if (ctxP) {
            new Chart(ctxP, {
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
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: yAxisOptions,
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        const ctxC = document.getElementById('chartTopClientes')?.getContext('2d');
        if (ctxC) {
            new Chart(ctxC, {
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
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: yAxisOptions,
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }
    })();
</script>
@endsection