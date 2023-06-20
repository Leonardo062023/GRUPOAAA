<?php
/**
 * controllerGFElementoFicha.php
 *
 * Archivo de control para registro, modificación y eliminado de la tabla elemento ficha
 *
 * @author Alexander Numpaque
 * @package Elemento Ficha
 * @param String $action Variable para indicar que proceso se va a realizar
 * @version $Id: controllerGFElementoFicha.php 001 2017-05-26 Alexander Numpaque$
 */


if(!empty($_REQUEST['action'])){
    $action =  $_REQUEST['action'];
}elseif ($_REQUEST['action']) {
    $action =  $_GET['action'];
}

require ('../json/registrar_GF_ELEMENTO_FICHAJson.php');

if($action == 'insert') {
    $nombre  = $_REQUEST['nombre'];
    $tipoDato  = $_REQUEST['tipoDato'];
    $result = gf_elemento_ficha::save_data($nombre, $tipoDato);
    if($result){
       echo 1;
    }else{
       echo 2;
    }
}elseif($action == 'modify') {
    $nombre  = $_REQUEST['nombre'];
    $tipoDato  = $_REQUEST['tipoDato'];
    $id_unico  = $_REQUEST['id'];
    $result = gf_elemento_ficha::modify_data($nombre, $tipoDato, $id_unico);
    if($result){
        echo 1;
    }else{
        echo 2;
    }
}elseif($action == 'delete') {
    $id_unico = $_REQUEST['id_unico'];
    $result = gf_elemento_ficha::delete_data($id_unico);
    if($result){
        echo 1;
    }else{
        echo 2;
    }
}
?>