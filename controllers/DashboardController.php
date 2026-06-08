<?php
require_once __DIR__ . '/../models/KPIModel.php';

class DashboardController {
    private $kpiModel;

    public function __construct() {
        $this->kpiModel = new KPIModel();
    }

    public function index() {
        $fechaFin = date("Y-m-d");
        $fechaInicio = date("Y-m-d", strtotime("-30 days"));

        $totalVentas = $this->kpiModel->getTotalVentas($fechaInicio, $fechaFin);
        $ticketPromedio = $this->kpiModel->getTicketPromedio($fechaInicio, $fechaFin);
        $totalTransacciones = $this->kpiModel->getTotalTransacciones($fechaInicio, $fechaFin);
        $ventasHoy = $this->kpiModel->getVentasHoy();
        $comparativa = $this->kpiModel->getComparativaMensual();

        $ventasPorCategoria = $this->kpiModel->getVentasPorCategoria($fechaInicio, $fechaFin);
        $ventasPorMes = $this->kpiModel->getVentasPorMes();
        $topProductos = $this->kpiModel->getTopProductos(5, $fechaInicio, $fechaFin);
        $rendimientoVendedores = $this->kpiModel->getRendimientoVendedores($fechaInicio, $fechaFin);

        include __DIR__ . '/../views/dashboard/index.php';
    }

    public function apiGetKPIs() {
        header("Content-Type: application/json; charset=utf-8");

        $fechaInicio = $_GET["fecha_inicio"] ?? date("Y-m-d", strtotime("-30 days"));
        $fechaFin = $_GET["fecha_fin"] ?? date("Y-m-d");

        $data = [
            "total_ventas" => $this->kpiModel->getTotalVentas($fechaInicio, $fechaFin),
            "ticket_promedio" => $this->kpiModel->getTicketPromedio($fechaInicio, $fechaFin),
            "total_transacciones" => $this->kpiModel->getTotalTransacciones($fechaInicio, $fechaFin),
            "ventas_por_categoria" => $this->kpiModel->getVentasPorCategoria($fechaInicio, $fechaFin),
            "ventas_por_mes" => $this->kpiModel->getVentasPorMes(),
            "top_productos" => $this->kpiModel->getTopProductos(5, $fechaInicio, $fechaFin),
            "rendimiento_vendedores" => $this->kpiModel->getRendimientoVendedores($fechaInicio, $fechaFin),
            "comparativa" => $this->kpiModel->getComparativaMensual()
        ];

        echo json_encode($data);
    }
}
