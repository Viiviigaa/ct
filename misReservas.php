<?php
    include 'conectar.php';
    include 'mostrarAlojamientos.php';
    session_start(); 
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    $conn = conectarBD();

    $dni = isset($_SESSION['dni']) ? $_SESSION['dni'] : null;

    function buscarUsuarioPass($conn, $username, $password){
        $query = "SELECT * FROM usuarios WHERE nombreUsuario = ? AND Contrasena = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$username, $password]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function iniciarSesion($conn, $username, $password){
        $usuario = buscarUsuarioPass($conn, $username, $password); 
        
        if($usuario) {
            $_SESSION['usuario'] = $usuario['nombreUsuario'];
            $_SESSION['dni'] = $usuario['dni']; 
            $_SESSION['rol'] = $usuario['Rol'];
            return true;
        }     
        return false;
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $username = $_POST['username'];
        $pass = $_POST['password']; 

        if(iniciarSesion($conn, $username, $pass)){
            $dni = $_SESSION['dni'];
        }
    }

    function listarReservasAlojamientos($conn, $dni){
        $query = "SELECT u.nombreUsuario, u.dni, r.id AS id_reserva,
        r.fechaInicio, r.fechaFinal, a.nombreAlojamiento, a.isla, a.direccion, a.precio AS precio_noche
        FROM usuarios u
        INNER JOIN reservas r ON u.dni = r.dniReserva
        INNER JOIN alojamientos a ON r.idAlojamiento = a.ID
        WHERE u.dni = ?";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([$dni]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function listarReservasVuelos($nombreUsuario){
        $conn = conectarBD();
        $query = "SELECT * from reservasVuelos where nombreUsuario = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$nombreUsuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function listarReservasCoches($conn, $nombreUsuario) {
        $query = "SELECT ID_Reserva, matricula , precio, nombre, apellidos, nombreUsuario , marca, modelo, telefono from reservasCarRental 
                where nombreUsuario = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$nombreUsuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function listarReservasFerrys($conn, $nombreUsuario){
        $query = "SELECT * FROM reservasFerrys where nombreUsuario = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$nombreUsuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Canary Travel - Mis Reservas</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        form { max-width: 500px; margin: 40px auto; padding: 30px; background-color: #ffffff; border-radius: 15px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        form label { font-weight: 600; color: #333; margin-bottom: 8px; display: inline-block; }
        form input[type="text"], form input[type="password"] { width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; margin-bottom: 15px; }
        form input[type="submit"]{ width: 100%; padding: 12px 15px; border-radius: 8px; }
    </style>
</head>
<body>
    <header>   
        <img src="static/img/lista.png" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" width="30" style="cursor: pointer;"> 
        <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample">  
            <div class="offcanvas-header">    
                <h5 class="offcanvas-title">Menú Lateral</h5>    
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>  
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

        <a href="index.php" style="text-decoration: none; color: inherit; display: flex; align-items: center;">
            <img src="static/img/logo.png" alt="logo" width="50">
            <h3 style="margin-left: 10px;">Canary Travel</h3>
        </a>
    </header>

    <main>
        <?php
            if(!isset($_SESSION['usuario'])){
                echo "<div>
                        <form action='' method='post'>
                            <h3>Iniciar sesión</h3>
                            <label>Nombre de usuario: </label><br>
                            <input type='text' name='username'><br>
                            <label>Contraseña: </label><br>
                            <input type='password' name='password'><br>
                            <input type='submit' class='btn btn-primary' value='Iniciar sesión'>
                        </form>
                    </div>";
            } else {
                echo "<div class='container mt-4'>
                <ul class='nav nav-tabs' id='myTab' role='tablist'>
                    <li class='nav-item'><button class='nav-link active' data-bs-toggle='tab' data-bs-target='#alojamientos' type='button' role='tab'>Alojamientos</button></li>
                    <li class='nav-item'><button class='nav-link' data-bs-toggle='tab' data-bs-target='#vuelos' type='button' role='tab'>Vuelos</button></li>
                    <li class='nav-item'><button class='nav-link' data-bs-toggle='tab' data-bs-target='#coches' type='button' role='tab'>Coches</button></li>
                    <li class='nav-item'><button class='nav-link' data-bs-toggle='tab' data-bs-target='#ferrys' type='button' role='tab'>Ferrys</button></li>
                </ul>
                <div class='tab-content p-4 bg-white shadow-sm' style='border: 1px solid #dee2e6; border-top: none; border-radius: 0 0 10px 10px;'>
                    
                    <div class='tab-pane fade show active' id='alojamientos' role='tabpanel'>
                        <h4>Reservas de alojamientos</h4>";
                        $reservas = listarReservasAlojamientos($conn, $_SESSION['usuario']); 
                        if(!empty($reservas)){
                            foreach($reservas as $r){
                                $fechaInicio = date('d-m-Y', strtotime($r['fechaInicio']));
                                $fechaFinal = date('d-m-Y', strtotime($r['fechaFinal']));
                                
                                $fechaInicioDiff = new DateTime($r['fechaInicio']);
                                $fechaFinalDiff =  new DateTime($r['fechaFinal']);
                                $diff = $fechaInicioDiff->diff($fechaFinalDiff);
                                $precio = (float) $r['precio_noche'];
                                $precioTotal = $precio * $diff->days;
                                echo "
                                <div class='card mb-3 shadow-sm border-0' style='border-radius: 12px;'>
                                    <div class='row g-0 align-items-center'>
                                        <div class='col-md-1 bg-light d-flex align-items-center justify-content-center py-3'>
                                            <strong>#{$r['id_reserva']}</strong>
                                        </div>
                                        <div class='col-md-5 px-4'>
                                            <h5 class='mb-1'>{$r['nombreAlojamiento']}</h5>
                                            <small class='text-muted'>{$r['isla']} - {$r['direccion']}</small>
                                        </div>
                                        <div class='col-md-4 text-center'>
                                            <span class='fw-bold'>$fechaInicio</span> → <span class='fw-bold'>$fechaFinal</span>
                                        </div>
                                        <div class='col-md-2 text-center'>
                                            <span class='fs-5 fw-bold'>{$precioTotal}€</span>
                                        </div>
                                    </div>
                                </div>";
                            }
                        } else { echo "<p>No hay reservas de alojamientos.</p>"; }
                echo "</div>

                    <div class='tab-pane fade' id='vuelos' role='tabpanel'>
                        <h4>Reservas de vuelos</h4>";
                        $reservasVuelos = listarReservasVuelos($_SESSION['usuario']);
                        if(!empty($reservasVuelos)){
                            foreach($reservasVuelos as $rv){
                                echo "
                                <div class='card mb-3 shadow-sm border-0' style='border-radius: 12px;'>
                                    <div class='row g-0 align-items-center'>
                                        <div class='col-md-1 bg-light d-flex align-items-center justify-content-center py-3'>
                                            <strong>ID Reserva: {$rv['id']}</strong>
                                        </div>
                                        <div class='col-md-5 px-4'>
                                            <h5 class='mb-1'>{$rv['salida']}</h5>
                                            <h5 class='mb-1'>{$rv['destino']}</h5>
                                        </div>
                                        <div class='col-md-4 text-center'>
                                            <small>Duración de vuelo estimada: {$rv['tiempoVuelo']}</small>
                                        </div>
                                        <div class='col-md-2 text-center'>
                                            <small><strong>Coste: {$rv['coste']}€ </strong></small>
                                        </div>
                                    </div>
                                </div>";
                            }
                        }else{
                            echo "<p>No hay vuelos programados.</p>";
                        }
                    echo "</div>
                    <div class='tab-pane fade' id='coches' role='tabpanel'>
                        <h4>Reservas de coches</h4>";
                        $reservasCoches = listarReservasCoches($conn, $_SESSION['usuario']); 
                        if(!empty($reservasCoches)){
                            foreach($reservasCoches as $rc){
                                echo "
                                <div class='card mb-3 shadow-sm border-0' style='border-radius: 12px;'>
                                    <div class='row g-0 align-items-center'>
                                        <div class='col-md-1 bg-light d-flex align-items-center justify-content-center py-3'>
                                            <strong>ID Reserva: {$rc['ID_Reserva']}</strong>
                                        </div>
                                        <div class='col-md-5 px-4'>
                                            <h5 class='mb-1'>{$rc['marca']} {$rc['modelo']}</h5>
                                            <p class='mb-0 text-muted'>Matrícula: {$rc['matricula']}</p>
                                        </div>
                                        <div class='col-md-4 text-center'>
                                            <small>Titular: {$rc['nombre']} {$rc['apellidos']}</small>
                                        </div>
                                        <div class='col-md-2 text-center'>
                                            <span class='fs-5 fw-bold'>{$rc['precio']}€</span>
                                        </div>
                                    </div>
                                </div>";
                            }
                        } else { echo "<p>No hay reservas de coches pendientes.</p>"; }
                echo "</div>
                    <div class='tab-pane fade' id='ferrys' role='tabpanel'>
                        <h4>Reservas de ferrys</h4>";
                        $reservasFerrys = listarReservasFerrys($conn, $_SESSION['usuario']); 
                        if(!empty($reservasFerrys)){
                            foreach($reservasFerrys as $rf){
                                echo "
                                <div class='card mb-3 shadow-sm border-0' style='border-radius: 12px;'>
                                    <div class='row g-0 align-items-center'>
                                        <div class='col-md-1 bg-light d-flex align-items-center justify-content-center py-3'>
                                            <strong>#{$rf['ID_Reserva']}</strong>
                                        </div>
                                        <div class='col-md-5 px-4'>
                                            <h5 class='mb-1'>{$rf['nombre']} {$rf['apellidos']}</h5>
                                            <small class='text-muted'>Número de telefono: {$rf['telefono']}</small>
                                        </div>
                                        <div class='col-md-4 text-center'>
                                            <span class='fw-bold'>{$rf['origen']}</span> → <span class='fw-bold'>{$rf['destino']}</span>
                                        </div>
                                        <div class='col-md-2 text-center'>
                                            <span class='fs-5 fw-bold'>{$rf['precio']}€</span>
                                        </div>
                                    </div>
                                </div>";
                            }
                        } else { echo "<p>No hay reservas de ferrys pendientes.</p>"; }
                        echo "</div>
                    </div>
                </div>
                </div>";
            }
        ?>
    </main>
</body>
</html>