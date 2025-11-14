<?php 
    /**
     * Esta es la PLANTILLA PRINCIPAL para todas las vistas del paciente.
     * El AuthController carga esta plantilla y le pasa dos variables:
     * 1. $activePage: (ej: 'inicio', 'citas') Para saber qué botón resaltar.
     * 2. $viewToLoad: (ej: 'app/views/paciente/view_citas.php') El archivo de contenido para inyectar.
     */

    // Incluir el encabezado HTML (carga Bootstrap, <head>, <body>)
    include_once __DIR__ . '/../layout/header.php'; 
?>

<!-- 
==================================================
    Barra de Navegación Superior (Navbar)
==================================================
-->
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
  <div class="container-fluid">
    
    <!-- Logo (Opción 3) -->
    <a class="navbar-brand fw-bold text-primary" href="index.php?action=dashboard">
        <i class="bi bi-bar-chart-line-fill"></i>
        HealthyView
    </a>
    
    <!-- Botón responsive para móviles -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <!-- Contenido de la Navbar -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        
        <!-- Menú Desplegable del Usuario (basado en tu maqueta) -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle me-1"></i>
                Hola, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
                <li>
                    <a class="dropdown-item" href="index.php?action=showProfile">
                        <i class="bi bi-person-fill me-2"></i> Mi Perfil
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-danger" href="index.php?action=logout">
                        <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                    </a>
                </li>
            </ul>
        </li>
        
      </ul>
    </div>
  </div>
</nav>

<!-- 
==================================================
    Contenedor Principal (Sidebar + Contenido)
==================================================
-->
<div class="container-fluid">
    <div class="row">
        
        <!-- 
        ============================================
            Barra Lateral (Sidebar)
        ============================================
        -->
        <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse vh-100 shadow-sm">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($activePage == 'inicio') ? 'active' : ''; ?>" aria-current="page" href="index.php?action=dashboard">
                            <i class="bi bi-house-fill me-2"></i> Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($activePage == 'actividades') ? 'active' : ''; ?>" href="index.php?action=verActividades">
                            <i class="bi bi-person-walking me-2"></i> Actividades de salud
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($activePage == 'seguimientos') ? 'active' : ''; ?>" href="index.php?action=verSeguimientos">
                            <i class="bi bi-clipboard2-data-fill me-2"></i> Seguimientos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($activePage == 'recetas') ? 'active' : ''; ?>" href="index.php?action=verRecetas">
                            <i class="bi bi-journal-text me-2"></i> Recetas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($activePage == 'citas') ? 'active' : ''; ?>" href="index.php?action=verCitas">
                            <i class="bi bi-calendar-check-fill me-2"></i> Citas programadas
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- 
        ============================================
            Área de Contenido Principal
        ============================================
        -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="pt-3 pb-2 mb-3 border-bottom">
                <!-- Aquí es donde el contenido de otras vistas se "inyecta" -->
                <?php
                    // Cargar la vista de contenido específico (ej: view_citas.php)
                    if (isset($viewToLoad) && file_exists($viewToLoad)) {
                        include_once $viewToLoad;
                    } else {
                        echo '<div class="alert alert-danger">Error: No se pudo cargar el contenido de la vista.</div>';
                    }
                ?>
            </div>
        </main>
    </div>
</div>


<?php 
    // Incluir el pie de página (cierra <body>, <html> y carga JS)
    include_once __DIR__ . '/../layout/footer.php'; 
?>
