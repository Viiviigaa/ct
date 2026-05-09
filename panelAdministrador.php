<?php
include 'conectar.php';
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: index.php");
    exit();
}

function recomendacionesPorAprobar()
{
    $conn = conectarBD();
    $query = "SELECT  * from recomendacionesPendientes";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $resultado;
}

function listadoUsuarios()
{
    $conn = conectarBD();
    $query = "SELECT * FROM usuarios";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $resultado;
}

function listadoReservasVigentes()
{
    $conn = conectarBD();
    $hoy = new DateTime();
    $hoy =  $hoy->format('Y-m-d');
    $query = "SELECT * FROM reservas where fechaInicio<= ? and fechaFin >=?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$hoy, $hoy]);
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $resultado;
}

function listadoAlojamientos()
{
    $conn = conectarBD();
    $query = "SELECT * FROM alojamientos";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $resultado;
}

function listadoAlojamientosPorAprobar()
{
    $conn = conectarBD();
    $query = "SELECT * FROM alojamientosPendientes";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $resultado;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - Canary Travel</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #menuPrincipial {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: black;
        }

        #logo {
            width: 50px;
            margin-right: 10px;
        }

        .logs .btn {
            margin-left: 5px;
        }

        /* Ajustes para el Menú Lateral (Claro) */
        .offcanvas {
            background-color: #f8f9fa;
            border-right: 1px solid #dee2e6;
        }

        .list-group-item {
            background-color: transparent;
            border: none;
        }

        .list-group-item a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
        }

        .list-group-item a:hover {
            color: #0d6efd;
        }

        /* Cuerpo del Panel */
        body {
            background-color: #f4f7f6;
        }

        .admin-section {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 40px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border: 1px solid #e9ecef;
        }

        .section-title {
            border-left: 5px solid #0d6efd;
            padding-left: 15px;
            margin-bottom: 25px;
        }
    </style>
</head>

