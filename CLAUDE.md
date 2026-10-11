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
- **MySQL/MariaDB** con **PDO**, consultas preparadas siempre, `utf8mb4`.
- **Frontend: HTML5 + CSS propio + JavaScript sin framework.** Alpine.js permitido solo si simplifica formularios dinámicos. No usar React, Vue, Bootstrap ni Tailwind.
- Tokens **CSRF** en todos los formularios `POST`. Escapar toda salida con `htmlspecialchars`.
- **Fotos**: validar tipo real (finfo), máximo 5 MB, redimensionar a 1200 px de lado mayor y guardar como JPEG/WebP con nombre aleatorio en `public/uploads/viveros/` o `public/uploads/productos/`. En la base solo se guarda el nombre del archivo.
- Más adelante: PWA (manifest + service worker) para instalar en el celular.

### Entornos
- **Local**: XAMPP en Windows, proyecto en `C:\xampp\htdocs\red-viveros`. Base `red_viveros` en phpMyAdmin (`http://localhost/phpmyadmin`).
- **Producción**: GoDaddy Web Hosting Económico (cPanel, Linux). Solo `public/` debe quedar expuesto; `app/` y `config/` fuera de la raíz web o protegidos con `.htaccess`.
- `config/config.php` **no se sube a Git**; existe `config/config.example.php` como plantilla.

### Estructura
```
red-viveros/
├── CLAUDE.md
├── database.sql
├── prototipo.html          ← diseño de referencia (no se usa en producción)
├── .gitignore
├── config/
│   ├── config.example.php
│   └── config.php          ← ignorado por Git
├── app/
│   ├── core/               ← Router, Database (PDO), Session, Csrf, Auth, View, helpers
│   ├── controllers/        ← Mapa, Mercado, Producto, Vivero, Cuenta, Auth, Admin
│   ├── models/             ← Usuario, Vivero, Inventario, Producto, Contacto, Reporte, catálogos
│   └── views/              ← layout + plantillas por sección
└── public/
    ├── index.php           ← único punto de entrada
    ├── .htaccess           ← reescritura de rutas
    ├── css/app.css
    ├── js/                 ← mapa.js, mercado.js, formularios.js
    └── uploads/
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

## Buenas prácticas de PHP 8.1+

### Seguridad (OWASP Top 10)
1. **SQL Injection**: usar siempre consultas preparadas con PDO (`:param` o `?`), nunca concatenación de strings.
2. **XSS**: escapar toda salida HTML con `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')` o `htmlentities()`.
3. **CSRF**: incluir token en todos los formularios `POST/PUT/DELETE`; validar con `Csrf::verify()`.
4. **Validación de entrada**: validar tipo, rango, formato en el servidor (no confiar en cliente). Ejemplos:
   - Email: `filter_var($email, FILTER_VALIDATE_EMAIL)`
   - Teléfono: 10 dígitos exactos, `ctype_digit()`
   - Números: `is_numeric()` o `ctype_digit()`
   - URLs: `filter_var($url, FILTER_VALIDATE_URL)`
5. **Hashing de contraseñas**: usar `password_hash($pin, PASSWORD_BCRYPT)` e `password_verify()`, nunca MD5 ni SHA1.
6. **Archivos subidos**: validar tipo real con `finfo_file()`, no confiar en extensión ni `$_FILES['file']['type']`.

### Estilo de código
- PSR-12: indentación 4 espacios, máximo 120 caracteres por línea.
- Nombres: `camelCase` para funciones/variables, `PascalCase` para clases, `UPPER_CASE` para constantes.
- Type hints obligatorios en todas las funciones: `function save(User $user): bool` o `function getId(): ?int`.
- Visibilidad explícita: `private`, `protected`, `public` en propiedades y métodos.
- Variables de instancia en `__construct()`, nunca declarar propiedades sin inicializar.

### Estructura de archivos
- Controllers: reciben `$_GET`, `$_POST`, validan y delegan a Models.
- Models: contienen lógica de negocio y acceso a BD. Métodos estáticos para consultas comunes.
- Views: solo HTML con `<?= htmlspecialchars($var) ?>`, sin lógica.
- Core: clases reutilizables (Router, Database, Session, Csrf, Auth, View).

### Manejo de errores
- En desarrollo (XAMPP): mostrar errores en pantalla (`error_reporting(E_ALL)`, `display_errors = On`).
- En producción: guardar en log y mostrar página genérica al usuario. Usar `set_error_handler()` y `set_exception_handler()`.
- Nunca mostrar paths, stacktraces o SQL en producción.

### Base de datos
- Collation: `utf8mb4_unicode_ci` en tablas y columnas.
- Índices en columnas de búsqueda y foreign keys.
- Transacciones para operaciones relacionadas: `$pdo->beginTransaction()`, `commit()`, `rollBack()`.
- Prepared statements: `$stmt = $pdo->prepare("SELECT * FROM users WHERE phone = ?"); $stmt->execute([$phone]);`

