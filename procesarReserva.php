<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procesar reserva</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
        <header>
        <img src="static/img/lista.png"  data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample" width="30"> 
        <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">  <div class="offcanvas-header">    
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
        <a href="index.php" id="menuPrincipial">
            <img src="static/img/logo.png" alt="logo" id="logo">
            <h3 id="textoCabecera">Canary Travel</h3>
        </a>
    </header>
    <?php
    include 'conectar.php'; 
    session_start();

    if (!isset($_SESSION['usuario'])) {
        die("Error: No hay una sesión activa. Por favor, inicia sesión.");
    }

    $id_alojamiento = $_GET['id'] ?? null;
    $estancia = $_GET['estancia'] ?? 0;
    $precioTotal = $_GET['precio'] ?? 0;
    $huespedes = $_GET['huespedes'] ?? 0;

    if (!$id_alojamiento) {
        die("Error: No se ha seleccionado ningún alojamiento.");
    }

    $usuario = $_SESSION['usuario'];

    function detallesAlojamiento($id){
        try {
        $conn = conectarBD(); 
        // Buscamos los datos del usuario logueado
        $query = "SELECT * FROM alojamientos WHERE ID = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute(); 
        $resultados = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultados;
        }catch(PDOException $e){
            echo "No se ha podido obtener la información requerida" . $e->getMessage();
        }
    }
    try {
        $conn = conectarBD(); 
        $query = "SELECT * FROM usuarios WHERE nombreUsuario = :user";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user', $usuario);
        $stmt->execute(); 
        
        $datosUsuario = $stmt->fetch(PDO::FETCH_ASSOC);
        $datosAlojamiento = detallesAlojamiento($id_alojamiento);

        if ($datosUsuario) {
    echo "
    <div class='container my-5'>
        <div class='row justify-content-center'>
            <div class='col-md-8 col-lg-6'>
                <div class='card shadow-lg border-0 rounded-2'>
                    <div class='card-header bg-primary text-white p-4 text-center'>
                         <h3>Detalles de la reserva</h3>
                    </div>
                    <div class='card-body p-4'>
                        <div class='row mb-4'>
                            <div class='col-12'>
                                <h5 class='text-primary border-bottom pb-2'>Datos del Cliente</h5>
                                <p class='mb-1'><strong>Nombre completo:</strong> {$datosUsuario['nombre']} {$datosUsuario['apellidos']}</p>
                                <p class='mb-0'><strong>Usuario:</strong> $usuario</p>
                                <br>
                                <h5 class='text-primary border-bottom pb-2'>Datos del alojamiento</h5>
                                <p class='mb-1'><strong>Nombre alojamiento:</strong> {$datosAlojamiento['nombreAlojamiento']}</p>
                                <p class='mb-0'><strong>Direccion:</strong> {$datosAlojamiento['direccion']}, {$datosAlojamiento['isla']}</p>
                                <p class='mb-0'><strong>Huéspedes máximos:</strong> {$datosAlojamiento['max_huespedes']}</p>
                                <br>
                                <h5 class='text-primary border-bottom pb-2'>Datos de la estancia</h5>
                                <p class='mb-0'><strong>Huéspedes:</strong> {$huespedes}</p>
                                <p class='mb-0'><strong>Tiempo total de la estancia:</strong> {$estancia} dias</p>
                                <br>
                                <button class='btn btn-primary w-100 p-4'><a href='pago.php?id=" . $_GET['id'] . "&huespedes=" . $huespedes . "' class='text-white text-center fs-2 fw-bold' style='text-decoration: none;'>Pagar: {$precioTotal}€</a></button>
                                <br><br>
                                <a href='detallesAlojamiento.php?id=" . htmlspecialchars($id_alojamiento) . "&estancia=" . urlencode($estancia) . "&precio=" . urlencode($precioTotal) . "&huespedes=" . urlencode($huespedes) . "' 
                                    class='d-block text-center text-decoration-none text-black fw-bold'>
                                    Volver a detalles
                                </a>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>";
}

    } catch(PDOException $e) {
        die("Error en la base de datos: " . $e->getMessage());
    }
?>
</body>
</html>