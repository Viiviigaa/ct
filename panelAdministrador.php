<?php
include 'conectar.php';

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: index.php");
    exit();
}

function recomendacionesPorAprobar(){
    $conn = conectarBD();
    $query = "SELECT  * from recomendacionesPendientes";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $resultado;
}

function listadoUsuarios(){
    $conn = conectarBD();
    $query = "SELECT * FROM usuarios";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $resultado;
}

function listadoReservasVigentes(){
    $conn = conectarBD();
    $hoy = new DateTime();
    $hoy =  $hoy->format('Y-m-d');
    $query = "SELECT * FROM reservas where fechaInicio <= ? and fechaFinal >= ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$hoy, $hoy]);
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $resultado;
}

function listadoReservas(){
    $conn = conectarBD();
    $query = "SELECT * FROM reservas";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $resultado;
}

function listadoAlojamientos(){
    $conn = conectarBD();
    $query = "SELECT * FROM alojamientos";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $resultado;
}

function listadoAlojamientosPorAprobar(){
    $conn = conectarBD();
    $query = "SELECT * FROM alojamientosPendientes";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $resultado;
}

function informacionAlojamiento($id){
    $conn = conectarBD();
    $query = "SELECT * FROM alojamientosPendientes where ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id]);
    $resultados = $stmt->fetch(PDO::FETCH_ASSOC);
    return $resultados; 
}

