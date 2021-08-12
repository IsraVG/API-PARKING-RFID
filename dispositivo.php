<?php

require_once "Clases/respuestas.class.php";
require_once "Clases/dispositivoConfig.class.php";
$_respuestas = new respuestas;
$_dispositivo = new dispositivoConfig;

if($_SERVER["REQUEST_METHOD"] == "GET"){

    if(isset($_GET["serie"])){
        $serie = $_GET["serie"];
        $evento = $_dispositivo->getDatosDispositivo($serie);
        header("Content-Type: application/json");
        echo json_encode($evento);
        http_response_code(200);
    }
}

?>