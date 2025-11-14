<?php
/**
 * Vista de CONTENIDO para "Gestionar Ítems de Receta" (Médico).
 * Esta vista es "inyectada" por layout_medico.php
 *
 * Variables disponibles:
 * $datosReceta (array): Datos de la receta (resumen, pacienteNombre, idPaciente).
 * $listaItems (array): Lista de ítems (medicamentos) de la receta.
 * $activePage (string): 'pacientes'.
 * $successMessage (string|null): Mensaje de éxito.
 * $errorMessage (string|null): Mensaje de error.
 */
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Gestionar Receta</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?action=verPerfilPaciente&idPaciente=<?php echo $datosReceta['idPaciente']; ?>" class="btn btn-sm btn-outline-secondary d-flex align-items-center">
            <i class="bi bi-arrow-left me-1"></i>
            Volver al Perfil de <?php echo htmlspecialchars($datosReceta['pacienteNombre']); ?>
        </a>
    </div>
</div>

<?php if (isset($successMessage)): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($successMessage); ?>
    </div>
<?php endif; ?>
<?php if (isset($errorMessage)): ?>
    <div class="alert alert-danger">
        <?php echo htmlspecialchars($errorMessage); ?>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Añadir elementos a la Receta</h5>
            </div>
            <div class="card-body">
                <form action="index.php?action=agregarItemReceta" method="POST">
                    <input type="hidden" name="idReceta" value="<?php echo $datosReceta['idReceta']; ?>">
                    <input type="hidden" name="idPaciente" value="<?php echo $datosReceta['idPaciente']; ?>">
                    
                    <div class="mb-3">
                        <label for="nombreMedicamento" class="form-label">Medicamento / Indicación:</label>
                        <input type="text" class="form-control" id="nombreMedicamento" name="nombreMedicamento" placeholder="Ej: Paracetamol 500mg" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="dosis" class="form-label">Dosis:</label>
                            <input type="text" class="form-control" id="dosis" name="dosis" placeholder="Ej: 1 tableta">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="frecuencia" class="form-label">Frecuencia:</label>
                            <input type="text" class="form-control" id="frecuencia" name="frecuencia" placeholder="Ej: Cada 8 horas">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="duracion" class="form-label">Duración:</label>
                        <input type="text" class="form-control" id="duracion" name="duracion" placeholder="Ej: Por 3 días">
                    </div>

                    <div class="mb-3">
                        <label for="instrucciones" class="form-label">Instrucciones Adicionales:</label>
                        <textarea class="form-control" id="instrucciones" name="instrucciones" rows="2" placeholder="Ej: Tomar con alimentos"></textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary" name="agregarItem">Añadir</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">Elementos de la Receta: "<?php echo htmlspecialchars($datosReceta['resumen']); ?>"</h5>
            </div>
            <div class="card-body">
                <p><strong>Observaciones Generales:</strong> <?php echo nl2br(htmlspecialchars($datosReceta['observaciones'])); ?></p>
                <hr>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Medicamento/Indicación</th>
                                <th>Dosis</th>
                                <th>Frecuencia</th>
                                <th>Duración</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($listaItems)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Aún no hay medicamentos en esta receta.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($listaItems as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['nombreMedicamento']); ?></td>
                                        <td><?php echo htmlspecialchars($item['dosis']); ?></td>
                                        <td><?php echo htmlspecialchars($item['frecuencia']); ?></td>
                                        <td><?php echo htmlspecialchars($item['duracion']); ?></td>
                                        <td>
                                            <a href="index.php?action=eliminarItemReceta&idItem=<?php echo $item['idItem']; ?>&idReceta=<?php echo $datosReceta['idReceta']; ?>&idPaciente=<?php echo $datosReceta['idPaciente']; ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               title="Eliminar medicamento/elemento"
                                               onclick="return confirm('¿Estás seguro de que deseas eliminar este elemento de la receta?');">
                                                <i class="bi bi-trash-fill"></i>
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
    </div>
</div>