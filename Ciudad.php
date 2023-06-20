<?php
	require_once('Conexion/conexion.php');
	session_start();

	$sqlCiudad = "SELECT Id_Unico, Nombre	
        FROM gf_ciudad 
        WHERE Departamento = ".$_REQUEST['id_depto']." 
        ORDER BY Nombre ASC";
		$queryC = oci_parse($oracle, $sqlCiudad);        // Preparar la sentencia
		$resC   = oci_execute( $queryC ); 
		if( $resC == true )
		{
		  while  (($fileC=oci_fetch_assoc($queryC))!=false){
			  echo '<option value="' . $fileC['ID_UNICO'] . '">' . ucwords(mb_strtolower($fileC['NOMBRE'])) . ' - ' . $file['NUMEROIDENTIFICACION']. '</option>';
		  }

		}

?>
