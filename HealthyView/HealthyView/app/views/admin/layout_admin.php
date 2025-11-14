<?php
/**
 * Plantilla Principal del Panel de Administrador
 *
 * Variables esperadas del controlador:
 * $pageTitle (string): Título de la página.
 * $viewToLoad (string): Ruta de la vista de contenido a cargar.
 * $activePage (string): 'dashboard', 'medicos', 'pacientes' (para resaltar el enlace).
 */
 
 // Incluir el header principal (el que tiene <head> y Bootstrap)
 include_once 'app/views/layout/header.php';
?>

<!-- Navbar superior del Administrador -->
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold text-primary" href="index.php?action=dashboard">
        <i class="bi bi-bar-chart-line-fill"></i>
        HealthyView (Admin)
    </a>
    <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <span class="navbar-text me-3">
            <i class="bi bi-person-circle me-1"></i>
            Hola, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>
          </span>
        </li>
        <li class="nav-item">
          <a class="btn btn-outline-primary" href="index.php?action=logout">Cerrar Sesión</a>
        </li>
      </ul>
  </div>
</nav>

<div class="container-fluid">
    <div class="row">
        
        <!-- Barra lateral de navegación del Admin -->
        <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse shadow-sm" style="min-height: calc(100vh - 56px);"> <!-- 56px es la altura de la navbar -->
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($activePage == 'dashboard') ? 'active' : ''; ?>" href="index.php?action=dashboard">
                            <i class="bi bi-house-fill me-2"></i>
                            Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($activePage == 'medicos') ? 'active' : ''; ?>" href="index.php?action=manageMedicos">
                            <i class="bi bi-person-badge-fill me-2"></i>
                            Gestionar Médicos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($activePage == 'pacientes') ? 'active' : ''; ?>" href="index.php?action=managePacientes">
                            <i class="bi bi-people-fill me-2"></i>
                            Gestionar Pacientes
                        </a>
                    </li>

                     <li class="nav-item">
                        <a class="nav-link <?php echo ($activePage == 'actividades') ? 'active' : ''; ?>" href="index.php?action=manageActividades">
                            <i class="bi bi-person-walking me-2"></i>
                            Gestionar Actividades
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($activePage == 'admins') ? 'active' : ''; ?>" href="index.php?action=manageAdmins">
                            <i class="bi bi-shield-lock-fill me-2"></i>
                            Gestionar Admins
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?php echo ($activePage == 'backups') ? 'active' : ''; ?>" href="index.php?action=manageBackups">
                            <i class="bi bi-database-fill-gear me-2"></i>
                            Respaldo y BD
                        </a>
                    </li>
                    
                </ul>
            </div>
        </nav>

        <!-- Contenido Principal -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-4">
            
            <?php
            // --- Inyección de la Vista ---
            // Aquí es donde se cargará el contenido (dashboard, manage_medicos, etc.)
            if (isset($viewToLoad)) {
                include_once $viewToLoad;
            } else {
                echo "<p>Error: No se encontró la vista a cargar.</p>";
            }
            ?>

        </main>
    </div>
</div>

<?php 
// Incluir el footer principal (el que tiene <script> de Bootstrap)
include_once __DIR__ . '/../layout/footer.php'; 
?>

