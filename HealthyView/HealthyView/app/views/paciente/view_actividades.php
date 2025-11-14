<?php
/**
 * Vista para "Mis Actividades de Salud" (Paciente).
 * Esta vista es "inyectada" por layout_paciente.php
 *
 * Variables disponibles:
 * $listaActividades (array): Lista de actividades asignadas.
 * $activePage (string): 'actividades'.
 * $successMessage (string|null): Mensaje de éxito.
 * $errorMessage (string|null): Mensaje de error.
 */
?>

<!-- Encabezado del contenido -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Mis Actividades de Salud</h1>
</div>

<!-- Mensajes de Alerta -->
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

<!-- Lista de Actividades Asignadas -->
<div class="row">
    <?php if (empty($listaActividades)): ?>
        <div class="col-12">
            <div class="alert alert-info text-center">
                Tu médico aún no te ha asignado ninguna actividad.
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($listaActividades as $act): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm <?php echo ($act['estado'] == 'Completada') ? 'border-success' : 'border-primary'; ?>">
                    <div class="card-header <?php echo ($act['estado'] == 'Completada') ? 'bg-success text-white' : 'bg-primary text-white'; ?>">
                        <h5 class="mb-0"><?php echo htmlspecialchars($act['actividadNombre']); ?></h5>
                        <small><?php echo htmlspecialchars($act['actividadTipo']); ?> | <?php echo htmlspecialchars($act['actividadFrecuencia']); ?></small>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($act['actividadDescripcion']); ?></p>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item px-0">
                                <strong>Asignada por:</strong> <?php echo htmlspecialchars($act['medicoNombre'] ?? 'Sistema'); ?>
                            </li>
                            <li class="list-group-item px-0">
                                <strong>Fechas:</strong> 
                                <?php echo htmlspecialchars(date("d/m/Y", strtotime($act['fechaInicio'] ?? $act['fechaAsignacion']))); ?>
                                <?php if ($act['fechaFin']): ?>
                                    al <?php echo htmlspecialchars(date("d/m/Y", strtotime($act['fechaFin']))); ?>
                                <?php endif; ?>
                            </li>
                            <li class="list-group-item px-0">
                                <strong>Observaciones:</strong> <?php echo htmlspecialchars($act['medicoObservaciones'] ?? 'Sin observaciones.'); ?>
                            </li>
                        </ul>
                        
                        <!-- Formulario de Progreso (RFN-10) -->
                        <form action="index.php?action=actualizarProgreso" method="POST">
                            <input type="hidden" name="idAsignacion" value="<?php echo $act['idAsignacion']; ?>">
                            
                            <label for="progreso-<?php echo $act['idAsignacion']; ?>" class="form-label">
                                <strong>Progreso: <span id="label-progreso-<?php echo $act['idAsignacion']; ?>"><?php echo (int)$act['progreso']; ?></span>%</strong>
                            </label>
                            
                            <div class="d-flex">
                                <input type="range" 
                                       class="form-range me-3" 
                                       id="progreso-<?php echo $act['idAsignacion']; ?>" 
                                       name="progreso" 
                                       min="0" 
                                       max="100" 
                                       step="10" 
                                       value="<?php echo (int)$act['progreso']; ?>"
                                       oninput="document.getElementById('label-progreso-<?php echo $act['idAsignacion']; ?>').innerText = this.value;"
                                       <?php echo ($act['estado'] == 'Completada') ? 'disabled' : ''; ?>>
                                       
                                <button type="submit" 
                                        name="actualizarProgreso" 
                                        class="btn btn-sm btn-outline-primary"
                                        <?php echo ($act['estado'] == 'Completada') ? 'disabled' : ''; ?>>
                                    Guardar
                                </button>
                            </div>
                        </form>
                        
                    </div>
                    <?php if ($act['estado'] == 'Completada'): ?>
                        <div class="card-footer bg-light text-success text-center">
                            <strong>¡Actividad Completada!</strong>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>