<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de sesión</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/login.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</head>
<?php

include 'conectar.php';

session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

    $errores = [];
    $correcta = false;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre = trim($_POST['nombreUsuario'] ?? '');
        $contrasena  = trim($_POST['contrasena'] ?? '');

        if (empty($nombre)){
            $errores['nombreUsuario'] = "Requerido";
        } 
        /* 
        if((strlen($contrasena)<8) || (strlen($contrasena)>15)){
            $errores['contrasena']="La longitud de la contrasena debe estar entre 8 y 15 caracteres";
        }
        */
        if (empty($errores)) {
            try {
                $conn = conectarBD();
                $stmt = $conn->query("SELECT nombreUsuario, Contrasena, dni from usuarios");
                $validar = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($validar as $u) {
                    if ($u['nombreUsuario'] == $nombre && $u['Contrasena']==$contrasena){
                        $correcta =  true;
                        $_SESSION['dni'] = $u['dni'];
                    }
                }
                if($correcta){
                    $_SESSION['usuario'] = $nombre;
                    header("Location: index.php");
                    exit();
                }else{
                    $errores['contrasenaInvalida'] = "La contraseña no es correcta";
                }
            } catch (PDOException $e) {
                echo "Ha ocurrido un error al conectar con base de datos";
            }
        }
        
    }
?>
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
                <li class="list-group-item"><a href="misReservas.php">Mis reservas</a></li>    
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
        <div class="logs">
            <button class="btn btn-primary"><a href="empresas.php" style='text-decoration: none; color:white; width:150px'>Empresas</a></button>
            <button class="btn btn-primary"><a href="sesion.php" style='text-decoration: none; color:white; width:150px'>Iniciar sesión</a></button>
            <button class="btn btn-primary"><a href="registro.php" style='text-decoration: none; color:white; width:150px'>Registrarse</a></button>
        </div>
    </header>

    <div id="sesion-box">
        <h3>Iniciar sesión</h3>
        <form action="sesion.php" method="POST">
            <label for="nombreUsuario">Nombre de ususario:</label><br>
            <input type="text" name="nombreUsuario"  id="nombreUsuario" class="inputIni" placeholder="Ingresa tu nombre de usuario">
            <br>
            <?php if (isset($errores['nombreUsuario'])){
                 echo "<span style='color:red'>{$errores['nombreUsuario']}</span>";
            } 
            ?>
            <label for="contrasena">Contraseña:</label>
            <br>
            <input type="password" name="contrasena" placeholder="Contraseña" id="contrasena" class="inputIni" placeholder="Ingresa tu contraseña">
            <br>
            <?php if (isset($errores['contrasena'])){
                 echo "<span style='color:red'>{$errores['contrasena']}</span>";
            } 
            ?>
            <br>
            <input type="submit" name="envio" value="Enviar" id='enviar'>
        </form>
        <br>
    </div>

    <footer>

    </footer>
    
</body>
</html>