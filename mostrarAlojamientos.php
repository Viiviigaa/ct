<?php
    include_once 'conectar.php';
    
    function mostrarAlojamientos($isla, $max_huespedes){
        $conn = conectarBD();
        $sql = "Select ID,nombreAlojamiento, isla, descripcion, fotos, precio, direccion, max_huespedes from alojamientos where isla = ? and max_huespedes >= ? ORDER BY precio ASC";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$isla, $max_huespedes]);
        $alojamientoEncontrados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $alojamientoEncontrados;
    }

    function mostrarTodasRecomendaciones() {
            $conn = conectarBD();
            $sql = "SELECT * FROM recomendaciones";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

    function mostrarRecomendaciones($isla, $tipoActividad) {
        $conn = conectarBD();
        $sql = "SELECT * FROM recomendaciones WHERE 1=1";
        $params = [];
        if (!empty($isla)) {
            $sql .= " AND lugar = :isla";
            $params[':isla'] = $isla;
        }
        if (!empty($tipoActividad)) {
            $sql .= " AND tipoActividad = :tipo";
            $params[':tipo'] = $tipoActividad;
        }
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function infoAlojamiento($id){
        $conn = conectarBD();
        $sql = "Select ID, nombreAlojamiento, isla, descripcion, fotos, precio, direccion, max_huespedes from alojamientos where id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        $alojamiento = $stmt->fetch(PDO::FETCH_ASSOC);
        return $alojamiento;
    }

    function mostrarCoches(){
        $conn = conectarBD();
        $sql = "Select * from car_Rental ORDER BY precio ASC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $cochesEncontrados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $cochesEncontrados;
    }

    function mostrarFerrys(){
        $conn = conectarBD();
        $sql = "SELECT * FROM ferrys order by precio asc";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $ferrysEncontrados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $ferrysEncontrados;
    }
?>