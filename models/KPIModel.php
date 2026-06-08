<?php
require_once __DIR__ . '/../config/Database.php';

class KPIModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getTotalVentas($fechaInicio = null, $fechaFin = null) {
        $sql = "SELECT COALESCE(SUM(total), 0) AS total FROM ventas WHERE 1=1";
        $params = [];

        if ($fechaInicio && $fechaFin) {
            $sql .= " AND fecha BETWEEN :inicio AND :fin";
            $params = [":inicio" => $fechaInicio, ":fin" => $fechaFin];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()["total"];
    }

    public function getTicketPromedio($fechaInicio = null, $fechaFin = null) {
        $sql = "SELECT COALESCE(AVG(total), 0) AS promedio FROM ventas WHERE 1=1";
        $params = [];

        if ($fechaInicio && $fechaFin) {
            $sql .= " AND fecha BETWEEN :inicio AND :fin";
            $params = [":inicio" => $fechaInicio, ":fin" => $fechaFin];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return round($stmt->fetch()["promedio"], 2);
    }

    public function getTotalTransacciones($fechaInicio = null, $fechaFin = null) {
        $sql = "SELECT COUNT(*) AS total FROM ventas WHERE 1=1";
        $params = [];

        if ($fechaInicio && $fechaFin) {
            $sql .= " AND fecha BETWEEN :inicio AND :fin";
            $params = [":inicio" => $fechaInicio, ":fin" => $fechaFin];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()["total"];
    }

    public function getVentasPorCategoria($fechaInicio = null, $fechaFin = null) {
        $sql = "SELECT categoria, SUM(total) AS total FROM ventas WHERE 1=1";
        $params = [];

        if ($fechaInicio && $fechaFin) {
            $sql .= " AND fecha BETWEEN :inicio AND :fin";
            $params = [":inicio" => $fechaInicio, ":fin" => $fechaFin];
        }

        $sql .= " GROUP BY categoria ORDER BY total DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getVentasPorMes($anio = null) {
        $anio = $anio ?? date("Y");

        $sql = "SELECT MONTH(fecha) AS mes, SUM(total) AS total, COUNT(*) AS transacciones
                FROM ventas
                WHERE YEAR(fecha) = :anio
                GROUP BY MONTH(fecha)
                ORDER BY mes";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([":anio" => $anio]);

        $resultados = [];
        for ($i = 1; $i <= 12; $i++) {
            $resultados[$i] = ["mes" => $i, "total" => 0, "transacciones" => 0];
        }

        foreach ($stmt->fetchAll() as $row) {
            $resultados[(int)$row["mes"]] = $row;
        }

        return array_values($resultados);
    }

    public function getTopProductos($limit = 5, $fechaInicio = null, $fechaFin = null) {
        $sql = "SELECT producto, SUM(cantidad) AS cantidad, SUM(total) AS total
                FROM ventas WHERE 1=1";
        $params = [];

        if ($fechaInicio && $fechaFin) {
            $sql .= " AND fecha BETWEEN :inicio AND :fin";
            $params = [":inicio" => $fechaInicio, ":fin" => $fechaFin];
        }

        $sql .= " GROUP BY producto ORDER BY cantidad DESC LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(":limit", (int)$limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getRendimientoVendedores($fechaInicio = null, $fechaFin = null) {
        $sql = "SELECT vendedor, SUM(total) AS total, COUNT(*) AS ventas, AVG(total) AS promedio
                FROM ventas WHERE 1=1";
        $params = [];

        if ($fechaInicio && $fechaFin) {
            $sql .= " AND fecha BETWEEN :inicio AND :fin";
            $params = [":inicio" => $fechaInicio, ":fin" => $fechaFin];
        }

        $sql .= " GROUP BY vendedor ORDER BY total DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function getVentasHoy() {
        $sql = "SELECT COALESCE(SUM(total), 0) AS total, COUNT(*) AS transacciones
                FROM ventas WHERE fecha = CURDATE()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getComparativaMensual() {
        $mesActual = date("m");
        $anioActual = date("Y");
        $mesAnterior = date("m", strtotime("-1 month"));
        $anioAnterior = date("Y", strtotime("-1 month"));

        $sql = "SELECT 
                COALESCE(SUM(CASE WHEN MONTH(fecha) = :mesActual AND YEAR(fecha) = :anioActual THEN total ELSE 0 END), 0) AS total_actual,
                COALESCE(SUM(CASE WHEN MONTH(fecha) = :mesAnterior AND YEAR(fecha) = :anioAnterior THEN total ELSE 0 END), 0) AS total_anterior
                FROM ventas";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ":mesActual" => $mesActual,
            ":anioActual" => $anioActual,
            ":mesAnterior" => $mesAnterior,
            ":anioAnterior" => $anioAnterior
        ]);

        $result = $stmt->fetch();
        $actual = (float)$result["total_actual"];
        $anterior = (float)$result["total_anterior"];

        $porcentaje = 0;
        if ($anterior > 0) {
            $porcentaje = round((($actual - $anterior) / $anterior) * 100, 1);
        }

        return [
            "total_actual" => $actual,
            "total_anterior" => $anterior,
            "porcentaje" => $porcentaje,
            "tendencia" => $porcentaje >= 0 ? "up" : "down"
        ];
    }
}
