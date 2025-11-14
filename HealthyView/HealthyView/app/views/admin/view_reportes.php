<?php
/**
 * Vista de CONTENIDO para "Reportes" (Administrador).
 *
 * Variables disponibles:
 * $reporteGeneros (array)
 * $reporteCitas (array)
 * $reporteActividades (array)
 * $reporteRanking (array)
 */
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Reportes del Sistema</h1>
</div>

<div class="row">

    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-light">
                <h5 class="mb-0">Pacientes por Género</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr><th>Género</th><th>Total de Pacientes</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reporteGeneros as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['genero']); ?></td>
                                <td><?php echo htmlspecialchars($item['total']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-light">
                <h5 class="mb-0">Citas Atendidas por Médico</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr><th>Médico</th><th>Especialidad</th><th>Total Citas</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reporteCitas as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['medicoNombre']); ?></td>
                                <td><?php echo htmlspecialchars($item['especialidad']); ?></td>
                                <td><?php echo htmlspecialchars($item['totalCitas']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-light">
                <h5 class="mb-0">Actividades Más Asignadas</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr><th>Actividad</th><th>Tipo</th><th>Nº Asignaciones</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reporteActividades as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['actividadNombre']); ?></td>
                                <td><span class="badge bg-secondary"><?php echo htmlspecialchars($item['tipo']); ?></span></td>
                                <td><?php echo htmlspecialchars($item['totalAsignaciones']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-light">
                <h5 class="mb-0">Ranking Pacientes (Actividades Completadas)</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr><th>Paciente</th><th>Correo</th><th>Total Completadas</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reporteRanking as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['pacienteNombre']); ?></td>
                                <td><?php echo htmlspecialchars($item['correo']); ?></td>
                                <td><?php echo htmlspecialchars($item['totalCompletadas']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>