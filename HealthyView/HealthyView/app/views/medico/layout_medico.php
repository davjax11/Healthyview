<?php
/**
 * Plantilla Principal del Panel de Médico
 * ...
 */
 
 // Incluir el header principal
 include_once 'app/views/layout/header.php';
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold text-primary" href="index.php?action=dashboard">
        <i class="bi bi-bar-chart-line-fill"></i>
        HealthyView (Médico)
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
        
        <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse shadow-sm" style="min-height: calc(100vh - 56px);">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($activePage == 'inicio') ? 'active' : ''; ?>" href="index.php?action=dashboard">
                            <i class="bi bi-house-fill me-2"></i>
                            Inicio
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($activePage == 'citas') ? 'active' : ''; ?>" href="index.php?action=verCitas">
                            <i class="bi bi-calendar-check-fill me-2"></i>
                            Mis Citas
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?php echo ($activePage == 'pacientes') ? 'active' : ''; ?>" href="index.php?action=verPacientes">
                            <i class="bi bi-people-fill me-2"></i>
                            Pacientes
                        </a>
                    </li>
                    
                </ul>
            </div>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-4">
            
            <?php
            // --- Inyección de la Vista ---
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
// Incluir el footer principal
include_once __DIR__ . '/../layout/footer.php'; 
?>