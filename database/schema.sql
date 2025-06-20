-- Crear base de datos
DROP DATABASE IF EXISTS reclutamiento;
CREATE DATABASE reclutamiento CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE reclutamiento;

-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('superadmin', 'admin', 'usuario', 'lider') NOT NULL DEFAULT 'usuario',
    activo BOOLEAN DEFAULT TRUE,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de empresas
CREATE TABLE empresas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    cliente VARCHAR(100),
    sucursal VARCHAR(100),
    contacto VARCHAR(100),
    proceso_detallado TEXT,
    examen_medico ENUM('SI', 'NO'),
    responsables_entrevista TEXT,
    rutas_transporte TEXT,
    direccion_entrevista TEXT,
    link_maps TEXT,
    vacante VARCHAR(100),
    sexo ENUM('H', 'M', 'Indistinto'),
    escolaridad ENUM('primaria', 'secundaria', 'preparatoria', 'licenciatura', 'maestria', 'doctorado', 'nada'),
    edad VARCHAR(20),
    horarios VARCHAR(100),
    salario VARCHAR(50),
    prestaciones TEXT,
    experiencia VARCHAR(50),
    aspecto_fisico TEXT,
    requisitos_adicionales TEXT,
    categorias TEXT,
    tiempo_planta VARCHAR(100),
    banco_pago VARCHAR(100),
    semana_fondo VARCHAR(100),
    actividades TEXT,
    prestaciones_extra TEXT,
    cantidad_vacantes INT DEFAULT 0,
    activo BOOLEAN DEFAULT TRUE,
    doc_solicitud TINYINT(1),
    doc_ine TINYINT(1),
    doc_acta TINYINT(1),
    doc_curp TINYINT(1),
    doc_nss TINYINT(1),
    doc_rfc TINYINT(1),
    doc_domicilio TINYINT(1),
    doc_estudios TINYINT(1),
    doc_retencion TINYINT(1)
);

-- Tabla de citas
CREATE TABLE citas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT NOT NULL,
    usuario_id INT NOT NULL,

    nombre VARCHAR(100) NOT NULL,
    apellido_paterno VARCHAR(100) NOT NULL,
    apellido_materno VARCHAR(100) NOT NULL,
    edad INT NOT NULL,
    escolaridad ENUM('primaria', 'secundaria', 'preparatoria', 'licenciatura', 'maestria', 'doctorado', 'nada') NOT NULL,

    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    ruta VARCHAR(100),
    coordinador VARCHAR(100) NOT NULL,
    reclutador VARCHAR(100) NOT NULL,

    estado ENUM('PENDIENTE', 'RECHAZADO', 'ACUDIÓ','NO ACUDIÓ A ENTREVISTA', 'PRIMER DÍA DE TRABAJO', 'SEGUNDO DÍA DE TRABAJO') DEFAULT 'CITADO',

    -- Seguimiento reclutador
    estado_seguimiento ENUM(
        'ACEPTADO',
        'RECHAZADO',
        'NO ACIDIÓ',
        'CONTRATADO',
        'REINGRESO',
        'PRIMER DÍA DE TRABAJO',
        'SEGUNDO DÍA DE TRABAJO'
    ) DEFAULT 'citado',
    seguimiento_usuario TEXT,
    seguimiento_actualizado_por INT,
    fecha_actualizacion_seguimiento TIMESTAMP NULL DEFAULT NULL,

    -- Seguimiento líder
    seguimiento_lider TEXT,
    actualizado_por_lider VARCHAR(100),
    fecha_actualizacion_lider TIMESTAMP NULL DEFAULT NULL,

    comentarios TEXT,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    -- Claves foráneas
    FOREIGN KEY (empresa_id) REFERENCES empresas(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (seguimiento_actualizado_por) REFERENCES usuarios(id)
);

-- Índices sugeridos
CREATE INDEX idx_usuario_fecha ON citas(usuario_id, fecha);


