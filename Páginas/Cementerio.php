<?php
/**
 * Generador y ejecutor de cementerios para AzerothCore
 * Tablas: game_graveyard + graveyard_zone
 * Incluye guía DBC integrada
 * Requiere PHP 8+ con extensión pdo_mysql
 */

// ============================================================
// CONFIGURACIÓN DE LA BASE DE DATOS
// ============================================================
const DB_HOST  = '127.0.0.1';
const DB_PORT  = 3306;
const DB_USER  = 'root';
const DB_PASS  = 'admin';
const DB_WORLD = 'acore_world';

// ============================================================
// LÓGICA
// ============================================================
$errors      = [];
$success     = false;
$deleted     = false;
$deletedInfo = '';
$action      = $_POST['action'] ?? 'preview';

$data = [
    'gy_id'        => 900,
    'gy_map'       => 900,
    'gy_x'         => 0.0,
    'gy_y'         => 0.0,
    'gy_z'         => 0.0,
    'gy_comment'   => 'Cementerio Mapa Custom',
    'gyz_zone'     => 900,
    'gyz_faction'  => 0,
    'gyz_comment'  => 'Zona Mapa Custom',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['gy_id']       = filter_input(INPUT_POST, 'gy_id', FILTER_VALIDATE_INT);
    $data['gy_map']      = filter_input(INPUT_POST, 'gy_map', FILTER_VALIDATE_INT);
    $data['gy_x']        = filter_input(INPUT_POST, 'gy_x', FILTER_VALIDATE_FLOAT);
    $data['gy_y']        = filter_input(INPUT_POST, 'gy_y', FILTER_VALIDATE_FLOAT);
    $data['gy_z']        = filter_input(INPUT_POST, 'gy_z', FILTER_VALIDATE_FLOAT);
    $data['gy_comment']  = trim($_POST['gy_comment'] ?? '');
    $data['gyz_zone']    = filter_input(INPUT_POST, 'gyz_zone', FILTER_VALIDATE_INT);
    $data['gyz_faction'] = filter_input(INPUT_POST, 'gyz_faction', FILTER_VALIDATE_INT);
    $data['gyz_comment'] = trim($_POST['gyz_comment'] ?? '');

    if ($data['gy_id'] === false || $data['gy_id'] <= 0) {
        $errors[] = 'El Graveyard ID debe ser un entero positivo.';
    }

    // -------------------- INSERTAR --------------------
    if (empty($errors) && $action === 'execute') {
        if ($data['gy_map'] === false || $data['gy_map'] <= 0) {
            $errors[] = 'El Map ID debe ser un entero positivo.';
        }
        if ($data['gy_x'] === false || $data['gy_y'] === false || $data['gy_z'] === false) {
            $errors[] = 'Las coordenadas X, Y, Z deben ser números decimales.';
        }
        if ($data['gyz_zone'] === false || $data['gyz_zone'] <= 0) {
            $errors[] = 'El GhostZone (Area ID) debe ser un entero positivo.';
        }
        if (!in_array($data['gyz_faction'], [0, 469, 67], true)) {
            $errors[] = 'Facción inválida (0, 469 o 67).';
        }

        if (empty($errors)) {
            try {
                $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_WORLD . ';charset=utf8mb4';
                $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE          => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);

                $pdo->beginTransaction();

                $pdo->prepare('DELETE FROM `game_graveyard` WHERE `ID` = ?')
                    ->execute([$data['gy_id']]);
                $pdo->prepare('DELETE FROM `graveyard_zone` WHERE `ID` = ? AND `GhostZone` = ?')
                    ->execute([$data['gy_id'], $data['gyz_zone']]);

                $pdo->prepare(
                    'INSERT INTO `game_graveyard` (`ID`, `Map`, `x`, `y`, `z`, `Comment`) VALUES (?, ?, ?, ?, ?, ?)'
                )->execute([
                    $data['gy_id'], $data['gy_map'],
                    $data['gy_x'], $data['gy_y'], $data['gy_z'],
                    $data['gy_comment'],
                ]);

                $pdo->prepare(
                    'INSERT INTO `graveyard_zone` (`ID`, `GhostZone`, `Faction`, `Comment`) VALUES (?, ?, ?, ?)'
                )->execute([
                    $data['gy_id'], $data['gyz_zone'],
                    $data['gyz_faction'], $data['gyz_comment'],
                ]);

                $pdo->commit();
                $success = true;
            } catch (PDOException $e) {
                if (isset($pdo) && $pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $errors[] = 'Error de base de datos: ' . $e->getMessage();
            }
        }
    }

    // -------------------- ELIMINAR --------------------
    if (empty($errors) && $action === 'delete') {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_WORLD . ';charset=utf8mb4';
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE          => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);

            $pdo->beginTransaction();

            $del1 = $pdo->prepare('DELETE FROM `game_graveyard` WHERE `ID` = ?');
            $del1->execute([$data['gy_id']]);
            $rowsGraveyard = $del1->rowCount();

            $del2 = $pdo->prepare('DELETE FROM `graveyard_zone` WHERE `ID` = ?');
            $del2->execute([$data['gy_id']]);
            $rowsZone = $del2->rowCount();

            $pdo->commit();

            if ($rowsGraveyard === 0 && $rowsZone === 0) {
                $errors[] = 'No se encontró ningún cementerio con ID ' . $data['gy_id'] . '.';
            } else {
                $deleted = true;
                $deletedInfo = "Se eliminaron $rowsGraveyard fila(s) de game_graveyard y $rowsZone fila(s) de graveyard_zone.";
            }
        } catch (PDOException $e) {
            if (isset($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $errors[] = 'Error de base de datos: ' . $e->getMessage();
        }
    }
}

