<?php
if (!ob_start("ob_gzhandler")) ob_start();

ini_set('display_errors', 1);
ini_set('memory_limit', '512M');
error_reporting(E_ALL);

$origen      = $_GET['origen']       ?? 'MAD';
$destinoIATA = $_GET['destino']      ?? 'LPA';
$fecha_ida   = $_GET['fecha']        ?? '';
$fecha_vuelta = !empty($_GET['fecha_vuelta']) ? $_GET['fecha_vuelta'] : null;

$api_key = "duffel_test_2MC0WXCcYUXxbgkSSZLC17YgNimlXcpd7XrxYK9JWhf";

$slices = [["origin" => $origen, "destination" => $destinoIATA, "departure_date" => $fecha_ida]];
if ($fecha_vuelta) {
    $slices[] = ["origin" => $destinoIATA, "destination" => $origen, "departure_date" => $fecha_vuelta];
}

$postData = ["data" => [
    "slices"          => $slices,
    "passengers"      => [["type" => "adult"]],
    "cabin_class"     => "economy",
    "max_connections" => 1
]];

$ch = curl_init("https://api.duffel.com/air/offer_requests?return_offers=true");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . $api_key,
    "Duffel-Version: v2",
    "Content-Type: application/json",
    "Accept-Encoding: gzip"
]);
curl_setopt($ch, CURLOPT_ENCODING, "gzip");
curl_setopt($ch, CURLOPT_TIMEOUT, 15);

$response  = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$data = json_decode($response, true);
unset($response);

$ofertas_reales = array_slice($data['data']['offers'] ?? [], 0, 40);

usort($ofertas_reales, function($a, $b) {
    return $a['total_amount'] <=> $b['total_amount'];
});

$v_pag    = 10;
$total    = count($ofertas_reales);
$paginas  = ceil($total / $v_pag);
$p_actual = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
$indice   = ($p_actual - 1) * $v_pag;
$ofertas_finales = array_slice($ofertas_reales, $indice, $v_pag);