INSERT INTO `empresas` (`id`, `nombre`, `contacto`, `cliente`, `sucursal`, `proceso_detallado`, `examen_medico`, `responsables_entrevista`, `rutas_transporte`, `direccion_entrevista`, `link_maps`, `vacante`, `sexo`, `escolaridad`, `edad`, `horarios`, `salario`, `prestaciones`, `experiencia`, `aspecto_fisico`, `requisitos_adicionales`, `categorias`, `tiempo_planta`, `banco_pago`, `semana_fondo`, `actividades`, `prestaciones_extra`, `doc_solicitud`, `doc_ine`, `doc_acta`, `doc_curp`, `doc_nss`, `doc_rfc`, `doc_domicilio`, `doc_estudios`, `doc_retencion`, `cantidad_vacantes`, `activo`) VALUES
(1, 'AguaFria', 'NA', 'AguaFria', 'Apodaca', 'DÍA 1 ● Entrevista con RH APTOS INGRESAN AL DÍA SIGUIENTE', 'NO', 'Lic. Daniela Salazar', 'San Nicolás, Santa María', 'Ojo de Agua 400, Apodaca Centro, 66600 Cdad. Apodaca, N.L.', 'https://maps.app.goo.gl/TcLsZXfXTPgRYwr78', 'Ayudante general', 'H', '', '18 a 53 años', 'HORARIO ROTATIVO SEMANAL (NO HAY TURNOS NI DESCANSO FIJO)  ● Día: 07:00 am a 03:00 pm ● Tarde: 03:00', '● Sueldo diario $306.13 (al sexto mes sube sueldo ', '● Premio de puntualidad y asistencia semanal $195.16	 ● 35 días de aguinaldo	 ● 13% fondo de ahorro	 ● 15% vales de despensa cada 2 semanas ($642 aprox)	 ● Transporte (San Nicolás y Santa Maria)	 ● Ayuda de transporte en caso de aplicar (Juarez). El monto se le menciona en entrevista $350 a la semana.	 ● El Bono de permanencia $4,500 se entrega de la siguiente manera. manera:	\"○ $1,000 primer mes. ○ $1,500 segundo mes. ○ $2,000 tercer mes.\" ● Tiempo extra (sujeto a operación)	 \"● Seguro de gastos médicos mayores al cumplir 9 meses (Beneficio de la clínica de Cervecería Cuauhtémoc y Famosa)\"	 ● Área de comida (no comedor subsidiado)	 ● Uniforme gratuito (e entrega a la semana)	 ● Bono de productividad cada 3 meses (se le menciona el monto en entrevista)', 'NO', '● Tatuajes no ofensivos. ● No personas con discapacidad, lesiones u operación reciente.', '-', 'NO', 'NO', 'Nomina BBVA / único banco (si tienen cuenta que la lleven)', 'Día de pago: viernes ●NO SEMANA DE FONDO NI DESFASADA', '\"● Realizan hielo ● Emplaye de tarima ● Almacén ● Acomodo de materia prima ● Trabajo de pie ● Tolerancia a bajas temperaturas -5 (en algunas áreas).\"', '-', 1, 1, 1, 0, 1, 1, 1, 0, 0, 100, 1);

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `rol`, `activo`, `creado_en`) VALUES
(1, 'Super Admin', 'superadmin@admin.com', '$2y$10$0bGsT/ZRG4XbDU7jZWsN2uvJclm.uFSN6u8BC7uMd9iXMHaqRn/su', 'superadmin', 1, '2025-06-15 02:19:00'),
(2, 'MarianaTH', 'marith.3289@gmail.com', '$2y$10$p7qZw//X1HHrH2KiW885nus0H9TcfzA7KXq8hKb9OPbQYuzVBhOe6', 'usuario', 1, '2025-06-15 02:21:39'),
(3, 'MarianaLider', 'mth96225@gmail.com', '$2y$10$0LWg.TE4Nq4Oee4Js9hP8.dl2T49lKLFq3.XClACuMjX2/.Mald5e', 'lider', 1, '2025-06-15 03:47:19'),
(5, 'MarianaAdmin', 'admin@demo.com', '$2y$10$ZuJKws8BzuNfwFcCmsJq5uFgDtDNvxYHgWeRt9xIVfUpi/UW63a6m', 'admin', 1, '2025-06-20 02:09:43');
