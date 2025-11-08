@extends('layout.Layout')
@section('title', 'Reportes')
@section('contenido')
<div id="app" class="scrollable-reportes">
    <div class="contenedor-filtros">
        <h2 class="titulo-seccion">Filtros de Reporte</h2>
        <div class="flex-filtros">
            <div class="item-filtro">
                <label for="tipoReporte">Tipo de Reporte</label>
                <select id="tipoReporte" class="input select-busqueda" v-model="tipoReporte" @change="onTipoReporteChange">
                    <option value="etiqueta_id">Por Categoria</option>
                    <option value="status">Por Estado</option>
                    <option value="usuario_asignado_id">Por Usuario Asignado</option>
                    <option value="cliente_id">Por Cliente</option>
                    <option value="proyecto_id">Por Proyecto</option>
                </select>
            </div>
            <div class="item-filtro">
                <label id="filtrosExtra" for="filtroExtra">Filtrar por</label>
                <select id="filtroExtra" class="input select-busqueda"
                    v-model="filtroExtra"
                    @change="onFiltroExtraChange"
                    v-if="filtrosDisponibles.length">
                    <option value="">Todos</option>
                    <option v-for="f in filtrosDisponibles" :key="f.valor" :value="f.valor">
                        @{{ f.texto }}
                    </option>
                </select>
                <p v-else class="texto-aviso">No hay filtros disponibles</p>
            </div>
        </div>
    </div>

    <div class="contenedor-graficas">
        <div class="caja-grafica">
            <h3>Gráfica de Barras</h3>
            <div class="grafica-barra-wrapper">
                <canvas id="chartBar" class="lienzo-grafica"></canvas>
            </div>
        </div>
        <div class="caja-grafica">
            <h3>Gráfica Circular</h3>
            <div class="grafica-pie-wrapper">
                <canvas id="chartPie" class="lienzo-grafica"></canvas>
            </div>
        </div>
    </div>

    <div class="tabla-container">
        <h3 class="titulo-seccion">Listado de Tickets</h3>
        <table class="tabla" v-if="tickets.length > 0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Cliente</th>
                    <th>Proyecto</th>
                    <th>Asignado</th>
                    <th>Estado</th>
                    <th>Prioridad</th>
                    <th>Categoria</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="ticket in tickets" :key="ticket.serieFolio">
                    <td>@{{ ticket.serie_folio }}</td>
                    <td>@{{ ticket.titulo }}</td>
                    <td>@{{ ticket.cliente }}</td>
                    <td>@{{ ticket.proyecto }}</td>
                    <td>@{{ ticket.usuario_asignado }}</td>
                    <td><span class="badge">@{{ ticket.status }}</span></td>
                    <td><span class="badge">@{{ ticket.prioridad }}</span></td>
                    <td>@{{ ticket.etiqueta }}</td>
                </tr>
            </tbody>
        </table>
        <div v-else class="texto-aviso">
            No existe un ticket con esos filtros.
        </div>
    </div>
</div>

