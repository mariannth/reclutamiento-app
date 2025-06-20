CREATE DATABASE reclutamiento;
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


-- Empresas
CREATE TABLE empresas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    contacto VARCHAR(100)
);


-- Eliminar tabla citas actual para crear una nueva desde cero (haz backup antes si tienes datos)
DROP TABLE IF EXISTS citas;

-- Crear tabla citas con estructura completa y adecuada
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
    estado ENUM('PENDIENTE', 'RECHAZADO', 'NO ACUDIÓ A ENTREVISTA', 'ACEPTADO') DEFAULT 'PENDIENTE',
    comentarios TEXT,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (empresa_id) REFERENCES empresas(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Opcional: índice para consultas rápidas por usuario y fecha
CREATE INDEX idx_usuario_fecha ON citas(usuario_id, fecha);


ALTER TABLE empresas
ADD COLUMN cliente VARCHAR(100),
ADD COLUMN sucursal VARCHAR(100),
ADD COLUMN proceso_detallado TEXT,
ADD COLUMN examen_medico ENUM('SI', 'NO'),
ADD COLUMN responsables_entrevista TEXT,
ADD COLUMN rutas_transporte TEXT,
ADD COLUMN direccion_entrevista TEXT,
ADD COLUMN link_maps TEXT,
ADD COLUMN vacante VARCHAR(100),
ADD COLUMN sexo ENUM('H', 'M', 'Indistinto'),
ADD COLUMN escolaridad ENUM('primaria', 'secundaria', 'preparatoria', 'licenciatura', 'maestria', 'doctorado', 'nada'),
ADD COLUMN edad VARCHAR(20),
ADD COLUMN horarios VARCHAR(100),
ADD COLUMN salario VARCHAR(50),
ADD COLUMN prestaciones TEXT,
ADD COLUMN experiencia VARCHAR(50),
ADD COLUMN aspecto_fisico TEXT,
ADD COLUMN requisitos_adicionales TEXT,
ADD COLUMN categorias TEXT,
ADD COLUMN tiempo_planta VARCHAR(100),
ADD COLUMN banco_pago VARCHAR(100),
ADD COLUMN semana_fondo VARCHAR(100),
ADD COLUMN actividades TEXT,
ADD COLUMN prestaciones_extra TEXT,
ADD COLUMN doc_solicitud TINYINT(1),
ADD COLUMN doc_ine TINYINT(1),
ADD COLUMN doc_acta TINYINT(1),
ADD COLUMN doc_curp TINYINT(1),
ADD COLUMN doc_nss TINYINT(1),
ADD COLUMN doc_rfc TINYINT(1),
ADD COLUMN doc_domicilio TINYINT(1),
ADD COLUMN doc_estudios TINYINT(1),
ADD COLUMN doc_retencion TINYINT(1);


ALTER TABLE citas 
ADD COLUMN estado_seguimiento ENUM(
    'aceptado', 
    'rechazado', 
    'no acudió', 
    'contratado', 
    'reingreso', 
    'primer día'
) DEFAULT NULL AFTER estado,
ADD COLUMN seguimiento_actualizado_por INT NULL AFTER estado_seguimiento,
ADD COLUMN fecha_actualizacion_seguimiento TIMESTAMP NULL DEFAULT NULL AFTER seguimiento_actualizado_por,
ADD CONSTRAINT fk_seguimiento_usuario FOREIGN KEY (seguimiento_actualizado_por) REFERENCES usuarios(id);

ALTER TABLE empresas ADD COLUMN cantidad_vacantes INT DEFAULT 0;
