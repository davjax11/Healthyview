<?php
// app/models/AuthModel.php

class AuthModel {
    private $connection;

    // Constructor: recibe la conexión
    public function __construct($connection) {
        $this->connection = $connection;
    }

    // --- FUNCIONES DE AUTENTICACIÓN Y REGISTRO ---

    /**
     * Verifica un usuario en las 3 tablas (paciente, medico, admin)
     * Esta función se queda aquí porque es para el LOGIN.
     */
    public function verificarUsuario($correo) {
        // Consulta UNION para buscar en las tres tablas
        $sql = "
            (SELECT idPaciente as id, nombre, apellidoPaterno, passwordHash, 'Paciente' as tipoUsuario FROM paciente WHERE correo = ?)
            UNION
            (SELECT idMedico as id, nombre, apellidoPaterno, passwordHash, 'Medico' as tipoUsuario FROM medico WHERE correo = ?)
            UNION
            (SELECT idAdmin as id, nombre, apellidoPaterno, passwordHash, 'Administrador' as tipoUsuario FROM administrador WHERE correo = ?)
            LIMIT 1
        ";

        try {
            $statement = $this->connection->prepare($sql);
            $statement->bind_param("sss", $correo, $correo, $correo);
            $statement->execute();
            $result = $statement->get_result();

            if ($row = $result->fetch_assoc()) {
                $statement->close();
                return $row; // Devuelve la fila del usuario encontrado
            }
            $statement->close();
        } catch (mysqli_sql_exception $e) {
            // Manejar error
            return false;
        }
        return false; // No se encontró usuario
    }

    /**
     * Verifica si un correo ya existe en CUALQUIERA de las 3 tablas de usuario
     * Se usa para el REGISTRO.
     */
    public function verificarCorreoExistente($correo) {
        // Usamos la misma consulta UNION pero solo contamos los resultados
        $sql = "
            SELECT correo FROM paciente WHERE correo = ?
            UNION
            SELECT correo FROM medico WHERE correo = ?
            UNION
            SELECT correo FROM administrador WHERE correo = ?
            LIMIT 1
        ";
        $statement = $this->connection->prepare($sql);
        $statement->bind_param("sss", $correo, $correo, $correo);
        $statement->execute();
        $result = $statement->get_result();
        $existe = $result->num_rows > 0;
        $statement->close();
        return $existe;
    }


    /**
     * Registra un nuevo PACIENTE en la base de datos
     * Se queda aquí porque es para el REGISTRO PÚBLICO.
     */
    public function registrarPaciente($nombre, $apellidoPaterno, $apellidoMaterno, $correo, $pass_hash, $fechaNacimiento, $genero) {
        $sql = "INSERT INTO paciente (nombre, apellidoPaterno, apellidoMaterno, correo, passwordHash, fechaNacimiento, genero) VALUES (?, ?, ?, ?, ?, ?, ?)";

        try {
            $statement = $this->connection->prepare($sql);
            // s = string, s = string, s = string, s = string, s = string, s = string (date), s = string (enum)
            $statement->bind_param("sssssss", $nombre, $apellidoPaterno, $apellidoMaterno, $correo, $pass_hash, $fechaNacimiento, $genero);
            $exito = $statement->execute();
            $statement->close();
            return $exito;
        } catch (mysqli_sql_exception $e) {
            // echo "Error en modelo: " . $e->getMessage();
            return false;
        }
    }

    // --- FIN DE LAS FUNCIONES DE AuthModel ---
    // (Todas las demás funciones como getCitas, getMedicos, updateProfile, etc.
    // se moverán a sus propios modelos: PacienteModel y AdminModel)
}
?>

