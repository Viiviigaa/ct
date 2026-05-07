<?php
    function conectarBD(){
        $host     = getenv('DB_HOST') ?: "canarytravel-1-viviga04-fc6b.d.aivencloud.com";
        $port     = getenv('DB_PORT') ?: "23695";
        $dbname   = getenv('DB_NAME') ?: "canaryTravel";
        $username = getenv('DB_USER') ?: "avnadmin";
        $password = getenv('DB_PASS') ?: "AVNS_pwhka325GRZ_WJAmz3A";

        // Usar variable de entorno DB_CA_CERT si existe, si no caer al archivo local
        $ca_cert_env = getenv('DB_CA_CERT');
        if ($ca_cert_env) {
            $ca_cert = sys_get_temp_dir() . '/ca.pem';
            file_put_contents($ca_cert, $ca_cert_env);
        } else {
            $ca_cert = __DIR__ . '/certs/ca.pem';
        }

        try{
            $options = [
                PDO::MYSQL_ATTR_SSL_CA => $ca_cert,
                PDO::ATTR_ERRMODE      => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];

            $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";  
            $pdo = new PDO($dsn, $username, $password, $options);
            return $pdo; 
        }catch(PDOException $e){
            die("Error de conexión: " . $e->getMessage());
        }
    }
?>