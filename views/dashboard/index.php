<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="dashboard-container">
    <div class="dashboard-header">
        <div>
            <h1>Dashboard de Ventas</h1>
            <p>Indicadores KPI con PHP, MySQL, MVC, AJAX y Chart.js</p>
        </div>

        <div class="filtros-fecha">
            <label>Rango de fechas:</label>
            <input type="date" id="fecha_inicio" value="<?php echo date('Y-m-d', strtotime('-30 days')); ?>">
            <input type="date" id="fecha_fin" value="<?php echo date('Y-m-d'); ?>">
            <button id="btn-actualizar" class="btn-primary">Actualizar Dashboard</button>
            <button id="btn-pdf" class="btn-pdf">Exportar PDF</button>
        </div>
    </div>

    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-icon">💰</div>
            <div>
                <h3>Ventas Totales</h3>
                <div class="kpi-value" id="total-ventas">S/ <?php echo number_format($totalVentas, 2); ?></div>
                <div class="kpi-sub" id="trend-ventas">
                    <?php echo $comparativa['porcentaje']; ?>% vs mes anterior
                </div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon">🧾</div>
            <div>
                <h3>Ticket Promedio</h3>
                <div class="kpi-value" id="ticket-promedio">S/ <?php echo number_format($ticketPromedio, 2); ?></div>
                <div class="kpi-sub">Por transacción</div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon">📦</div>
            <div>
                <h3>Transacciones</h3>
                <div class="kpi-value" id="total-transacciones"><?php echo number_format($totalTransacciones); ?></div>
                <div class="kpi-sub">Total de ventas</div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon">🗓️</div>
            <div>
                <h3>Ventas Hoy</h3>
                <div class="kpi-value">S/ <?php echo number_format($ventasHoy['total'], 2); ?></div>
                <div class="kpi-sub"><?php echo $ventasHoy['transacciones']; ?> transacciones</div>
            </div>
        </div>
    </div>

    <div class="charts-grid">
        <div class="chart-card">
            <h3>Ventas por Mes</h3>
            <div class="chart-container">
                <canvas id="chart-ventas-mensuales"></canvas>
            </div>
        </div>

        <div class="chart-card">
            <h3>Ventas por Categoría</h3>
            <div class="chart-container">
                <canvas id="chart-categorias"></canvas>
            </div>
        </div>

        <div class="chart-card full-width">
            <h3>Top 5 Productos Más Vendidos</h3>
            <div class="chart-container">
                <canvas id="chart-top-productos"></canvas>
            </div>
        </div>

        <div class="table-card full-width">
            <h3>Rendimiento por Vendedor</h3>
            <div class="table-responsive">
                <table class="data-table" id="tabla-vendedores">
                    <thead>
                        <tr>
                            <th>Vendedor</th>
                            <th>Ventas</th>
                            <th>Transacciones</th>
                            <th>Ticket Promedio</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rendimientoVendedores as $v): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($v['vendedor']); ?></td>
                                <td>S/ <?php echo number_format($v['total'], 2); ?></td>
                                <td><?php echo $v['ventas']; ?></td>
                                <td>S/ <?php echo number_format($v['promedio'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    const ventasMensuales = <?php echo json_encode(array_column($ventasPorMes, 'total')); ?>;
    const categoriasLabels = <?php echo json_encode(array_column($ventasPorCategoria, 'categoria')); ?>;
    const categoriasData = <?php echo json_encode(array_column($ventasPorCategoria, 'total')); ?>;
    const productosLabels = <?php echo json_encode(array_column($topProductos, 'producto')); ?>;
    const productosData = <?php echo json_encode(array_column($topProductos, 'cantidad')); ?>;
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="assets/js/dashboard.js"></script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
