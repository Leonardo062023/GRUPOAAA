<?php
     
class movimiento {

    public static function save_data($numero, $fecha, $descripcion, $plazoE, $observaciones, $tipoM, $param, $responsable, $tercero, $dependencia, $centrocosto, $rrptal, $proyecto, $lugarE, $unidadE, $estado, $iva, $compania, $tipo_doc_sop, $num_doc_sop, $fuente) {
        require ('../Conexion/conexion.php');
        $inserted = false;
        $agr = false;
        $sql = "INSERT INTO gf_movimiento(numero, fecha, descripcion, plazoentrega, observaciones, tipomovimiento, parametrizacionanno, tercero, tercero2, dependencia, centrocosto, rubropptal, proyecto, lugarentrega, unidadentrega, estado, porcivaglobal, compania, tipo_doc_sop, numero_doc_sop, fuente)
              VALUES ($numero, $fecha, $descripcion, $plazoE, $observaciones, $tipoM, $param, $responsable, $tercero, $dependencia, $centrocosto, $rrptal, $proyecto, $lugarE, $unidadE, $estado, $iva, $compania, $tipo_doc_sop, $num_doc_sop, $fuente)";
        $result = $mysqli->query($sql);
        if($result == true) {
        $inserted = true;
        }
        return $inserted;
    }

    public static function get_last_id($tipomovimiento, $numero) {
        require ('../Conexion/conexion.php');
        $id_unico = 0;
        $sqlUL = "SELECT MAX(id_unico) FROM gf_movimiento WHERE numero=$numero AND tipomovimiento=$tipomovimiento";
        $resultUL = $mysqli->query($sqlUL);
        $rows = mysqli_num_rows($resultUL);
        if($rows > 0){
            $fila = mysqli_fetch_row($resultUL);
            $id_unico = $fila[0];
        }
        return $id_unico;
    }


    public static function save_detail_mov($planI, $cantidad, $valor, $valorIva , $movimiento, $afectado = 'NULL') {
        require ('../Conexion/conexion.php');
        $valorF=0;
        $inserted = "";
        date_default_timezone_set('America/Bogota');
        $inv = "SELECT 	iva_descontable FROM gf_plan_inventario WHERE id_unico = $planI";
        $invResult = $mysqli->query($inv);
        $fila = mysqli_fetch_row($invResult);
        $iva_descontable=$fila[0];
        if ($iva_descontable==2) {
           $valorF=$valor+$valorIva;
           $iva=0;
        }else{
            $valorF=$valor;
            $iva=$valorIva;
        }
        $hora     = date('H:i:s');
        $sql = "INSERT INTO gf_detalle_movimiento(planmovimiento, cantidad, valor, iva, movimiento, detalleasociado, hora) VALUES($planI, $cantidad, $valorF, $iva, $movimiento, $afectado, '$hora');";
        $result = $mysqli->query($sql);
        if ($result == true) {
        $inserted = true;
        }
        return $inserted;
    }

    public static function save_detail_mov_N($planI, $cantidad, $valor, $valorIva , $movimiento,  $impo) {
        require ('../Conexion/conexion.php');

        $inserted = "";
        date_default_timezone_set('America/Bogota');
        $hora     = date('H:i:s');
        $sql = "INSERT INTO gf_detalle_movimiento(planmovimiento, cantidad, valor, iva, movimiento, detalleasociado, hora, impoconsumo) VALUES($planI, $cantidad, $valor, $valorIva, $movimiento, NULL, '$hora', $impo);";
        $result = $mysqli->query($sql);
        if ($result == true) {
            $inserted = true;
        }
        return $inserted;
    }

    public static function save_detail_mov_ajuste($planI, $cantidad, $valor, $valorIva ,$movimiento, $afectado = 'NULL', $ajuste) {
        require ('../Conexion/conexion.php');
        $inserted = "";
        $sql = "INSERT INTO gf_detalle_movimiento(planmovimiento, cantidad, valor, iva, movimiento, detalleasociado, ajuste) VALUES($planI, $cantidad, $valor, $valorIva, $movimiento, $afectado, $ajuste);";
        $result = $mysqli->query($sql);
        if ($result == true) {
            $inserted = true;
        }
        return $inserted;
    }

