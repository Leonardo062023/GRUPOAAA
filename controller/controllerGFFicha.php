<?php
/**
 * controllerGFFicha.php
 *
 * Archivo de direcciónamiento para invocar la clase y realizar una acción dependiendo de la variable actión recibida
 *
 * @author Alexander Numpaque
 * @package Tipo Archivo
 * @param String $action Variable para indicar que proceso se va a realizar
 * @version $Id: controllerGFFicha.php 001 2017-05-26 Alexander Numpaque$
 **/

require ('../json/registrarFichaJson.php');
if(!empty($_REQUEST['action'])){
    $action =  $_REQUEST['action'];
}elseif ($_REQUEST['action']) {
    $action =  $_REQUEST['action'];
}
if($action == 'insert'){
    $nombre = $_REQUEST['txtDescripcion'];
    $result = gf_ficha::save_data($nombre);
    if($result){
        echo 1;
    }else{
        echo 2;
    }
}else if($action == 'modify'){
    $nombre = $_REQUEST['txtDescripcion'];
    $id_unico = $_REQUEST['id'];
    $result = gf_ficha::modify_data($nombre, $id_unico);
    if($result){
        echo 1;
    }else{
        echo 2;
    }
}else if($action === "delete"){
    $id_unico = $_REQUEST['id_unico'];
    $result = gf_ficha::delete_data($id_unico);
    if($result){
        echo 1;
    }else{
        echo 2;
    }
}
