<?php
/**
 * Modelo para las acciones del Paciente.
 * Interactúa con las tablas 'cita', 'medico', 'paciente', 'seguimiento', etc.
 */

class PacienteModel {
    private $connection;

    // Constructor: recibe la conexión
    public function __construct($connection) {
        $this->connection = $connection;
    }

    // --- FUNCIONES DE PACIENTE ---

    /**
     * Obtiene la lista de citas de un paciente específico
     * @return array
     */
    public function getCitasPaciente($idPaciente) {
        $sql = "SELECT
                    c.idCita,
                    c.fechaHora,
                    c.motivo,
                    c.duracionMin,
                    c.estado,
                    CONCAT(m.nombre, ' ', m.apellidoPaterno) as medicoNombre
                FROM cita c
                JOIN medico m ON c.idMedico = m.idMedico
                WHERE c.idPaciente = ?
                ORDER BY c.fechaHora DESC";

        try {
            $statement = $this->connection->prepare($sql);
            $statement->bind_param("i", $idPaciente);
            $statement->execute();
            $result = $statement->get_result();
            $statement->close();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (mysqli_sql_exception $e) {
            return [];
        }
    }

    /**
     * Obtiene los datos de perfil de un paciente (para formulario "Mi Perfil")
     * @return array|null
     */
    public function getDatosPaciente($idPaciente) {
        $sql = "SELECT nombre, apellidoPaterno, apellidoMaterno, correo, 
                       fechaNacimiento, genero, telefono, 
                       peso, estatura, diagnostico 
                FROM paciente 
                WHERE idPaciente = ? 
                LIMIT 1";
        
        try {
            $statement = $this->connection->prepare($sql);
            $statement->bind_param("i", $idPaciente);
            $statement->execute();
            $result = $statement->get_result();
            $statement->close();
            return $result->fetch_assoc();
        } catch (mysqli_sql_exception $e) {
            return null;
        }
    }
    
    /**
     * Actualiza los datos del perfil de un paciente
     * @return bool
     */
    public function updateDatosPaciente($idPaciente, $nombre, $apellidoPaterno, $apellidoMaterno, $telefono, $diagnostico, $fechaNacimiento, $genero, $correo) {
        
        $sql = "UPDATE paciente SET 
                    nombre = ?, 
                    apellidoPaterno = ?, 
                    apellidoMaterno = ?, 
                    telefono = ?, 
                    diagnostico = ?,
                    fechaNacimiento = ?,
                    genero = ?,
                    correo = ?
                WHERE idPaciente = ?";
        
        try {
            $statement = $this->connection->prepare($sql);
            $statement->bind_param("ssssssssi", 
                $nombre, 
                $apellidoPaterno, 
                $apellidoMaterno, 
                $telefono, 
                $diagnostico,
                $fechaNacimiento,
                $genero,
                $correo,
                $idPaciente
            );
            $exito = $statement->execute();
            $statement->close();
            return $exito;
        } catch (mysqli_sql_exception $e) {
            return false;
        }
    }

    /**
     * Crea una nueva cita para un paciente
     * @return bool
     */
    public function crearCitaPaciente($idPaciente, $idMedico, $fechaHora, $motivo) {
        $sql = "INSERT INTO cita (idPaciente, idMedico, fechaHora, duracionMin, motivo)
                VALUES (?, ?, ?, 30, ?)"; // 'duracionMin' fijada en 30

        try {
            $statement = $this->connection->prepare($sql);
            $statement->bind_param("iiss", $idPaciente, $idMedico, $fechaHora, $motivo);
            $exito = $statement->execute();
            $statement->close();
            return $exito;
        } catch (mysqli_sql_exception $e) {
            return false;
        }
    }

    // --- FUNCIONES PARA EDITAR/CANCELAR CITA (PACIENTE) ---

    /**
     * Cambia el estado de una cita a 'Cancelada'.
     * @return bool
     */
    public function actualizarEstadoCita($idCita, $idPaciente, $nuevoEstado) {
        if ($nuevoEstado != 'Cancelada') {
            return false; 
        }

        $sql = "UPDATE cita SET estado = ? WHERE idCita = ? AND idPaciente = ?";
        
        try {
            $statement = $this->connection->prepare($sql);
            $statement->bind_param("sii", $nuevoEstado, $idCita, $idPaciente);
            $exito = $statement->execute();
            $filasAfectadas = $statement->affected_rows;
            $statement->close();
            return ($exito && $filasAfectadas > 0);
        } catch (mysqli_sql_exception $e) {
            return false;
        }
    }
    
    /**
     * Obtiene los datos de una cita específica para editarla.
     * @return array|null
     */
    public function getCitaEspecifica($idCita, $idPaciente) {
        $sql = "SELECT idCita, idMedico, fechaHora, duracionMin, motivo 
                FROM cita 
                WHERE idCita = ? AND idPaciente = ? 
                LIMIT 1";
        
        try {
            $statement = $this->connection->prepare($sql);
            $statement->bind_param("ii", $idCita, $idPaciente);
            $statement->execute();
            $result = $statement->get_result();
            $statement->close();
            return $result->fetch_assoc();
        } catch (mysqli_sql_exception $e) {
            return null;
        }
    }

    /**
     * Actualiza los datos de una cita específica.
     * @return bool
     */
    public function updateCitaPaciente($idCita, $idPaciente, $idMedico, $fechaHora, $motivo) {
        $sql = "UPDATE cita SET 
                    idMedico = ?, 
                    fechaHora = ?, 
                    duracionMin = 30, 
                    motivo = ? 
                WHERE idCita = ? AND idPaciente = ?";
        
        try {
            $statement = $this->connection->prepare($sql);
            $statement->bind_param("issii", $idMedico, $fechaHora, $motivo, $idCita, $idPaciente);
            $exito = $statement->execute();
            $filasAfectadas = $statement->affected_rows;
            $statement->close();
            return ($exito && $filasAfectadas > 0);
        } catch (mysqli_sql_exception $e) {
            return false;
        }
    }
    
    /**
     * Verifica si hay conflicto de horario
     * @return bool
     */
    public function verificarCitaDuplicada($idPaciente, $fechaHora, $idCitaExcluir = null) {
        $timestampPropuesto = strtotime($fechaHora);
        if ($timestampPropuesto === false) return true;

        $inicioRango = date('Y-m-d H:i:s', $timestampPropuesto - 1799); // 29m 59s
        $finRango = date('Y-m-d H:i:s', $timestampPropuesto + 1799); // 29m 59s

        $sql = "SELECT idCita FROM cita
                WHERE idPaciente = ? 
                AND estado = 'Programada'
                AND fechaHora BETWEEN ? AND ?";

        if ($idCitaExcluir !== null) {
            $sql .= " AND idCita != ?";
        }
        $sql .= " LIMIT 1";

        try {
            $statement = $this->connection->prepare($sql);
            if ($idCitaExcluir !== null) {
                $statement->bind_param("issi", $idPaciente, $inicioRango, $finRango, $idCitaExcluir);
            } else {
                $statement->bind_param("iss", $idPaciente, $inicioRango, $finRango);
            }
            $statement->execute();
            $result = $statement->get_result();
            $existeConflicto = $result->num_rows > 0;
            $statement->close();
            return $existeConflicto;
        } catch (mysqli_sql_exception $e) {
            return true;
        }
    }
    
    /**
     * Obtiene solo los médicos ACTIVOS (para que los pacientes agenden).
     * @return array
     */
    public function getMedicosActivos() {
        $sql = "SELECT idMedico, nombre, apellidoPaterno, especialidad 
                FROM medico 
                WHERE estado = 1";
        $result = $this->connection->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Obtiene las horas de inicio de citas ya programadas
     * @return array
     */
    public function getHorasOcupadasPorMedicoYFecha($idMedico, $fecha) {
        $sql = "SELECT TIME(fechaHora) as horaOcupada 
                FROM cita
                WHERE idMedico = ? 
                AND DATE(fechaHora) = ?
                AND estado = 'Programada'";
        
        try {
            $statement = $this->connection->prepare($sql);
            $statement->bind_param("is", $idMedico, $fecha);
            $statement->execute();
            $result = $statement->get_result();
            $horasOcupadas = $result->fetch_all(MYSQLI_ASSOC);
            $statement->close();
            
            return array_column($horasOcupadas, 'horaOcupada');
            
        } catch (mysqli_sql_exception $e) {
            return [];
        }
    }
    
    /**

     * Obtiene la disponibilidad (turno) de un médico específico.
     * @return string|null
     */
    public function getMedicoDisponibilidad($idMedico) {
        $sql = "SELECT disponibilidad FROM medico WHERE idMedico = ? LIMIT 1";
        
        try {
            $statement = $this->connection->prepare($sql);
            $statement->bind_param("i", $idMedico);
            $statement->execute();
            $result = $statement->get_result();
            if ($row = $result->fetch_assoc()) {
                $statement->close();
                return $row['disponibilidad'];
            }
            $statement->close();
        } catch (mysqli_sql_exception $e) {
            return null;
        }
        return null;
    }

    /**

     * Obtiene la lista de resúmenes de recetas para un paciente.
     * @return array
     */
    public function getRecetasPaciente($idPaciente) {
        $sql = "SELECT
                    r.idReceta,
                    r.fechaEmision,
                    r.resumen,
                    r.estado,
                    CONCAT(m.nombre, ' ', m.apellidoPaterno) as medicoNombre
                FROM receta r
                JOIN medico m ON r.idMedico = m.idMedico
                WHERE r.idPaciente = ?
                ORDER BY r.fechaEmision DESC";

        try {
            $statement = $this->connection->prepare($sql);
            $statement->bind_param("i", $idPaciente);
            $statement->execute();
            $result = $statement->get_result();
            $statement->close();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (mysqli_sql_exception $e) {
            return [];
        }
    }

    /**

     * Obtiene los datos de una receta específica, validando que pertenezca al paciente.
     * @return array|null
     */
    public function getRecetaEspecificaPaciente($idReceta, $idPaciente) {
        $sql = "SELECT r.idReceta, r.fechaEmision, r.resumen, r.observaciones, r.estado,
                       CONCAT(m.nombre, ' ', m.apellidoPaterno) as medicoNombre, m.especialidad
                FROM receta r
                JOIN medico m ON r.idMedico = m.idMedico
                WHERE r.idReceta = ? AND r.idPaciente = ?
                LIMIT 1";
        
        try {
            $statement = $this->connection->prepare($sql);
            $statement->bind_param("ii", $idReceta, $idPaciente);
            $statement->execute();
            $result = $statement->get_result();
            $statement->close();
            return $result->fetch_assoc();
        } catch (mysqli_sql_exception $e) {
            return null;
        }
    }

    /**

     * Obtiene todos los ítems (medicamentos/indicaciones) de una receta.
     * (Similar a la de MedicoModel, pero la ponemos aquí para mantener el MVC).
     * @return array
     */
    public function getItemsDeReceta($idReceta) {
        $sql = "SELECT idItem, nombreMedicamento, dosis, frecuencia, duracion, instrucciones
                FROM recetaItem
                WHERE idReceta = ?
                ORDER BY idItem ASC";
        
        try {
            $statement = $this->connection->prepare($sql);
            $statement->bind_param("i", $idReceta);
            $statement->execute();
            $result = $statement->get_result();
            $statement->close();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (mysqli_sql_exception $e) {
            return [];
        }
    }

    // --- INICIO DE NUEVAS FUNCIONES (Gestión de Actividades - RFN-10) ---

    /**
     * Obtiene el historial de actividades asignadas a un paciente.
     * @return array
     */
    public function getActividadesDelPaciente($idPaciente) {
        $sql = "SELECT 
                    ap.idAsignacion,
                    ap.fechaAsignacion,
                    ap.fechaInicio,
                    ap.fechaFin,
                    ap.progreso,
                    ap.estado,
                    ap.observaciones AS medicoObservaciones,
                    a.nombre AS actividadNombre,
                    a.tipo AS actividadTipo,
                    a.descripcion AS actividadDescripcion,
                    a.frecuencia AS actividadFrecuencia,
                    CONCAT(m.nombre, ' ', m.apellidoPaterno) as medicoNombre
                FROM actividadPaciente ap
                JOIN actividad a ON ap.idActividad = a.idActividad
                LEFT JOIN medico m ON ap.idMedico = m.idMedico
                WHERE ap.idPaciente = ?
                ORDER BY ap.estado ASC, ap.fechaAsignacion DESC";
        
        try {
            $statement = $this->connection->prepare($sql);
            $statement->bind_param("i", $idPaciente);
            $statement->execute();
            $result = $statement->get_result();
            $statement->close();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (mysqli_sql_exception $e) {
            return [];
        }
    }

    /**
     * Actualiza el progreso y estado de una actividad para un paciente.
     * @return bool
     */
    public function updateProgresoActividad($idAsignacion, $idPaciente, $progreso) {
        // Determinar el estado basado en el progreso
        $estado = ($progreso >= 100) ? 'Completada' : 'Activa';

        $sql = "UPDATE actividadPaciente SET 
                    progreso = ?, 
                    estado = ?
                WHERE idAsignacion = ? AND idPaciente = ?"; // Validamos con idPaciente por seguridad
        
        try {
            $statement = $this->connection->prepare($sql);
            $statement->bind_param("dsii", $progreso, $estado, $idAsignacion, $idPaciente);
            $exito = $statement->execute();
            $filasAfectadas = $statement->affected_rows;
            $statement->close();
            return ($exito && $filasAfectadas > 0);
        } catch (mysqli_sql_exception $e) {
            return false;
        }
    }
    // --- FIN DE NUEVAS FUNCIONES ---
    
}
?>