<script>
    const app = Vue.createApp({
        data() {
            return {
                tipoReporte: 'etiqueta_id',
                filtroExtra: '',
                filtrosDisponibles: [],
                tickets: [],
                barChart: null,
                pieChart: null,
                datosGraficas: [],
                coloresGraficas: [
                    '#3b82f6', '#22c55e', '#facc15', '#f97316', '#a855f7',
                    '#ef4444', '#64748b', '#0e7490', '#d946ef', '#16a34a'
                ],
            }
        },
        methods: {
            safeDestroyChart(chartInstance) {
                if (chartInstance && typeof chartInstance.destroy === 'function') {
                    chartInstance.destroy();
                }
            },

            async cargarFiltros() {
                try {
                    const params = new URLSearchParams();
                    let tipoParaFiltros = this.tipoReporte;
                    params.append('tipo', tipoParaFiltros);
                    const res = await fetch(`{{ route('reportes.filtros') }}?${params.toString()}`);
                    const json = await res.json();
                    this.filtrosDisponibles = Array.isArray(json.original) ? json.original : Array.isArray(json) ? json : [];
                    this.filtroExtra = '';
                } catch (error) {
                    console.error('Error al cargar filtros:', error);
                    this.filtrosDisponibles = [];
                    this.filtroExtra = '';
                }
            },

            async cargarGraficas() {
                try {
                    const params = new URLSearchParams();
                    let tipoParaGraficas = this.tipoReporte;
                    params.append('tipo', tipoParaGraficas);

                    const res = await fetch(`{{ route('reportes.datos') }}?${params.toString()}`);
                    const data = await res.json();

                    const labels = data.map(d => d.nombre);
                    const totals = data.map(d => d.total);

                    this.safeDestroyChart(this.barChart);
                    this.safeDestroyChart(this.pieChart);

                    function recreateCanvas(id, className) {
                        const oldCanvas = document.getElementById(id);
                        if (oldCanvas) {
                            const parent = oldCanvas.parentNode;
                            oldCanvas.remove();
                            const newCanvas = document.createElement('canvas');
                            newCanvas.id = id;
                            if (className) newCanvas.className = className;
                            parent.appendChild(newCanvas);
                            return newCanvas;
                        }
                        return null;
                    }

                    const barCanvas = recreateCanvas('chartBar', 'lienzo-grafica');
                    const pieCanvas = recreateCanvas('chartPie', 'lienzo-grafica');
                    const barCtx = (barCanvas || document.getElementById('chartBar')).getContext('2d');
                    const pieCtx = (pieCanvas || document.getElementById('chartPie')).getContext('2d');

                    this.barChart = new Chart(barCtx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Tickets',
                                data: totals,
                                backgroundColor: this.coloresGraficas.slice(0, labels.length),
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
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1,
                                        callback: v => (v % 1 === 0) ? v : null
                                    },
                                    grid: {
                                        drawBorder: false
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    }
                                }
                            }
                        }
                    });

                    let pieOptions = {
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
                                display: false
                            }
                        }
                    };

                    let piePlugins = [];
                    if (typeof ChartDataLabels !== "undefined") {
                        pieOptions.plugins.datalabels = {
                            formatter: (value, ctx) => {
                                const label = ctx.chart.data.labels[ctx.dataIndex];
                                return `${label}: ${value}`;
                            },
                            color: (ctx) => ctx.chart.data.datasets[0].backgroundColor[ctx.dataIndex],
                            font: {
                                weight: '600',
                                size: 13
                            },
                            anchor: 'end',
                            align: 'end',
                            offset: 12,
                            clamp: false,
                            clip: false,
                            textAlign: 'left',
                            backgroundColor: 'rgba(255,255,255,0.85)',
                            borderRadius: 4,
                            padding: 4,
                            display: true
                        };
                        piePlugins.push(ChartDataLabels);
                    }

                    this.pieChart = new Chart(pieCtx, {
                        type: 'pie',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: totals,
                                backgroundColor: this.coloresGraficas.slice(0, labels.length),
                                borderWidth: 1,
                                borderColor: '#fff',
                                hoverBorderColor: '#fff'
                            }]
                        },
                        options: pieOptions,
                        plugins: piePlugins
                    });
                } catch (error) {
                    console.error('Error al cargar gráficas:', error);
                }
            },

            async cargarTabla() {
                try {
                    const params = new URLSearchParams();
                    let tipoParaConsulta = this.tipoReporte;
                    params.append('tipo', tipoParaConsulta);
                    if (this.filtroExtra) params.append('filtro', this.filtroExtra);

                    const res = await fetch(`{{ route('reportes.tickets') }}?${params.toString()}`);
                    this.tickets = await res.json();
                } catch (error) {
                    console.error('Error al cargar tabla:', error);
                }
            },

            async onTipoReporteChange() {
                await this.cargarFiltros();
                await this.cargarGraficas();
                await this.cargarTabla();
            },

            async onFiltroExtraChange() {
                await this.cargarTabla();
            }
        },
        async mounted() {
            await this.cargarFiltros();
            await this.cargarGraficas();
            await this.cargarTabla();
        }
    });

    app.mount('#app');
</script>
@endsection