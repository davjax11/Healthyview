<?php
/**
 * Controlador para todas las acciones del Paciente.
 * Gestiona el dashboard, perfil, citas y seguimientos del paciente.
 */

// Cargar el modelo de Paciente
include_once "app/models/PacienteModel.php";

class PacienteController {
    private $model;
    private $connection;

    // Constructor: recibe la conexión y crea un PacienteModel
    public function __construct($connection) {
        $this->connection = $connection;
        $this->model = new PacienteModel($connection);
    }

    // --- ACCIONES DE PACIENTE (PRIVADAS) ---

    /**
     * Muestra el dashboard principal del paciente (la sección "Inicio")
     */
    public function dashboard() {
        $activePage = 'inicio';
        $viewToLoad = 'app/views/paciente/view_paciente_inicio.php';
        include_once 'app/views/paciente/layout_paciente.php';
    }

    /**
     * Muestra la página de citas del paciente
     */
    public function verCitas() {
        $idPaciente = $_SESSION['usuario_id'];
        $listaCitas = $this->model->getCitasPaciente($idPaciente);
        $activePage = 'citas';
        $viewToLoad = 'app/views/paciente/view_citas.php';
        include_once 'app/views/paciente/layout_paciente.php';
    }

    // Marcador de posición para Actividades
    /**
     * MODIFICADO: Muestra la lista de actividades del paciente.
     */
    public function verActividades() {
        $idPaciente = $_SESSION['usuario_id'];
        
        // Manejo de mensajes de éxito/error
        $successMessage = null;
        if (isset($_GET['success']) && $_GET['success'] == 'progreso_actualizado') {
            $successMessage = "¡Progreso actualizado exitosamente!";
        }
        $errorMessage = null;
        if (isset($_GET['error']) && $_GET['error'] == 'progreso_fallido') {
            $errorMessage = "Error al actualizar el progreso. Inténtalo de nuevo.";
        }

        // 1. Obtener la lista de actividades
        $listaActividades = $this->model->getActividadesDelPaciente($idPaciente);

        $activePage = 'actividades';
        $viewToLoad = 'app/views/paciente/view_actividades.php'; // <-- Nueva Vista
        
        include_once 'app/views/paciente/layout_paciente.php';
    }
    
    
    // Marcador de posición para Seguimientos
    public function verSeguimientos() {
        $activePage = 'seguimientos';
        $viewToLoad = 'app/views/paciente/view_paciente_placeholder.php';
        $tituloVista = "Seguimientos";
        include_once 'app/views/paciente/layout_paciente.php';
    }
    
    // Marcador de posición para Recetas
    /**
     * Muestra la lista de recetas del paciente.
     */
    public function verRecetas() {
        $idPaciente = $_SESSION['usuario_id'];
        
        // 1. Obtener la lista de recetas
        $listaRecetas = $this->model->getRecetasPaciente($idPaciente);

        $activePage = 'recetas';
        $viewToLoad = 'app/views/paciente/view_recetas.php'; // <--- Nueva Vista
        
        include_once 'app/views/paciente/layout_paciente.php';
    }

    /**
     * Muestra el detalle (ítems) de una receta específica.
     */
    public function verDetalleReceta() {
        if (!isset($_GET['idReceta'])) {
            header("Location: index.php?action=verRecetas");
            exit();
        }

        $idReceta = (int)$_GET['idReceta'];
        $idPaciente = $_SESSION['usuario_id'];

        // 1. Obtener los datos de la receta (y validar que sea del paciente)
        $datosReceta = $this->model->getRecetaEspecificaPaciente($idReceta, $idPaciente);

        if (!$datosReceta) {
            // Si no es su receta, lo mandamos a la lista
            header("Location: index.php?action=verRecetas&error=no_encontrada");
            exit();
        }

        // 2. Obtener los ítems de esa receta
        $listaItems = $this->model->getItemsDeReceta($idReceta);

        $pageTitle = "Detalle de Receta";
        $activePage = 'recetas';
        $viewToLoad = 'app/views/paciente/view_receta_detalle.php'; // <--- Nueva Vista
        
        include_once 'app/views/paciente/layout_paciente.php';
    }

    // --- INICIO DE NUEVA ACCIÓN (Progreso Actividades - RFN-10) ---

    /**
     * Procesa la actualización del progreso de una actividad.
     */
    public function actualizarProgreso() {
        if (isset($_POST["actualizarProgreso"])) {
            $idAsignacion = (int)$_POST['idAsignacion'];
            $progreso = (int)$_POST['progreso'];
            $idPaciente = (int)$_SESSION['usuario_id'];

            // Limitar el progreso a 0-100
            if ($progreso < 0) $progreso = 0;
            if ($progreso > 100) $progreso = 100;

            $exito = $this->model->updateProgresoActividad($idAsignacion, $idPaciente, $progreso);

            if ($exito) {
                header("Location: index.php?action=verActividades&success=progreso_actualizado");
            } else {
                header("Location: index.php?action=verActividades&error=progreso_fallido");
            }
            exit();
        } else {
            header("Location: index.php?action=verActividades");
            exit();
        }
    }
    // --- FIN DE NUEVA ACCIÓN ---