### Sesiones
- `session_start()` al inicio, después de `session_name()` y configuración de cookies.
- Cookie: `HttpOnly = true` (JS no accede), `Secure = true` en producción (HTTPS), `SameSite = Lax` (CSRF).
- `session_regenerate_id(true)` tras login (destruir vieja sesión).
- Guardar datos sensibles en sesión servidor, nunca en cliente.

### Variables de entorno
- Guardar en `config/config.php` (no versionado) o usar variable de entorno.
- Nunca hardcodear DB host, usuario, contraseña, API keys, tokens.
- `config/config.example.php` con valores ficticios como referencia.

### Pruebas y validación
- Verificar con `php -l archivo.php` antes de commit.
- Testear rutas: métodos GET, POST, redirect, status 200/404/403.
- Testear validación: entradas vacías, muy largas, tipos incorrectos.
- Testear seguridad: sin token CSRF, SQL injection, XSS, falta de autenticación.

## Forma de trabajar

- Editar solo los archivos y líneas necesarias; no regenerar archivos completos sin motivo.
- Validar sintaxis con `php -l` después de cambiar archivos PHP.
- Commits pequeños con mensajes en español que describan el cambio.
- Textos de la interfaz en español de México, claros y cortos.
- Seguir las buenas prácticas de seguridad de PHP 8.1+ listadas arriba; revisar el código para cumplir con OWASP Top 10.

## Orden de trabajo

1. Core: router, conexión PDO, sesión, CSRF, layout y `app.css` desde el prototipo.
2. Inicio y cierre de sesión, cambio obligatorio de PIN.
3. Registro de comprador y de responsable de vivero.
4. Panel del vivero: datos, ubicación en mapa, inventario, fotos.
5. Mapa público con Leaflet.
6. Mercado, ficha de producto, publicar/editar productos, contacto con registro en `contactos`.
7. Panel de administrador: aprobar viveros, comunidades, restablecer PIN, reportes, bloqueo.
8. Correos de aviso, captcha, consejos de seguridad.

## Patrones comunes en el proyecto

### Validación de entrada
```php
// Teléfono (10 dígitos)
$phone = trim($_POST['phone'] ?? '');
if (!ctype_digit($phone) || strlen($phone) !== 10) {
    throw new Exception('Teléfono inválido');
}

// Email (opcional pero validado si no vacío)
$email = trim($_POST['email'] ?? '');
if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    throw new Exception('Email inválido');
}

// PIN (6 dígitos)
$pin = trim($_POST['pin'] ?? '');
if (!ctype_digit($pin) || strlen($pin) !== 6) {
    throw new Exception('PIN debe tener 6 dígitos');
}

// Número positivo
$quantity = (int)($_POST['quantity'] ?? 0);
if ($quantity <= 0) {
    throw new Exception('Cantidad debe ser mayor a 0');
}
```

### Hashing y verificación de contraseñas
```php
// Al guardar PIN
$hashedPin = password_hash($pin, PASSWORD_BCRYPT, ['cost' => 12]);

// Al verificar
if (!password_verify($pin, $hashedPin)) {
    throw new Exception('PIN incorrecto');
}
```

### Consultas preparadas (PDO)
```php
// Con nombrados
$stmt = $pdo->prepare('SELECT * FROM usuarios WHERE phone = ?');
$stmt->execute([$phone]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Con placeholders nombrados
$stmt = $pdo->prepare('INSERT INTO usuarios (phone, pin, role) VALUES (:phone, :pin, :role)');
$stmt->execute([':phone' => $phone, ':pin' => $pin, ':role' => 'comprador']);
```

### Escapar HTML en vistas
```php
<!-- En las vistas -->
<h1><?= htmlspecialchars($vivero['nombre'], ENT_QUOTES, 'UTF-8') ?></h1>
<p><?= nl2br(htmlspecialchars($descripcion)) ?></p>

<!-- Atributos HTML -->
<img src="<?= htmlspecialchars($url) ?>" alt="<?= htmlspecialchars($alt) ?>">
```

### Manejo de errores y excepciones
```php
try {
    $vivero = Vivero::findById($id);
    if (!$vivero) {
        http_response_code(404);
        return View::render('404');
    }
    return View::render('vivero/detalle', ['vivero' => $vivero]);
} catch (Exception $e) {
    error_log('Error en viveroDetalle: ' . $e->getMessage());
    http_response_code(500);
    return View::render('500');
}
```

### Token CSRF en formularios
```html
<!-- En vistas -->
<form method="POST" action="/viveros/guardar">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
    <!-- resto del formulario -->
</form>
```

```php
// En controladores
Csrf::verify($_POST['csrf_token'] ?? '');
// Si no es válido, lanza Exception
```

## Ideas futuras (no implementar todavía)

- IA: publicar productos por voz y foto; alertas de riesgo de moniliasis con pronóstico del clima (Open-Meteo); diagnóstico de enfermedades por foto (moniliasis del cacao, enfermedades del maíz); asistente agrícola; resumen mensual para el programa.
- Publicidad para tiendas agrícolas locales: siempre marcada "Patrocinado", nunca dentro de un diagnóstico; "¿Dónde lo consigo?" con tiendas cercanas en el mapa.
