<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - Canary Travel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Estilos para tu cabecera personalizada */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: #ffffff;
            border-bottom: 1px solid #ddd;
            sticky: top;
            z-index: 1000;
        }
        #menuPrincipial {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: black;
        }
        #logo { width: 50px; margin-right: 10px; }
        .logs .btn { margin-left: 5px; }

        /* Ajustes para el Menú Lateral (Claro) */
        .offcanvas { background-color: #f8f9fa; border-right: 1px solid #dee2e6; }
        .list-group-item { background-color: transparent; border: none; }
        .list-group-item a { text-decoration: none; color: #333; font-weight: 500; }
        .list-group-item a:hover { color: #0d6efd; }

        /* Cuerpo del Panel */
        body { background-color: #f4f7f6; }
        .admin-section { 
            background: white; 
            border-radius: 12px; 
            padding: 25px; 
            margin-bottom: 40px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border: 1px solid #e9ecef;
        }
        .section-title { border-left: 5px solid #0d6efd; padding-left: 15px; margin-bottom: 25px; }
    </style>
</head>
<body>

    <!-- TU CABECERA PERSONALIZADA -->
    <header>   
        <img src="static/img/lista.png" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample" width="30" style="cursor:pointer"> 
        
        <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">  
            <div class="offcanvas-header">    
                <h5 class="offcanvas-title" id="offcanvasExampleLabel">Gestión Admin</h5>    
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
                    <li class="list-group-item"><a href="informacionCuenta.php">Mi cuenta</a></li> 
                    <li class="list-group-item"><a href="misReservas.php">Mis reservas</a></li>    
                    <li class="list-group-item"><a href="recomendaciones.php">Recomendaciones Públicas</a></li>  
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
        
        <!-- 1. MODERACIÓN DE RECOMENDACIONES -->
        <div id="sec-recomendaciones" class="admin-section">
            <h3 class="section-title">Moderación de Recomendaciones</h3>
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Usuario</th>
                        <th>Comentario</th>
                        <th>Fecha</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Carlos Santana</td>
                        <td>"El apartamento en Corralejo fue fantástico."</td>
                        <td>2024-03-14</td>
                        <td>
                            <button class="btn btn-success btn-sm">Publicar</button>
                            <button class="btn btn-danger btn-sm">Descartar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- 2. USUARIOS -->
        <div id="sec-usuarios" class="admin-section">
            <h3 class="section-title">Gestión de Usuarios</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>DNI</th>
                        <th>Nombre</th>
                        <th>Rol</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Aquí el foreach de PHP -->
                </tbody>
            </table>
        </div>

        <!-- 3. RESERVAS -->
        <div id="sec-reservas" class="admin-section">
            <h3 class="section-title">Reservas del Sistema</h3>
            <div class="alert alert-info">Total de reservas activas hoy: 12</div>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Ref</th>
                        <th>Cliente</th>
                        <th>Alojamiento</th>
                        <th>Check-in</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Datos -->
                </tbody>
            </table>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>