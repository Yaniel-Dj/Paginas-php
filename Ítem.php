<?php
/* ============================================================
   CONFIGURACIÓN DE LA BASE DE DATOS
   ============================================================ */
$DB_HOST = '127.0.0.1';
$DB_PORT = 3306;
$DB_NAME = 'acore_world';
$DB_USER = 'root';
$DB_PASS = 'admin';
$DB_CHARSET = 'utf8mb4';

/* ============================================================
   COLUMNAS DE item_template (AzerothCore 3.3.5a)
   ============================================================ */
$COLUMNS = [
  'entry','class','subclass','SoundOverrideSubclass','name','displayid','Quality','Flags','FlagsExtra',
  'BuyCount','BuyPrice','SellPrice','InventoryType','AllowableClass','AllowableRace','ItemLevel',
  'RequiredLevel','RequiredSkill','RequiredSkillRank','requiredspell','requiredhonorrank',
  'RequiredCityRank','RequiredReputationFaction','RequiredReputationRank','maxcount','stackable',
  'ContainerSlots',
  'stat_type1','stat_value1','stat_type2','stat_value2','stat_type3','stat_value3','stat_type4','stat_value4',
  'stat_type5','stat_value5','stat_type6','stat_value6','stat_type7','stat_value7','stat_type8','stat_value8',
  'stat_type9','stat_value9','stat_type10','stat_value10',
  'ScalingStatDistribution','ScalingStatValue',
  'dmg_min1','dmg_max1','dmg_type1','dmg_min2','dmg_max2','dmg_type2',
  'armor','holy_res','fire_res','nature_res','frost_res','shadow_res','arcane_res',
  'delay','ammo_type','RangedModRange',
  'spellid_1','spelltrigger_1','spellcharges_1','spellppmRate_1','spellcooldown_1','spellcategory_1','spellcategorycooldown_1',
  'spellid_2','spelltrigger_2','spellcharges_2','spellppmRate_2','spellcooldown_2','spellcategory_2','spellcategorycooldown_2',
  'spellid_3','spelltrigger_3','spellcharges_3','spellppmRate_3','spellcooldown_3','spellcategory_3','spellcategorycooldown_3',
  'spellid_4','spelltrigger_4','spellcharges_4','spellppmRate_4','spellcooldown_4','spellcategory_4','spellcategorycooldown_4',
  'spellid_5','spelltrigger_5','spellcharges_5','spellppmRate_5','spellcooldown_5','spellcategory_5','spellcategorycooldown_5',
  'bonding','description','PageText','LanguageID','PageMaterial','startquest','lockid','Material','sheath',
  'RandomProperty','RandomSuffix','block','itemset','MaxDurability','area','Map','BagFamily','TotemCategory',
  'socketColor_1','socketContent_1','socketColor_2','socketContent_2','socketColor_3','socketContent_3',
  'socketBonus','GemProperties','RequiredDisenchantSkill','ArmorDamageModifier','duration',
  'ItemLimitCategory','HolidayId','ScriptName','DisenchantID','FoodType','minMoneyLoot','maxMoneyLoot',
  'flagsCustom','VerifiedBuild'
];

$STRING_COLS = ['name', 'description', 'ScriptName'];

/* ============================================================
   HELPERS
   ============================================================ */
function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

function pv($key, $default = '') {
    return isset($_POST[$key]) ? trim((string)$_POST[$key]) : (string)$default;
}
function pn($key, $default = 0) {
    return (isset($_POST[$key]) && $_POST[$key] !== '') ? (int)$_POST[$key] : $default;
}
function pf($key, $default = 0.0) {
    return (isset($_POST[$key]) && $_POST[$key] !== '') ? (float)$_POST[$key] : $default;
}

function opt($opts, $sel) {
    $out = '';
    foreach ($opts as $o) {
        $out .= '<option value="' . h($o['v']) . '"' . ((string)$o['v'] === (string)$sel ? ' selected' : '') . '>' . h($o['l']) . '</option>';
    }
    return $out;
}

function inputField($name, $type, $label, $help = '', $step = null, $default = '') {
    $stepAttr = $step !== null ? ' step="' . h($step) . '"' : '';
    $val = h(pv($name, $default));
    return '<div class="field">
        <label>' . h($label) . '</label>
        <input type="' . h($type) . '" name="' . h($name) . '" id="' . h($name) . '" value="' . $val . '"' . $stepAttr . '>
        <span class="help">' . h($help) . '</span>
    </div>';
}

function selectField($name, $opts, $label, $help = '', $default = 0) {
    $val = pn($name, $default);
    return '<div class="field">
        <label>' . h($label) . '</label>
        <select name="' . h($name) . '" id="' . h($name) . '">' . opt($opts, $val) . '</select>
        <span class="help">' . h($help) . '</span>
    </div>';
}

