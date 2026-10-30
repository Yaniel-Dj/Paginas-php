# 🐉 Mis páginas PHP para AzerothCore

![PHP](https://img.shields.io/badge/PHP-8.x-blue)
![AzerothCore](https://img.shields.io/badge/AzerothCore-3.3.5a-green)
![Laragon](https://img.shields.io/badge/Laragon-6.x-purple)
![License](https://img.shields.io/badge/license-GPL--3.0-blue)
![Sin frameworks](https://img.shields.io/badge/frameworks-ninguno-lightgrey)

Repositorio personal donde voy subiendo **mis aportes en PHP puro** para servidores **AzerothCore** (World of Warcraft 3.3.5a).

Aquí no hay frameworks, ni Composer, ni Laravel, ni Symfony. Solo **archivos `.php`** que puedes copiar a tu web, configurar la conexión a la base de datos y usar. Cada página es independiente y funciona por sí sola.

---

## 📋 Índice

- [¿Qué es esto?](#-qué-es-esto)
- [Filosofía del repositorio](#-filosofía-del-repositorio)
- [Requisitos](#-requisitos)
- [Mi entorno de desarrollo: Laragon](#-mi-entorno-de-desarrollo-laragon)
- [Cómo usar estas páginas](#-cómo-usar-estas-páginas)
- [Páginas que voy aportando](#-páginas-que-voy-aportando)
- [Estructura del repositorio](#-estructura-del-repositorio)
- [Configuración de la base de datos](#-configuración-de-la-base-de-datos)
- [Notas y avisos](#-notas-y-avisos)
- [Licencia](#-licencia)

---

## 🧭 ¿Qué es esto?

Soy un desarrollador aficionado que va creando **páginas PHP sueltas** para servidores AzerothCore y las comparte aquí por si le sirven a alguien más.

Cada archivo `.php` que subo:

- ✅ Está escrito en **PHP puro** (sin frameworks, sin dependencias externas).
- ✅ Se puede **copiar y pegar** en cualquier hosting con PHP + MySQL.
- ✅ Incluye su **configuración de base de datos** de forma clara al principio del archivo o en un `config.php` común.
- ✅ Tiene **comentarios en español** explicando lo importante.
- ✅ Es **independiente**: no necesita las demás páginas para funcionar (salvo el `config.php`).

> ⚠️ No esperes un proyecto "profesional" con estructura MVC, tests ni Composer. Es mi cajón de aportes PHP, hecho con cariño y ganas de ayudar. 😄

---

## 💡 Filosofía del repositorio

- **PHP puro**: nada de `composer install`, nada de `npm install`. Copias el archivo, lo configuras y funciona.
- **Cada página = un archivo**: fácil de leer, fácil de modificar, fácil de borrar.
- **Código legible**: antes que "bonito", que se entienda.
- **Sin dependencias raras**: solo lo que trae PHP y MySQL de serie.
- **Compartir es la idea**: si algo te sirve, úsalo. Si lo mejoras, compártelo.

---

## ⚙️ Requisitos

Para que estas páginas funcionen necesitas:

- **PHP 8.x** (funcionan también en 7.4, pero desarrollo en 8.x)
- **MySQL / MariaDB** con las bases de AzerothCore:
  - `acore_auth`
  - `acore_characters`
  - `acore_world`
- Un servidor web con **PHP** (Apache, Nginx, o el que uses)
- Un servidor **AzerothCore** funcionando

### Extensiones PHP que uso

Estas son las que normalmente necesitan mis páginas:

| Extensión | Para qué la uso |
|-----------|-----------------|
| `mysqli` | Conexión a la base de datos (la más simple) |
| `pdo_mysql` | Alternativa cuando uso PDO en alguna página |
| `openssl` | Registro de cuentas (SRP6) y hashes |
| `mbstring` | Nombres de personajes en UTF-8 |
| `session` | Login y panel de cuenta |
| `json` | Respuestas AJAX (alguna página lo usa) |

En la mayoría de hostings vienen activadas por defecto. Si usas **Laragon**, también.

---

## 💻 Mi entorno de desarrollo: Laragon

Yo desarrollo y pruebo todas las páginas con **Laragon** en Windows. Es cómodo, rápido y no hay que configurar casi nada:

1. Descarga Laragon desde 👉 https://laragon.org/download/
2. Instálalo (versión **Full**, trae PHP, MySQL, Apache, HeidiSQL…).
3. Arranca Laragon (botón **Start All**).
4. Copia la carpeta del proyecto en:
   ```
   C:\laragon\www\azerothcore-php-pages
   ```
5. Laragon te la sirve automáticamente en:
   ```
   http://azerothcore-php-pages.test
   ```
6. Abre **HeidiSQL** o **phpMyAdmin** desde Laragon para gestionar las bases de datos.

### Configuración típica de Laragon

- **Usuario MySQL**: `root`
- **Contraseña**: *(vacía)*
- **Host**: `127.0.0.1`
- **Puerto**: `3306`

> Si en tu servidor real tienes otro usuario/contraseña, solo tienes que cambiar esos valores en el `config.php`.

### Versión de PHP que uso

Trabajo principalmente con **PHP 8.1 / 8.2** en Laragon. Puedes cambiar la versión desde:

> Laragon → menú **PHP** → **Version** → elige la que quieras

---

## 🚀 Cómo usar estas páginas

No hay instalación ni migraciones. Es tan simple como:

1. **Descarga** el archivo `.php` que te interese (o clona el repo entero):
   ```bash
   git clone https://github.com/TU_USUARIO/azerothcore-php-pages.git
   ```

2. **Cópialo** a tu carpeta web (en Laragon → `C:\laragon\www\tu-carpeta`).

3. **Edita la configuración** de la base de datos al principio del archivo (o en `config.php` si la página lo usa).

4. **Ábrelo** en el navegador:
   ```
   http://localhost/tu-carpeta/nombre-de-la-pagina.php
   ```

5. Si sale un error de conexión → revisa usuario/contraseña/host de MySQL.
6. Si sale un error de PHP → revisa que la extensión necesaria esté activada.

> 💡 Cada página suele llevar un comentario arriba indicando qué extensión necesita, por si acaso.

---

## 📄 Páginas que voy aportando

Voy añadiendo poco a poco. Esta tabla la iré actualizando:

| Página | Qué hace | Estado |
|--------|----------|--------|
| `register.php` | Registro de cuenta con SRP6 (formato AzerothCore) | ✅ Subida |
| `login.php` | Login de cuenta con sesión PHP | ✅ Subida |
| `account.php` | Panel básico de cuenta (cambiar pass, ver datos) | ✅ Subida |
| `online.php` | Lista de jugadores conectados | ✅ Subida |
| `ranking.php` | Ranking de jugadores (PvP / PvE / oro) | ✅ Subida |
| `characters.php` | Ver personajes de una cuenta | ⏳ Pendiente |
| `vote.php` | Sistema de votos | ⏳ Pendiente |
| `donate.php` | Página de donaciones | ⏳ Pendiente |

> ⏳ = todavía no la he subido, pero la tengo en mente.
> 🛠️ Si ves algo roto o mejorable, ¡abre un issue o mándame un PR!

---

## 📁 Estructura del repositorio

La estructura es **sencilla a propósito**:

```
/
├── LICENSE                  # Texto completo de la licencia GPL-3.0
├── README.md
├── config.php               # Configuración común (host, user, pass, BDs)
├── register.php             # Registro con SRP6
├── login.php                # Login de cuenta
├── account.php              # Panel de cuenta
├── online.php               # Jugadores online
├── ranking.php              # Rankings
├── ...
```

- Todo vive **en la raíz** (o casi). Sin subcarpetas complicadas.
- Cada página PHP es **un archivo independiente**.
- Solo `config.php` es compartido (para no repetir credenciales en cada archivo).
- Los estilos y scripts van **inline** en cada página cuando hace falta, o en un `style.css` común si alguna página lo necesita.

> No hay `composer.json`, ni `vendor/`, ni `node_modules/`. Y no los habrá. 🙂

---

## 🔧 Configuración de la base de datos

La idea es que **todas las páginas lean de un mismo `config.php`** para no tener que ir archivo por archivo cambiando credenciales.

Ejemplo de `config.php` (muy simple, sin clases ni historias):

```php
<?php
// config.php — Configuración de conexión a AzerothCore

// Datos del servidor MySQL
$db_host = '127.0.0.1';   // En Laragon: 127.0.0.1
$db_user = 'root';        // En Laragon: root
$db_pass = '';            // En Laragon: vacío

// Nombres de las bases de datos
$db_auth  = 'acore_auth';
$db_chars = 'acore_characters';
$db_world = 'acore_world';

// Conexión a auth (registro, login, cuentas)
$conn_auth = new mysqli($db_host, $db_user, $db_pass, $db_auth);
if ($conn_auth->connect_error) {
    die('Error conectando a acore_auth: ' . $conn_auth->connect_error);
}
$conn_auth->set_charset('utf8mb4');

// Conexión a characters (personajes, online, ranking)
$conn_chars = new mysqli($db_host, $db_user, $db_pass, $db_chars);
if ($conn_chars->connect_error) {
    die('Error conectando a acore_characters: ' . $conn_chars->connect_error);
}
$conn_chars->set_charset('utf8mb4');

// Conexión a world (items, criaturas, etc.)
$conn_world = new mysqli($db_host, $db_user, $db_pass, $db_world);
if ($conn_world->connect_error) {
    die('Error conectando a acore_world: ' . $conn_world->connect_error);
}
$conn_world->set_charset('utf8mb4');
```

Y en cada página:

```php
<?php
require 'config.php';

// ... aquí el código de la página usando $conn_auth, $conn_chars o $conn_world
```

Así, si cambias de servidor, **solo editas `config.php`** y listo.

### Nota sobre credenciales

En este repo subo `config.php` con valores de ejemplo (los de Laragon). Si lo usas en producción:

- 🔐 Cambia usuario y contraseña por los reales de tu servidor.
- 🔐 No uses `root` en producción. Crea un usuario específico con permisos solo de lo que necesites.
- 🔐 Si vas a subir tu propio fork con tus credenciales, **no commitees `config.php` con datos reales**.

---

## 📌 Notas y avisos

- **PHP puro, sin dependencias**: si algo no funciona, normalmente es por la versión de PHP o por una extensión no activada.
- **Código orientativo**: son mis aportes, no un producto terminado. Léelo antes de usarlo en producción.
- **Los datos de ejemplo son de Laragon**: `root` sin contraseña. Cámbialos en tu servidor.
- **Puedo tardar en actualizar**: esto es un hobby, no un trabajo. 😅
- **Sugerencias bienvenidas**: si tienes una idea de página PHP que te gustaría tener para AzerothCore, dímelo y lo mismo la hago.

---

## 📜 Licencia

Este repositorio está bajo la **GNU General Public License v3.0 (GPL-3.0)**.

Esto significa que puedes:

- ✅ **Usar** estas páginas en tu servidor AzerothCore (público o privado).
- ✅ **Modificar** el código a tu gusto.
- ✅ **Redistribuir** copias originales o modificadas.

Siempre que:

- 📌 **Mantengas la misma licencia GPL-3.0** en las versiones modificadas.
- 📌 **Compartas el código fuente** si redistribuyes.
- 📌 **Mantengas el aviso de copyright** original.
- 📌 **No me hagas responsable** si algo se rompe (la licencia incluye cláusula de "sin garantía").

> 🧠 **En cristiano**: usa, modifica y comparte estas páginas libremente. Pero si las mejoras y las publicas, hazlo también bajo GPL-3.0 para que todos podamos beneficiarnos. Es el mismo espíritu que usa [AzerothCore](https://github.com/azerothcore/azerothcore-wotlk) con su licencia AGPL.

Consulta el archivo [LICENSE](LICENSE) para el texto legal completo.

---

## ⭐ Créditos

- [AzerothCore](https://www.azerothcore.org/) — El emulador de WoW 3.3.5a
- [Laragon](https://laragon.org/) — Mi entorno de desarrollo PHP
- La comunidad de servidores privados de WoW, que comparte sin pedir nada a cambio

---

> 💡 **Recordatorio personal**: si vas a probar esto en un servidor en producción, **haz backup de la base de datos** antes de tocar nada. 🙂
