<?php
/**
 * Vista de CONTENIDO para "Mis Citas" (Médico).
 *
 * Variables disponibles:
 * $listaCitas (array): Lista de citas cargada desde el controlador.
 * $activePage (string): 'dashboard'.
 * $successMessage (string|null): Mensaje de éxito.
 * $errorMessage (string|null): Mensaje de error.
 */
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Mis Citas Programadas</h1>
</div>

<?php if ($successMessage ?? null): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($successMessage); ?>
    </div>
<?php endif; ?>
<?php if ($errorMessage ?? null): ?>
    <div class="alert alert-danger">
        <?php echo htmlspecialchars($errorMessage); ?>
    </div>
<?php endif; ?>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Fecha y Hora</th>
                        <th scope="col">Paciente</th>
                        <th scope="col">Teléfono Paciente</th>
                        <th scope="col">Motivo</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($listaCitas)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No tienes citas programadas.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($listaCitas as $cita): ?>
                            <tr>
                                <td><?php echo htmlspecialchars(date("d/m/Y h:i A", strtotime($cita['fechaHora']))); ?></td>
                                <td><?php echo htmlspecialchars($cita['pacienteNombre']); ?></td>
                                <td><?php echo htmlspecialchars($cita['telefono'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($cita['motivo']); ?></td>
                                <td>
                                    <?php 
                                        $estado = htmlspecialchars($cita['estado']);
                                        $badgeClass = 'bg-secondary'; // Default
                                        if ($estado == 'Programada') $badgeClass = 'bg-primary';
                                        if ($estado == 'Completada') $badgeClass = 'bg-success';
                                        if ($estado == 'Cancelada') $badgeClass = 'bg-danger';
                                        if ($estado == 'NoAsistida') $badgeClass = 'bg-warning text-dark';
                                    ?>
                                    <span class="badge <?php echo $badgeClass; ?>"><?php echo $estado; ?></span>
                                </td>
                                <td>
                                    <?php if ($cita['estado'] == 'Programada'): ?>
                                        <a href="index.php?action=actualizarEstadoCita&idCita=<?php echo $cita['idCita']; ?>&estado=Completada" 
                                           class="btn btn-sm btn-outline-success" 
                                           title="Marcar como Completada"
                                           onclick="return confirm('¿Estás seguro de que deseas marcar esta cita como COMPLETADA?');">
                                            <i class="bi bi-check-lg"></i>
                                        </a>
                                        <a href="index.php?action=actualizarEstadoCita&idCita=<?php echo $cita['idCita']; ?>&estado=NoAsistida" 
                                           class="btn btn-sm btn-outline-warning" 
                                           title="Marcar como No Asistida"
                                           onclick="return confirm('¿Estás seguro de que deseas marcar esta cita como NO ASISTIDA?');">
                                            <i class="bi bi-x-lg"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">N/A</span>
                                    <?php endif; ?>
                                    </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>