function nuevoAlojamiento($nombre, $isla, $descripcion, $fotos, $precio, $direccion, $huespedes, $codigoEmpresa){
    $conn = conectarBD();
    try {
        $query = "INSERT INTO alojamientos (nombreAlojamiento, isla, descripcion, fotos, precio, direccion, max_huespedes, codigoEmpresa) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
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

function eliminarAlojamientoPendiente($id){
    $conn = conectarBD();
    $query = "DELETE FROM alojamientosPendientes where ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id]);  
}
function eliminarUsuarios($id){
    $conn = conectarBD();
    $query = "DELETE FROM usuarios WHERE nombreUsuario = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id]);
}

function insertarRecomendacion($titulo, $desc, $img,$precio, $lugar, $tipoAct){
    try{
        $conn = conectarBD();
        $query = "INSERT into recomendaciones (titulo, descripcion, imagen, precio, lugar, tipoActividad) values (?,?,?,?,?,?)";
        $stmt = $conn->prepare($query); 
        $resultado = $stmt->execute([
            $titulo,
            $desc,
            $img, 
            $precio,
            $lugar,
            $tipoAct
        ]);
        return $resultado;
    }catch(PDOException $e){
        echo $e->getMessage();
    }
}

function recomendacionPendienteAInsertar($id){
    $conn = conectarBD();
    $query = "Select * from recomendacionesPendientes where id = ?"; 
    $stmt = $conn->prepare($query);
    $stmt->execute([$id]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    return $resultado;
}

function eliminarRecomendacionPendiente($id){
    $conn = conectarBD();
    $query = "DELETE FROM recomendacionesPendientes where id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id]);    
}

function informacionUsuario($nombreUsuario){
    $conn = conectarBD();
    $query = "SELECT * FROM usuarios where nombreUsuario = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$nombreUsuario]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    return $resultado;
}

function actualizarDatosUsuario($nombre, $apellidos, $dni, $correo, $telefono, $fecha, $rol, $nombreUsuario){
    $conn = conectarBD();
    $query = "UPDATE usuarios set 
              nombre = ?, 
              apellidos = ?, 
              dni = ?, 
              Correo = ?, 
              Telefono = ?, 
              FechaNac = ?, 
              Rol = ? 
             WHERE nombreUsuario = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([
        $nombre,
        $apellidos, 
        $dni,
        $correo, 
        $telefono, 
        $fecha, 
        $rol,
        $nombreUsuario
    ]);
    header('Location: ' . $_SERVER['PHP_SELF']);
}

function listarDestinos(){
    $conn = conectarBD();
    $query = "SELECT nombre from destinos";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $resultado; 
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    //APROBAR O DENEGAR UNA NUEVO RECOMENDACIÓN
    if(isset($_POST['recomendacionPendiente']) && isset($_POST['recomendacionPendienteVal'])){
        if($_POST['recomendacionPendienteVal'] == 'Publicar'){
            try{
                $recom = recomendacionPendienteAInsertar($_POST['recomendacionPendiente']);
                insertarRecomendacion($recom['titulo'],$recom['descripcion'], $recom['imagen'], $recom['precio'], $recom['lugar'], $recom['tipoActividad']);
                eliminarRecomendacionPendiente($_POST['recomendacionPendiente']);
            }catch(PDOException $e){
                echo "Error: " . $e->getMessage();
            }
        }else{
            try{
                eliminarRecomendacionPendiente($_POST['recomendacionPendiente']);
            }catch(PDOException $e){
                echo "Error: " . $e->getMessage();
            }
        }
    }

    //Publicar o eliminar un alojamiento pendiente
    if(isset($_POST['alojamientoID']) && $_POST['alojamientoPendiente'] == 'Aprobada'){
        try{
            $infoAlojamiento = informacionAlojamiento($_POST['alojamientoID']);
            nuevoAlojamiento($infoAlojamiento['nombreAlojamiento'],$infoAlojamiento['isla'],$infoAlojamiento['descripcion'],$infoAlojamiento['fotos'], $infoAlojamiento['precio'], $infoAlojamiento['direccion'],$infoAlojamiento['max_huespedes'], $infoAlojamiento['codigoEmpresa']);
            eliminarAlojamientoPendiente($_POST['alojamientoID']);
        }catch(PDOException $e){
            echo "Error: " . $e->getMessage();
        }
    }else if(isset($_POST['alojamientoID']) && $_POST['alojamientoPendiente'] == 'Rechazada'){
        try{
            eliminarAlojamientoPendiente($_POST['alojamientoID']);
        }catch(PDOException $e){
            echo "Error: " . $e->getMessage();
        }
    }

    //Modificar o eliminar un usuario
    if(isset($_POST['nombreUsuario'])){
        if(isset($_POST['accion_update_user'])){
            //Recogemos todos los datos del formulario modal
            $id_original = $_POST['id_original'];
            $nuevo_user  = $_POST['upd_username'];
            $nombre      = $_POST['upd_nombre'];
            $apellidos   = $_POST['upd_apellidos'];
            $dni         = $_POST['upd_dni'];
            $correo      = $_POST['upd_correo'];
            $telefono    = $_POST['upd_telefono'];
            $fecha       = $_POST['upd_fecha'];
            $rol         = $_POST['upd_rol'];
            try{
                actualizarDatosUsuario($nombre, $apellidos, $dni, $correo, $telefono, $fecha, $rol, $nuevo_user); 
            }catch(PDOException $e){
                echo "Error: " . $e->getMessage();
            }
        }else if(isset($_POST['accion']) && $_POST['accion']=='eliminar'){
            //Eliminamos con nombre de usuario en vez de el ID porque es la primary key de la tabla. 
            eliminarUsuarios($_POST['nombreUsuario']); 
        }
    }
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
    <div class="container mt-5">
        <div id="sec-recomendaciones" class="admin-section">
            <h3 class="section-title">Moderación de Recomendaciones</h3>
                    <?php
                    $recommend = recomendacionesPorAprobar();
                    if(!empty($recommend)){
                        echo "<table class='table align-middle'>
                            <thead class='table-light'>
                                <tr>
                                    <th>ID</th>
                                    <th>Titulo</th>
                                    <th>Descripcion</th>
                                    <th>Imagen</th>
                                    <th>Precio</th>
                                    <th>Lugar</th>
                                    <th>Tipo de actividad</th>
                                </tr>
                            </thead>
                            <tbody>
                        ";
                        foreach ($recommend as $r) {
                        echo "
                        <tr>
                            <td>{$r['id']}</td>
                            <td>{$r['titulo']}</td>
                            <td>{$r['descripcion']}</td>
                            <td style='overflow-x: auto; max-width: 100px'><a target='_blank' href='{$r['imagen']}'>Ver imagen</a></td>
                            <td>{$r['precio']}</td>
                            <td>{$r['lugar']}</td>
                            <td>{$r['tipoActividad']}</td>
                            <td>
                                <form method='post' action=''>
                                    <input type='hidden' value='{$r['id']}' name='recomendacionPendiente'>
                                    <input type='hidden' value='Publicar' name='recomendacionPendienteVal'>
                                    <button type='submit' class='btn btn-success btn-sm'>Publicar</button>
                                </form>
                            </td>
                            <td>
                                <form method='post' action=''>
                                    <input type='hidden' value='{$r['id']}' name='recomendacionPendiente'>
                                    <input type='hidden' value='Rechazada' name='recomendacionPendienteVal'>
                                    <button type='submit' class='btn btn-success btn-sm'>Rechazar</button>
                                </form>
                            </td>
                        </tr>";
                    }
                    }else{
                        echo "<h3>No hay recomendaciones pendientes por aprobar</h3>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="container mt-5">
        <div id="sec-recomendaciones" class="admin-section">
            <h3 class="section-title">Moderación de alojamientos</h3>
                <?php
                    $alojamientos = listadoAlojamientosPorAprobar();
                    if(!empty($alojamientos)){
                        echo "<div class='table-responsive'>
                            <table class='table align-middle'>
                                <thead class='table-light'>
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
                        <tbody>";
                        foreach ($alojamientos as $a) {
                            echo "
                            <tr>
                                <td>{$a['ID']}</td>
                                <td>{$a['nombreAlojamiento']}</td>
                                <td>{$a['isla']}</td>
                                <td>{$a['descripcion']}</td>
                                <td><a href='{$a['fotos']}'>Ver imagenes</a></td>
                                <td>{$a['precio']}</td>
                                <td>{$a['direccion']}</td>
                                <td>{$a['max_huespedes']}</td>
                                <td>{$a['codigoEmpresa']}</td>
                                <td>
                                    <form method='post' action=''>
                                    <input type='hidden' value='{$a['ID']}' name='alojamientoID'>
                                        <input type='hidden' name='alojamientoPendiente' value='Aprobada'>
                                        <button type='submit' class='btn btn-success btn-sm'>Publicar</button>
                                    </form>
                                </td>
                                <td>
                                    <form method='post' action=''>
                                        <input type='hidden' value='{$a['ID']}' name='alojamientoID'>
                                        <input type='hidden' name='alojamientoPendiente' value='Rechazada'>
                                        <button type='submit' class='btn btn-success btn-sm'>Rechazar</button>
                                    </form>
                                </td>
                            </tr>";
                        }
                        }else{
                            echo "<h3>No hay alojamientos pendientes por aprobar</h3>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
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
                                <form action='' method='post'>
                                    <button type='button' 
                                        class='btn btn-success btn-sm btn-edit' 
                                        data-username='{$u['nombreUsuario']}'
                                        data-nombre='{$u['nombre']}'
                                        data-apellidos='{$u['apellidos']}'
                                        data-dni='{$u['dni']}'
                                        data-correo='{$u['Correo']}'
                                        data-telefono='{$u['Telefono']}'
                                        data-fecha='{$u['FechaNac']}'
                                        data-rol='{$u['Rol']}'>
                                        Modificar
                                    </button>
                                </form>
                            </td>
                            <td>
                                <form method='post' action=''>
                                    <input type='hidden' name='nombreUsuario' value='{$u['nombreUsuario']}'>
                                    <input type='hidden' name='accion' value='eliminar'>
                                    <button type='submit' class='btn btn-success btn-sm'>Eliminar</button>
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
                            <td>{$r['idAlojamiento']}</td>
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
            <div class="table-responsive">
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
                        $reservas = listadoReservas();
                        foreach ($reservas as $r) {
                            echo "
                            <tr>
                                <td>{$r['id']}</td>
                                <td>{$r['idAlojamiento']}</td>
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
    </div>

    <div class="container mt-5">
        <div id="sec-alojamientos" class="admin-section">
            <div class="d-flex justify-content-between">
                <h3 class="section-title">Moderación de alojamientos</h3>
                <button id="btnAbrirAlta" class="btn btn-outline-primary btn-sm">+ Añadir alojamiento</button>
            </div>
            <br>
            <div class="table-responsive">
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
                        $alojamientos = listadoAlojamientos();
                        foreach ($alojamientos as $a) {
                            echo "
                            <tr>
                                <td>{$a['ID']}</td>
                                <td>{$a['nombreAlojamiento']}</td>
                                <td>{$a['isla']}</td>
                                <td>{$a['descripcion']}</td>
                                <td style='overflow-x: auto; max-width: 100px'>{$a['fotos']}</td>
                                <td>{$a['precio']}</td>
                                <td>{$a['direccion']}</td>
                                <td>{$a['max_huespedes']}</td>
                                <td>{$a['codigoEmpresa']}</td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-labelledby="modalEditarUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarUsuarioLabel">Modificar Datos de Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="id_original" id="edit_id_original">
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre de Usuario</label>
                            <input type="text" name="upd_username" id="edit_username" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="upd_nombre" id="edit_nombre" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Apellidos</label>
                            <input type="text" name="upd_apellidos" id="edit_apellidos" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">DNI</label>
                            <input type="text" name="upd_dni" id="edit_dni" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Correo</label>
                            <input type="email" name="upd_correo" id="edit_correo" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="upd_telefono" id="edit_telefono" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha Nacimiento</label>
                            <input type="date" name="upd_fecha" id="edit_fecha" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Rol</label>
                            <select name="upd_rol" id="edit_rol" class="form-select">
                                <option value="base">Base</option>
                                <option value="pro">Pro</option>
                                <option value="business">Business</option>
                                <option value="administrador">Administrador</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" name="accion_update_user" class="btn btn-primary">Guardar Cambios</button>
                </div>

        <div class="modal fade" id="darAltaAlojamiento" tabindex="-1" aria-labelledby="modalEditarUsuarioLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="insertarAlojamienti">Añadir nuevo alojamiento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST">
                <div class="modal-body">
                    <div class="row">                
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre del alojamiento</label>
                            <input type="text" name="nombre_aloj" id="edit_username" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Isla</label>
                            <select class="form-control" name="destino_aloj">
                                <?php
                                    $destinos = listarDestinos();
                                    foreach($destinos as $d){
                                        echo "<option value='{$r['nombre']}'>{$r['nombre']}</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Descripcion</label>
                            <input type="text" name="descripcion_aloj" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">DNI</label>
                            <input type="text" name="dni_aloj"  class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fotos</label>
                            <input type="text" name="fotos-aloj"  class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Precio</label>
                            <input type="email" name="precio_aloj"  class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Dirección</label>
                            <input type="text" name="direccion_aloj"  class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Cantidad máxima de huéspedes</label>
                            <input type="date" name="huespedes_aloj"  class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Código de empresa</label>
                            <input type="date" name="empresa_aloj"  class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" name="alta_aloj" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const editButtons = document.querySelectorAll('.btn-edit');
            const editModal = new bootstrap.Modal(document.getElementById('modalEditarUsuario'));

            const altaModal =  new bootstrap.Modal(document.getElementById('darAltaAlojamiento'));
            const btnAbrirAlta = document.getElementById("btnAbrirAlta");

            if(btnAbrirAlta){
                btnAbrirAlta.addEventListener('click', function() {
                altaModal.show();
                });
            }
            
            editButtons.forEach(button => {
                button.addEventListener('click', function () {
                    // Extraer datos del botón
                    document.getElementById('edit_id_original').value = this.dataset.username;
                    document.getElementById('edit_username').value = this.dataset.username;
                    document.getElementById('edit_nombre').value = this.dataset.nombre;
                    document.getElementById('edit_apellidos').value = this.dataset.apellidos;
                    document.getElementById('edit_dni').value = this.dataset.dni;
                    document.getElementById('edit_correo').value = this.dataset.correo;
                    document.getElementById('edit_telefono').value = this.dataset.telefono;
                    document.getElementById('edit_fecha').value = this.dataset.fecha;
                    document.getElementById('edit_rol').value = this.dataset.rol;

                    // Mostrar el modal
                    editModal.show();
                });
            });
        });
    </script>
</body>
</html>