<?php
#######################################################################################
#16/06/2017 |ERICA G. |ARCHIVO CREADO
#######################################################################################
require_once '../Conexion/conexion.php';
require_once '../Conexion/ConexionPDO.php';
$con = new ConexionPDO();
session_start();
$conexion = $_SESSION['conexion'];
switch ($_POST['action']) {
        #############GUARDAR ADICION APROPIACION##########    
    case 1:
        $numero  = $_REQUEST['numero'];
        $fecha  = $_REQUEST['fecha'];
        $fechaVen  = $_REQUEST['fechaVen'];
        if (!empty($_REQUEST['descripcion'])) {
            $descripcion = $_REQUEST['descripcion'];
        } else {
            $descripcion = NULL;
        }
        $estado = $_REQUEST['estado'];
        $tipocomprobante = $_REQUEST['tipocomprobante'];

        switch ($conexion) {
            case 1:
                ######CONVERSION FECHA######
                $fecha = trim($fecha, '"');
                $fecha_div = explode("/", $fecha);
                $dia = $fecha_div[0];
                $mes = $fecha_div[1];
                $anio = $fecha_div[2];
                $fecha = $anio . '-' . $mes . '-' . $dia;

                #######CONVERSION FECHA VENCIMIENTO###########
                $fechaVen = trim($fechaVen, '"');
                $fecha_div = explode("/", $fechaVen);
                $dia = $fecha_div[0];
                $mes = $fecha_div[1];
                $anio = $fecha_div[2];
                $fechaVen = $anio . '-' . $mes . '-' . $dia;
                break;
            case 2:
                break;
            default:
                # code...
                break;
        }

        $tercero = 2;
        $responsable = 2;
        $parametroAnno = $_SESSION['anno'];
        switch ($conexion) {
            case 1:
                $fechaE = date('Y/m/d');
                break;
            case 2:
                $fechaE = date('d/m/Y');
                break;
            default:
                break;
        }
       
        $user = $_SESSION['usuario'];

        $sql_cons  = "INSERT INTO gf_comprobante_pptal(numero, fecha, fechavencimiento, descripcion, parametrizacionanno, tipocomprobante, 
        tercero, estado, responsable, fecha_elaboracion, usuario) 
        VALUES (:numero,:fecha,:fechavencimiento,:descripcion,:parametrizacionanno,:tipocomprobante,:tercero,:estado,:responsable,:fecha_elaboracion
        ,:usuario)";
        $sql_dato = array(
            array(":numero", $numero),
            array(":fecha", $fecha),
            array(":fechavencimiento", $fechaVen),
            array(":descripcion", $descripcion),
            array(":parametrizacionanno", $parametroAnno),
            array(":tipocomprobante", $tipocomprobante),
            array(":tercero", $tercero),
            array(":estado", $estado),
            array(":responsable", $responsable),
            array(":fecha_elaboracion", $fechaE),
            array(":usuario", $user),
        );
        $obj_resp = $con->InAcEl($sql_cons, $sql_dato);
        if (empty($obj_resp)) {
            $rowUC = $con->Listar("SELECT MAX(id_unico) FROM gf_comprobante_pptal
                            WHERE numero = '$numero' and tipocomprobante =$tipocomprobante");

            echo json_decode($rowUC[0][0]);

        } else {
            echo 2;
        }

        break;
        #######MODIFICAR COMPROBANTE ADICION APROPIACION###########                
    case 2:
        $id =  $_REQUEST['comprobante'];
        $fecha  = $_REQUEST['fecha'];
        $fechaVen  = $_REQUEST['fechaVen'];
        ##FECHA
        if ($conexion == 1) {
            $fecha = trim($fecha, '"');
            $fecha_div = explode("/", $fecha);
            $dia = $fecha_div[0];
            $mes = $fecha_div[1];
            $anio = $fecha_div[2];
            $fecha = $anio . '-' . $mes . '-' . $dia;

            $fechaVen = trim($fechaVen, '"');
            $fecha_div = explode("/", $fechaVen);
            $dia = $fecha_div[0];
            $mes = $fecha_div[1];
            $anio = $fecha_div[2];
            $fechaVen = $anio . '-' . $mes . '-' . $dia;
        }

        $descripcion = $_REQUEST['descripcion'];
        if (empty($descripcion)) {
            $descripcion = NULL;
        } else {
            $descripcion = $_REQUEST['descripcion'];
        }
        ##FECHA VENCIMIENTO

        // $upd = "UPDATE gf_comprobante_pptal SET fecha='$fecha', fechavencimiento ='$fechaVen', "
        //     . "descripcion = $descripcion WHERE id_unico = $id";
        // $result = $mysqli->query($upd);
        $sql_cons = "UPDATE gf_comprobante_pptal 
            SET fecha=:fecha, fechavencimiento=:fechavencimiento, descripcion=:descripcion
            WHERE id_unico = :id_unico";
        $sql_dato = array(
            array(":fecha", $fecha),
            array(":fechavencimiento", $fechaVen),
            array(":descripcion", $descripcion),
            array(":id_unico", $id),
        );
        $obj_resp = $con->InAcEl($sql_cons, $sql_dato);

        // //ACTUALIZAR DETALLES
        // $udpd = "UPDATE gf_detalle_comprobante_pptal SET descripcion = $descripcion WHERE comprobantepptal = $id";
        // $udpd = $mysqli->query($udpd);
        // if ($result == true || $result == 1) {
        //     echo 1;
        // } else {
        //     echo 0;
        // }
        $sql_cons1 = "UPDATE gf_detalle_comprobante_pptal 
            SET descripcion=:descripcion
            WHERE comprobantepptal = :id_unico";
        $sql_dato1 = array(
            array(":descripcion", $descripcion),
            array(":id_unico", $id),
        );
        $obj_resp1 = $con->InAcEl($sql_cons1, $sql_dato1);
        if (empty($obj_resp) && empty($obj_resp1)) {
            echo 1;
        } else {
            echo 2;
        }
        break;
        ############REGISTRAR DETALLE ADICION A APROPIACION###########
    case 3:
        $rubro  = $_REQUEST['rubro'];
        $fuente = $_REQUEST['fuente'];
        $valor  = $_REQUEST['valor'];
        switch ($conexion) {
            case 1:
                $valor  = str_replace(',', '', $valor);
                break;
            case 2:
                $valor  = str_replace(',', '', $valor);
                $valor = str_replace('.', ',', $valor);
                break;
            default:
                break;
        }
        ######RUBRO FUENTE#######
        $rubroFuente = $con->Listar("SELECT id_unico 
                FROM gf_rubro_fuente 
                WHERE rubro = $rubro   
                AND fuente = $fuente");

        $result = 1;
        if (count($rubroFuente) > 0) {
            $id_rubro_fuente = $rubroFuente[0][0];
        } else {
            // $resultado = "INSERT INTO gf_rubro_fuente (rubro, fuente) 
            //     VALUES($rubro, $fuente)";
            // $resultado = $mysqli->query($insertSQL);
            $sql_cons  = "INSERT INTO gf_rubro_fuente (rubro,fuente) 
            VALUES (:rubro,:fuente)";
            $sql_dato = array(
                array(":rubro", $nombre),
                array(":fuente", $tipoC),
            );
            $obj_resp = $con->InAcEl($sql_cons, $sql_dato);
            if (empty($obj_resp)) {
                $row = $con->Listar("SELECT MAX(id_unico) FROM gf_rubro_fuente WHERE rubro = $rubro AND fuente = $fuente");
                $id_rubro_fuente = $row[0];
            } else {
                $result = 2;
            }
        }
        $id_comprobante_pptal = $_REQUEST['id'];
        if (!empty($_REQUEST['descripcion'])) {
            $descripcion = $_REQUEST['descripcion'];
        } else {
            $descripcion = NULL;
        }
        $tercero = 2;
        $id_proyecto = 2147483647;
        $sql_cons  = "INSERT INTO gf_detalle_comprobante_pptal(descripcion, valor, comprobantepptal, rubrofuente, tercero, proyecto)
            VALUES (:descripcion,:valor,:id_comprobante_pptal,:id_rubro_fuente,:tercero,:id_proyecto)";
        $sql_dato = array(
            array(":descripcion", $descripcion),
            array(":valor", $valor),
            array(":id_comprobante_pptal", $id_comprobante_pptal),
            array(":id_rubro_fuente", $id_rubro_fuente),
            array(":tercero", $tercero),
            array(":id_proyecto", $id_proyecto),
        );
        $obj_resp = $con->InAcEl($sql_cons, $sql_dato);
        if (empty($obj_resp)) {
            $result = 1;
        } else {
            $result = 2;
        }
        echo json_decode($result);
        break;
        #########ELIMINAR DETALLES ADICION##########
    case 4:
        $id = $_REQUEST['id'];
        // $delet = "DELETE FROM gf_detalle_comprobante_pptal WHERE id_unico = $id";
        // $delete = $mysqli->query($delet);
        // if ($delete == true) {
        //     $result = 1;
        // } else {
        //     $result = 2;
        // }
        // echo json_decode($result);
        $sql_cons = "DELETE FROM gf_detalle_comprobante_pptal WHERE id_unico = :id_unico";
        $sql_dato = array(
            array(":id_unico", $id)
        );
        $resp = $con->InAcEl($sql_cons, $sql_dato);
        if (empty($resp)) {
            echo 1;
        } else {
            echo 2;
        }
        break;
        ##########MODIFICAR DETALLE ADICION APROPIACION#######
    case 5:
        $id     = $_REQUEST['id'];
        $valor  = $_REQUEST['valor'];
        switch ($conexion) {
            case 1:
                $valor  = str_replace(',', '', $valor);
                break;
            case 2:
                $valor  = str_replace(',', '', $valor);
                $valor = str_replace('.', ',', $valor);
                break;
            default:
                break;
        }
        $sql_cons = "UPDATE gf_detalle_comprobante_pptal 
            SET valor=:valor
            WHERE id_unico = :id_unico";
        $sql_dato = array(
            array(":valor", $valor),
            array(":id_unico", $id),
        );
        $obj_resp = $con->InAcEl($sql_cons, $sql_dato);
        if (empty($obj_resp)) {
            echo 1;
        } else {
            echo 2;
        }
        break;
}
