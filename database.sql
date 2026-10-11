-- =====================================================================
--  Red de Viveros Tabasco · Base de datos
--  Compatible con MySQL 8.0+ y MariaDB 10.4+ (GoDaddy cPanel, Laragon, XAMPP)
--
--  CÓMO IMPORTAR
--  · Local (Laragon/XAMPP): en phpMyAdmin crea la base "red_viveros"
--    con cotejamiento utf8mb4_unicode_ci, selecciónala y usa Importar.
--  · cPanel: crea la base en "Bases de datos MySQL" (quedará con prefijo,
--    p. ej. usuario_red_viveros), ábrela en phpMyAdmin e importa este archivo.
--    Este script NO crea la base para que funcione igual en ambos casos.
--
--  ADMINISTRADOR INICIAL (ver final del archivo)
--    Teléfono: 9930000000   PIN temporal: 123456
--    Cambia el teléfono por el tuyo y al entrar el sistema te pedirá un PIN nuevo.
-- =====================================================================

SET NAMES utf8mb4;
SET time_zone = '-06:00';
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS reportes, contactos, restablecimientos_pin, fotos_producto, fotos_vivero,
  productos, inventario, viveros, compradores, usuarios, comunidades, municipios, estados,
  roles, especies, categorias, unidades;

SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------
--  CATÁLOGOS
-- ---------------------------------------------------------------------

