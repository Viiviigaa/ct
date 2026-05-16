<?php
session_start();
include 'conectar.php';

if (isset($_SESSION['usuario']) && isset($_GET['plan'])) {
    $user = $_SESSION['usuario'];
    $nuevoRol = $_GET['plan'];

    try {
        $conn = conectarBD();
        $sql = "UPDATE usuarios SET Rol = ? WHERE nombreUsuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$nuevoRol, $user]);
        header('Location: informacionCuenta.php?mensaje=Plan actualizado con éxito');
        exit;
    } catch (PDOException $e) {
        die("Error al actualizar el plan: " . $e->getMessage());
    }
} else {
    header('Location: index.php');
    exit;
}