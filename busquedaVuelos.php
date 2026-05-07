<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscador - Canary Travel</title>
    <script src="https://cdn.tailwindcss.com"></script>
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

        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 20px;
            background-color: #ffffff;
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
            font-size: 1.2rem;
            font-weight: 700;
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

        .page-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
        }
        .offcanvas {
            z-index: 1045;
        }
        .offcanvas-backdrop {
            z-index: 1040;
        }
        .offcanvas-body ul.list-group {
            padding-left: 0;
            list-style: none;
            margin-bottom: 0;
        }

        @media (max-width: 480px) {
            .logs .btn:not(:last-child) {
                display: none;
            }
            #textoCabecera {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <img src="static/img/lista.png" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample" width="30">
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
                    <li class="list-group-item"><a href="logout.php" style="text-decoration: none; color: black">Cerrar sesion</a></li>
                </ul>
            </div>
        </div>

        <a href="" id="menuPrincipial">
            <img src="static/img/logo.png" alt="logo" id="logo">
            <h3 id="textoCabecera">Canary Travel</h3>
        </a>

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
    <div class="page-content">
        <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border border-slate-200 w-full max-w-2xl">
            <h2 class="text-3xl font-black text-slate-800 mb-6 uppercase italic text-center">Encuentra tu vuelo</h2>

            <form action="resultados.php" method="GET" id="formularioVuelos" class="space-y-6">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase mb-2 ml-1">Origen</label>
                        <select name="origen" class="w-full border-2 border-slate-100 p-4 rounded-2xl bg-slate-50 font-bold focus:border-blue-500 outline-none transition-all">
                            <option value="MAD">Madrid (MAD)</option>
                            <option value="BCN">Barcelona (BCN)</option>
                            <option value="AGP">Málaga (AGP)</option>
                            <option value="BIO">Bilbao (BIO)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase mb-2 ml-1">Destino</label>
                        <select name="destino" class="w-full border-2 border-slate-100 p-4 rounded-2xl bg-slate-50 font-bold focus:border-blue-500 outline-none transition-all">
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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase mb-2 ml-1">Fecha Ida</label>
                        <input type="date" name="fecha" required
                            class="w-full border-2 border-slate-100 p-4 rounded-2xl bg-slate-50 font-bold focus:border-blue-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase mb-2 ml-1">Fecha Vuelta (Opcional)</label>
                        <input type="date" name="fecha_vuelta"
                            class="w-full border-2 border-slate-100 p-4 rounded-2xl bg-slate-50 font-bold focus:border-blue-500 outline-none transition-all">
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-5 rounded-2xl shadow-lg shadow-blue-100 transition-all active:scale-[0.98] flex items-center justify-center text-lg uppercase tracking-wider">
                    Buscar Vuelos
                </button>
            </form>
            <a href="index.php" class="flex items-center justify-center mt-5 text-slate-400 text-sm hover:text-blue-500">Volver a la página principal</a>
        </div>
    </div>

    <script>
        document.getElementById('formularioVuelos').addEventListener('submit', function() {
            const btn = this.querySelector('button[type="submit"]');
            btn.innerHTML = `
                <svg class="animate-spin h-6 w-6 mr-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Buscando mejores vuelos...
            `;
            btn.disabled = true;
            btn.classList.add('opacity-80', 'cursor-not-allowed');
        });
    </script>
</body>
</html>