    // --- ACCIONES DE PERFIL (PACIENTE) ---

    /**
     * Muestra la página de "Mi Perfil" con los datos actuales del paciente.
     */
    public function showProfile() {
        $idPaciente = $_SESSION['usuario_id'];
        $datosPaciente = $this->model->getDatosPaciente($idPaciente);
        if (!$datosPaciente) {
            $this->dashboard();
            return;
        }
        $activePage = 'perfil';
        $viewToLoad = 'app/views/paciente/view_perfil.php';
        include_once 'app/views/paciente/layout_paciente.php';
    }

    /**
     * Procesa la actualización de los datos del perfil del paciente.
     */
    public function updateProfile() {
        if (isset($_POST["actualizar"])) {
            $idPaciente = $_SESSION['usuario_id'];
            $nombre = trim($_POST['nombre']);
            $apellidoPaterno = trim($_POST['apellidoPaterno']);
            $apellidoMaterno = trim($_POST['apellidoMaterno']);
            $telefono = trim($_POST['telefono']);
            $diagnostico = trim($_POST['diagnostico']);
            $fechaNacimiento = $_POST['fechaNacimiento'];
            $genero = $_POST['genero'];
            $correo = trim($_POST['correo']);

            $datosActuales = $this->model->getDatosPaciente($idPaciente);
            include_once "app/models/AuthModel.php";
            $authModel = new AuthModel($this->connection);
            
            if ($correo != $datosActuales['correo'] && $authModel->verificarCorreoExistente($correo)) {
                 header("Location: index.php?action=showProfile&error=correo_existe");
                 exit();
            }

            $exito = $this->model->updateDatosPaciente($idPaciente, $nombre, $apellidoPaterno, $apellidoMaterno, $telefono, $diagnostico, $fechaNacimiento, $genero, $correo);

            if ($exito) {
                $_SESSION['usuario_nombre'] = $nombre . ' ' . $apellidoPaterno;
                header("Location: index.php?action=showProfile&success=true");
            } else {
                header("Location: index.php?action=showProfile&error=true");
            }
            exit();

        } else {
            header("Location: index.php?action=showProfile");
            exit();
        }
    } 

    // --- ACCIONES PARA CREAR CITA (PACIENTE) ---

    /**
     * Muestra el formulario para crear una nueva cita.
     */
    public function showCrearCita() {
        $listaMedicos = $this->model->getMedicosActivos();
        $activePage = 'citas';
        $viewToLoad = 'app/views/paciente/view_crear_cita.php';
        include_once 'app/views/paciente/layout_paciente.php';
    }

    /**
     * Procesa el formulario de creación de cita.
     */
    public function crearCita() {
        if (isset($_POST["programarCita"])) {
            $idPaciente = $_SESSION['usuario_id'];
            $idMedico = (int)$_POST['idMedico'];
            $motivo = trim($_POST['motivo']);
            $fecha = $_POST['fecha'];
            $hora = $_POST['hora'];
            
            if (empty($hora)) {
                header("Location: index.php?action=showCrearCita&error=hora_vacia");
                exit();
            }
            
            $fechaHora = $fecha . ' ' . $hora;

            $timestampCita = strtotime($fechaHora);
            $timestampActual = time();
            if ($timestampCita < $timestampActual) {
                header("Location: index.php?action=showCrearCita&error=fecha_pasada");
                exit();
            }

            if ($this->model->verificarCitaDuplicada($idPaciente, $fechaHora)) {
                header("Location: index.php?action=showCrearCita&error=cita_duplicada");
                exit();
            }
            
            $horasOcupadasMedico = $this->model->getHorasOcupadasPorMedicoYFecha($idMedico, $fecha);
            if (in_array($hora, $horasOcupadasMedico)) {
                header("Location: index.php?action=showCrearCita&error=medico_ocupado");
                exit();
            }

            $exito = $this->model->crearCitaPaciente($idPaciente, $idMedico, $fechaHora, $motivo);

            if ($exito) {
                header("Location: index.php?action=verCitas&success=cita_creada");
            } else {
                header("Location: index.php?action=showCrearCita&error=error_guardar");
            }
            exit();
        } else {
            header("Location: index.php?action=showCrearCita");
            exit();
        }
    }
    
    // --- ACCIONES PARA EDITAR/CANCELAR CITA (PACIENTE) ---
    
