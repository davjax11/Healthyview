<?php
/**
 * Modelo para las acciones del Administrador.
 * Interactúa con las tablas 'medico' y 'paciente' para el CRUD.
 */

class AdminModel {
    private $connection;

    // Constructor: recibe la conexión
    public function __construct($connection) {
        $this->connection = $connection;
    }

    // --- FUNCIONES CRUD DE MÉDICOS (ADMIN) ---

    /**
     * Registra un nuevo MEDICO en la base de datos (para el Admin)
     * MODIFICADO: Añadidos $telefono y $disponibilidad
     */
    public function registrarMedico($nombre, $apellidoPaterno, $apellidoMaterno, $correo, $pass_hash, $especialidad, $cedula, $telefono, $disponibilidad) {
        $sql = "INSERT INTO medico (nombre, apellidoPaterno, apellidoMaterno, correo, passwordHash, especialidad, cedulaProfesional, telefono, disponibilidad) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        try {
            $statement = $this->connection->prepare($sql);
            // s = string (9 veces)
            $statement->bind_param("sssssssss", $nombre, $apellidoPaterno, $apellidoMaterno, $correo, $pass_hash, $especialidad, $cedula, $telefono, $disponibilidad);
            $exito = $statement->execute();
            $statement->close();
            return $exito;
        } catch (mysqli_sql_exception $e) {
            return false;
        }
    }

    /**
     * Obtiene TODOS los médicos (activos e inactivos) para el panel de Admin.
     * MODIFICADO: Añadido telefono y disponibilidad
     */
    public function getMedicosParaAdmin() {
        $sql = "SELECT idMedico, nombre, apellidoPaterno, especialidad, cedulaProfesional, correo, estado, telefono, disponibilidad 
                FROM medico
                ORDER BY apellidoPaterno ASC, nombre ASC";
        $result = $this->connection->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Obtiene los datos de un médico específico para editarlo.
     * MODIFICADO: Añadidos telefono y disponibilidad
     */
    public function getMedicoEspecifico($idMedico) {
        $sql = "SELECT idMedico, nombre, apellidoPaterno, apellidoMaterno, correo, 
                       especialidad, cedulaProfesional, estado, telefono, disponibilidad 
                FROM medico 
                WHERE idMedico = ? 
                LIMIT 1";
        
        try {
            $statement = $this->connection->prepare($sql);
            $statement->bind_param("i", $idMedico);
            $statement->execute();
            $result = $statement->get_result();
            $statement->close();
            return $result->fetch_assoc();
        } catch (mysqli_sql_exception $e) {
            return null;
        }
    }
    
    /**
     * Actualiza los datos de un médico específico.
     * MODIFICADO: Añadidos $telefono y $disponibilidad, y CORREGIDO el bloque 'else'.
     */
    public function updateDatosMedico($idMedico, $nombre, $apellidoPaterno, $apellidoMaterno, $correo, $especialidad, $cedula, $estado, $password, $telefono, $disponibilidad) {
        
        try {
            // Si el campo de contraseña no está vacío, actualizamos el hash
            if (!empty($password)) {
                $pass_hash = password_hash($password, PASSWORD_BCRYPT);
                $sql = "UPDATE medico SET 
                            nombre = ?, 
                            apellidoPaterno = ?, 
                            apellidoMaterno = ?, 
                            correo = ?, 
                            especialidad = ?, 
                            cedulaProfesional = ?, 
                            estado = ?,
                            telefono = ?,
                            disponibilidad = ?,
                            passwordHash = ? 
                        WHERE idMedico = ?";
                $statement = $this->connection->prepare($sql);
                // s(7), i(1), s(2), s(1), i(1)
                $statement->bind_param("ssssssisssi", $nombre, $apellidoPaterno, $apellidoMaterno, $correo, $especialidad, $cedula, $estado, $telefono, $disponibilidad, $pass_hash, $idMedico);
            } else {
                // Si la contraseña está vacía, no la actualizamos
                $sql = "UPDATE medico SET 
                            nombre = ?, 
                            apellidoPaterno = ?, 
                            apellidoMaterno = ?, 
                            correo = ?, 
                            especialidad = ?, 
                            cedulaProfesional = ?, 
                            estado = ?,
                            telefono = ?,
                            disponibilidad = ?
                        WHERE idMedico = ?";
                $statement = $this->connection->prepare($sql);
                 // s(6), i(1), s(2), i(1) -> Total "ssssssisii"
                $statement->bind_param("ssssssisii", $nombre, $apellidoPaterno, $apellidoMaterno, $correo, $especialidad, $cedula, $estado, $telefono, $disponibilidad, $idMedico);
            }
            
            $exito = $statement->execute();
            $statement->close();
            return $exito;
            
        } catch (mysqli_sql_exception $e) {
            return false;
        }
    }
    
    /**
     * Cambia el estado de un médico (Activo/Inactivo).
     */
    public function actualizarEstadoMedico($idMedico, $nuevoEstado) {
        $estadoValidado = ($nuevoEstado == 1) ? 1 : 0;
        $sql = "UPDATE medico SET estado = ? WHERE idMedico = ?";
        
        try {
            $statement = $this->connection->prepare($sql);
            $statement->bind_param("ii", $estadoValidado, $idMedico);
            $exito = $statement->execute();
            $statement->close();
            return $exito;
        } catch (mysqli_sql_exception $e) {
            return false;
        }
    }


    // --- FUNCIONES CRUD DE PACIENTES (ADMIN) ---

    /**
     * Obtiene TODOS los pacientes (para el panel de Admin)
     */
    public function getPacientesParaAdmin() {
        $sql = "SELECT idPaciente, nombre, apellidoPaterno, correo, telefono, estado 
                FROM paciente
                ORDER BY apellidoPaterno ASC, nombre ASC";
        $result = $this->connection->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Obtiene los datos de un paciente específico para editarlo (versión Admin)
     */
    public function getPacienteEspecifico($idPaciente) {
        $sql = "SELECT idPaciente, nombre, apellidoPaterno, apellidoMaterno, correo, 
                       telefono, diagnostico, estado 
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
     * Actualiza los datos de un paciente (versión Admin)
     */
    public function adminUpdateDatosPaciente($idPaciente, $nombre, $apellidoPaterno, $apellidoMaterno, $correo, $telefono, $diagnostico, $estado, $password) {
        
        try {
            if (!empty($password)) {
                $pass_hash = password_hash($password, PASSWORD_BCRYPT);
                $sql = "UPDATE paciente SET 
                            nombre = ?, apellidoPaterno = ?, apellidoMaterno = ?, 
                            correo = ?, telefono = ?, diagnostico = ?, 
                            estado = ?, passwordHash = ? 
                        WHERE idPaciente = ?";
                $statement = $this->connection->prepare($sql);
                $statement->bind_param("ssssssisi", $nombre, $apellidoPaterno, $apellidoMaterno, $correo, $telefono, $diagnostico, $estado, $pass_hash, $idPaciente);
            } else {
                $sql = "UPDATE paciente SET 
                            nombre = ?, apellidoPaterno = ?, apellidoMaterno = ?, 
                            correo = ?, telefono = ?, diagnostico = ?, estado = ? 
                        WHERE idPaciente = ?";
                $statement = $this->connection->prepare($sql);
                $statement->bind_param("ssssssii", $nombre, $apellidoPaterno, $apellidoMaterno, $correo, $telefono, $diagnostico, $estado, $idPaciente);
            }
            
            $exito = $statement->execute();
            $statement->close();
            return $exito;
            
        } catch (mysqli_sql_exception $e) {
            return false;
        }
    }

    /**
     * Cambia el estado de un paciente (Activo/Inactivo).
     */
    public function actualizarEstadoPaciente($idPaciente, $nuevoEstado) {
        $estadoValidado = ($nuevoEstado == 1) ? 1 : 0;
        $sql = "UPDATE paciente SET estado = ? WHERE idPaciente = ?";
        
        try {
            $statement = $this->connection->prepare($sql);
            $statement->bind_param("ii", $estadoValidado, $idPaciente);
            $exito = $statement->execute();
            $statement->close();
            return $exito;
        } catch (mysqli_sql_exception $e) {
            return false;
        }
    }

    // --- INICIO DE NUEVAS FUNCIONES (CRUD Actividades - RFN-07) ---

    /**
     * Obtiene TODAS las actividades del catálogo
     * @return array
     */
    public function getActividades() {
        $sql = "SELECT idActividad, nombre, tipo, descripcion, frecuencia 
                FROM actividad
                ORDER BY tipo, nombre ASC";
        $result = $this->connection->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Registra una nueva actividad en el catálogo
     * @return bool
     */
    public function registrarActividad($nombre, $tipo, $descripcion, $frecuencia) {
        $sql = "INSERT INTO actividad (nombre, tipo, descripcion, frecuencia) 
                VALUES (?, ?, ?, ?)";
        try {
            $statement = $this->connection->prepare($sql);
            $statement->bind_param("ssss", $nombre, $tipo, $descripcion, $frecuencia);
            $exito = $statement->execute();
            $statement->close();
            return $exito;
        } catch (mysqli_sql_exception $e) {
            return false;
        }
    }

    /**
     * Obtiene los datos de una actividad específica para editarla
     * @return array|null
     */
    public function getActividadEspecifica($idActividad) {
        $sql = "SELECT idActividad, nombre, tipo, descripcion, frecuencia 
                FROM actividad 
                WHERE idActividad = ? 
                LIMIT 1";
        try {
            $statement = $this->connection->prepare($sql);
            $statement->bind_param("i", $idActividad);
            $statement->execute();
            $result = $statement->get_result();
            $statement->close();
            return $result->fetch_assoc();
        } catch (mysqli_sql_exception $e) {
            return null;
        }
    }

    /**
     * Actualiza los datos de una actividad específica.
     * @return bool
     */
    public function updateActividad($idActividad, $nombre, $tipo, $descripcion, $frecuencia) {
        $sql = "UPDATE actividad SET 
                    nombre = ?, 
                    tipo = ?, 
                    descripcion = ?, 
                    frecuencia = ?
                WHERE idActividad = ?";
        try {
            $statement = $this->connection->prepare($sql);
            $statement->bind_param("ssssi", $nombre, $tipo, $descripcion, $frecuencia, $idActividad);
            $exito = $statement->execute();
            $statement->close();
            return $exito;
        } catch (mysqli_sql_exception $e) {
            return false;
        }
    }
    

    // --- INICIO DE NUEVAS FUNCIONES (Respaldo y Restauración - RFN-14) ---

    /**
     * Obtiene la lista de archivos de respaldo disponibles en la carpeta /backups/
     */
    public function getBackupFiles() {
        $backupDir = 'backups/'; // Ruta relativa desde index.php
        $files = [];
        
        if (is_dir($backupDir)) {
            $rawFiles = scandir($backupDir, SCANDIR_SORT_DESCENDING); // Más recientes primero
            foreach ($rawFiles as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) == 'sql') {
                    $files[] = $file;
                }
            }
        }
        return $files;
    }

    /**
     * Genera un respaldo completo de la base de datos.
     * Basado en tu script 'backup_tables'.
     */
    public function generarRespaldo() {
        $link = $this->connection;
        $tables = array();
        
        // 1. Obtener todas las tablas
        $result = $link->query('SHOW TABLES');
        while($row = mysqli_fetch_row($result)) {
            $tables[] = $row[0];
        }
        
        $return = "";
        
        // 2. Recorrer tablas
        foreach($tables as $table) {
            $result = $link->query('SELECT * FROM '.$table);
            $num_fields = mysqli_num_fields($result);
            
            // Drop table si existe
            $return .= "DROP TABLE IF EXISTS `".$table."`;";
            
            // Create table query
            $row2 = mysqli_fetch_row($link->query('SHOW CREATE TABLE '.$table));
            $return .= "\n\n".$row2[1].";\n\n";
            
            // Insertar datos
            for ($i = 0; $i < $num_fields; $i++) {
                while($row = mysqli_fetch_row($result)) {
                    $return .= 'INSERT INTO '.$table.' VALUES(';
                    for($j=0; $j<$num_fields; $j++) {
                        $row[$j] = addslashes($row[$j]);
                        // Eliminar saltos de línea problemáticos
                        $row[$j] = preg_replace("/\n/","\\n",$row[$j]);
                        if (isset($row[$j])) { 
                            $return .= '"'.$row[$j].'"' ; 
                        } else { 
                            $return .= '""'; 
                        }
                        if ($j<($num_fields-1)) { $return .= ','; }
                    }
                    $return .= ");\n";
                }
            }
            $return .= "\n\n\n";
        }
        
        // 3. Guardar archivo
        // Usamos fecha y hora para evitar sobrescribir backups del mismo día
        $fecha = date("Y-m-d_H-i-s"); 
        $fileName = 'db-backup-'.$fecha.'.sql';
        $handle = fopen('backups/'.$fileName, 'w+');
        
        if ($handle) {
            fwrite($handle, $return);
            fclose($handle);
            return $fileName;
        } else {
            return false;
        }
    }

    /**
     * Restaura la base de datos desde un archivo SQL específico.
     */
    public function restaurarRespaldo($fileName) {
        $filePath = 'backups/' . $fileName;
        
        if (!file_exists($filePath)) {
            return false;
        }
        
        // Leer todo el contenido del archivo
        $sql = file_get_contents($filePath);
        
        // Ejecutar multi_query es necesario para scripts largos
        if ($this->connection->multi_query($sql)) {
            // Limpiar los resultados para evitar errores de "commands out of sync"
            do {
                if ($result = $this->connection->store_result()) {
                    $result->free();
                }
            } while ($this->connection->next_result());
            return true;
        } else {
            return false;
        }
    }
    
    // --- FUNCIONES CRUD DE ADMINISTRADORES (ADMIN) ---

    /**
     * Obtiene TODOS los administradores (para el panel de Admin)
     */
    public function getAdminsParaAdmin() {
        $sql = "SELECT idAdmin, nombre, apellidoPaterno, correo, departamento, estado 
                FROM administrador
                ORDER BY apellidoPaterno ASC, nombre ASC";
        $result = $this->connection->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Registra un nuevo ADMINISTRADOR en la base de datos
     */
    public function registrarAdmin($nombre, $apellidoPaterno, $apellidoMaterno, $correo, $pass_hash, $departamento) {
        $sql = "INSERT INTO administrador (nombre, apellidoPaterno, apellidoMaterno, correo, passwordHash, departamento) 
                VALUES (?, ?, ?, ?, ?, ?)";

        try {
            $statement = $this->connection->prepare($sql);
            // s = string (6 veces)
            $statement->bind_param("ssssss", $nombre, $apellidoPaterno, $apellidoMaterno, $correo, $pass_hash, $departamento);
            $exito = $statement->execute();
            $statement->close();
            return $exito;
        } catch (mysqli_sql_exception $e) {
            return false;
        }
    }

    /**
     * Obtiene los datos de un admin específico para editarlo.
     */
    public function getAdminEspecifico($idAdmin) {
        $sql = "SELECT idAdmin, nombre, apellidoPaterno, apellidoMaterno, correo, 
                       departamento, estado 
                FROM administrador 
                WHERE idAdmin = ? 
                LIMIT 1";
        
        try {
            $statement = $this->connection->prepare($sql);
            $statement->bind_param("i", $idAdmin);
            $statement->execute();
            $result = $statement->get_result();
            $statement->close();
            return $result->fetch_assoc();
        } catch (mysqli_sql_exception $e) {
            return null;
        }
    }
    
    /**
     * Actualiza los datos de un administrador específico.
     */
    public function updateDatosAdmin($idAdmin, $nombre, $apellidoPaterno, $apellidoMaterno, $correo, $departamento, $estado, $password) {
        
        try {
            // Si el campo de contraseña no está vacío, actualizamos el hash
            if (!empty($password)) {
                $pass_hash = password_hash($password, PASSWORD_BCRYPT);
                $sql = "UPDATE administrador SET 
                            nombre = ?, 
                            apellidoPaterno = ?, 
                            apellidoMaterno = ?, 
                            correo = ?, 
                            departamento = ?, 
                            estado = ?,
                            passwordHash = ? 
                        WHERE idAdmin = ?";
                $statement = $this->connection->prepare($sql);
                $statement->bind_param("sssssisi", $nombre, $apellidoPaterno, $apellidoMaterno, $correo, $departamento, $estado, $pass_hash, $idAdmin);
            } else {
                // Si la contraseña está vacía, no la actualizamos
                $sql = "UPDATE administrador SET 
                            nombre = ?, 
                            apellidoPaterno = ?, 
                            apellidoMaterno = ?, 
                            correo = ?, 
                            departamento = ?, 
                            estado = ?
                        WHERE idAdmin = ?";
                $statement = $this->connection->prepare($sql);
                $statement->bind_param("sssssii", $nombre, $apellidoPaterno, $apellidoMaterno, $correo, $departamento, $estado, $idAdmin);
            }
            
            $exito = $statement->execute();
            $statement->close();
            return $exito;
            
        } catch (mysqli_sql_exception $e) {
            return false;
        }
    }
    
    /**
     * Cambia el estado de un administrador (Activo/Inactivo).
     */
    public function actualizarEstadoAdmin($idAdmin, $nuevoEstado) {
        // Nos aseguramos que el ID 1 (Admin Principal) no pueda ser desactivado
        if ($idAdmin == 1) {
            return false;
        }
        
        $estadoValidado = ($nuevoEstado == 1) ? 1 : 0;
        $sql = "UPDATE administrador SET estado = ? WHERE idAdmin = ?";
        
        try {
            $statement = $this->connection->prepare($sql);
            $statement->bind_param("ii", $estadoValidado, $idAdmin);
            $exito = $statement->execute();
            $statement->close();
            return $exito;
        } catch (mysqli_sql_exception $e) {
            return false;
        }
    }


    // --- FUNCIONES DE REPORTES (RFN-12) ---

    /**
     * Reporte: Conteo de pacientes por género.
     */
    public function getReportePacientesPorGenero() {
        $sql = "SELECT genero, COUNT(idPaciente) as total 
                FROM paciente 
                GROUP BY genero 
                ORDER BY total DESC";
        $result = $this->connection->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Reporte: Conteo de citas por médico.
     */
    public function getReporteCitasPorMedico() {
        $sql = "SELECT 
                    CONCAT(m.nombre, ' ', m.apellidoPaterno) as medicoNombre, 
                    m.especialidad,
                    COUNT(c.idCita) as totalCitas
                FROM cita c
                JOIN medico m ON c.idMedico = m.idMedico
                GROUP BY c.idMedico
                ORDER BY totalCitas DESC";
        $result = $this->connection->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Reporte: Conteo de actividades asignadas (por tipo de actividad).
     */
    public function getReporteActividadesAsignadas() {
        $sql = "SELECT 
                    a.nombre as actividadNombre,
                    a.tipo,
                    COUNT(ap.idAsignacion) as totalAsignaciones
                FROM actividadPaciente ap
                JOIN actividad a ON ap.idActividad = a.idActividad
                GROUP BY ap.idActividad
                ORDER BY totalAsignaciones DESC";
        $result = $this->connection->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Reporte: Ranking de participación de pacientes (basado en actividades completadas).
     */
    public function getReporteRankingPacientes() {
        $sql = "SELECT 
                    CONCAT(p.nombre, ' ', p.apellidoPaterno) as pacienteNombre,
                    p.correo,
                    COUNT(ap.idAsignacion) as totalCompletadas
                FROM actividadPaciente ap
                JOIN paciente p ON ap.idPaciente = p.idPaciente
                WHERE ap.estado = 'Completada'
                GROUP BY ap.idPaciente
                ORDER BY totalCompletadas DESC
                LIMIT 10"; // Mostramos el Top 10
        $result = $this->connection->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>