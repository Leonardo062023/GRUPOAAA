<?php

require_once('Conexion/conexion.php');
    session_start();
    
    $claseM = "SELECT DISTINCT id_unico,
                               nombre
                      FROM   gf_clase
                      WHERE id_unico IN (1,4,5)
                      ORDER  BY nombre ASC";
    $clase = $mysqli->query($claseM);

    
    echo '<option value="">Clase Movimiento</option>';
    echo '<option value="11"> Movimientos Almacen</option>';
	while ($row = mysqli_fetch_row($clase))
	{
		echo '<option value="'.$row[0].'">'.($row[1]).'</option>';
	}

?>

