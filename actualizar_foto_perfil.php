<?php
session_start();
include 'conectar.php';

if (isset($_GET['url']) && isset($_SESSION['usuario'])) {
    $nuevaUrl = $_GET['url'];
    $usuario = $_SESSION['usuario'];

    try {
        $conn = conectarBD();
        $query = "UPDATE usuarios SET foto_perfil = ? WHERE nombreUsuario = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$nuevaUrl, $usuario]);
        header('Location: informacionCuenta.php?status=foto_actualizada');
    } catch (PDOException $e) {
        die("Error al guardar la URL: " . $e->getMessage());
    }
}

function insertarRecomendacion($titulo, $desc, $img,$precio, $lugar, $tipoAct){
    $titulo = $_POST['titulo'] ?? null;
    $tipoActividad = $_POST['tipo'] ?? null;
    $precio = $_POST['precio'] ?? null;
    $descrip = $_POST['descripcion'] ?? null;
    $imagen = $_POST['imagen'] ?? null; 
    $lugar = $_POST['lugar'] ?? null;
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
?>