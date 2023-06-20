<?php
session_start();
require_once('../Conexion/ConexionPDO.php');
$con = new ConexionPDO();
$action = $_REQUEST['action'];
$compania   = $_SESSION['compania'];
switch ($action) {
  case 1:
    // $sql = "DELETE FROM gp_tipo_factura WHERE id_unico = $id";
    $id = $_REQUEST['id'];
    $sql_cons = "DELETE FROM gp_tipo_factura WHERE id_unico = :id_unico";
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
  case 2:
    $nombre     = $_REQUEST['nombre'];
    if (empty($_REQUEST['prefijo'])) {
      $prefijo    = NULL;
    } else {
      $prefijo    = $_REQUEST['prefijo'];
    }
    if (empty($_REQUEST['sltClase'])) {
      $clase    = NULL;
    } else {
      $clase    = $_REQUEST['sltClase'];
    }
    if (!empty($_REQUEST['tipoC'])) {
      $tipoC = $_REQUEST['tipoC'];
    } else {
      $tipoC = NULL;
    }
    if (!empty($_REQUEST['tipoR'])) {
      $tipoR = $_REQUEST['tipoR'];
    } else {
      $tipoR = NULL;
    }

    if (!empty($_REQUEST['sltMov'])) {
      $tipoM = $_REQUEST['sltMov'];
    } else {
      $tipoM = NULL;
    }
    $cons       = $_REQUEST['consecutivo'];
    $serv       = $_REQUEST['serv'];

    if (!empty($_REQUEST['optXDescuento'])) {
      $desc = $_REQUEST['optXDescuento'];
    } else {
      $desc = NULL;
    }
    $automatico = $_REQUEST['optAutomatico'];
    if (!empty($_REQUEST['sltTipoCambio'])) {
      $tipo_c = $_REQUEST['sltTipoCambio'];
    } else {
      $tipo_c = NULL;
    }

    $optfe    = $_REQUEST['optfe'];

    $consulta = $con->Listar("SELECT facturacion_e FROM gp_tipo_factura WHERE facturacion_e = $optfe");
    $consulta = count($consulta);
    if ($consulta > 0) {
      $sql_cons = "UPDATE gp_tipo_factura 
            SET facturacion_e=:facturacion_e 
            WHERE facturacion_e = :facturacion";
      $sql_dato = array(
        array(":facturacion_e", NULL),
        array(":facturacion", $optfe),
      );
      $obj_resp = $con->InAcEl($sql_cons, $sql_dato);
    }
    $sql_cons  = "INSERT INTO gp_tipo_factura (nombre,prefijo, clase_factura, tipo_comprobante, tipo_recaudo, 
    tipo_movimiento, sigue_consecutivo, servicio, xDescuento, automatico, tipo_cambio, compania, facturacion_e) 
    VALUES (:nombre,:prefijo,:clase,:tipoC,:tipoR,:tipoM,:cons,:serv,:descuento,:automatico,:tipo_c,:compania,:facturacion)";
    $sql_dato = array(
      array(":nombre", $nombre),
      array(":prefijo", $prefijo),
      array(":clase", $clase),
      array(":tipoC", $tipoC),
      array(":tipoR", $tipoR),
      array(":tipoM", $tipoM),
      array(":cons", $cons),
      array(":serv", $serv),
      array(":descuento", $desc),
      array(":automatico", $automatico),
      array(":tipo_c", $tipo_c),
      array(":compania", $compania),
      array(":facturacion", $optfe),
    );
    $obj_resp = $con->InAcEl($sql_cons, $sql_dato);
    if (empty($obj_resp)) {
      echo 1;
    } else {
      echo 2;
    }
    break;
  case 3:
    $nombre     = $_REQUEST['nombre'];
    if (empty($_REQUEST['prefijo'])) {
      $prefijo    = NULL;
    } else {
      $prefijo    = $_REQUEST['prefijo'];
    }
    if (empty($_REQUEST['sltClase'])) {
      $clase    = NULL;
    } else {
      $clase    = $_REQUEST['sltClase'];
    }
    if (!empty($_REQUEST['tipoC'])) {
      $tipoC = $_REQUEST['tipoC'];
    } else {
      $tipoC = NULL;
    }
    if (!empty($_REQUEST['tipoR'])) {
      $tipoR = $_REQUEST['tipoR'];
    } else {
      $tipoR = NULL;
    }

    if (!empty($_REQUEST['sltMov'])) {
      $tipoM = $_REQUEST['sltMov'];
    } else {
      $tipoM = NULL;
    }
    $cons       = $_REQUEST['consecutivo'];
    $serv       = $_REQUEST['serv'];

    if (!empty($_REQUEST['optXDescuento'])) {
      $desc = $_REQUEST['optXDescuento'];
    } else {
      $desc = NULL;
    }
    $automatico = $_REQUEST['optAutomatico'];
    if (!empty($_REQUEST['sltTipoCambio'])) {
      $tipo_c = $_REQUEST['sltTipoCambio'];
    } else {
      $tipo_c = NULL;
    }
    $optfe    = $_REQUEST['optfe'];
    $id = $_REQUEST['id'];
    // $insertSQL = "UPDATE gp_tipo_factura SET 
    //         nombre='$nombre',prefijo='$prefijo',
    // clase_factura=$clase, tipo_comprobante=$tipoC, 
    // tipo_recaudo=$tipoR, tipo_movimiento=$tipoM, 
    // sigue_consecutivo=$cons, servicio=$serv, 
    // xDescuento=$desc, automatico=$automatico, 
    // tipo_cambio=$tipo_c, facturacion_e = $optfe  WHERE id_unico = $id";
    // $resultado = $mysqli->query($insertSQL);
    $sql_cons = "UPDATE gp_tipo_factura 
            SET nombre=:nombre, prefijo=:prefijo,clase_factura=:clase_factura,tipo_comprobante=:tipo_comprobante,
            tipo_recaudo=:tipo_recaudo, tipo_movimiento=:tipo_movimiento, sigue_consecutivo=:sigue_consecutivo,
            servicio=:servicio,xDescuento=:xDescuento,automatico=:automatico,tipo_cambio=:tipo_cambio, facturacion_e=:facturacion_e 
            WHERE id_unico = :id_unico";
    $sql_dato = array(
      array(":nombre", $nombre),
      array(":prefijo", $prefijo),
      array(":clase_factura", $clase),
      array(":tipo_comprobante", $tipoC),
      array(":tipo_recaudo", $tipoR),
      array(":tipo_movimiento", $tipoM),
      array(":sigue_consecutivo", $cons),
      array(":servicio", $serv),
      array(":xDescuento", $desc),
      array(":automatico", $automatico),
      array(":tipo_cambio", $tipo_c),
      array(":facturacion_e", $optfe),
      array(":id_unico", $id),
    );
    $obj_resp = $con->InAcEl($sql_cons, $sql_dato);
    if (empty($obj_resp)) {
      echo 1;
    } else {
      echo 2;
    }
    break;
  default:
    break;
}
