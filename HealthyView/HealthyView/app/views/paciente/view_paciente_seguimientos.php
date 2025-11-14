<?php
/**
 * Vista para "Mis Seguimientos" (Paciente).
 * Esta vista es "inyectada" por layout_paciente.php
 *
 * Variables disponibles:
 * $historialSeguimiento (array): Historial de seguimientos cargado desde el controlador.
 * $activePage (string): 'seguimientos'.
 */
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Mis Seguimientos</h1>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <p class="card-text text-muted">Aquí puedes ver el historial de seguimientos que tu médico ha registrado en cada consulta.</p>
        
        <div class="table-responsive mt-3">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Fecha de Registro</th>
                        <th scope="col">Registrado por</th>
                        <th scope="col">Peso (kg)</th>
                        <th scope="col">IMC</th>
                        <th scope="col">Nivel de Bienestar</th>
                        <th scope="col">Observaciones Médicas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($historialSeguimiento)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">Aún no tienes seguimientos registrados.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($historialSeguimiento as $seguimiento): ?>
                            <tr>
                                <td><?php echo htmlspecialchars(date("d/m/Y h:i A", strtotime($seguimiento['fechaRegistro']))); ?></td>
                                <td><?php echo htmlspecialchars($seguimiento['medicoNombre'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($seguimiento['peso']); ?></td>
                                <td><?php echo ($seguimiento['imc']) ? number_format($seguimiento['imc'], 2) : 'N/A'; ?></td>
                                <td>
                                    <?php 
                                        $bienestar = htmlspecialchars($seguimiento['nivelBienestar']);
                                        $badgeClass = 'bg-secondary';
                                        if ($bienestar == 'Excelente') $badgeClass = 'bg-success';
                                        if ($bienestar == 'Bueno') $badgeClass = 'bg-primary';
                                        if ($bienestar == 'Regular') $badgeClass = 'bg-warning text-dark';
                                        if ($bienestar == 'Malo') $badgeClass = 'bg-danger';
                                    ?>
                                    <span class="badge <?php echo $badgeClass; ?>"><?php echo $bienestar; ?></span>
                                </td>
                                <td><?php echo htmlspecialchars($seguimiento['observaciones'] ?? 'Sin observaciones.'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>