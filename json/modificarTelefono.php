<?php
require_once('../Conexion/conexionPDO.php');
session_start();
$con = new ConexionPDO();

$action = $_REQUEST['action'];

switch ($action) {
	case 1:
		//eliminar en la base de datos
		$id = $_REQUEST['id'];
		//Si no existe el registro como predecesor se elimina.
		if (count($queryPred) == 0) {
			$sql_cons = "DELETE FROM gf_telefono WHERE id_unico = :id_unico";
			$sql_dato = array(
				array(":id_unico", $id)
			);
			$resp = $con->InAcEl($sql_cons, $sql_dato);
			if (empty($resp)) {
				echo 1;
			} else {
				echo 2;
			}
		}
		break;
	case 2:
		//Captura de datos e instrucción SQL para su inserción en la tabla gf_perfil_tercero.
		$telefono  = $_REQUEST['tel'];
		$valor  = $_REQUEST['valor'];
		$tercero = $_REQUEST['tercero'];


		$num = $con->Listar("SELECT id_unico FROM gf_telefono WHERE valor = $valor AND tercero = $tercero");
		$num = count($num);

		if ($num == 0) //Si no existe el año, se realizará la inserción de datos en la tabla.
		{
			/*$insertSQL = "INSERT INTO gf_telefono (tipo_telefono, valor, tercero) VALUES($telefono, $valor, $tercero)";
			$resultado = $mysqli->query($insertSQL);*/
			$sql_cons  = "INSERT INTO gf_telefono (tipo_telefono, valor, tercero) 
                VALUES (:tipo, :valor ,:tercero)";
			$sql_dato = array(
				array(":tipo", $telefono),
				array(":valor", $valor),
				array(":tercero", $tercero),
			);

			$obj_resp = $con->InAcEl($sql_cons, $sql_dato);
			if (empty($obj_resp)) {
				echo 1;
			} else {
				echo 2;
			}
		} else //Si no se encuentra, retronará false en el resultado.
		{
			echo 2;
			$resultado = false;
		}
		break;
	case 3:
		//Variables
		///$response = 0;
		$id = $_REQUEST['tipoteledi'];
		$tipoActi = $_REQUEST['tipoActmodal'];
		$valor = $_REQUEST['valorA'];
		$valorx = $_REQUEST['valorAx'];

		if ($valor != $valorx) {
			$num = $con->Listar("SELECT id_unico FROM gf_telefono WHERE valor = $valor");
			$num = count($num);
			if ($num == 0) {
				$sql_cons = "UPDATE gf_telefono 
      			SET tipo_telefono=:tipo, valor=:valor
      			WHERE id_unico = :id_unico";

				$sql_dato = array(
					array(":tipo", $tipoActi),
					array(":valor", $valor),
					array(":id_unico", $id),
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
		} else {
			$sql_cons = "UPDATE gf_telefono 
      			SET tipo_telefono=:tipo, valor=:valor
      			WHERE id_unico = :id_unico";
			$sql_dato = array(
				array(":tipo", $tipoActi),
				array(":valor", $valor),
				array(":id_unico", $id),
			);
			$obj_resp = $con->InAcEl($sql_cons, $sql_dato);
			if (empty($obj_resp)) {
				echo 1;
			} else {
				echo 2;
			}
		}
		break;
	default:
		# code...
		break;
}
?>
