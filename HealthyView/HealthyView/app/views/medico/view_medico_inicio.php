<?php
/**
 * Vista de CONTENIDO para "Inicio" (Médico).
 * Esta vista es "inyectada" por layout_medico.php
 *
 * Variables disponibles:
 * $citasHoy (array): Lista de citas programadas para hoy.
 * $activePage (string): 'inicio'.
 */
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Inicio</h1>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <div class="card text-white bg-primary shadow-sm">
            <div class="card-body">
                <h5 class="card-title"><?php echo count($citasHoy); ?></h5>
                <p class="card-text">Cita(s) programada(s) para hoy</p>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a href="index.php?action=verCitas" class="small text-white stretched-link">Ver todas las citas</a>
                <div class="small text-white"><i class="bi bi-arrow-right-circle-fill"></i></div>
            </div>
        </div>
    </div>
    </div>

<hr>

<h2 class="h4 mt-4 mb-3">Próximas Citas de Hoy</h2>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Hora</th>
                        <th scope="col">Paciente</th>
                        <th scope="col">Motivo</th>
                        <th scope="col">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($citasHoy)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">No tienes citas programadas para hoy.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($citasHoy as $cita): ?>
                            <tr>
                                <td><?php echo htmlspecialchars(date("h:i A", strtotime($cita['fechaHora']))); ?></td>
                                <td><?php echo htmlspecialchars($cita['pacienteNombre']); ?></td>
                                <td><?php echo htmlspecialchars($cita['motivo']); ?></td>
                                <td>
                                    <span class="badge bg-primary"><?php echo htmlspecialchars($cita['estado']); ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>