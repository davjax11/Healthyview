<?php
/**
 * Dashboard del Paciente
 * Variables: $resumen (array con proximaCita, ultimoSeguimiento, actividadesPendientes)
 */
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Hola, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?> 👋</h1>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title text-primary"><i class="bi bi-calendar-event"></i> Próxima Cita</h5>
                <?php if ($resumen['proximaCita']): ?>
                    <h3 class="mt-3"><?php echo date('d M, h:i A', strtotime($resumen['proximaCita']['fechaHora'])); ?></h3>
                    <p class="text-muted mb-0"><?php echo htmlspecialchars($resumen['proximaCita']['motivo']); ?></p>
                    <small class="text-primary">Con Dr. <?php echo htmlspecialchars($resumen['proximaCita']['medico']); ?></small>
                <?php else: ?>
                    <p class="text-muted mt-3">No tienes citas programadas próximamente.</p>
                    <a href="index.php?action=showCrearCita" class="btn btn-sm btn-outline-primary">Agendar ahora</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title text-success"><i class="bi bi-heart-pulse"></i> Mi Estado Actual</h5>
                <?php if ($resumen['ultimoSeguimiento']): ?>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <h3 class="mb-0"><?php echo htmlspecialchars($resumen['ultimoSeguimiento']['peso']); ?> kg</h3>
                            <small class="text-muted">Peso Actual</small>
                        </div>
                        <div class="text-end">
                            <h3 class="mb-0"><?php echo htmlspecialchars($resumen['ultimoSeguimiento']['imc']); ?></h3>
                            <small class="text-muted">IMC</small>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="badge bg-light text-dark border">Estado: <?php echo htmlspecialchars($resumen['ultimoSeguimiento']['nivelBienestar']); ?></span>
                    </div>
                <?php else: ?>
                    <p class="text-muted mt-3">Aún no tienes registros de seguimiento médico.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card border-0 shadow-sm h-100 bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-list-check"></i> Mis Actividades</h5>
                <h2 class="display-4 fw-bold mt-3"><?php echo $resumen['actividadesPendientes']; ?></h2>
                <p class="card-text">Actividades activas asignadas por tu médico.</p>
            </div>
            <div class="card-footer border-0 bg-transparent">
                <a href="index.php?action=verActividades" class="text-white text-decoration-none">Ver actividades <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
</div>

