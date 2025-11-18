<?php
/**
 * Vista Dashboard Médico
 * Variables: $citasHoy (array), $stats (array con totalPacientes, citasPendientes, recetasMes)
 */
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Bienvenido, Dr. <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary"><?php echo date('d/m/Y'); ?></button>
        </div>
    </div>
</div>

<div class="row mb-4">
    
    <div class="col-md-4 mb-3">
        <div class="card text-white bg-primary shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="card-title mb-0">Citas Pendientes</h6>
                    <h2 class="mt-2 mb-0 fw-bold"><?php echo $stats['citasPendientes']; ?></h2>
                </div>
                <i class="bi bi-calendar-event fs-1 opacity-50"></i>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="index.php?action=verCitas" class="text-white text-decoration-none small">Ver agenda completa <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card text-white bg-success shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="card-title mb-0">Pacientes Atendidos</h6>
                    <h2 class="mt-2 mb-0 fw-bold"><?php echo $stats['totalPacientes']; ?></h2>
                </div>
                <i class="bi bi-people fs-1 opacity-50"></i>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="index.php?action=verPacientes" class="text-white text-decoration-none small">Ir al directorio <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card text-white bg-info shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="card-title mb-0">Recetas (Este Mes)</h6>
                    <h2 class="mt-2 mb-0 fw-bold"><?php echo $stats['recetasMes']; ?></h2>
                </div>
                <i class="bi bi-prescription2 fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<h4 class="mb-3 text-secondary"><i class="bi bi-clock-history me-2"></i>Agenda de Hoy</h4>
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col" class="ps-4">Hora</th>
                        <th scope="col">Paciente</th>
                        <th scope="col">Motivo</th>
                        <th scope="col">Estado</th>
                        <th scope="col" class="text-end pe-4">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($citasHoy)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                                No tienes citas programadas para el resto del día.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($citasHoy as $cita): ?>
                            <tr>
                                <td class="ps-4 fw-bold"><?php echo htmlspecialchars(date("h:i A", strtotime($cita['fechaHora']))); ?></td>
                                <td><?php echo htmlspecialchars($cita['pacienteNombre']); ?></td>
                                <td><?php echo htmlspecialchars($cita['motivo']); ?></td>
                                <td><span class="badge bg-primary">Programada</span></td>
                                <td class="text-end pe-4">
                                    <a href="index.php?action=verPerfilPaciente&idPaciente=<?php echo $cita['idPaciente'] ?? 1; ?>" class="btn btn-sm btn-outline-primary">
                                        Atender
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>