<?php

session_start();

include 'conectar.php';

try {
    $conn = conectarBD();
    if (!isset($_SESSION['usuario'])) {
        header('Location: index.php');
        exit;
    }

    $user = $_SESSION['usuario'];

    function obtenerInformacionUsuario($nombreUsuario, $db){
        $query = "SELECT * FROM usuarios WHERE nombreUsuario = ?";  
        $stmt = $db->prepare($query);
        $stmt->execute([$nombreUsuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    $informacionUsuario = obtenerInformacionUsuario($user, $conn);
    $date = new DateTime($informacionUsuario['FechaNac']);
    $fechaFormateada = $date->format('d/m/Y');

    function cambiarPassword($conn, $vieja, $nueva1, $nueva2, $informacionUsuario) {
        if ($nueva1 !== $nueva2) {
            echo "Las nuevas contraseñas no coinciden entre sí.";
            return false;
        }


        if ($informacionUsuario['Contrasena'] === $vieja) {
            $query = "UPDATE usuarios SET Contrasena = ? WHERE nombreUsuario = ?";      
            try {
                $stmt = $conn->prepare($query);
                $stmt->execute([$nueva1, $informacionUsuario['nombreUsuario']]);        
                echo "Contraseña actualizada con éxito.";
                return true;
            } catch (PDOException $e) {
                echo "Error al actualizar: " . $e->getMessage();
                return false;
            }
        } else {
            echo "La contraseña actual no es correcta.";
            return false;

        }

    }


    if (!$informacionUsuario) {
        session_destroy();
        header('Location: index.php');
        exit;
    }


} catch (Exception $e) {
    die("Error crítico: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi cuenta - Canary Travel</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<style>
    .lenguajes {
        margin: 5px;
    }

    .contenido {
        background-color: #fff;
        padding: 25px;
        border-radius: 12px;
        height: 100%;
        color: #333;
    }

    .texto-azul {
        color: #0d6efd !important;
    }

    body {
        background-color: #ffffffff;
    }

    .inputPadd{
        padding: 5px;
        border-radius: 5px;
    }

    /* ── Suscripciones ── */
    .plans-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-top: 12px;
    }

    .plan {
        border: 2px solid #dee2e6;
        border-radius: 10px;
        padding: 20px 14px 16px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        position: relative;
        background: #fff;
        transition: border-color 0.2s, box-shadow 0.2s;
        text-decoration: none;
        color: inherit;
    }

    .plan:hover {
        border-color: #0d6efd;
        box-shadow: 0 4px 14px rgba(13,110,253,0.12);
        text-decoration: none;
        color: inherit;
    }

    .plan.active {
        border-color: #0d6efd;
        background: #f0f6ff;
    }

    .plan-badge {
        position: absolute;
        top: -12px;
        left: 50%;
        transform: translateX(-50%);
        background: #0d6efd;
        color: #fff;
        font-size: 0.68rem;
        font-weight: 700;
        padding: 2px 12px;
        border-radius: 20px;
        white-space: nowrap;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .plan-name {
        font-size: 1rem;
        font-weight: 700;
        color: #0d6efd;
        margin-top: 4px;
    }

    .plan-price {
        font-size: 1.6rem;
        font-weight: 700;
        color: #111;
        line-height: 1;
    }

    .plan-price span {
        font-size: 0.85rem;
        font-weight: 400;
        color: #666;
    }

    .plan-desc {
        font-size: 0.8rem;
        color: #555;
        text-align: center;
        line-height: 1.4;
    }

    .plan-divider {
        width: 100%;
        border: none;
        border-top: 1px solid #e8edf2;
        margin: 4px 0;
    }
    .feature-list {
        list-style: none;
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 5px;
        margin-bottom: 4px;
        padding: 0;
    }
    .feature-list li {
        font-size: 0.8rem;
        color: #444;
        display: flex;
        align-items: center;
        gap: 7px;
    }
    .feature-list li::before {
        content: '✓';
        color: #0d6efd;
        font-weight: 700;
        flex-shrink: 0;
    }
    .feature-list li.missing {
        color: #bbb;
    }
    .feature-list li.missing::before {
        content: '–';
        color: #ccc;
    }
    .plan-btn {
        margin-top: 4px;
        width: 100%;
        padding: 8px 0;
        border: none;
        border-radius: 7px;
        font-size: 0.88rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.18s, transform 0.1s;
        background: #0d6efd;
        color: #fff;
    }
    .plan-btn:hover {
        background: #0a58ca;
        transform: translateY(-1px);
    }
    .plan-btn.current {
        background: #e0e7ef;
        color: #0d6efd;
        cursor: default;
    }

    .plan-btn.current:hover {
        background: #e0e7ef;
        transform: none;
    }

    .current-label {
        font-size: 0.72rem;
        color: #0d6efd;
        font-weight: 600;
        background: #dceeff;
        border-radius: 20px;
        padding: 2px 10px;
    }

    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.35);
        z-index: 200;
        align-items: center;
        justify-content: center;
    }

    .modal-overlay.open { display: flex; }

    .modal-box {
        background: #fff;
        border-radius: 12px;
        padding: 28px 24px;
        max-width: 360px;
        width: 90%;
        box-shadow: 0 8px 32px rgba(0,0,0,0.14);
        text-align: center;
    }

    .modal-box h2 { color: #0d6efd; font-size: 1.1rem; margin-bottom: 8px; }
    .modal-box p  { color: #555; font-size: 0.9rem; margin-bottom: 20px; }

    .modal-btns {
        display: flex;
        gap: 10px;
        justify-content: center;
    }

    .modal-btns button {
        padding: 8px 22px;
        border-radius: 7px;
        border: none;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-confirm { background: #0d6efd; color: #fff; }
    .btn-confirm:hover { background: #0a58ca; }
    .btn-cancel  { background: #e0e7ef; color: #333; }
    .btn-cancel:hover { background: #cdd7e5; }

    @media (max-width: 600px) {
        .plans-grid { grid-template-columns: 1fr; }
    }
</style>
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
    <main class="container" style="margin-top: 100px !important;">
        <h3 class="display-3 fw-bold texto-azul mb-4" id="datosPersonales">
            Usuario:
            <?php
                if(isset($_SESSION['usuario'])){
                    $userLogged = $_SESSION['usuario'];
                    echo $userLogged;
                }
            ?>
        </h3>
        <h5 class="display-3 fw-bold texto-azul mb-4">
            <?php echo $informacionUsuario['rol']; ?>
        </h5>
        <div class="row g-4">
            <div class="col-lg-4 col-md-5">
                <div class="contenido border shadow-sm text-dark">
                <img src="<?php echo !empty($informacionUsuario['foto_perfil']) ? $informacionUsuario['foto_perfil'] : 'static/img/usuario.png'; ?>" 
                    alt="foto de perfil" class="img-fluid rounded mb-4 shadow-sm">
                
                <div class="d-grid gap-2">
                    <button id="upload_widget" class="btn btn-primary">Subir nueva foto</button>
                </div>
        </div>
            </div>

            <div class="col-lg-8 col-md-7">
                <div class="contenido border shadow-sm text-dark">

                    <!-- Datos personales -->
                    <h3 class="texto-azul">Datos personales</h3>
                    <div class="row">
                        <div class="col-md-8">
                            <p class="mb-1"><strong>Nombre: </strong><?php echo $informacionUsuario['nombre'] ?></p>
                            <p class="mb-1"><strong>Apellidos: </strong><?php echo $informacionUsuario['apellidos'] ?></p>
                            <p class="mb-1"><strong>DNI: </strong><?php echo $informacionUsuario['dni'] ?></p>
                            <p class="mb-1"><strong>Teléfono: </strong><?php echo $informacionUsuario['Telefono'] ?></p>
                            <p class="mb-1"><strong>Correo electrónico: </strong><?php echo $informacionUsuario['Correo'] ?></p>
                            <p class="mb-1"><strong>Fecha de nacimiento: </strong><?php echo $fechaFormateada ?></p>
                        </div>
                    </div>
                    <hr>

                    <!-- Cambiar contraseña -->
                    <h3 class="texto-azul">Cambiar contraseña</h3>
                    <form action="" method='post'>
                        <input type="password" name="actual" placeholder="Contraseña actual" class="inputPadd">
                        <input type="password" name="nueva" placeholder="Contraseña nueva" class="inputPadd">
                        <input type="password" name="repiteNueva" placeholder="Repite la nueva contraseña" class="inputPadd">
                        <button type="submit" class="btn btn-primary">Cambiar la contraseña</button>
                    </form>
                    <?php
                        if($_SERVER['REQUEST_METHOD'] == 'POST'){
                            if(isset($_POST['actual']) && isset($_POST['nueva']) && isset($_POST['repiteNueva'])){
                                $actual = $_POST['actual'];
                                $nueva = $_POST['nueva'];  
                                $repiteNueva = $_POST['repiteNueva'];  
                                cambiarPassword($conn, $actual, $nueva, $repiteNueva, $informacionUsuario);
                            }
                        }
                    ?>
                    <hr>

                    <!-- Suscripciones -->
                    <h3 class="texto-azul">Suscripciones</h3>

                    <?php $rolActual = strtolower($informacionUsuario['Rol']); ?>

                    <div class="plans-grid">

                        <!-- FREE -->
                        <div class="plan <?php echo ($rolActual == 'base') ? 'active' : ''; ?>">
                            <?php if($rolActual == 'base'): ?>
                                <span class="current-label">Plan actual</span>
                            <?php endif; ?>
                            <div class="plan-name">Free Plan</div>
                            <div class="plan-price">0 €<span> /mes</span></div>
                            <div class="plan-desc">Perfecto para empezar sin compromiso.</div>
                            <hr class="plan-divider">
                            <ul class="feature-list">
                                <li>Acceso básico</li>
                                <li>Soporte prioritario</li>
                                <li class="missing">Sin anuncios</li>
                                <li class="missing">Descuentos Exclusivos</li>
                                <li class="missing">Capacidad de Añadir 3 alojamientos</li>
                            </ul>
                            <?php if($rolActual == 'base'): ?>
                                <button class="plan-btn current" disabled>Plan actual</button>
                            <?php else: ?>
                                <button class="plan-btn" onclick="openModal('free', 'gratuito')">Cambiar a Free</button>
                            <?php endif; ?>
                        </div>

                        <!-- PRO -->
                        <div class="plan <?php echo ($rolActual == 'pro') ? 'active' : ''; ?>">
                            <div class="plan-badge">Más popular</div>
                            <?php if($rolActual == 'pro'): ?>
                                <span class="current-label">Plan actual</span>
                            <?php endif; ?>
                            <div class="plan-name">Plan Pro</div>
                            <div class="plan-price">9,99 €<span> /mes</span></div>
                            <div class="plan-desc">Ideal para usuarios activos que quieren más.</div>
                            <hr class="plan-divider">
                            <ul class="feature-list">
                                <li>Acceso completo</li>
                                <li>Soporte prioritario</li>
                                <li>Sin anuncios</li>
                                <li>Descuentos Exclusivos</li>
                                <li class="missing">Capacidad de Añadir 3 alojamientos</li>
                            </ul>
                            <?php if($rolActual == 'pro'): ?>
                                <button class="plan-btn current" disabled>Plan actual</button>
                            <?php else: ?>
                                <button class="plan-btn" onclick="openModal('Plan Pro', '9,99 €/mes')">Suscribirse</button>
                            <?php endif; ?>
                        </div>

                        <!-- BUSINESS -->
                        <div class="plan <?php echo ($rolActual == 'business') ? 'active' : ''; ?>">
                            <?php if($rolActual == 'business'): ?>
                                <span class="current-label">Plan actual</span>
                            <?php endif; ?>
                            <div class="plan-name">Plan Business</div>
                            <div class="plan-price">24,99 €<span> /mes</span></div>
                            <div class="plan-desc">Para equipos y empresas que necesitan lo mejor.</div>
                            <hr class="plan-divider">
                            <ul class="feature-list">
                                <li>Acceso completo</li>
                                <li>Soporte prioritario</li>
                                <li>Sin anuncios</li>
                                <li>Descuentos Exclusivos</li>
                                <li>Capacidad de Añadir 3 alojamientos</li>
                            </ul>
                            <?php if($rolActual == 'business'): ?>
                                <button class="plan-btn current" disabled>Plan actual</button>
                            <?php else: ?>
                                <button class="plan-btn" onclick="openModal('Plan Business', '24,99 €/mes')">Suscribirse</button>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </main>
   
    <div class="modal-overlay" id="modalSuscripcion">
        <div class="modal-box">
            <h2>Confirmar suscripción</h2>
            <p id="modal-text"></p>
            <div class="modal-btns">
                <button class="btn-cancel" onclick="closeModal()">Cancelar</button>
                <button class="btn-confirm" onclick="confirmPurchase()">Confirmar</button>
            </div>
        </div>
    </div>

    <br><br>

    <script>
        let selectedPlan = '';

        function openModal(plan, price) {
            selectedPlan = plan;
            const msg = price === 'gratuito'
                ? `¿Deseas cambiar al <strong>${plan}</strong>?`
                : `¿Deseas suscribirte al <strong>${plan}</strong> por <strong>${price}</strong>?`;
            document.getElementById('modal-text').innerHTML = msg;
            document.getElementById('modalSuscripcion').classList.add('open');
        }

        function closeModal() {
            document.getElementById('modalSuscripcion').classList.remove('open');
        }

        function confirmPurchase() {
            closeModal();
            let planParam = selectedPlan.toLowerCase().replace('plan ', '');
            
            if(planParam === 'free') planParam = 'base';

            if(planParam === 'base'){
                window.location.href = `actualizar_rol.php?plan=base`;
            }else{
                window.location.href = `pago.php?type=suscripcion&plan=${planParam}`;
            }

        }

        document.getElementById('modalSuscripcion').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });
    </script><!-- Script de Cloudinary -->
    <script src="https://upload-widget.cloudinary.com/global/all.js" type="text/javascript"></script>
    <script>
        var myWidget = cloudinary.createUploadWidget({
            cloudName: 'dkbepwbpj', 
            uploadPreset: 'canary'
        }, (error, result) => { 
            if (!error && result && result.event === "success") { 
                console.log('Imagen subida con éxito: ', result.info.secure_url);
                // Enviamos la URL al servidor mediante una redirección o un formulario oculto
                window.location.href = `actualizar_foto_perfil.php?url=${encodeURIComponent(result.info.secure_url)}`;
            }
        });

        document.getElementById("upload_widget").addEventListener("click", function(e){
            e.preventDefault();
            myWidget.open();
        }, false);
    </script>
</body>
</html>