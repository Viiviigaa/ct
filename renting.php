<?php
    include 'conectar.php';
    include 'mostrarAlojamientos.php';
    include 'login.php';
    session_start(); 
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    $pagina_actual = basename($_SERVER['PHP_SELF']);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Canary Travel - Alquiler de Coches</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <header>   
        <img src="static/img/lista.png" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample" width="30" style="cursor: pointer;"> 
        <div class="offcanvas-body d-flex flex-column">
            <ul class="list-group">
                <li class="list-group-item <?php echo ($pagina_actual == 'informacionCuenta.php') ? 'active' : ''; ?>">
                    <a href="informacionCuenta.php" class="nav-link <?php echo ($pagina_actual == 'informacionCuenta.php') ? 'text-white' : 'text-dark'; ?>">
                        Mi cuenta
                    </a>
                </li>
                <li class="list-group-item <?php echo ($pagina_actual == 'misReservas.php') ? 'active' : ''; ?>">
                    <a href="misReservas.php" class="nav-link <?php echo ($pagina_actual == 'misReservas.php') ? 'text-white' : 'text-dark'; ?>">
                        Mis Reservas
                    </a>
                </li>
                <li class="list-group-item <?php echo ($pagina_actual == 'recomendaciones.php') ? 'active' : ''; ?>">
                    <a href="recomendaciones.php" class="nav-link <?php echo ($pagina_actual == 'recomendaciones.php') ? 'text-white' : 'text-dark'; ?>">
                        Recomendaciones
                    </a>
                </li>

            </ul>    
            <ul class="list-group mt-auto">
                <li class="list-group-item">
                    <a href="logout.php" class="text-danger" style="text-decoration: none;">Cerrar sesión</a>
                </li>
            </ul>
        </div>

        <a href="index.php" id="menuPrincipial" style="text-decoration: none; color: inherit; display: flex; align-items: center;">
            <img src="static/img/logo.png" alt="logo" id="logo" width="50">
            <h3 id="textoCabecera" style="margin-left: 10px;">Canary Travel</h3>
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
    <main>
        <nav>
            <ul class="navList">
                <li><a style='text-decoration: none; color:black;' href="busquedaVuelos.php">Vuelos</a></li>
                <li><a style='text-decoration: none; color:black;' href="renting.php">Alquiler de coches</a></li>
                <li><a style='text-decoration: none; color:black;' href="ferrys.php">Ferrys</a></li>
            </ul>
        </nav>
        <br>
        <h3 style='text-align: center'>Encuentra tu coche de alquiler al mejor precio</h3>
        <div id="buscador" class="container mt-4">
            <form action="" method="post"> <div class="row align-items-end justify-content-center">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Destino</label>
                        <select name="destinos" class="form-select">
                            <?php
                            try {
                                $conn = conectarBD();
                                $stmt = $conn->query("SELECT nombre FROM destinos");
                                $destinos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($destinos as $d) {
                                    $selected = (isset($_POST['destinos']) && $_POST['destinos'] == $d['nombre']) ? 'selected' : '';
                                    echo "<option value='" . htmlspecialchars($d['nombre']) . "' $selected>{$d['nombre']}</option>";
                                }
                            } catch (PDOException $e) {
                                echo "<option>Error de conexión</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Recogida</label>
                        <input type="date" name="fechaIni" class="form-control" value="<?php echo $_POST['fechaIni'] ?? ''; ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Devolución</label>
                        <input type="date" name="fechaFin" class="form-control" value="<?php echo $_POST['fechaFin'] ?? ''; ?>">
                    </div>
                    <div class="col-md-2">
                        <input class="btn btn-primary w-100" type="submit" name="buscar" value="Buscar">
                    </div>
                </div>
            </form>
        </div>

        <div class="text-center mt-3">
            <p><small>*Nota: Tanto la recogida como la devolución se realizarán en la terminal del aeropuerto.</small></p>
        </div>

        <?php
        // Procesamiento de datos de búsqueda
        $destinoSeleccionado = $_POST['destinos'] ?? null;
        $fechaIni = $_POST['fechaIni'] ?? null;
        $fechaFin = $_POST['fechaFin'] ?? null;
        
        $_SESSION['fechaInicioReservaCoche'] = $fechaIni;
        $_SESSION['fechaFinReservaCoche'] = $fechaFin;
        // Cálculo de días
        $totalDias = 1;
        if ($fechaIni && $fechaFin) {
            $date1 = new DateTime($fechaIni);
            $date2 = new DateTime($fechaFin);
            $diff = $date1->diff($date2);
            $totalDias = $diff->days > 0 ? $diff->days : 1;
        }

        // Solo mostramos si hay un destino seleccionado o se ha pulsado buscar
        if ($destinoSeleccionado) {
            $cochesEncontrados = mostrarCoches();

            $rol = getRol($_SESSION['usuario']);

            if (empty($cochesEncontrados)) {
                echo "<h2 class='text-center mt-5'>No se encontraron vehículos disponibles en " . htmlspecialchars($destinoSeleccionado) . ".</h2>";
            } else {
                echo "<div class='container mt-4'>";
                
                foreach ($cochesEncontrados as $coche) {
                    // Usamos la matrícula para crear un ID único para el carrusel de Bootstrap
                    $carouselId = "car_" . preg_replace('/[^A-Za-z0-9]/', '', $coche['matricula']);

                    if($rol === 'pro'){
                        $precioTotalCoche = round(($coche['precio'] * $totalDias)*0.9, 2);
                    }else{
                        $precioTotalCoche = $coche['precio'] * $totalDias;
                    }


                    $precioOriginal = number_format($coche['precio'], 2);
                    if ($rol === 'pro') {
                        $precioDescuento = round($coche['precio'] * 0.9, 2);
                        $precioHTML = "<del class='text-danger'>{$precioOriginal}€</del> <span class='fw-bold text-success'>{$precioDescuento}€</span>";
                    } else {
                        $precioHTML = "<span>{$precioOriginal}€</span>";
                    }
                    

                    echo "
                    <div class='card mb-4 shadow-sm' style='border-radius: 15px; overflow: hidden;'>
                        <div class='row g-0'>
                            <div class='col-md-4'>
                                <div id='{$carouselId}' class='carousel slide'>
                                    <div class='carousel-inner'>";
                                    
                                    $imagenes = explode(";", $coche['fotos']);
                                    foreach($imagenes as $index => $img) {
                                        $activeClass = ($index === 0) ? 'active' : '';
                                        $rutaImg = trim($img);
                                        echo "
                                        <div class='carousel-item {$activeClass}'>
                                            <img src='{$rutaImg}' class='img-fluid w-100' style='height: 250px; object-fit: cover;' alt='vehículo'>
                                        </div>";
                                    }

                                    echo "
                                    </div>
                                    <button class='carousel-control-prev' type='button' data-bs-target='#{$carouselId}' data-bs-slide='prev'>
                                        <span class='carousel-control-prev-icon' aria-hidden='true'></span>
                                    </button>
                                    <button class='carousel-control-next' type='button' data-bs-target='#{$carouselId}' data-bs-slide='next'>
                                        <span class='carousel-control-next-icon' aria-hidden='true'></span>
                                    </button>
                                </div>
                            </div>
                            
                            <div class='col-md-5 p-4'>
                                <h2 class='card-title'>{$coche['marca']} {$coche['modelo']}</h2>
                                <div class='mt-3'>
                                    <p class='mb-1'><strong>Asientos:</strong> {$coche['asientos']}</p> 
                                    <p class='mb-1'><strong>Transmisión:</strong> {$coche['transmision']}</p>
                                    <p class='mb-1'><strong>Combustible:</strong> {$coche['combustible']}</p> 
                                    <p class='text-muted'>Precio por día: " . $precioHTML . "</p>
                                </div>
                            </div>

                            <div class='col-md-3 d-flex flex-column justify-content-center align-items-center bg-light border-start'>
                                <h2 class='text-success mb-0'>" . number_format($precioTotalCoche, 2) . "€</h2>
                                <p class='text-muted mb-3'>Total por {$totalDias} días</p>
                                <a href='procesarRenting.php?marca={$coche['marca']}&modelo={$coche['modelo']}&dias={$totalDias}&precio=$precioTotalCoche' class='btn btn-primary btn-lg'>Reservar ahora</a>
                            </div>
                        </div>
                    </div>";
                }
                echo "</div>";
            }
        }
        ?>
    </main>
</body>
</html>