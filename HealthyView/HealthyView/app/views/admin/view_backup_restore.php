<?php
/**
 * Vista de CONTENIDO para "Respaldo y Restauración" (Administrador).
 *
 * Variables disponibles:
 * $listaBackups (array): Nombres de archivos .sql en la carpeta backups.
 * $error, $success (strings): Mensajes de alerta.
 */
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Respaldo y Restauración de Base de Datos</h1>
</div>

<!-- Alertas -->
<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<!-- Sección de Crear Respaldo -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <h5 class="card-title">Crear Nuevo Respaldo</h5>
        <p class="card-text text-muted">Esto generará un archivo SQL con toda la estructura y datos actuales del sistema. El proceso puede tardar unos segundos.</p>
        <a href="index.php?action=crearRespaldo" class="btn btn-primary">
            <i class="bi bi-database-add me-2"></i> Generar Backup Ahora
        </a>
    </div>
</div>

<!-- Sección de Historial y Restauración -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-light">
        <h5 class="mb-0">Respaldos Disponibles</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nombre del Archivo</th>
                        <th>Fecha de Creación</th>
                        <th>Tamaño</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($listaBackups)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">No hay respaldos disponibles.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($listaBackups as $file): 
                            $filePath = 'backups/' . $file;
                            $fileSize = file_exists($filePath) ? round(filesize($filePath) / 1024, 2) . ' KB' : 'N/A';
                            // Extraer fecha del nombre si tiene formato db-backup-YYYY-MM-DD_HH-MM-SS
                            $fechaCreacion = "Desconocida";
                            if (preg_match('/db-backup-(\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2})/', $file, $matches)) {
                                $fechaCreacion = str_replace('_', ' ', $matches[1]);
                            }
                        ?>
                            <tr>
                                <td>
                                    <i class="bi bi-filetype-sql text-secondary me-2"></i>
                                    <?php echo htmlspecialchars($file); ?>
                                </td>
                                <td><?php echo htmlspecialchars($fechaCreacion); ?></td>
                                <td><?php echo $fileSize; ?></td>
                                <td class="text-end">
                                    <!-- Botón Descargar -->
                                    <a href="index.php?action=descargarRespaldo&file=<?php echo urlencode($file); ?>" class="btn btn-sm btn-outline-secondary me-1" title="Descargar archivo SQL">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    
                                    <!-- Botón Restaurar -->
                                    <a href="index.php?action=restaurarRespaldo&file=<?php echo urlencode($file); ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       title="Restaurar este punto"
                                       onclick="return confirm('⚠️ ¡ADVERTENCIA! \n\nAl restaurar este respaldo, SE BORRARÁN todos los datos actuales y se reemplazarán por los de esta copia.\n\n¿Estás completamente seguro de continuar?');">
                                        <i class="bi bi-arrow-counterclockwise"></i> Restaurar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>