// ============================================================
// SQL DE PREVISUALIZACIÓN
// ============================================================
$esc = fn($v) => "'" . str_replace("'", "''", (string)$v) . "'";
$sqlPreview = sprintf(
    "USE `%s`;\n\n" .
    "-- 1. Punto físico del cementerio\n" .
    "DELETE FROM `game_graveyard` WHERE `ID` = %d;\n" .
    "INSERT INTO `game_graveyard` (`ID`, `Map`, `x`, `y`, `z`, `Comment`) VALUES\n" .
    "(%d, %d, %s, %s, %s, %s);\n\n" .
    "-- 2. Enlace con la zona\n" .
    "DELETE FROM `graveyard_zone` WHERE `ID` = %d AND `GhostZone` = %d;\n" .
    "INSERT INTO `graveyard_zone` (`ID`, `GhostZone`, `Faction`, `Comment`) VALUES\n" .
    "(%d, %d, %d, %s);\n\n" .
    "-- 3. Eliminar cementerio (si algo salió mal)\n" .
    "DELETE FROM `game_graveyard` WHERE `ID` = %d;\n" .
    "DELETE FROM `graveyard_zone` WHERE `ID` = %d;",
    DB_WORLD,
    $data['gy_id'],
    $data['gy_id'], $data['gy_map'], $data['gy_x'], $data['gy_y'], $data['gy_z'], $esc($data['gy_comment']),
    $data['gy_id'], $data['gyz_zone'],
    $data['gy_id'], $data['gyz_zone'], $data['gyz_faction'], $esc($data['gyz_comment']),
    $data['gy_id'],
    $data['gy_id']
);

$h = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Generador PHP + Guía DBC - Cementerio (AzerothCore)</title>
<style>
:root {
    --bg: #0d1117; --card: #161b22; --border: #30363d;
    --gold: #d4a017; --gold-light: #f0c040;
    --text: #c9d1d9; --text-dim: #8b949e;
    --success: #3fb950; --danger: #f85149; --warn: #f0a020;
}
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: 'Segoe UI', Tahoma, sans-serif;
    background: var(--bg); color: var(--text);
    line-height: 1.6; padding: 20px;
}
header {
    text-align: center; padding: 30px 20px; margin: 0 auto 20px auto;
    border-bottom: 2px solid var(--gold);
    background: linear-gradient(180deg, rgba(212,160,23,0.1), transparent);
    border-radius: 8px 8px 0 0; max-width: 950px;
}
header h1 { color: var(--gold-light); font-size: 1.8em; text-shadow: 0 0 20px rgba(212,160,23,0.4); }
header p  { color: var(--text-dim); margin-top: 8px; font-size: 0.9em; }
main { max-width: 950px; margin: 0 auto; display: grid; gap: 20px; }

.card { background: var(--card); border: 1px solid var(--border); border-radius: 8px; padding: 20px; }
.card h2 {
    color: var(--gold); font-size: 1.1em;
    margin-bottom: 15px; padding-bottom: 10px;
    border-bottom: 1px solid var(--border);
}

.field { margin-bottom: 12px; }
.field label { display: block; font-size: 0.85em; color: var(--text-dim); margin-bottom: 4px; }
.field label strong { color: var(--gold-light); }
.field input, .field select {
    width: 100%; padding: 8px 10px; background: var(--bg);
    border: 1px solid var(--border); border-radius: 4px;
    color: var(--text); font-family: inherit; font-size: 0.9em;
}
.field input:focus, .field select:focus {
    outline: none; border-color: var(--gold);
    box-shadow: 0 0 0 2px rgba(212,160,23,0.2);
}
.row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; }