    public static function get_detail_mov ($id_unico) {
        require ('../Conexion/conexion.php');
        $details = array();
        if(!empty($id_unico)){
            $sql = "SELECT id_unico FROM gf_detalle_movimiento WHERE movimiento = $id_unico";
            $result = $mysqli->query($sql);
            $rows = mysqli_num_rows($result);
            if($rows > 0){
                while ($row = mysqli_fetch_row($result)) {
                    $details[] = $row[0];
                }
            }
        }
        return $details;
    }

    public static function get_detail_mov2 ($id_unico) {
        require ('../Conexion/conexion.php');
        $details = array();
        if(!empty($id_unico)){
            $sql = "SELECT    dtm.id_unico
                    FROM      gf_detalle_movimiento as dtm
                    LEFT JOIN gf_plan_inventario    as pln ON dtm.planmovimiento = pln.id_unico
                    WHERE     dtm.movimiento      = $id_unico";
            $result = $mysqli->query($sql);
            $rows = mysqli_num_rows($result);
            if($rows > 0){
                while ($row = mysqli_fetch_row($result)) {
                    $details[] = $row[0];
                }
            }
        }
        return $details;
    }

    public static function get_values_detail ($id_unico) {
        require ('../Conexion/conexion.php');
        $values = array();
        $sql = "SELECT planmovimiento, cantidad, valor, iva  FROM gf_detalle_movimiento WHERE  id_unico = $id_unico";
        $result = $mysqli->query($sql);
        $rows = mysqli_num_rows($result);
        if($rows > 0) {
            $row = mysqli_fetch_row($result);
            for($a = 0; $a < count($row); $a ++) {
                $values[] = $row[$a];
            }
        }
        return $values;
    }

    public static function modify_data($id_unico, $fecha, $observaciones, $descripcion, $iva, 
            $responsable, $tercero, $fuente, $proyecto) {
        require ('../Conexion/conexion.php');
        $edited = false;
        $sql = "UPDATE gf_movimiento SET fecha = $fecha, observaciones = $observaciones, descripcion = $descripcion, 
        porcivaglobal = $iva, tercero = $responsable, tercero2= $tercero, "
                . "fuente = $fuente, proyecto= $proyecto WHERE id_unico = $id_unico";
        $result = $mysqli->query($sql);
        if($result == true) {
            $edited = true;
        }
        return $edited;
    }
     public static function modify_dataEntrada($id_unico, $fecha, $observaciones, $descripcion, $iva, 
            $responsable, $tercero, $fuente, $proyecto, $lugarE, $unidadPE, $plazoE, $centrocosto,$rubroP,$dependencia,$tipo_doc_sopt , $num_doc) {
        require ('../Conexion/conexion.php');
        $edited = false;
         $sql = "UPDATE gf_movimiento SET fecha = $fecha, observaciones = $observaciones, descripcion = $descripcion, 
        porcivaglobal = $iva, tercero = $responsable, tercero2= $tercero,
        fuente = $fuente, proyecto= $proyecto , lugarentrega = $lugarE, unidadentrega = $unidadPE,  plazoentrega =  $plazoE, 
        centrocosto = $centrocosto, rubropptal = $rubroP, dependencia= $dependencia, tipo_doc_sop = $tipo_doc_sopt , 
         numero_doc_sop = $num_doc 

        WHERE id_unico = $id_unico";
        $result = $mysqli->query($sql);
        if($result == true) {
            $edited = true;
        }
        return $edited;
    }


    
    public static function modify_detail ($valor, $cantidad, $valorIva, $id_unico) {
        require ('../Conexion/conexion.php');
        $edited = false;
        $sql = "UPDATE gf_detalle_movimiento SET valor = $valor, cantidad = $cantidad, iva = $valorIva WHERE id_unico = $id_unico";
        $result = $mysqli->query($sql);
        if($result == true) {
            $edited = true;
        }
        return $edited;
    }

