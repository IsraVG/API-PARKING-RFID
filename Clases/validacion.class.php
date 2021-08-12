<?php 
    require_once "Conexion/conexion.php";
    require_once "respuestas.class.php";

    class validacion extends conexion{

        //protected $tblTarjeta = "tbl_tarjetas";
        //protected $tblPensionado = "tbl_pensionados"; 

        public function getDatosTarjeta($tarjeta){
            $query = "SELECT pp.pnds_id, pp.pnds_tarj_id, pp.pnds_nombre, pp.pnds_activo,
             tt.tarj_activo, tt.tarj_estatus_antipb, tt.tarj_tipta_id, tt.tarj_estatus_pago
			FROM tbl_pensionados as pp
			RIGHT JOIN tbl_tarjetas as tt ON tt.tarj_id = pp.pnds_tarj_id
			WHERE tarj_codigo = '$tarjeta'";
            //print_r($query);
            return parent::obtenerDatos($query);
        }

        public function actualizaPassBack($tarjeta){
            $query = "UPDATE tbl_tarjetas SET tarj_estatus_antipb = !tarj_estatus_antipb WHERE tarj_codigo = '$tarjeta'";
            $resp = parent::nonQuery($query);
            return $resp;
        }

        public function insertRegistro($tarjeta_id,$pensionado_id,$evento,$estatus,$TarjetaInvalida=""){
            $tabla = "tbl_movimientos_pensionados";
            if($TarjetaInvalida!=""){
                $query = "INSERT INTO " . $tabla . " (mope_lectura, mope_evento ,mope_cea_id_int , mope_fecha, mope_hora) 
                VALUES ('$TarjetaInvalida', '$evento', '$estatus', '".date("Y-m.d")."', '".date("H:i:s")."')";
            }else{
                $query = "INSERT INTO " . $tabla . " (mope_tarj_id, mope_pnds_id, mope_evento, mope_cea_id_int, mope_fecha, mope_hora) 
                VALUES ('$tarjeta_id', '$pensionado_id', '$evento', '$estatus', '".date("Y-m.d")."', '".date("H:i:s")."')";
            }
            $resp = parent::nonQuery($query);
            return $resp;
        }


    }

?>