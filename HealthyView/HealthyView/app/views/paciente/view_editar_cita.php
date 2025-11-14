<?php
/**
 * Vista para "Editar Cita Programada" del Paciente.
 * Esta vista es "inyectada" por layout_paciente.php
 *
 * Variables disponibles:
 * $listaMedicos (array): Lista de médicos activos.
 * $datosCita (array): Datos de la cita que se está editando.
 * $activePage (string): 'citas'.
 */

// Mensajes de éxito o error (vienen por GET en la URL)
$errorMessage = $_GET['error'] ?? null;
$errorMap = [
    'fecha_pasada' => 'Error: No puedes programar una cita en una fecha u hora pasada.',
    'cita_duplicada' => 'Error: Ya tienes una cita programada en un horario demasiado cercano a este.',
    'medico_ocupado' => 'Error: El médico seleccionado ya tiene una cita en ese horario. Por favor, elige otra hora.',
    'hora_vacia' => 'Error: Debes seleccionar un horario disponible.',
    'error_actualizar' => 'Error: No se pudo actualizar la cita. Inténtalo de nuevo.'
];

// Pre-procesar los datos de la cita para rellenar el formulario
$fechaCitaOriginal = date('Y-m-d', strtotime($datosCita['fechaHora']));
$horaCitaOriginal = date('H:i:s', strtotime($datosCita['fechaHora'])); // Formato HH:MM:SS
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Editar Cita Programada</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?action=verCitas" class="btn btn-sm btn-outline-secondary d-flex align-items-center">
            <i class="bi bi-arrow-left me-1"></i>
            Volver a Mis Citas
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4 p-md-5">

        <?php if ($errorMessage && isset($errorMap[$errorMessage])): ?>
            <div class="alert alert-danger">
                <?php echo $errorMap[$errorMessage]; ?>
            </div>
        <?php endif; ?>
        
        <form action="index.php?action=updateCita" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas guardar los cambios en esta cita?');">
            
            <input type="hidden" name="idCita" value="<?php echo $datosCita['idCita']; ?>">
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="idMedico" class="form-label">Médico:</label>
                    <select class="form-select" id="idMedico" name="idMedico" required>
                        <option value="" disabled>-- Selecciona un médico --</option>
                        <?php foreach ($listaMedicos as $medico): ?>
                            <option value="<?php echo $medico['idMedico']; ?>" <?php echo ($medico['idMedico'] == $datosCita['idMedico']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($medico['nombre'] . ' ' . $medico['apellidoPaterno'] . ' (' . $medico['especialidad'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="fecha" class="form-label">Fecha de la Cita:</label>
                    <?php
                        $hoy = date('Y-m-d');
                    ?>
                    <input type="date" class="form-control" id="fecha" name="fecha" min="<?php echo $hoy; ?>" value="<?php echo $fechaCitaOriginal; ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="hora" class="form-label">Hora Disponible:</label>
                <select class="form-select" id="hora" name="hora" required>
                    <option value="" selected>-- Cargando horarios... --</option>
                </select>
                <div id="loadingSpinner" class="spinner-border spinner-border-sm text-primary mt-2 d-none" role="status">
                    <span class="visually-hidden">Buscando horarios...</span>
                </div>
            </div>

            <div class="mb-3">
                <label for="motivo" class="form-label">Motivo de la Cita:</label>
                <textarea class="form-control" id="motivo" name="motivo" rows="3" placeholder="Ej: Consulta de seguimiento, Revisión de dieta..." required><?php echo htmlspecialchars($datosCita['motivo']); ?></textarea>
            </div>

            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary btn-lg" name="actualizarCita">Guardar Cambios</button>
            </div>

        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        const idMedicoSelect = document.getElementById('idMedico');
        const fechaInput = document.getElementById('fecha');
        const horaSelect = document.getElementById('hora');
        const loadingSpinner = document.getElementById('loadingSpinner');
        
        const originalHora = '<?php echo $horaCitaOriginal; ?>';
        const originalFecha = '<?php echo $fechaCitaOriginal; ?>';
        const originalIdMedico = '<?php echo $datosCita['idMedico']; ?>';

        idMedicoSelect.addEventListener('change', actualizarHorariosDisponibles);
        fechaInput.addEventListener('input', actualizarHorariosDisponibles);

        async function actualizarHorariosDisponibles() {
            const idMedico = idMedicoSelect.value;
            const fecha = fechaInput.value;

            horaSelect.innerHTML = '<option value="">Buscando horarios...</option>';
            horaSelect.disabled = true;

            if (!idMedico || !fecha) {
                horaSelect.innerHTML = '<option value="">-- Primero selecciona un médico y una fecha --</option>';
                return;
            }

            loadingSpinner.classList.remove('d-none');

            try {
                // 1. Llamar a la API
                const response = await fetch(`index.php?action=getHorariosDisponibles&idMedico=${idMedico}&fecha=${fecha}`);
                if (!response.ok) throw new Error('Error en la respuesta de la red');
                
                // --- INICIO DE CORRECCIÓN ---
                const data = await response.json();
                const horasOcupadas = data.ocupadas;
                const disponibilidad = data.disponibilidad;
                // --- FIN DE CORRECCIÓN ---

                if (data.error) throw new Error(data.error);

                horaSelect.innerHTML = '';
                
                // --- INICIO DE CORRECCIÓN: Definir rangos de hora según disponibilidad ---
                let horaInicio = 9;
                let horaFin = 20; 

                if (disponibilidad === 'Matutino') {
                    horaInicio = 9;
                    horaFin = 13;
                } else if (disponibilidad === 'Vespertino') {
                    horaInicio = 14;
                    horaFin = 20;
                }
                // --- FIN DE CORRECCIÓN ---
                
                const fechaSeleccionada = new Date(fecha + 'T00:00:00');
                const hoy = new Date();
                hoy.setHours(0, 0, 0, 0);
                
                let hayHorariosDisponibles = false;
                let opcionSeleccionada = false;

                for (let h = horaInicio; h <= horaFin; h++) {
                    for (let m = 0; m < 60; m += 30) {
                        
                        const horaStr = h.toString().padStart(2, '0');
                        const minStr = m.toString().padStart(2, '0');
                        const timeString = `${horaStr}:${minStr}:00`;
                        const displayTime = `${horaStr}:${minStr}`;

                        let isDisabled = false;
                        
                        if (horasOcupadas.includes(timeString)) {
                            isDisabled = true;
                        }

                        if (fechaSeleccionada.getTime() === hoy.getTime()) {
                            const horaActual = new Date().getHours();
                            const minActual = new Date().getMinutes();
                            if (h < horaActual || (h === horaActual && m < minActual)) {
                                isDisabled = true;
                            }
                        }

                        // Lógica de edición
                        const esSlotOriginal = (timeString === originalHora && fecha === originalFecha && idMedico === originalIdMedico);
                        
                        if (esSlotOriginal) {
                            isDisabled = false;
                        }

                        const option = document.createElement('option');
                        option.value = timeString;
                        option.textContent = displayTime;
                        
                        if (isDisabled) {
                            option.disabled = true;
                            option.textContent += ' (Ocupado)';
                        } else {
                            hayHorariosDisponibles = true;
                        }
                        
                        if (esSlotOriginal) {
                            option.selected = true;
                            opcionSeleccionada = true;
                        }
                        
                        horaSelect.appendChild(option);
                    }
                }
                
                if (!hayHorariosDisponibles && !opcionSeleccionada) {
                     horaSelect.innerHTML = '<option value="" disabled>-- No hay horarios disponibles para este día/turno --</option>';
                } else if (!opcionSeleccionada) {
                    const placeholder = document.createElement('option');
                    placeholder.value = "";
                    placeholder.textContent = "-- Selecciona un horario --";
                    placeholder.selected = true;
                    horaSelect.prepend(placeholder);
                }

            } catch (error) {
                console.error('Error al cargar horarios:', error);
                horaSelect.innerHTML = '<option value="">Error al cargar horarios</option>';
            } finally {
                loadingSpinner.classList.add('d-none');
                horaSelect.disabled = false;
            }
        }
        
        // Cargar los horarios al iniciar la página
        actualizarHorariosDisponibles();
    });
</script>