    public static function delete_detail ($id_unico) {
        require ('../Conexion/conexion.php');
        $deleted = false;
        $sql = "SELECT producto  FROM gf_movimiento_producto WHERE detallemovimiento = $id_unico";
        $result = $mysqli->query($sql);
        $rows = mysqli_num_rows($result);
        if($rows > 0) {
            while ($row = mysqli_fetch_array($result)) {
                $producto = $row[0];
                $sql = "DELETE FROM gf_producto_especificacion WHERE producto = $producto";
                $result = $mysqli->query($sql);
                $sql = "DELETE FROM gf_movimiento_producto WHERE producto = $producto";
                $result = $mysqli->query($sql);
                $sql = "DELETE FROM gf_producto WHERE id_unico = $producto";
                $result = $mysqli->query($sql);
            }
        }
        $sql = "DELETE FROM gf_detalle_movimiento WHERE id_unico = $id_unico";
        $result = $mysqli->query($sql);
        if($result == true) {
            $deleted = true;
        }
        return $deleted;
    }


    public static function delete_data ($id_unico) {
        require ('../Conexion/conexion.php');
        $deleted = false;
        $sql = "DELETE FROM gf_movimiento WHERE  id_unico = $id_unico";
        $result = $mysqli->query($sql);
        if($result == true) {
            $deleted = true;
        }
        return $deleted;
    }

 
    public static function get_values_aso_for_exit ($id_aso) {
        require ('../Conexion/conexion.php');
        $values = "";
        $sql = "SELECT plazoentrega, rubropptal, lugarentrega, unidadentrega, porcivaglobal, tipo_doc_sop, numero_doc_sop, tercero FROM gf_movimiento WHERE id_unico = $id_aso";
        $result = $mysqli->query($sql);
        $row = mysqli_fetch_row($result);
        $rows= mysqli_num_rows($result);
        if($rows > 0) {
            $values = $row[0].",".$row[1].",".$row[2].",".$row[3].",".$row[4].",".$row[5].",".$row[6].",".$row[7];
        }
        return $values;
    }

    public function obtnerDataAsociado($aso){
        require ('../Conexion/conexion.php');
        $sql = "SELECT cantidad FROM gf_detalle_movimiento WHERE detalleasociado = $aso";
        $res = $mysqli->query($sql);
        $row = $res->fetch_all(MYSQLI_NUM);
        return $row;
    }

