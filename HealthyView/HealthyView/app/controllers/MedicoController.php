<?php
// Cargar el modelo de Medico
include_once "app/models/MedicoModel.php";

class MedicoController {
    private $model;
    private $connection;

    // Constructor: recibe la conexión y crea un MedicoModel
    public function __construct($connection) {
        $this->connection = $connection;
        $this->model = new MedicoModel($connection);
    }

    /**
     * Muestra el dashboard principal del médico (AHORA ES LA VISTA DE INICIO)
     */
    public function dashboard() {
        $idMedico = $_SESSION['usuario_id'];
        
        // 1. Obtener los datos del dashboard (citas de hoy)
        $citasHoy = $this->model->getDashboardData($idMedico);
        
        // 2. Definir la página activa y la vista a cargar
        $pageTitle = "Inicio";
        $activePage = 'inicio'; // <--- Página activa es "inicio"
        $viewToLoad = 'app/views/medico/view_medico_inicio.php'; // <--- Carga la nueva vista
        
        // 3. Cargar la plantilla principal del médico
        include_once 'app/views/medico/layout_medico.php';
    }

    /**
     * Muestra la lista completa de citas del médico
     * (Muestra alertas de éxito/error)
     */
    public function verCitas() {
        $idMedico = $_SESSION['usuario_id'];
        
        // --- INICIO DE MODIFICACIÓN: Mensajes de alerta ---
        $successMessage = null;
        if (isset($_GET['success'])) {
            $map = [
                'cita_completada' => '¡Cita marcada como Completada!',
                'cita_noasistida' => 'Cita marcada como No Asistida.'
            ];
            $successMessage = $map[$_GET['success']] ?? 'Operación exitosa.';
        }
        $errorMessage = null;
        if (isset($_GET['error']) && $_GET['error'] == 'actualizar_fallo') {
            $errorMessage = "Error al actualizar la cita. Es posible que no te pertenezca o ya estuviera actualizada.";
        }
        
        // 1. Obtener la lista de TODAS las citas del médico
        $listaCitas = $this->model->getCitasMedico($idMedico);
        
        // 2. Definir la página activa y la vista a cargar
        $pageTitle = "Mis Citas Programadas";
        $activePage = 'citas';
        $viewToLoad = 'app/views/medico/view_medico_citas.php';
        
        // 3. Cargar la plantilla principal del médico
        include_once 'app/views/medico/layout_medico.php';
    }
    
    
    /**
     * Muestra la lista de pacientes asociados al médico
     */
    public function verPacientes() {
        $idMedico = $_SESSION['usuario_id'];
        
        // 1. Obtener la lista de pacientes del médico
        $listaPacientes = $this->model->getPacientesDelMedico($idMedico);
        
        // 2. Definir la página activa y la vista a cargar
        $pageTitle = "Mis Pacientes";
        $activePage = 'pacientes'; // <--- Página activa es "pacientes"
        $viewToLoad = 'app/views/medico/view_medico_pacientes.php'; // <--- Carga la nueva vista
        
        // 3. Cargar la plantilla principal del médico
        include_once 'app/views/medico/layout_medico.php';
    }

