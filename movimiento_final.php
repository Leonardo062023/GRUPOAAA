<?php

require_once('Conexion/conexion.php');
	session_start();
        $valor= $_REQUEST['id_clase'];

        if($valor==11){
                $sqlMFinal ="SELECT DISTINCT t.id_unico, t.sigla, t.nombre 
                FROM gf_movimiento m 
                LEFT JOIN gf_tipo_movimiento t ON m.tipomovimiento = t.id_unico 
                LEFT JOIN gf_clase c ON c.id_unico= t.clase
                WHERE t.clase IN (7,2,6,3)  
                ORDER BY t.sigla DESC";
        }else{
                $sqlMFinal ="SELECT DISTINCT t.id_unico, t.sigla, t.nombre 
                FROM gf_movimiento m 
                LEFT JOIN gf_tipo_movimiento t ON m.tipomovimiento = t.id_unico 
                LEFT JOIN gf_clase c ON c.id_unico= t.clase
                WHERE t.clase IN (".$_REQUEST['id_clase'].")  
                ORDER BY t.sigla DESC";
        }


 	$MovFi = $mysqli->query($sqlMFinal);
	while ($row = mysqli_fetch_row($MovFi))
	{
    	echo '<option value="'.$row[1].'">'.($row[1]).'-'.ucwords(mb_strtolower($row[2])).'</option>';
	}

?>