    public function obtenerCiudadCompania($compania){
        require ('../Conexion/conexion.php');
        $xxx = "NULL";
        $str = "SELECT ciudadidentificacion FROM gf_tercero WHERE id_unico = $compania";
        $res = $mysqli->query($str);
        if($res->num_rows > 0){
            $row = $res->fetch_row();
            $xxx = $row[0];
        }
        return $xxx;
    }
    public static function agregarMov($id){
        $xxx=agregarMovimiento($id);
        return $xxx;
    }
    public static function agregarMov1($id){
        $xxx=agregarMovimiento1($id);
        return $xxx;
    }
    public static function agregarMov2($id_mov){
        require ('../Conexion/conexion.php');
        session_start();
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Agregar';
        $obs     =  'Agregar C/U';
        $table   = 'gf_movimiento';
        $valor   =  'NA';
        
        if(!empty($id_mov)){ 
            $ret = "SELECT * FROM gf_movimiento WHERE id_unico = $id_mov";
            $cr  = $mysqli->query($ret);
            if(mysqli_num_rows($cr)>0){
            while ($row = mysqli_fetch_row($cr)) {
                $id_campo= $row[0];
                #*******************************#
                $campo = 'id_unico';
                $datoA = $row[0];
                $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                $insert = $mysqli->query($insert);
                if(!empty($row[1])){ 
                    $campo = 'numero';
                    $datoA = $row[1];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[2])){ 
                    $campo = 'fecha';
                    $datoA = $row[2];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[3])){ 
                    $campo = 'descripcion';
                    $datoA = $row[3];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[4])){ 
                    $campo = 'porcivaglobal';
                    $datoA = $row[4];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[5])){ 
                    $campo = 'plazoentrega';
                    $datoA = $row[5];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[6])){ 
                    $campo = 'observaciones';
                    $datoA = $row[6];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[7])){ 
                    $campo = 'tipomovimiento';
                    $datoA = $row[7];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }

                if(!empty($row[8])){ 
                    $campo = 'parametrizacionanno';
                    $datoA = $row[8];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[9])){ 
                    $campo = 'compania';
                    $datoA = $row[9];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[10])){ 
                    $campo = 'tercero';
                    $datoA = $row[10];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[11])){ 
                    $campo = 'tercero2';
                    $datoA = $row[11];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[12])){ 
                    $campo = 'dependencia';
                    $datoA = $row[12];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[13])){ 
                    $campo = 'centrocosto';
                    $datoA = $row[13];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[14])){ 
                    $campo = 'rubropptal';
                    $datoA = $row[14];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[15])){ 
                    $campo = 'proyecto';
                    $datoA = $row[15];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[16])){ 
                    $campo = 'formapa';
                    $datoA = $row[16];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[17])){ 
                    $campo = 'lugarentrega';
                    $datoA = $row[17];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[18])){ 
                    $campo = 'unidadentrega';
                    $datoA = $row[18];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[19])){ 
                    $campo = 'estado';
                    $datoA = $row[19];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[20])){ 
                    $campo = 'tipo_doc_sop';
                    $datoA = $row[20];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[21])){ 
                    $campo = 'numero_doc_sop';
                    $datoA = $row[21];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[22])){ 
                    $campo = 'afectado_contabilidad';
                    $datoA = $row[22];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[23])){ 
                    $campo = 'descuento';
                    $datoA = $row[23];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[24])){ 
                    $campo = 'fecha_hora';
                    $datoA = $row[24];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[25])){ 
                    $campo = 'factura';
                    $datoA = $row[25];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[26])){ 
                    $campo = 'fuente';
                    $datoA = $row[26];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[27])){ 
                    $campo = 'objeto';
                    $datoA = $row[27];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[28])){ 
                    $campo = 'forma_pago';
                    $datoA = $row[28];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[29])){ 
                    $campo = 'valor_contrato';
                    $datoA = $row[29];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[30])){ 
                    $campo = 'clausulas';
                    $datoA = $row[30];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[31])){ 
                    $campo = 'fecha_terminacion';
                    $datoA = $row[31];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[32])){ 
                    $campo = 'numero_actas';
                    $datoA = $row[32];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[33])){ 
                    $campo = 'forma_contratacion';
                    $datoA = $row[33];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[34])){ 
                    $campo = 'id_proceso';
                    $datoA = $row[34];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                
                
            }
        }
        }
    return (true); 
    }

    public static function agregarDetMov($id){
        $xxx=agregarDetalleMovimiento($id);
        return $xxx;
    }

    public static function modificarMovEn($id_unico, $fechaM, $observaciones, 
    $descripcion, $iva, $responsable, $tercero, $fuente, $proyecto){
        require ('../Conexion/conexion.php');
        $sqlComp="SELECT fecha,observaciones,descripcion,porcivaglobal,tercero,tercero2,fuente, 
         proyecto FROM gf_movimiento WHERE id_unico = $id_unico";
        $crComp = $GLOBALS['mysqli']->query($sqlComp);
        $rowComp = mysqli_fetch_row($crComp);
    
            $anno    = $_SESSION['anno'];
            $fecha   =  date('Y-m-d');
            $equipo  =  gethostname();
            $usuario =  $_SESSION['id_usuario'];
            $ip      =  $_SERVER['REMOTE_ADDR'];
            $accion  =  'Actualizar';
            $obs     =  'Actualizado C/U';
            $table   = 'gf_movimiento';
            
            if(!empty($id_unico)){ 
                $ret = "SELECT fecha,observaciones,descripcion,porcivaglobal,tercero,tercero2,fuente, 
                proyecto FROM gf_movimiento WHERE id_unico = $id_unico";
                $cr = $GLOBALS['mysqli']->query($ret);
                if(mysqli_num_rows($cr)>0){
                while ($row = mysqli_fetch_row($cr)) {
                    $id_campo= $id_unico;
    
                    if( $rowComp[0]!=$fechaM){ 
                        $datoA   = $fechaM;
                        $campo = 'fecha';
                        $valor = $row[0];
                        $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                            id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                            direccionip, sistema, usuario, observacion ) 
                            VALUES ('$table', '$campo', 
                            '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                            '$ip', '$equipo', '$usuario', '$obs')";
                        $insert = $GLOBALS['mysqli']->query($insert);
                    }
                    if($rowComp[1]!=$observaciones){ 
                        $datoA   = $observaciones;
                        $campo = 'observaciones';
                        $valor = $row[1];
                        $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                            id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                            direccionip, sistema, usuario, observacion ) 
                            VALUES ('$table', '$campo', 
                            '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                            '$ip', '$equipo', '$usuario', '$obs')";
                        $insert = $GLOBALS['mysqli']->query($insert);
                    }
                    if($rowComp[2]!=$descripcion){
                        $datoA   = $descripcion;
                        $campo = 'descripcion';
                        $valor = $row[2];
                        $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                            id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                            direccionip, sistema, usuario, observacion ) 
                            VALUES ('$table', '$campo', 
                            '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                            '$ip', '$equipo', '$usuario', '$obs')";
                        $insert = $GLOBALS['mysqli']->query($insert);
                    }
                    if($rowComp[3]!=$iva){
                        $datoA   = $iva;
                        $campo = 'porcivaglobal';
                        $valor = $row[3];
                        $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                            id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                            direccionip, sistema, usuario, observacion ) 
                            VALUES ('$table', '$campo', 
                            '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                            '$ip', '$equipo', '$usuario', '$obs')";
                        $insert = $GLOBALS['mysqli']->query($insert);
                    }
                    if($rowComp[4]!=$responsable){
                        $datoA   = $responsable;
                        $campo = 'tercero';
                        $valor = $row[4];
                        $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                            id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                            direccionip, sistema, usuario, observacion ) 
                            VALUES ('$table', '$campo', 
                            '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                            '$ip', '$equipo', '$usuario', '$obs')";
                        $insert = $GLOBALS['mysqli']->query($insert);
                    }
                    if($rowComp[5]!=$tercero){
                        $datoA   = $tercero;
                        $campo = 'tercero2';
                        $valor = $row[5];
                        $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                            id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                            direccionip, sistema, usuario, observacion ) 
                            VALUES ('$table', '$campo', 
                            '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                            '$ip', '$equipo', '$usuario', '$obs')";
                        $insert = $GLOBALS['mysqli']->query($insert);
                    }
                    if($rowComp[6]!=$fuente){
                        $datoA   = $fuente;
                        $campo = 'fuente';
                        $valor = $row[6];
                        $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                            id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                            direccionip, sistema, usuario, observacion ) 
                            VALUES ('$table', '$campo', 
                            '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                            '$ip', '$equipo', '$usuario', '$obs')";
                        $insert = $GLOBALS['mysqli']->query($insert);
                    }
    
                    if($rowComp[7]!=$proyecto){
                        $datoA   = $proyecto;
                        $campo = 'proyecto';
                        $valor = $row[7];
                        $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                            id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                            direccionip, sistema, usuario, observacion ) 
                            VALUES ('$table', '$campo', 
                            '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                            '$ip', '$equipo', '$usuario', '$obs')";
                        $insert = $GLOBALS['mysqli']->query($insert);
                    }
                    
                }
            }
            }
        return (true);    
    }

    public static function agregarMovProd($producto,$detalleMov){
        $xxx=agregarMovimientoProducto($producto,$detalleMov);
        return $xxx;
    }
    public  function agregarDep($indicador){
        require ('../Conexion/conexion.php');
     
        session_start();
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Agregar';
        $obs     =  'Agregar cascada';
        $table   = 'ga_depreciacion';
        $valor   =  'NA';
        $id_campo=  'NA';
        if(!empty($indicador)){ 
            $ret = "SELECT * FROM ga_depreciacion";
            $cr  = $mysqli->query($ret);
            while ($row = mysqli_fetch_row($cr)) {
                $id_campo= $row[0];
                #*******************************#
                $campo = 'id_unico';
                $datoA = $row[0];
                $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                $insert = $mysqli->query($insert);
                if(!empty($row[1])){ 
                    $campo = 'producto';
                    $datoA = $row[1];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[2])){ 
                    $campo = 'fecha_dep';
                    $datoA = $row[2];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }  
                if(!empty($row[3])){ 
                    $campo = 'dias_dep';
                    $datoA = $row[3];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                } 
                if(!empty($row[4])){ 
                    $campo = 'valor';
                    $datoA = $row[4];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }        
                
            }
        }
    return (true);    
    }


    public static function agregarMovimiento($id_mov){  
        require_once ('../Conexion/conexion.php');
        session_start();
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Agregar';
        $obs     =  'Agregar C/U';
        $table   = 'gf_movimiento';
        $valor   =  'NA';
        
        if(!empty($id_mov)){ 
            $ret = "SELECT * FROM gf_movimiento WHERE id_unico = $id_mov";
            $cr  = $GLOBALS['mysqli']->query($ret);
            if(mysqli_num_rows($cr)>0){
            while ($row = mysqli_fetch_row($cr)) {
                $id_campo= $row[0];
                #*******************************#
                $campo = 'id_unico';
                $datoA = $row[0];
                $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                $insert = $GLOBALS['mysqli']->query($insert);
                if(!empty($row[1])){ 
                    $campo = 'numero';
                    $datoA = $row[1];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[2])){ 
                    $campo = 'fecha';
                    $datoA = $row[2];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[3])){ 
                    $campo = 'descripcion';
                    $datoA = $row[3];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[4])){ 
                    $campo = 'porcivaglobal';
                    $datoA = $row[4];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[5])){ 
                    $campo = 'plazoentrega';
                    $datoA = $row[5];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[6])){ 
                    $campo = 'observaciones';
                    $datoA = $row[6];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[7])){ 
                    $campo = 'tipomovimiento';
                    $datoA = $row[7];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }

                if(!empty($row[8])){ 
                    $campo = 'parametrizacionanno';
                    $datoA = $row[8];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[9])){ 
                    $campo = 'compania';
                    $datoA = $row[9];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[10])){ 
                    $campo = 'tercero';
                    $datoA = $row[10];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[11])){ 
                    $campo = 'tercero2';
                    $datoA = $row[11];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[12])){ 
                    $campo = 'dependencia';
                    $datoA = $row[12];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[13])){ 
                    $campo = 'centrocosto';
                    $datoA = $row[13];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[14])){ 
                    $campo = 'rubropptal';
                    $datoA = $row[14];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[15])){ 
                    $campo = 'proyecto';
                    $datoA = $row[15];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[16])){ 
                    $campo = 'formapa';
                    $datoA = $row[16];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[17])){ 
                    $campo = 'lugarentrega';
                    $datoA = $row[17];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[18])){ 
                    $campo = 'unidadentrega';
                    $datoA = $row[18];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[19])){ 
                    $campo = 'estado';
                    $datoA = $row[19];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[20])){ 
                    $campo = 'tipo_doc_sop';
                    $datoA = $row[20];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[21])){ 
                    $campo = 'numero_doc_sop';
                    $datoA = $row[21];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[22])){ 
                    $campo = 'afectado_contabilidad';
                    $datoA = $row[22];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[23])){ 
                    $campo = 'descuento';
                    $datoA = $row[23];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[24])){ 
                    $campo = 'fecha_hora';
                    $datoA = $row[24];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[25])){ 
                    $campo = 'factura';
                    $datoA = $row[25];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[26])){ 
                    $campo = 'fuente';
                    $datoA = $row[26];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[27])){ 
                    $campo = 'objeto';
                    $datoA = $row[27];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[28])){ 
                    $campo = 'forma_pago';
                    $datoA = $row[28];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[29])){ 
                    $campo = 'valor_contrato';
                    $datoA = $row[29];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[30])){ 
                    $campo = 'clausulas';
                    $datoA = $row[30];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[31])){ 
                    $campo = 'fecha_terminacion';
                    $datoA = $row[31];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[32])){ 
                    $campo = 'numero_actas';
                    $datoA = $row[32];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[33])){ 
                    $campo = 'forma_contratacion';
                    $datoA = $row[33];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                if(!empty($row[34])){ 
                    $campo = 'id_proceso';
                    $datoA = $row[34];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $GLOBALS['mysqli']->query($insert);
                }
                
                
            }
        }
        }
    return (true);    
}

