<?php
    include 'conectar.php';
    session_start();
    if($_SESSION['rol']!= 'administrador'){
        header('Location: index.php');
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión Integral - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; }
        .sidebar { height: 100vh; position: fixed; top: 0; left: 0; padding: 20px; background: #2c3e50; color: white; z-index: 100; }
        .main-content { margin-left: 16.6%; padding: 40px; }
        .admin-card { background: white; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 50px; border: none; }
        .card-header-admin { background: white; border-bottom: 2px solid #f4f7f6; padding: 20px; border-radius: 10px 10px 0 0; }
        .btn-action { padding: 5px 15px; border-radius: 20px; }
        .sticky-section-title { position: sticky; top: 0; background: #f4f7f6; z-index: 10; padding: 10px 0; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Fijo -->
        <nav class="col-md-2 d-none d-md-block sidebar">
            <h3 class="mb-4 text-center text-info">Admin v1.0</h3>
            <ul class="nav flex-column">
                <li class="nav-item mb-2">
                    <a class="nav-link text-white" href="#sec-recomendaciones"><i class="bi bi-chat-left-quote me-2"></i> Moderación</a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link text-white" href="#sec-usuarios"><i class="bi bi-people me-2"></i> Usuarios</a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link text-white" href="#sec-reservas"><i class="bi bi-calendar-check me-2"></i> Reservas</a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link text-white" href="#sec-alojamientos"><i class="bi bi-house-door me-2"></i> Alojamientos</a>
                </li>
                <li class="nav-item mt-5">
                    <a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión</a>
                </li>
            </ul>
        </nav>

        <!-- Contenido Desplazable -->
        <main class="col-md-10 main-content">
            
            <h1 class="mb-5">Panel de Control General</h1>

            <!-- 1. SECCIÓN RECOMENDACIONES -->
            <div id="sec-recomendaciones" class="admin-card card">
                <div class="card-header-admin d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 text-primary">Recomendaciones Pendientes</h4>
                    <span class="badge bg-warning text-dark">Moderación requerida</span>
                </div>
                <div class="card-body">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Alojamiento</th>
                                <th>Comentario</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Ejemplo de datos -->
                            <tr>
                                <td><strong>@user99</strong></td>
                                <td>Villa Sol</td>
                                <td>"Increíble vista, volveré pronto."</td>
                                <td>
                                    <a href="moderar.php?id=1&estado=ok" class="btn btn-success btn-action btn-sm">Aceptar</a>
                                    <a href="moderar.php?id=1&estado=no" class="btn btn-outline-danger btn-action btn-sm">Denegar</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 2. SECCIÓN USUARIOS -->
            <div id="sec-usuarios" class="admin-card card">
                <div class="card-header-admin">
                    <h4 class="mb-0 text-success">Gestión de Usuarios</h4>
                </div>
                <div class="card-body text-center py-4">
                    <p class="text-muted">Aquí puedes ver y editar los roles de los usuarios registrados.</p>
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>DNI</th>
                                <th>Nombre</th>
                                <th>Rol Actual</th>
                                <th>Cambiar Rol</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- PHP Foreach aquí -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 3. SECCIÓN RESERVAS -->
            <div id="sec-reservas" class="admin-card card">
                <div class="card-header-admin">
                    <h4 class="mb-0 text-info">Reservas Activas</h4>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Fecha Entrada</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- PHP Foreach aquí -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 4. SECCIÓN ALOJAMIENTOS -->
            <div id="sec-alojamientos" class="admin-card card border-primary">
                <div class="card-header-admin d-flex justify-content-between">
                    <h4 class="mb-0 text-secondary">Catálogo de Alojamientos</h4>
                    <button class="btn btn-primary btn-sm">+ Añadir Nuevo</button>
                </div>
                <div class="card-body">
                    <!-- Lista de alojamientos -->
                    <div class="row">
                        <!-- Ejemplo de item -->
                        <div class="col-md-4 mb-3">
                            <div class="p-3 border rounded">
                                <h6>Apartamento Centro</h6>
                                <p class="small text-muted">Precio: 85€/noche</p>
                                <button class="btn btn-link btn-sm p-0">Editar datos</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>