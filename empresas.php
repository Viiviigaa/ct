<?php
    include 'conectar.php'; 
    session_start(); 
    //error_reporting(E_ALL);
    //ini_set('display_errors', 1);
    $conn = conectarBD();
    
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
            if($_SESSION['rol'] != 'business'){
                $msg = "<div class='alert alert-warning'>Acceso restringido: Solo para cuentas de empresa.</div>";
            } else {
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        } else {
            $msg = "<div class='alert alert-danger'>Usuario o contraseña incorrectos.</div>";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/login.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script> 
    <title>CanaryTravel</title>
</head>
<style>
        form { max-width: 500px; margin: 40px auto; padding: 30px; background-color: #ffffff; border-radius: 15px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        form label { font-weight: 600; color: #333; margin-bottom: 8px; display: inline-block; }
        form input[type="text"], form input[type="password"] { width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; margin-bottom: 15px; }
        form input[type="submit"]{ width: 100%; padding: 12px 15px; border-radius: 8px; }
</style>
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
        <div class="logs">
            <button class="btn btn-primary"><a href="empresas.php" style='text-decoration: none; color:white; width:150px'>Empresas</a></button>
            <button class="btn btn-primary"><a href="sesion.php" style='text-decoration: none; color:white; width:150px'>Iniciar sesión</a></button>
            <button class="btn btn-primary"><a href="registro.php" style='text-decoration: none; color:white; width:150px'>Registrarse</a></button>
        </div>
    </header>
    <main>
        <?php
            if(isset($_SESSION['usuario']) && $_SESSION['rol'] == 'business'){
                header('Location: panelEmpresa.php');
            }else{
                    echo "<div>
                        <form action='' method='post'>
                            <h3>Iniciar sesión</h3>
                            <label>Nombre de usuario: </label><br>
                            <input type='text' name='username'><br>
                            <label>Contraseña: </label><br>
                            <input type='password' name='password'><br>
                    ";
                    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($msg)){
                        echo $msg;
                    }
                    echo "<input type='submit' class='btn btn-primary' value='Iniciar sesión'>
                        </form>
                    </div>";
            }    
        ?>
    </main>
    <footer>

    </footer>
</body>
</html>