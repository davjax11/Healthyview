<?php
// Cargar el modelo de autenticación
include_once "app/models/AuthModel.php";

class AuthController {
    private $model;
    private $connection;

    // Constructor: recibe la conexión a la BD
    public function __construct($connection) {
        $this->connection = $connection;
        $this->model = new AuthModel($connection);
    }

    // --- ACCIONES DE AUTENTICACIÓN (PÚBLICAS) ---

    /**
     * Muestra el formulario de login
     */
    public function showLogin($error = null) {
        // Mensajes de error (si vienen por GET en la URL)
        $errorMessage = $_GET['error'] ?? $error; // $error es para fallos internos
        $errorMap = [
            'acceso_denegado' => 'Error: Debes iniciar sesión para acceder a esa página.',
            'default' => 'Error: Acción no válida o sesión expirada.'
        ];
        
        // Si el error no está en el mapa, usa el texto del error directamente
        // (ej. "Correo o contraseña incorrectos.")
        $error = $errorMap[$errorMessage] ?? $errorMessage;
        
        include_once "app/views/view_login.php";
    }

    /**
     * Procesa el formulario de login
     */
    public function login() {
        if (isset($_POST["enviar"])) {
            $correo = $_POST["correo"];
            $pass = trim($_POST["pass"]);

            // Verificar usuario
            $usuario = $this->model->verificarUsuario($correo);

            // Si el usuario existe y la contraseña es correcta
            if ($usuario && password_verify($pass, $usuario['passwordHash'])) {
                // Guardar datos en la sesión
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'] . ' ' . $usuario['apellidoPaterno'];
                $_SESSION['usuario_tipo'] = $usuario['tipoUsuario'];
                
                // Redirigir al dashboard (el index.php se encargará de enviarlo
                // al controlador de Paciente o Admin según su rol)
                header("Location: index.php?action=dashboard");
                exit();
            } else {
                $this->showLogin("Correo o contraseña incorrectos.");
            }
        } else {
            $this->showLogin();
        }
    }

    /**
     * Muestra el formulario de registro de paciente
     */
    public function showRegister($error = null) {
        include_once "app/views/view_registro.php";
    }

    /**
     * Procesa el formulario de registro de paciente (con fecha y género)
     */
    public function register() {
        if (isset($_POST["enviar"])) {
            $nombre = trim($_POST["nombre"]);
            $apellidoPaterno = trim($_POST["apellidoPaterno"]);
            $apellidoMaterno = trim($_POST["apellidoMaterno"]);
            $correo = trim($_POST["correo"]);
            $pass = trim($_POST["pass"]);
            $pass_confirm = trim($_POST["pass_confirm"]);
            
            $fechaNacimiento = $_POST["fechaNacimiento"];
            $genero = $_POST["genero"];

            // Validar que las contraseñas coincidan
            if ($pass !== $pass_confirm) {
                $this->showRegister("Las contraseñas no coinciden.");
                return;
            }
            
            // Validar que el correo no exista
            if ($this->model->verificarCorreoExistente($correo)) {
                $this->showRegister("El correo electrónico ya está registrado.");
                return;
            }

            // Hash de la contraseña
            $pass_hash = password_hash($pass, PASSWORD_BCRYPT);

            // Llamar al modelo para insertar
            $exito = $this->model->registrarPaciente($nombre, $apellidoPaterno, $apellidoMaterno, $correo, $pass_hash, $fechaNacimiento, $genero);

            if ($exito) {
                // Redirigir al login con mensaje de éxito
                header("Location: index.php?action=showLogin&registro=exitoso");
                exit();
            } else {
                $this->showRegister("Error al registrar el usuario.");
            }
        } else {
            $this->showRegister();
        }
    }

    /**
     * Cierra la sesión del usuario
     */
    public function logout() {
        session_unset();
        session_destroy();
        header("Location: index.php?action=showLogin");
        exit();
    }
}
?>