function link_p($n, $params) {
    $params['p'] = $n;
    return "?" . http_build_query($params);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Canary Travel - Resultados</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-slate-100">
        <header>   
        <img src="static/img/lista.png"  data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample" width="30"> 
        <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">  <div class="offcanvas-header">    
            <h5 class="offcanvas-title" id="offcanvasExampleLabel">Menú Lateral</h5>    
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>  
        </div>  
        <div class="offcanvas-body d-flex flex-column">    
            <ul class="list-group">      
                <li class="list-group-item"><a href="informacionCuenta.php">Mi cuenta</a></li> 
                <li class="list-group-item"><a href="misReservas.php">Mis reservas</a></li>    
                <li class="list-group-item"><a href="recomendaciones.php">Recomendaciones</a></li>  
            </ul> 
            <ul class="list-group mt-auto">
                <li class="list-group-item"><a href="logout.php" style='text-decoration: none; color:black'>Cerrar sesion</a></li>
            </ul> 
        </div> 
        </div>

        <a href="index.php" id="menuPrincipial">
            <img src="static/img/logo.png" alt="logo" id="logo">
            <h3 id="textoCabecera">Canary Travel</h3>
        </a>
        <div class="logs">
            <button class="btn btn-primary"><a href="empresas.php" style='text-decoration: none; color:white; width:150px'>Empresas</a></button>
            <button class="btn btn-primary"><a href="sesion.php" style='text-decoration: none; color:white; width:150px'>Iniciar sesión</a></button>
            <button class="btn btn-primary"><a href="registro.php" style='text-decoration: none; color:white; width:150px'>Registrarse</a></button>
        </div>
    </header>
    <!-- PANTALLA DE CARGA -->
    <div id="loading-screen" class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-slate-100">
        <div class="relative flex items-center justify-center">
            <div class="text-5xl animate-bounce">✈️</div>
            <div class="w-20 h-20 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin absolute"></div>
        </div>
        <h2 class="mt-8 text-xl font-black text-slate-700 uppercase tracking-widest">Buscando en Canary Travel</h2>
        <p class="text-slate-400 animate-pulse">Conectando con las aerolíneas...</p>
        <div class="mt-10 w-full max-w-md space-y-4 px-6">
            <div class="h-24 bg-white rounded-3xl animate-pulse"></div>
            <div class="h-24 bg-white rounded-3xl animate-pulse opacity-50"></div>
        </div>
    </div>

    <script>
        window.addEventListener('load', function() {
            const loader = document.getElementById('loading-screen');
            loader.style.transition = 'opacity 0.5s ease';
            loader.style.opacity = '0';
            setTimeout(() => loader.remove(), 500);
        });
    </script>

    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-black text-slate-800 uppercase italic">
                <?php echo htmlspecialchars($origen); ?> ✈ <?php echo htmlspecialchars($destinoIATA); ?>
            </h1>
            <span class="bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full">
                <?php echo $total; ?> ofertas encontradas
            </span>
        </div>

        <?php if ($http_code !== 201): ?>
            <div class="bg-red-50 border border-red-200 p-6 rounded-2xl text-red-700">
                <p class="font-bold">ERROR AL BUSCAR VUELOS</p>
                <p class="text-sm">La API tardó demasiado o los datos son incorrectos. (HTTP <?php echo $http_code; ?>)</p>
            </div>
        <?php elseif ($total === 0): ?>
            <div class="bg-yellow-50 border border-yellow-200 p-6 rounded-2xl text-yellow-700">
                <p class="font-bold">Sin resultados</p>
                <p class="text-sm">No se encontraron vuelos para esa ruta y fecha.</p>
            </div>
        <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($ofertas_finales as $v):
                    $ida   = $v['slices'][0];
                    $vuelta = $v['slices'][1] ?? null;
                    $logo  = $v['owner']['logo_symbol_url'] ?? '';

                    // *** CLAVE: extraer el passenger_id de esta oferta ***
                    $passenger_id = $v['passengers'][0]['id'] ?? '';

                    $ida_dep = new DateTime($ida['segments'][0]['departing_at']);
                    $ida_arr = new DateTime(end($ida['segments'])['arriving_at']);

                    if ($vuelta) {
                        $vta_dep = new DateTime($vuelta['segments'][0]['departing_at']);
                        $vta_arr = new DateTime(end($vuelta['segments'])['arriving_at']);
                    }
                ?>
                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden hover:shadow-lg transition-all duration-300">
                    <div class="p-8">
                        <!-- IDA -->
                        <div class="flex items-center gap-6 mb-6">
                            <div class="w-20 text-center">
                                <?php if ($logo): ?>
                                    <img src="<?php echo htmlspecialchars($logo); ?>" class="w-12 h-12 mx-auto object-contain" loading="lazy">
                                <?php endif; ?>
                                <span class="text-[10px] font-bold text-slate-400 block mt-1 uppercase"><?php echo htmlspecialchars($v['owner']['name']); ?></span>
                            </div>
                            <div class="flex-1 grid grid-cols-3 items-center text-center">
                                <div class="text-left">
                                    <p class="text-2xl font-black"><?php echo $ida_dep->format('H:i'); ?></p>
                                    <p class="text-xs text-slate-400 font-bold"><?php echo htmlspecialchars($origen); ?></p>
                                </div>
                                <div class="px-4">
                                    <p class="text-[10px] text-slate-400 font-bold italic"><?php echo str_replace(['PT','H','M'], ['','h ','m'], $ida['duration']); ?></p>
                                    <div class="h-[1px] bg-slate-200 w-full relative my-2">
                                        <div class="absolute -top-[3.5px] right-0 w-2 h-2 rounded-full bg-blue-500"></div>
                                    </div>
                                    <p class="text-[10px] text-green-500 font-bold uppercase">
                                        <?php echo count($ida['segments']) > 1 ? (count($ida['segments'])-1).' escala' : 'Directo'; ?>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-black"><?php echo $ida_arr->format('H:i'); ?></p>
                                    <p class="text-xs text-slate-400 font-bold"><?php echo htmlspecialchars($destinoIATA); ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- VUELTA (si existe) -->
                        <?php if ($vuelta): ?>
                        <div class="flex items-center gap-6 pt-6 border-t border-dashed border-slate-100">
                            <div class="w-20 text-center">
                                <?php if ($logo): ?>
                                    <img src="<?php echo htmlspecialchars($logo); ?>" class="w-12 h-12 mx-auto object-contain grayscale opacity-40" loading="lazy">
                                <?php endif; ?>
                                <span class="text-[10px] font-bold text-slate-300 block mt-1 uppercase italic">Vuelta</span>
                            </div>
                            <div class="flex-1 grid grid-cols-3 items-center text-center">
                                <div class="text-left">
                                    <p class="text-2xl font-black text-slate-600"><?php echo $vta_dep->format('H:i'); ?></p>
                                    <p class="text-xs text-slate-400 font-bold"><?php echo htmlspecialchars($destinoIATA); ?></p>
                                </div>
                                <div class="px-4">
                                    <p class="text-[10px] text-slate-400 font-bold italic"><?php echo str_replace(['PT','H','M'], ['','h ','m'], $vuelta['duration']); ?></p>
                                    <div class="h-[1px] bg-slate-200 w-full relative my-2">
                                        <div class="absolute -top-[3.5px] left-0 w-2 h-2 rounded-full bg-orange-400"></div>
                                    </div>
                                    <p class="text-[10px] text-green-500 font-bold uppercase">
                                        <?php echo count($vuelta['segments']) > 1 ? (count($vuelta['segments'])-1).' escala' : 'Directo'; ?>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-black text-slate-600"><?php echo $vta_arr->format('H:i'); ?></p>
                                    <p class="text-xs text-slate-400 font-bold"><?php echo htmlspecialchars($origen); ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- PIE DE TARJETA: precio + botón -->
                    <div class="bg-slate-50 px-10 py-6 flex justify-between items-center border-t border-slate-100">
                        <div>
                            <p class="text-[10px] text-slate-400 font-black uppercase">Precio Final</p>
                            <p class="text-4xl font-black text-blue-600">
                                <?php echo number_format($v['total_amount'], 2, ',', '.'); ?>
                                <span class="text-sm font-normal text-slate-400"><?php echo $v['total_currency']; ?></span>
                            </p>
                        </div>
                        <button
                            onclick="seleccionar(
                                '<?php echo $v['id']; ?>',
                                '<?php echo $passenger_id; ?>',
                                '<?php echo $v['total_amount']; ?>',
                                '<?php echo $v['total_currency']; ?>'
                            )"
                            class="bg-blue-600 text-white px-12 py-4 rounded-2xl font-bold shadow-lg shadow-blue-100 hover:bg-blue-700 transition-colors">
                            Seleccionar
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- PAGINACIÓN -->
            <?php if ($paginas > 1): ?>
            <div class="flex justify-center gap-3 mt-12 pb-10">
                <?php for ($i = 1; $i <= $paginas; $i++): ?>
                    <a href="<?php echo link_p($i, $_GET); ?>"
                        class="w-12 h-12 flex items-center justify-center rounded-2xl font-bold <?php echo $i == $p_actual ? 'bg-blue-600 text-white shadow-xl' : 'bg-white text-slate-400 border border-slate-200 hover:border-blue-500 transition-all'; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- FORMULARIO OCULTO para pasar datos a checkout -->
    <form id="form-seleccionar" action="checkout.php" method="POST" style="display:none;">
        <input type="hidden" name="offer_id"     id="input-offer-id">
        <input type="hidden" name="passenger_id" id="input-passenger-id">
        <input type="hidden" name="precio"       id="input-precio">
        <input type="hidden" name="moneda"       id="input-moneda">
        <input type="hidden" name="origen"       value="<?php echo htmlspecialchars($origen); ?>">
        <input type="hidden" name="destino"      value="<?php echo htmlspecialchars($destinoIATA); ?>">
    </form>

    <script>
    function seleccionar(offerId, passengerId, precio, moneda) {
        document.getElementById('input-offer-id').value     = offerId;
        document.getElementById('input-passenger-id').value = passengerId;
        document.getElementById('input-precio').value       = precio;
        document.getElementById('input-moneda').value       = moneda;
        document.getElementById('form-seleccionar').submit();
    }
    </script>
</body>
</html>