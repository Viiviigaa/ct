<?php
session_start();
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

$type = $_GET['type'] ?? 'reserva';
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numeroTarjeta = trim($_POST['numeroTarjeta'] ?? '');
    $fechaTarjeta  = trim($_POST['fechaTarjeta'] ?? '');
    $cvv = trim($_POST['cvv'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');

    if (empty($numeroTarjeta)) {
        $errores['numeroTarjeta'] = "El número de tarjeta es obligatorio.";
    } else if (strlen($numeroTarjeta) < 13 || strlen($numeroTarjeta) > 19) {
        $errores['numeroTarjeta'] = "Longitud no válida.";
    }else if(!preg_match('/^[0-9]+$/', $numeroTarjeta)){
         $errores['numeroTarjeta'] = "El numero de tarjeta no puede contener letras";
    }

    if (empty($fechaTarjeta)) {
        $errores['fechaTarjeta'] = "La fecha es obligatoria.";
    } else {
        $fechaActual = new DateTime('first day of this month');
        $fechaExpedicion = DateTime::createFromFormat('Y-m', $fechaTarjeta);
        if ($fechaExpedicion < $fechaActual) {
            $errores['fechaTarjeta'] = "La tarjeta está caducada.";
        }
    }

    if (empty($cvv) || (strlen($cvv) < 3) || (strlen($cvv) > 4)) {
        $errores['cvv'] = "CVV no válido.";
    }else if(!preg_match('/^[0-9]+$/', $cvv)){
        $errores['cvv'] = "CVV no válido.";
    }

    if (empty($nombre)) {
        $errores['nombre'] = "El nombre es obligatorio.";
    }

    if (empty($errores)) {
        if ($type === 'suscripcion') {
            header("Location: correcto.php?type=suscripcion&plan=" . urlencode($_GET['plan']));
        } else {
            header("Location: correcto.php?type=reserva&id=" . urlencode($_GET['id']) . "&huespedes=" . urlencode($_GET['huespedes']));
        }
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pasarela de Pago</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .payment-form { max-width: 400px; margin: 50px auto; padding: 30px; background: #fff; border-radius: 5px; box-shadow: 0px 0px 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
<div class="container">
    <form class="payment-form" action="" method="post">
        <h2 class="text-center mb-4">
            <?php echo ($type === 'suscripcion') ? "Pagar Suscripción " . ucfirst($_GET['plan']) : "Realizar Pago Reserva"; ?>
        </h2>
        
        <div class="form-group">
            <label>Número de tarjeta</label>
            <input type="text" class="form-control" name="numeroTarjeta">
            <?php if (isset($errores['numeroTarjeta'])) echo "<small class='text-danger'>{$errores['numeroTarjeta']}</small>"; ?>
        </div>

        <div class="form-group">
            <label>Fecha de expiración</label>
            <input type="month" class="form-control" name="fechaTarjeta">
            <?php if (isset($errores['fechaTarjeta'])) echo "<small class='text-danger'>{$errores['fechaTarjeta']}</small>"; ?>
        </div>

        <div class="form-group">
            <label>CVV</label>
            <input type="text" class="form-control" name="cvv">
            <?php if (isset($errores['cvv'])) echo "<small class='text-danger'>{$errores['cvv']}</small>"; ?>
        </div>

        <div class="form-group">
            <label>Nombre en la tarjeta</label>
            <input type="text" class="form-control" name="nombre">
            <?php if (isset($errores['nombre'])) echo "<small class='text-danger'>{$errores['nombre']}</small>"; ?>
        </div> 
        <button type="submit" class="btn btn-primary btn-block">Confirmar Pago</button>
        <a href="index.php" style='text-align: center; text-decoration: none'>Volver al menú principal</a>
    </form>
</div>
</body>
</html>