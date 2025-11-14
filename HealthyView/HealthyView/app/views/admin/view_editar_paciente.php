<?php
/**
 * Vista para "Editar Paciente" (Administrador).
 *
 * Variables disponibles:
 * $datosPaciente (array): Datos del paciente cargados desde el controlador.
 */

// Incluir el header
$pageTitle = "Editar Paciente";
include_once 'app/views/layout/header.php'; 

// Mensajes de error (si los hay)
$errorMessage = $_GET['error'] ?? null;
$errorMap = [
    'true' => 'Error al actualizar. Revisa los datos.',
    'correo_existe' => 'Error: El correo electrónico ya está en uso por otro usuario.'
];
?>

<!-- Contenido principal -->
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-15">

            <!-- Formulario para Editar Paciente -->
            <div class="card border-0 shadow-sm mb-0">
                <div class="card-body p-0 p-md-0">
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="h3 mb-0">Editar Paciente</h1>
                        <a href="index.php?action=managePacientes" class="btn btn-sm btn-outline-secondary d-flex align-items-center">
                            <i class="bi bi-arrow-left me-1"></i>
                            Volver a la lista
                        </a>
                    </div>

                    <!-- Mensajes de Alerta -->
                    <?php if ($errorMessage && isset($errorMap[$errorMessage])): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($errorMap[$errorMessage]); ?></div>
                    <?php endif; ?>

                    <form action="index.php?action=updatePaciente" method="POST">
                        
                        <!-- ID del paciente (oculto) -->
                        <input type="hidden" name="idPaciente" value="<?php echo $datosPaciente['idPaciente']; ?>">
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="nombre" class="form-label">Nombre(s):</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($datosPaciente['nombre']); ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="apellidoPaterno" class="form-label">Apellido Paterno:</label>
                                <input type="text" class="form-control" id="apellidoPaterno" name="apellidoPaterno" value="<?php echo htmlspecialchars($datosPaciente['apellidoPaterno']); ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="apellidoMaterno" class="form-label">Apellido Materno:</label>
                                <input type="text" class="form-control" id="apellidoMaterno" name="apellidoMaterno" value="<?php echo htmlspecialchars($datosPaciente['apellidoMaterno']); ?>">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="correo" class="form-label">Correo Electrónico:</label>
                                <input type="email" class="form-control" id="correo" name="correo" value="<?php echo htmlspecialchars($datosPaciente['correo']); ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="estado" class="form-label">Estado:</label>
                                <select class="form-select" id="estado" name="estado" required>
                                    <option value="1" <?php echo ($datosPaciente['estado'] == 1) ? 'selected' : ''; ?>>Activo</option>
                                    <option value="0" <?php echo ($datosPaciente['estado'] == 0) ? 'selected' : ''; ?>>Inactivo</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono:</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($datosPaciente['telefono']); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="diagnostico" class="form-label">Diagnóstico:</label>
                            <textarea class="form-control" id="diagnostico" name="diagnostico" rows="3"><?php echo htmlspecialchars($datosPaciente['diagnostico']); ?></textarea>
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
                            <button type="submit" class="btn btn-primary" name="actualizarPaciente">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<?php 
// Incluir el footer
include_once 'app/views/layout/footer.php'; 
?>

