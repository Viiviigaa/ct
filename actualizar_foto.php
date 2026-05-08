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
?>