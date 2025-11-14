<?php
/**
 * Controlador para todas las acciones del Administrador.
 * Gestiona el CRUD de Médicos y Pacientes.
 */

// Cargar el modelo de Admin
include_once "app/models/AdminModel.php";
// Cargar el modelo de Auth (para validación de correo)
include_once "app/models/AuthModel.php";


class AdminController {
    private $model;
    private $connection;
    private $authModel; // Para validaciones cruzadas

    // Constructor: recibe la conexión y crea los modelos
    public function __construct($connection) {
        $this->connection = $connection;
        $this->model = new AdminModel($connection);
        $this->authModel = new AuthModel($connection);
    }

    /**
     * Muestra el dashboard principal del Administrador
     */
    public function dashboard() {
        $pageTitle = "Dashboard";
        $activePage = 'dashboard';
        $viewToLoad = 'app/views/view_dashboard_content_admin.php'; // Creamos esta vista
        
        include_once 'app/views/admin/layout_admin.php';
    }

    // --- ACCIONES DE ADMINISTRADOR (CRUD Médicos) ---

    /**
     * Muestra la página para gestionar médicos (Crear y Leer)
     * MODIFICADO: Añadida validación de pass_confirm y campos de teléfono/disponibilidad.
     */
    public function manageMedicos() {
        $error = null;
        $success = null;
        
        // Mensajes de éxito/error de otras acciones (redirección)
        if (isset($_GET['success'])) {
            $successMap = [
                'medico_actualizado' => 'Médico actualizado exitosamente.',
                'medico_desactivado' => 'Médico desactivado exitosamente.',
                'medico_activado' => 'Médico activado exitosamente.'
            ];
            $success = $successMap[$_GET['success']] ?? 'Operación exitosa.';
        }
        if (isset($_GET['error'])) {
             $errorMap = [
                'no_encontrado' => 'Error: El médico que intenta editar no fue encontrado.',
                'actualizar_estado_fallo' => 'Error al actualizar el estado del médico.',
                'correo_existe' => 'Error: El correo electrónico ya está en uso por otro usuario.',
                'pass_no_coinciden' => 'Error: Las contraseñas no coinciden.' // Nueva alerta
             ];
             $error = $errorMap[$_GET['error']] ?? 'No se pudo completar la operación.';
        }

        // Lógica para registrar un nuevo médico (Formulario de CREAR)
        if (isset($_POST["registrarMedico"])) {
            $nombre = trim($_POST["nombre"]);
            $apellidoPaterno = trim($_POST["apellidoPaterno"]);
            $apellidoMaterno = trim($_POST["apellidoMaterno"]);
            $correo = trim($_POST["correo"]);
            $pass = trim($_POST["pass"]);
            $pass_confirm = trim($_POST["pass_confirm"]); // NUEVO
            $especialidad = trim($_POST["especialidad"]);
            $cedula = trim($_POST["cedulaProfesional"]);
            $telefono = trim($_POST["telefono"]); // NUEVO
            $disponibilidad = trim($_POST["disponibilidad"]); // NUEVO

            // --- NUEVA VALIDACIÓN ---
            if ($pass !== $pass_confirm) {
                $error = "Las contraseñas no coinciden.";
            }
            // Usamos el AuthModel para la validación cruzada
            else if ($this->authModel->verificarCorreoExistente($correo)) {
                $error = "El correo electrónico ya está registrado.";
            } else {
                $pass_hash = password_hash($pass, PASSWORD_BCRYPT);
                // Usamos el AdminModel para registrar
                $exito = $this->model->registrarMedico($nombre, $apellidoPaterno, $apellidoMaterno, $correo, $pass_hash, $especialidad, $cedula, $telefono, $disponibilidad);
                if ($exito) {
                    $success = "Médico registrado exitosamente.";
                } else {
                    $error = "Error al registrar al médico.";
                }
            }
        }
        
        // Obtener la lista de TODOS los médicos (Leer)
        $listaMedicos = $this->model->getMedicosParaAdmin();
        
        // Cargar la Plantilla
        $pageTitle = "Gestionar Médicos";
        $activePage = 'medicos';
        $viewToLoad = 'app/views/admin/manage_medicos.php';
        
        include_once 'app/views/admin/layout_admin.php';
    }
    