    /**
     * Muestra el perfil detallado de un paciente específico.
     */
    public function verPerfilPaciente() {
        if (!isset($_GET['idPaciente'])) {
            header("Location: index.php?action=verPacientes");
            exit();
        }
        
        $idPaciente = (int)$_GET['idPaciente'];
        $idMedico = $_SESSION['usuario_id'];
        
        $successMessage = null;
       if (isset($_GET['success'])) {
            $map = [
                'seguimiento_registrado' => '¡Seguimiento registrado exitosamente!',
                'receta_creada' => '¡Receta asignada exitosamente!',
                'actividad_asignada' => '¡Actividad asignada exitosamente!' // <-- NUEVO
            ];
            $successMessage = $map[$_GET['success']] ?? 'Operación exitosa.';
        }
        $errorMessage = null;
        if (isset($_GET['error'])) {
             $map = [
                'registro_fallido' => 'Error al registrar el seguimiento. Inténtalo de nuevo.',
                'receta_fallida' => 'Error al asignar la receta. Inténtalo de nuevo.',
                'asignacion_fallida' => 'Error al asignar la actividad.' // <-- NUEVO
            ];
            $errorMessage = $map[$_GET['error']] ?? 'Error desconocido.';
        }

        // 1. Obtener los datos del perfil del paciente
        $datosPaciente = $this->model->getDatosPacienteParaMedico($idPaciente);
        
        if (!$datosPaciente) {
            header("Location: index.php?action=verPacientes&error=no_encontrado");
            exit();
        }
        
        // 2. Obtener el historial de seguimientos (RFN-11)
        $historialSeguimiento = $this->model->getHistorialSeguimiento($idPaciente);
        
        // 3. Obtener historial de recetas (RFN-06)
        $historialRecetas = $this->model->getRecetasDelPaciente($idPaciente);
        
        // 4. --- NUEVO: Obtener historial de Actividades (RFN-07/10) ---
        $historialActividades = $this->model->getActividadesDelPaciente($idPaciente);

        // 5. Cargar la vista del perfil
        $pageTitle = "Perfil de " . htmlspecialchars($datosPaciente['nombre']);
        $activePage = 'pacientes';
        $viewToLoad = 'app/views/medico/view_perfil_paciente.php';
        
        include_once 'app/views/medico/layout_medico.php';
    }
    /**
     * Procesa el formulario de registro de seguimiento (RFN-11).
     */
    public function registrarSeguimiento() {
        if (isset($_POST["registrarSeguimiento"])) {
            $idPaciente = (int)$_POST['idPaciente'];
            $idMedico = (int)$_SESSION['usuario_id'];
            
            $peso = (float)$_POST['peso'];
            $estatura = (float)$_POST['estatura'];
            $nivelBienestar = $_POST['nivelBienestar'];
            $observaciones = trim($_POST['observaciones']);

            // Calcular IMC (Copiado de la lógica del paciente, pero en servidor)
            $imc = null;
            if ($peso > 0 && $estatura > 0) {
                $imc = $peso / ($estatura * $estatura);
            }

            // 1. Guardar en la tabla 'seguimiento'
            $exitoSeguimiento = $this->model->registrarSeguimiento($idPaciente, $idMedico, $peso, $imc, $nivelBienestar, $observaciones);
            
            // 2. Actualizar la tabla 'paciente' con los datos más recientes
             $exitoPaciente = $this->model->updatePacientePesoEstatura($idPaciente, $peso, $estatura, $imc);

            if ($exitoSeguimiento && $exitoPaciente) {
                header("Location: index.php?action=verPerfilPaciente&idPaciente=$idPaciente&success=seguimiento_registrado");
            } else {
                header("Location: index.php?action=verPerfilPaciente&idPaciente=$idPaciente&error=registro_fallido");
            }
            exit();
        } else {
            // Redirigir si se accede sin POST
            header("Location: index.php?action=verPacientes");
            exit();
        }
    }

    /**
     * Procesa la actualización de estado de una cita (Completada / No Asistida).
     */
    public function actualizarEstadoCita() {
        if (!isset($_GET['idCita']) || !isset($_GET['estado'])) {
            header("Location: index.php?action=verCitas");
            exit();
        }
        
        $idCita = (int)$_GET['idCita'];
        $idMedico = (int)$_SESSION['usuario_id'];
        $nuevoEstado = $_GET['estado']; // "Completada" o "NoAsistida"
        
        // Llamar al modelo para actualizar
        $exito = $this->model->actualizarEstadoCita($idCita, $idMedico, $nuevoEstado);

        if ($exito) {
            $mensaje = ($nuevoEstado == 'Completada') ? 'cita_completada' : 'cita_noasistida';
            header("Location: index.php?action=verCitas&success=$mensaje");
        } else {
            header("Location: index.php?action=verCitas&error=actualizar_fallo");
        }
        exit();
    }
    
