<?php
include 'conectar.php';
include 'mostrarAlojamientos.php';
include 'google_config.php';
include 'login.php';
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles alojamientos - Canary Travel</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<style>
    .popup{
        display: none; 
        position: fixed;
        top:0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        justify-content: center;
        align-items: center;
    }

    .popup-content{
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        width: 400px;
        text-align: center;
    }

    #closeBtn{
        float: right;
        cursor: pointer;
        font-size: 20px;
    }

    input{
        width: 100%;
        margin: 10px 0;
        padding: 8px;
    }

</style>
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
                    <li class="list-group-item"><a href="logout.php" style='text-decoration: none; color:black'>Cerrar sesion</a></li>
                </ul>
            </div>
        </div>
        <a href="index.php" id="menuPrincipial" style="text-decoration:none; color:inherit; display:flex; align-items:center; padding:10px;">
            <img src="static/img/logo.png" alt="logo" width="50">
            <h3 class="ms-2">Canary Travel</h3>
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
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre = trim($_POST['user'] ?? '');
        $contrasena = trim($_POST['pass'] ?? '');
        if (!isset($_SESSION['usuario'])){

            $destino = "procesarReserva.php?" . $_SERVER['QUERY_STRING'];
            loginUser($nombre, $contrasena, $destino);
        }
    }
 
    if (isset($_GET['id'])) {
    $totalEstancia = $_GET['estancia'] ?? 0;

    $huespedes = $_GET['huespedes'] ?? 0;
    $precioTotal = $_GET['precio'] ?? 0;
    $precio = $precioTotal/$totalEstancia;
    $id_ver = $_GET['id'];
    $alojamiento = infoAlojamiento($id_ver);

    if ($alojamiento) {
        $imagenes = explode(";", $alojamiento['fotos']);
        
        $url_mapa = "https://www.google.com/maps/search/?api=1&query=" . urlencode($alojamiento['direccion']);

        echo "<div class='container mt-4'>";
    
            echo "<div class='d-flex justify-content-between align-items-start mb-3'>
                    <div> <a href='{$url_mapa}' target='_blank'>
                        <h1>" . $alojamiento['nombreAlojamiento'] . "</h1>
                        <p class='text-primary small'><i class='bi bi-geo-alt-fill'></i> " . $alojamiento['direccion'] . " - Ubicación excelente - Ver mapa</p>
                    </div> </a>
                  </div>";

            echo "<div class='row g-2 mb-4' style='height: 450px;'>
                    <div class='col-md-6 h-100'>
                        <img src='".htmlspecialchars_decode(trim($imagenes[0]))."' class='w-100 h-100 object-fit-cover rounded-start' alt='Principal'>
                    </div>
                    <div class='col-md-6 h-100'>
                        <div class='row g-2 h-100'>
                            <div class='col-12 h-50'>
                                <img src='".htmlspecialchars_decode($imagenes[1] ?? $imagenes[0])."' class='w-100 h-100 object-fit-cover' alt='Foto 2'>
                            </div>
                            <div class='col-6 h-50'>
                                <img src='".htmlspecialchars_decode($imagenes[2] ?? $imagenes[0])."' class='w-100 h-100 object-fit-cover' alt='Foto 3'>
                            </div>
                            <div class='col-6 h-50 position-relative'>
                                <img src='".htmlspecialchars_decode($imagenes[3] ?? $imagenes[0])."' class='w-100 h-100 object-fit-cover rounded-end' alt='Foto 4'>
                                <div class='position-absolute top-50 start-50 translate-middle text-white fw-bold bg-dark bg-opacity-50 p-2 rounded'>
                                    +" . (count($imagenes) - 4) . " fotos
                                </div>
                            </div>
                        </div>
                    </div>
                  </div>";

            // SERVICIOS (Los cuadraditos que viste)
            echo "<div class='row mb-4'>
                    <div class='col-md-8'>
                        <div class='d-flex flex-wrap gap-2 mb-4'>
                            <div class='border p-2 rounded'><i class='bi bi-wifi'></i> WiFi gratis</div>
                            <div class='border p-2 rounded'><i class='bi bi-snow'></i> Aire acondicionado</div>
                            <div class='border p-2 rounded'><i class='bi bi-p-circle'></i> Parking</div>
                            <div class='border p-2 rounded'><i class='bi bi-tsunami'></i> Vistas al mar</div>
                        </div>
                        <h4 class='fw-bold'>Descripción</h4>
                        <p>{$alojamiento['descripcion']}</p>
                    </div>
                    
                    <div class='col-md-4'>
                        <div class='card shadow-sm border-primary'>
                            <div class='card-body'>
                                <h5 class='fw-bold'>Precio total de la estancia: </h5>
                                <p class='display-6 fw-bold text-primary'>{$precioTotal}€</p>
                                <p class='text-secondary'>{$huespedes} huéspedes - {$totalEstancia} días - {$precio}€ persona/noche";
                                
                                if(isset($_SESSION['usuario'])){
                                    echo "<a href='procesarReserva.php?id=" . $_GET['id'] . "&estancia=" . $totalEstancia . "&precio=" . $precioTotal . "&huespedes=" . $huespedes . "' class='btn btn-primary w-100 py-2'>Reservar ahora</a>";
                                }else{
                                    echo "<button id='loginBtn' onclick='loginPopup()' class='btn btn-primary'>Inicia sesión para continuar</button></a>";
                                }

                    echo "  </div>
                        </div>
                    </div>
                  </div>";
            
            
        echo "</div>";       
    }
}
?>
<div id="popup" class="popup">
    <div class="popup-content">
        <span id="closeBtn">&times;</span>
        <h2>Iniciar sesión</h2>
        <form action="detallesAlojamiento.php?<?php echo $_SERVER['QUERY_STRING']; ?>" method="post">
            <input type="text" name="user" id="user" placeholder="Introduce tu nombre de usuario">
            <input type="password" name="pass" id="pass" placeholder="Introduce tu contraseña">
            <button class="btn btn-primary" style='width: 100%' type="submit">Entrar</button>
        </form>
        <p>¿No tienes cuenta? Registrate<a href='registro.php'> aquí</a>
</body>
</html>
<script>
    const loginBtn = document.getElementById("loginBtn");
    const popup = document.getElementById("popup");
    const closeBtn = document.getElementById("closeBtn");

    loginBtn.onclick = function(){
        popup.style.display = "flex";
    }

    closeBtn.onclick = function(){
        popup.style.display = "none";
    }

    window.onclick = function(event){
        if(event.target == popup){
            popup.style.display = "none";
        }
    }
</script>
