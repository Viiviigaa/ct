<?php
include 'conectar.php';
session_start();
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
$conn = conectarBD();

function buscarUsuarioPass($conn, $username, $password)
{
    $query = "SELECT * FROM usuarios WHERE nombreUsuario = ? AND Contrasena = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$username, $password]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function iniciarSesion($conn, $username, $password)
{
    $usuario = buscarUsuarioPass($conn, $username, $password);

    if ($usuario) {
        $_SESSION['usuario'] = $usuario['nombreUsuario'];
        $_SESSION['dni'] = $usuario['dni'];
        $_SESSION['rol'] = $usuario['Rol'];
        return true;
    }
    return false;
}

function nuevoAlojamiento($conn, $nombre, $isla, $descripcion, $fotos, $precio, $huespedes, $direccion, $codigoEmpresa)
{
    try {
        $query = "INSERT INTO alojamientosPendientes (nombreAlojamiento, isla, descripcion, fotos, precio, direccion, max_huespedes, codigoEmpresa) 
                  VALUES (?, ?, ?, ?, ?, ?, ?,?)";
        $stmt = $conn->prepare($query);

        $resultado = $stmt->execute([
            $nombre,
            $isla,
            $descripcion,
            $fotos,
            $precio,
            $direccion,
            $huespedes,
            $codigoEmpresa
        ]);
        return $resultado;
    } catch (PDOException $e) {
        echo "Error al insertar: " . $e->getMessage();
        return false;
    }
}

function alojamientosEmpresa() {
    $conn = conectarBD();
    $query = "SELECT * FROM alojamientos WHERE codigoEmpresa = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$_SESSION['usuario']]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function eliminarAlojamiento($nombreAloj){
    $conn = conectarBD();
    $query = "DELETE from alojamientos where nombreAlojamiento = ?";
    $stmt = $conn->prepare($query); 
    $stmt->execute([$nombreAloj]);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $pass = $_POST['password'];

    if (iniciarSesion($conn, $username, $pass)) {
        if ($_SESSION['rol'] != 'business') {
            $msg = "<div class='alert alert-warning'>Acceso restringido: Solo para cuentas de empresa.</div>";
        } else {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    } else {
        $msg = "<div class='alert alert-danger'>Usuario o contraseña incorrectos.</div>";
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['enviarAlojamiento'])) {
        $nombre      = $_POST['nombreAlojamiento'];
        $isla        = $_POST['isla'];
        $direccion   = $_POST['direccion'];
        $descripcion = $_POST['descripcion'];
        $precio      = $_POST['precio'];
        $huespedes   = $_POST['max_huespedes'];
        $fotoNombre = $_FILES['fotos']['name'];

        nuevoAlojamiento($conn, $nombre, $isla, $descripcion,$fotoNombre, $precio,$huespedes,$direccion, $_SESSION['usuario']);
    }
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nombre_alojamiento'])){
    $nombreAloj = $_POST['nombre_alojamiento'];
    eliminarAlojamiento($nombreAloj);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <title>CanaryTravel</title>
</head>

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
        <a href="index.php" id="menuPrincipial">
            <img src="static/img/logo.png" alt="logo" id="logo">
            <h3 id="textoCabecera">Canary Travel</h3>
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
    <main class="admin-panel">
        <div class="container-fluid px-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Panel de Gestión: <?php echo $_SESSION['usuario']; ?></h1>
            </div>
        </div>
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
        <?php 
        $alojamientosEmpresa = alojamientosEmpresa();
        foreach($alojamientosEmpresa as $a){
            echo "<div class='col'>
            <div class='card h-100 border-0 shadow-sm custom-card'>         
                <div class='card-body p-4'>
                    <div class='d-flex justify-content-between align-items-start mb-2'>
                        <h5 class='card-title fw-bold text-dark mb-0'>{$a['nombreAlojamiento']}</h5>
                        <span class='badge rounded-pill bg-info-subtle text-info-emphasis border border-info-subtle'>{$a['isla']}</span>
                    </div>
                    
                    <p class='text-muted small mb-3'>
                        <i class='bi bi-geo-alt-fill'></i> <strong>Dirección:</strong> {$a['direccion']}
                    </p>
                    
                    <p class='card-text text-secondary small text-truncate-2'>
                        <strong>Descripción:</strong> <br> {$a['descripcion']}
                    </p>
                    <p class='card-text text-secondary small text-truncate-2'>
                        <strong>Precio: </strong>{$a['precio']} €/noche
                    </p>
                    <div class='d-flex gap-3 mt-3'>
                        <span class='small text-muted'><i class='bi bi-people'></i><strong>Número máximo de huéspedes: </strong>{$a['max_huespedes']}</span>
                    </div>
                </div>           
                <div class='card-footer bg-transparent border-top-0 p-4 pt-0'>
                    <div class='d-grid gap-2 d-md-flex justify-content-md-end'>
                        <form action='' method='post'>
                            <input type='hidden' name='nombre_alojamiento' value='{$a['nombreAlojamiento']}'>
                            <button type='submit' class='btn btn-outline-danger btn-sm px-3'>Eliminar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>";
        }
        ?>
    </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Añadir Nuevo Alojamiento</h5>
            </div>
            <div class="card-body p-4">
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="row g-3">
                        <!-- Primera Fila -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Nombre del Alojamiento</label>
                            <input type="text" name="nombreAlojamiento" class="form-control" placeholder="Ej: Villa Paraíso" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Isla</label>
                            <select name="isla" class="form-select" required>
                                <option value="Tenerife">Tenerife</option>
                                <option value="Gran Canaria">Gran Canaria</option>
                                <option value="Lanzarote">Lanzarote</option>
                                <option value="Fuerteventura">Fuerteventura</option>
                                <option value="La Palma">La Palma</option>
                                <option value="La Gomera">La Gomera</option>
                                <option value="El Hierro">El Hierro</option>
                                <option value="La Graciosa">La Graciosa</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Dirección Exacta</label>
                            <input type="text" name="direccion" class="form-control" placeholder="Calle y número" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Máx. Huéspedes</label>
                            <input type="number" name="max_huespedes" class="form-control" min="1" required>
                        </div>

                        <!-- Segunda Fila -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Descripción del Servicio</label>
                            <textarea name="descripcion" class="form-control" rows="1" placeholder="Breve descripción de las comodidades..."></textarea>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Precio / Noche (€)</label>
                            <input type="number" name="precio" class="form-control" placeholder="0.00" step="0.01" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Fotos del Local</label>
                            <input type="file" name="fotos" class="form-control" accept="image/*" required>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" name="enviarAlojamiento" class="btn btn-primary w-100 py-2">
                                <i class="bi bi-save"></i> Guardar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        </div>
    </main>
    <footer>

    </footer>
</body>
</html>