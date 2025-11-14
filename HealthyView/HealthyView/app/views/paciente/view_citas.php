<?php
/**
 * Vista para "Mis Citas Programadas" del Paciente.
 * Esta vista es "inyectada" por layout_paciente.php
 *
 * Variables disponibles:
 * $listaCitas (array): Lista de citas cargada desde el controlador.
 * $activePage (string): 'citas' (para resaltar el botón en la sidebar).
 */

// Mensajes de éxito (vienen por GET en la URL)
$successMessage = $_GET['success'] ?? null;
?>

<!-- Encabezado del contenido -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Mis Citas Programadas</h1>
    <!-- Botón para ir a crear una nueva cita -->
    <a href="index.php?action=showCrearCita" class="btn btn-primary">
        <i class="bi bi-plus-circle-fill me-1"></i>
        Programar Nueva Cita
    </a>
</div>

<!-- Mensajes de Alerta -->
<?php if ($successMessage == 'cita_creada'): ?>
    <div class="alert alert-success">
        ¡Tu cita ha sido programada exitosamente!
    </div>
<?php elseif ($successMessage == 'cita_cancelada'): ?>
    <div class="alert alert-warning">
        Tu cita ha sido cancelada.
    </div>
<?php elseif ($successMessage == 'cita_editada'): ?>
    <div class="alert alert-success">
        Tu cita ha sido actualizada exitosamente.
    </div>
<?php endif; ?>

<!-- Tabla de Citas -->
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Fecha y Hora</th>
                        <th scope="col">Médico</th>
                        <th scope="col">Motivo</th>
                        <th scope="col">Duración</th>
                        <th scope="col">Estado</th>
                        <!-- NUEVA COLUMNA -->
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
                                <td><?php echo htmlspecialchars($cita['medicoNombre']); ?></td>
                                <td><?php echo htmlspecialchars($cita['motivo']); ?></td>
                                <td><?php echo htmlspecialchars($cita['duracionMin']); ?> min.</td>
                                <td>
                                    <!-- Dar estilo al estado de la cita -->
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
                                <!-- NUEVAS ACCIONES -->
                                <td>
                                    <?php if ($cita['estado'] == 'Programada'): ?>
                                        <!-- Botón Editar -->
                                        <a href="index.php?action=showEditarCita&idCita=<?php echo $cita['idCita']; ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <!-- Botón Cancelar (con confirmación JS) -->
                                        <a href="index.php?action=cancelarCita&idCita=<?php echo $cita['idCita']; ?>" 
                                           class="btn btn-sm btn-outline-danger" 
                                           title="Cancelar"
                                           onclick="return confirm('¿Estás seguro de que deseas cancelar esta cita?');">
                                            <i class="bi bi-trash-fill"></i>
                                        </a>
                                    <?php else: ?>
                                        <!-- Si no está 'Programada', no se puede editar ni cancelar -->
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

