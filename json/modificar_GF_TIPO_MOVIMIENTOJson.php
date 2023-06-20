<?php
/*require_once('../Conexion/conexion.php');
session_start();
$id  = $mysqli->real_escape_string('' . $_POST['id'] . '');
$nombre  = '"' . $mysqli->real_escape_string('' . $_POST['nombre'] . '') . '"';
$cadena1 = strtolower($nombre);
//cargo en el array los textos a sustituir
$exp_regular = array();
$exp_regular[0] = '/á/';
$exp_regular[1] = '/é/';
$exp_regular[2] = '/í/';
$exp_regular[3] = '/ó/';
$exp_regular[4] = '/ú/';
$exp_regular[4] = '/ñ/';
//cargo en el array los textos que pondremos en la sustitucion
$cadena_nueva = array();
$cadena_nueva[0] = 'a';
$cadena_nueva[1] = 'e';
$cadena_nueva[2] = 'i';
$cadena_nueva[3] = 'o';
$cadena_nueva[4] = 'u';
$cadena_nueva[4] = 'n';
$nombreC = preg_replace($exp_regular, $cadena_nueva, $cadena1);
$costea  = '"' . $mysqli->real_escape_string('' . $_POST['costea'] . '') . '"';
$clase   = '"' . $mysqli->real_escape_string('' . $_POST['clase'] . '') . '"';
$elemento = '"' . $mysqli->real_escape_string('' . $_POST['elemento'] . '') . '"';
$persona  = '"' . $mysqli->real_escape_string('' . $_POST['persona'] . '') . '"';
$formato  = '"' . $mysqli->real_escape_string('' . $_POST['formato'] . '') . '"';
$sigla  = '"' . $mysqli->real_escape_string('' . $_POST['txtSigla'] . '') . '"';
$queryUA = "SELECT nombre, costea, clase, tipoelemento, tipopersona, tipo_documento, sigla FROM gf_tipo_movimiento "
    . "WHERE id_unico = '$id'";
$carA = $mysqli->query($queryUA);
$numA =  mysqli_fetch_row($carA);
$queryU = "SELECT nombre, costea, clase, tipo_documento, tipopersona, tipo_documento, sigla FROM gf_tipo_movimiento 
      WHERE LOWER(nombre) = $nombreC 
      AND costea = $costea 
      AND clase=$clase 
      AND tipoelemento = $elemento 
      AND tipopersona = $persona 
      AND tipo_documento = $formato
      AND sigla = $sigla";
$car = $mysqli->query($queryU);
$num = mysqli_num_rows($car);
if (
    '"' . strtolower($numA[0]) . '"' == $nombreC && '"' . $numA[1] . '"' == $costea
    && '"' . $numA[2] . '"' == $clase && '"' . $numA[3] . '"' == $elemento && '"' . $numA[4] . '"' == $persona
    && '"' . $numA[5] . '"' == $formato && '"' . $numA[6] . '"' == $sigla
) {
    $resultado = '1';
} else {
    if ($num == 0) {
        $update = "UPDATE gf_tipo_movimiento "
            . "SET nombre =$nombre, "
            . "costea = $costea, "
            . "clase =$clase, "
            . "tipoelemento =$elemento, "
            . "tipopersona =$persona, "
            . "tipo_documento = $formato, "
            . "sigla = $sigla"
            . "WHERE id_unico = $id";
        $resul = $mysqli->query($update);
        $resultado = '1';
    } else {
        if ($num > 0) {
            $resultado = '3';
        } else {
            $resultado = false;
        }
    }
}*/
?>
<?php

require_once('../Conexion/conexionPDO.php');
session_start();
$con = new ConexionPDO();
$action = $_REQUEST['action'];

