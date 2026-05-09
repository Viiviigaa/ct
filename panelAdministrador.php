<?php
include 'conectar.php';
session_start();

$conn = conectarBD();

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: sesion.php");
    exit();
}

// ── Acciones POST ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Eliminar usuario
    if (isset($_POST['eliminar_usuario'])) {
        $dni = $_POST['dni_usuario'];
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE dni = ?");
        $stmt->execute([$dni]);
    }

    // Cambiar rol usuario
    if (isset($_POST['cambiar_rol'])) {
        $dni = $_POST['dni_usuario'];
        $rol = $_POST['nuevo_rol'];
        $stmt = $conn->prepare("UPDATE usuarios SET Rol = ? WHERE dni = ?");
        $stmt->execute([$rol, $dni]);
    }

    // Eliminar reserva
    if (isset($_POST['eliminar_reserva'])) {
        $id = $_POST['id_reserva'];
        $stmt = $conn->prepare("DELETE FROM reservas WHERE id = ?");
        $stmt->execute([$id]);
    }

    // Eliminar alojamiento
    if (isset($_POST['eliminar_alojamiento'])) {
        $nombre = $_POST['nombre_alojamiento'];
        $stmt = $conn->prepare("DELETE FROM alojamientos WHERE nombreAlojamiento = ?");
        $stmt->execute([$nombre]);
    }

    // Aceptar / denegar recomendación
    if (isset($_POST['accion_recomendacion'])) {
        $id     = $_POST['id_recomendacion'];
        $accion = $_POST['accion_recomendacion']; // 'aceptada' | 'denegada'
        $stmt   = $conn->prepare("UPDATE recomendaciones SET estado = ? WHERE id = ?");
        $stmt->execute([$accion, $id]);
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// ── Consultas ─────────────────────────────────────────────────
$usuarios      = $conn->query("SELECT * FROM usuarios ORDER BY nombreUsuario")->fetchAll(PDO::FETCH_ASSOC);
$reservas      = $conn->query("SELECT r.*, u.nombreUsuario FROM reservas r LEFT JOIN usuarios u ON r.dni_usuario = u.dni ORDER BY r.id DESC")->fetchAll(PDO::FETCH_ASSOC);
$alojamientos  = $conn->query("SELECT * FROM alojamientos ORDER BY nombreAlojamiento")->fetchAll(PDO::FETCH_ASSOC);
$recomendaciones = $conn->query("SELECT * FROM recomendaciones ORDER BY estado ASC, id DESC")->fetchAll(PDO::FETCH_ASSOC);

$totalUsuarios     = count($usuarios);
$totalReservas     = count($reservas);
$totalAlojamientos = count($alojamientos);
$pendientes        = count(array_filter($recomendaciones, fn($r) => $r['estado'] === 'pendiente'));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin · CanaryTravel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        /* ── Tokens ──────────────────────────── */
        :root {
            --sand:    #f5f0e8;
            --cream:   #faf8f4;
            --ink:     #1a1612;
            --muted:   #6b6560;
            --border:  #e2dbd0;
            --teal:    #0d6e6e;
            --teal-lt: #e4f2f2;
            --amber:   #c97d2a;
            --amber-lt:#fdf3e3;
            --coral:   #c94040;
            --coral-lt:#fde8e8;
            --green:   #2d7a4f;
            --green-lt:#e5f4ec;
            --sidebar: #131110;
        }
        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--cream);
            color: var(--ink);
            margin: 0;
        }

        /* ── Sidebar ─────────────────────────── */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: 240px; height: 100vh;
            background: var(--sidebar);
            display: flex; flex-direction: column;
            padding: 0;
            z-index: 100;
        }
        .sidebar-brand {
            padding: 28px 24px 20px;
            border-bottom: 1px solid rgba(255,255,255,.07);
        }
        .sidebar-brand .logo-text {
            font-family: 'DM Serif Display', serif;
            font-size: 1.25rem;
            color: #fff;
            line-height: 1;
        }
        .sidebar-brand .logo-sub {
            font-size: .68rem;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: rgba(255,255,255,.35);
            margin-top: 4px;
        }
        .sidebar-nav {
            flex: 1;
            padding: 16px 12px;
            overflow-y: auto;
        }
        .nav-label {
            font-size: .65rem;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: rgba(255,255,255,.28);
            padding: 12px 12px 6px;
        }
        .nav-item a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 8px;
            color: rgba(255,255,255,.55);
            text-decoration: none;
            font-size: .875rem;
            font-weight: 400;
            transition: background .15s, color .15s;
        }
        .nav-item a:hover,
        .nav-item a.active {
            background: rgba(255,255,255,.07);
            color: #fff;
        }
        .nav-item a i { font-size: 1rem; width: 20px; text-align: center; }
        .badge-pill {
            margin-left: auto;
            background: var(--amber);
            color: #fff;
            font-size: .65rem;
            padding: 2px 7px;
            border-radius: 20px;
            font-weight: 500;
        }
        .sidebar-footer {
            padding: 16px 12px;
            border-top: 1px solid rgba(255,255,255,.07);
        }
        .sidebar-footer a {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 12px; border-radius: 8px;
            color: rgba(255,255,255,.4);
            text-decoration: none; font-size: .875rem;
            transition: color .15s;
        }
        .sidebar-footer a:hover { color: #fff; }

        /* ── Main ────────────────────────────── */
        .main-wrap {
            margin-left: 240px;
            min-height: 100vh;
        }
        .topbar {
            background: var(--cream);
            border-bottom: 1px solid var(--border);
            padding: 18px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky; top: 0;
            z-index: 50;
        }
        .topbar-title {
            font-family: 'DM Serif Display', serif;
            font-size: 1.35rem;
            color: var(--ink);
        }
        .topbar-user {
            font-size: .8rem;
            color: var(--muted);
            display: flex; align-items: center; gap: 8px;
        }
        .topbar-user .avatar {
            width: 32px; height: 32px;
            background: var(--teal);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: .75rem; font-weight: 500;
        }
        .content {
            padding: 32px;
        }

        /* ── Stat cards ──────────────────────── */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 32px;
        }
        .stat-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px 22px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .stat-icon {
            width: 36px; height: 36px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem;
        }
        .stat-value {
            font-family: 'DM Serif Display', serif;
            font-size: 2rem;
            line-height: 1;
            color: var(--ink);
        }
        .stat-label {
            font-size: .78rem;
            color: var(--muted);
            font-weight: 400;
        }

        /* ── Section ─────────────────────────── */
        .section { display: none; }
        .section.active { display: block; }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .section-header h2 {
            font-family: 'DM Serif Display', serif;
            font-size: 1.4rem;
            margin: 0;
        }

        /* ── Table ───────────────────────────── */
        .ct-table-wrap {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
        }
        .ct-table {
            width: 100%;
            border-collapse: collapse;
            font-size: .84rem;
        }
        .ct-table thead th {
            background: var(--sand);
            padding: 11px 16px;
            text-align: left;
            font-size: .7rem;
            letter-spacing: .09em;
            text-transform: uppercase;
            color: var(--muted);
            font-weight: 500;
            border-bottom: 1px solid var(--border);
        }
        .ct-table tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background .12s;
        }
        .ct-table tbody tr:last-child { border-bottom: none; }
        .ct-table tbody tr:hover { background: var(--sand); }
        .ct-table td {
            padding: 12px 16px;
            vertical-align: middle;
            color: var(--ink);
        }

        /* ── Badges ──────────────────────────── */
        .badge-role {
            display: inline-block;
            padding: 2px 9px;
            border-radius: 20px;
            font-size: .7rem;
            font-weight: 500;
        }
        .badge-admin   { background: #ede9fe; color: #5b21b6; }
        .badge-pro     { background: var(--amber-lt); color: var(--amber); }
        .badge-business{ background: var(--teal-lt); color: var(--teal); }
        .badge-user    { background: var(--sand); color: var(--muted); }

        .badge-estado {
            display: inline-block;
            padding: 2px 9px;
            border-radius: 20px;
            font-size: .7rem;
            font-weight: 500;
        }
        .badge-pendiente { background: var(--amber-lt); color: var(--amber); }
        .badge-aceptada  { background: var(--green-lt); color: var(--green); }
        .badge-denegada  { background: var(--coral-lt); color: var(--coral); }

        /* ── Buttons ─────────────────────────── */
        .btn-ct {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 5px 12px;
            border-radius: 7px;
            font-size: .78rem;
            font-weight: 500;
            border: 1px solid transparent;
            cursor: pointer;
            transition: opacity .15s;
            text-decoration: none;
        }
        .btn-ct:hover { opacity: .8; }
        .btn-danger-ct  { background: var(--coral-lt); color: var(--coral); border-color: #f5c6c6; }
        .btn-teal-ct    { background: var(--teal-lt);  color: var(--teal);  border-color: #b2d8d8; }
        .btn-green-ct   { background: var(--green-lt); color: var(--green); border-color: #b8dfc9; }
        .btn-amber-ct   { background: var(--amber-lt); color: var(--amber); border-color: #f5d9a8; }

        /* ── Select inline ───────────────────── */
        .select-rol {
            font-size: .78rem;
            padding: 4px 8px;
            border: 1px solid var(--border);
            border-radius: 7px;
            background: var(--cream);
            color: var(--ink);
            cursor: pointer;
        }

        /* ── Search ──────────────────────────── */
        .search-wrap {
            position: relative;
            max-width: 280px;
        }
        .search-wrap input {
            width: 100%;
            padding: 7px 12px 7px 34px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: .83rem;
            background: #fff;
            color: var(--ink);
            outline: none;
        }
        .search-wrap input:focus { border-color: var(--teal); }
        .search-wrap .si {
            position: absolute; left: 10px; top: 50%;
            transform: translateY(-50%);
            color: var(--muted); font-size: .9rem;
        }

        /* ── Reco card ───────────────────────── */
        .reco-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 16px;
        }
        .reco-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .reco-card h5 {
            font-family: 'DM Serif Display', serif;
            font-size: 1rem;
            margin: 0;
        }
        .reco-card p {
            font-size: .83rem;
            color: var(--muted);
            margin: 0;
            line-height: 1.5;
        }
        .reco-card .meta {
            font-size: .73rem;
            color: var(--muted);
            display: flex; gap: 12px; flex-wrap: wrap;
        }
        .reco-card .actions { display: flex; gap: 8px; margin-top: 4px; }

        /* ── Responsive ──────────────────────── */
        @media (max-width: 900px) {
            .sidebar { width: 60px; }
            .sidebar-brand .logo-text,
            .sidebar-brand .logo-sub,
            .nav-label,
            .nav-item a span,
            .sidebar-footer a span,
            .badge-pill { display: none; }
            .nav-item a { justify-content: center; padding: 10px; }
            .main-wrap { margin-left: 60px; }
            .stat-grid { grid-template-columns: repeat(2,1fr); }
        }
    </style>
</head>
<body>

<!-- ═══════ SIDEBAR ═══════ -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="logo-text">CanaryTravel</div>
        <div class="logo-sub">Admin Panel</div>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-label">General</div>
        <ul class="list-unstyled mb-0">
            <li class="nav-item">
                <a href="#" class="active" onclick="showSection('dashboard', this)">
                    <i class="bi bi-grid"></i><span>Inicio</span>
                </a>
            </li>
        </ul>
        <div class="nav-label">Gestión</div>
        <ul class="list-unstyled mb-0">
            <li class="nav-item">
                <a href="#" onclick="showSection('usuarios', this)">
                    <i class="bi bi-people"></i><span>Usuarios</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" onclick="showSection('reservas', this)">
                    <i class="bi bi-calendar-check"></i><span>Reservas</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" onclick="showSection('alojamientos', this)">
                    <i class="bi bi-house-door"></i><span>Alojamientos</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" onclick="showSection('recomendaciones', this)">
                    <i class="bi bi-star"></i><span>Recomendaciones</span>
                    <?php if ($pendientes > 0): ?>
                        <span class="badge-pill"><?= $pendientes ?></span>
                    <?php endif; ?>
                </a>
            </li>
        </ul>
    </nav>
    <div class="sidebar-footer">
        <a href="index.php">
            <i class="bi bi-arrow-left-circle"></i><span>Volver al sitio</span>
        </a>
        <a href="logout.php">
            <i class="bi bi-box-arrow-right"></i><span>Cerrar sesión</span>
        </a>
    </div>
</aside>

<!-- ═══════ MAIN ═══════ -->
<div class="main-wrap">

    <!-- Topbar -->
    <div class="topbar">
        <span class="topbar-title" id="topbar-title">Inicio</span>
        <div class="topbar-user">
            <div class="avatar"><?= strtoupper(substr($_SESSION['usuario'], 0, 2)) ?></div>
            <span><?= htmlspecialchars($_SESSION['usuario']) ?> · Admin</span>
        </div>
    </div>

    <div class="content">

        <!-- ═══ DASHBOARD ═══ -->
        <div id="section-dashboard" class="section active">
            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background:var(--teal-lt);color:var(--teal)"><i class="bi bi-people-fill"></i></div>
                    <div class="stat-value"><?= $totalUsuarios ?></div>
                    <div class="stat-label">Usuarios registrados</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background:var(--amber-lt);color:var(--amber)"><i class="bi bi-calendar2-check-fill"></i></div>
                    <div class="stat-value"><?= $totalReservas ?></div>
                    <div class="stat-label">Reservas totales</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background:var(--green-lt);color:var(--green)"><i class="bi bi-house-fill"></i></div>
                    <div class="stat-value"><?= $totalAlojamientos ?></div>
                    <div class="stat-label">Alojamientos activos</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background:var(--coral-lt);color:var(--coral)"><i class="bi bi-star-fill"></i></div>
                    <div class="stat-value"><?= $pendientes ?></div>
                    <div class="stat-label">Recomendaciones pendientes</div>
                </div>
            </div>

            <!-- Resumen últimas reservas -->
            <div class="section-header">
                <h2>Últimas reservas</h2>
            </div>
            <div class="ct-table-wrap">
                <table class="ct-table">
                    <thead>
                        <tr>
                            <th>#</th><th>Usuario</th><th>Destino</th><th>Fecha entrada</th><th>Fecha salida</th><th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($reservas, 0, 6) as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['id']) ?></td>
                            <td><?= htmlspecialchars($r['nombreUsuario'] ?? $r['dni_usuario']) ?></td>
                            <td><?= htmlspecialchars($r['destino'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($r['fecha_entrada'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($r['fecha_salida'] ?? '—') ?></td>
                            <td><span class="badge-estado badge-<?= $r['estado'] ?? 'pendiente' ?>"><?= $r['estado'] ?? 'pendiente' ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ═══ USUARIOS ═══ -->
        <div id="section-usuarios" class="section">
            <div class="section-header">
                <h2>Usuarios</h2>
                <div class="search-wrap">
                    <i class="bi bi-search si"></i>
                    <input type="text" placeholder="Buscar usuario…" oninput="filtrarTabla(this,'tabla-usuarios')">
                </div>
            </div>
            <div class="ct-table-wrap">
                <table class="ct-table" id="tabla-usuarios">
                    <thead>
                        <tr><th>DNI</th><th>Usuario</th><th>Email</th><th>Rol</th><th>Cambiar rol</th><th>Acciones</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $u): ?>
                        <tr>
                            <td><?= htmlspecialchars($u['dni']) ?></td>
                            <td><?= htmlspecialchars($u['nombreUsuario']) ?></td>
                            <td><?= htmlspecialchars($u['email'] ?? '—') ?></td>
                            <td>
                                <span class="badge-role badge-<?= $u['Rol'] ?>">
                                    <?= htmlspecialchars($u['Rol']) ?>
                                </span>
                            </td>
                            <td>
                                <form method="post" style="display:inline-flex;gap:6px;align-items:center">
                                    <input type="hidden" name="dni_usuario" value="<?= htmlspecialchars($u['dni']) ?>">
                                    <select name="nuevo_rol" class="select-rol">
                                        <option value="user"     <?= $u['Rol']==='user'     ?'selected':'' ?>>user</option>
                                        <option value="pro"      <?= $u['Rol']==='pro'      ?'selected':'' ?>>pro</option>
                                        <option value="business" <?= $u['Rol']==='business' ?'selected':'' ?>>business</option>
                                        <option value="admin"    <?= $u['Rol']==='admin'    ?'selected':'' ?>>admin</option>
                                    </select>
                                    <button type="submit" name="cambiar_rol" class="btn-ct btn-teal-ct">
                                        <i class="bi bi-check2"></i>
                                    </button>
                                </form>
                            </td>
                            <td>
                                <?php if ($u['nombreUsuario'] !== $_SESSION['usuario']): ?>
                                <form method="post" onsubmit="return confirm('¿Eliminar usuario <?= htmlspecialchars($u['nombreUsuario']) ?>?')">
                                    <input type="hidden" name="dni_usuario" value="<?= htmlspecialchars($u['dni']) ?>">
                                    <button type="submit" name="eliminar_usuario" class="btn-ct btn-danger-ct">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                <?php else: ?>
                                <span style="font-size:.75rem;color:var(--muted)">Tú</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ═══ RESERVAS ═══ -->
        <div id="section-reservas" class="section">
            <div class="section-header">
                <h2>Reservas</h2>
                <div class="search-wrap">
                    <i class="bi bi-search si"></i>
                    <input type="text" placeholder="Buscar reserva…" oninput="filtrarTabla(this,'tabla-reservas')">
                </div>
            </div>
            <div class="ct-table-wrap">
                <table class="ct-table" id="tabla-reservas">
                    <thead>
                        <tr><th>#</th><th>Usuario</th><th>Destino</th><th>Entrada</th><th>Salida</th><th>Huéspedes</th><th>Precio</th><th>Estado</th><th>Acción</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservas as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['id']) ?></td>
                            <td><?= htmlspecialchars($r['nombreUsuario'] ?? $r['dni_usuario']) ?></td>
                            <td><?= htmlspecialchars($r['destino'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($r['fecha_entrada'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($r['fecha_salida'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($r['num_huespedes'] ?? '—') ?></td>
                            <td><?= isset($r['precio_total']) ? number_format($r['precio_total'],2).' €' : '—' ?></td>
                            <td><span class="badge-estado badge-<?= $r['estado'] ?? 'pendiente' ?>"><?= $r['estado'] ?? 'pendiente' ?></span></td>
                            <td>
                                <form method="post" onsubmit="return confirm('¿Eliminar reserva #<?= $r['id'] ?>?')">
                                    <input type="hidden" name="id_reserva" value="<?= $r['id'] ?>">
                                    <button type="submit" name="eliminar_reserva" class="btn-ct btn-danger-ct">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ═══ ALOJAMIENTOS ═══ -->
        <div id="section-alojamientos" class="section">
            <div class="section-header">
                <h2>Alojamientos</h2>
                <div class="search-wrap">
                    <i class="bi bi-search si"></i>
                    <input type="text" placeholder="Buscar alojamiento…" oninput="filtrarTabla(this,'tabla-alojamientos')">
                </div>
            </div>
            <div class="ct-table-wrap">
                <table class="ct-table" id="tabla-alojamientos">
                    <thead>
                        <tr><th>Nombre</th><th>Isla</th><th>Dirección</th><th>Precio/noche</th><th>Huéspedes</th><th>Empresa</th><th>Acción</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($alojamientos as $a): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($a['nombreAlojamiento']) ?></strong></td>
                            <td><?= htmlspecialchars($a['isla']) ?></td>
                            <td><?= htmlspecialchars($a['direccion']) ?></td>
                            <td><?= number_format($a['precio'],2) ?> €</td>
                            <td><?= htmlspecialchars($a['max_huespedes']) ?></td>
                            <td><?= htmlspecialchars($a['codigoEmpresa']) ?></td>
                            <td>
                                <form method="post" onsubmit="return confirm('¿Eliminar «<?= htmlspecialchars($a['nombreAlojamiento']) ?>»?')">
                                    <input type="hidden" name="nombre_alojamiento" value="<?= htmlspecialchars($a['nombreAlojamiento']) ?>">
                                    <button type="submit" name="eliminar_alojamiento" class="btn-ct btn-danger-ct">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ═══ RECOMENDACIONES ═══ -->
        <div id="section-recomendaciones" class="section">
            <div class="section-header">
                <h2>Recomendaciones locales</h2>
                <span style="font-size:.82rem;color:var(--muted)"><?= $pendientes ?> pendiente<?= $pendientes!=1?'s':'' ?></span>
            </div>
            <div class="reco-grid">
                <?php foreach ($recomendaciones as $rec): ?>
                <div class="reco-card">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px">
                        <h5><?= htmlspecialchars($rec['nombre'] ?? $rec['titulo'] ?? 'Sin título') ?></h5>
                        <span class="badge-estado badge-<?= $rec['estado'] ?>"><?= $rec['estado'] ?></span>
                    </div>
                    <p><?= htmlspecialchars($rec['descripcion'] ?? '—') ?></p>
                    <div class="meta">
                        <?php if (!empty($rec['categoria'])): ?>
                            <span><i class="bi bi-tag"></i> <?= htmlspecialchars($rec['categoria']) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($rec['isla'])): ?>
                            <span><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($rec['isla']) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($rec['usuario'])): ?>
                            <span><i class="bi bi-person"></i> <?= htmlspecialchars($rec['usuario']) ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if ($rec['estado'] === 'pendiente'): ?>
                    <div class="actions">
                        <form method="post">
                            <input type="hidden" name="id_recomendacion" value="<?= $rec['id'] ?>">
                            <button type="submit" name="accion_recomendacion" value="aceptada" class="btn-ct btn-green-ct">
                                <i class="bi bi-check-circle"></i> Aceptar
                            </button>
                        </form>
                        <form method="post">
                            <input type="hidden" name="id_recomendacion" value="<?= $rec['id'] ?>">
                            <button type="submit" name="accion_recomendacion" value="denegada" class="btn-ct btn-danger-ct">
                                <i class="bi bi-x-circle"></i> Denegar
                            </button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
                <?php if (empty($recomendaciones)): ?>
                    <p style="color:var(--muted);font-size:.88rem;padding:8px 0">No hay recomendaciones registradas.</p>
                <?php endif; ?>
            </div>
        </div>

    </div><!-- /content -->
</div><!-- /main-wrap -->

<script>
const sectionLabels = {
    dashboard:        'Inicio',
    usuarios:         'Usuarios',
    reservas:         'Reservas',
    alojamientos:     'Alojamientos',
    recomendaciones:  'Recomendaciones',
};

function showSection(id, el) {
    document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
    document.getElementById('section-' + id).classList.add('active');
    document.querySelectorAll('.nav-item a').forEach(a => a.classList.remove('active'));
    if (el) el.classList.add('active');
    document.getElementById('topbar-title').textContent = sectionLabels[id] || id;
}

function filtrarTabla(input, tablaId) {
    const q = input.value.toLowerCase();
    const filas = document.getElementById(tablaId).querySelectorAll('tbody tr');
    filas.forEach(f => {
        f.style.display = f.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}
</script>

</body>
</html>