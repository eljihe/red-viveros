# Red de Viveros · Instrucciones del proyecto

Página web para los viveros comunitarios del programa estatal Sembrando Vida en Tabasco. Grupos de 20 a 30 personas producen plantas (cacao, frutales, maderables) en viveros que luego siembran en sus parcelas. El piloto empieza en **Comalcalco** (más de 10 viveros) y crecerá a otros municipios y estados.

Los usuarios son campesinos con celulares básicos y poca señal: todo debe ser **ligero, claro, con textos cortos en español y botones grandes**.

> No usar "Sembrando Vida" como nombre de marca, dominio ni logotipo: es un programa de gobierno y la página no es oficial.

## Funciones

1. **Mapa de viveros** (Leaflet + OpenStreetMap, vista satelital Esri, agrupación con markercluster). Cada vivero aprobado aparece con foto, historia ("Conoce al vivero"), inventario (especie, cantidad, edad en meses) y botón "Cómo llegar" (Google Maps). Filtros por especie y municipio.
2. **Registro de ubicación tocando el mapa** o con "Usar mi ubicación" (GPS). Las coordenadas se llenan solas; también se aceptan pegadas desde Google Maps.
3. **Mercado**: productos ligados a un vivero. Título, descripción, categoría, precio por unidad (vacío = "Precio a tratar"), cantidad disponible, fotos. Entrega opcional: "Hago entregas", hasta X km y costo. Si el comprador dio su ubicación, se calcula la distancia y se indica si el vivero llega a su zona. Filtros: nombre, categoría, municipio, con entrega. Ficha de producto con "Más de este vivero". Diseño inspirado en comproagro.com.
4. **Botón "+ Vender un producto"** en el mercado: lleva al formulario si ya tiene vivero; si no, al registro de vivero; si es comprador, explica que el responsable debe registrarse como vivero.
5. **Contacto solo para registrados**: sin sesión se muestra "Para ver el contacto necesita estar registrado" y botón "Registrarme". Con sesión: teléfono + botón de WhatsApp (`https://wa.me/52XXXXXXXXXX?text=...`). **El teléfono nunca se envía al navegador si no hay sesión.**

## Cuentas y seguridad

- Roles (tabla `roles`): `comprador`, `responsable_vivero`, `admin`. Un rol por usuario.
- **Inicio de sesión: teléfono (10 dígitos) + PIN de 6 dígitos** con `password_hash`/`password_verify`. 5 intentos fallidos = bloqueo de 15 minutos. Botón de cerrar sesión. Sesión larga con cookie segura (`HttpOnly`, `SameSite=Lax`, `Secure` en producción) y `session_regenerate_id` al iniciar sesión.
- **Restablecer PIN**: "Olvidé mi PIN" indica escribir al administrador por WhatsApp. El admin genera un PIN temporal desde su panel (caduca en 24 h, se guarda cifrado en `restablecimientos_pin`, obliga a cambiarlo al entrar). Recuperación por correo (enlace de 1 hora, un solo uso) queda como opcional.
- **Una cuenta de responsable = un vivero.** Un vivero nuevo queda `pendiente` y no aparece en el mapa hasta que un admin lo aprueba. Al registrarse se envía correo a los administradores. Rechazo o suspensión con motivo.
- **Comunidades**: catálogo que da de alta el admin.
- **Registro de comprador**: nombre, apellidos, teléfono, municipio, comunidad o colonia, tipo (particular, tienda o negocio, acopiador), RFC opcional, ubicación opcional (privada), aceptar términos y aviso de privacidad.
- **Tabla `contactos`**: se inserta cada vez que un usuario ve el teléfono de un producto. Usos: el vivero ve en su panel quién lo contactó; límite de 15 contactos al día (3 en las primeras 24 h de la cuenta); solo se puede reportar un contacto que existió; estadísticas para el programa.
- **Reportes** (estafa, acoso, no se presentó, otro) desde el panel del vivero. El admin revisa y bloquea usuarios.
- Captcha gratuito en el registro (Cloudflare Turnstile).
- Consejos de seguridad visibles para los viveros: no entregar sin ver el dinero en su cuenta, desconfiar de capturas de pantalla y de "te deposité de más", no compartir códigos SMS, preferir efectivo al entregar.
- Para más adelante (no implementar aún): verificación de correo.

## Tecnología