CREATE TABLE roles (
  id          TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  clave       VARCHAR(30)  NOT NULL COMMENT 'Valor que usa el código PHP',
  nombre      VARCHAR(50)  NOT NULL COMMENT 'Texto que ve la gente',
  descripcion VARCHAR(150) NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uk_roles_clave (clave)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE estados (
  id     TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(40) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uk_estados_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE municipios (
  id        SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  estado_id TINYINT UNSIGNED  NOT NULL,
  nombre    VARCHAR(60) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uk_municipios (estado_id, nombre),
  CONSTRAINT fk_municipios_estado FOREIGN KEY (estado_id) REFERENCES estados (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE comunidades (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  municipio_id SMALLINT UNSIGNED NOT NULL,
  nombre       VARCHAR(80) NOT NULL COMMENT 'Ej. Ranchería Cupilco',
  activo       TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY uk_comunidades (municipio_id, nombre),
  CONSTRAINT fk_comunidades_municipio FOREIGN KEY (municipio_id) REFERENCES municipios (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Catálogo que da de alta el administrador';

CREATE TABLE especies (
  id                SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre            VARCHAR(60) NOT NULL,
  nombre_cientifico VARCHAR(80) NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uk_especies_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE categorias (
  id     TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(40) NOT NULL,
  orden  TINYINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY uk_categorias_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE unidades (
  id     TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(20) NOT NULL COMMENT 'Singular: planta, kg, pieza…',
  plural VARCHAR(20) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uk_unidades_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  CUENTAS
-- ---------------------------------------------------------------------

CREATE TABLE usuarios (
  id                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  rol_id            TINYINT UNSIGNED NOT NULL,
  telefono          CHAR(10)     NOT NULL COMMENT 'Usuario para iniciar sesión, 10 dígitos',
  pin_hash          VARCHAR(255) NOT NULL COMMENT 'password_hash() del PIN de 6 dígitos',
  nombre            VARCHAR(60)  NOT NULL,
  apellidos         VARCHAR(80)  NOT NULL,
  email             VARCHAR(120) NULL COMMENT 'Opcional; obligatorio para administradores (avisos)',
  estado            ENUM('activo','bloqueado') NOT NULL DEFAULT 'activo',
  debe_cambiar_pin  TINYINT(1)   NOT NULL DEFAULT 0 COMMENT '1 tras un restablecimiento',
  intentos_fallidos TINYINT UNSIGNED NOT NULL DEFAULT 0,
  bloqueado_hasta   DATETIME     NULL COMMENT 'Bloqueo temporal tras 5 intentos fallidos',
  acepto_terminos_en DATETIME    NOT NULL,
  ultimo_acceso     DATETIME     NULL,
  creado_en         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  actualizado_en    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uk_usuarios_telefono (telefono),
  UNIQUE KEY uk_usuarios_email (email),
  KEY idx_usuarios_rol (rol_id),
  CONSTRAINT fk_usuarios_rol FOREIGN KEY (rol_id) REFERENCES roles (id),
  CONSTRAINT chk_usuarios_telefono CHECK (telefono REGEXP '^[0-9]{10}$')
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE compradores (
  usuario_id        INT UNSIGNED NOT NULL,
  municipio_id      SMALLINT UNSIGNED NOT NULL,
  comunidad_colonia VARCHAR(80)  NOT NULL,
  tipo_comprador    ENUM('particular','tienda_negocio','acopiador') NOT NULL DEFAULT 'particular',
  rfc               VARCHAR(13)  NULL COMMENT 'Opcional, solo negocios',
  lat               DECIMAL(9,6) NULL COMMENT 'Opcional, para calcular entregas',
  lng               DECIMAL(9,6) NULL,
  PRIMARY KEY (usuario_id),
  KEY idx_compradores_municipio (municipio_id),
  CONSTRAINT fk_compradores_usuario   FOREIGN KEY (usuario_id)   REFERENCES usuarios (id) ON DELETE CASCADE,
  CONSTRAINT fk_compradores_municipio FOREIGN KEY (municipio_id) REFERENCES municipios (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Solo usuarios con rol comprador';

CREATE TABLE restablecimientos_pin (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  usuario_id  INT UNSIGNED NOT NULL,
  metodo      ENUM('admin','correo') NOT NULL,
  token_hash  VARCHAR(255) NOT NULL COMMENT 'PIN temporal o token de correo, siempre cifrado',
  creado_por  INT UNSIGNED NULL COMMENT 'Administrador que lo generó (método admin)',
  expira_en   DATETIME     NOT NULL,
  usado_en    DATETIME     NULL,
  creado_en   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_restab_usuario (usuario_id, expira_en),
  CONSTRAINT fk_restab_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE CASCADE,
  CONSTRAINT fk_restab_admin   FOREIGN KEY (creado_por) REFERENCES usuarios (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  VIVEROS
-- ---------------------------------------------------------------------

CREATE TABLE viveros (
  id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  usuario_id     INT UNSIGNED NOT NULL COMMENT 'Responsable; una cuenta = un vivero',
  comunidad_id   INT UNSIGNED NOT NULL COMMENT 'Municipio y estado se obtienen de aquí',
  nombre         VARCHAR(80)  NOT NULL,
  historia       TEXT         NULL COMMENT 'Sección "Conoce al vivero"',
  lat            DECIMAL(9,6) NOT NULL,
  lng            DECIMAL(9,6) NOT NULL,
  integrantes    SMALLINT UNSIGNED NULL,
  estado         ENUM('pendiente','aprobado','rechazado','suspendido') NOT NULL DEFAULT 'pendiente',
  motivo_estado  VARCHAR(255) NULL COMMENT 'Motivo de rechazo o suspensión',
  revisado_por   INT UNSIGNED NULL,
  revisado_en    DATETIME     NULL,
  creado_en      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  actualizado_en DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uk_viveros_usuario (usuario_id),
  KEY idx_viveros_estado (estado),
  KEY idx_viveros_comunidad (comunidad_id),
  CONSTRAINT fk_viveros_usuario   FOREIGN KEY (usuario_id)   REFERENCES usuarios (id) ON DELETE CASCADE,
  CONSTRAINT fk_viveros_comunidad FOREIGN KEY (comunidad_id) REFERENCES comunidades (id),
  CONSTRAINT fk_viveros_revisor   FOREIGN KEY (revisado_por) REFERENCES usuarios (id) ON DELETE SET NULL,
  CONSTRAINT chk_viveros_coords CHECK (lat BETWEEN 14 AND 33 AND lng BETWEEN -118 AND -86)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Solo aparece en el mapa cuando estado = aprobado';

CREATE TABLE inventario (
  vivero_id      INT UNSIGNED      NOT NULL,
  especie_id     SMALLINT UNSIGNED NOT NULL,
  cantidad       INT UNSIGNED      NOT NULL,
  edad_meses     TINYINT UNSIGNED  NULL,
  actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (vivero_id, especie_id),
  KEY idx_inventario_especie (especie_id),
  CONSTRAINT fk_inventario_vivero  FOREIGN KEY (vivero_id)  REFERENCES viveros (id) ON DELETE CASCADE,
  CONSTRAINT fk_inventario_especie FOREIGN KEY (especie_id) REFERENCES especies (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE fotos_vivero (
  id        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  vivero_id INT UNSIGNED NOT NULL,
  archivo   VARCHAR(120) NOT NULL COMMENT 'Nombre del archivo en uploads/viveros/',
  orden     TINYINT UNSIGNED NOT NULL DEFAULT 0,
  creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_fotos_vivero (vivero_id, orden),
  CONSTRAINT fk_fotos_vivero FOREIGN KEY (vivero_id) REFERENCES viveros (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  MERCADO
-- ---------------------------------------------------------------------

CREATE TABLE productos (
  id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  vivero_id      INT UNSIGNED NOT NULL COMMENT 'Origen = comunidad del vivero',
  categoria_id   TINYINT UNSIGNED NOT NULL,
  unidad_id      TINYINT UNSIGNED NOT NULL,
  titulo         VARCHAR(80)   NOT NULL,
  descripcion    TEXT          NULL COMMENT 'Sección "Descripción del producto"',
  precio         DECIMAL(10,2) NULL COMMENT 'NULL = precio a tratar',
  disponible     INT UNSIGNED  NULL COMMENT 'Cantidad en la unidad elegida',
  hace_entrega   TINYINT(1)    NOT NULL DEFAULT 0,
  entrega_km     SMALLINT UNSIGNED NULL COMMENT 'Radio en línea recta desde el vivero',
  entrega_costo  VARCHAR(60)   NULL COMMENT 'Texto libre: Gratis, $50 por viaje…',
  activo         TINYINT(1)    NOT NULL DEFAULT 1 COMMENT '0 = pausado o retirado por admin',
  creado_en      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_productos_vivero (vivero_id, activo),
  KEY idx_productos_categoria (categoria_id, activo),
  KEY idx_productos_fecha (creado_en),
  CONSTRAINT fk_productos_vivero    FOREIGN KEY (vivero_id)    REFERENCES viveros (id) ON DELETE CASCADE,
  CONSTRAINT fk_productos_categoria FOREIGN KEY (categoria_id) REFERENCES categorias (id),
  CONSTRAINT fk_productos_unidad    FOREIGN KEY (unidad_id)    REFERENCES unidades (id),
  CONSTRAINT chk_productos_entrega CHECK (hace_entrega = 0 OR entrega_km IS NOT NULL),
  CONSTRAINT chk_productos_precio  CHECK (precio IS NULL OR precio >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE fotos_producto (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  producto_id INT UNSIGNED NOT NULL,
  archivo     VARCHAR(120) NOT NULL COMMENT 'Nombre del archivo en uploads/productos/',
  orden       TINYINT UNSIGNED NOT NULL DEFAULT 0,
  creado_en   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_fotos_producto (producto_id, orden),
  CONSTRAINT fk_fotos_producto FOREIGN KEY (producto_id) REFERENCES productos (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  SEGURIDAD: CONTACTOS Y REPORTES
-- ---------------------------------------------------------------------

CREATE TABLE contactos (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  usuario_id  INT UNSIGNED NOT NULL COMMENT 'Quién vio el teléfono',
  producto_id INT UNSIGNED NOT NULL COMMENT 'Desde qué producto (el vivero se obtiene de aquí)',
  creado_en   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_contactos_usuario_fecha (usuario_id, creado_en) COMMENT 'Límite diario',
  KEY idx_contactos_producto (producto_id, creado_en),
  CONSTRAINT fk_contactos_usuario  FOREIGN KEY (usuario_id)  REFERENCES usuarios (id) ON DELETE CASCADE,
  CONSTRAINT fk_contactos_producto FOREIGN KEY (producto_id) REFERENCES productos (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE reportes (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  contacto_id  INT UNSIGNED NOT NULL COMMENT 'Solo se reporta un contacto que existió',
  reporta_id   INT UNSIGNED NOT NULL COMMENT 'Responsable del vivero que reporta',
  motivo       ENUM('estafa','acoso','no_se_presento','otro') NOT NULL,
  detalle      VARCHAR(500) NULL,
  estado       ENUM('pendiente','resuelto','descartado') NOT NULL DEFAULT 'pendiente',
  nota_admin   VARCHAR(255) NULL,
  resuelto_por INT UNSIGNED NULL,
  resuelto_en  DATETIME NULL,
  creado_en    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uk_reportes_contacto (contacto_id, reporta_id) COMMENT 'Un reporte por contacto',
  KEY idx_reportes_estado (estado, creado_en),
  CONSTRAINT fk_reportes_contacto FOREIGN KEY (contacto_id)  REFERENCES contactos (id) ON DELETE CASCADE,
  CONSTRAINT fk_reportes_reporta  FOREIGN KEY (reporta_id)   REFERENCES usuarios (id) ON DELETE CASCADE,
  CONSTRAINT fk_reportes_admin    FOREIGN KEY (resuelto_por) REFERENCES usuarios (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='El comprador reportado se obtiene de contactos.usuario_id';

-- =====================================================================
--  DATOS INICIALES
-- =====================================================================

INSERT INTO roles (id, clave, nombre, descripcion) VALUES
 (1, 'comprador',          'Comprador',             'Ve el contacto de los viveros y puede comprar'),
 (2, 'responsable_vivero', 'Responsable de vivero', 'Registra un vivero, su inventario y sus productos'),
 (3, 'admin',              'Administrador',         'Aprueba viveros, atiende reportes y restablece PIN');

INSERT INTO estados (id, nombre) VALUES (1, 'Tabasco');

INSERT INTO municipios (estado_id, nombre) VALUES
 (1,'Balancán'),(1,'Cárdenas'),(1,'Centla'),(1,'Centro'),(1,'Comalcalco'),(1,'Cunduacán'),
 (1,'Emiliano Zapata'),(1,'Huimanguillo'),(1,'Jalapa'),(1,'Jalpa de Méndez'),(1,'Jonuta'),
 (1,'Macuspana'),(1,'Nacajuca'),(1,'Paraíso'),(1,'Tacotalpa'),(1,'Teapa'),(1,'Tenosique');

-- Comunidades: las da de alta el administrador desde su panel.

INSERT INTO categorias (nombre, orden) VALUES
 ('Plantas',1),('Frutas',2),('Cacao y chocolate',3),('Verduras',4),
 ('Semillas',5),('Abono y composta',6),('Otro',7);

INSERT INTO unidades (nombre, plural) VALUES
 ('planta','plantas'),('kg','kg'),('pieza','piezas'),('racimo','racimos'),
 ('costal','costales'),('caja','cajas'),('litro','litros'),('docena','docenas');

INSERT INTO especies (nombre, nombre_cientifico) VALUES
 ('Cacao','Theobroma cacao'),('Cedro','Cedrela odorata'),('Caoba','Swietenia macrophylla'),
 ('Plátano','Musa paradisiaca'),('Coco','Cocos nucifera'),('Mango','Mangifera indica'),
 ('Naranja','Citrus sinensis'),('Limón','Citrus latifolia'),('Chicozapote','Manilkara zapota'),
 ('Guanábana','Annona muricata'),('Pimienta','Pimenta dioica'),('Aguacate','Persea americana'),
 ('Macuilí','Tabebuia rosea'),('Ramón','Brosimum alicastrum'),('Rambután','Nephelium lappaceum'),
 ('Zapote mamey','Pouteria sapota'),('Guaya','Melicoccus bijugatus'),('Café','Coffea arabica');

-- Administrador inicial. CAMBIA el teléfono y el correo por los tuyos antes de importar,
-- o edítalos después en phpMyAdmin. PIN temporal 123456; el sistema obliga a cambiarlo.
INSERT INTO usuarios (rol_id, telefono, pin_hash, nombre, apellidos, email, debe_cambiar_pin, acepto_terminos_en)
VALUES (3, '9930000000', '$2y$10$.6XYEEEaLTiN7jHCUfxiX.wqa9MZTupTDMczxGKJdwoCgcCMI2aiC',
        'Administrador', 'Red de Viveros', 'cambia-este-correo@ejemplo.com', 1, NOW());
