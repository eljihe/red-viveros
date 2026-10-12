-- Agrega la tabla `servicios`: talleres que ofrece un vivero (injerto, composta, etc.)
-- con horario y costo en texto libre. Se muestran en la ficha del vivero en el mapa
-- público, igual que el inventario, sin requerir registro para verlos.

CREATE TABLE servicios (
  id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  vivero_id      INT UNSIGNED NOT NULL,
  titulo         VARCHAR(80)  NOT NULL COMMENT 'Ej. Taller de injerto de cacao',
  descripcion    VARCHAR(500) NULL,
  horario        VARCHAR(120) NULL COMMENT 'Texto libre: Sabados 9am, Bajo cita previa...',
  costo          VARCHAR(60)  NULL COMMENT 'Texto libre: Gratis, $150 por persona... (vacio = a tratar)',
  activo         TINYINT(1)   NOT NULL DEFAULT 1,
  creado_en      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_servicios_vivero (vivero_id, activo),
  CONSTRAINT fk_servicios_vivero FOREIGN KEY (vivero_id) REFERENCES viveros (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Talleres o servicios que ofrece el vivero (injerto, composta, etc.)';
