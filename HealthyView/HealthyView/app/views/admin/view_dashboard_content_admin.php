<?php
/**
 * Vista Dashboard Admin
 * Variables: $stats (array)
 */
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Panel de Control</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary">Exportar Datos</button>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-primary shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Pacientes Activos</h6>
                        <h2 class="mt-2 mb-0"><?php echo $stats['pacientes']; ?></h2>
                    </div>
                    <i class="bi bi-people-fill fs-1 opacity-50"></i>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="index.php?action=managePacientes" class="text-white text-decoration-none small">Gestionar <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card text-white bg-success shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Médicos Activos</h6>
                        <h2 class="mt-2 mb-0"><?php echo $stats['medicos']; ?></h2>
                    </div>
                    <i class="bi bi-person-badge-fill fs-1 opacity-50"></i>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="index.php?action=manageMedicos" class="text-white text-decoration-none small">Gestionar <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card text-white bg-info shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Total Citas</h6>
                        <h2 class="mt-2 mb-0"><?php echo $stats['citas']; ?></h2>
                    </div>
                    <i class="bi bi-calendar-check fs-1 opacity-50"></i>
                </div>
            </div>
             <div class="card-footer bg-transparent border-0">
                <a href="index.php?action=manageReportes" class="text-white text-decoration-none small">Ver Reportes <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card text-white bg-warning shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Actividad Foro</h6>
                        <h2 class="mt-2 mb-0"><?php echo $stats['posts']; ?></h2>
                    </div>
                    <i class="bi bi-chat-quote-fill fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<h4 class="mt-4 mb-3">Gestión del Sistema</h4>
<div class="row">
    <div class="col-md-4">
        <div class="list-group shadow-sm">
            <a href="index.php?action=manageReportes" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                <div><i class="bi bi-graph-up me-2 text-primary"></i> Ver Reportes Estadísticos</div>
                <i class="bi bi-chevron-right text-muted"></i>
            </a>
            <a href="index.php?action=manageAdmins" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                <div><i class="bi bi-shield-lock me-2 text-danger"></i> Gestionar Administradores</div>
                <i class="bi bi-chevron-right text-muted"></i>
            </a>
             <a href="index.php?action=manageActividades" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                <div><i class="bi bi-list-check me-2 text-success"></i> Catálogo de Actividades</div>
                <i class="bi bi-chevron-right text-muted"></i>
            </a>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center d-flex flex-column justify-content-center">
                <h5 class="card-title text-muted">Seguridad de Datos</h5>
                <p class="card-text">Realiza una copia de seguridad de la base de datos actual.</p>
                <a href="index.php?action=manageBackups" class="btn btn-dark">
                    <i class="bi bi-database-down me-2"></i> Ir a Respaldos
                </a>
            </div>
        </div>
    </div>
</div>