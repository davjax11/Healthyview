DROP TABLE IF EXISTS `actividad`;

CREATE TABLE `actividad` (
  `idActividad` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `tipo` enum('Física','Alimentaria','Mental','Otro') DEFAULT 'Otro',
  `descripcion` text DEFAULT NULL,
  `frecuencia` varchar(80) DEFAULT NULL,
  `fechaRegistro` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`idActividad`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='catálogo de actividades saludables disponibles.';

INSERT INTO actividad VALUES("1","Caminata diaria","Física","Caminata de 30 minutos a paso moderado","Diaria","2025-11-12 14:14:39");
INSERT INTO actividad VALUES("2","Plan de comidas saludable","Alimentaria","Plan semanal con calorías controladas","Semanal","2025-11-12 14:14:39");
INSERT INTO actividad VALUES("3","Meditación guiada","Mental","Sesiones de 15 minutos para control de estrés","Diaria","2025-11-12 14:14:39");



DROP TABLE IF EXISTS `actividadpaciente`;

CREATE TABLE `actividadpaciente` (
  `idAsignacion` int(11) NOT NULL AUTO_INCREMENT,
  `idActividad` int(11) NOT NULL,
  `idPaciente` int(11) NOT NULL,
  `idMedico` int(11) DEFAULT NULL,
  `fechaAsignacion` date NOT NULL,
  `fechaInicio` date DEFAULT NULL,
  `fechaFin` date DEFAULT NULL,
  `progreso` decimal(5,2) DEFAULT 0.00,
  `estado` enum('Activa','Completada','Cancelada') DEFAULT 'Activa',
  `observaciones` text DEFAULT NULL,
  PRIMARY KEY (`idAsignacion`),
  KEY `idActividad` (`idActividad`),
  KEY `idPaciente` (`idPaciente`),
  KEY `idMedico` (`idMedico`),
  CONSTRAINT `actividadpaciente_ibfk_1` FOREIGN KEY (`idActividad`) REFERENCES `actividad` (`idActividad`) ON UPDATE CASCADE,
  CONSTRAINT `actividadpaciente_ibfk_2` FOREIGN KEY (`idPaciente`) REFERENCES `paciente` (`idPaciente`) ON UPDATE CASCADE,
  CONSTRAINT `actividadpaciente_ibfk_3` FOREIGN KEY (`idMedico`) REFERENCES `medico` (`idMedico`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='registra las actividades asignadas a cada paciente y su progreso.';

INSERT INTO actividadpaciente VALUES("1","1","1","1","2025-11-12","2025-11-12","","0.00","Activa","");
INSERT INTO actividadpaciente VALUES("2","2","1","1","2025-11-12","2025-11-12","","0.00","Activa","");
INSERT INTO actividadpaciente VALUES("3","3","2","2","2025-11-12","2025-11-12","","0.00","Activa","");



DROP TABLE IF EXISTS `administrador`;

CREATE TABLE `administrador` (
  `idAdmin` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `apellidoPaterno` varchar(100) NOT NULL,
  `apellidoMaterno` varchar(100) DEFAULT NULL,
  `correo` varchar(150) NOT NULL,
  `passwordHash` varchar(255) NOT NULL,
  `departamento` varchar(100) DEFAULT NULL,
  `permisos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permisos`)),
  `fechaRegistro` datetime DEFAULT current_timestamp(),
  `estado` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`idAdmin`),
  UNIQUE KEY `correo` (`correo`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='gestiona el sistema y tiene acceso a reportes globales.';

INSERT INTO administrador VALUES("1","Admin","Principal","","admin@healthyview.com","$2y$10$VBu/GZZSAJ4odbmxKH8bnuhr.FTeLMhEhFJ3RSwNJYH/9PqHxU/Vm","Sistemas","[\"usuarios\", \"reportes\", \"medicos\", \"pacientes\"]","2025-11-12 14:14:39","1");
INSERT INTO administrador VALUES("2","Soporte","Técnico","","soporte@healthyview.com","$2y$10$VBu/GZZSAJ4odbmxKH8bnuhr.FTeLMhEhFJ3RSwNJYH/9PqHxU/Vm","Sistemas","[\"usuarios\", \"medicos\"]","2025-11-12 14:14:39","1");
INSERT INTO administrador VALUES("3","Gerencia","Operaciones","","gerencia@healthyview.com","$2y$10$VBu/GZZSAJ4odbmxKH8bnuhr.FTeLMhEhFJ3RSwNJYH/9PqHxU/Vm","Operaciones","[\"reportes\"]","2025-11-12 14:14:39","1");



DROP TABLE IF EXISTS `cita`;

CREATE TABLE `cita` (
  `idCita` int(11) NOT NULL AUTO_INCREMENT,
  `idPaciente` int(11) NOT NULL,
  `idMedico` int(11) NOT NULL,
  `fechaHora` datetime NOT NULL,
  `duracionMin` int(11) DEFAULT 30,
  `motivo` varchar(255) DEFAULT NULL,
  `estado` enum('Programada','Completada','Cancelada','NoAsistida') DEFAULT 'Programada',
  `creadoEn` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`idCita`),
  KEY `idPaciente` (`idPaciente`),
  KEY `idMedico` (`idMedico`),
  CONSTRAINT `cita_ibfk_1` FOREIGN KEY (`idPaciente`) REFERENCES `paciente` (`idPaciente`) ON UPDATE CASCADE,
  CONSTRAINT `cita_ibfk_2` FOREIGN KEY (`idMedico`) REFERENCES `medico` (`idMedico`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='controla las citas médicas programadas entre pacientes y médicos.';

INSERT INTO cita VALUES("1","1","1","2025-11-14 14:14:39","45","Consulta nutricional de seguimiento","Programada","2025-11-12 14:14:39");
INSERT INTO cita VALUES("2","2","2","2025-11-15 14:14:39","60","Revisión cardiológica anual","Programada","2025-11-12 14:14:39");
INSERT INTO cita VALUES("3","3","1","2025-11-15 14:14:39","30","Asesoría plan alimenticio deportivo","Programada","2025-11-12 14:14:39");



DROP TABLE IF EXISTS `foro`;

CREATE TABLE `foro` (
  `idPublicacion` int(11) NOT NULL AUTO_INCREMENT,
  `idPaciente` int(11) DEFAULT NULL,
  `titulo` varchar(200) DEFAULT NULL,
  `contenido` text NOT NULL,
  `fechaPublicacion` datetime DEFAULT current_timestamp(),
  `reacciones` int(11) DEFAULT 0,
  PRIMARY KEY (`idPublicacion`),
  KEY `idPaciente` (`idPaciente`),
  CONSTRAINT `foro_ibfk_1` FOREIGN KEY (`idPaciente`) REFERENCES `paciente` (`idPaciente`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='espacio donde los pacientes pueden publicar mensajes motivacionales.';

INSERT INTO foro VALUES("1","1","¡Mi primer semana!","Terminé la primera semana del plan de caminata. ¡Me siento con más energía!","2025-11-12 14:14:39","5");
INSERT INTO foro VALUES("2","3","Receta de batido de proteína","Les comparto mi receta de batido post-entrenamiento: Plátano, avena, 1 scoop de proteína...","2025-11-12 14:14:39","12");
INSERT INTO foro VALUES("3","2","Controlando el estrés","Las sesiones de meditación (Actividad 3) me han ayudado mucho con la ansiedad. ¿Alguien más las prueba?","2025-11-12 14:14:39","8");



DROP TABLE IF EXISTS `medico`;

CREATE TABLE `medico` (
  `idMedico` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `apellidoPaterno` varchar(100) NOT NULL,
  `apellidoMaterno` varchar(100) DEFAULT NULL,
  `correo` varchar(150) NOT NULL,
  `passwordHash` varchar(255) NOT NULL,
  `especialidad` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `cedulaProfesional` varchar(50) DEFAULT NULL,
  `disponibilidad` varchar(100) DEFAULT NULL,
  `fechaRegistro` datetime DEFAULT current_timestamp(),
  `estado` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`idMedico`),
  UNIQUE KEY `correo` (`correo`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='almacena información profesional y de acceso de los médicos.';

INSERT INTO medico VALUES("1","Elena","Herrera","Ruiz","elena.herrera@healthyview.com","$2y$10$VBu/GZZSAJ4odbmxKH8bnuhr.FTeLMhEhFJ3RSwNJYH/9PqHxU/Vm","Nutrición Clínica","5511112222","1234567","Matutino","2025-11-12 14:14:39","1");
INSERT INTO medico VALUES("2","Miguel","Castro","Vera","miguel.castro@healthyview.com","$2y$10$IJ9auuw2YK72fchLpd8St.vOAUm4FVTiHmhgbHNmBrjANCKKsp75y","Cardiología","5533334444","7654321","Vespertino","2025-11-12 14:14:39","1");
INSERT INTO medico VALUES("3","Sofía","Mendoza","Cruz","sofia.mendoza@healthyview.com","$2y$10$VBu/GZZSAJ4odbmxKH8bnuhr.FTeLMhEhFJ3RSwNJYH/9PqHxU/Vm","Medicina General y Deporte","5566667777","8889990","Ambos","2025-11-12 14:14:39","1");



DROP TABLE IF EXISTS `paciente`;

CREATE TABLE `paciente` (
  `idPaciente` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `apellidoPaterno` varchar(100) NOT NULL,
  `apellidoMaterno` varchar(100) DEFAULT NULL,
  `correo` varchar(150) NOT NULL,
  `passwordHash` varchar(255) NOT NULL,
  `fechaNacimiento` date DEFAULT NULL,
  `genero` enum('Masculino','Femenino','Otro') DEFAULT 'Otro',
  `peso` decimal(5,2) DEFAULT NULL,
  `estatura` decimal(4,2) DEFAULT NULL,
  `imc` decimal(5,2) DEFAULT NULL,
  `diagnostico` text DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `fechaRegistro` datetime DEFAULT current_timestamp(),
  `estado` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`idPaciente`),
  UNIQUE KEY `correo` (`correo`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='almacena los datos generales y de salud del paciente.';

INSERT INTO paciente VALUES("1","Carlos","Sánchez","López","carlos.sanchez@mail.com","$2y$10$VBu/GZZSAJ4odbmxKH8bnuhr.FTeLMhEhFJ3RSwNJYH/9PqHxU/Vm","1990-05-15","Masculino","85.50","1.75","27.92","Sobrepeso tipo I. Requiere plan alimenticio y rutina de ejercicio.","5512345678","2025-11-12 14:14:39","1");
INSERT INTO paciente VALUES("2","Ana","García","Martínez","ana.garcia@mail.com","$2y$10$VvR3gGGiEivcB8g90gnFeeAuYxWltWKJRigc75pm08dMJ6gUqhTHS","1985-11-20","Femenino","62.00","1.60","24.22","Hipertensión arterial leve. Monitoreo de sodio.","5598765432","2025-11-12 14:14:39","1");
INSERT INTO paciente VALUES("3","Luis","Torres","Díaz","luis.torres@mail.com","$2y$10$VBu/GZZSAJ4odbmxKH8bnuhr.FTeLMhEhFJ3RSwNJYH/9PqHxU/Vm","2000-01-30","Masculino","70.20","1.80","21.67","Paciente saludable, buscando optimizar rendimiento deportivo.","5555555555","2025-11-12 14:14:39","1");



DROP TABLE IF EXISTS `receta`;

CREATE TABLE `receta` (
  `idReceta` int(11) NOT NULL AUTO_INCREMENT,
  `idPaciente` int(11) NOT NULL,
  `idMedico` int(11) NOT NULL,
  `fechaEmision` datetime NOT NULL DEFAULT current_timestamp(),
  `resumen` varchar(255) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `estado` enum('Activa','Finalizada','Cancelada') DEFAULT 'Activa',
  PRIMARY KEY (`idReceta`),
  KEY `idPaciente` (`idPaciente`),
  KEY `idMedico` (`idMedico`),
  CONSTRAINT `receta_ibfk_1` FOREIGN KEY (`idPaciente`) REFERENCES `paciente` (`idPaciente`) ON UPDATE CASCADE,
  CONSTRAINT `receta_ibfk_2` FOREIGN KEY (`idMedico`) REFERENCES `medico` (`idMedico`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='registra las recetas médicas generadas por los doctores.';

INSERT INTO receta VALUES("1","2","2","2025-11-12 14:14:39","Control de hipertensión","Monitorear presión arterial dos veces al día.","Activa");
INSERT INTO receta VALUES("2","1","1","2025-11-12 14:14:39","Plan vitamínico y reducción de peso","Ajustar dieta y suplementar Vitamina D.","Activa");
INSERT INTO receta VALUES("3","2","2","2025-11-12 14:14:39","Ajuste de dosis Losartán","Paciente reporta mareos leves, ajustar dosis.","Activa");



DROP TABLE IF EXISTS `recetaitem`;

CREATE TABLE `recetaitem` (
  `idItem` int(11) NOT NULL AUTO_INCREMENT,
  `idReceta` int(11) NOT NULL,
  `nombreMedicamento` varchar(200) NOT NULL,
  `dosis` varchar(100) DEFAULT NULL,
  `frecuencia` varchar(100) DEFAULT NULL,
  `duracion` varchar(50) DEFAULT NULL,
  `instrucciones` text DEFAULT NULL,
  PRIMARY KEY (`idItem`),
  KEY `idReceta` (`idReceta`),
  CONSTRAINT `recetaitem_ibfk_1` FOREIGN KEY (`idReceta`) REFERENCES `receta` (`idReceta`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='detalla los medicamentos y dosis de cada receta.';

INSERT INTO recetaitem VALUES("1","1","Losartán","50 mg","1 cada 24 horas","90 días","Tomar por la mañana, preferentemente a la misma hora.");
INSERT INTO recetaitem VALUES("2","2","Vitamina D3","2000 UI","1 cada 24 horas","60 días","Tomar junto con el desayuno.");
INSERT INTO recetaitem VALUES("3","3","Losartán","25 mg","1 cada 24 horas","30 días","Nueva dosis reducida. Vigilar síntomas.");



DROP TABLE IF EXISTS `seguimiento`;

CREATE TABLE `seguimiento` (
  `idSeguimiento` int(11) NOT NULL AUTO_INCREMENT,
  `idPaciente` int(11) NOT NULL,
  `idMedico` int(11) DEFAULT NULL,
  `fechaRegistro` datetime DEFAULT current_timestamp(),
  `peso` decimal(5,2) DEFAULT NULL,
  `imc` decimal(5,2) DEFAULT NULL,
  `nivelBienestar` enum('Excelente','Bueno','Regular','Malo') DEFAULT 'Regular',
  `observaciones` text DEFAULT NULL,
  PRIMARY KEY (`idSeguimiento`),
  KEY `idPaciente` (`idPaciente`),
  KEY `idMedico` (`idMedico`),
  CONSTRAINT `seguimiento_ibfk_1` FOREIGN KEY (`idPaciente`) REFERENCES `paciente` (`idPaciente`) ON UPDATE CASCADE,
  CONSTRAINT `seguimiento_ibfk_2` FOREIGN KEY (`idMedico`) REFERENCES `medico` (`idMedico`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='registra el progreso y bienestar del paciente con evaluación médica.';

INSERT INTO seguimiento VALUES("1","1","1","2025-11-12 14:14:39","84.00","27.43","Regular","Inicio de tratamiento. Peso 1.5kg menor al inicial.");
INSERT INTO seguimiento VALUES("2","2","2","2025-11-12 14:14:39","61.50","24.02","Bueno","Presión arterial controlada (120/80).");
INSERT INTO seguimiento VALUES("3","3","1","2025-11-12 14:14:39","70.00","21.60","Excelente","Masa muscular en aumento, grasa corporal estable.");



DROP TABLE IF EXISTS `sesion`;

CREATE TABLE `sesion` (
  `idSesion` int(11) NOT NULL AUTO_INCREMENT,
  `tipoUsuario` enum('Paciente','Medico','Administrador') NOT NULL,
  `idPaciente` int(11) DEFAULT NULL,
  `idMedico` int(11) DEFAULT NULL,
  `idAdmin` int(11) DEFAULT NULL,
  `fechaInicio` datetime NOT NULL DEFAULT current_timestamp(),
  `fechaFin` datetime DEFAULT NULL,
  `ipAcceso` varchar(45) DEFAULT NULL,
  `estado` enum('Activa','Cerrada','Fallida') DEFAULT 'Activa',
  PRIMARY KEY (`idSesion`),
  KEY `idPaciente` (`idPaciente`),
  KEY `idMedico` (`idMedico`),
  KEY `idAdmin` (`idAdmin`),
  CONSTRAINT `sesion_ibfk_1` FOREIGN KEY (`idPaciente`) REFERENCES `paciente` (`idPaciente`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `sesion_ibfk_2` FOREIGN KEY (`idMedico`) REFERENCES `medico` (`idMedico`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `sesion_ibfk_3` FOREIGN KEY (`idAdmin`) REFERENCES `administrador` (`idAdmin`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='controla y audita las sesiones de usuarios del sistema.';

INSERT INTO sesion VALUES("1","Paciente","1","","","2025-11-12 14:14:39","","192.168.1.10","Activa");
INSERT INTO sesion VALUES("2","Medico","","1","","2025-11-12 14:14:39","","200.50.10.1","Activa");
INSERT INTO sesion VALUES("3","Administrador","","","1","2025-11-12 14:14:39","","189.10.10.5","Cerrada");



