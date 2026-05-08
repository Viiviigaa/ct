<?php
    $pagina_actual = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscador - Canary Travel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="css/styles.css">

    <style>
        *, *::before, *::after { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background-color: #f1f5f9;
            display: flex;
            flex-direction: column;
        }

        /* ── Cabecera idéntica a index.php ── */
        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 20px;
            background-color: lightblue;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
            position: sticky;
            top: 0;
            z-index: 1000;
            gap: 12px;
        }

        header > img:first-child {
            cursor: pointer;
            flex-shrink: 0;
        }

        #menuPrincipial {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            flex: 1;
            justify-content: center;
        }

        #logo {
            height: 36px;
            width: auto;
        }

        #textoCabecera {
            margin: 0;
            color: #1e293b;
            white-space: nowrap;
        }

        .logs {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }

        .logs .btn {
            font-size: 13px;
            padding: 6px 14px;
            white-space: nowrap;
        }

        /* ── Contenido principal ── */
        .page-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
        }

        .buscador-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 2.5rem;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 2rem 2rem;
            width: 100%;
            max-width: 680px;
        }

        .buscador-card h2 {
            font-size: 1.6rem;
            font-weight: 900;
            color: #1e293b;
            text-transform: uppercase;
            font-style: italic;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .form-label-custom {
            font-size: 0.7rem;
            font-weight: 800;
            color: #94a3b8;
            text-transform: uppercase;
            margin-bottom: 0.4rem;
            margin-left: 0.25rem;
            display: block;
        }

        .form-control-custom {
            width: 100%;
            border: 2px solid #f1f5f9;
            padding: 1rem;
            border-radius: 1rem;
            background-color: #f8fafc;
            font-weight: 700;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-control-custom:focus {
            border-color: #3b82f6;
        }

        .btn-buscar {
            width: 100%;
            background-color: #2563eb;
            color: #fff;
            font-weight: 900;
            font-size: 1rem;
            padding: 1.1rem;
            border: none;
            border-radius: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            cursor: pointer;
            transition: background-color 0.2s, transform 0.1s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-buscar:hover { background-color: #1d4ed8; }
        .btn-buscar:active { transform: scale(0.98); }
        .btn-buscar:disabled { opacity: 0.8; cursor: not-allowed; }

        .volver-link {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 1.2rem;
            color: #94a3b8;
            font-size: 0.875rem;
            text-decoration: none;
            transition: color 0.2s;
        }
        .volver-link:hover { color: #3b82f6; }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.2rem;
            margin-bottom: 1.2rem;
        }

        @media (max-width: 520px) {
            .form-row { grid-template-columns: 1fr; }
            .buscador-card { padding: 1.5rem 1rem; border-radius: 1.5rem; }
            .logs .btn:not(:last-child) { display: none; }
            #textoCabecera { font-size: 1rem; }
        }

        .offcanvas { z-index: 1045; }
        .offcanvas-backdrop { z-index: 1040; }
        .active {
            color: blue !important; /* El color que deseas */
            font-weight: bold;
        }
    </style>
</head>
<body>
    <header>
        <img src="static/img/lista.png"
             data-bs-toggle="offcanvas"
             data-bs-target="#offcanvasExample"
             aria-controls="offcanvasExample"
             width="30">

        <!-- Offcanvas lateral -->
        <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasExampleLabel">Menú Lateral</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
            </div>
            <div class="offcanvas-body d-flex flex-column">
                <ul class="list-group">
                    <li class="list-group-item"><a href="informacionCuenta.php">Mi cuenta</a></li>
                    <li class="list-group-item"><a href="misReservas.php">Mis Reservas</a></li>
                    <li class="list-group-item"><a href="recomendaciones.php">Recomendaciones</a></li>
                </ul>
                <ul class="list-group mt-auto">
                    <li class="list-group-item">
                        <a href="logout.php" style="text-decoration: none; color: black">Cerrar sesión</a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Logo + título centrado -->
        <a href="index.php" id="menuPrincipial">
            <img src="static/img/logo.png" alt="logo" id="logo" width="75px" height="auto">
            <h3 id="textoCabecera">Canary Travel</h3>
        </a>

        <!-- Botones sesión (igual que index.php) -->
        <?php
        if (!isset($_SESSION['usuario'])) {
            echo "<div class='logs'>
                <a href='empresas.php' class='btn btn-primary'>Empresas</a>
                <a href='sesion.php' class='btn btn-primary'>Iniciar sesión</a>
                <a href='registro.php' class='btn btn-primary'>Registrarse</a>
            </div>";
        }
        ?>
    </header>
    <main>
        <nav>
            <ul class="navList">
                <li><a style='text-decoration: none; color:black;' href="index.php">Alojamientos</a></li>
                <li><a  class="active" style='text-decoration: none; color:black;' href="busquedaVuelos.php">Vuelos</a></li>
                <li><a style='text-decoration: none; color:black;' href="renting.php">Alquiler de coches</a></li>
                <li><a style='text-decoration: none; color:black;' href="ferrys.php">Ferrys</a></li>
            </ul>
        </nav>
        <div class="page-content">
        <div class="buscador-card">
            <h2>Encuentra tu vuelo</h2>

            <form action="resultados.php" method="GET" id="formularioVuelos">

                <div class="form-row">
                    <div>
                        <label class="form-label-custom">Origen</label>
                        <select name="origen" class="form-control-custom">
                            <option value="MAD">Madrid (MAD)</option>
                            <option value="BCN">Barcelona (BCN)</option>
                            <option value="AGP">Málaga (AGP)</option>
                            <option value="BIO">Bilbao (BIO)</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label-custom">Destino</label>
                        <select name="destino" class="form-control-custom">
                            <option value="TFN">Tenerife Norte (TFN)</option>
                            <option value="TFS">Tenerife Sur (TFS)</option>
                            <option value="LPA" selected>Gran Canaria (LPA)</option>
                            <option value="ACE">Lanzarote (ACE)</option>
                            <option value="FUE">Fuerteventura (FUE)</option>
                            <option value="SPC">La Palma (SPC)</option>
                            <option value="VDE">El Hierro (VDE)</option>
                            <option value="GMZ">La Gomera (GMZ)</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div>
                        <label class="form-label-custom">Fecha Ida</label>
                        <input type="date" name="fecha" required class="form-control-custom" id="ida">
                    </div>
                    <div>
                        <label class="form-label-custom">Fecha Vuelta (Opcional)</label>
                        <input type="date" name="fecha_vuelta" class="form-control-custom" id="vuelta">
                    </div>
                </div>

                <button type="submit" class="btn-buscar" id="btnBuscar">
                    Buscar Vuelos
                </button>
            </form>

            <a href="index.php" class="volver-link">Volver a la página principal</a>
        </div>
    </div>
    </main>
    <script>
        document.getElementById('formularioVuelos').addEventListener('submit', function () {
            const btn = document.getElementById('btnBuscar');
            btn.innerHTML = `
                <svg class="spin" style="width:22px;height:22px;animation:spin 1s linear infinite" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle style="opacity:.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path style="opacity:.75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Buscando mejores vuelos...
            `;
            btn.disabled = true;
        });

        const ida = document.getElementById("ida");
        const vuelta = document.getElementById("vuelta");
        const hoy = new Date().toISOString().split('T')[0];
        ida.setAttribute('min', hoy);
        vuelta.setAttribute('min', hoy);
    </script>
    <style>
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</body>
</html>