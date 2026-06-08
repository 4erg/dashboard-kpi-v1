# Dashboard KPI con PHP + MySQL + MVC + AJAX

Proyecto académico de Dashboard y Reportes de KPI.

## Funciones

- Dashboard administrativo
- Tarjetas KPI
- Gráficos con Chart.js
- Filtros por fecha
- Actualización AJAX
- Reporte PDF con Dompdf
- Conexión MySQL usando PDO
- Estructura MVC

## Instalación

1. Importar la base de datos:

```sql
database/schema.sql
```

2. Instalar dependencias:

```bash
composer install
```

3. Ejecutar servidor local:

```bash
cd public
php -S localhost:8000
```

4. Abrir:

```text
http://localhost:8000
```

## Configuración

Editar datos de conexión en:

```text
config/Database.php
```

Por defecto:

```text
host: localhost
database: dashboard_kpi
usuario: root
contraseña: vacío
```