<body>
    <header>
        <img src="static/img/lista.png" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample" width="30" style="cursor:pointer">
        <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasExampleLabel">Gestión Administrador</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
            </div>
            <div class="offcanvas-body d-flex flex-column">
                <ul class="list-group">
                    <li class="list-group-item"><strong>NAVEGACIÓN PANEL</strong></li>
                    <li class="list-group-item"><a href="#sec-recomendaciones">Aceptar/Denegar Recomendaciones</a></li>
                    <li class="list-group-item"><a href="#sec-usuarios">Gestión de Usuarios</a></li>
                    <li class="list-group-item"><a href="#sec-reservas">Control de Reservas</a></li>
                    <li class="list-group-item"><a href="#sec-alojamientos">Alojamientos</a></li>
                    <hr>
                    <li class="list-group-item"><strong>NAVEGACIÓN DE LA APLICACIÓN</strong></li>
                    <li class="list-group-item"><a href="index.php">Alojamientos</a></li>
                    <li class="list-group-item"><a href="recomendaciones.php">Recomendaciones Públicas</a></li>
                    <li class="list-group-item"><a href="busquedaVuelos.php">Vuelos</a></li>
                    <li class="list-group-item"><a href="renting.php">Alquiler de coches</a></li>
                    <li class="list-group-item"><a href="ferrys.php">Ferrys</a></li>
                </ul>
                <ul class="list-group mt-auto">
                    <li class="list-group-item"><a href="logout.php" style='text-decoration: none; color:black'>Cerrar sesión</a></li>
                </ul>
            </div>
        </div>

        <a href="index.php" id="menuPrincipial">
            <img src="static/img/logo.png" alt="logo" id="logo">
            <h3 id="textoCabecera" class="mb-0">Canary Travel</h3>
        </a>

        <div class="logs">
            <button class="btn btn-primary"><a href="empresas.php" style='text-decoration: none; color:white;'>Empresas</a></button>
            <button class="btn btn-primary"><a href="sesion.php" style='text-decoration: none; color:white;'>Iniciar sesión</a></button>
            <button class="btn btn-primary"><a href="registro.php" style='text-decoration: none; color:white;'>Registrarse</a></button>
        </div>
    </header>
    <div class="container mt-5">
        <div id="sec-recomendaciones" class="admin-section">
            <h3 class="section-title">Moderación de Recomendaciones</h3>
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Titulo</th>
                        <th>Descripcion</th>
                        <th>Imagen</th>
                        <th>Precio</th>
                        <th>Lugar</th>
                        <th>Tipo de actividad</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $recommend = recomendacionesPorAprobar();
                    foreach ($recommend as $r) {
                        echo "
                        <tr>
                            <td>{$r['titulo']}</td>
                            <td>{$r['descripcion']}</td>
                            <td>{$r['imagen']}</td>
                            <td>{$r['precio']}</td>
                            <td>{$r['lugar']}</td>
                            <td>{$r['tipoActividad']}</td>
                            <td>
                                <form method='post' action=''>
                                    <input type='hidden' name='recomendacionPendiente' value='Aprobada'>
                                    <button class='btn btn-success btn-sm'>Publicar</button>
                                </form>
                            </td>
                            <td>
                                <form method='post' action=''>
                                    <input type='hidden' name='recomendacionPendiente' value='Rechazada'>
                                    <button class='btn btn-success btn-sm'>Publicar</button>
                                </form>
                            </td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="container mt-5">
        <div id="sec-recomendaciones" class="admin-section">
            <h3 class="section-title">Moderación de alojamientos</h3>
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre del alojamiento</th>
                        <th>Isla</th>
                        <th>Descripción</th>
                        <th>Fotos</th>
                        <th>Precio</th>
                        <th>Dirección</th>
                        <th>Cantidad máxima de huéspedes</th>
                        <th>Código de empresa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $alojamientos = listadoAlojamientosPorAprobar();
                    foreach ($alojamientos as $a) {
                        echo "
                        <tr>
                            <td>{$a['ID']}</td>
                            <td>{$a['nombreAlojamiento']}</td>
                            <td>{$a['Isla']}</td>
                            <td>{$a['descripcion']}</td>
                            <td>{$a['fotos']}</td>
                            <td>{$a['precio']}</td>
                            <td>{$a['direccion']}</td>
                            <td>{$a['max_huespedes']}</td>
                            <td>{$a['codigoEmpresa']}</td>
                            <td>
                                <form method='get' action=''>
                                    <input type='hidden' name='alojamientoPendiente' value='Aprobada'>
                                    <button class='btn btn-success btn-sm'>Publicar</button>
                                </form>
                            </td>
                            <td>
                                <form method='get' action=''>
                                    <input type='hidden' name='alojamientoPendiente' value='Rechazada'>
                                    <button class='btn btn-success btn-sm'>Publicar</button>
                                </form>
                            </td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="container mt-5">
        <div id="sec-recomendaciones" class="admin-section">
            <h3 class="section-title">Gestión de usuarios</h3>
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nombre de usuario</th>
                        <th>Nombre</th>
                        <th>Apellidos</th>
                        <th>DNI</th>
                        <th>Correo</th>
                        <th>Telefono</th>
                        <th>Fecha de nacimiento</th>
                        <th>Rol</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $usuarios = listadoUsuarios();
                    foreach ($usuarios as $u) {
                        echo "
                        <tr>
                            <td>{$u['nombreUsuario']}</td>
                            <td>{$u['nombre']}</td>
                            <td>{$u['apellidos']}</td>
                            <td>{$u['dni']}</td>
                            <td>{$u['Correo']}</td>
                            <td>{$u['Telefono']}</td>
                            <td>{$u['FechaNac']}</td>
                            <td>{$u['Rol']}</td>
                            <td>
                                <form method='post' action='procesarRecomendacion.php'>
                                    <input type='hidden' value='{$u['nombreUsuario']}'>
                                    <button class='btn btn-success btn-sm'>Modificar</button>
                                </form>
                            </td>
                            <td>
                                <form method='post' action='procesarRecomendacion.php'>
                                    <input type='hidden' value='{$u['nombreUsuario']}'>
                                    <button class='btn btn-success btn-sm'>Eliminar</button>
                                </form>
                            </td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="container mt-5">
        <div id="sec-recomendaciones" class="admin-section">
            <h3 class="section-title">Reservas vigentes</h3>
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID Reserva</th>
                        <th>ID Alojamiento</th>
                        <th>Fecha de inicio</th>
                        <th>Fecha final</th>
                        <th>Cantidad de huéspedes</th>
                        <th>DNI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $reservas = listadoReservasVigentes();
                    foreach ($reservas as $r) {
                        echo "
                        <tr>
                            <td>{$r['id']}</td>
                            <td>{$r['nombidAlojamiento']}</td>
                            <td>{$r['fechaInicio']}</td>
                            <td>{$r['fechaFinal']}</td>
                            <td>{$r['cantidadHuespedes']}</td>
                            <td>{$r['dniReserva']}</td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="container mt-5">
        <div id="sec-recomendaciones" class="admin-section">
            <h3 class="section-title">Reservas de alojamientos sin restricción de fecha</h3>
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID Reserva</th>
                        <th>ID Alojamiento</th>
                        <th>Fecha de inicio</th>
                        <th>Fecha final</th>
                        <th>Cantidad de huéspedes</th>
                        <th>DNI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $reservas = listadoAlojamientos();
                    foreach ($reservas as $r) {
                        echo "
                        <tr>
                            <td>{$r['id']}</td>
                            <td>{$r['nombidAlojamiento']}</td>
                            <td>{$r['fechaInicio']}</td>
                            <td>{$r['fechaFinal']}</td>
                            <td>{$r['cantidadHuespedes']}</td>
                            <td>{$r['dniReserva']}</td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 4. ALOJAMIENTOS -->
    <div id="sec-alojamientos" class="admin-section">
        <div class="d-flex justify-content-between">
            <h3 class="section-title">Alojamientos</h3>
            <button class="btn btn-outline-primary btn-sm">+ Añadir Casa</button>
        </div>
        <div class="row mt-3">
            <div class="col-md-4">
                <div class="card p-3 shadow-sm border-0">
                    <strong>Villa Oasis</strong>
                    <p class="mb-0 text-muted small">Maspalomas, Gran Canaria</p>
                </div>
            </div>
        </div>
    </div>

    </div>
</body>

</html>