    /**
     * Muestra el formulario para editar un médico existente
     */
    public function showEditarMedico() {
        if (!isset($_GET['idMedico'])) {
            header("Location: index.php?action=manageMedicos");
            exit();
        }
        
        $idMedico = (int)$_GET['idMedico'];
        
        $datosMedico = $this->model->getMedicoEspecifico($idMedico);
        
        if (!$datosMedico) {
            header("Location: index.php?action=manageMedicos&error=no_encontrado");
            exit();
        }
        
        $pageTitle = "Editar Médico";
        $activePage = 'medicos';
        $viewToLoad = 'app/views/admin/view_editar_medico.php';
        
        include_once 'app/views/admin/layout_admin.php'; 
    }
    
    /**
     * Procesa el formulario de actualización de un médico
     * MODIFICADO: Añadidos teléfono y disponibilidad.
     */
    public function updateMedico() {
        if (isset($_POST["actualizarMedico"])) {
            $idMedico = (int)$_POST['idMedico'];
            $nombre = trim($_POST['nombre']);
            $apellidoPaterno = trim($_POST['apellidoPaterno']);
            $apellidoMaterno = trim($_POST['apellidoMaterno']);
            $correo = trim($_POST['correo']);
            $especialidad = trim($_POST['especialidad']);
            $cedula = trim($_POST['cedulaProfesional']);
            $estado = (int)$_POST['estado']; // 1 o 0
            $password = trim($_POST['pass']);
            $pass_confirm = trim($_POST['pass_confirm']);
            $telefono = trim($_POST['telefono']); // NUEVO
            $disponibilidad = trim($_POST['disponibilidad']); // NUEVO
            
            if (!empty($password) && $password != $pass_confirm) {
                // Redirigir de vuelta con un error
                header("Location: index.php?action=showEditarMedico&idMedico=$idMedico&error=pass_no_coinciden");
                exit();
            }

            // Validar que el nuevo correo no exista (si es diferente al actual)
            $datosActuales = $this->model->getMedicoEspecifico($idMedico);
            
            if ($correo != $datosActuales['correo'] && $this->authModel->verificarCorreoExistente($correo)) {
                 header("Location: index.php?action=showEditarMedico&idMedico=$idMedico&error=correo_existe");
                 exit();
            }

            // Llamar al modelo para actualizar
            $exito = $this->model->updateDatosMedico($idMedico, $nombre, $apellidoPaterno, $apellidoMaterno, $correo, $especialidad, $cedula, $estado, $password, $telefono, $disponibilidad);

            if ($exito) {
                header("Location: index.php?action=manageMedicos&success=medico_actualizado");
            } else {
                header("Location: index.php?action=showEditarMedico&idMedico=$idMedico&error=true");
            }
            exit();
        } else {
            header("Location: index.php?action=manageMedicos");
            exit();
        }
    }
    
    /**
     * Procesa la (des)activación de un médico
     */
    public function desactivarMedico() {
        if (isset($_GET['idMedico']) && isset($_GET['estado'])) {
            $idMedico = (int)$_GET['idMedico'];
            $nuevoEstado = (int)$_GET['estado']; // 0 o 1
            
            $exito = $this->model->actualizarEstadoMedico($idMedico, $nuevoEstado);

            if ($exito) {
                $mensaje = ($nuevoEstado == 0) ? 'medico_desactivado' : 'medico_activado';
                header("Location: index.php?action=manageMedicos&success=$mensaje");
            } else {
                header("Location: index.php?action=manageMedicos&error=actualizar_estado_fallo");
            }
            exit();
        } else {
            header("Location: index.php?action=manageMedicos");
            exit();
        }
    }

    // --- ACCIONES DE ADMINISTRADOR (CRUD Pacientes) ---

    /**
     * Muestra la página para gestionar pacientes
     */
    public function managePacientes() {
        $error = null;
        $success = null;
        
        if (isset($_GET['success'])) {
            $successMap = [
                'paciente_actualizado' => 'Paciente actualizado exitosamente.',
                'paciente_desactivado' => 'Paciente desactivado exitosamente.',
                'paciente_activado' => 'Paciente activado exitosamente.'
            ];
            $success = $successMap[$_GET['success']] ?? 'Operación exitosa.';
        }
        if (isset($_GET['error'])) {
             $errorMap = [
                'no_encontrado' => 'Error: El paciente no fue encontrado.',
                'actualizar_estado_fallo' => 'Error al actualizar el estado del paciente.',
                'correo_existe' => 'Error: El correo electrónico ya está en uso por otro usuario.',
                'pass_no_coinciden' => 'Error: Las contraseñas no coinciden.' // <-- AÑADIR
             ];
             $error = $errorMap[$_GET['error']] ?? 'No se pudo completar la operación.';
        }

        $listaPacientes = $this->model->getPacientesParaAdmin();
        
        $pageTitle = "Gestionar Pacientes";
        $activePage = 'pacientes';
        $viewToLoad = 'app/views/admin/manage_pacientes.php';
        
        include_once 'app/views/admin/layout_admin.php';
    }

