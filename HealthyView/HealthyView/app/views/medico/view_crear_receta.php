<?php
/**
 * Vista de CONTENIDO para "Asignar Nueva Receta" (Médico).
 * Esta vista es "inyectada" por layout_medico.php
 *
 * Variables disponibles:
 * $datosPaciente (array): Datos del paciente al que se asigna la receta.
 * $activePage (string): 'pacientes'.
 */
 
// Mensaje de error (si la redirección anterior falló)
$errorMessage = $_GET['error'] ?? null;
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Nueva Receta</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?action=verPerfilPaciente&idPaciente=<?php echo $datosPaciente['idPaciente']; ?>" class="btn btn-sm btn-outline-secondary d-flex align-items-center">
            <i class="bi bi-arrow-left me-1"></i>
            Volver al Perfil
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4 p-md-5">

        <?php if ($errorMessage == 'receta_fallida'): ?>
            <div class="alert alert-danger">
                Error: No se pudo asignar la receta. Inténtalo de nuevo.
            </div>
        <?php endif; ?>
        
        <form action="index.php?action=crearReceta" method="POST">
            <input type="hidden" name="idPaciente" value="<?php echo $datosPaciente['idPaciente']; ?>">

            <div class="mb-3">
                <label class="form-label">Paciente:</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($datosPaciente['nombre'] . ' ' . $datosPaciente['apellidoPaterno']); ?>" readonly disabled>
            </div>
            
            <div class="mb-3">
                <label for="resumen" class="form-label">Resumen / Plan de seguimiento:</label>
                <input type="text" class="form-control" id="resumen" name="resumen" placeholder="Ej: Plan de dieta hiposódica, Tratamiento inicial..." required>
            </div>

            <div class="mb-3">
                <label for="observaciones" class="form-label">Observaciones Generales:</label>
                <textarea class="form-control" id="observaciones" name="observaciones" rows="5" placeholder="Ej: Seguir el plan durante 30 días, evitar alimentos procesados..."></textarea>
            </div>

            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary btn-lg" name="asignarReceta">Guardar y Asignar Receta</button>
            </div>
            
            <div class="text-center mt-3">
                 <small class="text-muted">Nota: Podrás añadir medicamentos o cosas específicas después de crear la receta.</small>
            </div>

        </form>
    </div>
</div>