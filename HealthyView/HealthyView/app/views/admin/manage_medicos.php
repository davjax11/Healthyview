<?php
/**
 * Vista de CONTENIDO para "Gestionar Médicos" (Administrador).
 * Esta vista es "inyectada" por layout_admin.php
 *
 * Variables disponibles:
 * $listaMedicos (array): Lista de médicos cargados desde el controlador.
 * $error (string|null): Mensaje de error (si hubo).
 * $success (string|null): Mensaje de éxito (si hubo).
 */

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

?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Gestionar Médicos</h1>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4 p-md-5">
        <h3 class="h4 mb-4">Registrar Nuevo Médico</h3>

        <?php if ($error ?? null): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success ?? null): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form action="index.php?action=manageMedicos" method="POST">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="nombre" class="form-label">Nombre(s):</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="apellidoPaterno" class="form-label">Apellido Paterno:</label>
                    <input type="text" class="form-control" id="apellidoPaterno" name="apellidoPaterno" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="apellidoMaterno" class="form-label">Apellido Materno:</label>
                    <input type="text" class="form-control" id="apellidoMaterno" name="apellidoMaterno">
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="correo" class="form-label">Correo Electrónico:</label>
                    <input type="email" class="form-control" id="correo" name="correo" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="pass" class="form-label">Contraseña Temporal:</label>
                    <input type="password" class="form-control" id="pass" name="pass" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="pass_confirm" class="form-label">Confirmar Contraseña:</label>
                    <input type="password" class="form-control" id="pass_confirm" name="pass_confirm" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="especialidad" class="form-label">Especialidad:</label>
                    <select class="form-select" id="especialidad" name="especialidad" required>
                        <option value="" disabled selected>-- Selecciona una especialidad --</option>
                        <?php foreach ($especialidades as $esp): ?>
                            <option value="<?php echo $esp; ?>"><?php echo $esp; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="cedulaProfesional" class="form-label">Cédula Profesional:</label>
                    <input type="text" class="form-control" id="cedulaProfesional" name="cedulaProfesional" placeholder="Ej: 12345678">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="telefono" class="form-label">Teléfono:</label>
                    <input type="tel" class="form-control" id="telefono" name="telefono" placeholder="Ej: 5512345678">
                </div>
                 <div class="col-md-6 mb-3">
                    <label for="disponibilidad" class="form-label">Disponibilidad (Turno):</label>
                    <select class="form-select" id="disponibilidad" name="disponibilidad" required>
                        <option value="Ambos" selected>Ambos turnos (Default)</option>
                        <option value="Matutino">Matutino (9:00 - 13:30)</option>
                        <option value="Vespertino">Vespertino (14:00 - 20:30)</option>
                    </select>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary" name="registrarMedico">Registrar Médico</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4 p-md-5">
        <h2 class="h4 mb-4">Médicos Registrados</h2>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Nombre Completo</th>
                        <th scope="col">Especialidad</th>
                        <th scope="col">Teléfono</th>
                        <th scope="col">Disponibilidad</th>
                        <th scope="col">Correo</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($listaMedicos)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">No hay médicos registrados.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($listaMedicos as $medico): ?>
                            <tr>
                                <td><?php echo $medico['idMedico']; ?></td>
                                <td><?php echo htmlspecialchars($medico['nombre'] . ' ' . $medico['apellidoPaterno']); ?></td>
                                <td><?php echo htmlspecialchars($medico['especialidad']); ?></td>
                                <td><?php echo htmlspecialchars($medico['telefono'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($medico['disponibilidad'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($medico['correo']); ?></td>
                                
                                <td>
                                    <?php if ($medico['estado'] == 1): ?>
                                        <span class="badge bg-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                
                                <td>
                                    <a href="index.php?action=showEditarMedico&idMedico=<?php echo $medico['idMedico']; ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    
                                    <?php if ($medico['estado'] == 1): ?>
                                        <a href="index.php?action=desactivarMedico&idMedico=<?php echo $medico['idMedico']; ?>&estado=0" 
                                           class="btn btn-sm btn-outline-danger" 
                                           title="Desactivar"
                                           onclick="return confirm('¿Estás seguro de que deseas DESACTIVAR a este médico?');">
                                            <i class="bi bi-trash-fill"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="index.php?action=desactivarMedico&idMedico=<?php echo $medico['idMedico']; ?>&estado=1" 
                                           class="btn btn-sm btn-outline-success" 
                                           title="Activar"
                                           onclick="return confirm('¿Estás seguro de que deseas ACTIVAR a este médico?');">
                                            <i class="bi bi-check-circle-fill"></i>
                                        </a>
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