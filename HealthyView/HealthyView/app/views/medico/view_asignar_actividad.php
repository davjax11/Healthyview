<?php
/**
 * Vista de CONTENIDO para "Asignar Nueva Actividad" (Médico).
 * Esta vista es "inyectada" por layout_medico.php
 *
 * Variables disponibles:
 * $datosPaciente (array): Datos del paciente al que se asigna la actividad.
 * $listaActividades (array): Catálogo de actividades disponibles.
 * $activePage (string): 'pacientes'.
 */
 
// Mensaje de error (si la redirección anterior falló)
$errorMessage = $_GET['error'] ?? null;
?>

<!-- Encabezado del contenido -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Asignar Actividad</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?action=verPerfilPaciente&idPaciente=<?php echo $datosPaciente['idPaciente']; ?>" class="btn btn-sm btn-outline-secondary d-flex align-items-center">
            <i class="bi bi-arrow-left me-1"></i>
            Volver al Perfil de <?php echo htmlspecialchars($datosPaciente['nombre']); ?>
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4 p-md-5">

        <!-- Mensajes de Alerta -->
        <?php if ($errorMessage == 'asignacion_fallida'): ?>
            <div class="alert alert-danger">
                Error: No se pudo asignar la actividad. Inténtalo de nuevo.
            </div>
        <?php endif; ?>
        
        <form action="index.php?action=asignarActividad" method="POST">
            <!-- IDs ocultos -->
            <input type="hidden" name="idPaciente" value="<?php echo $datosPaciente['idPaciente']; ?>">

            <div class="mb-3">
                <label class="form-label">Paciente:</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($datosPaciente['nombre'] . ' ' . $datosPaciente['apellidoPaterno']); ?>" readonly disabled>
            </div>
            
            <div class="mb-3">
                <label for="idActividad" class="form-label">Actividad del Catálogo:</label>
                <select class="form-select" id="idActividad" name="idActividad" required>
                    <option value="" disabled selected>-- Selecciona una actividad --</option>
                    <?php 
                    $currentTipo = '';
                    foreach ($listaActividades as $act): 
                        if ($act['tipo'] != $currentTipo):
                            $currentTipo = $act['tipo'];
                    ?>
                        <optgroup label="<?php echo htmlspecialchars($currentTipo); ?>">
                    <?php endif; ?>
                        <option value="<?php echo $act['idActividad']; ?>">
                            <?php echo htmlspecialchars($act['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="fechaAsignacion" class="form-label">Fecha de Asignación:</label>
                    <input type="date" class="form-control" id="fechaAsignacion" name="fechaAsignacion" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="fechaInicio" class="form-label">Fecha de Inicio (Opcional):</label>
                    <input type="date" class="form-control" id="fechaInicio" name="fechaInicio">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="fechaFin" class="form-label">Fecha Límite (Opcional):</label>
                    <input type="date" class="form-control" id="fechaFin" name="fechaFin">
                </div>
            </div>

            <div class="mb-3">
                <label for="observaciones" class="form-label">Observaciones (Opcional):</label>
                <textarea class="form-control" id="observaciones" name="observaciones" rows="3" placeholder="Ej: Realizar esta actividad por la mañana."></textarea>
            </div>

            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary btn-lg" name="asignarActividad">Asignar Actividad</button>
            </div>

        </form>
    </div>
</div>