switch ($action) {
    case 1:
        $id = $_REQUEST['id'];

        $sql_cons = "DELETE FROM gf_tipo_movimiento WHERE id_unico = :id_unico";
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
        $compania = $_SESSION['compania'];
        $nombre  = $_REQUEST['nombre'];
        $cadena1 = strtolower($nombre);

        //cargo en el array los textos a sustituir
        $exp_regular = array();
        $exp_regular[0] = '/á/';
        $exp_regular[1] = '/é/';
        $exp_regular[2] = '/í/';
        $exp_regular[3] = '/ó/';
        $exp_regular[4] = '/ú/';
        $exp_regular[4] = '/ñ/';

        //cargo en el array los textos que pondremos en la sustitucion
        $cadena_nueva = array();
        $cadena_nueva[0] = 'a';
        $cadena_nueva[1] = 'e';
        $cadena_nueva[2] = 'i';
        $cadena_nueva[3] = 'o';
        $cadena_nueva[4] = 'u';
        $cadena_nueva[4] = 'n';

        $nombreC = preg_replace($exp_regular, $cadena_nueva, $cadena1);
        $costea  = $_REQUEST['costea'];
        $clase  = $_REQUEST['clase'];
        $elemento  = $_REQUEST['elemento'];
        $persona  = $_REQUEST['persona'];
        $formato  = $_REQUEST['formato'];
        $sigla  = $_REQUEST['txtSigla'];
        $num = $con->Listar("SELECT * FROM gf_tipo_movimiento 
            WHERE LOWER(nombre) = $nombreC 
            AND costea = $costea 
            AND clase=$clase 
            AND tipoelemento = $elemento 
            AND tipopersona = $persona 
            AND tipo_documento = $formato 
            AND sigla = $sigla");
        $num = count($num);
        if ($num == 0) {
            $sql_cons  = "INSERT INTO gf_tipo_movimiento (nombre, costea, clase, tipoelemento, tipopersona, tipo_documento, compania, sigla) 
            VALUES (:nombre, :costea, :clase, :elemento, :persona, :documento, :compania, :sigla)";
            $sql_dato = array(
                array(":nombre", $nombre),
                array(":costea", $costea),
                array(":clase", $clase),
                array(":elemento", $elemento),
                array(":persona", $persona),
                array(":documento", $formato),
                array(":compania", $compania),
                array(":sigla", $sigla),
            );

            $obj_resp = $con->InAcEl($sql_cons, $sql_dato);
            if (empty($obj_resp)) {
                echo 1;
            } else {
                echo 2;
            }
        } else {
            echo 2;
        }
        break;
    case 3:
        $id  = $_REQUEST['id'];
        $nombre  =  $_REQUEST['nombre'];
        $cadena1 = strtolower($nombre);
        //cargo en el array los textos a sustituir
        $exp_regular = array();
        $exp_regular[0] = '/á/';
        $exp_regular[1] = '/é/';
        $exp_regular[2] = '/í/';
        $exp_regular[3] = '/ó/';
        $exp_regular[4] = '/ú/';
        $exp_regular[4] = '/ñ/';
        //cargo en el array los textos que pondremos en la sustitucion
        $cadena_nueva = array();
        $cadena_nueva[0] = 'a';
        $cadena_nueva[1] = 'e';
        $cadena_nueva[2] = 'i';
        $cadena_nueva[3] = 'o';
        $cadena_nueva[4] = 'u';
        $cadena_nueva[4] = 'n';
        $nombreC = preg_replace($exp_regular, $cadena_nueva, $cadena1);
        $costea  = $_REQUEST['costea'];
        $clase   = $_REQUEST['clase'];
        $elemento = $_REQUEST['elemento'];
        $persona  = $_REQUEST['persona'];
        $formato  = $_REQUEST['formato'];
        $sigla  = $_REQUEST['txtSigla'];

        $numA = $con->Listar("SELECT nombre, costea, clase, tipoelemento, tipopersona, tipo_documento, sigla FROM gf_tipo_movimiento WHERE id_unico = $id");
        $numA =  count($numA);

        $num = $con->Listar("SELECT nombre, costea, clase, tipo_documento, tipopersona, tipo_documento, sigla FROM gf_tipo_movimiento 
        WHERE LOWER(nombre) = $nombreC 
        AND costea = $costea 
        AND clase=$clase 
        AND tipoelemento = $elemento 
        AND tipopersona = $persona 
        AND tipo_documento = $formato
        AND sigla = $sigla");
        $num = count($num);

        if ($num == 0) {
            $sql_cons = "UPDATE gf_tipo_movimiento 
                SET nombre=:nombre, costea=:costea, clase=:clase, tipoelemento=:elemento, tipopersona=:persona, tipo_documento=:documento, sigla=:sigla
                WHERE id_unico = :id_unico";

            $sql_dato = array(
                array(":nombre", $nombre),
                array(":costea", $costea),
                array(":clase", $clase),
                array(":elemento", $elemento),
                array(":persona", $persona),
                array(":documento", $formato),
                array(":sigla", $sigla),
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
    default:
        # code...
        break;
}
?>