- **PHP 8.1+ puro con MVC sencillo, sin framework ni Composer.** Debe funcionar subiendo los archivos a cPanel.
- **MySQL/MariaDB** con **PDO singleton** centralizado en `App\Core\Database`, consultas preparadas siempre, `utf8mb4`.
  - **Una sola conexión**: `Database::connection()` retorna la misma instancia PDO singleton en toda la app
  - **Credenciales en configuración**: se leen de `config/config.php`, nunca hardcodeadas
  - **Manejo de errores**: captura `PDOException` y lanza `DatabaseException` personalizada
  - **Atributos PDO seguros**: `ATTR_ERRMODE=EXCEPTION`, `EMULATE_PREPARES=false`, `STRINGIFY_FETCHES=false`
- **Frontend: HTML5 + CSS propio + JavaScript sin framework.** Alpine.js permitido solo si simplifica formularios dinámicos. No usar React, Vue, Bootstrap ni Tailwind.
- Tokens **CSRF** en todos los formularios `POST`. Escapar toda salida con `htmlspecialchars`.
- **Fotos**: validar tipo real (finfo), máximo 5 MB, redimensionar a 1200 px de lado mayor y guardar como JPEG/WebP con nombre aleatorio en `public/uploads/viveros/` o `public/uploads/productos/`. En la base solo se guarda el nombre del archivo.
- Más adelante: PWA (manifest + service worker) para instalar en el celular.

 Seguir el skill php-best-practices, con estas adaptaciones:
  - Sin Composer: autoload PSR-4 propio con spl_autoload_register en app/core/autoload.php.
  - Usar declare(strict_types=1) en todos los archivos PHP.
  - No usar funciones de PHP posteriores a la versión configurada en GoDaddy.

### Entornos
- **Local**: XAMPP en Windows, proyecto en `C:\xampp\htdocs\red-viveros`. Base `red_viveros` en phpMyAdmin (`http://localhost/phpmyadmin`).
- **Producción**: GoDaddy Web Hosting Económico (cPanel, Linux). Solo `public/` debe quedar expuesto; `app/` y `config/` fuera de la raíz web o protegidos con `.htaccess`.
- `config/config.php` **no se sube a Git**; existe `config/config.example.php` como plantilla.

### Configuración centralizada
- **`config/config.php`**: Configuración de aplicación (entorno, debug, base_url, base de datos, timezone). **No versionado**.
- **`config/urls.php`**: URLs de CDN y recursos externos (Leaflet, Google Fonts). Cambiar aquí para usar otros CDN.
- **`config/paths.php`**: Rutas de carpetas (logs, uploads, etc.). Cambiar aquí si se reubican carpetas.
- **Helpers**: `url()`, `path()`, `externalUrl()` en `app/core/helpers.php` acceden a estas configuraciones.

**Regla de oro**: Nunca hardcodear valores que cambien entre entornos (timezones, URLs de CDN, rutas, credenciales, dominios). Si algo puede variar en local vs producción, va en un archivo de configuración, no en el código.
- ✅ Hardcodeable: lógica de negocio, validaciones, límites de sistema (ej. máximo 6 fotos por vivero)
- ❌ Nunca hardcodear: URLs, rutas, timezone, dominio, BD, API keys, CDNs, puertos

### Estructura
```
red-viveros/
├── CLAUDE.md
├── database.sql
├── prototipo.html          ← diseño de referencia (no se usa en producción)
├── .gitignore
├── config/
│   ├── config.example.php
│   ├── config.php          ← ignorado por Git
│   ├── urls.php            ← URLs de CDN (Leaflet, Google Fonts)
│   └── paths.php           ← Rutas de carpetas (logs, uploads)
├── app/
│   ├── core/               ← Router, Database, Session, Csrf, Auth, View, Logger, helpers
│   ├── controllers/        ← Mapa, Mercado, Producto, Vivero, Cuenta, Auth, Admin, Registro
│   ├── models/             ← Usuario, Vivero, Inventario, Producto, Contacto, Reporte, catálogos
│   └── views/              ← layout + plantillas por sección
├── logs/                   ← error.log, info.log, audit.log (generados en runtime)
└── public/
    ├── index.php           ← único punto de entrada
    ├── .htaccess           ← reescritura de rutas
    ├── css/app.css
    ├── js/                 ← mapa-edicion.js, formularios.js
    └── uploads/
        ├── viveros/        ← fotos de viveros
        └── productos/      ← fotos de productos
```

