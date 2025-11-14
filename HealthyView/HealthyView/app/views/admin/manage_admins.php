<?php
/**
 * Vista de CONTENIDO para "Gestionar Administradores" (Administrador).
 *
 * Variables disponibles:
 * $listaAdmins (array): Lista de admins cargados desde el controlador.
 * $error (string|null): Mensaje de error (si hubo).
 * $success (string|null): Mensaje de éxito (si hubo).
 */
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Gestionar Administradores</h1>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4 p-md-5">
        <h3 class="h4 mb-4">Registrar Nuevo Administrador</h3>

        <?php if ($error ?? null): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success ?? null): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form action="index.php?action=manageAdmins" method="POST">
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
                <div class="col-md-6 mb-3">
                    <label for="correo" class="form-label">Correo Electrónico:</label>
                    <input type="email" class="form-control" id="correo" name="correo" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="departamento" class="form-label">Departamento:</label>
                    <input type="text" class="form-control" id="departamento" name="departamento" placeholder="Ej: Sistemas">
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

            <div class="d-grid">
                <button type="submit" class="btn btn-primary" name="registrarAdmin">Registrar Administrador</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4 p-md-5">
        <h2 class="h4 mb-4">Administradores Registrados</h2>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Nombre Completo</th>
                        <th scope="col">Departamento</th>
                        <th scope="col">Correo</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($listaAdmins)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No hay administradores registrados.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($listaAdmins as $admin): ?>
                            <tr>
                                <td><?php echo $admin['idAdmin']; ?></td>
                                <td><?php echo htmlspecialchars($admin['nombre'] . ' ' . $admin['apellidoPaterno']); ?></td>
                                <td><?php echo htmlspecialchars($admin['departamento'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($admin['correo']); ?></td>
                                
                                <td>
                                    <?php if ($admin['estado'] == 1): ?>
                                        <span class="badge bg-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                
                                <td>
                                    <?php if ($admin['idAdmin'] == 1): ?>
                                        <span class="text-muted small">N/A (Principal)</span>
                                    <?php else: ?>
                                        <a href="index.php?action=showEditarAdmin&idAdmin=<?php echo $admin['idAdmin']; ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        
                                        <?php if ($admin['estado'] == 1): ?>
                                            <a href="index.php?action=desactivarAdmin&idAdmin=<?php echo $admin['idAdmin']; ?>&estado=0" 
                                               class="btn btn-sm btn-outline-danger" 
                                               title="Desactivar"
                                               onclick="return confirm('¿Estás seguro de que deseas DESACTIVAR a este administrador?');">
                                                <i class="bi bi-trash-fill"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="index.php?action=desactivarAdmin&idAdmin=<?php echo $admin['idAdmin']; ?>&estado=1" 
                                               class="btn btn-sm btn-outline-success" 
                                               title="Activar"
                                               onclick="return confirm('¿Estás seguro de que deseas ACTIVAR a este administrador?');">
                                                <i class="bi bi-check-circle-fill"></i>
                                            </a>
                                        <?php endif; ?>
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