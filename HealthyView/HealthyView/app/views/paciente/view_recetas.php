<?php
/**
 * Vista para "Mis Recetas" (Paciente).
 * Esta vista es "inyectada" por layout_paciente.php
 *
 * Variables disponibles:
 * $listaRecetas (array): Lista de recetas cargada desde el controlador.
 * $activePage (string): 'recetas'.
 */

$errorMessage = $_GET['error'] ?? null;
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Mis Recetas</h1>
</div>

<?php if ($errorMessage == 'no_encontrada'): ?>
    <div class="alert alert-danger">
        Error: No se pudo encontrar la receta solicitada.
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Fecha de Emisión</th>
                        <th scope="col">Médico</th>
                        <th scope="col">Resumen de la Receta</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($listaRecetas)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">Aún no tienes recetas asignadas.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($listaRecetas as $receta): ?>
                            <tr>
                                <td><?php echo htmlspecialchars(date("d/m/Y", strtotime($receta['fechaEmision']))); ?></td>
                                <td><?php echo htmlspecialchars($receta['medicoNombre']); ?></td>
                                <td><?php echo htmlspecialchars($receta['resumen']); ?></td>
                                <td>
                                    <?php 
                                        $estado = htmlspecialchars($receta['estado']);
                                        $badgeClass = 'bg-secondary';
                                        if ($estado == 'Activa') $badgeClass = 'bg-success';
                                        if ($estado == 'Finalizada') $badgeClass = 'bg-secondary';
                                        if ($estado == 'Cancelada') $badgeClass = 'bg-danger';
                                    ?>
                                    <span class="badge <?php echo $badgeClass; ?>"><?php echo $estado; ?></span>
                                </td>
                                <td>
                                    <a href="index.php?action=verDetalleReceta&idReceta=<?php echo $receta['idReceta']; ?>" class="btn btn-sm btn-outline-primary" title="Ver Detalles">
                                        <i class="bi bi-eye-fill"></i> Ver Detalles
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