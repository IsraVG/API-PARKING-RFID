<?php

require_once "Conexion/conexion.php";
require_once "respuestas.class.php";

class dispositivoConfig extends conexion{

    //protected $arduinoCodigo = "";

    public function getDatosDispositivo($dispositivoSerie){
        $query = "SELECT movimiento_evento
        FROM dispositivo_evento 
        WHERE numero_serie = '$dispositivoSerie'";
        //print_r($query);
        return parent::obtenerDatos($query);
    }
    

}

?>