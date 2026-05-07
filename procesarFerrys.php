<?php
    include 'conectar.php';
    include 'mostrarAlojamientos.php';
    session_start(); 
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    $conn = conectarBD();
    $fechaInicio = $_SESSION['fechaInicioReservaCoche'] ?? null;
    $fechaFin = $_SESSION['fechaFinReservaCoche'] ?? null;

    $origen = $_GET['origen'] ?? null;
    $destino = $_GET['destino'] ?? null;
    $pasajeros = $_GET['pasajeros'] ?? null;
    $precioFinal = $_GET['precio'] ?? null;
    $dias = $_GET['dias'] ?? null;

    function insertarReservaFerry($conn, $origen, $destino, $pasajeros, $dni, $nombre, $apellidos, $fechaNacimiento, $precio, $telefono) {
    try {
        $sql = "INSERT INTO reservasFerrys (origen, destino, pasajeros, dni, nombre, apellidos, telefono, precio, fechaNacimiento) 
                VALUES (:origen, :destino, :pasajeros, :dni, :nombre, :apellidos, :telefono, :precio, :fechaNacimiento)";
        
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':origen', $origen);
        $stmt->bindParam(':destino', $destino);
        $stmt->bindParam(':pasajeros', $pasajeros, PDO::PARAM_INT);
        $stmt->bindParam(':dni', $dni);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellidos', $apellidos);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':fechaNacimiento', $fechaNacimiento);

        return $stmt->execute();

    } catch (PDOException $e) {
        echo "Error al insertar: " . $e->getMessage();
        return false;
    }
}

    $errores = []; 

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $nombre = $_POST['nombre'] ?? null; 
        $apellidos = $_POST['apellidos'] ?? null; 
        $fechaNacimiento = $_POST['fechaNac'] ?? null; 
        $numTlf = $_POST['numeroTlf'] ?? null; 
        $dni = $_POST['dni'] ?? null;

        if(empty($nombre)){
            $errores['nombre'] = "<h3 style='color:red'>Este campo no puede estar vacío</h3>";
        }
        if(empty($apellidos)){
            $errores['apellidos'] = "<h3 style='color:red'>Este campo no puede estar vacío</h3>";
        }

        if(empty($fechaNacimiento)){
            $errores['fecha'] = "<h3 style='color:red'>Este campo no puede estar vacío</h3>";
        } else {
            $fechaActual = new DateTime();
            $fNac = new DateTime($fechaNacimiento);
            $diff = $fechaActual->diff($fNac);
            if($diff->y < 18){
                $errores['fecha'] = "<h3 style='color:red'>Debe ser mayor de 18 años</h3>";
            }
        }

        // Validación del teléfono mediante expresión regular
        if (empty($numTlf)) {
            $errores['tlf'] = "<h3 style='color:red'>Este campo no puede estar vacío</h3>";
        } elseif (!preg_match('/^[0-9]+$/', $numTlf)) {
            $errores['tlf'] = "<h3 style='color:red'>Solo se admiten caracteres numéricos {0-9}</h3>";
        } elseif (strlen($numTlf) != 9) {
            $errores['tlf'] = "<h3 style='color:red'>La longitud debe ser de 9 dígitos</h3>";
        }

        if(strlen($dni) != 9){
            $errores['dni'] = "<h3 style='color:red'>La longitud del DNI es incorrecta</h3>";
        }

        if(empty($errores)){
            $resultado = insertarReservaFerry($conn, $origen, $destino, $pasajeros, $dni, $nombre, $apellidos, $fechaNacimiento, $precioFinal, $numTlf);
            if ($resultado) {
                echo "<div class='alert alert-success'>Reserva confirmada con éxito.</div>";
            } else {
                echo "<div class='alert alert-danger'>Hubo un error al guardar en la base de datos.</div>";
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Canary Travel - Reserva de ferrys</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<style>
form {
    max-width: 500px;
    margin: 40px auto;
    padding: 30px;
    background-color: #ffffff;
    border-radius: 15px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
.form-label {
    margin-bottom: 20px;
    text-align: left;
}
form label {
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
    display: inline-block;
}
form input[type="text"],
form input[type="date"],
form input[type="file"] {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-sizing: border-box; 
    transition: all 0.3s ease;
    font-size: 16px;
    background-color: #f9f9f9;
}
form input:focus {
    outline: none;
    border-color: #0d6efd;
    background-color: #fff;
    box-shadow: 0 0 8px rgba(13, 110, 253, 0.2);
}
form input[type="file"] {
    padding: 10px;
    background-color: #e9ecef;
    cursor: pointer;
}
</style>
<body>
    <header>   
        <img src="static/img/lista.png" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample" width="30" style="cursor: pointer;"> 
        
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
                    <li class="list-group-item"><a href="logout.php" style='text-decoration: none; color:black'>Cerrar sesion</a></li>
                </ul>
            </div>
        </div>

        <a href="index.php" id="menuPrincipial" style="text-decoration: none; color: inherit; display: flex; align-items: center;">
            <img src="static/img/logo.png" alt="logo" id="logo" width="50">
            <h3 id="textoCabecera" style="margin-left: 10px;">Canary Travel</h3>
        </a>
        
        <div class="logs">
            <a href="empresas.php" class="btn btn-primary">Empresas</a>
            <a href="sesion.php" class="btn btn-primary">Iniciar sesión</a>
            <a href="registro.php" class="btn btn-primary">Registrarse</a>
        </div>
    </header>

    <main>
        <form action="" method="POST" enctype="multipart/form-data">
            <h3 class="mb-4">Finaliza tu reserva</h3> 
            
            <div class="form-label">
                <label>Nombre</label>
                <input type="text" name="nombre" placeholder="Ej: Juan" value="<?= $_POST['nombre'] ?? '' ?>">
                <?= $errores['nombre'] ?? '' ?>
            </div>
            
            <div class="form-label">
                <label>Apellidos</label>
                <input type="text" name="apellidos" placeholder="Ej: Pérez Santana" value="<?= $_POST['apellidos'] ?? '' ?>">
                <?= $errores['apellidos'] ?? '' ?>
            </div>
            
            <div class="form-label">
                <label>Fecha de nacimiento</label>
                <input type="date" name="fechaNac" value="<?= $_POST['fechaNac'] ?? '' ?>">
                <?= $errores['fecha'] ?? '' ?>
            </div>

            <div class="form-label">
                <label>Documento de Identidad: </label>
                <input type="text" name="dni" value="<?= $_POST['dni'] ?? '' ?>">
                <?= $errores['dni'] ?? '' ?>
            </div>
        
            <div class="form-label">
                <label>Número de teléfono</label>
                <input type="text" name="numeroTlf" placeholder="600000000" value="<?= $_POST['numeroTlf'] ?? '' ?>">
                <?= $errores['tlf'] ?? '' ?>
            </div>
            <button type="submit" class="btn btn-primary w-100 mt-3 shadow-sm">Reservar</button>
        </form>
    </main>
</body>
</html>