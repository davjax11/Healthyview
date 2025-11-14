<?php
/**
 * Vista para "Mi Perfil" del Paciente.
 * Esta vista es "inyectada" por layout_paciente.php
 *
 * Variables disponibles:
 * $datosPaciente (array): Datos del paciente cargados desde el controlador.
 * $activePage (string): 'perfil' (aunque no se usa en la sidebar, es para el controlador).
 */

// Mensajes de éxito o error (vienen por GET en la URL)
$successMessage = $_GET['success'] ?? null;
$errorMessage = $_GET['error'] ?? null;
?>

<!-- Encabezado del contenido -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Mi Perfil</h1>
</div>

<!-- Formulario de "Mi Perfil" -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-4 p-md-5">

        <!-- Mensajes de Alerta -->
        <?php if ($successMessage): ?>
            <div class="alert alert-success">
                ¡Perfil actualizado exitosamente!
            </div>
        <?php endif; ?>
        <?php if ($errorMessage): ?>
            <div class="alert alert-danger">
                Error al actualizar el perfil. Inténtalo de nuevo.
            </div>
        <?php endif; ?>
        
        <!-- Formulario que envía a la acción 'updateProfile' -->
        <form action="index.php?action=updateProfile" method="POST">
            
            <!-- Sección de Datos Personales -->
            <h5 class="mb-3">Datos Personales</h5>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="nombre" class="form-label">Nombre(s):</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($datosPaciente['nombre']); ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="apellidoPaterno" class="form-label">Apellido Paterno:</label>
                    <input type="text" class="form-control" id="apellidoPaterno" name="apellidoPaterno" value="<?php echo htmlspecialchars($datosPaciente['apellidoPaterno']); ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="apellidoMaterno" class="form-label">Apellido Materno:</label>
                    <input type="text" class="form-control" id="apellidoMaterno" name="apellidoMaterno" value="<?php echo htmlspecialchars($datosPaciente['apellidoMaterno']); ?>">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="fechaNacimiento" class="form-label">Fecha de Nacimiento:</label>
                    <!-- MODIFICADO: Cambiado a type="date" y quitado readonly -->
                    <input type="date" class="form-control" id="fechaNacimiento" name="fechaNacimiento" value="<?php echo htmlspecialchars($datosPaciente['fechaNacimiento']); ?>">
                </div>
                <div class="col-md-2 mb-3">
                    <!-- NUEVO: Campo para Edad (calculada) -->
                    <label for="edad" class="form-label">Edad:</label>
                    <input type="text" class="form-control" id="edad" name="edad" readonly disabled placeholder="--">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="genero" class="form-label">Género:</label>
                    <!-- MODIFICADO: Cambiado a <select> -->
                    <select class="form-select" id="genero" name="genero">
                        <option value="Masculino" <?php echo ($datosPaciente['genero'] == 'Masculino') ? 'selected' : ''; ?>>Masculino</option>
                        <option value="Femenino" <?php echo ($datosPaciente['genero'] == 'Femenino') ? 'selected' : ''; ?>>Femenino</option>
                        <option value="Otro" <?php echo ($datosPaciente['genero'] == 'Otro') ? 'selected' : ''; ?>>Otro</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label for="correo" class="form-label">Correo Electrónico:</label>
                <!-- MODIFICADO: Quitado readonly -->
                <input type="email" class="form-control" id="correo" name="correo" value="<?php echo htmlspecialchars($datosPaciente['correo']); ?>" required>
            </div>

            <hr class="my-4">

            <!-- Sección de Datos de Contacto y Salud -->
            <h5 class="mb-3">Datos de Contacto y Salud</h5>
            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono:</label>
                <input type="tel" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($datosPaciente['telefono'] ?? ''); ?>" placeholder="Ej: 5512345678">
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="peso" class="form-label">Peso (kg):</label>
                    <input type="number" class="form-control" id="peso" name="peso" step="0.1" value="<?php echo htmlspecialchars($datosPaciente['peso'] ?? 0.0); ?>" placeholder="Ej: 70.5" readonly>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="estatura" class="form-label">Estatura (m):</label>
                    <input type="number" class="form-control" id="estatura" name="estatura" step="0.01" value="<?php echo htmlspecialchars($datosPaciente['estatura'] ?? 0.0); ?>" placeholder="Ej: 1.75" readonly>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="imc" class="form-label">IMC (calculado):</label>
                    <input type="text" class="form-control" id="imc" name="imc" readonly disabled>
                </div>
            </div>
            <small class="text-muted d-block mb-3">El peso y la estatura se actualizan desde la sección "Seguimientos".</small>

            <div class="mb-3">
                <label for="diagnostico" class="form-label">Diagnóstico (Opcional):</label>
                <textarea class="form-control" id="diagnostico" name="diagnostico" rows="3" placeholder="Ej: Hipertensión Leve, Alergia a nueces..."><?php echo htmlspecialchars($datosPaciente['diagnostico'] ?? ''); ?></textarea>
            </div>

            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary btn-lg" name="actualizar">Guardar Cambios</button>
            </div>

        </form>
    </div>
</div>

<!-- Script para calcular el IMC y la Edad automáticamente -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // --- Cálculo de IMC ---
        const pesoInput = document.getElementById('peso');
        const estaturaInput = document.getElementById('estatura');
        const imcInput = document.getElementById('imc');

        function calcularIMC() {
            const peso = parseFloat(pesoInput.value);
            const estatura = parseFloat(estaturaInput.value);

            if (peso > 0 && estatura > 0) {
                const imc = peso / (estatura * estatura);
                imcInput.value = imc.toFixed(2);
            } else {
                imcInput.value = '';
            }
        }
        calcularIMC(); // Calcular al cargar
        pesoInput.addEventListener('input', calcularIMC);
        estaturaInput.addEventListener('input', calcularIMC);


        // --- NUEVO: Cálculo de Edad ---
        const fechaNacimientoInput = document.getElementById('fechaNacimiento');
        const edadInput = document.getElementById('edad');

        function calcularEdad() {
            const fechaNacimiento = fechaNacimientoInput.value;
            if (!fechaNacimiento) {
                edadInput.value = '--';
                return;
            }

            const hoy = new Date();
            const nacimiento = new Date(fechaNacimiento);
            let edad = hoy.getFullYear() - nacimiento.getFullYear();
            const mes = hoy.getMonth() - nacimiento.getMonth();

            if (mes < 0 || (mes === 0 && hoy.getDate() < nacimiento.getDate())) {
                edad--;
            }

            edadInput.value = (edad >= 0) ? edad : '--';
        }
        
        calcularEdad(); // Calcular al cargar
        fechaNacimientoInput.addEventListener('input', calcularEdad); // Calcular al cambiar la fecha
    });
</script>

