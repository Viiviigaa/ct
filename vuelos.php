<?php
// Configura los errores para ver qué pasa si algo falla
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// 1. TU TOKEN DE DUFFEL (Cópialo de tu panel)
$api_key = "duffel_test_2MC0WXCcYUXxbgkSSZLC17YgNimlXcpd7XrxYK9JWhf"; 

// 2. RECIBIR DATOS DEL FRONTEND (VUE)
$origen = $_GET['origen'] ?? 'MAD';
$destino = $_GET['destino'] ?? 'TFN'; // Tenerife Norte
$fecha = $_GET['fecha'] ?? '2026-06-15';

// 3. PREPARAR LA PETICIÓN
$url = "https://api.duffel.com/air/offer_requests";
$data = json_encode([
    "data" => [
        "slices" => [[
            "origin" => $origen,
            "destination" => $destino,
            "departure_date" => $fecha
        ]],
        "adults" => [["type" => "adult"]],
        "cabin_class" => "economy"
    ]
]);

// 4. LLAMADA CON CURL (Sustituye a la librería de Duffel)
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . $api_key,
    "Duffel-Version: 2022-03-28",
    "Content-Type: application/json"
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// 5. ENVIAR RESPUESTA A VUE
if ($http_code == 201) {
    echo $response;
} else {
    echo json_encode([
        "data" => [
            "slices" => [[
                "origin" => $origen,
                "destination" => $destino,
                "departure_date" => $fecha
            ]],
            "adults" => [["type" => "adult"]],
            "cabin_class" => "economy"
        ]
    ]);
}

?>