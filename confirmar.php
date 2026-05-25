<?php
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
session_start();

require_once 'conectar.php'; 

$offer_id         = $_POST['offer_id']         ?? '';
$passenger_id     = $_POST['passenger_id']     ?? '';
$nombre           = trim($_POST['nombre']       ?? '');
$apellidos        = trim($_POST['apellidos']    ?? '');
$email            = trim($_POST['email']        ?? '');
$telefono         = trim($_POST['telefono']     ?? '');
$fecha_nacimiento = $_POST['fecha_nacimiento']  ?? '';
$genero           = $_POST['genero']            ?? 'm';
$precio           = $_POST['precio']            ?? '';
$moneda           = $_POST['moneda']            ?? 'EUR';
$origen           = $_POST['origen']            ?? '';
$destino          = $_POST['destino']           ?? '';

$usuario_sesion = $_SESSION['usuario'];

if (empty($offer_id) || empty($passenger_id)) {
    header('Location: busquedaVuelos.php');
    exit;
}

$api_key = "duffel_test_2MC0WXCcYUXxbgkSSZLC17YgNimlXcpd7XrxYK9JWhf";

$ch = curl_init("https://api.duffel.com/air/offers/" . $offer_id);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . $api_key,
    "Duffel-Version: v2",
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$resp_refresh = curl_exec($ch);
$code_refresh = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$oferta_fresca = json_decode($resp_refresh, true);


function checkIdReserva($id) {
    $conn = conectarBD('canaryTravel', 'root', 'root');
    $sql = "SELECT ID FROM reservasVuelos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function generarId() {
    $letrasMayusculas = range('A', 'Z');
    $numeros = range(0, 9);
    $encontrado = false;

    do {
        $id = "";
        for ($i = 0; $i < 8; $i++) {
            if ($i < 3) {
                $id .= $letrasMayusculas[array_rand($letrasMayusculas)];
            } else if ($i > 2 && $i < 6) {
                $id .= $numeros[array_rand($numeros)];
            } else if ($i == 6) {
                $id .= $letrasMayusculas[array_rand($letrasMayusculas)];
            } else if ($i == 7) {
                $id .= $numeros[array_rand($numeros)];
            }
        }
        if (!checkIdReserva($id)) {
            $encontrado = true;
        }
    } while (!$encontrado);
    
    return $id;
}


if ($code_refresh !== 200 || empty($oferta_fresca['data'])) {
    $exito  = false;
    $orden  = null;
    $result = ['errors' => [['message' => 'La oferta ha caducado. Por favor, vuelve a buscar el vuelo.']]];
} else {
    $precio_final = $oferta_fresca['data']['total_amount'];
    $moneda_final = $oferta_fresca['data']['total_currency'];

    $duracion_raw = $oferta_fresca['data']['slices'][0]['duration'] ?? 'PT0H0M';
    $duracion = str_replace(['PT','H','M'], ['','h ','m'], $duracion_raw);

    $orderData = [
        "data" => [
            "selected_offers" => [$offer_id],
            "payments" => [[
                "type"     => "balance",
                "currency" => $moneda_final,
                "amount"   => $precio_final
            ]],
            "passengers" => [[
                "id"           => $passenger_id,
                "given_name"   => $nombre,
                "family_name"  => $apellidos,
                "email"        => $email,
                "phone_number" => $telefono,
                "born_on"      => $fecha_nacimiento,
                "gender"       => $genero,
                "title"        => $genero === 'm' ? 'mr' : 'ms'
            ]]
        ]
    ];

    $ch = curl_init("https://api.duffel.com/air/orders");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($orderData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . $api_key,
        "Duffel-Version: v2",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);

    $response  = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $result = json_decode($response, true);
    $orden  = $result['data'] ?? null;
    $exito  = in_array($http_code, [200, 201]) && $orden;

    if ($exito) {
        $newID = generarId();
        try {
            $con = conectarBD('canaryTravel', 'root', 'root');
            $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $con->prepare("
                INSERT INTO reservasVuelos (id, salida, destino, coste, tiempoVuelo, nombreUsuario)
                VALUES (:id, :salida, :destino, :coste, :tiempoVuelo, :nombreUsuario)
            ");
            $stmt->execute([
                ':id'      => $newID,
                ':salida'      => $origen,
                ':destino'     => $destino,
                ':coste'       => $precio_final,
                ':tiempoVuelo' => $duracion,
                ':nombreUsuario'  => $usuario_sesion
            ]);
        } catch (PDOException $e) {
            error_log("Error INSERT reserva: " . $e->getMessage());
            die("❌ Error BD: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Canary Travel - Confirmación</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-6">
<div class="bg-white p-10 rounded-[2.5rem] shadow-xl border border-slate-200 w-full max-w-xl text-center">

    <?php if ($exito): ?>
        <div class="text-6xl mb-4">🎉</div>
        <h2 class="text-3xl font-black text-slate-800 uppercase italic mb-2">¡Reserva confirmada!</h2>
        <p class="text-slate-400 mb-6">Tu vuelo ha sido reservado correctamente.</p>

        <div class="bg-slate-50 rounded-2xl p-6 text-left space-y-3 mb-8">
            <div class="flex justify-between">
                <span class="text-xs font-black text-slate-400 uppercase">Referencia</span>
                <span class="font-bold text-slate-800"><?php echo htmlspecialchars($orden['booking_reference'] ?? '—'); ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-xs font-black text-slate-400 uppercase">Ruta</span>
                <span class="font-bold text-slate-800"><?php echo htmlspecialchars($origen . ' → ' . $destino); ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-xs font-black text-slate-400 uppercase">Pasajero</span>
                <span class="font-bold text-slate-800"><?php echo htmlspecialchars($nombre . ' ' . $apellidos); ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-xs font-black text-slate-400 uppercase">Email</span>
                <span class="font-bold text-slate-800"><?php echo htmlspecialchars($email); ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-xs font-black text-slate-400 uppercase">Total</span>
                <span class="font-bold text-blue-600 text-lg">
                    <?php echo number_format((float)$precio_final, 2, ',', '.') . ' ' . htmlspecialchars($moneda_final); ?>
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-xs font-black text-slate-400 uppercase">Duración vuelo</span>
                <span class="font-bold text-slate-800"><?php echo htmlspecialchars($duracion); ?></span>
            </div>
        </div>

        <a href="busquedaVuelos.php"
            class="inline-block bg-blue-600 text-white font-black px-10 py-4 rounded-2xl hover:bg-blue-700 transition-colors">
            Nueva búsqueda
        </a>

    <?php else: ?>
        <div class="text-6xl mb-4">❌</div>
        <h2 class="text-3xl font-black text-slate-800 uppercase italic mb-2">Error en la reserva</h2>
        <p class="text-slate-400 mb-4">No se pudo completar la reserva.</p>

        <?php if (!empty($result['errors'])): ?>
            <div class="bg-red-50 p-4 rounded-2xl mb-6">
                <?php foreach ($result['errors'] as $err): ?>
                    <p class="text-red-500 text-sm font-bold">
                        <?php echo htmlspecialchars($err['message'] ?? 'Error desconocido'); ?>
                    </p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <a href="busquedaVuelos.php"
            class="inline-block bg-blue-600 text-white font-black px-10 py-4 rounded-2xl hover:bg-blue-700 transition-colors mb-4">
            Nueva búsqueda
        </a>
        <br>
        <a href="javascript:history.back()"
            class="inline-block text-blue-500 font-bold hover:underline text-sm mt-3">← Volver</a>
    <?php endif; ?>

</div>
</body>
</html>