<?php
/**
 * Vista de CONTENIDO para "Gestionar Actividades" (Administrador).
 * Esta vista es "inyectada" por layout_admin.php
 *
 * Variables disponibles:
 * $listaActividades (array): Lista de actividades cargadas desde el controlador.
 * $error (string|null): Mensaje de error (si hubo).
 * $success (string|null): Mensaje de éxito (si hubo).
 */

// Opciones para el <select> de Tipo
$tiposActividad = ['Física', 'Alimentaria', 'Mental', 'Otro'];
?>

<!-- Encabezado del contenido -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Gestionar Catálogo de Actividades</h1>
</div>

<!-- 1. Formulario para Registrar Nueva Actividad -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4 p-md-5">
        <h3 class="h4 mb-4">Registrar Nueva Actividad</h3>

        <!-- Mensajes de Alerta -->
        <?php if ($error ?? null): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success ?? null): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form action="index.php?action=manageActividades" method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nombre" class="form-label">Nombre de la Actividad:</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ej: Caminata de 30 min" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="tipo" class="form-label">Tipo de Actividad:</label>
                    <select class="form-select" id="tipo" name="tipo" required>
                        <?php foreach ($tiposActividad as $tipo): ?>
                            <option value="<?php echo $tipo; ?>"><?php echo $tipo; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción:</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="2" placeholder="Ej: Caminata a paso moderado sin parar"></textarea>
            </div>
            
            <div class="mb-3">
                <label for="frecuencia" class="form-label">Frecuencia Recomendada:</label>
                <input type="text" class="form-control" id="frecuencia" name="frecuencia" placeholder="Ej: Diaria, 3 veces por semana">
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary" name="registrarActividad">Registrar Actividad</button>
            </div>
        </form>
    </div>
</div>

<!-- 2. Lista de Actividades Existentes -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-4 p-md-5">
        <h2 class="h4 mb-4">Catálogo de Actividades</h2>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Nombre</th>
                        <th scope="col">Tipo</th>
                        <th scope="col">Descripción</th>
                        <th scope="col">Frecuencia</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($listaActividades)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">No hay actividades registradas en el catálogo.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($listaActividades as $actividad): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($actividad['nombre']); ?></td>
                                <td><span class="badge bg-secondary"><?php echo htmlspecialchars($actividad['tipo']); ?></span></td>
                                <td><?php echo htmlspecialchars($actividad['descripcion']); ?></td>
                                <td><?php echo htmlspecialchars($actividad['frecuencia']); ?></td>
                                <td>
                                    <a href="index.php?action=showEditarActividad&idActividad=<?php echo $actividad['idActividad']; ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <!-- NOTA: No hay botón de eliminar para prevenir romper el historial de pacientes (ON DELETE RESTRICT) -->
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
    </div>
</div>