public static function agregarDetalleMovimiento($id_mov){  
        require ('../Conexion/conexion.php');
        session_start();
        $anno    = $_SESSION['anno'];
        $fecha   =  date('Y-m-d');
        $equipo  =  gethostname();
        $usuario =  $_SESSION['id_usuario'];
        $ip      =  $_SERVER['REMOTE_ADDR'];
        $accion  =  'Agregar';
        $obs     =  'Agregar C/U';
        $table   = 'gf_detalle_movimiento';
        $valor   =  'NA';
        
        if(!empty($id_mov)){ 
            $ret = "SELECT * FROM gf_detalle_movimiento WHERE id_unico = $id_mov";
            $cr  = $mysqli->query($ret);
            if(mysqli_num_rows($cr)>0){
            while ($row = mysqli_fetch_row($cr)) {
                $id_campo= $row[0];
                #*******************************#
                $campo = 'id_unico';
                $datoA = $row[0];
                $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                $insert = $mysqli->query($insert);
                if(!empty($row[1])){ 
                    $campo = 'cantidad';
                    $datoA = $row[1];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[2])){ 
                    $campo = 'valor';
                    $datoA = $row[2];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[3])){ 
                    $campo = 'iva';
                    $datoA = $row[3];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[4])){ 
                    $campo = 'porcentajeneto';
                    $datoA = $row[4];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[5])){ 
                    $campo = 'porcentajeiva';
                    $datoA = $row[5];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[6])){ 
                    $campo = 'hora';
                    $datoA = $row[6];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[7])){ 
                    $campo = 'movimiento';
                    $datoA = $row[7];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }

                if(!empty($row[8])){ 
                    $campo = 'detalleasociado';
                    $datoA = $row[8];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[9])){ 
                    $campo = 'planmovimiento';
                    $datoA = $row[9];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[10])){ 
                    $campo = 'ajuste';
                    $datoA = $row[10];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[11])){ 
                    $campo = 'cantidad_origen';
                    $datoA = $row[11];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[12])){ 
                    $campo = 'unidad_origen ';
                    $datoA = $row[12];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[13])){ 
                    $campo = 'valor_origen';
                    $datoA = $row[13];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[14])){ 
                    $campo = 'xvalor_t';
                    $datoA = $row[14];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[15])){ 
                    $campo = 'descuento';
                    $datoA = $row[15];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[16])){ 
                    $campo = 'id_factura';
                    $datoA = $row[16];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[17])){ 
                    $campo = 'n_registro';
                    $datoA = $row[17];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[18])){ 
                    $campo = 'observaciones';
                    $datoA = $row[18];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[19])){ 
                    $campo = 'impoconsumo';
                    $datoA = $row[19];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                }
                if(!empty($row[20])){ 
                    $campo = 'detalle_comprobante ';
                    $datoA = $row[20];
                    $insert = "INSERT INTO gs_auditoria (nombre_tabla, nombre_campo, 
                        id_campo, equipo, fecha, accion, dato_anterior,dato_actual, 
                        direccionip, sistema, usuario, observacion ) 
                        VALUES ('$table', '$campo', 
                        '$id_campo', '$equipo', '$fecha', '$accion', '$valor', '$datoA', 
                        '$ip', '$equipo', '$usuario', '$obs')";
                    $insert = $mysqli->query($insert);
                } 
            }
        }
        }
    return (true);    
}


}
