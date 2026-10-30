<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Generador SQL — item_template (AzerothCore · Keira3 Style)</title>
<style>
  :root{
    --bg:#0b1120; --panel:#111c2e; --panel2:#17263d; --border:#22344f;
    --text:#e5eef8; --muted:#8aa2bf; --accent:#38bdf8; --accent2:#0ea5e9;
    --ok:#22c55e; --warn:#f59e0b;
  }
  *{box-sizing:border-box;margin:0;padding:0}
  body{font-family:'Segoe UI',system-ui,sans-serif;background:var(--bg);color:var(--text);padding:24px;line-height:1.55}
  .container{max-width:1250px;margin:0 auto}
  h1{font-size:1.7rem;color:var(--accent);margin-bottom:4px}
  .sub{color:var(--muted);font-size:.9rem;margin-bottom:22px}
  .banner{background:var(--panel2);border-left:4px solid var(--accent);padding:12px 16px;border-radius:6px;font-size:.85rem;color:var(--muted);margin-bottom:20px}
  .banner b{color:var(--text)}
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
  .btn-copy{background:var(--ok);color:#fff}
  .btn-copy:hover{background:#16a34a}
  .btn-reset{background:var(--panel2);color:var(--muted);border:1px solid var(--border)}
  .btn-reset:hover{color:var(--text)}
  .output-box{background:#020617;border:1px solid var(--border);border-radius:10px;padding:18px;font-family:'Cascadia Code','Fira Code',monospace;font-size:.72rem;color:#a5f3fc;white-space:pre-wrap;word-break:break-all;min-height:180px;max-height:500px;overflow:auto;line-height:1.45}
  .output-box:empty::before{content:'El SQL completo aparecerá aquí…';color:#334155;font-style:italic;font-size:.9rem}
  .note{font-size:.78rem;color:var(--warn);margin-top:10px}
  @media(max-width:600px){.grid{grid-template-columns:1fr}}
</style>
</head>
<body>
<div class="container">

  <h1>⚔️ Generador SQL — item_template</h1>
  <p class="sub">AzerothCore 3.3.5a · Valores por defecto y estructura guiados por Keira3</p>

  <div class="banner">
    <b>¿Cómo funciona?</b> Elige el tipo. Los campos se muestran con los mismos valores por defecto que usa Keira3.
    Rellena lo que necesites y pulsa <b>Generar SQL completo</b> para obtener el <code>INSERT</code> con las <b>134 columnas</b> oficiales de <code>item_template</code>.
  </div>

  <div class="type-selector">
    <button type="button" class="type-btn active" data-type="weapon" onclick="switchType('weapon')">🗡️ Arma</button>
    <button type="button" class="type-btn" data-type="armor" onclick="switchType('armor')">🛡️ Armadura</button>
    <button type="button" class="type-btn" data-type="mount" onclick="switchType('mount')">🐎 Montura</button>
  </div>

  <div id="panel-weapon"></div>
  <div id="panel-armor" style="display:none"></div>
  <div id="panel-mount" style="display:none"></div>

  <div class="actions">
    <button type="button" class="btn btn-primary" onclick="generarSQL()">⚙️ Generar SQL completo</button>
    <button type="button" class="btn btn-copy" onclick="copiarSQL(event)">📋 Copiar</button>
    <button type="button" class="btn btn-reset" onclick="resetForm()">🔄 Limpiar</button>
  </div>

  <div class="output-box" id="output"></div>

</div>

<script>
/* ============================================================
   ESTADO
   ============================================================ */
let currentType = 'weapon';

/* ============================================================
   OPCIONES REUTILIZABLES (basadas en los tipos de la tabla)
   ============================================================ */
const QUALITY_OPTS = [
  {v:0,l:'0 — Pobre (gris)'},{v:1,l:'1 — Común (blanco)'},{v:2,l:'2 — Poco común (verde)'},
  {v:3,l:'3 — Raro (azul)'},{v:4,l:'4 — Épico (morado)'},{v:5,l:'5 — Legendario (naranja)'},
  {v:6,l:'6 — Artefacto (rojo)'},{v:7,l:'7 — Heredado (oro)'}
];
const BONDING_OPTS = [
  {v:0,l:'0 — No ligado'},{v:1,l:'1 — Ligado al recoger'},{v:2,l:'2 — Ligado al equipar'},
  {v:3,l:'3 — Ligado al usar'},{v:4,l:'4 — Ligado a misión'}
];
const WEAPON_SUBCLASS = [
  {v:0,l:'0 — Hacha 1 mano'},{v:1,l:'1 — Hacha 2 manos'},{v:2,l:'2 — Arco'},{v:3,l:'3 — Arma de fuego'},
  {v:4,l:'4 — Maza 1 mano'},{v:5,l:'5 — Maza 2 manos'},{v:6,l:'6 — Polearm'},{v:7,l:'7 — Espada 1 mano'},
  {v:8,l:'8 — Espada 2 manos'},{v:10,l:'10 — Bastón'},{v:13,l:'13 — Arma de puño'},{v:14,l:'14 — Daga'},
  {v:15,l:'15 — Ballesta'},{v:16,l:'16 — Varita'},{v:18,l:'18 — Honda'},{v:19,l:'19 — Arma 2 manos genérica'}
];
const WEAPON_INV = [
  {v:13,l:'13 — Una mano'},{v:17,l:'17 — Dos manos'},{v:15,l:'15 — A distancia (arco/ballesta)'},
  {v:26,l:'26 — A distancia derecha (varita/arma fuego)'},{v:21,l:'21 — Mano principal (off-hand)'},
  {v:22,l:'22 — Mano izquierda'}
];
const DMG_TYPE = [
  {v:0,l:'0 — Físico'},{v:1,l:'1 — Sagrado'},{v:2,l:'2 — Fuego'},{v:3,l:'3 — Naturaleza'},
  {v:4,l:'4 — Escarcha'},{v:5,l:'5 — Sombra'},{v:6,l:'6 — Arcano'}
];
const SHEATH_WEAPON = [
  {v:1,l:'1 — En la espalda (2 manos)'},{v:3,l:'3 — En la cintura (1 mano)'},{v:2,l:'2 — Al costado'}
];
const SHEATH_ARMOR = [
  {v:0,l:'0 — No aplica'},{v:4,l:'4 — Escudo en la espalda'}
];
const ARMOR_SUBCLASS = [
  {v:1,l:'1 — Tela'},{v:2,l:'2 — Cuero'},
  {v:3,l:'3 — Malla'},{v:4,l:'4 — Placas'},{v:5,l:'5 — Escudo'}
];
const ARMOR_INV = [
  {v:1,l:'1 — Cabeza'},{v:2,l:'2 — Cuello'},{v:3,l:'3 — Hombros'},{v:16,l:'16 — Espalda'},
  {v:5,l:'5 — Pecho'},{v:20,l:'20 — Túnica'},{v:9,l:'9 — Muñecas'},{v:10,l:'10 — Manos'},
  {v:6,l:'6 — Cintura'},{v:7,l:'7 — Piernas'},{v:8,l:'8 — Pies'},{v:11,l:'11 — Dedo'},
  {v:12,l:'12 — Abalorio'},{v:14,l:'14 — Escudo'}
];
const SPELL_TRIGGER_MOUNT = [
  {v:0,l:'0 — Al usar (Use)'},{v:1,l:'1 — Al equipar (Equip)'}
];
const SPELL_TRIGGER = [
  {v:0,l:'0 — Al usar'},{v:1,l:'1 — Al equipar'},{v:2,l:'2 — Al golpear'}
];
const MATERIAL_MOUNT = [
  {v:8,l:'8 — Cuero (montura viviente)'},
  {v:1,l:'1 — Metal (montura mecánica)'}
];
const REP_RANK = [
  {v:0,l:'0 — Sin requisito'},{v:1,l:'1 — Hostil'},{v:2,l:'2 — Neutral'},{v:3,l:'3 — Amistoso'},
  {v:4,l:'4 — Honorable'},{v:5,l:'5 — Venerado'},{v:6,l:'6 — Exaltado'}
];
const SOCKET_COLORS = [
  {v:0,l:'0 — Ninguno'},{v:1,l:'1 — Meta'},{v:2,l:'2 — Rojo'},{v:4,l:'4 — Amarillo'},{v:8,l:'8 — Azul'}
];
const STAT_OPTIONS = [
  {v:0,l:'— Ninguno —'},{v:3,l:'3 — Agilidad'},{v:4,l:'4 — Fuerza'},{v:5,l:'5 — Intelecto'},
  {v:6,l:'6 — Espíritu'},{v:7,l:'7 — Aguante'},{v:12,l:'12 — Defensa'},{v:13,l:'13 — Esquiva'},
  {v:14,l:'14 — Parada'},{v:15,l:'15 — Bloqueo'},{v:16,l:'16 — Golpe melee'},
  {v:17,l:'17 — Golpe a distancia'},{v:18,l:'18 — Golpe hechizos'},
  {v:19,l:'19 — Crítico melee'},{v:20,l:'20 — Crítico a distancia'},{v:21,l:'21 — Crítico hechizos'},
  {v:22,l:'22 — Poder ataque'},{v:23,l:'23 — Poder ataque ranged'},{v:24,l:'24 — Poder hechizos'},
  {v:25,l:'25 — Penetración hechizos'},{v:26,l:'26 — Índice golpe'},{v:27,l:'27 — Índice crítico'},
  {v:28,l:'28 — Índice celeridad'},{v:29,l:'29 — Golpe melee'},{v:30,l:'30 — Celeridad melee'},
  {v:31,l:'31 — Golpe hechizos'},{v:32,l:'32 — Crítico hechizos'},{v:33,l:'33 — Celeridad hechizos'},
  {v:34,l:'34 — Golpe ranged'},{v:35,l:'35 — Resiliencia'},{v:36,l:'36 — Poder ataque ranged'},
  {v:37,l:'37 — Poder hechizos arcano'},{v:38,l:'38 — Golpe fuego'},{v:39,l:'39 — Crítico fuego'},
  {v:40,l:'40 — Celeridad fuego'}
];

/* Mapa de Material según la API: Tela→7, Cuero→8, Malla→5, Placas→6, Escudo→1 */
const ARMOR_MATERIAL_MAP = { 1:7, 2:8, 3:5, 4:6, 5:1 };

/* ============================================================
   COLUMNAS item_template (AzerothCore 3.3.5a) — 134 columnas
   ============================================================ */
const COLUMNS = [
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

/* ============================================================
   HELPERS DE CONSTRUCCIÓN HTML
   ============================================================ */
function field(type, id, value, label, help, step){
  const stepAttr = step ? ` step="${step}"` : '';
  return `<div class="field">
    <label>${label}</label>
    <input type="${type}" id="${id}" value="${value}"${stepAttr}>
    <span class="help">${help || ''}</span>
  </div>`;
}
function selectEl(id, opts, sel, label, help){
  return `<div class="field">
    <label>${label}</label>
    <select id="${id}">${opts.map(o=>`<option value="${o.v}"${o.v==sel?' selected':''}>${o.l}</option>`).join('')}</select>
    <span class="help">${help || ''}</span>
  </div>`;
}
function section(title, content){
  return `<div class="section">
    <div class="section-title">${title}</div>
    ${content}
  </div>`;
}

/* ============================================================
   CONSTRUCCIÓN DE PANELES (valores por defecto estilo Keira3)
   ============================================================ */
function buildPanel(p, type){
  const isMount = type === 'mount';
  const isWeapon = type === 'weapon';
  const isArmor = type === 'armor';
  let html = '';

  const icons = { weapon:'🗡️ ARMA', armor:'🛡️ ARMADURA', mount:'🐎 MONTURA' };
  const descs = {
    weapon:'Rellena identificación, calidad, datos del arma, estadísticas, resistencias, requisitos y extras.',
    armor:'Rellena identificación, calidad, datos de la armadura, estadísticas, resistencias, requisitos y extras.',
    mount:'Rellena identificación, calidad, hechizo de la montura y requisitos. Las monturas no llevan estadísticas ni resistencias.'
  };
  html += `<div class="panel-header">${icons[type]} — ${descs[type]}</div>`;

  /* ---------- 1 · IDENTIFICACIÓN Y CALIDAD ---------- */
  html += section('1 · Identificación y calidad', `
    <div class="section-desc">Datos visibles en el tooltip del ítem.</div>
    <div class="grid">
      ${field('number', p+'_entry', 90000, 'entry — ID único', 'Número único del ítem. Usa 90000+ para personalizados.')}
      ${field('text',   p+'_name', isMount?'Nueva montura':isWeapon?'Nueva arma':'Nueva armadura', 'name — Nombre', 'Nombre que verá el jugador.')}
      ${field('number', p+'_displayid', 0, 'displayid — Modelo', 'Modelo 3D del ítem. 0 = invisible.')}
      ${selectEl(p+'_quality', QUALITY_OPTS, 3, 'Quality — Rareza', 'Color del nombre.')}
      ${field('number', p+'_itemLevel', isMount?60:200, 'ItemLevel', 'Nivel de objeto.')}
      ${field('number', p+'_requiredLevel', isMount?60:80, 'RequiredLevel', 'Nivel mínimo para usarlo.')}
      ${selectEl(p+'_bonding', BONDING_OPTS, 1, 'bonding — Tipo de ligado', 'Cuándo se liga al jugador. Por defecto: Ligado al recoger.')}
      ${field('number', p+'_buyPrice', 0, 'BuyPrice (cobre)', '1 oro = 10000 cobre.')}
      ${field('number', p+'_sellPrice', 0, 'SellPrice (cobre)', 'Lo que recibe el jugador al venderlo.')}
      ${isMount ? '' : field('number', p+'_stackable', 1, 'stackable — Apilable hasta', 'Por defecto 1 (no apilable) para equipables.')}
      ${isMount ? '' : field('number', p+'_maxcount', 0, 'maxcount — Máx. en inventario', '0 = sin límite.')}
      ${isMount ? '' : field('number', p+'_maxDurability', 0, 'maxDurability — Durabilidad', '0 = no se rompe.')}
    </div>
    <div class="field" style="margin-top:14px">
      <label>description — Descripción</label>
      <textarea id="${p}_description" placeholder="Texto bajo el nombre…"></textarea>
      <span class="help">Texto opcional en el tooltip.</span>
    </div>
  `);

  /* ---------- 2 · ESPECÍFICO DEL TIPO ---------- */
  if (isWeapon){
    html += section('2 · Datos del arma 🗡️', `
      <div class="section-desc">Tipo de arma, daño y velocidad. El Material se calcula automáticamente (Metal o Madera para bastones).</div>
      <div class="grid">
        ${selectEl(p+'_subclass', WEAPON_SUBCLASS, 7, 'subclass — Tipo de arma', 'Determina habilidad y animación de ataque.')}
        ${selectEl(p+'_inventoryType', WEAPON_INV, 13, 'InventoryType — Casilla', 'Dónde se equipa.')}
        ${field('number', p+'_dmgMin1', 100, 'dmg_min1 — Daño mínimo', 'Daño mínimo base del arma.', '0.1')}
        ${field('number', p+'_dmgMax1', 200, 'dmg_max1 — Daño máximo', 'Daño máximo base del arma.', '0.1')}
        ${selectEl(p+'_dmgType1', DMG_TYPE, 0, 'dmg_type1 — Tipo de daño', 'Casi siempre físico (0).')}
        ${field('number', p+'_delay', 2000, 'delay — Velocidad (ms)', 'Tiempo entre golpes. 1500–3500 típico.')}
        ${selectEl(p+'_sheath', SHEATH_WEAPON, 1, 'sheath — Guardado', 'Animación al guardar el arma.')}
      </div>
    `);
  } else if (isArmor){
    html += section('2 · Datos de la armadura 🛡️', `
      <div class="section-desc">Material, casilla y armadura. El campo Material del SQL se ajusta solo (Tela→7, Cuero→8, Malla→5, Placas→6, Escudo→1).</div>
      <div class="grid">
        ${selectEl(p+'_subclass', ARMOR_SUBCLASS, 2, 'subclass — Material', 'Determina la habilidad de defensa.')}
        ${selectEl(p+'_inventoryType', ARMOR_INV, 5, 'InventoryType — Casilla', 'Dónde se equipa. Escudos: casilla 14.')}
        ${field('number', p+'_armor', 1000, 'armor — Armadura', '0 en abalorios y joyas.')}
        ${field('number', p+'_block', 0, 'block — Bloqueo (escudos)', 'Solo escudos. 0 en el resto.')}
        ${selectEl(p+'_sheath', SHEATH_ARMOR, 0, 'sheath — Guardado', 'Solo relevante en escudos.')}
      </div>
    `);
  } else if (isMount){
    html += section('2 · Datos de la montura 🐎', `
      <div class="section-desc">Una montura es un ítem que enseña un hechizo al usarlo.</div>
      <div class="grid">
        ${field('number', p+'_spellId', 0, 'spellid_1 — Hechizo de la montura', 'ID del hechizo de invocación (Spell.dbc). Ej: 55884, 48025, 60136.')}
        ${selectEl(p+'_spellTrigger', SPELL_TRIGGER_MOUNT, 0, 'spelltrigger_1 — Disparo', 'Las monturas usan 0 (al usar).')}
        ${field('number', p+'_spellCooldown', -1, 'spellcooldown_1 (ms)', '-1 = usa el del hechizo.')}
        ${field('number', p+'_flags', 0, 'Flags — Banderas', '0 por defecto. 64 = única, 8192 = no vendible.')}
        ${selectEl(p+'_material', MATERIAL_MOUNT, 8, 'Material de la montura', '8 = Cuero (viviente), 1 = Metal (mecánica).')}
      </div>
      <p class="note">💡 Al generar el SQL se aplican automáticamente <code>class=15</code>, <code>subclass=5</code> y <code>spellcharges_1=1</code> (se consume al usar).</p>
    `);
  }

  /* ---------- 3 · ESTADÍSTICAS (solo arma/armadura) ---------- */
  if (!isMount){
    html += `<div class="section">
      <div class="section-title">3 · Estadísticas del ítem 📊</div>
      <div class="section-desc">Hasta 10 pares de estadística. Deja «Ninguno» en las que no uses.</div>
      <table class="stats-table">
        <thead><tr><th style="width:40px">#</th><th>Tipo de estadística</th><th style="width:140px">Valor</th></tr></thead>
        <tbody id="${p}_statsBody"></tbody>
      </table>
    </div>`;

    html += section('4 · Resistencias elementales', `
      <div class="section-desc">Déjalas a 0 si no aplica.</div>
      <div class="grid">
        ${field('number', p+'_holyRes', 0, 'holy_res — Sagrado', '')}
        ${field('number', p+'_fireRes', 0, 'fire_res — Fuego', '')}
        ${field('number', p+'_natureRes', 0, 'nature_res — Naturaleza', '')}
        ${field('number', p+'_frostRes', 0, 'frost_res — Escarcha', '')}
        ${field('number', p+'_shadowRes', 0, 'shadow_res — Sombra', '')}
        ${field('number', p+'_arcaneRes', 0, 'arcane_res — Arcano', '')}
      </div>
    `);
  }

  /* ---------- 5 · REQUISITOS ---------- */
  html += section('5 · Requisitos de uso', `
    <div class="section-desc">Restricciones de clase, raza, habilidad o reputación.</div>
    <div class="grid">
      ${field('number', p+'_allowableClass', -1, 'AllowableClass', '-1 = todas. Máscaras: 1=Guerrero, 2=Paladín, 4=Cazador, 8=Pícaro, 16=Sacerdote, 32=DK, 64=Chamán, 128=Mago, 256=Brujo, 1024=Druida.')}
      ${field('number', p+'_allowableRace', -1, 'AllowableRace', '-1 = todas. Máscaras: 1=Humano, 2=Orco, 4=Enano, 8=Elfo noche, 16=No-muerto, 32=Tauren, 64=Gnomo, 128=Trol, 512=Elfo sangre, 1024=Draenei.')}
      ${isMount ? '' : field('number', p+'_requiredSkill', 0, 'RequiredSkill', 'ID de habilidad. 0 = ninguna.')}
      ${isMount ? '' : field('number', p+'_requiredSkillRank', 0, 'RequiredSkillRank', 'Rango mínimo. 0 = sin mínimo.')}
      ${field('number', p+'_requiredspell', 0, 'requiredspell', 'Hechizo que debe conocer el jugador. 0 = ninguno.')}
      ${isMount ? '' : field('number', p+'_requiredRepFaction', 0, 'RequiredReputationFaction', '0 = ninguna.')}
      ${isMount ? '' : selectEl(p+'_requiredRepRank', REP_RANK, 0, 'RequiredReputationRank', 'Rango mínimo de reputación.')}
    </div>
  `);

  /* ---------- 6 · HECHIZO EXTRA (solo arma/armadura) ---------- */
  if (!isMount){
    html += section('6 · Hechizo extra del ítem (opcional)', `
      <div class="section-desc">Para que el ítem otorgue un hechizo al equipar, usar o golpear.</div>
      <div class="grid">
        ${field('number', p+'_spellId1', 0, 'spellid_1 — ID del hechizo', '0 = ninguno.')}
        ${selectEl(p+'_spellTrigger1', SPELL_TRIGGER, 0, 'spelltrigger_1 — Disparo', 'Cuándo se activa.')}
        ${field('number', p+'_spellCharges1', 0, 'spellcharges_1 — Cargas', '0 = ilimitadas.')}
        ${field('number', p+'_spellCooldown1', -1, 'spellcooldown_1 (ms)', '-1 = usa el del hechizo.')}
      </div>
    `);

    html += section('7 · Engranajes y datos extra', `
      <div class="section-desc">Todo opcional. Deja 0 si no aplica.</div>
      <div class="grid">
        ${selectEl(p+'_socketColor1', SOCKET_COLORS, 0, 'socketColor_1', 'Color del engranaje 1.')}
        ${selectEl(p+'_socketColor2', SOCKET_COLORS, 0, 'socketColor_2', 'Color del engranaje 2.')}
        ${selectEl(p+'_socketColor3', SOCKET_COLORS, 0, 'socketColor_3', 'Color del engranaje 3.')}
        ${field('number', p+'_itemset', 0, 'itemset — Conjunto', 'ID de ItemSet.dbc. 0 = ninguno.')}
        ${field('number', p+'_randomProperty', 0, 'RandomProperty', '0 = ninguno.')}
        ${field('number', p+'_randomSuffix', 0, 'RandomSuffix', '0 = ninguno.')}
        ${field('number', p+'_minMoneyLoot', 0, 'minMoneyLoot (cobre)', '')}
        ${field('number', p+'_maxMoneyLoot', 0, 'maxMoneyLoot (cobre)', '')}
        ${field('text',   p+'_scriptName', '', 'ScriptName', 'Script C++. Vacío = sin script.')}
      </div>
    `);
  }

  return html;
}

/* ============================================================
   POBLAR TABLA DE ESTADÍSTICAS
   ============================================================ */
function initStats(p){
  const body = document.getElementById(p + '_statsBody');
  if (!body) return;
  body.innerHTML = '';
  for (let i = 1; i <= 10; i++){
    const tr = document.createElement('tr');
    tr.innerHTML =
      '<td>' + i + '</td>' +
      '<td><select id="' + p + '_stat_type' + i + '">' +
        STAT_OPTIONS.map(o => '<option value="' + o.v + '">' + o.l + '</option>').join('') +
      '</select></td>' +
      '<td><input type="number" id="' + p + '_stat_value' + i + '" value="0"></td>';
    body.appendChild(tr);
  }
}

/* ============================================================
   CONSTRUIR TODOS LOS PANELES AL INICIO
   ============================================================ */
function buildAllPanels(){
  const map = { weapon:'w', armor:'a', mount:'m' };
  ['weapon','armor','mount'].forEach(function(t){
    const p = map[t];
    document.getElementById('panel-' + t).innerHTML = buildPanel(p, t);
    if (t !== 'mount') initStats(p);
  });
}

/* ============================================================
   CAMBIO DE PANEL
   ============================================================ */
function switchType(type){
  currentType = type;
  document.querySelectorAll('.type-btn').forEach(function(b){
    b.classList.toggle('active', b.dataset.type === type);
  });
  document.getElementById('panel-weapon').style.display = (type === 'weapon') ? 'block' : 'none';
  document.getElementById('panel-armor').style.display  = (type === 'armor')  ? 'block' : 'none';
  document.getElementById('panel-mount').style.display  = (type === 'mount')  ? 'block' : 'none';
  document.getElementById('output').textContent = '';
}

/* ============================================================
   LECTURA DE CAMPOS
   ============================================================ */
function num(id, def){
  if (def === undefined) def = 0;
  const el = document.getElementById(id);
  if (!el) return def;
  const n = parseInt(el.value, 10);
  return isNaN(n) ? def : n;
}
function fnum(id, def){
  if (def === undefined) def = 0;
  const el = document.getElementById(id);
  if (!el) return def;
  const n = parseFloat(el.value);
  return isNaN(n) ? def : n;
}
function str(id, def){
  if (def === undefined) def = '';
  const el = document.getElementById(id);
  if (!el) return def;
  return (el.value || def).replace(/'/g, "''");
}

/* ============================================================
   CONSTRUIR OBJETO DE VALORES (lógica de Keira3)
   ============================================================ */
function buildValues(){
  const map = { weapon:'w', armor:'a', mount:'m' };
  const p = map[currentType];
  const isMount = currentType === 'mount';
  const isWeapon = currentType === 'weapon';
  const isArmor = currentType === 'armor';

  // Valores por defecto de la tabla item_template
  const v = {
    entry: num(p+'_entry', 90000),
    SoundOverrideSubclass: -1,
    name: "'" + str(p+'_name', '') + "'",
    displayid: num(p+'_displayid', 0),
    Quality: num(p+'_quality', 3),
    Flags: isMount ? num(p+'_flags', 0) : 0,
    FlagsExtra: 0,
    BuyCount: 1,
    BuyPrice: num(p+'_buyPrice', 0),
    SellPrice: num(p+'_sellPrice', 0),
    InventoryType: 0,
    AllowableClass: num(p+'_allowableClass', -1),
    AllowableRace: num(p+'_allowableRace', -1),
    ItemLevel: num(p+'_itemLevel', 1),
    RequiredLevel: num(p+'_requiredLevel', 0),
    RequiredSkill: isMount ? 0 : num(p+'_requiredSkill', 0),
    RequiredSkillRank: isMount ? 0 : num(p+'_requiredSkillRank', 0),
    requiredspell: num(p+'_requiredspell', 0),
    requiredhonorrank: 0,
    RequiredCityRank: 0,
    RequiredReputationFaction: isMount ? 0 : num(p+'_requiredRepFaction', 0),
    RequiredReputationRank: isMount ? 0 : num(p+'_requiredRepRank', 0),
    maxcount: isMount ? 0 : num(p+'_maxcount', 0),
    stackable: isMount ? 1 : Math.max(1, num(p+'_stackable', 1)),
    ContainerSlots: 0,
    ScalingStatDistribution: 0,
    ScalingStatValue: 0,
    dmg_min1: 0, dmg_max1: 0, dmg_type1: 0,
    dmg_min2: 0, dmg_max2: 0, dmg_type2: 0,
    armor: 0,
    holy_res: 0, fire_res: 0, nature_res: 0, frost_res: 0, shadow_res: 0, arcane_res: 0,
    delay: 0,
    ammo_type: 0,
    RangedModRange: 0,
    bonding: num(p+'_bonding', 1),
    description: "'" + str(p+'_description', '') + "'",
    PageText: 0, LanguageID: 0, PageMaterial: 0,
    startquest: 0, lockid: 0,
    Material: 8,
    sheath: 0,
    RandomProperty: 0,
    RandomSuffix: 0,
    block: 0,
    itemset: 0,
    MaxDurability: isMount ? 0 : num(p+'_maxDurability', 0),
    area: 0, Map: 0, BagFamily: 0, TotemCategory: 0,
    socketColor_1: 0, socketContent_1: 0,
    socketColor_2: 0, socketContent_2: 0,
    socketColor_3: 0, socketContent_3: 0,
    socketBonus: 0, GemProperties: 0,
    RequiredDisenchantSkill: -1,
    ArmorDamageModifier: 0,
    duration: 0, ItemLimitCategory: 0, HolidayId: 0,
    ScriptName: "''",
    DisenchantID: 0, FoodType: 0,
    minMoneyLoot: 0,
    maxMoneyLoot: 0,
    flagsCustom: 0,
    VerifiedBuild: 12340
  };

  // Inicializar los 5 slots de hechizo
  for (let i = 1; i <= 5; i++){
    v['spellid_' + i] = 0;
    v['spelltrigger_' + i] = 0;
    v['spellcharges_' + i] = 0;
    v['spellppmRate_' + i] = 0;
    v['spellcooldown_' + i] = -1;
    v['spellcategory_' + i] = 0;
    v['spellcategorycooldown_' + i] = -1;
  }
  // Inicializar las 10 estadísticas
  for (let i = 1; i <= 10; i++){
    v['stat_type' + i] = 0;
    v['stat_value' + i] = 0;
  }

  /* ---------- ESPECÍFICO POR TIPO ---------- */
  if (isWeapon){
    v.class = 2;
    v.subclass = num(p+'_subclass', 7);
    v.InventoryType = num(p+'_inventoryType', 13);
    v.dmg_min1 = fnum(p+'_dmgMin1', 0);
    v.dmg_max1 = fnum(p+'_dmgMax1', 0);
    v.dmg_type1 = num(p+'_dmgType1', 0);
    v.delay = num(p+'_delay', 2000);
    v.sheath = num(p+'_sheath', 1);
    v.Material = (v.subclass === 10) ? 2 : 1; // Bastón→Madera, resto→Metal
  }
  else if (isArmor){
    v.class = 4;
    v.subclass = num(p+'_subclass', 2);
    v.InventoryType = num(p+'_inventoryType', 5);
    v.armor = num(p+'_armor', 0);
    v.block = num(p+'_block', 0);
    v.sheath = num(p+'_sheath', 0);
    v.Material = ARMOR_MATERIAL_MAP[v.subclass] || 8;
  }
  else if (isMount){
    v.class = 15;
    v.subclass = 5;
    v.InventoryType = 0;
    v.Material = num(p+'_material', 8);
    v.spellid_1 = num(p+'_spellId', 0);
    v.spelltrigger_1 = num(p+'_spellTrigger', 0);
    v.spellcooldown_1 = num(p+'_spellCooldown', -1);
    v.spellcharges_1 = 1; // Consumible al usar
  }

  /* ---------- ESTADÍSTICAS, RESISTENCIAS Y EXTRAS ---------- */
  if (!isMount){
    for (let i = 1; i <= 10; i++){
      const t = num(p+'_stat_type' + i, 0);
      v['stat_type' + i] = t;
      v['stat_value' + i] = (t === 0) ? 0 : num(p+'_stat_value' + i, 0);
    }
    v.holy_res = num(p+'_holyRes', 0);
    v.fire_res = num(p+'_fireRes', 0);
    v.nature_res = num(p+'_natureRes', 0);
    v.frost_res = num(p+'_frostRes', 0);
    v.shadow_res = num(p+'_shadowRes', 0);
    v.arcane_res = num(p+'_arcaneRes', 0);
    v.socketColor_1 = num(p+'_socketColor1', 0);
    v.socketColor_2 = num(p+'_socketColor2', 0);
    v.socketColor_3 = num(p+'_socketColor3', 0);
    v.itemset = num(p+'_itemset', 0);
    v.RandomProperty = num(p+'_randomProperty', 0);
    v.RandomSuffix = num(p+'_randomSuffix', 0);
    v.minMoneyLoot = num(p+'_minMoneyLoot', 0);
    v.maxMoneyLoot = num(p+'_maxMoneyLoot', 0);
    v.ScriptName = "'" + str(p+'_scriptName', '') + "'";

    const sid = num(p+'_spellId1', 0);
    if (sid > 0){
      v.spellid_1 = sid;
      v.spelltrigger_1 = num(p+'_spellTrigger1', 0);
      v.spellcharges_1 = num(p+'_spellCharges1', 0);
      v.spellcooldown_1 = num(p+'_spellCooldown1', -1);
    }
  }

  return v;
}

/* ============================================================
   GENERAR SQL
   ============================================================ */
function generarSQL(){
  const v = buildValues();

  const sql =
'-- ============================================================\n' +
'-- Ítem generado para AzerothCore 3.3.5a (formato Keira3)\n' +
'-- Tabla: item_template  |  entry: ' + v.entry + '\n' +
'-- Tipo: ' + currentType + '\n' +
'-- ============================================================\n' +
'INSERT INTO item_template (\n' +
wrapCols(COLUMNS, 4) + '\n' +
') VALUES (\n' +
wrapVals(COLUMNS.map(function(c){ return (v[c] !== undefined) ? v[c] : 0; }), 4) + '\n' +
');\n';

  document.getElementById('output').textContent = sql;
}

function wrapCols(cols, perLine){
  const lines = [];
  for (let i = 0; i < cols.length; i += perLine){
    lines.push('  ' + cols.slice(i, i + perLine).join(', '));
  }
  return lines.join(',\n');
}
function wrapVals(vals, perLine){
  const lines = [];
  for (let i = 0; i < vals.length; i += perLine){
    lines.push('  ' + vals.slice(i, i + perLine).join(', '));
  }
  return lines.join(',\n');
}

/* ============================================================
   COPIAR Y LIMPIAR
   ============================================================ */
async function copiarSQL(ev){
  const txt = document.getElementById('output').textContent;
  if (!txt) return;
  try {
    await navigator.clipboard.writeText(txt);
    const btn = ev.target;
    const orig = btn.textContent;
    btn.textContent = '✅ Copiado';
    setTimeout(function(){ btn.textContent = orig; }, 1500);
  } catch(e){
    alert('No se pudo copiar. Selecciona el texto manualmente.');
  }
}

function resetForm(){
  const map = { weapon:'w', armor:'a', mount:'m' };
  const p = map[currentType];
  const isMount = currentType === 'mount';
  const isWeapon = currentType === 'weapon';

  document.getElementById('output').textContent = '';
  document.getElementById(p+'_entry').value = 90000;
  document.getElementById(p+'_name').value = isMount ? 'Nueva montura' : isWeapon ? 'Nueva arma' : 'Nueva armadura';
  document.getElementById(p+'_displayid').value = 0;
  document.getElementById(p+'_description').value = '';
  document.getElementById(p+'_quality').value = 3;
  document.getElementById(p+'_itemLevel').value = isMount ? 60 : 200;
  document.getElementById(p+'_requiredLevel').value = isMount ? 60 : 80;
  document.getElementById(p+'_bonding').value = 1;
  document.getElementById(p+'_buyPrice').value = 0;
  document.getElementById(p+'_sellPrice').value = 0;
  document.getElementById(p+'_allowableClass').value = -1;
  document.getElementById(p+'_allowableRace').value = -1;
  document.getElementById(p+'_requiredspell').value = 0;

  if (!isMount){
    document.getElementById(p+'_stackable').value = 1;
    document.getElementById(p+'_maxcount').value = 0;
    document.getElementById(p+'_maxDurability').value = 0;
    document.getElementById(p+'_requiredSkill').value = 0;
    document.getElementById(p+'_requiredSkillRank').value = 0;
    document.getElementById(p+'_requiredRepFaction').value = 0;
    document.getElementById(p+'_requiredRepRank').value = 0;
    document.getElementById(p+'_holyRes').value = 0;
    document.getElementById(p+'_fireRes').value = 0;
    document.getElementById(p+'_natureRes').value = 0;
    document.getElementById(p+'_frostRes').value = 0;
    document.getElementById(p+'_shadowRes').value = 0;
    document.getElementById(p+'_arcaneRes').value = 0;
    document.getElementById(p+'_spellId1').value = 0;
    document.getElementById(p+'_spellTrigger1').value = 0;
    document.getElementById(p+'_spellCharges1').value = 0;
    document.getElementById(p+'_spellCooldown1').value = -1;
    document.getElementById(p+'_socketColor1').value = 0;
    document.getElementById(p+'_socketColor2').value = 0;
    document.getElementById(p+'_socketColor3').value = 0;
    document.getElementById(p+'_itemset').value = 0;
    document.getElementById(p+'_randomProperty').value = 0;
    document.getElementById(p+'_randomSuffix').value = 0;
    document.getElementById(p+'_minMoneyLoot').value = 0;
    document.getElementById(p+'_maxMoneyLoot').value = 0;
    document.getElementById(p+'_scriptName').value = '';
    for (let i = 1; i <= 10; i++){
      document.getElementById(p+'_stat_type' + i).value = 0;
      document.getElementById(p+'_stat_value' + i).value = 0;
    }
    if (isWeapon){
      document.getElementById(p+'_subclass').value = 7;
      document.getElementById(p+'_inventoryType').value = 13;
      document.getElementById(p+'_dmgMin1').value = 100;
      document.getElementById(p+'_dmgMax1').value = 200;
      document.getElementById(p+'_dmgType1').value = 0;
      document.getElementById(p+'_delay').value = 2000;
      document.getElementById(p+'_sheath').value = 1;
    } else {
      document.getElementById(p+'_subclass').value = 2;
      document.getElementById(p+'_inventoryType').value = 5;
      document.getElementById(p+'_armor').value = 1000;
      document.getElementById(p+'_block').value = 0;
      document.getElementById(p+'_sheath').value = 0;
    }
  } else {
    document.getElementById(p+'_spellId').value = 0;
    document.getElementById(p+'_spellTrigger').value = 0;
    document.getElementById(p+'_spellCooldown').value = -1;
    document.getElementById(p+'_flags').value = 0;
    document.getElementById(p+'_material').value = 8;
  }
}

/* ============================================================
   ARRANQUE
   ============================================================ */
document.addEventListener('DOMContentLoaded', function(){
  buildAllPanels();
  switchType('weapon');
});
</script>
</body>
</html>
