<?php
/**
 * Vista de CONTENIDO para "Editar Administrador".
 *
 * Variables disponibles:
 * $datosAdmin (array): Datos del admin cargados desde el controlador.
 */

// Mensajes de error (si los hay)
$errorMessage = $_GET['error'] ?? null;
$errorMap = [
    'true' => 'Error al actualizar. Revisa los datos.',
    'correo_existe' => 'Error: El correo electrónico ya está en uso por otro usuario.',
    'pass_no_coinciden' => 'Error: Las contraseñas no coinciden.'
];

?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Editar Administrador</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?action=manageAdmins" class="btn btn-sm btn-outline-secondary d-flex align-items-center">
            <i class="bi bi-arrow-left me-1"></i>
            Volver a la lista
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4 p-md-5">
        
        <?php if ($errorMessage && isset($errorMap[$errorMessage])): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($errorMap[$errorMessage]); ?></div>
        <?php endif; ?>

        <form action="index.php?action=updateAdmin" method="POST">
            
            <input type="hidden" name="idAdmin" value="<?php echo $datosAdmin['idAdmin']; ?>">
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="nombre" class="form-label">Nombre(s):</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($datosAdmin['nombre']); ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="apellidoPaterno" class="form-label">Apellido Paterno:</label>
                    <input type="text" class="form-control" id="apellidoPaterno" name="apellidoPaterno" value="<?php echo htmlspecialchars($datosAdmin['apellidoPaterno']); ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="apellidoMaterno" class="form-label">Apellido Materno:</label>
                    <input type="text" class="form-control" id="apellidoMaterno" name="apellidoMaterno" value="<?php echo htmlspecialchars($datosAdmin['apellidoMaterno']); ?>">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="correo" class="form-label">Correo Electrónico:</label>
                    <input type="email" class="form-control" id="correo" name="correo" value="<?php echo htmlspecialchars($datosAdmin['correo']); ?>" required>
                </div>
                 <div class="col-md-6 mb-3">
                    <label for="departamento" class="form-label">Departamento:</label>
                    <input type="text" class="form-control" id="departamento" name="departamento" value="<?php echo htmlspecialchars($datosAdmin['departamento'] ?? ''); ?>">
                </div>
            </div>

            <div class="mb-3">
                <label for="estado" class="form-label">Estado:</label>
                <select class="form-select" id="estado" name="estado" required>
                    <option value="1" <?php echo ($datosAdmin['estado'] == 1) ? 'selected' : ''; ?>>Activo</option>
                    <option value="0" <?php echo ($datosAdmin['estado'] == 0) ? 'selected' : ''; ?>>Inactivo</option>
                </select>
            </div>
            
            <hr class="my-3">
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="pass" class="form-label">Nueva Contraseña:</label>
                    <input type="password" class="form-control" id="pass" name="pass">
                    <small class="text-muted">Dejar en blanco si no deseas cambiar la contraseña.</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="pass_confirm" class="form-label">Confirmar Nueva Contraseña:</label>
                    <input type="password" class="form-control" id="pass_confirm" name="pass_confirm">
                </div>
            </div>
            
            <div class="d-grid">
                <button type="submit" class="btn btn-primary" name="actualizarAdmin">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>