    /**
     * Procesa la cancelación de una cita
     */
    public function cancelarCita() {
        if (isset($_GET['idCita'])) {
            $idCita = (int)$_GET['idCita'];
            $idPaciente = $_SESSION['usuario_id'];
            
            $exito = $this->model->actualizarEstadoCita($idCita, $idPaciente, 'Cancelada');

            if ($exito) {
                header("Location: index.php?action=verCitas&success=cita_cancelada");
            } else {
                header("Location: index.php?action=verCitas&error=cancelar_fallo");
            }
            exit();
        } else {
            header("Location: index.php?action=verCitas");
            exit();
        }
    }
    
    /**
     * Muestra el formulario para editar una cita existente
     */
    public function showEditarCita() {
        if (!isset($_GET['idCita'])) {
            header("Location: index.php?action=verCitas");
            exit();
        }
        
        $idCita = (int)$_GET['idCita'];
        $idPaciente = $_SESSION['usuario_id'];
        
        $datosCita = $this->model->getCitaEspecifica($idCita, $idPaciente);
        
        if (!$datosCita) {
            header("Location: index.php?action=verCitas&error=no_encontrada");
            exit();
        }
        
        $listaMedicos = $this->model->getMedicosActivos();
        
        $activePage = 'citas';
        $viewToLoad = 'app/views/paciente/view_editar_cita.php';
        include_once 'app/views/paciente/layout_paciente.php';
    }
    
    /**
     * Procesa el formulario de actualización de cita
     */
    public function updateCita() {
        if (isset($_POST["actualizarCita"])) {
            $idCita = (int)$_POST['idCita'];
            $idPaciente = $_SESSION['usuario_id'];
            $idMedico = (int)$_POST['idMedico'];
            $motivo = trim($_POST['motivo']);
            $fecha = $_POST['fecha'];
            $hora = $_POST['hora'];
            
            if (empty($hora)) {
                header("Location: index.php?action=showEditarCita&idCita=$idCita&error=hora_vacia");
                exit();
            }
            
            $fechaHora = $fecha . ' ' . $hora;

            $timestampCita = strtotime($fechaHora);
            $timestampActual = time();
            if ($timestampCita < $timestampActual) {
                header("Location: index.php?action=showEditarCita&idCita=$idCita&error=fecha_pasada");
                exit();
            }

            if ($this->model->verificarCitaDuplicada($idPaciente, $fechaHora, $idCita)) {
                header("Location: index.php?action=showEditarCita&idCita=$idCita&error=cita_duplicada");
                exit();
            }
            
            $horasOcupadasMedico = $this->model->getHorasOcupadasPorMedicoYFecha($idMedico, $fecha);
            $datosCitaOriginal = $this->model->getCitaEspecifica($idCita, $idPaciente);
            $horaOriginal = date('H:i:s', strtotime($datosCitaOriginal['fechaHora']));
            
            if (in_array($hora, $horasOcupadasMedico) && $hora != $horaOriginal) {
                header("Location: index.php?action=showEditarCita&idCita=$idCita&error=medico_ocupado");
                exit();
            }

            $exito = $this->model->updateCitaPaciente($idCita, $idPaciente, $idMedico, $fechaHora, $motivo);

            if ($exito) {
                header("Location: index.php?action=verCitas&success=cita_editada");
            } else {
                header("Location: index.php?action=showEditarCita&idCita=$idCita&error=error_actualizar");
            }
            exit();
        } else {
            header("Location: index.php?action=verCitas");
            exit();
        }
    }
    
    /**
     * --- API Endpoint para AJAX ---
     * MODIFICADO: Devuelve las horas ocupadas Y la disponibilidad del turno.
     */
    public function getHorariosDisponibles() {
        if (isset($_GET['idMedico']) && isset($_GET['fecha'])) {
            $idMedico = (int)$_GET['idMedico'];
            $fecha = $_GET['fecha'];
            
            // 1. Obtener las horas ocupadas desde el modelo
            $horasOcupadas = $this->model->getHorasOcupadasPorMedicoYFecha($idMedico, $fecha);
            
            // 2. Obtener la disponibilidad (turno) del médico
            $disponibilidad = $this->model->getMedicoDisponibilidad($idMedico);
            if (!$disponibilidad) {
                $disponibilidad = 'Ambos'; // Default por si acaso
            }
            
            // 3. Devolver ambos datos como JSON
            header('Content-Type: application/json');
            echo json_encode([
                'ocupadas' => $horasOcupadas,
                'disponibilidad' => $disponibilidad
            ]);
            exit();
        }
        
        // Devolver JSON vacío si no hay parámetros
        header('Content-Type: application/json');
        echo json_encode(['ocupadas' => [], 'disponibilidad' => 'Ambos']);
        exit();
    }
}
?>