function getPdo() {
    global $DB_HOST, $DB_PORT, $DB_NAME, $DB_USER, $DB_PASS, $DB_CHARSET;
    try {
        $dsn = "mysql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_NAME;charset=$DB_CHARSET";
        return new PDO($dsn, $DB_USER, $DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } catch (PDOException $e) {
        return null;
    }
}

function prefix($tipo) {
    return ['weapon' => 'w', 'armor' => 'a', 'mount' => 'm'][$tipo] ?? 'w';
}

function armorMaterial($subclass) {
    return [1 => 7, 2 => 8, 3 => 5, 4 => 6, 5 => 1][$subclass] ?? 8;
}

/* ============================================================
   OPCIONES
   ============================================================ */
$QUALITY_OPTS = [
  ['v'=>0,'l'=>'0 — Pobre (gris)'],['v'=>1,'l'=>'1 — Común (blanco)'],['v'=>2,'l'=>'2 — Poco común (verde)'],
  ['v'=>3,'l'=>'3 — Raro (azul)'],['v'=>4,'l'=>'4 — Épico (morado)'],['v'=>5,'l'=>'5 — Legendario (naranja)'],
  ['v'=>6,'l'=>'6 — Artefacto (rojo)'],['v'=>7,'l'=>'7 — Heredado (oro)']
];
$BONDING_OPTS = [
  ['v'=>0,'l'=>'0 — No ligado'],['v'=>1,'l'=>'1 — Ligado al recoger'],['v'=>2,'l'=>'2 — Ligado al equipar'],
  ['v'=>3,'l'=>'3 — Ligado al usar'],['v'=>4,'l'=>'4 — Ligado a misión']
];
$WEAPON_SUBCLASS = [
  ['v'=>0,'l'=>'0 — Hacha 1 mano'],['v'=>1,'l'=>'1 — Hacha 2 manos'],['v'=>2,'l'=>'2 — Arco'],['v'=>3,'l'=>'3 — Arma de fuego'],
  ['v'=>4,'l'=>'4 — Maza 1 mano'],['v'=>5,'l'=>'5 — Maza 2 manos'],['v'=>6,'l'=>'6 — Polearm'],['v'=>7,'l'=>'7 — Espada 1 mano'],
  ['v'=>8,'l'=>'8 — Espada 2 manos'],['v'=>10,'l'=>'10 — Bastón'],['v'=>13,'l'=>'13 — Arma de puño'],['v'=>14,'l'=>'14 — Daga'],
  ['v'=>15,'l'=>'15 — Ballesta'],['v'=>16,'l'=>'16 — Varita'],['v'=>18,'l'=>'18 — Honda'],['v'=>19,'l'=>'19 — Arma 2 manos genérica']
];
$WEAPON_INV = [
  ['v'=>13,'l'=>'13 — Una mano'],['v'=>17,'l'=>'17 — Dos manos'],['v'=>15,'l'=>'15 — A distancia (arco/ballesta)'],
  ['v'=>26,'l'=>'26 — A distancia derecha (varita/arma fuego)'],['v'=>21,'l'=>'21 — Mano principal (off-hand)'],
  ['v'=>22,'l'=>'22 — Mano izquierda']
];
$DMG_TYPE = [
  ['v'=>0,'l'=>'0 — Físico'],['v'=>1,'l'=>'1 — Sagrado'],['v'=>2,'l'=>'2 — Fuego'],['v'=>3,'l'=>'3 — Naturaleza'],
  ['v'=>4,'l'=>'4 — Escarcha'],['v'=>5,'l'=>'5 — Sombra'],['v'=>6,'l'=>'6 — Arcano']
];
$SHEATH_WEAPON = [
  ['v'=>1,'l'=>'1 — En la espalda (2 manos)'],['v'=>3,'l'=>'3 — En la cintura (1 mano)'],['v'=>2,'l'=>'2 — Al costado']
];
$SHEATH_ARMOR = [['v'=>0,'l'=>'0 — No aplica'],['v'=>4,'l'=>'4 — Escudo en la espalda']];
$ARMOR_SUBCLASS = [
  ['v'=>1,'l'=>'1 — Tela'],['v'=>2,'l'=>'2 — Cuero'],
  ['v'=>3,'l'=>'3 — Malla'],['v'=>4,'l'=>'4 — Placas'],['v'=>5,'l'=>'5 — Escudo']
];
$ARMOR_INV = [
  ['v'=>1,'l'=>'1 — Cabeza'],['v'=>2,'l'=>'2 — Cuello'],['v'=>3,'l'=>'3 — Hombros'],['v'=>16,'l'=>'16 — Espalda'],
  ['v'=>5,'l'=>'5 — Pecho'],['v'=>20,'l'=>'20 — Túnica'],['v'=>9,'l'=>'9 — Muñecas'],['v'=>10,'l'=>'10 — Manos'],
  ['v'=>6,'l'=>'6 — Cintura'],['v'=>7,'l'=>'7 — Piernas'],['v'=>8,'l'=>'8 — Pies'],['v'=>11,'l'=>'11 — Dedo'],
  ['v'=>12,'l'=>'12 — Abalorio'],['v'=>14,'l'=>'14 — Escudo']
];
$SPELL_TRIGGER_MOUNT = [['v'=>0,'l'=>'0 — Al usar (Use)'],['v'=>1,'l'=>'1 — Al equipar (Equip)']];
$SPELL_TRIGGER = [['v'=>0,'l'=>'0 — Al usar'],['v'=>1,'l'=>'1 — Al equipar'],['v'=>2,'l'=>'2 — Al golpear']];
$MATERIAL_MOUNT = [['v'=>8,'l'=>'8 — Cuero (viviente)'],['v'=>1,'l'=>'1 — Metal (mecánica)']];
$REP_RANK = [
  ['v'=>0,'l'=>'0 — Sin requisito'],['v'=>1,'l'=>'1 — Hostil'],['v'=>2,'l'=>'2 — Neutral'],['v'=>3,'l'=>'3 — Amistoso'],
  ['v'=>4,'l'=>'4 — Honorable'],['v'=>5,'l'=>'5 — Venerado'],['v'=>6,'l'=>'6 — Exaltado']
];
$SOCKET_COLORS = [
  ['v'=>0,'l'=>'0 — Ninguno'],['v'=>1,'l'=>'1 — Meta'],['v'=>2,'l'=>'2 — Rojo'],['v'=>4,'l'=>'4 — Amarillo'],['v'=>8,'l'=>'8 — Azul']
];
$STAT_OPTIONS = [
  ['v'=>0,'l'=>'— Ninguno —'],['v'=>3,'l'=>'3 — Agilidad'],['v'=>4,'l'=>'4 — Fuerza'],['v'=>5,'l'=>'5 — Intelecto'],
  ['v'=>6,'l'=>'6 — Espíritu'],['v'=>7,'l'=>'7 — Aguante'],['v'=>12,'l'=>'12 — Defensa'],['v'=>13,'l'=>'13 — Esquiva'],
  ['v'=>14,'l'=>'14 — Parada'],['v'=>15,'l'=>'15 — Bloqueo'],['v'=>16,'l'=>'16 — Golpe melee'],
  ['v'=>17,'l'=>'17 — Golpe a distancia'],['v'=>18,'l'=>'18 — Golpe hechizos'],
  ['v'=>19,'l'=>'19 — Crítico melee'],['v'=>20,'l'=>'20 — Crítico a distancia'],['v'=>21,'l'=>'21 — Crítico hechizos'],
  ['v'=>22,'l'=>'22 — Poder ataque'],['v'=>23,'l'=>'23 — Poder ataque ranged'],['v'=>24,'l'=>'24 — Poder hechizos'],
  ['v'=>25,'l'=>'25 — Penetración hechizos'],['v'=>26,'l'=>'26 — Índice golpe'],['v'=>27,'l'=>'27 — Índice crítico'],
  ['v'=>28,'l'=>'28 — Índice celeridad'],['v'=>29,'l'=>'29 — Golpe melee'],['v'=>30,'l'=>'30 — Celeridad melee'],
  ['v'=>31,'l'=>'31 — Golpe hechizos'],['v'=>32,'l'=>'32 — Crítico hechizos'],['v'=>33,'l'=>'33 — Celeridad hechizos'],
  ['v'=>34,'l'=>'34 — Golpe ranged'],['v'=>35,'l'=>'35 — Resiliencia'],['v'=>36,'l'=>'36 — Poder ataque ranged'],
  ['v'=>37,'l'=>'37 — Poder hechizos arcano'],['v'=>38,'l'=>'38 — Golpe fuego'],['v'=>39,'l'=>'39 — Crítico fuego'],
  ['v'=>40,'l'=>'40 — Celeridad fuego']
];

/* ============================================================
   CONSTRUIR VALORES
   ============================================================ */
function buildValues($tipo) {
    global $COLUMNS;
    $p = prefix($tipo);
    $isMount = $tipo === 'mount';
    $isWeapon = $tipo === 'weapon';
    $isArmor = $tipo === 'armor';
    $gear = !$isMount;

    $v = array_fill_keys($COLUMNS, 0);

    $v['entry'] = pn($p . '_entry', 90000);
    $v['SoundOverrideSubclass'] = -1;
    $v['name'] = pv($p . '_name', 'Nuevo ítem');
    $v['displayid'] = pn($p . '_displayid', 0);
    $v['Quality'] = pn($p . '_quality', 3);
    $v['Flags'] = $isMount ? pn($p . '_flags', 0) : 0;
    $v['FlagsExtra'] = 0;
    $v['BuyCount'] = 1;
    $v['BuyPrice'] = pn($p . '_buyPrice', 0);
    $v['SellPrice'] = pn($p . '_sellPrice', 0);
    $v['AllowableClass'] = pn($p . '_allowableClass', -1);
    $v['AllowableRace'] = pn($p . '_allowableRace', -1);
    $v['ItemLevel'] = pn($p . '_itemLevel', 1);
    $v['RequiredLevel'] = pn($p . '_requiredLevel', 0);
    $v['RequiredSkill'] = $gear ? pn($p . '_requiredSkill', 0) : 0;
    $v['RequiredSkillRank'] = $gear ? pn($p . '_requiredSkillRank', 0) : 0;
    $v['requiredspell'] = pn($p . '_requiredspell', 0);
    $v['RequiredReputationFaction'] = $gear ? pn($p . '_requiredRepFaction', 0) : 0;
    $v['RequiredReputationRank'] = $gear ? pn($p . '_requiredRepRank', 0) : 0;
    $v['maxcount'] = $isMount ? 0 : pn($p . '_maxcount', 0);
    $v['stackable'] = $isMount ? 1 : max(1, pn($p . '_stackable', 1));
    $v['bonding'] = pn($p . '_bonding', 1);
    $v['description'] = pv($p . '_description', '');
    $v['Material'] = 8;
    $v['MaxDurability'] = $gear ? pn($p . '_maxDurability', 0) : 0;

    for ($i = 1; $i <= 5; $i++) {
        $v["spellid_$i"] = 0;
        $v["spelltrigger_$i"] = 0;
        $v["spellcharges_$i"] = 0;
        $v["spellppmRate_$i"] = 0;
        $v["spellcooldown_$i"] = -1;
        $v["spellcategory_$i"] = 0;
        $v["spellcategorycooldown_$i"] = -1;
    }
    for ($i = 1; $i <= 10; $i++) {
        $v["stat_type$i"] = 0;
        $v["stat_value$i"] = 0;
    }

    if ($isWeapon) {
        $v['class'] = 2;
        $v['subclass'] = pn($p . '_subclass', 7);
        $v['InventoryType'] = pn($p . '_inventoryType', 13);
        $v['dmg_min1'] = pf($p . '_dmgMin1', 0);
        $v['dmg_max1'] = pf($p . '_dmgMax1', 0);
        $v['dmg_type1'] = pn($p . '_dmgType1', 0);
        $v['delay'] = pn($p . '_delay', 2000);
        $v['sheath'] = pn($p . '_sheath', 1);
        $v['Material'] = ($v['subclass'] == 10) ? 2 : 1;
    } elseif ($isArmor) {
        $v['class'] = 4;
        $v['subclass'] = pn($p . '_subclass', 2);
        $v['InventoryType'] = pn($p . '_inventoryType', 5);
        $v['armor'] = pn($p . '_armor', 0);
        $v['block'] = pn($p . '_block', 0);
        $v['sheath'] = pn($p . '_sheath', 0);
        $v['Material'] = armorMaterial($v['subclass']);
    } elseif ($isMount) {
        $v['class'] = 15;
        $v['subclass'] = 5;
        $v['InventoryType'] = 0;
        $v['Material'] = pn($p . '_material', 8);
        $v['spellid_1'] = pn($p . '_spellId', 0);
        $v['spelltrigger_1'] = pn($p . '_spellTrigger', 0);
        $v['spellcooldown_1'] = pn($p . '_spellCooldown', -1);
        $v['spellcharges_1'] = 1;
    }

    if ($gear) {
        for ($i = 1; $i <= 10; $i++) {
            $t = pn($p . "_stat_type$i", 0);
            $v["stat_type$i"] = $t;
            $v["stat_value$i"] = $t === 0 ? 0 : pn($p . "_stat_value$i", 0);
        }
        $v['holy_res'] = pn($p . '_holyRes', 0);
        $v['fire_res'] = pn($p . '_fireRes', 0);
        $v['nature_res'] = pn($p . '_natureRes', 0);
        $v['frost_res'] = pn($p . '_frostRes', 0);
        $v['shadow_res'] = pn($p . '_shadowRes', 0);
        $v['arcane_res'] = pn($p . '_arcaneRes', 0);
        $v['socketColor_1'] = pn($p . '_socketColor1', 0);
        $v['socketColor_2'] = pn($p . '_socketColor2', 0);
        $v['socketColor_3'] = pn($p . '_socketColor3', 0);
        $v['itemset'] = pn($p . '_itemset', 0);
        $v['RandomProperty'] = pn($p . '_randomProperty', 0);
        $v['RandomSuffix'] = pn($p . '_randomSuffix', 0);
        $v['minMoneyLoot'] = pn($p . '_minMoneyLoot', 0);
        $v['maxMoneyLoot'] = pn($p . '_maxMoneyLoot', 0);
        $v['ScriptName'] = pv($p . '_scriptName', '');

        $sid = pn($p . '_spellId1', 0);
        if ($sid > 0) {
            $v['spellid_1'] = $sid;
            $v['spelltrigger_1'] = pn($p . '_spellTrigger1', 0);
            $v['spellcharges_1'] = pn($p . '_spellCharges1', 0);
            $v['spellcooldown_1'] = pn($p . '_spellCooldown1', -1);
        }
    }

    return $v;
}

function sqlValue($col, $v) {
    global $STRING_COLS;
    if (in_array($col, $STRING_COLS, true)) {
        return "'" . str_replace("'", "''", (string)$v) . "'";
    }
    return $v;
}

function buildSqlString($values) {
    global $COLUMNS;
    $cols = [];
    $vals = [];
    foreach ($COLUMNS as $c) {
        $cols[] = $c;
        $vals[] = sqlValue($c, $values[$c] ?? 0);
    }
    return "INSERT INTO item_template (\n  " . implode(', ', $cols) . "\n) VALUES (\n  " . implode(', ', $vals) . "\n);";
}

/* ============================================================
   CONSTRUCCIÓN DEL PANEL DE CADA TIPO
   ============================================================ */
function buildPanel($tipo) {
    global $QUALITY_OPTS, $BONDING_OPTS, $WEAPON_SUBCLASS, $WEAPON_INV, $DMG_TYPE, $SHEATH_WEAPON,
           $SHEATH_ARMOR, $ARMOR_SUBCLASS, $ARMOR_INV, $SPELL_TRIGGER_MOUNT, $SPELL_TRIGGER,
           $MATERIAL_MOUNT, $REP_RANK, $SOCKET_COLORS, $STAT_OPTIONS;

    $p = prefix($tipo);
    $isMount = $tipo === 'mount';
    $isWeapon = $tipo === 'weapon';
    $isArmor = $tipo === 'armor';
    $gear = !$isMount;

    $icons = ['weapon'=>'🗡️ ARMA','armor'=>'🛡️ ARMADURA','mount'=>'🐎 MONTURA'];
    $html = '<div class="panel-header">' . $icons[$tipo] . ' — Configura solo los campos de este tipo.</div>';

    /* ---------- 1 · IDENTIFICACIÓN ---------- */
    $html .= '<div class="section">
      <div class="section-title">1 · Identificación y calidad</div>
      <div class="section-desc">Datos visibles en el tooltip del ítem.</div>
      <div class="grid">';
    $html .= inputField($p.'_entry', 'number', 'entry — ID único', 'Usa 90000+ para personalizados.', null, '90000');
    $defName = $isMount ? 'Nueva montura' : ($isWeapon ? 'Nueva arma' : 'Nueva armadura');
    $html .= inputField($p.'_name', 'text', 'name — Nombre', 'Nombre que verá el jugador.', null, $defName);
    $html .= inputField($p.'_displayid', 'number', 'displayid — Modelo', 'Modelo 3D del ítem. 0 = invisible.', null, '0');
    $html .= selectField($p.'_quality', $QUALITY_OPTS, 'Quality — Rareza', 'Color del nombre.', 3);
    $html .= inputField($p.'_itemLevel', 'number', 'ItemLevel', 'Nivel de objeto.', null, $isMount ? '60' : '200');
    $html .= inputField($p.'_requiredLevel', 'number', 'RequiredLevel', 'Nivel mínimo para usarlo.', null, $isMount ? '60' : '80');
    $html .= selectField($p.'_bonding', $BONDING_OPTS, 'bonding — Tipo de ligado', 'Cuándo se liga al jugador.', 1);
    $html .= inputField($p.'_buyPrice', 'number', 'BuyPrice (cobre)', '1 oro = 10000 cobre.', null, '0');
    $html .= inputField($p.'_sellPrice', 'number', 'SellPrice (cobre)', 'Lo que recibe al venderlo.', null, '0');
    if ($gear) {
        $html .= inputField($p.'_stackable', 'number', 'stackable — Apilable hasta', '1 = no apilable.', null, '1');
        $html .= inputField($p.'_maxcount', 'number', 'maxcount — Máx. inventario', '0 = sin límite.', null, '0');
        $html .= inputField($p.'_maxDurability', 'number', 'maxDurability — Durabilidad', '0 = no se rompe.', null, '0');
    }
    $html .= '</div>
      <div class="field" style="margin-top:14px">
        <label>description — Descripción</label>
        <textarea name="'.$p.'_description" placeholder="Texto bajo el nombre…">' . h(pv($p.'_description','')) . '</textarea>
        <span class="help">Texto opcional en el tooltip.</span>
      </div>
    </div>';

    /* ---------- 2 · ESPECÍFICO DEL TIPO ---------- */
    if ($isWeapon) {
        $html .= '<div class="section">
          <div class="section-title">2 · Datos del arma 🗡️</div>
          <div class="section-desc">Material del SQL se calcula solo: Metal (1), excepto bastones → Madera (2).</div>
          <div class="grid">';
        $html .= selectField($p.'_subclass', $WEAPON_SUBCLASS, 'subclass — Tipo', 'Habilidad y animación.', 7);
        $html .= selectField($p.'_inventoryType', $WEAPON_INV, 'InventoryType — Casilla', 'Dónde se equipa.', 13);
        $html .= inputField($p.'_dmgMin1', 'number', 'dmg_min1 — Daño mínimo', 'Daño mínimo base.', '0.1', '100');
        $html .= inputField($p.'_dmgMax1', 'number', 'dmg_max1 — Daño máximo', 'Daño máximo base.', '0.1', '200');
        $html .= selectField($p.'_dmgType1', $DMG_TYPE, 'dmg_type1 — Tipo de daño', 'Casi siempre físico (0).', 0);
        $html .= inputField($p.'_delay', 'number', 'delay (ms)', 'Velocidad del arma.', null, '2000');
        $html .= selectField($p.'_sheath', $SHEATH_WEAPON, 'sheath', 'Cómo se guarda.', 1);
        $html .= '</div></div>';
    } elseif ($isArmor) {
        $html .= '<div class="section">
          <div class="section-title">2 · Datos de la armadura 🛡️</div>
          <div class="section-desc">Material del SQL: Tela→7, Cuero→8, Malla→5, Placas→6, Escudo→1 (Metal).</div>
          <div class="grid">';
        $html .= selectField($p.'_subclass', $ARMOR_SUBCLASS, 'subclass — Material', 'Habilidad de defensa.', 2);
        $html .= selectField($p.'_inventoryType', $ARMOR_INV, 'InventoryType — Casilla', 'Escudos: casilla 14.', 5);
        $html .= inputField($p.'_armor', 'number', 'armor', '0 en abalorios y joyas.', null, '1000');
        $html .= inputField($p.'_block', 'number', 'block (escudos)', 'Solo escudos. 0 en el resto.', null, '0');
        $html .= selectField($p.'_sheath', $SHEATH_ARMOR, 'sheath', 'Solo relevante en escudos.', 0);
        $html .= '</div></div>';
    } elseif ($isMount) {
        $html .= '<div class="section">
          <div class="section-title">2 · Datos de la montura 🐎</div>
          <div class="section-desc">Una montura es un ítem que enseña un hechizo al usarlo.</div>
          <div class="grid">';
        $html .= inputField($p.'_spellId', 'number', 'spellid_1 — Hechizo', 'ID del hechizo de invocación (Spell.dbc). Ej: 55884.', null, '0');
        $html .= selectField($p.'_spellTrigger', $SPELL_TRIGGER_MOUNT, 'spelltrigger_1', 'Las monturas usan 0.', 0);
        $html .= inputField($p.'_spellCooldown', 'number', 'spellcooldown_1 (ms)', '-1 = usa el del hechizo.', null, '-1');
        $html .= inputField($p.'_flags', 'number', 'Flags', '0 por defecto. 64 = única.', null, '0');
        $html .= selectField($p.'_material', $MATERIAL_MOUNT, 'Material', '8 = Cuero, 1 = Metal.', 8);
        $html .= '</div>
          <p class="note">💡 Se aplican automáticamente class=15, subclass=5 y spellcharges_1=1.</p>
        </div>';
    }

    /* ---------- 3 · ESTADÍSTICAS (solo gear) ---------- */
    if ($gear) {
        $html .= '<div class="section">
          <div class="section-title">3 · Estadísticas del ítem 📊</div>
          <div class="section-desc">Hasta 10 pares. Deja «Ninguno» en las que no uses.</div>
          <table class="stats-table">
            <thead><tr><th style="width:40px">#</th><th>Tipo</th><th style="width:140px">Valor</th></tr></thead>
            <tbody>';
        for ($i = 1; $i <= 10; $i++) {
            $html .= '<tr><td>' . $i . '</td>';
            $html .= '<td><select name="' . $p . '_stat_type' . $i . '">' . opt($STAT_OPTIONS, pn($p.'_stat_type'.$i, 0)) . '</select></td>';
            $html .= '<td><input type="number" name="' . $p . '_stat_value' . $i . '" value="' . h(pn($p.'_stat_value'.$i, 0)) . '"></td></tr>';
        }
        $html .= '</tbody></table></div>';

        /* ---------- 4 · RESISTENCIAS ---------- */
        $html .= '<div class="section">
          <div class="section-title">4 · Resistencias elementales</div>
          <div class="section-desc">Déjalas a 0 si no aplica.</div>
          <div class="grid">';
        $html .= inputField($p.'_holyRes', 'number', 'holy_res', '', null, '0');
        $html .= inputField($p.'_fireRes', 'number', 'fire_res', '', null, '0');
        $html .= inputField($p.'_natureRes', 'number', 'nature_res', '', null, '0');
        $html .= inputField($p.'_frostRes', 'number', 'frost_res', '', null, '0');
        $html .= inputField($p.'_shadowRes', 'number', 'shadow_res', '', null, '0');
        $html .= inputField($p.'_arcaneRes', 'number', 'arcane_res', '', null, '0');
        $html .= '</div></div>';
    }

    /* ---------- 5 · REQUISITOS ---------- */
    $html .= '<div class="section">
      <div class="section-title">5 · Requisitos de uso</div>
      <div class="section-desc">Restricciones de clase, raza, habilidad o reputación.</div>
      <div class="grid">';
    $html .= inputField($p.'_allowableClass', 'number', 'AllowableClass', '-1 = todas.', null, '-1');
    $html .= inputField($p.'_allowableRace', 'number', 'AllowableRace', '-1 = todas.', null, '-1');
    if ($gear) {
        $html .= inputField($p.'_requiredSkill', 'number', 'RequiredSkill', '0 = ninguna.', null, '0');
        $html .= inputField($p.'_requiredSkillRank', 'number', 'RequiredSkillRank', '0 = sin mínimo.', null, '0');
    }
    $html .= inputField($p.'_requiredspell', 'number', 'requiredspell', '0 = ninguno.', null, '0');
    if ($gear) {
        $html .= inputField($p.'_requiredRepFaction', 'number', 'RequiredReputationFaction', '0 = ninguna.', null, '0');
        $html .= selectField($p.'_requiredRepRank', $REP_RANK, 'RequiredReputationRank', '', 0);
    }
    $html .= '</div></div>';

    /* ---------- 6 · HECHIZO EXTRA (solo gear) ---------- */
    if ($gear) {
        $html .= '<div class="section">
          <div class="section-title">6 · Hechizo extra (opcional)</div>
          <div class="section-desc">Para que el ítem otorgue un hechizo al equipar/usar/golpear.</div>
          <div class="grid">';
        $html .= inputField($p.'_spellId1', 'number', 'spellid_1', '0 = ninguno.', null, '0');
        $html .= selectField($p.'_spellTrigger1', $SPELL_TRIGGER, 'spelltrigger_1', '', 0);
        $html .= inputField($p.'_spellCharges1', 'number', 'spellcharges_1', '0 = ilimitadas.', null, '0');
        $html .= inputField($p.'_spellCooldown1', 'number', 'spellcooldown_1 (ms)', '-1 = del hechizo.', null, '-1');
        $html .= '</div></div>';

        /* ---------- 7 · EXTRAS ---------- */
        $html .= '<div class="section">
          <div class="section-title">7 · Engranajes y datos extra</div>
          <div class="section-desc">Todo opcional.</div>
          <div class="grid">';
        $html .= selectField($p.'_socketColor1', $SOCKET_COLORS, 'socketColor_1', '', 0);
        $html .= selectField($p.'_socketColor2', $SOCKET_COLORS, 'socketColor_2', '', 0);
        $html .= selectField($p.'_socketColor3', $SOCKET_COLORS, 'socketColor_3', '', 0);
        $html .= inputField($p.'_itemset', 'number', 'itemset', 'ID de ItemSet.dbc. 0 = ninguno.', null, '0');
        $html .= inputField($p.'_randomProperty', 'number', 'RandomProperty', '', null, '0');
        $html .= inputField($p.'_randomSuffix', 'number', 'RandomSuffix', '', null, '0');
        $html .= inputField($p.'_minMoneyLoot', 'number', 'minMoneyLoot', '', null, '0');
        $html .= inputField($p.'_maxMoneyLoot', 'number', 'maxMoneyLoot', '', null, '0');
        $html .= inputField($p.'_scriptName', 'text', 'ScriptName', 'Script C++. Vacío = sin script.', null, '');
        $html .= '</div></div>';
    }

    return $html;
}

/* ============================================================
   PROCESAR ACCIONES
   ============================================================ */
$accion = $_POST['accion'] ?? '';
$tipo = $_POST['tipo'] ?? 'weapon';
if (!in_array($tipo, ['weapon', 'armor', 'mount'], true)) $tipo = 'weapon';

$mensaje = '';
$mensajeTipo = '';
$sqlGenerado = '';

if ($accion === 'generar') {
    $values = buildValues($tipo);
    $sqlGenerado = buildSqlString($values);
} elseif ($accion === 'insertar') {
    $pdo = getPdo();
    if (!$pdo) {
        $mensaje = '❌ No se pudo conectar a la BD (' . h($DB_HOST) . '/' . h($DB_NAME) . '). Verifica que MySQL esté encendido y los datos de conexión.';
        $mensajeTipo = 'error';
    } else {
        try {
            $values = buildValues($tipo);
            $colList = implode(', ', array_map(fn($c) => '`' . $c . '`', $COLUMNS));
            $placeholders = implode(', ', array_fill(0, count($COLUMNS), '?'));
            $stmt = $pdo->prepare("INSERT INTO `item_template` ($colList) VALUES ($placeholders)");
            $params = [];
            foreach ($COLUMNS as $c) {
                $params[] = $values[$c] ?? 0;
            }
            $stmt->execute($params);
            $mensaje = '✅ Ítem insertado correctamente en <b>' . h($DB_NAME) . '</b>. <b>entry = ' . h($values['entry']) . '</b>';
            $mensajeTipo = 'success';
        } catch (PDOException $e) {
            $mensaje = '❌ Error al insertar: ' . h($e->getMessage());
            $mensajeTipo = 'error';
        }
    }
} elseif ($accion === 'eliminar') {
    $pdo = getPdo();
    if (!$pdo) {
        $mensaje = '❌ No se pudo conectar a la BD.';
        $mensajeTipo = 'error';
    } else {
        $entry = pn(prefix($tipo) . '_entry', 0);
        if ($entry <= 0) {
            $mensaje = '⚠️ Introduce un entry válido antes de eliminar.';
            $mensajeTipo = 'error';
        } else {
            try {
                $stmt = $pdo->prepare("DELETE FROM `item_template` WHERE `entry` = ?");
                $stmt->execute([$entry]);
                if ($stmt->rowCount() > 0) {
                    $mensaje = '✅ Ítem con entry = <b>' . h($entry) . '</b> eliminado correctamente de <b>' . h($DB_NAME) . '</b>.';
                    $mensajeTipo = 'success';
                } else {
                    $mensaje = '⚠️ No se encontró ningún ítem con entry = <b>' . h($entry) . '</b> en <b>' . h($DB_NAME) . '</b>.';
                    $mensajeTipo = 'warning';
                }
            } catch (PDOException $e) {
                $mensaje = '❌ Error al eliminar: ' . h($e->getMessage());
                $mensajeTipo = 'error';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Generador SQL — item_template (AzerothCore)</title>
<style>
  :root{
    --bg:#0b1120; --panel:#111c2e; --panel2:#17263d; --border:#22344f;
    --text:#e5eef8; --muted:#8aa2bf; --accent:#38bdf8; --accent2:#0ea5e9;
    --ok:#22c55e; --danger:#ef4444; --warn:#f59e0b;
  }
  *{box-sizing:border-box;margin:0;padding:0}
  body{font-family:'Segoe UI',system-ui,sans-serif;background:var(--bg);color:var(--text);padding:24px;line-height:1.55}
  .container{max-width:1250px;margin:0 auto}
  h1{font-size:1.7rem;color:var(--accent);margin-bottom:4px}
  .sub{color:var(--muted);font-size:.9rem;margin-bottom:22px}
  .banner{background:var(--panel2);border-left:4px solid var(--accent);padding:12px 16px;border-radius:6px;font-size:.85rem;color:var(--muted);margin-bottom:20px}
  .banner b{color:var(--text)}
  .banner code{background:#0f172a;padding:2px 6px;border-radius:4px;color:#7dd3fc;font-size:.82rem}
  .mensaje{padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:.92rem;border:1px solid transparent}
  .mensaje.success{background:#052e16;border-color:#166534;color:#bbf7d0}
  .mensaje.error{background:#450a0a;border-color:#991b1b;color:#fecaca}
  .mensaje.warning{background:#451a03;border-color:#92400e;color:#fde68a}
  .type-selector{display:flex;gap:10px;margin-bottom:16px;flex-wrap:wrap}
  .type-btn{background:var(--panel);border:2px solid var(--border);color:var(--text);padding:12px 28px;border-radius:8px;cursor:pointer;font-weight:700;font-size:.95rem;transition:.2s}
  .type-btn.active{background:var(--accent2);border-color:var(--accent);color:#fff;box-shadow:0 0 15px rgba(56,189,248,.4)}
  .type-btn:hover:not(.active){background:var(--panel2);border-color:var(--accent)}
  .panel-header{background:linear-gradient(135deg,var(--accent2),#0369a1);padding:14px 20px;border-radius:10px;margin-bottom:16px;font-weight:700;font-size:1.05rem}
  .section{background:var(--panel);border:1px solid var(--border);border-radius:10px;padding:20px;margin-bottom:16px}
  .section-title{font-size:1.02rem;font-weight:700;margin-bottom:6px;color:var(--accent)}
  .section-desc{font-size:.82rem;color:var(--muted);margin-bottom:14px}
  .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:14px}
  .field{display:flex;flex-direction:column;gap:4px}
  .field label{font-size:.8rem;color:var(--text);font-weight:600}
  .field .help{font-size:.72rem;color:var(--muted);line-height:1.35}
  .field input,.field select,.field textarea{background:var(--bg);border:1px solid var(--border);border-radius:6px;color:var(--text);padding:8px 10px;font-size:.9rem;outline:none;font-family:inherit;transition:border-color .2s}
  .field input:focus,.field select:focus,.field textarea:focus{border-color:var(--accent)}
  .field textarea{resize:vertical;min-height:60px}
  .stats-table{width:100%;border-collapse:collapse;font-size:.85rem}
  .stats-table th{text-align:left;color:var(--muted);font-weight:600;padding:6px 8px;border-bottom:1px solid var(--border);font-size:.75rem;text-transform:uppercase;letter-spacing:.03em}
  .stats-table td{padding:4px 6px}
  .stats-table select,.stats-table input{width:100%;background:var(--bg);border:1px solid var(--border);border-radius:5px;color:var(--text);padding:6px 8px;font-size:.82rem}
  .actions{display:flex;gap:12px;flex-wrap:wrap;margin:18px 0}
  .btn{padding:12px 28px;border-radius:8px;border:none;font-weight:700;font-size:.95rem;cursor:pointer;transition:.2s}
  .btn-primary{background:var(--accent2);color:#fff}
  .btn-primary:hover{background:#0284c7}
  .btn-success{background:var(--ok);color:#fff}
  .btn-success:hover{background:#16a34a}
  .btn-danger{background:var(--danger);color:#fff}
  .btn-danger:hover{background:#b91c1c}
  .btn-copy{background:var(--panel2);color:var(--text);border:1px solid var(--border)}
  .btn-copy:hover{background:#22344f}
  .output-box{background:#020617;border:1px solid var(--border);border-radius:10px;padding:18px;font-family:'Cascadia Code','Fira Code',monospace;font-size:.72rem;color:#a5f3fc;white-space:pre-wrap;word-break:break-all;min-height:180px;max-height:500px;overflow:auto;line-height:1.45}
  .output-box:empty::before{content:'El SQL aparecerá aquí al pulsar Generar SQL…';color:#334155;font-style:italic;font-size:.9rem}
  .note{font-size:.78rem;color:var(--warn);margin-top:10px}
  .status{font-size:.82rem;color:var(--muted);margin-bottom:16px;padding:8px 12px;background:var(--panel);border-radius:6px;border:1px solid var(--border)}
  .status b{color:var(--text)}
  @media(max-width:600px){.grid{grid-template-columns:1fr}}
</style>
</head>
<body data-tipo="<?= h($tipo) ?>">
<div class="container">

  <h1>⚔️ Generador SQL + Inserción directa — item_template</h1>
  <p class="sub">AzerothCore 3.3.5a · PHP + MySQL · Armas · Armaduras · Monturas</p>

  <div class="status">
    🔌 <b>Conexión:</b> <?= h($DB_USER) ?>@<?= h($DB_HOST) ?>:<?= h($DB_PORT) ?> → <b><?= h($DB_NAME) ?></b>
  </div>

  <div class="banner">
    <b>¿Cómo funciona?</b> Rellena los campos y pulsa <b>Generar SQL</b> para verlo, o <b>Insertar en BD</b> para
    añadirlo directamente a <code>item_template</code>. Si algo falla, usa <b>Eliminar de BD</b> para quitarlo.
  </div>

  <?php if ($mensaje): ?>
    <div class="mensaje <?= h($mensajeTipo) ?>"><?= $mensaje ?></div>
  <?php endif; ?>

  <form method="POST" action="">
    <input type="hidden" name="tipo" id="tipoHidden" value="<?= h($tipo) ?>">

    <div class="type-selector">
      <button type="button" class="type-btn" data-type="weapon" onclick="switchType('weapon')">🗡️ Arma</button>
      <button type="button" class="type-btn" data-type="armor" onclick="switchType('armor')">🛡️ Armadura</button>
      <button type="button" class="type-btn" data-type="mount" onclick="switchType('mount')">🐎 Montura</button>
    </div>

    <div id="panel-weapon"><?= buildPanel('weapon') ?></div>
    <div id="panel-armor" style="display:none"><?= buildPanel('armor') ?></div>
    <div id="panel-mount" style="display:none"><?= buildPanel('mount') ?></div>

    <div class="actions">
      <button type="submit" name="accion" value="generar" class="btn btn-primary">⚙️ Generar SQL</button>
      <button type="submit" name="accion" value="insertar" class="btn btn-success">💾 Insertar en BD</button>
      <button type="submit" name="accion" value="eliminar" class="btn btn-danger" onclick="return confirm('¿Eliminar el ítem con el entry actual de la base de datos?');">🗑️ Eliminar de BD</button>
      <button type="button" class="btn btn-copy" onclick="copiarSQL(event)">📋 Copiar SQL</button>
    </div>

    <div class="output-box" id="output"><?= $sqlGenerado !== '' ? h($sqlGenerado) : '' ?></div>
  </form>

</div>

<script>
function switchType(type) {
  document.getElementById('tipoHidden').value = type;
  document.querySelectorAll('.type-btn').forEach(b => b.classList.toggle('active', b.dataset.type === type));
  document.getElementById('panel-weapon').style.display = (type === 'weapon') ? 'block' : 'none';
  document.getElementById('panel-armor').style.display  = (type === 'armor')  ? 'block' : 'none';
  document.getElementById('panel-mount').style.display  = (type === 'mount')  ? 'block' : 'none';
}
async function copiarSQL(ev) {
  const txt = document.getElementById('output').textContent;
  if (!txt) { alert('Primero genera el SQL.'); return; }
  try {
    await navigator.clipboard.writeText(txt);
    const orig = ev.target.textContent;
    ev.target.textContent = '✅ Copiado';
    setTimeout(() => ev.target.textContent = orig, 1500);
  } catch(e) { alert('No se pudo copiar.'); }
}
document.addEventListener('DOMContentLoaded', function() {
  switchType(document.body.dataset.tipo || 'weapon');
});
</script>
</body>
</html>
