<?php
/**
 * Vista de CONTENIDO para "Editar Médico" (Administrador).
 * Esta vista es "inyectada" por layout_admin.php
 *
 * Variables disponibles:
 * $datosMedico (array): Datos del médico cargados desde el controlador.
 */

// Mensajes de error (si los hay)
$errorMessage = $_GET['error'] ?? null;
$errorMap = [
    'true' => 'Error al actualizar. Revisa los datos.',
    'correo_existe' => 'Error: El correo electrónico ya está en uso por otro usuario.'
];

// --- LISTA DE OPCIONES PARA ESPECIALIDAD ---
$especialidades = [
    "Nutrición",
    "Psicología",
    "Medicina General",
    "Cardiología",
    "Dermatología",
    "Endocrinología",
    "Fisioterapia",
    "Gastroenterología",
    "Neurología",
    "Pediatría",
    "Otra"
];

// --- LISTA DE OPCIONES PARA DISPONIBILIDAD ---
$disponibilidades = [
    "Matutino" => "Matutino (9:00 - 13:30)",
    "Vespertino" => "Vespertino (14:00 - 20:30)",
    "Ambos" => "Ambos turnos (9:00 - 20:30)"
];

// Variables que vienen del controlador
$medicoEspecialidadActual = $datosMedico['especialidad'] ?? '';
$medicoDisponibilidadActual = $datosMedico['disponibilidad'] ?? 'Ambos';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Editar Médico</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?action=manageMedicos" class="btn btn-sm btn-outline-secondary d-flex align-items-center">
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

        <form action="index.php?action=updateMedico" method="POST">
            
            <input type="hidden" name="idMedico" value="<?php echo $datosMedico['idMedico']; ?>">
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="nombre" class="form-label">Nombre(s):</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($datosMedico['nombre']); ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="apellidoPaterno" class="form-label">Apellido Paterno:</label>
                    <input type="text" class="form-control" id="apellidoPaterno" name="apellidoPaterno" value="<?php echo htmlspecialchars($datosMedico['apellidoPaterno']); ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="apellidoMaterno" class="form-label">Apellido Materno:</label>
                    <input type="text" class="form-control" id="apellidoMaterno" name="apellidoMaterno" value="<?php echo htmlspecialchars($datosMedico['apellidoMaterno']); ?>">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-8 mb-3">
                    <label for="correo" class="form-label">Correo Electrónico:</label>
                    <input type="email" class="form-control" id="correo" name="correo" value="<?php echo htmlspecialchars($datosMedico['correo']); ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="estado" class="form-label">Estado:</label>
                    <select class="form-select" id="estado" name="estado" required>
                        <option value="1" <?php echo ($datosMedico['estado'] == 1) ? 'selected' : ''; ?>>Activo</option>
                        <option value="0" <?php echo ($datosMedico['estado'] == 0) ? 'selected' : ''; ?>>Inactivo</option>
                    </select>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="especialidad" class="form-label">Especialidad:</label>
                    <select class="form-select" id="especialidad" name="especialidad" required>
                        <option value="" disabled>-- Selecciona una especialidad --</option>
                        <?php foreach ($especialidades as $esp): ?>
                            <option value="<?php echo $esp; ?>" <?php echo ($medicoEspecialidadActual == $esp) ? 'selected' : ''; ?>>
                                <?php echo $esp; ?>
                            </option>
                        <?php endforeach; ?>
                        <?php if (!in_array($medicoEspecialidadActual, $especialidades) && $medicoEspecialidadActual != ''): ?>
                            <option value="<?php echo htmlspecialchars($medicoEspecialidadActual); ?>" selected>
                                <?php echo htmlspecialchars($medicoEspecialidadActual); ?> (Guardada)
                            </option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="cedulaProfesional" class="form-label">Cédula Profesional:</label>
                    <input type="text" class="form-control" id="cedulaProfesional" name="cedulaProfesional" value="<?php echo htmlspecialchars($datosMedico['cedulaProfesional']); ?>">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="telefono" class="form-label">Teléfono:</label>
                    <input type="tel" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($datosMedico['telefono'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="disponibilidad" class="form-label">Disponibilidad (Turno):</label>
                    <select class="form-select" id="disponibilidad" name="disponibilidad" required>
                        <?php foreach ($disponibilidades as $valor => $texto): ?>
                             <option value="<?php echo $valor; ?>" <?php echo ($medicoDisponibilidadActual == $valor) ? 'selected' : ''; ?>>
                                <?php echo $texto; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
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
                <button type="submit" class="btn btn-primary" name="actualizarMedico">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>