<?php 
    // ---- CONFIGURACIÓN DE LA VISTA ----
    $pageTitle = "Registro de Paciente";
    $isAuthPage = true; // Usar el fondo gris y centrado
    
    include_once 'app/views/layout/header.php'; 
?>

<div class="container auth-container">
    <div class="row justify-content-center w-100">
        <!-- Hacemos la tarjeta un poco más ancha para los nuevos campos -->
        <div class="col-md-8 col-lg-7"> 
            
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-md-5">
                    
                    <!-- Logo (Opción 3) -->
                    <div class="text-center mb-4">
                        <h1 class="h3 fw-bold text-primary">
                            <!-- Puedes reemplazar <i> por <img src="ruta/a/tu/logo.svg"> -->
                            <i class="bi bi-bar-chart-line-fill"></i>
                            HealthyView
                        </h1>
                    </div>

                    <h2 class="h4 card-title text-center mb-4">Registro de nuevo paciente</h2>

                    <?php
                        // Mostrar errores si existen
                        if (isset($error)) {
                            echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($error) . '</div>';
                        }
                    ?>

                    <!-- El 'action' apunta al método 'register' del controlador -->
                    <form action="index.php?action=register" method="POST">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre(s):</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="apellidoPaterno" class="form-label">Apellido Paterno:</label>
                                <input type="text" class="form-control" id="apellidoPaterno" name="apellidoPaterno" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="apellidoMaterno" class="form-label">Apellido Materno: <span class="text-muted small">(Opcional)</span></label>
                                <input type="text" class="form-control" id="apellidoMaterno" name="apellidoMaterno">
                            </div>
                        </div>

                        <!-- === NUEVOS CAMPOS AÑADIDOS === -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fechaNacimiento" class="form-label">Fecha de Nacimiento:</label>
                                <input type="date" class="form-control" id="fechaNacimiento" name="fechaNacimiento" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="genero" class="form-label">Género:</label>
                                <select class="form-select" id="genero" name="genero" required>
                                    <option value="" selected disabled>Seleccionar...</option>
                                    <option value="Masculino">Masculino</option>
                                    <option value="Femenino">Femenino</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                        </div>
                        <!-- === FIN DE NUEVOS CAMPOS === -->

                        <div class="mb-3">
                            <label for="correo" class="form-label">Correo Electrónico:</label>
                            <input type="email" class="form-control" id="correo" name="correo" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="pass" class="form-label">Contraseña:</label>
                                <input type="password" class="form-control" id="pass" name="pass" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="pass_confirm" class="form-label">Confirmar Contraseña:</label>
                                <input type="password" class="form-control" id="pass_confirm" name="pass_confirm" required>
                            </div>
                        </div>

                        <div class="d-grid mt-3">
                            <button type="submit" class="btn btn-primary btn-lg" name="enviar">Crear cuenta</button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-4">
                        <a href="index.php?action=showLogin">¿Ya tienes cuenta? Inicia Sesión</a>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
    // Incluir el pie de página (cierra el HTML y carga JS)
    include_once 'app/views/layout/footer.php'; 
?>

