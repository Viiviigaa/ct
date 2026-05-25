<?php   
include 'conectar.php';
include 'enviarEmail.php';
session_start();
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/login.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script> 
</head>
<?php
    $errores = [];
    $correcta = false;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $usuario = trim($_POST['nombreUsuario'] ?? '');
        $contrasena  = trim($_POST['contrasena'] ?? '');
        $contrasenaR  = trim($_POST['contrasenaR'] ?? '');
        $nombre = trim($_POST["nombre"] ?? "");
        $apellido = trim($_POST["apellido"] ?? "");
        $correo = trim($_POST["correo"] ?? "");
        $dni = trim($_POST["dni"] ?? "");
        $fechaNac = $_POST['fechaNac'] ?? '';
        $telefono = $_POST['telefono'] ?? '';

        if (empty($usuario)){
            $errores['nombreUsuario'] = "<h6 style='color:red'>El nombre de usuario no puede estar vacio</h6>";
        }  

        if (strlen($contrasena) < 8) {
            $errores['contrasena'] = "<h6 style='color:red'>Mínimo 8 caracteres</h6>";
        }
        elseif (!preg_match('/[A-Z]/', $contrasena)) {
            $errores['contrasena'] = "<h6 style='color:red'>Al menos una mayúscula</h6>";
        }
        elseif (!preg_match('/[a-z]/', $contrasena)) {
            $errores['contrasena'] = "<h6 style='color:red'>Al menos una minúscula</h6>";
        }
        elseif (!preg_match('/[0-9]/', $contrasena)) {
            $errores['contrasena'] = "<h6 style='color:red'>Al menos un número</h6>";
        }
        elseif (!preg_match('/[\W_]/', $contrasena)) {
            $errores['contrasena'] = "<h6 style='color:red'>Al menos un carácter especial (!@#$...)</h6>";
        }

        if (empty($correo)) {
            $errores["correo"] = "<h6 style='color:red'>Email obligatorio</h6>";
        } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errores["correo"] = "<h6 style='color:red'>Email no válido</h6>";
        }

        if (empty($telefono)) {
            $errores["telefono"] = "<h6 style='color:red'>El telefono no puede estar vacio</h6>";
        }else if(!preg_match('/^[0-9]+$/', $telefono)){
            $errores["telefono"] = "<h6 style='color:red'>El telefono no puede contener letras</h6>";
        }
        
        if (empty($nombre)) {
            $errores["nombre"] = "<h6 style='color:red'>El nombre es obligatorio</h6>";
        } elseif (strlen($nombre) < 2) {
            $errores["nombre"] = "<h6 style='color:red'>Mínimo 2 caracteres</h6>";
        } elseif (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $nombre)) {
            $errores["nombre"] = "<h6 style='color:red'>Solo letras y espacios</h6>";
        }

        if (empty($apellido)) {
            $errores["apellido"] = "<h6 style='color:red'>El apellido es obligatorio</h6>";
        } elseif (strlen($apellido) < 2) {
            $errores["apellido"] = "<h6 style='color:red'>Mínimo 2 caracteres</h6>";
        } elseif (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $apellido)) {
            $errores["apellido"] = "<h6 style='color:red'>Solo letras y espacios</h6>";
        }

        if (empty($dni)) {
            $errores["dni"] = "<h6 style='color:red'>El DNI es obligatorio</h6>";
        }elseif (strlen($dni) != 9){
            $errores["dni"] = "<h6 style='color:red'>El DNI debe de tener 9 caracteres</h6>";
        }else if (!preg_match('/^[0-9]{8}[A-Za-z]$/', $dni)) {
            $errores["dni"] = "<h6 style='color:red'>El DNI debe de tener 8 números y 1 letra</h6>";
        }

        if (empty($fechaNac)) {
            $errores['fechaNac'] = "<h6 style='color:red'>La fecha es obligatoria</h6>";
        }else{
            $dt = new DateTime($fechaNac);
            $hoy  = new DateTime();
            $edad = $hoy->diff($dt)->y;
            if ($edad < 18) {
                $errores['fechaNac'] = "<h6 style='color:red'>Debes tener al menos 18 años</h6>";
            } elseif ($edad > 105) {
                $errores['fechaNac'] = "<h6 style='color:red'>Fecha demasiado antigua</h6>";
            }
        }




    if (empty($errores)) {
        try {
            $conn = conectarBD();
            $stmt = $conn->prepare("INSERT INTO usuarios (nombreUsuario, nombre, apellidos, dni, Correo, Contrasena, Telefono, FechaNac, Rol) VALUES (:usuario, :nombre, :apellidos, :dni, :correo, :contrasena, :telefono, :fechaNac, :rol)");
            $stmt->execute([
                'usuario' => $usuario,
                'nombre'=> $nombre,
                'apellidos' => $apellido,
                'dni' => $dni,
                'correo' => $correo,
                'contrasena' => $contrasena,
                'telefono' => $telefono,
                'fechaNac' => $fechaNac,
                'rol' => "base"
            ]);
            header('Location:registroCompletado.php');
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
            <div id="formulario">
                <h2>Formulario de Registro</h2>
                <form method="POST" id="registerForm">
            <div class="form-group">
                <label for="nombreUsuario">Nombre de usuario:</label><br>
                <input type="text" name="nombreUsuario" placeholder="Nombre Usuario" id="nombreUsuario" class="inputIni" value="<?= htmlspecialchars($_POST['nombreUsuario'] ?? '') ?>">
                <?= $errores['nombreUsuario'] ?? '' ?>
            </div>

            <div class="form-group">
                <label for="contrasena">Contraseña:</label><br>
                <input type="password" name="contrasena" placeholder="Contraseña" id="contrasena" class="inputIni">
                <?= $errores['contrasena'] ?? '' ?>
            </div>

            <div class="form-group">
                <label for="correo">Correo:</label><br>
                <input type="email" name="correo" placeholder="Correo" id="correo" class="inputIni" value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>">
                <?= $errores['correo'] ?? '' ?>
            </div>

            <div class="form-group">
                <label for="nombre">Nombre Personal:</label><br>
                <input type="text" name="nombre" placeholder="Nombre" id="nombre" class="inputIni" value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
                <?= $errores['nombre'] ?? '' ?>
            </div>

            <div class="form-group">
                <label for="apellido">Apellidos:</label><br>
                <input type="text" name="apellido" placeholder="Apellido" id="apellido" class="inputIni" value="<?= htmlspecialchars($_POST['apellido'] ?? '') ?>">
                <?= $errores['apellido'] ?? '' ?>
            </div>

            <div class="form-group">
                <label for="dni">DNI:</label><br>
                <input type="text" name="dni" placeholder="DNI" id="dni" class="inputIni" value="<?= htmlspecialchars($_POST['dni'] ?? '') ?>">
                <?= $errores['dni'] ?? '' ?>
            </div>

            <div class="form-group">
                <label for="telefono">Telefono:</label><br>
                <input type="text" name="telefono" placeholder="Telefono" id="telefono" class="inputIni" value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>">
                <?= $errores['telefono'] ?? '' ?>
            </div>

            <div class="form-group">
                <label for="fechaNac">Fecha Nacimiento:</label><br>
                <input type="date" name="fechaNac" id="fechaNac" class="inputIni" value="<?= htmlspecialchars($_POST['fechaNac'] ?? '') ?>">
                <?= $errores['fechaNac'] ?? '' ?>
            </div>
            <input type="submit" value="Registrarse" id="submitRegister" class="btn btn-primary">    
        </form>    
    </div>
</main>
<footer>

</footer>  
</body>
</html>