<?php
session_start();
include 'conectar.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['titulo'])) {
    $titulo      = $_POST['titulo'];
    $tipo        = $_POST['tipo'];
    $lugar       = $_POST['lugar'];
    $precio      = $_POST['precio'];
    $descripcion = $_POST['descripcion'];
    $imagen      = $_POST['imagen']; 

    try {
        $conn = conectarBD();
        $query = "INSERT INTO recomendaciones (titulo, tipo, lugar, precio, descripcion, imagen) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->execute([$titulo, $tipo, $lugar, $precio, $descripcion, $imagen]);
        header('Location: recomendaciones.php?status=recomendacion_inser');
        exit();
    } catch (PDOException $e) {
        die("Error al guardar la recomendación: " . $e->getMessage());
    }
}
?>
