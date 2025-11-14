<?php
/**
 * Vista de CONTENIDO para "Editar Actividad" (Administrador).
 * Esta vista es "inyectada" por layout_admin.php
 *
 * Variables disponibles:
 * $datosActividad (array): Datos de la actividad a editar.
 */

// Mensajes de error (si los hay)
$errorMessage = $_GET['error'] ?? null;
$errorMap = [
    'true' => 'Error al actualizar. Revisa los datos.',
];

// Opciones para el <select> de Tipo
$tiposActividad = ['Física', 'Alimentaria', 'Mental', 'Otro'];
$tipoActual = $datosActividad['tipo'];
?>

<!-- Encabezado del contenido -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Editar Actividad</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?action=manageActividades" class="btn btn-sm btn-outline-secondary d-flex align-items-center">
            <i class="bi bi-arrow-left me-1"></i>
            Volver a la lista
        </a>
    </div>
</div>

<!-- Formulario para Editar Actividad -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4 p-md-5">
        
        <!-- Mensajes de Alerta -->
        <?php if ($errorMessage && isset($errorMap[$errorMessage])): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($errorMap[$errorMessage]); ?></div>
        <?php endif; ?>

        <form action="index.php?action=updateActividad" method="POST">
            
            <input type="hidden" name="idActividad" value="<?php echo $datosActividad['idActividad']; ?>">
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nombre" class="form-label">Nombre de la Actividad:</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($datosActividad['nombre']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="tipo" class="form-label">Tipo de Actividad:</label>
                    <select class="form-select" id="tipo" name="tipo" required>
                        <?php foreach ($tiposActividad as $tipo): ?>
                            <option value="<?php echo $tipo; ?>" <?php echo ($tipoActual == $tipo) ? 'selected' : ''; ?>>
                                <?php echo $tipo; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción:</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="2"><?php echo htmlspecialchars($datosActividad['descripcion']); ?></textarea>
            </div>
            
            <div class="mb-3">
                <label for="frecuencia" class="form-label">Frecuencia Recomendada:</label>
                <input type="text" class="form-control" id="frecuencia" name="frecuencia" value="<?php echo htmlspecialchars($datosActividad['frecuencia']); ?>">
            </div>
            
            <div class="d-grid">
                <button type="submit" class="btn btn-primary" name="actualizarActividad">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>