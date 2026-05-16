<?php
    include 'conectar.php';
    include 'mostrarAlojamientos.php';
    include 'login.php';
    session_start();
    //error_reporting(E_ALL);
    //ini_set('display_errors', 1);
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
<style>
    .active {
        color: blue !important; /* El color que deseas */
        font-weight: bold;
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
                    <li class="list-group-item">
                        <a href="logout.php" style="text-decoration: none; color: black">Cerrar sesión</a>
                    </li>
                </ul>
            </div>
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
                <li><a style='text-decoration: none; color:black;' href="index.php">Alojamientos</a></li>
                <li><a style='text-decoration: none; color:black;' href="busquedaVuelos.php">Vuelos</a></li>
                <li><a style='text-decoration: none; color:black;' href="renting.php">Alquiler de coches</a></li>
                <li><a class="active" style='text-decoration: none; color:black;' href="ferrys.php">Ferrys</a></li>
            </ul>
        </nav>
        <br>
        <h3 style='text-align: center' >Encuentra tu ferry para moverte entre las islas con facilidad</h3>
        <div id="buscador" class="container mt-4">
            <form action="" method="post"> <div class="row align-items-end justify-content-center">
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Origen</label>
                        <select name="origen" class="form-select">
                            <?php
                            try {
                                $conn = conectarBD();
                                $stmt = $conn->query("SELECT nombre FROM destinos");
                                $destinos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($destinos as $d) {
                                    $selected = (isset($_POST['origen']) && $_POST['origen'] == $d['nombre']) ? 'selected' : '';
                                    echo "<option value='" . htmlspecialchars($d['nombre']) . "' $selected>{$d['nombre']}</option>";
                                }
                            } catch (PDOException $e) {
                                echo "<option>Error de conexión</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-2">
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
                        <label class="form-label fw-bold">Fecha de salida</label>
                        <input type="date" name="fechaSalida" id="ida" class="form-control" value="<?php echo $_POST['fechaSalida'] ?? ''; ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Pasajeros</label>
                        <input type="number" min="1" name="pasajeros" id="pasajeros"  class="form-control" value="<?php echo $_POST['pasajeros'] ?? ''; ?>">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label fw-bold">Vehículo: </label>
                        <input type="checkbox" name="vehiculo" class="form-checkbox">
                    </div>
                    <div class="col-md-2">
                        <input class="btn btn-primary w-100" type="submit" name="buscar" value="Buscar">
                    </div>
                </div>
            </form>
        </div>
        <?php
        // Procesamiento de datos de búsqueda
        $origenSeleccionado = $_POST['origen'] ?? null;
        $destinoSeleccionado = $_POST['destinos'] ?? null;
        $pasajeros = $_POST['pasajeros'] ?? null;
        $pasajeros = (int) $pasajeros; 
        $fechaSalida = $_POST['fechaSalida'] ?? null;
        $vehiculo = $_POST['vehiculo'] ?? null;

        $rol = getRol($_SESSION['usuario']);

        // Solo mostramos si hay un destino seleccionado o se ha pulsado buscar
        if ($origenSeleccionado && $destinoSeleccionado) {
            $ferrysEncontrados = mostrarFerrys();

            if (empty($ferrysEncontrados)) {
                echo "<h2 class='text-center mt-5'>No se encontraron ferrys disponibles desde " . htmlspecialchars($origenSeleccionado) . " hasta " . htmlspecialchars($destinoSeleccionado) .".</h2>";
            } else {
                echo "<div class='container mt-4'>";
                
                foreach ($ferrysEncontrados as $ferry) {
                    $fechaForm = substr($ferry['fechaSalida'],0,10); 

                    if($fechaSalida == $fechaForm && $origenSeleccionado == $ferry['origen'] && $destinoSeleccionado == $ferry['destino']){
                    
                    $precioBasePorPasajero = $ferry['precio'];
                    $precioTotalNormal = $precioBasePorPasajero * $pasajeros;

                    if ($vehiculo) {
                        $precioTotalNormal += 20;
                    }

                    // Precio por pasajero (izquierda)
                    if ($rol === 'pro') {
                        $mostrarPrecioPorPasajero = "
                            <span class='text-danger text-decoration-line-through'>". number_format($precioBasePorPasajero, 2) ."€</span>
                            <span class='text-success fw-bold'>". number_format(round($precioBasePorPasajero * 0.9, 2), 2) ."€</span>
                        ";
                        $precioFinal = round($precioTotalNormal * 0.9, 2);
                        $mostrarPrecio = "
                            <span class='text-danger text-decoration-line-through fs-5'>". number_format($precioTotalNormal, 2) ."€</span>
                            <span class='text-success fw-bold fs-3'>". number_format($precioFinal, 2) ."€</span>
                            <br><small class='text-success'>10% descuento pro</small>
                        ";
                    } else {
                        $mostrarPrecioPorPasajero = number_format($precioBasePorPasajero, 2) . "€";
                        $precioFinal = $precioTotalNormal;
                        $mostrarPrecio = "<span class='text-success fw-bold fs-3'>". number_format($precioFinal, 2) ."€</span>";
                    }
                    

                        echo "
                        <div class='card mb-4 shadow-sm' style='border-radius: 15px; overflow: hidden;'>
                        <div class='row g-0'>                        
                            <div class='col-md-8 p-4'>
                                <h2 class='card-title'>Salida: {$ferry['origen']} => Llegada: {$ferry['destino']}</h2>
                                <div class='mt-3'>
                                    <p class='mb-1'><strong>Fecha de salida:</strong> {$ferry['fechaSalida']}</p> 
                                    <p class='text-muted'>Precio por pasajero: {$mostrarPrecioPorPasajero}</p>
                                </div>
                            </div>

                            <div class='col-md-4 d-flex flex-column justify-content-center align-items-center bg-light border-start'>
                            <h2 class='text-success mb-0'>{$mostrarPrecio}</h2>
                            <p class='text-muted mb-3'>Total por {$pasajeros} pasajeros</p>
                            <a href='procesarFerrys.php?origen={$ferry['origen']}&destino={$ferry['destino']}&pasajeros={$pasajeros}&precio={$precioFinal}' class='btn btn-primary btn-lg'>Reservar ahora</a>
                        </div>
                    </div>";
                    }
                }
                echo "</div>";
            }
        }
        ?>
    </main>
</body>
<script>
    const ida = document.getElementById("ida");
    const vuelta = document.getElementById("vuelta");
    const hoy = new Date().toISOString().split('T')[0];
    ida.setAttribute('min', hoy);
    vuelta.setAttribute('min', hoy);
    ida.addEventListener("change", () => {
        const fechaSeleccionada = ida.value;
        vuelta.setAttribute('min', fechaSeleccionada);
    });
</script>
</html>