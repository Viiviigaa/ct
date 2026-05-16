<?php
session_start();

include 'conectar.php';
if (file_exists('reservas.php')) include 'reservas.php'; 

error_reporting(E_ALL);
ini_set('display_errors', 1);


function checkIdReserva($id) {
    $conn = conectarBD();
    $sql = "SELECT ID FROM reservas WHERE id = ?";
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

$type = $_GET['type'] ?? 'reserva';
$usuario = $_SESSION['usuario'] ?? null;

if (!$usuario) {
    header('Location: index.php');
    exit;
}

try {
    $conn = conectarBD();

    if ($type === 'suscripcion') {
        $nuevoRol = $_GET['plan'] ?? 'base';
        
        $sql = "UPDATE usuarios SET Rol = ? WHERE nombreUsuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$nuevoRol, $usuario]);
        
        $tituloMsg = "Suscripción Actualizada";
        $cuerpoMsg = "Ahora eres miembro <strong>" . strtoupper($nuevoRol) . "</strong>.";
        $redirigirA = "index.php";

    } else {
        if (!isset($_GET['id'], $_SESSION['fechaInicio'], $_SESSION['fechaFin'])) {
            header('Location: index.php');
            exit;
        }

        $fechaInicio = $_SESSION['fechaInicio'];
        $fechaFin    = $_SESSION['fechaFin'];
        $huespedes   = $_GET['huespedes'] ?? 0;
        $id_ver      = $_GET['id'];
        $id_reserva  = generarId();

        $resultado = insertarReserva($id_reserva, $id_ver, $fechaInicio, $fechaFin, $huespedes, $usuario);
        
        if ($resultado) {
            unset($_SESSION['fechaInicio']);
            unset($_SESSION['fechaFin']);
            $tituloMsg = "Reserva Completada";
            $cuerpoMsg = "Tu reserva se ha procesado con éxito.";
            $redirigirA = "index.php";
        } else {
            header('Location: index.php?error=reserva_fallida');
            exit;
        }
    }

} catch (Exception $e) {
    die("Error crítico en el procesamiento: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $tituloMsg; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa; 
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
            padding: 20px;
        }
        h1 { color: #28a745 !important; }
        .spinner { animation: spin 1s infinite linear; }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

    <div class="container text-center">
        <h1><?php echo $tituloMsg; ?> con Éxito</h1>
        <p><?php echo $cuerpoMsg; ?></p>
        <p>Redirigiendo...</p><br>
        <div class="spinner-border text-primary spinner" role="status">
            <span class="sr-only">Cargando...</span>
        </div>
    </div>

    <script>
        setTimeout(function() {
            window.location.href = '<?php echo $redirigirA; ?>';
        }, 3000);
    </script>
</body>
</html>