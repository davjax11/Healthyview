<?php
// Iniciar la sesión
session_start();

// === INICIO DE LA MODIFICACIÓN (FIX ZONA HORARIA) ===
// Establecer la zona horaria para todo el script
date_default_timezone_set('America/Mexico_City');
// === FIN DE LA MODIFICACIÓN ===

// Cargar la conexión (siempre se necesita)
include_once "config/db_connection.php";

// Determinar la acción a realizar
$action = $_GET['action'] ?? 'showLogin';

// Definir las acciones públicas (no requieren inicio de sesión)
$publicActions = [
    'showLogin',
    'login',
    'showRegister',
    'register',
    'logout' // Logout es público en el sentido de que siempre debe funcionar
];

// Definir las acciones del "API" (requieren login, pero se llaman desde JS)
$apiActions = [
    'getHorariosDisponibles'
];

// 1. Manejar acciones públicas (Login/Registro)
if (in_array($action, $publicActions)) {
    include_once "app/controllers/AuthController.php";
    $controller = new AuthController($connection);
    
    // Llamar a la acción pública
    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        $controller->showLogin("Acción no válida");
    }
} 
// 2. Manejar acciones de API (AJAX)
else if (in_array($action, $apiActions)) {
    // Estas acciones SÍ requieren una sesión
    if (!isset($_SESSION['usuario_id'])) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'No autorizado']);
        exit();
    }
    
    // Determinar a qué controlador llamar (por ahora, solo el paciente usa la API)
    if ($_SESSION['usuario_tipo'] == 'Paciente') {
        include_once "app/controllers/PacienteController.php";
        $controller = new PacienteController($connection);
        if (method_exists($controller, $action)) {
            $controller->$action();
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Acción no permitida para este rol']);
        exit();
    }
}
// 3. Manejar acciones de PACIENTE (Requiere Sesión de Paciente)
else if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_tipo'] == 'Paciente') {
    include_once "app/controllers/PacienteController.php";
    $controller = new PacienteController($connection);
    
    // Si la acción existe en el controlador de Paciente, llamarla
    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        // Si no, mandarlo a su dashboard por defecto
        $controller->dashboard();
    }
} 
// 4. Manejar acciones de ADMINISTRADOR (Requiere Sesión de Admin)
else if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_tipo'] == 'Administrador') {
    include_once "app/controllers/AdminController.php";
    $controller = new AdminController($connection);
    
    // Si la acción existe en el controlador de Admin, llamarla
    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        // Si no, mandarlo a su dashboard por defecto
        $controller->dashboard();
    }
}
// 5. Manejar acciones de MÉDICO (¡AHORA IMPLEMENTADO!)
else if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_tipo'] == 'Medico') {
    
    include_once "app/controllers/MedicoController.php";
    $controller = new MedicoController($connection);
    
    // Si la acción existe en el controlador de Médico, llamarla
    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        // Si no, mandarlo a su dashboard por defecto
        $controller->dashboard();
    }
}
// 6. Si no es nada de lo anterior, es un usuario no logueado intentando acceder
else {
    // Redirigir al login
    header("Location: index.php?action=showLogin&error=acceso_denegado");
    exit();
}
?>