    /**
     * Muestra el formulario para editar un paciente
     */
    public function showEditarPaciente() {
        if (!isset($_GET['idPaciente'])) {
            header("Location: index.php?action=managePacientes");
            exit();
        }
        
        $idPaciente = (int)$_GET['idPaciente'];
        
        $datosPaciente = $this->model->getPacienteEspecifico($idPaciente);
        
        if (!$datosPaciente) {
            header("Location: index.php?action=managePacientes&error=no_encontrado");
            exit();
        }
        
        $pageTitle = "Editar Paciente";
        $activePage = 'pacientes';
        $viewToLoad = 'app/views/admin/view_editar_paciente.php';
        
        include_once 'app/views/admin/layout_admin.php'; 
    }
    
    /**
     * Procesa el formulario de actualización de un paciente
     */
    public function updatePaciente() {
        if (isset($_POST["actualizarPaciente"])) {
            $idPaciente = (int)$_POST['idPaciente'];
            
            $nombre = trim($_POST['nombre']);
            $apellidoPaterno = trim($_POST['apellidoPaterno']);
            $apellidoMaterno = trim($_POST['apellidoMaterno']);
            $correo = trim($_POST['correo']);
            $telefono = trim($_POST['telefono']);
            $diagnostico = trim($_POST['diagnostico']);
            $estado = (int)$_POST['estado'];
            $password = trim($_POST['pass']);
            $pass_confirm = trim($_POST['pass_confirm']); 

            if (!empty($password) && $password != $pass_confirm) {
                // Redirigir de vuelta con un error
                header("Location: index.php?action=showEditarPaciente&idPaciente=$idPaciente&error=pass_no_coinciden");
                exit();
            }

            $datosActuales = $this->model->getPacienteEspecifico($idPaciente);
            
            if ($correo != $datosActuales['correo'] && $this->authModel->verificarCorreoExistente($correo)) {
                 header("Location: index.php?action=showEditarPaciente&idPaciente=$idPaciente&error=correo_existe");
                 exit();
            }

            $exito = $this->model->adminUpdateDatosPaciente($idPaciente, $nombre, $apellidoPaterno, $apellidoMaterno, $correo, $telefono, $diagnostico, $estado, $password);

            if ($exito) {
                header("Location: index.php?action=managePacientes&success=paciente_actualizado");
            } else {
                header("Location: index.php?action=showEditarPaciente&idPaciente=$idPaciente&error=true");
            }
            exit();
        } else {
            header("Location: index.php?action=managePacientes");
            exit();
        }
    }
    
    /**
     * Procesa la (des)activación de un paciente
     */
    public function desactivarPaciente() {
        if (isset($_GET['idPaciente']) && isset($_GET['estado'])) {
            $idPaciente = (int)$_GET['idPaciente'];
            $nuevoEstado = (int)$_GET['estado'];
            
            $exito = $this->model->actualizarEstadoPaciente($idPaciente, $nuevoEstado);

            if ($exito) {
                $mensaje = ($nuevoEstado == 0) ? 'paciente_desactivado' : 'paciente_activado';
                header("Location: index.php?action=managePacientes&success=$mensaje");
            } else {
                header("Location: index.php?action=managePacientes&error=actualizar_estado_fallo");
            }
            exit();
        } else {
            header("Location: index.php?action=managePacientes");
            exit();
        }
    }

    // --- INICIO DE NUEVAS ACCIONES (CRUD Actividades - RFN-07) ---

