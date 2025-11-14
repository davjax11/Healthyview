-- ==========================================================
-- base de datos: healthyview
-- descripción: sistema web para monitoreo nutricional y hábitos saludables
-- ==========================================================

drop database if exists healthyview;
CREATE DATABASE IF NOT EXISTS healthyview;
USE healthyview;

-- ==========================================================
-- tabla: paciente
-- MODIFICADO: Añadido campo 'imc'
-- ==========================================================

CREATE TABLE paciente (
    idPaciente INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    apellidoPaterno VARCHAR(100) NOT NULL,
    apellidoMaterno VARCHAR(100),
    correo VARCHAR(150) NOT NULL UNIQUE,
    passwordHash VARCHAR(255) NOT NULL,
    fechaNacimiento DATE,
    genero ENUM('Masculino','Femenino','Otro') DEFAULT 'Otro',
    peso DECIMAL(5,2),
    estatura DECIMAL(4,2),
    imc DECIMAL(5,2) NULL, 
    diagnostico TEXT,
    telefono VARCHAR(20),
    fechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='almacena los datos generales y de salud del paciente.';
  
-- ==========================================================
-- tabla: medico
-- ==========================================================

CREATE TABLE medico (
    idMedico INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    apellidoPaterno VARCHAR(100) NOT NULL,
    apellidoMaterno VARCHAR(100),
    correo VARCHAR(150) NOT NULL UNIQUE,
    passwordHash VARCHAR(255) NOT NULL,
    especialidad VARCHAR(100),
    telefono VARCHAR(20),
    cedulaProfesional VARCHAR(50),
    disponibilidad VARCHAR(100),
    fechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='almacena información profesional y de acceso de los médicos.';
  
-- ==========================================================
-- tabla: administrador
-- ==========================================================

CREATE TABLE administrador (
    idAdmin INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    apellidoPaterno VARCHAR(100) NOT NULL,
    apellidoMaterno VARCHAR(100),
    correo VARCHAR(150) NOT NULL UNIQUE,
    passwordHash VARCHAR(255) NOT NULL,
    departamento VARCHAR(100),
    permisos JSON DEFAULT NULL,
    fechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='gestiona el sistema y tiene acceso a reportes globales.';

-- ==========================================================
-- tabla: sesion
-- ==========================================================

CREATE TABLE sesion (
    idSesion INT AUTO_INCREMENT PRIMARY KEY,
    tipoUsuario ENUM('Paciente','Medico','Administrador') NOT NULL,
    idPaciente INT NULL,
    idMedico INT NULL,
    idAdmin INT NULL,
    fechaInicio DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fechaFin DATETIME NULL,
    ipAcceso VARCHAR(45),
    estado ENUM('Activa','Cerrada','Fallida') DEFAULT 'Activa',
    FOREIGN KEY (idPaciente) REFERENCES paciente(idPaciente) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (idMedico) REFERENCES medico(idMedico) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (idAdmin) REFERENCES administrador(idAdmin) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='controla y audita las sesiones de usuarios del sistema.';
  
-- ==========================================================
-- tabla: receta
-- ==========================================================

CREATE TABLE receta (
    idReceta INT AUTO_INCREMENT PRIMARY KEY,
    idPaciente INT NOT NULL,
    idMedico INT NOT NULL,
    fechaEmision DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    resumen VARCHAR(255),
    observaciones TEXT,
    estado ENUM('Activa','Finalizada','Cancelada') DEFAULT 'Activa',
    FOREIGN KEY (idPaciente) REFERENCES paciente(idPaciente) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (idMedico) REFERENCES medico(idMedico) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='registra las recetas médicas generadas por los doctores.';
  
-- ==========================================================
-- tabla: recetaItem
-- ==========================================================

CREATE TABLE recetaItem (
    idItem INT AUTO_INCREMENT PRIMARY KEY,
    idReceta INT NOT NULL,
    nombreMedicamento VARCHAR(200) NOT NULL,
    dosis VARCHAR(100),
    frecuencia VARCHAR(100),
    duracion VARCHAR(50),
    instrucciones TEXT,
    FOREIGN KEY (idReceta) REFERENCES receta(idReceta) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='detalla los medicamentos y dosis de cada receta.';

-- ==========================================================
-- tabla: actividad
-- ==========================================================

CREATE TABLE actividad (
    idActividad INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    tipo ENUM('Física','Alimentaria','Mental','Otro') DEFAULT 'Otro',
    descripcion TEXT,
    frecuencia VARCHAR(80),
    fechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='catálogo de actividades saludables disponibles.';
  
-- ==========================================================
-- tabla: actividadPaciente
-- ==========================================================

CREATE TABLE actividadPaciente (
    idAsignacion INT AUTO_INCREMENT PRIMARY KEY,
    idActividad INT NOT NULL,
    idPaciente INT NOT NULL,
    idMedico INT NULL,
    fechaAsignacion DATE NOT NULL,
    fechaInicio DATE NULL,
    fechaFin DATE NULL,
    progreso DECIMAL(5,2) DEFAULT 0.0,
    estado ENUM('Activa','Completada','Cancelada') DEFAULT 'Activa',
    observaciones TEXT,
    FOREIGN KEY (idActividad) REFERENCES actividad(idActividad) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (idPaciente) REFERENCES paciente(idPaciente) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (idMedico) REFERENCES medico(idMedico) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='registra las actividades asignadas a cada paciente y su progreso.';
  
-- ==========================================================
-- tabla: cita
-- ==========================================================

CREATE TABLE cita (
    idCita INT AUTO_INCREMENT PRIMARY KEY,
    idPaciente INT NOT NULL,
    idMedico INT NOT NULL,
    fechaHora DATETIME NOT NULL,
    duracionMin INT DEFAULT 30,
    motivo VARCHAR(255),
    estado ENUM('Programada','Completada','Cancelada','NoAsistida') DEFAULT 'Programada',
    creadoEn DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idPaciente) REFERENCES paciente(idPaciente) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (idMedico) REFERENCES medico(idMedico) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='controla las citas médicas programadas entre pacientes y médicos.';
  
-- ==========================================================
-- tabla: seguimiento
-- ==========================================================

CREATE TABLE seguimiento (
    idSeguimiento INT AUTO_INCREMENT PRIMARY KEY,
    idPaciente INT NOT NULL,
    idMedico INT NULL,
    fechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,
    peso DECIMAL(5,2),
    imc DECIMAL(5,2),
    nivelBienestar ENUM('Excelente','Bueno','Regular','Malo') DEFAULT 'Regular',
    observaciones TEXT,
    FOREIGN KEY (idPaciente) REFERENCES paciente(idPaciente) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (idMedico) REFERENCES medico(idMedico) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='registra el progreso y bienestar del paciente con evaluación médica.';
  
-- ==========================================================
-- tabla: foro
-- ==========================================================

CREATE TABLE foro (
    idPublicacion INT AUTO_INCREMENT PRIMARY KEY,
    idPaciente INT NULL,
    titulo VARCHAR(200),
    contenido TEXT NOT NULL,
    fechaPublicacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    reacciones INT DEFAULT 0,
    FOREIGN KEY (idPaciente) REFERENCES paciente(idPaciente) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  COMMENT='espacio donde los pacientes pueden publicar mensajes motivacionales.';

-- ==========================================================
-- INICIO DE DATOS DE EJEMPLO
-- ==========================================================

USE healthyview;

-- Contraseña genérica para todos los usuarios de ejemplo.
-- Es el hash de '123456'
SET @passwordHash = '$2y$10$VBu/GZZSAJ4odbmxKH8bnuhr.FTeLMhEhFJ3RSwNJYH/9PqHxU/Vm';

-- ==========================================================
-- tabla: administrador (3 entradas)
-- ==========================================================
INSERT INTO administrador 
    (nombre, apellidoPaterno, correo, passwordHash, departamento, permisos) 
VALUES 
    ('Admin', 'Principal', 'admin@healthyview.com', '$2y$10$VBu/GZZSAJ4odbmxKH8bnuhr.FTeLMhEhFJ3RSwNJYH/9PqHxU/Vm', 'Sistemas', '["usuarios", "reportes", "medicos", "pacientes"]'),
    ('Soporte', 'Técnico', 'soporte@healthyview.com', @passwordHash, 'Sistemas', '["usuarios", "medicos"]'),
    ('Gerencia', 'Operaciones', 'gerencia@healthyview.com', @passwordHash, 'Operaciones', '["reportes"]');

-- ==========================================================
-- tabla: paciente (3 entradas)
-- MODIFICADO: Añadido cálculo de IMC
-- ==========================================================
INSERT INTO paciente 
    (nombre, apellidoPaterno, apellidoMaterno, correo, passwordHash, fechaNacimiento, genero, peso, estatura, imc, diagnostico, telefono) 
VALUES
    ('Carlos', 'Sánchez', 'López', 'carlos.sanchez@mail.com', @passwordHash, '1990-05-15', 'Masculino', 85.5, 1.75, (85.5 / (1.75 * 1.75)), 'Sobrepeso tipo I. Requiere plan alimenticio y rutina de ejercicio.', '5512345678'),
    ('Ana', 'García', 'Martínez', 'ana.garcia@mail.com', @passwordHash, '1985-11-20', 'Femenino', 62.0, 1.60, (62.0 / (1.60 * 1.60)), 'Hipertensión arterial leve. Monitoreo de sodio.', '5598765432'),
    ('Luis', 'Torres', 'Díaz', 'luis.torres@mail.com', @passwordHash, '2000-01-30', 'Masculino', 70.2, 1.80, (70.2 / (1.80 * 1.80)), 'Paciente saludable, buscando optimizar rendimiento deportivo.', '5555555555');

-- ==========================================================
-- tabla: medico (3 entradas)
-- ==========================================================
INSERT INTO medico
    (nombre, apellidoPaterno, apellidoMaterno, correo, passwordHash, especialidad, telefono, cedulaProfesional, disponibilidad)
VALUES
    ('Elena', 'Herrera', 'Ruiz', 'elena.herrera@healthyview.com', @passwordHash, 'Nutrición Clínica', '5511112222', '1234567', 'Matutino'),
    ('Miguel', 'Castro', 'Vera', 'miguel.castro@healthyview.com', @passwordHash, 'Cardiología', '5533334444', '7654321', 'Vespertino'),
    ('Sofía', 'Mendoza', 'Cruz', 'sofia.mendoza@healthyview.com', @passwordHash, 'Medicina General y Deporte', '5566667777', '8889990', 'Ambos');

-- ==========================================================
-- tabla: actividad
-- (Se mantienen los 3 originales)
-- ==========================================================
INSERT INTO actividad (nombre, tipo, descripcion, frecuencia)
VALUES
('Caminata diaria', 'Física', 'Caminata de 30 minutos a paso moderado', 'Diaria'),
('Plan de comidas saludable', 'Alimentaria', 'Plan semanal con calorías controladas', 'Semanal'),
('Meditación guiada', 'Mental', 'Sesiones de 15 minutos para control de estrés', 'Diaria');

-- ==========================================================
-- tabla: sesion (3 entradas)
-- ==========================================================
INSERT INTO sesion
    (tipoUsuario, idPaciente, idMedico, idAdmin, ipAcceso, estado)
VALUES
    ('Paciente', 1, NULL, NULL, '192.168.1.10', 'Activa'),
    ('Medico', NULL, 1, NULL, '200.50.10.1', 'Activa'),
    ('Administrador', NULL, NULL, 1, '189.10.10.5', 'Cerrada');

-- ==========================================================
-- tabla: cita (3 entradas)
-- ==========================================================
INSERT INTO cita
    (idPaciente, idMedico, fechaHora, duracionMin, motivo, estado)
VALUES
    (1, 1, DATE_ADD(NOW(), INTERVAL 2 DAY), 45, 'Consulta nutricional de seguimiento', 'Programada'),
    (2, 2, DATE_ADD(NOW(), INTERVAL 3 DAY), 60, 'Revisión cardiológica anual', 'Programada'),
    (3, 1, DATE_ADD(NOW(), INTERVAL 3 DAY), 30, 'Asesoría plan alimenticio deportivo', 'Programada');

-- ==========================================================
-- tabla: receta (3 entradas)
-- ==========================================================
INSERT INTO receta
    (idPaciente, idMedico, resumen, observaciones)
VALUES
    (2, 2, 'Control de hipertensión', 'Monitorear presión arterial dos veces al día.'),
    (1, 1, 'Plan vitamínico y reducción de peso', 'Ajustar dieta y suplementar Vitamina D.'),
    (2, 2, 'Ajuste de dosis Losartán', 'Paciente reporta mareos leves, ajustar dosis.');

-- ==========================================================
-- tabla: recetaItem (3 entradas)
-- (Asumiendo que las recetas anteriores son IDs 1, 2, 3)
-- ==========================================================
INSERT INTO recetaItem
    (idReceta, nombreMedicamento, dosis, frecuencia, duracion, instrucciones)
VALUES
    (1, 'Losartán', '50 mg', '1 cada 24 horas', '90 días', 'Tomar por la mañana, preferentemente a la misma hora.'),
    (2, 'Vitamina D3', '2000 UI', '1 cada 24 horas', '60 días', 'Tomar junto con el desayuno.'),
    (3, 'Losartán', '25 mg', '1 cada 24 horas', '30 días', 'Nueva dosis reducida. Vigilar síntomas.');

-- ==========================================================
-- tabla: actividadPaciente (3 entradas)
-- ==========================================================
INSERT INTO actividadPaciente
    (idActividad, idPaciente, idMedico, fechaAsignacion, fechaInicio, estado)
VALUES
    (1, 1, 1, CURDATE(), CURDATE(), 'Activa'), -- Actividad 1 (Caminata) a Paciente 1
    (2, 1, 1, CURDATE(), CURDATE(), 'Activa'), -- Actividad 2 (Plan comidas) a Paciente 1
    (3, 2, 2, CURDATE(), CURDATE(), 'Activa'); -- Actividad 3 (Meditación) a Paciente 2

-- ==========================================================
-- tabla: seguimiento (3 entradas)
-- (Calculando IMC = peso / (estatura^2))
-- ==========================================================
INSERT INTO seguimiento
    (idPaciente, idMedico, peso, imc, nivelBienestar, observaciones)
VALUES
    (1, 1, 84.0, (84.0 / (1.75 * 1.75)), 'Regular', 'Inicio de tratamiento. Peso 1.5kg menor al inicial.'),
    (2, 2, 61.5, (61.5 / (1.60 * 1.60)), 'Bueno', 'Presión arterial controlada (120/80).'),
    (3, 1, 70.0, (70.0 / (1.80 * 1.80)), 'Excelente', 'Masa muscular en aumento, grasa corporal estable.');

-- ==========================================================
-- tabla: foro (3 entradas)
-- ==========================================================
INSERT INTO foro
    (idPaciente, titulo, contenido, reacciones)
VALUES
    (1, '¡Mi primer semana!', 'Terminé la primera semana del plan de caminata. ¡Me siento con más energía!', 5),
    (3, 'Receta de batido de proteína', 'Les comparto mi receta de batido post-entrenamiento: Plátano, avena, 1 scoop de proteína...', 12),
    (2, 'Controlando el estrés', 'Las sesiones de meditación (Actividad 3) me han ayudado mucho con la ansiedad. ¿Alguien más las prueba?', 8);
    
    
USE healthyview;
ALTER TABLE foro
ADD COLUMN imagenURL VARCHAR(255) NULL DEFAULT NULL AFTER contenido;


CREATE TABLE foroReaccion (
    idReaccion INT AUTO_INCREMENT PRIMARY KEY,
    idPublicacion INT NOT NULL,
    idPaciente INT NOT NULL,
    
    FOREIGN KEY (idPublicacion) REFERENCES foro(idPublicacion) ON DELETE CASCADE,
    FOREIGN KEY (idPaciente) REFERENCES paciente(idPaciente) ON DELETE CASCADE,
    
    -- Creamos una llave única para asegurar que un paciente solo puede
    -- reaccionar una vez por publicación
    UNIQUE KEY uk_reaccion_unica (idPublicacion, idPaciente)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;