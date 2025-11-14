<?php
/**
 * Vista de CONTENIDO para "Gestionar Pacientes" (Administrador).
 * Esta vista es "inyectada" por layout_admin.php
 *
 * Variables disponibles:
 * $listaPacientes (array): Lista de pacientes cargados desde el controlador.
 * $error (string|null): Mensaje de error (si hubo).
 * $success (string|null): Mensaje de éxito (si hubo).
 */
?>

<!-- Encabezado del contenido -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Gestionar Pacientes</h1>
    <!-- Puedes añadir un botón de "Crear Paciente" aquí si lo deseas en el futuro -->
</div>

<!-- Mensajes de Alerta -->
<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<!-- Lista de Pacientes Existentes -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-4 p-md-5">
        
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Nombre Completo</th>
                        <th scope="col">Correo Electrónico</th>
                        <th scope="col">Teléfono</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($listaPacientes)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No hay pacientes registrados.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($listaPacientes as $paciente): ?>
                            <tr>
                                <td><?php echo $paciente['idPaciente']; ?></td>
                                <td><?php echo htmlspecialchars($paciente['nombre'] . ' ' . $paciente['apellidoPaterno']); ?></td>
                                <td><?php echo htmlspecialchars($paciente['correo']); ?></td>
                                <td><?php echo htmlspecialchars($paciente['telefono'] ?? 'N/A'); ?></td>
                                
                                <!-- Estado (Activo/Inactivo) -->
                                <td>
                                    <?php if ($paciente['estado'] == 1): ?>
                                        <span class="badge bg-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                
                                <!-- Botones de Acción -->
                                <td>
                                    <!-- 1. Botón Editar -->
                                    <a href="index.php?action=showEditarPaciente&idPaciente=<?php echo $paciente['idPaciente']; ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    
                                    <!-- 2. Botón Desactivar/Activar -->
                                    <?php if ($paciente['estado'] == 1): ?>
                                        <a href="index.php?action=desactivarPaciente&idPaciente=<?php echo $paciente['idPaciente']; ?>&estado=0" 
                                           class="btn btn-sm btn-outline-danger" 
                                           title="Desactivar"
                                           onclick="return confirm('¿Estás seguro de que deseas DESACTIVAR a este paciente?');">
                                            <i class="bi bi-trash-fill"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="index.php?action=desactivarPaciente&idPaciente=<?php echo $paciente['idPaciente']; ?>&estado=1" 
                                           class="btn btn-sm btn-outline-success" 
                                           title="Activar"
                                           onclick="return confirm('¿Estás seguro de que deseas ACTIVAR a este paciente?');">
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
                                        