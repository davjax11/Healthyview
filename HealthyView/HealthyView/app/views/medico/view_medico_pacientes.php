<?php
/**
 * Vista de CONTENIDO para "Mis Pacientes" (Médico).
 * Esta vista es "inyectada" por layout_medico.php
 *
 * Variables disponibles:
 * $listaPacientes (array): Lista de pacientes cargada desde el controlador.
 * $activePage (string): 'pacientes'.
 */
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Mis Pacientes</h1>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Nombre Completo</th>
                        <th scope="col">Correo Electrónico</th>
                        <th scope="col">Teléfono</th>
                        <th scope="col">Progreso de Peso</th> <!-- NUEVA COLUMNA -->
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($listaPacientes)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">Aún no tienes pacientes asignados.</td> <!-- Colspan a 5 -->
                        </tr>
                    <?php else: ?>
                        <?php foreach ($listaPacientes as $paciente): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($paciente['nombre'] . ' ' . $paciente['apellidoPaterno']); ?></td>
                                <td><?php echo htmlspecialchars($paciente['correo']); ?></td>
                                <td><?php echo htmlspecialchars($paciente['telefono'] ?? 'N/A'); ?></td>
                                
                                <!-- INICIO DE NUEVA LÓGICA DE PROGRESO -->
                                <td>
                                    <?php
                                    $pesoInicial = $paciente['pesoInicial'];
                                    $pesoActual = $paciente['pesoActual'];
                                    
                                    if ($pesoInicial > 0 && $pesoActual > 0):
                                        $diferencia = $pesoActual - $pesoInicial;
                                        $diferenciaAbs = abs($diferencia);
                                        
                                        if ($diferencia < -0.1): // Perdió peso
                                            echo "<span class='badge bg-success'>Bajó {$diferenciaAbs} kg</span>";
                                        elseif ($diferencia > 0.1): // Subió peso
                                            echo "<span class='badge bg-danger'>Subió {$diferencia} kg</span>";
                                        else: // Mismo peso
                                            echo "<span class='badge bg-secondary'>Estable</span>";
                                        endif;
                                    else:
                                        echo "<span class='badge bg-light text-dark'>Sin datos</span>";
                                    endif;
                                    ?>
                                </td>
                                <!-- FIN DE NUEVA LÓGICA DE PROGRESO -->

                                <td>
                                    <a href="index.php?action=verPerfilPaciente&idPaciente=<?php echo $paciente['idPaciente']; ?>" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Ver Perfil Completo">
                                        <i class="bi bi-person-lines-fill"></i> Ver Perfil
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