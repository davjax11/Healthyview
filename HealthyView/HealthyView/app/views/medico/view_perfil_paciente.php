<?php
/**
 * Vista de CONTENIDO para "Perfil de Paciente" (Médico).
 *
 * Variables disponibles:
 * $datosPaciente (array): Datos del perfil del paciente.
 * $historialSeguimiento (array): Historial de seguimientos.
 * $historialRecetas (array): Historial de recetas asignadas.
 * $activePage (string): 'pacientes'.
 * $successMessage (string|null): Mensaje de éxito.
 * $errorMessage (string|null): Mensaje de error.
 */
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Perfil del Paciente</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?action=verPacientes" class="btn btn-sm btn-outline-secondary d-flex align-items-center">
            <i class="bi bi-arrow-left me-1"></i>
            Volver a la lista de Pacientes
        </a>
    </div>
</div>

<?php if (isset($successMessage)): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($successMessage); ?>
    </div>
<?php endif; ?>
<?php if (isset($errorMessage)): ?>
    <div class="alert alert-danger">
        <?php echo htmlspecialchars($errorMessage); ?>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($datosPaciente['nombre'] . ' ' . $datosPaciente['apellidoPaterno']); ?></h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <strong>Correo:</strong> <?php echo htmlspecialchars($datosPaciente['correo']); ?>
                    </li>
                    <li class="list-group-item">
                        <strong>Teléfono:</strong> <?php echo htmlspecialchars($datosPaciente['telefono'] ?? 'N/A'); ?>
                    </li>
                    <li class="list-group-item">
                        <strong>Género:</strong> <?php echo htmlspecialchars($datosPaciente['genero']); ?>
                    </li>
                    <li class="list-group-item">
                        <strong>Peso:</strong> <?php echo htmlspecialchars($datosPaciente['peso'] ?? 'N/A'); ?> kg
                    </li>
                    <li class="list-group-item">
                        <strong>Estatura:</strong> <?php echo htmlspecialchars($datosPaciente['estatura'] ?? 'N/A'); ?> m
                    </li>
                    <li class="list-group-item">
                        <strong>Diagnóstico:</strong>
                        <p><?php echo nl2br(htmlspecialchars($datosPaciente['diagnostico'] ?? 'Sin diagnóstico registrado.')); ?></p>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Registrar Nuevo Seguimiento</h5>
            </div>
            <div class="card-body">
                <form action="index.php?action=registrarSeguimiento" method="POST">
                    <input type="hidden" name="idPaciente" value="<?php echo $datosPaciente['idPaciente']; ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="peso" class="form-label">Peso (kg):</label>
                            <input type="number" class="form-control" id="peso" name="peso" step="0.1" value="<?php echo htmlspecialchars($datosPaciente['peso'] ?? 0.0); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="estatura" class="form-label">Estatura (m):</label>
                            <input type="number" class="form-control" id="estatura" name="estatura" step="0.01" value="<?php echo htmlspecialchars($datosPaciente['estatura'] ?? 0.0); ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="nivelBienestar" class="form-label">Nivel de Bienestar (reportado por paciente):</label>
                        <select class="form-select" id="nivelBienestar" name="nivelBienestar" required>
                            <option value="Regular" selected>Regular</option>
                            <option value="Bueno">Bueno</option>
                            <option value="Excelente">Excelente</option>
                            <option value="Malo">Malo</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones Médicas:</label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="3" placeholder="Ej: El paciente reporta buena adherencia al plan..."></textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary" name="registrarSeguimiento">Guardar Seguimiento</button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card border-0 shadow-sm mb-4"> <div class="card-header bg-light">
                <h5 class="mb-0">Historial de Seguimiento</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Peso</th>
                                <th>IMC</th>
                                <th>Bienestar</th>
                                <th>Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($historialSeguimiento)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No hay seguimientos registrados.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($historialSeguimiento as $seguimiento): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars(date("d/m/Y h:i A", strtotime($seguimiento['fechaRegistro']))); ?></td>
                                        <td><?php echo htmlspecialchars($seguimiento['peso']); ?> kg</td>
                                        <td><?php echo ($seguimiento['imc']) ? number_format($seguimiento['imc'], 2) : 'N/A'; ?></td>
                                        <td><?php echo htmlspecialchars($seguimiento['nivelBienestar']); ?></td>
                                        <td><?php echo htmlspecialchars($seguimiento['observaciones']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Historial de Recetas/Planes</h5>
                <a href="index.php?action=showCrearReceta&idPaciente=<?php echo $datosPaciente['idPaciente']; ?>" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-circle-fill me-1"></i>
                    Nueva receta/plan
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Resumen</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($historialRecetas)): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No hay recetas asignadas.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($historialRecetas as $receta): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars(date("d/m/Y", strtotime($receta['fechaEmision']))); ?></td>
                                        <td><?php echo htmlspecialchars($receta['resumen']); ?></td>
                                        <td>
                                            <?php 
                                                $estado = htmlspecialchars($receta['estado']);
                                                $badgeClass = ($estado == 'Activa') ? 'bg-success' : 'bg-secondary';
                                            ?>
                                            <span class="badge <?php echo $badgeClass; ?>"><?php echo $estado; ?></span>
                                        </td>
                                        <td>
                                            <a href="index.php?action=gestionarReceta&idReceta=<?php echo $receta['idReceta']; ?>&idPaciente=<?php echo $datosPaciente['idPaciente']; ?>" 
                                               class="btn btn-sm btn-outline-secondary" 
                                               title="Añadir/Ver Ítems">
                                                <i class="bi bi-list-ul"></i> Ver
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

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Historial de Actividades</h5>
                <a href="index.php?action=showAsignarActividad&idPaciente=<?php echo $datosPaciente['idPaciente']; ?>" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-circle-fill me-1"></i>
                    Asignar Nueva Actividad
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Actividad</th>
                                <th>Tipo</th>
                                <th>Asignada</th>
                                <th>Progreso</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($historialActividades)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No hay actividades asignadas.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($historialActividades as $act): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($act['actividadNombre']); ?></td>
                                        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($act['actividadTipo']); ?></span></td>
                                        <td><?php echo htmlspecialchars(date("d/m/Y", strtotime($act['fechaAsignacion']))); ?></td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar" role="progressbar" style="width: <?php echo htmlspecialchars($act['progreso']); ?>%;" aria-valuenow="<?php echo htmlspecialchars($act['progreso']); ?>" aria-valuemin="0" aria-valuemax="100">
                                                    <?php echo htmlspecialchars($act['progreso']); ?>%
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php 
                                                $estado = htmlspecialchars($act['estado']);
                                                $badgeClass = 'bg-secondary';
                                                if ($estado == 'Activa') $badgeClass = 'bg-primary';
                                                if ($estado == 'Completada') $badgeClass = 'bg-success';
                                                if ($estado == 'Cancelada') $badgeClass = 'bg-danger';
                                            ?>
                                            <span class="badge <?php echo $badgeClass; ?>"><?php echo $estado; ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>