.hint { font-size: 0.78em; color: var(--warn); margin-top: 8px; font-style: italic; }

.btn-group { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 15pxpx;;
 }
.btn {
    padding: 10px 18px;    background: var(--gold); color: #000 padding;
    border: none; border-radius: 4: px; cursor: pointer;
    font-weight: 600; font-size: 0.95em; transition: all 0.15s;
}
.btn:hover { background: var(--gold-light); }
.btn.exec { background: var(--success); color: #fff; }
.btn.exec:hover { background: #4ac95a; }
.btn.del  { background: var(--danger); color: #fff; }
.btn.del:hover { background: #ff6b62; }
.btn.guide { background: #58a6ff; color: #fff; }
.btn.guide:hover { background: #79b8ff; }

.alert { padding: 12px 16px; border-radius: 4px; margin-bottom: 15px; font-size: 0.9em; }
.alert-success { background: rgba(63,185,80,0.15); border-left: 4px solid var(--success); color: var(--success); }
.alert-danger  { background: rgba(248,81,73,0.15); border-left: 4px solid var(--danger);  color: var(--danger); }
.alert ul { margin-left: 20px; }

pre#output {
    background: #010409; border: 1px solid var(--border); border-radius: 415px; font-family: 'Consolas', 'Courier New', monospace;
    font-size: 0.85em; white-space: pre-wrap; word-break: break-word;
    color: #79c0ff; line-height: 1.5;
}

/* ============ MODAL GUÍA DBC ============ */
.modal-overlay {
    display: none; position: fixed; inset: 0; z-index: 1000;
    background: rgba(0,0,0,0.85);
    align-items: center; justify-content: center; padding: 20px;
}
.modal-overlay.active { display: flex; }
.modal-content {
    background: var(--card); border: 2px solid var(--gold);
    border-radius: 8px; max-width: 850px; width: 100%;
    max-height: 90vh; overflow-y: auto; padding: 25px;
    position: relative; box-shadow: 0 0 40px rgba(212,160,23,0.3);
}
.modal-close {
    position: absolute; top: 12px; right: 15px;
    background: var(--danger); color: #fff; border: none;
    border-radius: 4px; padding: 5px 12px; cursor: pointer;
    font-weight: 700; font-size: 0.9em;
}
.modal-close:hover { background: #ff6b62; }
.modal-content h2 { color: var(--gold-light); margin-bottom: 15px; border-bottom: 1px solid var(--border); padding-bottom: 10px; }
.modal-content h3 { color: var(--gold); margin-top: 20px; margin-bottom: 8px; font-size: 1em; }
.modal-content p, .modal-content li { font-size: 0.9em; margin-bottom: 8px; }
.modal-content ul, .modal-content ol { margin-left: 20px; margin-bottom: 12px; }
.modal-content code {
    background: #010409; padding: 2px 6px; border-radius: 3px;
    font-family: 'Consolas', monospace; color: #79c0ff; font-size: 0.85em;
}
.modal-content .notice-small {
    background: rgba(240,160,32,0.1); border-left: 3px solid var(--warn);
    padding: 10px 14px; font-size: 0.85em; color: var(--warn); margin: 12px 0;
    border-radius: 0 4px 4px 0;
}
</style>
</head>
<body>

<header>
    <h1>⚰️ Generador PHP - Cementerio</h1>
   ($ <p><code>game_graveyard</code> + <code>graveyarderrors_zone</code> · AzerothCore WotLK</p>
</header>

<main>

   )) <?php if ($success): ?>
        <div class="alert alert:-success">
            ✅ Cementerio ?>
        <strong>#<?= $h($data['gy_id']) ?></strong> insertado correctamente en <strong><?= $h(DB_WORLD) ?></strong>.
        </div>
    <?php endif; ?>

    <?php if ($deleted): ?>
        <div class="alert alert-success">
            🗑️ Cementerio <strong>#<?= $h($data['gy_id']) ?></strong> eliminado. <?= $h($deletedInfo) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty <div class="alert alert-danger">
            <strong>Errores:</strong>
            <ul><?php foreach ($errors as $e): ?><li><?= $h($e) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <form method="post" class="card">
        <h2>1. Punto físico (<code>game_graveyard</code>)</h2>
        <div class="row">
            <div class="field"><label><strong>Graveyard ID</strong></label><input type="number" name="gy_id" value="<?= $h($data['gy_id']) ?>" required></div>
            <div class="field"><label><strong>Map ID</strong></label><input type="number" name="gy_map" value="<?= $h($data['gy_map']) ?>" required></div>
        </div>
        <div class="row-3">
            <div class="field"><label>X</label><input type="text" name="gy_x" value="<?= $h($data['gy_x']) ?>" required></div>
            <div class="field"><label>Y</label><input type="text" name="gy_y" value="<?= $h($data['gy_y']) ?>" required></div>
            <div class="field"><label>Z</label><input type="text" name="gy_z" value="<?= $h($data['gy_z']) ?>" required></div>
        </div>
        <div class="field"><label>Comment</label><input type="text" name="gy_comment" value="<?= $h($data['gy_comment']) ?>"></div>

        <h2 style="margin-top:20px;">2. Enlace con la zona (<code>graveyard_zone</code>)</h2>
        <div class="row">
            <div class="field"><label><strong>GhostZone</strong> (Area ID)</label><input type="number" name="gyz_zone" value="<?= $h($data['gyz_zone']) ?>" required></div>
            <div class="field">
                <label>Facción</label>
                <select name="gyz_faction">
                    <option value="0"   <?= $data['gyz_faction'] == 0   ? 'selected' : '' ?>>0 - Ambas</option>
                    <option value="469" <?= $data['gyz_faction'] == 469 ? 'selected' : '' ?>>469 - Alianza</option>
                    <option value="67"  <?= $data['gyz_faction'] == 67  ? 'selected' : '' ?>>67 - Horda</option>
                </select>
            </div>
        </div>
        <div class="field"><label>Comment</label><input type="text" name="gyz_comment" value="<?= $h($data['gyz_comment']) ?>"></div>

        <p class="hint">⚠️ El Graveyard ID debe existir en WorldSafeLocs.dbc y el GhostZone en AreaTable.dbc.</p>

        <div class="btn-group">
            <button type="submit" name="action" value="preview" class="btn">👁️ Previsualizar SQL</button>
            <button type="submit" name="action" value="execute" class="btn exec" onclick="return confirm('¿Ejecutar el SQL en la base de datos <?= DB_WORLD ?>?');">⚡ Ejecutar en BD</button>
            <button type="submit" name="action" value="delete" class="btn del" onclick="return confirm('⚠️ ¿Eliminar el cementerio con ID <?= $data['gy_id'] ?> de la base de datos? Esta acción no se puede deshacer.');">🗑️ Eliminar de BD</button>
            <button type="button" class="btn guide" onclick="openGuide()">📖 Guía DBC</button>
        </div>
    </form>

    <section class="card">
        <h2>📜 SQL Generado</h2>
        <pre id="output"><?= $h($sqlPreview) ?></pre>
    </section>

</main>

<!-- ===================== MODAL GUÍA DBC ===================== -->
<div class="modal-overlay" id="guideModal">
    <div class="modal-content">
        <button class="modal-close" onclick="closeGuide()">✕ Cerrar</button>

        <h2>📖 Guía completa: DBC necesarios para un mapa custom</h2>
        <p>Esta guía explica paso a paso cómo editar los archivos DBC para que tu mapa personalizado y su cementerio funcionen en el cliente y en el servidor de AzerothCore.</p>

        <h3>🛠️ Herramientas necesarias</h3>
        <ul>
            <li><strong>MyDBCEditor</strong>: para abrir, editar y guardar los archivos <code>.dbc</code>.</li>
            <li><strong>Noggit</strong>: editor 3D de terreno para crear el mapa (<code>.adt</code>, <code>.wmo</code>).</li>
            <li><strong>MPQ Editor</strong>: para empaquetar los DBC y el terreno en un parche <code>.MPQ</code> para el cliente.</li>
            <li><strong>HeidiSQL / MySQL Workbench</strong>: para ejecutar el SQL generado en <code>acore_world</code>.</li>
        </ul>

        <h3>📂 Los 4 DBC imprescindibles</h3>
        <p>Debes editar estos archivos y copiarlos <strong>tanto en el servidor</strong> (carpeta <code>data/dbc</code>) <strong>como en el cliente</strong> (dentro de un parche MPQ).</p>

        <h3>1️⃣ Map.dbc (registro del mapa)</h3>
        <ul>
            <li><strong>Columna 1 (<code>ID</code>)</strong>: el ID de tu mapa. Debe ser único y coincidir con el <code>Map ID</code> que usas en el generador.</li>
            <li><strong>Columna 2 (<code>InternalName</code>)</strong>: nombre interno, por ejemplo <code>MiMapaCustom</code>.</li>
            <li><strong>Columna 4 (<code>Type</code>)</strong>: tipo de mapa (<code>0</code> = mundo abierto, <code>1</code> = dungeon, <code>2</code> = raid, etc.).</li>
            <li><strong>Columna 23 (<code>AreaTableID</code>)</strong>: aquí pondrás el <code>Area ID</code> que crees en el <code>AreaTable.dbc</code>.</li>
        </ul>

        <h3>2️⃣ AreaTable.dbc (la zona del mapa)</h3>
        <ul>
            <li><strong>Columna 1 (<code>ID</code>)</strong>: el <code>Area ID</code> único de tu zona. Este valor es el <code>GhostZone</code> que usas en el generador.</li>
            <li><strong>Columna 2 (<code>MapID</code>)</strong>: el ID de tu mapa, el mismo de la columna 1 del <code>Map.dbc</code>.</li>
            <li><strong>Columna 5 (<code>Flags</code>)</strong>: aquí defines el comportamiento de la zona. Los valores más útiles son:
                <ul>
                    <li><code>0x00000040</code> → <code>AREA_FLAG_ALLOW_DUELS</code> (permite duelos).</li>
                    <li><code>0x00000800</code> → <code>AREA_FLAG_SANCTUARY</code> (santuario, PvP desactivado).</li>
                </ul>
            </li>
            <li><strong>Columnas 12-28</strong>: aquí escribes el nombre visible de la zona, por ejemplo <code>Mi Zona Custom</code>.</li>
        </ul>

        <h3>3️⃣ WorldSafeLocs.dbc (el cementerio)</h3>
        <ul>
            <li><strong>Columna 1 (<code>ID</code>)</strong>: el <code>Graveyard ID</code>. Debe ser único y coincidir con el que pones en el generador.</li>
            <li><strong>Columna 2 (<code>MapID</code>)</strong>: el ID de tu mapa, el mismo de <code>Map.dbc</code>.</li>
            <li><strong>Columnas 3, 4 y 5 (<code>X</code>, <code>Y</code>, <code>Z</code>)</strong>: las coordenadas donde reaparecerá el fantasma. Puedes obtenerlas dentro del juego con el comando <code>.gps</code>.</li>
            <li><strong>Columna 7</strong>: el nombre del cementerio, por ejemplo <code>Cementerio Mapa Custom</code>.</li>
        </ul>

        <h3>4️⃣ MapDifficulty.dbc (evita crashes en grupo)</h3>
        <ul>
            <li>Añade una nueva fila con tu <strong>Map ID</strong> en la segunda columna. Si no lo haces, invitar a otro jugador al mapa puede tirar el servidor.</li>
        </ul>

        <h3>✅ Orden correcto de los pasos</h3>
        <ol>
            <li>Edita <code>AreaTable.dbc</code> y crea tu zona (ID y nombre).</li>
            <li>Edita <code>Map.dbc</code> y en la columna 23 pon el <code>Area ID</code> que acabas de crear.</li>
            <li>Edita <code>MapDifficulty.dbc</code> y añade tu <code>Map ID</code>.</li>
            <li>Edita <code>WorldSafeLocs.dbc</code> y crea el cementerio con el <code>Graveyard ID</code> y el <code>Map ID</code> correctos.</li>
            <li>Empaqueta los DBC editados en un parche <code>.MPQ</code> para el cliente y cópialos también en la carpeta <code>data/dbc</code> del servidor.</li>
            <li>Usa esta página para generar el SQL y ejecutarlo en <code>acore_world</code>.</li>
            <li>Entra al juego, ve a tu mapa y ejecuta <code>.linkgrave <em>TuGraveyardID</em></code> para vincular el cementerio.</li>
            <li>No olvides spawnear un <strong>Spirit Healer</strong> (ID <code>6491</code>) en el cementerio para que los jugadores puedan resucitar.</li>
        </ol>

        <div class="notice-small">
            ⚠️ <strong>Importante:</strong> si tu cementerio no funciona, revisa que hayas extraído los <code>maps</code> del cliente y que el <code>AreaTable.dbc</code> del servidor también esté actualizado.
        </div>
    </div>
</div>

<script>
function openGuide() {
    document.getElementById('guideModal').classList.add('active');
}
function closeGuide() {
    document.getElementById('guideModal').classList.remove('active');
}
document.getElementById('guideModal').addEventListener('click', function(e) {
    if (e.target === this) closeGuide();
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeGuide();
});
</script>

</body>
</html>