    /**
     * Muestra la página para gestionar el catálogo de actividades (Crear y Leer)
     */
    public function manageActividades() {
        $error = null;
        $success = null;

        // --- Mensajes de éxito/error de otras acciones (redirección) ---
        if (isset($_GET['success'])) {
            $successMap = [
                'actividad_creada' => 'Actividad registrada exitosamente.',
                'actividad_actualizada' => 'Actividad actualizada exitosamente.',
            ];
            $success = $successMap[$_GET['success']] ?? 'Operación exitosa.';
        }
        if (isset($_GET['error'])) {
             $errorMap = [
                'no_encontrada' => 'Error: La actividad no fue encontrada.',
                'registro_fallido' => 'Error al registrar la actividad.'
             ];
             $error = $errorMap[$_GET['error']] ?? 'No se pudo completar la operación.';
        }

        // Lógica para registrar una nueva actividad (Formulario de CREAR)
        if (isset($_POST["registrarActividad"])) {
            $nombre = trim($_POST["nombre"]);
            $tipo = trim($_POST["tipo"]);
            $descripcion = trim($_POST["descripcion"]);
            $frecuencia = trim($_POST["frecuencia"]);

            $exito = $this->model->registrarActividad($nombre, $tipo, $descripcion, $frecuencia);
            
            // Usamos el patrón Post-Redirect-Get (PRG)
            if ($exito) {
                header("Location: index.php?action=manageActividades&success=actividad_creada");
                exit();
            } else {
                $error = "Error al registrar la actividad.";
            }
        }
        
        // Obtener la lista de TODAS las actividades (Leer)
        $listaActividades = $this->model->getActividades();
        
        // --- Cargar la Plantilla ---
        $pageTitle = "Gestionar Actividades";
        $activePage = 'actividades';
        $viewToLoad = 'app/views/admin/manage_actividades.php'; // <-- Nueva Vista
        
        include_once 'app/views/admin/layout_admin.php';
    }
    
    /**
     * Muestra el formulario para editar una actividad existente
     */
    public function showEditarActividad() {
        if (!isset($_GET['idActividad'])) {
            header("Location: index.php?action=manageActividades");
            exit();
        }
        
        $idActividad = (int)$_GET['idActividad'];
        $datosActividad = $this->model->getActividadEspecifica($idActividad);
        
        if (!$datosActividad) {
            header("Location: index.php?action=manageActividades&error=no_encontrada");
            exit();
        }
        
        // --- Cargar la Plantilla ---
        $pageTitle = "Editar Actividad";
        $activePage = 'actividades';
        $viewToLoad = 'app/views/admin/view_editar_actividad.php'; // <-- Nueva Vista
        
        include_once 'app/views/admin/layout_admin.php'; 
    }
    
    /**
     * Procesa el formulario de actualización de una actividad
     */
    public function updateActividad() {
        if (isset($_POST["actualizarActividad"])) {
            $idActividad = (int)$_POST['idActividad'];
            $nombre = trim($_POST["nombre"]);
            $tipo = trim($_POST["tipo"]);
            $descripcion = trim($_POST["descripcion"]);
            $frecuencia = trim($_POST["frecuencia"]);

            $exito = $this->model->updateActividad($idActividad, $nombre, $tipo, $descripcion, $frecuencia);

            if ($exito) {
                header("Location: index.php?action=manageActividades&success=actividad_actualizada");
            } else {
                header("Location: index.php?action=showEditarActividad&idActividad=$idActividad&error=true");
            }
            exit();
        } else {
            header("Location: index.php?action=manageActividades");
            exit();
        }
    }

     // --- INICIO DE NUEVAS ACCIONES (Respaldo y Restauración - RFN-14) ---

    /**
     * Muestra la página de gestión de respaldos.
     */
    public function manageBackups() {
        $error = null;
        $success = null;
        
        if (isset($_GET['success'])) {
            $map = [
                'backup_creado' => 'Respaldo creado exitosamente.',
                'restauracion_exitosa' => 'Base de datos restaurada exitosamente.'
            ];
            $success = $map[$_GET['success']] ?? 'Operación exitosa.';
        }
        if (isset($_GET['error'])) {
            $map = [
                'backup_fallido' => 'Error al crear el archivo de respaldo.',
                'restauracion_fallida' => 'Error al restaurar la base de datos. Verifica el archivo SQL.',
                'archivo_no_encontrado' => 'El archivo de respaldo no existe.'
            ];
            $error = $map[$_GET['error']] ?? 'Error desconocido.';
        }

        // Obtener la lista de archivos existentes
        $listaBackups = $this->model->getBackupFiles();

        $pageTitle = "Respaldo y Restauración";
        $activePage = 'backups';
        $viewToLoad = 'app/views/admin/view_backup_restore.php'; // <-- Nueva Vista
        
        include_once 'app/views/admin/layout_admin.php';
    }

