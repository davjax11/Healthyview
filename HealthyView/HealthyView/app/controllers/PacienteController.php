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
     * Muestra el dashboard principal del paciente con resumen de datos.
     */
    public function dashboard() {
        $idPaciente = $_SESSION['usuario_id'];
        
        // Obtener resumen de datos
        $resumen = $this->model->getResumenDashboard($idPaciente);
        
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
    
    
    /**
     * Muestra la página de historial de seguimientos del paciente
     */
    public function verSeguimientos() {
        $idPaciente = $_SESSION['usuario_id'];
        
        // 1. Obtener los datos del modelo
        $historialSeguimiento = $this->model->getSeguimientosPaciente($idPaciente);
        
        // 2. Definir variables para la plantilla
        $activePage = 'seguimientos';
        $pageTitle = "Mis Seguimientos"; // Título para la pestaña del navegador
        
        // 3. Cargar la vista real, que será inyectada en el layout
        $viewToLoad = 'app/views/paciente/view_paciente_seguimientos.php'; 
        
        // 4. Cargar la plantilla principal
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

    // --- ACCIONES DE FORO (RFN-13) ---

    /**
     * Muestra el foro y gestiona la creación de nuevas publicaciones.
     */
    /**
     * Muestra el foro y gestiona la creación de nuevas publicaciones (con imágenes).
     */
    public function verForo() {
        $idPaciente = $_SESSION['usuario_id'];
        $successMessage = null;
        $errorMessage = null;

        // 1. Verificar si se está enviando una nueva publicación
        if (isset($_POST["publicarMensaje"])) {
            $titulo = trim($_POST['titulo']);
            $contenido = trim($_POST['contenido']);
            $imagenURL = null; // Default: no hay imagen

            // --- Lógica de Subida de Imagen ---
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
                $uploadDir = 'public/uploads/foro/';
                $allowedTypes = ['image/jpeg', 'image/png'];
                $maxSize = 2 * 1024 * 1024; // 2 MB

                $fileType = mime_content_type($_FILES['imagen']['tmp_name']);
                
                if (!in_array($fileType, $allowedTypes)) {
                    $errorMessage = "Error: Solo se permiten archivos JPG o PNG.";
                } elseif ($_FILES['imagen']['size'] > $maxSize) {
                    $errorMessage = "Error: El archivo es demasiado grande (Máx 2MB).";
                } else {
                    // Crear un nombre de archivo único
                    $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
                    $fileName = uniqid('img_', true) . '.' . $extension;
                    $uploadPath = $uploadDir . $fileName;

                    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $uploadPath)) {
                        $imagenURL = $uploadPath; // Ruta que se guardará en la BD
                    } else {
                        $errorMessage = "Error al mover el archivo subido.";
                    }
                }
            }
            // --- Fin de Lógica de Imagen ---

            if (empty($contenido)) {
                $errorMessage = "Error: El contenido del mensaje no puede estar vacío.";
            }

            // Si no hubo errores en la subida de imagen O no se subió imagen
            if ($errorMessage === null) {
                $exito = $this->model->insertarPublicacionForo($idPaciente, $titulo, $contenido, $imagenURL);
                if ($exito) {
                    header("Location: index.php?action=verForo&success=publicado");
                    exit();
                } else {
                    $errorMessage = "Error: No se pudo registrar tu publicación. Inténtalo de nuevo.";
                }
            }
        }

        // 2. Manejar mensajes de éxito
        if (isset($_GET['success']) && $_GET['success'] == 'publicado') {
            $successMessage = "¡Tu mensaje ha sido publicado en el foro!";
        }

        // 3. Obtener todas las publicaciones (modificaremos esto en el siguiente paso)
        $listaPublicaciones = $this->model->getPublicacionesForo($idPaciente);

        // 4. Cargar la vista
        $pageTitle = "Foro Motivacional";
        $activePage = 'foro';
        $viewToLoad = 'app/views/paciente/view_foro.php';
        
        include_once 'app/views/paciente/layout_paciente.php';
    }

    /**
     * Procesa una reacción (like/unlike) a una publicación del foro.
     */
    public function reaccionarForo() {
        if (!isset($_GET['idPublicacion'])) {
            header("Location: index.php?action=verForo");
            exit();
        }
        
        $idPublicacion = (int)$_GET['idPublicacion'];
        $idPaciente = (int)$_SESSION['usuario_id'];

        $this->model->alternarReaccion($idPublicacion, $idPaciente);

        // Redirigir de vuelta al foro
        header("Location: index.php?action=verForo");
        exit();
    }
    
    
}
?>