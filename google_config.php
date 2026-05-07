<?php


/* Configura y retorna el cliente de Google */
function obtenerClienteGoogle() {
    $client = new Google\Client();
    $client->setApplicationName('CanaryTravel System');
    $client->setAuthConfig(__DIR__ . '/credentials.json');
    $client->setAccessType('offline');
    $client->setPrompt('consent'); 
    $client->setScopes(Google\Service\Calendar::CALENDAR);

    $tokenPath = __DIR__ . '/token.json';

    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
    }

    // Si el token expiró, lo renovamos automáticamente
    if ($client->isAccessTokenExpired()) {
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            file_put_contents($tokenPath, json_encode($client->getAccessToken()));
        } else {
            // Si no hay refresh token, hay que re-autenticar (borra el token.json y recarga)
            return null;
        }
    }

    return $client;
}

/*Inserta un evento en el calendario*/

function insertarEventoGoogle($titulo, $descripcion, $fechaHora, $duracionMinutos = 60) {
    $client = obtenerClienteGoogle();
    if (!$client) return "Error: No hay conexión con Google.";

    $service = new Google\Service\Calendar($client);

    $event = new Google\Service\Calendar\Event([
        'summary' => $titulo,
        'description' => $descripcion,
        'start' => [
            'dateTime' => date('c', strtotime($fechaHora)),
            'timeZone' => 'Atlantic/Canary',
        ],
        'end' => [
            'dateTime' => date('c', strtotime($fechaHora . " + $duracionMinutos minutes")),
            'timeZone' => 'Atlantic/Canary',
        ],
    ]);

    try {
        $event = $service->events->insert('primary', $event);
        return $event->htmlLink; // Retorna el link al evento
    } catch (Exception $e) {
        return "Error: " . $e->getMessage();
    }
}

/* Obtiene y lista los próximos eventos*/

function listarEventosGoogle($cantidad = 5) {
    $client = obtenerClienteGoogle();
    if (!$client) return "<li>Por favor, vincula tu cuenta de Google.</li>";

    $service = new Google\Service\Calendar($client);
    
    $optParams = [
        'maxResults' => $cantidad,
        'orderBy' => 'startTime',
        'singleEvents' => true,
        'timeMin' => date('c'),
    ];

    try {
        $results = $service->events->listEvents('primary', $optParams);
        return $results->getItems();
    } catch (Exception $e) {
        return [];
    }
}