    /**
     * Procesa la creación de un nuevo respaldo.
     */
    public function crearRespaldo() {
        $archivo = $this->model->generarRespaldo();
        
        if ($archivo) {
            header("Location: index.php?action=manageBackups&success=backup_creado");
        } else {
            header("Location: index.php?action=manageBackups&error=backup_fallido");
        }
        exit();
    }

    /**
     * Procesa la restauración de un respaldo existente.
     */
    public function restaurarRespaldo() {
        if (isset($_GET['file'])) {
            $fileName = basename($_GET['file']); // basename por seguridad (evitar ../)
            
            $exito = $this->model->restaurarRespaldo($fileName);
            
            if ($exito) {
                header("Location: index.php?action=manageBackups&success=restauracion_exitosa");
            } else {
                header("Location: index.php?action=manageBackups&error=restauracion_fallida");
            }
            exit();
        } else {
            header("Location: index.php?action=manageBackups");
            exit();
        }
    }
    
    /**
     * Descarga un archivo de respaldo (Opcional, basado en tu 'descarga.php')
     */
    public function descargarRespaldo() {
        if (isset($_GET['file'])) {
            $fileName = basename($_GET['file']);
            $filePath = 'backups/' . $fileName;
            
            if (file_exists($filePath)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="'.basename($filePath).'"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($filePath));
                readfile($filePath);
                exit;
            }
        }
        header("Location: index.php?action=manageBackups&error=archivo_no_encontrado");
        exit();
    }
    
    /**
     * Muestra la página para gestionar administradores (Crear y Leer)
     */
    public function manageAdmins() {
        $error = null;
        $success = null;
        
        // Mensajes de éxito/error de otras acciones (redirección)
        if (isset($_GET['success'])) {
            $successMap = [
                'admin_actualizado' => 'Administrador actualizado exitosamente.',
                'admin_desactivado' => 'Administrador desactivado exitosamente.',
                'admin_activado' => 'Administrador activado exitosamente.'
            ];
            $success = $successMap[$_GET['success']] ?? 'Operación exitosa.';
        }
        if (isset($_GET['error'])) {
             $errorMap = [
                'no_encontrado' => 'Error: El administrador no fue encontrado.',
                'actualizar_estado_fallo' => 'Error al actualizar el estado del administrador.',
                'correo_existe' => 'Error: El correo electrónico ya está en uso por otro usuario.',
                'pass_no_coinciden' => 'Error: Las contraseñas no coinciden.'
             ];
             $error = $errorMap[$_GET['error']] ?? 'No se pudo completar la operación.';
        }

        // Lógica para registrar un nuevo admin (Formulario de CREAR)
        if (isset($_POST["registrarAdmin"])) {
            $nombre = trim($_POST["nombre"]);
            $apellidoPaterno = trim($_POST["apellidoPaterno"]);
            $apellidoMaterno = trim($_POST["apellidoMaterno"]);
            $correo = trim($_POST["correo"]);
            $pass = trim($_POST["pass"]);
            $pass_confirm = trim($_POST["pass_confirm"]);
            $departamento = trim($_POST["departamento"]);

            // Validaciones
            if ($pass !== $pass_confirm) {
                $error = "Las contraseñas no coinciden.";
            }
            // Usamos el AuthModel para la validación cruzada
            else if ($this->authModel->verificarCorreoExistente($correo)) {
                $error = "El correo electrónico ya está registrado.";
            } else {
                $pass_hash = password_hash($pass, PASSWORD_BCRYPT);
                // Usamos el AdminModel para registrar
                $exito = $this->model->registrarAdmin($nombre, $apellidoPaterno, $apellidoMaterno, $correo, $pass_hash, $departamento);
                if ($exito) {
                    $success = "Administrador registrado exitosamente.";
                } else {
                    $error = "Error al registrar al administrador.";
                }
            }
        }
        
        // Obtener la lista de TODOS los admins (Leer)
        $listaAdmins = $this->model->getAdminsParaAdmin();
        
        // Cargar la Plantilla
        $pageTitle = "Gestionar Administradores";
        $activePage = 'admins';
        $viewToLoad = 'app/views/admin/manage_admins.php';
        
        include_once 'app/views/admin/layout_admin.php';
    }
    
    /**
     * Muestra el formulario para editar un admin existente
     */
    public function showEditarAdmin() {
        if (!isset($_GET['idAdmin'])) {
            header("Location: index.php?action=manageAdmins");
            exit();
        }
        
        $idAdmin = (int)$_GET['idAdmin'];
        
        // No se puede editar al admin principal (ID 1)
        if ($idAdmin == 1) {
             header("Location: index.php?action=manageAdmins");
            exit();
        }
        
        $datosAdmin = $this->model->getAdminEspecifico($idAdmin);
        
        if (!$datosAdmin) {
            header("Location: index.php?action=manageAdmins&error=no_encontrado");
            exit();
        }
        
        $pageTitle = "Editar Administrador";
        $activePage = 'admins';
        $viewToLoad = 'app/views/admin/view_editar_admin.php';
        
        include_once 'app/views/admin/layout_admin.php'; 
    }
    
    /**
     * Procesa el formulario de actualización de un admin
     */
    public function updateAdmin() {
        if (isset($_POST["actualizarAdmin"])) {
            $idAdmin = (int)$_POST['idAdmin'];
            $nombre = trim($_POST['nombre']);
            $apellidoPaterno = trim($_POST['apellidoPaterno']);
            $apellidoMaterno = trim($_POST['apellidoMaterno']);
            $correo = trim($_POST['correo']);
            $departamento = trim($_POST['departamento']);
            $estado = (int)$_POST['estado'];
            $password = trim($_POST['pass']);
            $pass_confirm = trim($_POST['pass_confirm']);
            
            // Validar que las contraseñas coincidan si se escribió una nueva
            if (!empty($password) && $password != $pass_confirm) {
                header("Location: index.php?action=showEditarAdmin&idAdmin=$idAdmin&error=pass_no_coinciden");
                exit();
            }
            
            // Validar que el nuevo correo no exista (si es diferente al actual)
            $datosActuales = $this->model->getAdminEspecifico($idAdmin);
            
            if ($correo != $datosActuales['correo'] && $this->authModel->verificarCorreoExistente($correo)) {
                 header("Location: index.php?action=showEditarAdmin&idAdmin=$idAdmin&error=correo_existe");
                 exit();
            }

            // Llamar al modelo para actualizar
            $exito = $this->model->updateDatosAdmin($idAdmin, $nombre, $apellidoPaterno, $apellidoMaterno, $correo, $departamento, $estado, $password);

            if ($exito) {
                header("Location: index.php?action=manageAdmins&success=admin_actualizado");
            } else {
                header("Location: index.php?action=showEditarAdmin&idAdmin=$idAdmin&error=true");
            }
            exit();
        } else {
            header("Location: index.php?action=manageAdmins");
            exit();
        }
    }
    
    /**
     * Procesa la (des)activación de un admin
     */
    public function desactivarAdmin() {
        if (isset($_GET['idAdmin']) && isset($_GET['estado'])) {
            $idAdmin = (int)$_GET['idAdmin'];
            $nuevoEstado = (int)$_GET['estado'];
            
            $exito = $this->model->actualizarEstadoAdmin($idAdmin, $nuevoEstado);

            if ($exito) {
                $mensaje = ($nuevoEstado == 0) ? 'admin_desactivado' : 'admin_activado';
                header("Location: index.php?action=manageAdmins&success=$mensaje");
            } else {
                header("Location: index.php?action=manageAdmins&error=actualizar_estado_fallo");
            }
            exit();
        } else {
            header("Location: index.php?action=manageAdmins");
            exit();
        }
    }

    // --- ACCIÓN DE REPORTES (RFN-12) ---
    
    /**
     * Muestra la página de reportes dinámicos
     */
    public function manageReportes() {
        
        // 1. Obtener todos los datos de los reportes
        $reporteGeneros = $this->model->getReportePacientesPorGenero();
        $reporteCitas = $this->model->getReporteCitasPorMedico();
        $reporteActividades = $this->model->getReporteActividadesAsignadas();
        $reporteRanking = $this->model->getReporteRankingPacientes();
        
        // 2. Cargar la Plantilla
        $pageTitle = "Reportes del Sistema";
        $activePage = 'reportes';
        $viewToLoad = 'app/views/admin/view_reportes.php';
        
        include_once 'app/views/admin/layout_admin.php';
    }

}
?>