<?php 
    // ---- CONFIGURACIÓN DE LA VISTA ----
    $pageTitle = "Iniciar Sesión"; 
    $isAuthPage = true; // Usar el fondo gris y centrado
    
    // Incluir el encabezado (carga Bootstrap y el <head>)
    include_once 'app/views/layout/header.php'; 
?>

<!-- Contenedor principal para centrar el formulario -->
<div class="container auth-container">
    <div class="row justify-content-center w-100">
        <div class="col-md-6 col-lg-5 col-xl-4">
            
            <!-- Tarjeta de Bootstrap para el formulario -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-md-5">
                    
                    <!-- Logo (Opción 3: Monitor de Progreso) -->
                    <div class="text-center mb-4">
                        <h1 class="h3 fw-bold text-primary">
                            <!-- Ícono de Bootstrap que representa "View" y "Progreso" -->
                            <i class="bi bi-bar-chart-line-fill"></i>
                            HealthyView
                        </h1>
                    </div>
                    
                    <h2 class="h4 card-title text-center mb-4">Iniciar Sesión</h2>

                    <?php
                        // Mostrar error si existe
                        if (isset($error)) {
                            echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($error) . '</div>';
                        }
                        // Mostrar mensaje de registro exitoso
                        if (isset($_GET['registro']) && $_GET['registro'] == 'exitoso') {
                            echo '<div class="alert alert-success" role="alert">¡Registro exitoso! Ya puedes iniciar sesión.</div>';
                        }
                    ?>

                    <!-- Formulario adaptado a Bootstrap -->
                    <form action="index.php?action=login" method="POST">
                        <div class="mb-3">
                            <label for="correo" class="form-label">Correo Electrónico:</label>
                            <input type="email" class="form-control" id="correo" name="correo" required>
                        </div>

                        <div class="mb-3">
                            <label for="pass" class="form-label">Contraseña:</label>
                            <input type="password" class="form-control" id="pass" name="pass" required>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg" name="enviar">Iniciar sesión</button>
                        </div>
                    </form>
                    
                    <!-- Enlaces de Registro y Olvidar Contraseña -->
                    <div class="text-center mt-4">
                        <p class="mb-0 mt-2">
                            <a href="index.php?action=showRegister" class="text-decoration-none">¿No tienes cuenta? Regístrate</a>
                        </p>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
    // Incluir el pie de página (carga JS de Bootstrap y cierra el HTML)
    include_once 'app/views/layout/footer.php'; 
?>

