<?php
include 'conectar.php';
include 'mostrarAlojamientos.php'; 
session_start();

function insertarRecomendacion($titulo, $desc, $img,$precio, $lugar, $tipoAct){
    try{
        $conn = conectarBD();
        $query = "INSERT into recomendacionesPendientes (titulo, descripcion, imagen, precio, lugar, tipoActividad) values (?,?,?,?,?,?)";
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

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $titulo = $_POST['titulo'] ?? null;
    $tipoActividad = $_POST['tipo'] ?? null;
    $precio = $_POST['precio'] ?? null;
    $descrip = $_POST['descripcion'] ?? null;
    $imagen = $_POST['imagen'] ?? null; 
    $lugar = $_POST['lugar'] ?? null;
    if($titulo != null && $tipoActividad != null && $precio != null && $descrip != null && $imagen != null){
        insertarRecomendacion($titulo, $descrip, $imagen, $precio, $lugar, $tipoActividad);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="css/styles.css">
    <title>Recomendaciones - CanaryTravel</title>
    <style>
        .header-recom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 30px 0;
            padding: 0 20px;
        }
        .card-recom {
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s;
            height: 100%;
        }
        .card-recom:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .badge-tipo {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(13, 110, 253, 0.9);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        .img-container {
        height: 200px; /* Altura fija para todas las imágenes */
        background-color: #f0f0f0; /* Color de fondo si la imagen falla */
        position: relative;
        overflow: hidden;
        }

        .img-container img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Evita que la imagen se estire */
        }

        .card-recom {
            margin-top: 2rem; /* Separación entre filas */
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
    </style>
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

    <main class="container">
        <div class="header-recom d-flex justify-content-between align-items-center my-4">
        <h1>Explora Experiencias Locales</h1>
        <?php if(isset($_SESSION['usuario'])): ?>
            <button class='btn btn-success' data-bs-toggle='modal' data-bs-target='#modalAñadir'>
                + Añadir Recomendación
            </button>
        <?php endif; ?>
        </div>
        <div id="buscador" class="container mt-4">
            <form action="recomendaciones.php" method="post">
                <div class="row align-items-end justify-content-center">
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Isla</label>
                        <select name="isla" class="form-select" value="<?php if(isset($_POST['isla'])) echo $_POST['isla']?>">
                            <?php
                            try {
                                $conn = conectarBD();
                                $stmt = $conn->query("SELECT nombre from destinos");
                                $destinos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($destinos as $d) {
                                    echo "<option value='" . $d['nombre'] . "'>{$d['nombre']}</option>";
                                }
                            } catch (PDOException $e) {
                                echo "<option>Error de conexión</option>";
                            }
                            ?>
                        </select>
                    </div>
                      <div class="col-md-2">
                        <label class="form-label fw-bold">Tipo de actividad</label>
                        <select name="tipoAc" class="form-select">
                            <option value="Experiencia">Experiencia</option>
                            <option value="Local">Local</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input class="btn btn-primary w-100" type="submit" value="Buscar">
                    </div>
            </form>
            <br><br>
        </div>
        <div class="row g-5">
            <?php
                $isla = $_POST['isla'] ?? ''; 
                $tipoAc = $_POST['tipoAc'] ?? '';
                $recomendaciones = mostrarRecomendaciones($isla, $tipoAc);
                if(!empty($recomendaciones)){
                    foreach($recomendaciones as $r){
                        echo "
                            <div class='col-md-4'>
                                <div class='card card-recom shadow-sm h-100'>
                                    <div class='img-container' style='position: relative; height: 200px; overflow: hidden;'>
                                        <span class='badge-tipo' style='position: absolute; top: 15px; left: 15px; background: rgba(13, 110, 253, 0.9); color: white; padding: 5px 15px; border-radius: 20px; font-size: 0.75rem; font-weight: bold; z-index: 10;'>
                                            {$r['tipoActividad']}
                                        </span>
                                        <img src='{$r['imagen']}' alt='{$r['titulo']}' style='width: 100%; height: 100%; object-fit: cover;'>
                                    </div>
                                    <div class='card-body d-flex flex-column'>
                                    <h5 class='card-title fw-bold text-primary'>{$r['titulo']}</h5>
                                    <p class='text-muted mb-1 small'>
                                        <i class='bi bi-geo-alt'></i> {$r['lugar']}
                                    </p>
                                    <p class='card-text text-dark'>{$r['descripcion']}</p>
                                    <span class='badge rounded-pill bg-success p-2 px-3'>
                                        {$r['precio']}€ por persona
                                    </span>
                                </div>
                            </div>
                        </div>";
                    }
                }
            ?>
        </div>
    </main>
    <?php 
    echo "
        <div class='modal fade' id='modalAñadir' tabindex='-1' aria-labelledby='modalAñadirLabel' aria-hidden='true'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h5 class='modal-title' id='modalAñadirLabel'>Nueva Recomendación</h5>
                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                    </div>
                    <div class='modal-body'>
                        <form action='' method='POST' enctype='multipart/form-data'>
                            <div class='mb-3'>
                                <label class='form-label'>Título</label>
                                <input type='text' name='titulo' class='form-control' placeholder='Ej: Guachinche La Cueva' required>
                            </div>
                            <div class='mb-3'>
                                <label class='form-label'>Tipo</label>
                                <select name='tipo' class='form-select'>
                                    <option value='Local'>Local / Restaurante</option>
                                    <option value='Experiencia'>Experiencia / Actividad</option>
                                </select>
                            </div>
                            <div class='mb-3'>
                                <label class='form-label'>Lugar</label>
                                <input type='text' name='lugar' class='form-control' placeholder='Ej: Tenerife' required>
                            </div>
                            <div class='mb-3'>
                                <label class='form-label'>Precio</label>
                                <input type='text' name='precio' class='form-control' placeholder='Ej: 20€' required>
                            </div>
                            <div class='mb-3'>
                                <label class='form-label'>Descripción</label>
                                <textarea name='descripcion' class='form-control' rows='3' required></textarea>
                            </div>
                            <div class='mb-3'>
                                <label class='form-label d-block'>Imagen de la Recomendación</label>
                                <!-- El widget escribirá la URL aquí -->
                                <input type='hidden' name='imagen' id='input_url_imagen' required>
                                
                                <!-- Botón para abrir Cloudinary -->
                                <button type='button' id='upload_widget' class='btn btn-outline-primary w-100'>
                                    <i class='bi bi-camera'></i> Seleccionar Imagen
                                </button>
                                
                                <!-- Feedback visual para el usuario -->
                                <div id='preview_container' class='mt-2' style='display:none;'>
                                    <span class='badge bg-success'>Imagen cargada correctamente</span>
                                </div>
                            </div>
                            <div class='modal-footer px-0 pb-0'>
                                <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancelar</button>
                                <button type='submit' class='btn btn-primary'>Guardar Publicación</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>";
    ?>
</body>
<script src="https://upload-widget.cloudinary.com/global/all.js" type="text/javascript"></script>
<script>
    var myWidget = cloudinary.createUploadWidget({
        cloudName: 'dkbepwbpj', 
        uploadPreset: 'canary'
    }, (error, result) => { 
        if (!error && result && result.event === "success") { 
            console.log('Imagen subida con éxito: ', result.info.secure_url);
            
            const inputImagen = document.getElementById('input_url_imagen');
            const preview = document.getElementById('preview_container');
            const btnWidget = document.getElementById('upload_widget');

            inputImagen.value = result.info.secure_url;

            // 3. Feedback visual
            preview.style.display = 'block';
            btnWidget.innerText = 'Cambiar Imagen';
            btnWidget.classList.replace('btn-outline-primary', 'btn-outline-secondary');
        }
    });
    document.getElementById("upload_widget").addEventListener("click", function(e){
        e.preventDefault();
        myWidget.open();
    }, false);
</script>
</html>