## Diseño

Tomar `prototipo.html` como referencia visual (pasar su CSS a `public/css/app.css`):
- Fondo blanco; verde fuerte `#1E6B0F`; encabezado verde oscuro `#123A08`; franja de menú `#1E6B0F`; acentos amarillos `#F2B705` (pestaña activa, botón "Registrarme").
- Tipografías (Google Fonts): **Bricolage Grotesque** (títulos), **Atkinson Hyperlegible** (texto), **IBM Plex Mono** (coordenadas y números).
- Tarjetas blancas con borde suave y sombra ligera. Modo oscuro con `prefers-color-scheme`.
- Tarjeta de producto: foto, título, vivero y comunidad, precio en verde "/ unidad", indicador de entrega.
- Botón de WhatsApp verde `#1F8F4E`.
- Diseño pensado primero para celular.

## Base de datos

`database.sql` (17 tablas, probado en MariaDB 10.11): roles, estados, municipios, comunidades, especies, categorias, unidades, usuarios, compradores, restablecimientos_pin, viveros, inventario, fotos_vivero, productos, fotos_producto, contactos, reportes.
- El municipio y estado de un vivero se obtienen de `comunidades`; el teléfono del vivero es el de su usuario responsable.
- Trae Tabasco con 17 municipios, categorías, unidades, 18 especies y un admin inicial (tel. `9930000000`, PIN temporal `123456`, `debe_cambiar_pin = 1`).
- Si cambia el esquema, actualizar `database.sql` y agregar un archivo de migración en `database/migraciones/AAAA-MM-DD-descripcion.sql`.

## Forma de trabajar

- Editar solo los archivos y líneas necesarias; no regenerar archivos completos sin motivo.
- Validar sintaxis con `php -l` después de cambiar archivos PHP.
- Commits pequeños con mensajes en español que describan el cambio.
- Textos de la interfaz en español de México, claros y cortos.

## Orden de trabajo

1. Core: router, conexión PDO, sesión, CSRF, layout y `app.css` desde el prototipo.
2. Inicio y cierre de sesión, cambio obligatorio de PIN.
3. Registro de comprador y de responsable de vivero.
4. Panel del vivero: datos, ubicación en mapa, inventario, fotos.
5. Mapa público con Leaflet.
6. Mercado, ficha de producto, publicar/editar productos, contacto con registro en `contactos`.
7. Panel de administrador: aprobar viveros, comunidades, restablecer PIN, reportes, bloqueo.
8. Correos de aviso, captcha, consejos de seguridad.

## Ideas futuras (no implementar todavía)

- IA: publicar productos por voz y foto; alertas de riesgo de moniliasis con pronóstico del clima (Open-Meteo); diagnóstico de enfermedades por foto (moniliasis del cacao, enfermedades del maíz); asistente agrícola; resumen mensual para el programa.
- Publicidad para tiendas agrícolas locales: siempre marcada "Patrocinado", nunca dentro de un diagnóstico; "¿Dónde lo consigo?" con tiendas cercanas en el mapa.

## Estado actual (2026-10-11)

### ✅ Completado
- **Pasos 1-2**: Core, router, autenticación, cambio obligatorio de PIN
- **Paso 3**: Registro de comprador y responsable de vivero (completo y probado)
  - Dos flujos de registro (comprador/vivero) con validación completa
  - Preservación de datos de formulario en errores
  - Ubicación GPS con botón "Usar mi ubicación"
  - Filtrado dinámico de comunidades por municipio con JavaScript
  - Estado de vivero auto-establecido como "pendiente"

### ⏳ Pendiente
- **Paso 4**: Panel del vivero (datos, ubicación en mapa, inventario, fotos)
- **Paso 5**: Mapa público con Leaflet, OpenStreetMap, satelital Esri, markercluster
- **Paso 6**: Logging centralizado en `logs/` (estructura documentada en memoria como `paso_6_logging_pendiente.md`)
- **Paso 7**: Panel de administrador (aprobar viveros, gestionar comunidades, restablecer PIN, reportes, bloqueo)
- **Paso 8**: Correos, Cloudflare Turnstile, consejos de seguridad

### 🌳 Rama actual
- **Rama**: `delelop`
- Último commit: Paso 3 completado y probado
