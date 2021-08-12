<?php

require_once "Clases/respuestas.class.php";
require_once "Clases/validacion.class.php";
$_respuestas = new respuestas;
$_validacion = new validacion;


//******************- GET -********************//

if($_SERVER["REQUEST_METHOD"] == "GET"){
    
    if (isset($_GET["codigoTarjeta"]) && isset($_GET["presencia"]) && isset($_GET["evento"])){

        try{

            $getInfo = "";
            $tarjeta = $_GET["codigoTarjeta"];
            //busca datos de la tarjeta en la DB
            $getInfo = $_validacion->getDatosTarjeta($tarjeta);
            $evento = $_GET["evento"];
            $statusAcceso = null;
            //Valida que el evento sea salida o entrada
            if($evento == "E" || $evento == "S"){

                if($getInfo){
                    // valida presencia
                    if($_GET["presencia"]== 1 ){
                        // Tarjeta Ghost (No deja registro, No tiene Antipassback)
                        if($getInfo[0]['tarj_tipta_id'] == 5){

                            $status = true;
                            $message = $evento =='E' ? '   BIENVENIDO' : "  VUELVA PRONTO";
                            $getInfo[0]["pnds_nombre"] = "";

                        }else{
                            //Valida que la tarjeta esté ligada
                            if($getInfo[0]['pnds_id'] != null){

                                if($getInfo[0]['tarj_activo'] == 0){
                                    // valida que la tarjeta este activa
                                    $status = false;
                                    $message = 'TARJETA INACTIVA';
                                   
                                }
                                else if($getInfo[0]['pnds_activo'] == 0){
                                    //valida que el pensionado este activo
                                    $status = false;
                                    $message = '  PENS INACTIVO';
                                    
                                }
                                else if($getInfo[0]['tarj_tipta_id'] == 2){
                                    // Tarjeta Valet (Sin antipassback)
                                    $status = true;
                                    $message = $evento =='E' ? ' ENTRADA VALET' : "  SALIDA VALET";
                                    $statusAcceso = 0;

                                }else{

                                    $passBack = $evento =='E' ? 1 : 0;
                                    if($getInfo[0]['tarj_estatus_antipb'] == $passBack){
                                        //Valida antipassback
                                        $status = false;
                                        $message = '  ANTIPASSBACK';
                                        $statusAcceso = 4;
                                    }else{

                                        $status = true;
                                        $statusAcceso = 0;
                                        switch($getInfo[0]['tarj_tipta_id']){
                                            //Tarjeta Pensionado
                                            case 1: //valida pago
                                                    if($getInfo[0]['tarj_estatus_pago']!=1){

                                                    $status = false;    
                                                    $message = '  FALTA DE PAGO';
                                                    $statusAcceso = 6;
                                                    }else{

                                                        $message = $evento =='E' ? 'BIENVENIDO PENS' : "  VUELVA PRONTO";
                                                        $_validacion->actualizaPassBack($tarjeta);
                                                        
                                                    }
                                                    break;

                                            case 3: $message = $evento =='E' ? 'BIENVENIDO VISIT' : "  VUELVA PRONTO";
                                                    $_validacion->actualizaPassBack($tarjeta);
                                                    break;
                                                    
                                            case 4: $message = $evento =='E' ? 'BIENVENIDO PENS' : "  VUELVA PRONTO";
                                                    $_validacion->actualizaPassBack($tarjeta);
                                                    break;
                                        }
                                    }
                                }
                                
                            }else{
                                $status = false;
                                $message = 'TARJ. SIN LIGAR';
                                //echo("Tarjeta no ligada");
                            }

                            $registro = $_validacion->insertRegistro($getInfo[0]['pnds_tarj_id'], $getInfo[0]['pnds_id'],$evento, $statusAcceso);
                        }
                        
                    }else{
                        $status = false;
                        $message = '  SIN PRESENCIA';
                    }
                    
    
                    $success = true;
                }else{
                    $success = false;
                    $status = false;
                    $message = 'TARJETA INVALIDA';
                    $statusAcceso = 1;
                    $registro = $_validacion->insertRegistro(null, null,$evento ,$statusAcceso, $tarjeta);
                    //echo("tarjeta nula");
                }

            }else{
                $success = false;
                $status = false;
                $message = 'ERROR DE EVENTO';
            }
            

            //header("Content-Type: application/json");
            //echo json_encode($datosTarjeta);
            //http_response_code(200);

        }catch(Exception $e){
            $success = false;
            $status = false;
            $message = 'error'.$e->getMessage();
	    }
        $res = array('success' => $success,
				'status' => $status,
				'message' => $message, 
				'getInfo' => $getInfo);
	    echo json_encode($res);
        
    }

 
}


?>