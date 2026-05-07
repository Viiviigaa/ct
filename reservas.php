<?php

    include_once 'conectar.php';
    
    function insertarReserva($id, $idAlojamiento, $fechaInicio, $fechaFinal, $cantidadHuespedes, $dniReserva){
        try{
            $conn = conectarBD();
            $sql = "INSERT INTO reservas (id, idAlojamiento, fechaInicio, fechaFinal, cantidadHuespedes, dniReserva) VALUES (:id, :idAlojamiento, :fechaInicio, :fechaFinal, :cantidadHuespedes, :dniReserva)";
            $stmt = $conn->prepare($sql);
            $resultado = $stmt->execute([
                    'id' => $id,
                    'idAlojamiento'=> $idAlojamiento,
                    'fechaInicio' => $fechaInicio,
                    'fechaFinal' => $fechaFinal,
                    'cantidadHuespedes' => $cantidadHuespedes,
                    'dniReserva' => $dniReserva,
                ]);
            return $resultado;
        }catch (PDOException $e){
            error_log("Error en insertarReserva: " . $e->getMessage());
            return false;
        }
    }

?>