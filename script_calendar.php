<?php
require_once __DIR__ . '/vendor/autoload.php';

$client = new Google\Client();
$client->setAuthConfig(__DIR__ . '/credentials.json');
// IMPORTANTE: Esta URL debe ser exactamente igual a la que pusiste en la Consola de Google
$client->setRedirectUri('http://localhost:80/ejercicios/CanaryTravel/script_calendar.php');
$client->addScope(Google\Service\Calendar::CALENDAR);
$client->setAccessType('offline');
$client->setPrompt('consent'); // Fuerza a Google a dar el Refresh Token

$tokenPath = __DIR__ . '/token.json';

// PASO 1: Si Google nos devuelve un código por la URL (?code=xxx)
if (isset($_GET['code'])) {
    $accessToken = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($accessToken);

    // Guardamos el token
    if (!file_exists(dirname($tokenPath))) {
        mkdir(dirname($tokenPath), 0700, true);
    }
    file_put_contents($tokenPath, json_encode($client->getAccessToken()));
    
    // Limpiamos la URL para que no se quede el código ahí
    header('Location: ' . filter_var($client->getRedirectUri(), FILTER_SANITIZE_URL));
    exit;
}

// PASO 2: Si no tenemos token guardado, redirigimos al login de Google
if (!file_exists($tokenPath)) {
    $authUrl = $client->createAuthUrl();
    echo "<a class='btn btn-danger' href='$authUrl'>🔐 Haz clic aquí para Vincular Google Calendar</a>";
    exit;
}

// Si ya existe el token, cargamos el cliente
$accessToken = json_decode(file_get_contents($tokenPath), true);
$client->setAccessToken($accessToken);

echo "✅ ¡Token detectado y cargado correctamente!";