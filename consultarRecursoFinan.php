<?php

// require_once 'Conexion/conexion.php';

// session_start();

// if (!empty($_POST["cod"]) || !isset($_POST["cod"])) {


// 	$cod = $_POST["cod"];

// 	$sql = "SELECT DISTINCT rf.id_unico, rf.nombre, rf.codi, rf.tiporecursofinanciero
// 				FROM gf_recurso_financiero rf
// 				WHERE rf.codi = '$cod'";


// 	$resultado = $mysqli->query($sql);

// 	$filas =  mysqli_num_rows($resultado);

// 	if ($filas != 0) {
// 		while ($row = mysqli_fetch_row($resultado)) {
// 			echo '<input type="hidden" name="id" id="id" value="' . md5($row[0]) . '"/>;true';
// 			print($filas);
// 		}
// 	} else {
// 		echo 'false;0';
// 	}
// }
?>

 <?php
	require_once 'Conexion/conexionPDO.php';
	$con = new ConexionPDO();

	if (!empty($_REQUEST["cod"]) || !isset($_REQUEST["cod"])) {


		$cod = $_REQUEST["cod"];


		$filas = $con->Listar("SELECT DISTINCT rf.id_unico, rf.nombre, rf.codi, rf.tiporecursofinanciero
		FROM gf_recurso_financiero rf
		WHERE rf.codi = '$cod'");

		//$filas = count($filas); 

		if (count($filas) != 0) {
			echo 2;
		} else {
			echo 1;
		}
	}
	?>