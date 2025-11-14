<?php 
    $server = "localhost"; // o 127.0.0.1
    $user = "root";
    $password = ""; // Tu contraseña de XAMPP (suele ser vacía)
    $db = "healthyview"; // Apuntar a la nueva BD

    // Crear conexion a la base de datos
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    
    try {
        $connection = new mysqli($server, $user, $password, $db);
        // Establecer charset
        $connection->set_charset("utf8mb4");
    } catch (mysqli_sql_exception $e) {
        // die() detiene la ejecución de la aplicación si falla la BD
        die("Conexión fallida: ". $e->getMessage());
    }
?>