    /**
     * Muestra el formulario para crear una nueva receta (RFN-06).
     */
    public function showCrearReceta() {
        if (!isset($_GET['idPaciente'])) {
            header("Location: index.php?action=verPacientes");
            exit();
        }
        $idPaciente = (int)$_GET['idPaciente'];
        
        // Obtenemos los datos del paciente solo para mostrar su nombre
        $datosPaciente = $this->model->getDatosPacienteParaMedico($idPaciente);
        if (!$datosPaciente) {
            header("Location: index.php?action=verPacientes&error=no_encontrado");
            exit();
        }

        $pageTitle = "Asignar Receta";
        $activePage = 'pacientes'; // Mantenemos "Pacientes" activo en el menú
        $viewToLoad = 'app/views/medico/view_crear_receta.php'; // <--- Carga la nueva vista de formulario
        
        include_once 'app/views/medico/layout_medico.php';
    }

    /**
     * Procesa el formulario de creación de receta (RFN-09).
     */
    public function crearReceta() {
        if (isset($_POST["asignarReceta"])) {
            $idPaciente = (int)$_POST['idPaciente'];
            $idMedico = (int)$_SESSION['usuario_id'];
            
            $resumen = trim($_POST['resumen']);
            $observaciones = trim($_POST['observaciones']);

            // Guardar en la tabla 'receta'
            $exito = $this->model->crearReceta($idPaciente, $idMedico, $resumen, $observaciones);

            if ($exito) {
                // Redirigir de vuelta al perfil del paciente
                header("Location: index.php?action=verPerfilPaciente&idPaciente=$idPaciente&success=receta_creada");
            } else {
                // Redirigir de vuelta al formulario de creación con error
                header("Location: index.php?action=showCrearReceta&idPaciente=$idPaciente&error=receta_fallida");
            }
            exit();
        } else {
            header("Location: index.php?action=verPacientes");
            exit();
        }
    }

    /**
     * Muestra la página para gestionar los ítems de una receta específica (RFN-06).
     */
    public function gestionarReceta() {
        if (!isset($_GET['idReceta'])) {
            header("Location: index.php?action=verPacientes");
            exit();
        }
        
        $idReceta = (int)$_GET['idReceta'];
        $idMedico = (int)$_SESSION['usuario_id'];
        
        // 1. Obtener datos de la receta (y validar que sea del médico)
        $datosReceta = $this->model->getRecetaEspecifica($idReceta, $idMedico);
        
        if (!$datosReceta) {
            // Si no existe o no es del médico, lo saca al perfil del paciente (si tenemos el ID)
            $idPaciente = (int)$_GET['idPaciente'] ?? null;
            if ($idPaciente) {
                header("Location: index.php?action=verPerfilPaciente&idPaciente=$idPaciente&error=receta_no_encontrada");
            } else {
                header("Location: index.php?action=verPacientes");
            }
            exit();
        }
        
        // 2. Obtener los ítems existentes de esta receta
        $listaItems = $this->model->getItemsDeReceta($idReceta);

        // 3. Manejar mensajes de éxito/error de las sub-acciones
        $successMessage = null;
        if (isset($_GET['success']) && $_GET['success'] == 'item_agregado') {
            $successMessage = "¡Elemento agregado a la receta exitosamente!";
        }
        if (isset($_GET['success']) && $_GET['success'] == 'item_eliminado') {
            $successMessage = "Elemento eliminado de la receta.";
        }
        $errorMessage = null;
        if (isset($_GET['error']) && $_GET['error'] == 'item_fallido') {
            $errorMessage = "Error. Inténtalo de nuevo.";
        }
        
        // 4. Cargar la vista de gestión
        $pageTitle = "Gestionar Receta";
        $activePage = 'pacientes'; // Mantenemos "Pacientes" activo en el menú
        $viewToLoad = 'app/views/medico/view_gestionar_receta.php'; // <--- Carga la nueva vista
        
        include_once 'app/views/medico/layout_medico.php';
    }

