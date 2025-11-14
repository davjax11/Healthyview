<?php
/**
 * Vista para "Detalle de Receta" (Paciente).
 * Esta vista es "inyectada" por layout_paciente.php
 *
 * Variables disponibles:
 * $datosReceta (array): Datos de la receta (resumen, médico, etc.).
 * $listaItems (array): Lista de ítems (medicamentos) de la receta.
 * $activePage (string): 'recetas'.
 */
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Detalle de Receta</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?action=verRecetas" class="btn btn-sm btn-outline-secondary d-flex align-items-center">
            <i class="bi bi-arrow-left me-1"></i>
            Volver a Mis Recetas
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <h5 class="card-title"><?php echo htmlspecialchars($datosReceta['resumen']); ?></h5>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <strong>Médico:</strong> <?php echo htmlspecialchars($datosReceta['medicoNombre'] . ' (' . $datosReceta['especialidad'] . ')'); ?>
            </li>
            <li class="list-group-item">
                <strong>Fecha de Emisión:</strong> <?php echo htmlspecialchars(date("d/m/Y", strtotime($datosReceta['fechaEmision']))); ?>
            </li>
            <li class="list-group-item">
                <strong>Observaciones Generales:</strong>
                <p class="mb-0"><?php echo nl2br(htmlspecialchars($datosReceta['observaciones'] ?? 'Sin observaciones generales.')); ?></p>
            </li>
        </ul>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-light">
        <h5 class="mb-0">Medicamentos e Indicaciones</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="table-light">
                    <tr>
                        <th>Indicación / Medicamento</th>
                        <th>Dosis</th>
                        <th>Frecuencia</th>
                        <th>Duración</th>
                        <th>Instrucciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($listaItems)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">Esta receta no tiene ítems detallados.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($listaItems as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['nombreMedicamento']); ?></td>
                                <td><?php echo htmlspecialchars($item['dosis']); ?></td>
                                <td><?php echo htmlspecialchars($item['frecuencia']); ?></td>
                                <td><?php echo htmlspecialchars($item['duracion']); ?></td>
                                <td><?php echo htmlspecialchars($item['instrucciones']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>