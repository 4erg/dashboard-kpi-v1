<?php
require_once __DIR__ . '/../models/KPIModel.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

class ReporteController {
    private $kpiModel;

    public function __construct() {
        $this->kpiModel = new KPIModel();
    }

    public function pdf() {
        $fechaInicio = $_GET["fecha_inicio"] ?? date("Y-m-d", strtotime("-30 days"));
        $fechaFin = $_GET["fecha_fin"] ?? date("Y-m-d");

        $totalVentas = $this->kpiModel->getTotalVentas($fechaInicio, $fechaFin);
        $ticketPromedio = $this->kpiModel->getTicketPromedio($fechaInicio, $fechaFin);
        $totalTransacciones = $this->kpiModel->getTotalTransacciones($fechaInicio, $fechaFin);
        $ventasPorCategoria = $this->kpiModel->getVentasPorCategoria($fechaInicio, $fechaFin);
        $topProductos = $this->kpiModel->getTopProductos(5, $fechaInicio, $fechaFin);
        $rendimientoVendedores = $this->kpiModel->getRendimientoVendedores($fechaInicio, $fechaFin);

        $html = $this->generarHTMLReporte(
            $fechaInicio,
            $fechaFin,
            $totalVentas,
            $ticketPromedio,
            $totalTransacciones,
            $ventasPorCategoria,
            $topProductos,
            $rendimientoVendedores
        );

        $options = new Options();
        $options->set("defaultFont", "Arial");
        $options->set("isRemoteEnabled", true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper("A4", "landscape");
        $dompdf->render();
        $dompdf->stream("reporte_dashboard_" . date("Ymd_His") . ".pdf", ["Attachment" => true]);
    }

    private function generarHTMLReporte($fechaInicio, $fechaFin, $totalVentas, $ticketPromedio, $totalTransacciones, $ventasPorCategoria, $topProductos, $rendimientoVendedores) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Reporte Dashboard</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; color: #222; }
                h1 { color: #1f2937; border-bottom: 3px solid #2563eb; padding-bottom: 10px; }
                h2 { color: #111827; margin-top: 25px; }
                .header { text-align: center; margin-bottom: 25px; }
                .fecha { color: #6b7280; font-size: 13px; }
                .kpi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 20px; }
                .kpi-card { background: #f3f4f6; padding: 15px; border-radius: 10px; text-align: center; }
                .kpi-card h3 { margin: 0 0 10px 0; color: #111827; }
                .kpi-value { font-size: 24px; font-weight: bold; color: #2563eb; }
                table { width: 100%; border-collapse: collapse; margin: 15px 0; }
                th, td { border: 1px solid #d1d5db; padding: 9px; text-align: left; }
                th { background: #2563eb; color: white; }
                .footer { text-align: center; margin-top: 25px; font-size: 11px; color: #6b7280; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>Reporte de Ventas - Dashboard KPI</h1>
                <div class='fecha'>Período: " . date('d/m/Y', strtotime($fechaInicio)) . " - " . date('d/m/Y', strtotime($fechaFin)) . "</div>
            </div>

            <div class='kpi-grid'>
                <div class='kpi-card'><h3>Ventas Totales</h3><div class='kpi-value'>S/ " . number_format($totalVentas, 2) . "</div></div>
                <div class='kpi-card'><h3>Ticket Promedio</h3><div class='kpi-value'>S/ " . number_format($ticketPromedio, 2) . "</div></div>
                <div class='kpi-card'><h3>Transacciones</h3><div class='kpi-value'>" . number_format($totalTransacciones) . "</div></div>
            </div>

            <h2>Ventas por Categoría</h2>
            <table><tr><th>Categoría</th><th>Total Ventas</th></tr>
            " . $this->generarFilasTabla($ventasPorCategoria, 'categoria', 'total') . "
            </table>

            <h2>Top 5 Productos</h2>
            <table><tr><th>Producto</th><th>Cantidad Vendida</th><th>Total</th></tr>
            " . $this->generarFilasTabla($topProductos, 'producto', 'cantidad', 'total') . "
            </table>

            <h2>Rendimiento por Vendedor</h2>
            <table><tr><th>Vendedor</th><th>Total Ventas</th><th>Transacciones</th><th>Ticket Promedio</th></tr>
            " . $this->generarFilasTabla($rendimientoVendedores, 'vendedor', 'total', 'ventas', 'promedio') . "
            </table>

            <div class='footer'>Reporte generado el " . date('d/m/Y H:i:s') . "</div>
        </body>
        </html>";
    }

    private function generarFilasTabla($datos, ...$campos) {
        if (empty($datos)) {
            return "<tr><td colspan='" . count($campos) . "'>Sin datos</td></tr>";
        }

        $html = "";
        foreach ($datos as $row) {
            $html .= "<tr>";
            foreach ($campos as $campo) {
                $valor = $row[$campo] ?? "";
                if (is_numeric($valor) && ($campo === "total" || $campo === "promedio")) {
                    $valor = "S/ " . number_format($valor, 2);
                }
                $html .= "<td>" . htmlspecialchars($valor) . "</td>";
            }
            $html .= "</tr>";
        }
        return $html;
    }
}