    /**
     * Procesa el formulario para agregar un ítem a una receta.
     */
    public function agregarItemReceta() {
        if (isset($_POST["agregarItem"])) {
            $idReceta = (int)$_POST['idReceta'];
            $idPaciente = (int)$_POST['idPaciente']; // Para la redirección
            
            // Validar que el médico sea dueño de la receta
            $idMedico = (int)$_SESSION['usuario_id'];
            $datosReceta = $this->model->getRecetaEspecifica($idReceta, $idMedico);
            if (!$datosReceta) {
                 header("Location: index.php?action=verPerfilPaciente&idPaciente=$idPaciente&error=item_fallido");
                 exit();
            }

            // Recoger datos del formulario
            $nombre = trim($_POST['nombreMedicamento']);
            $dosis = trim($_POST['dosis']);
            $frecuencia = trim($_POST['frecuencia']);
            $duracion = trim($_POST['duracion']);
            $instrucciones = trim($_POST['instrucciones']);
            
            $exito = $this->model->agregarItemReceta($idReceta, $nombre, $dosis, $frecuencia, $duracion, $instrucciones);

            if ($exito) {
                header("Location: index.php?action=gestionarReceta&idReceta=$idReceta&idPaciente=$idPaciente&success=item_agregado");
            } else {
                header("Location: index.php?action=gestionarReceta&idReceta=$idReceta&idPaciente=$idPaciente&error=item_fallido");
            }
            exit();
        } else {
            header("Location: index.php?action=verPacientes");
            exit();
        }
    }

    /**
     * Procesa la eliminación de un ítem de una receta.
     */
    public function eliminarItemReceta() {
        if (isset($_GET['idItem'])) {
            $idItem = (int)$_GET['idItem'];
            $idReceta = (int)$_GET['idReceta']; // Para la redirección
            $idPaciente = (int)$_GET['idPaciente']; // Para la redirección
            $idMedico = (int)$_SESSION['usuario_id'];
            
            $exito = $this->model->eliminarItemReceta($idItem, $idMedico);

            if ($exito) {
                header("Location: index.php?action=gestionarReceta&idReceta=$idReceta&idPaciente=$idPaciente&success=item_eliminado");
            } else {
                header("Location: index.php?action=gestionarReceta&idReceta=$idReceta&idPaciente=$idPaciente&error=item_fallido");
            }
            exit();
        } else {
            header("Location: index.php?action=verPacientes");
            exit();
        }
    }

    /**
     * Muestra el formulario para asignar una actividad a un paciente.
     */
    public function showAsignarActividad() {
        if (!isset($_GET['idPaciente'])) {
            header("Location: index.php?action=verPacientes");
            exit();
        }
        $idPaciente = (int)$_GET['idPaciente'];
        
        // 1. Obtener datos del paciente (para el encabezado)
        $datosPaciente = $this->model->getDatosPacienteParaMedico($idPaciente);
        if (!$datosPaciente) {
            header("Location: index.php?action=verPacientes&error=no_encontrado");
            exit();
        }

        // 2. Obtener el catálogo de actividades
        $listaActividades = $this->model->getActividadesDisponibles();

        $pageTitle = "Asignar Actividad";
        $activePage = 'pacientes';
        $viewToLoad = 'app/views/medico/view_asignar_actividad.php'; // <-- Nueva Vista
        
        include_once 'app/views/medico/layout_medico.php';
    }

    /**
     * Procesa el formulario de asignación de actividad.
     */
    public function asignarActividad() {
        if (isset($_POST["asignarActividad"])) {
            $idPaciente = (int)$_POST['idPaciente'];
            $idMedico = (int)$_SESSION['usuario_id'];
            $idActividad = (int)$_POST['idActividad'];
            $fechaAsignacion = $_POST['fechaAsignacion'];
            $fechaInicio = $_POST['fechaInicio'];
            $fechaFin = $_POST['fechaFin'];
            $observaciones = trim($_POST['observaciones']);

            $exito = $this->model->asignarActividadPaciente($idPaciente, $idMedico, $idActividad, $fechaAsignacion, $fechaInicio, $fechaFin, $observaciones);

            if ($exito) {
                header("Location: index.php?action=verPerfilPaciente&idPaciente=$idPaciente&success=actividad_asignada");
            } else {
                header("Location: index.php?action=showAsignarActividad&idPaciente=$idPaciente&error=asignacion_fallida");
            }
            exit();
        } else {
            header("Location: index.php?action=verPacientes");
            exit();
        }
    }
}
?>