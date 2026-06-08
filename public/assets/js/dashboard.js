let chartVentasMensuales = null;
let chartCategorias = null;
let chartTopProductos = null;

document.addEventListener('DOMContentLoaded', function () {
    cargarGraficosIniciales();

    const btnActualizar = document.getElementById('btn-actualizar');
    const btnPdf = document.getElementById('btn-pdf');

    btnActualizar.addEventListener('click', function () {
        actualizarDashboard();
    });

    btnPdf.addEventListener('click', function () {
        const fechaInicio = document.getElementById('fecha_inicio').value;
        const fechaFin = document.getElementById('fecha_fin').value;

        window.location.href =
            `index.php?controller=reporte&action=pdf&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`;
    });
});

function cargarGraficosIniciales() {
    crearGraficoVentasMensuales(ventasMensuales);
    crearGraficoCategorias(categoriasLabels, categoriasData);
    crearGraficoTopProductos(productosLabels, productosData);
}

function crearGraficoVentasMensuales(data) {
    const ctx = document.getElementById('chart-ventas-mensuales');

    if (chartVentasMensuales) {
        chartVentasMensuales.destroy();
    }

    chartVentasMensuales = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            datasets: [{
                label: 'Ventas S/',
                data: data,
                borderColor: '#38bdf8',
                backgroundColor: 'rgba(56, 189, 248, 0.15)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointRadius: 4
            }]
        },
        options: opcionesBaseGrafico()
    });
}

function crearGraficoCategorias(labels, data) {
    const ctx = document.getElementById('chart-categorias');

    if (chartCategorias) {
        chartCategorias.destroy();
    }

    chartCategorias = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: [
                    '#3b82f6',
                    '#22c55e',
                    '#f59e0b',
                    '#ef4444',
                    '#8b5cf6',
                    '#06b6d4'
                ],
                borderColor: '#0f172a',
                borderWidth: 2
            }]
        },
        options: opcionesBaseGrafico()
    });
}

function crearGraficoTopProductos(labels, data) {
    const ctx = document.getElementById('chart-top-productos');

    if (chartTopProductos) {
        chartTopProductos.destroy();
    }

    chartTopProductos = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Unidades vendidas',
                data: data,
                backgroundColor: '#2563eb',
                borderRadius: 8
            }]
        },
        options: {
            ...opcionesBaseGrafico(),
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: '#94a3b8'
                    },
                    grid: {
                        color: 'rgba(148, 163, 184, 0.12)'
                    }
                },
                x: {
                    ticks: {
                        color: '#94a3b8'
                    },
                    grid: {
                        color: 'rgba(148, 163, 184, 0.06)'
                    }
                }
            }
        }
    });
}

function opcionesBaseGrafico() {
    return {
        responsive: true,
        maintainAspectRatio: false,
        animation: false,
        resizeDelay: 200,
        plugins: {
            legend: {
                labels: {
                    color: '#cbd5e1'
                }
            },
            tooltip: {
                backgroundColor: '#020617',
                titleColor: '#ffffff',
                bodyColor: '#cbd5e1',
                borderColor: '#334155',
                borderWidth: 1
            }
        }
    };
}

function actualizarDashboard() {
    const fechaInicio = document.getElementById('fecha_inicio').value;
    const fechaFin = document.getElementById('fecha_fin').value;
    const btn = document.getElementById('btn-actualizar');

    btn.textContent = 'Cargando...';
    btn.disabled = true;

    fetch(`index.php?controller=dashboard&action=apiGetKPIs&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('total-ventas').textContent = 'S/ ' + formatNumber(data.total_ventas);
            document.getElementById('ticket-promedio').textContent = 'S/ ' + formatNumber(data.ticket_promedio);
            document.getElementById('total-transacciones').textContent = Number(data.total_transacciones).toLocaleString('es-PE');

            const tendencia = data.comparativa.porcentaje >= 0 ? '▲' : '▼';
            document.getElementById('trend-ventas').textContent =
                `${tendencia} ${Math.abs(data.comparativa.porcentaje)}% vs mes anterior`;

            crearGraficoVentasMensuales(data.ventas_por_mes.map(item => item.total));
            crearGraficoCategorias(
                data.ventas_por_categoria.map(item => item.categoria),
                data.ventas_por_categoria.map(item => item.total)
            );
            crearGraficoTopProductos(
                data.top_productos.map(item => item.producto),
                data.top_productos.map(item => item.cantidad)
            );

            actualizarTablaVendedores(data.rendimiento_vendedores);
        })
        .catch(() => {
            alert('Error al actualizar el dashboard');
        })
        .finally(() => {
            btn.textContent = 'Actualizar Dashboard';
            btn.disabled = false;
        });
}

function actualizarTablaVendedores(vendedores) {
    const tbody = document.querySelector('#tabla-vendedores tbody');
    tbody.innerHTML = '';

    vendedores.forEach(v => {
        const fila = document.createElement('tr');

        fila.innerHTML = `
            <td>${v.vendedor}</td>
            <td>S/ ${formatNumber(v.total)}</td>
            <td>${v.ventas}</td>
            <td>S/ ${formatNumber(v.promedio)}</td>
        `;

        tbody.appendChild(fila);
    });
}

function formatNumber(num) {
    return Number(num).toLocaleString('es-PE', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}