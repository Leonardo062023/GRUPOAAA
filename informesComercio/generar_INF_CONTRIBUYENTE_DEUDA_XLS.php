<?php
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Contribuyentes_Morosos.xls");
    require_once("../Conexion/conexion.php");
    session_start();
    ini_set('max_execution_time', 0);
    
    $compania = $_SESSION['compania'];
    $usuario = $_SESSION['usuario'];
    $anno = $_SESSION['anno'];
    $anio = "SELECT anno FROM  gf_parametrizacion_anno WHERE id_unico = '$anno'";
    $ann = $mysqli->query($anio);
    $ANI = mysqli_fetch_row($ann);
    
    $AN = $ANI[0] - 1;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Contribuyentes Morosos</title>
    </head>
    <body>
        <table width="100%" border="1" cellspacing="0" cellpadding="0">
            <?php

                $colO = "";                 //Nombre de columna origen
                $colD = 0;                  //Contador de columnas destino
                $columnasDestino = "";      //Nombres de columnas Destino
                $tablaOrigen = "";          //Nombres de tabla Origen
                $tablasDestino = "";        //Nombres de tablas Destino
                $consultasTablaD = "";      //Consultas de tabla destino
                $idTH = "";                 //Id de las tablas homologables
                $consultaTablaO = "";       //Consulta de la tabla de origen
            ?>
            <thead>
                <?php
                    $sqlC = "SELECT     ter.id_unico,
                                    ter.razonsocial,
                                    UPPER(ti.nombre),
                                    ter.numeroidentificacion,
                                    dir.direccion,
                                    tel.valor,
                                    ter.ruta_logo
                            FROM gf_tercero ter
                            LEFT JOIN   gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
                            LEFT JOIN   gf_direccion dir ON dir.tercero = ter.id_unico
                            LEFT JOIN   gf_telefono  tel ON tel.tercero = ter.id_unico
                            WHERE ter.id_unico = $compania";

                    $resultC = $mysqli->query($sqlC);
                    $rowC = mysqli_fetch_row($resultC);
                    $razonsocial = $rowC[1];
                    $nombreIdent = $rowC[2];
                    $numeroIdent = $rowC[3];
                    $direccinTer = $rowC[4];
                    $telefonoTer = $rowC[5];
                    
                ?>
                    <tr>
                        <th colspan="6" align="center" ><strong>
                            <br/>&nbsp;
                            <br/><?php echo $razonsocial ?>
                            <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
                            <br/>&nbsp;
                            LISTADO DE CONTRIBUYENTES MOROSOS
                            </strong>
                            <br/>&nbsp;
                        </th>
                    </tr>
            </thead>
            <tbody>
                <tr>
                    <th align="center">CODIGO MATRICULA</th>
                    <th align="center">CONTRIBUYENTE</th>
                    <th align="center">RESPRESENTANTE LEGAL</th>
                    <th align="center">DIRECCIÓN</th>
                    <th align="center">TELÉFONO</th>
                    <th align="center">AÑOS DEUDA</th>
                </tr>
                    
                <?php 
                    $sql = "SELECT c.id_unico,
                                       IF(CONCAT_WS(' ',
                                        t.nombreuno,
                                        t.nombredos,
                                        t.apellidouno,
                                        t.apellidodos) 
                                        IS NULL OR CONCAT_WS(' ',
                                        t.nombreuno,
                                        t.nombredos,
                                        t.apellidouno,
                                        t.apellidodos) = '',
                                        (t.razonsocial),
                                        CONCAT_WS(' ',
                                        t.nombreuno,
                                        t.nombredos,
                                        t.apellidouno,
                                        t.apellidodos)) AS NOMBRETERCEROCONTRIBUYENTE, 
                                        c.codigo_mat,
                                        t.representantelegal,
                                        c.dir_correspondencia,
                                        c.telefono,
                                        c.fechainscripcion
                
                            FROM gc_contribuyente c 
                            LEFT JOIN gf_tercero t ON t.id_unico=c.tercero
                            WHERE c.estado = 1 ";  

                    $resultado=$mysqli->query($sql);
                   
                    while($row=mysqli_fetch_array($resultado)){
                        $fechaIDiv = explode('-',$row[6]);
                        $annoI = $fechaIDiv[0];
                        if(!empty($row[6])){
                            #Consulta el representante legal del contribuyente 
                            if(!empty($row[3])){
                                $Representante = "SELECT    IF(CONCAT_WS(' ',
                                                            t.nombreuno,
                                                            t.nombredos,
                                                            t.apellidouno,
                                                            t.apellidodos) 
                                                            IS NULL OR CONCAT_WS(' ',
                                                            t.nombreuno,
                                                            t.nombredos,
                                                            t.apellidouno,
                                                            t.apellidodos) = '',
                                                            (t.razonsocial),
                                                            CONCAT_WS(' ',
                                                            t.nombreuno,
                                                            t.nombredos,
                                                            t.apellidouno,
                                                            t.apellidodos)) AS NOMBRE
                                                FROM gf_tercero t 
                                                WHERE t.id_unico = '$row[3]'";

                                $repre = $mysqli->query($Representante);
                                $RE = mysqli_fetch_row($repre);
                            }else{
                                $RE[0] = "";
                            }

                            #consulta las activades del contribuyente
                            $deuda = "SELECT DISTINCT ac.vigencia  FROM  gc_anno_comercial ac
                                                LEFT JOIN gc_declaracion d ON d.periodo = ac.id_unico
                                                LEFT JOIN gc_recaudo_comercial rc ON rc.declaracion = d.id_unico
                                                LEFT JOIN gc_contribuyente c ON d.contribuyente = c.id_unico
                                                WHERE ac.vigencia BETWEEN '$annoI' AND '$AN' AND ac.mes = 0
                                                AND ac.id_unico NOT IN(SELECT de.periodo FROM gc_recaudo_comercial rco
                                                LEFT JOIN gc_declaracion de ON rco.declaracion = de.id_unico
                                                WHERE contribuyente = $row[0] AND de.clase = 1)  
                                                ORDER BY ac.vigencia ASC ";
                            #echo "<br/>";
                            $deu = $mysqli->query($deuda);
                            #echo "<br/>";

                            $deuda2 = "SELECT COUNT(DISTINCT ac.vigencia) FROM  gc_anno_comercial ac

                                            LEFT JOIN gc_declaracion d ON d.periodo = ac.id_unico
                                            LEFT JOIN gc_recaudo_comercial rc ON rc.declaracion = d.id_unico
                                            LEFT JOIN gc_contribuyente c ON d.contribuyente = c.id_unico
                                            WHERE ac.vigencia BETWEEN '$annoI' AND '$AN' AND ac.mes = 0 AND  ac.id_unico NOT IN(SELECT de.periodo FROM gc_recaudo_comercial rco LEFT JOIN gc_declaracion de ON rco.declaracion = de.id_unico WHERE contribuyente = $row[0] AND de.clase = 1)";
                            #echo "<br/>";
                            $deu2 = $mysqli->query($deuda2);            
                            $ndeu = mysqli_fetch_row($deu2);

                            
                           
                            
                            $cant = $ndeu[0] + 1;
                            if($ndeu[0] > 0){
                    ?>
                                <tr>
                                    <td rowspan="<?php echo $cant ?>" style='mso-number-format:\@'><?php echo $row[2] ?></td>
                                    <td colspan="" rowspan="<?php echo $cant ?>" headers=""><?php echo $row[1] ?></td>
                                    <td colspan="" rowspan="<?php echo $cant ?>" headers=""><?php echo $RE[0] ?></td>
                                    <td colspan="" rowspan="<?php echo $cant ?>" headers=""><?php echo $row[4] ?></td>
                                    <td colspan="" rowspan="<?php echo $cant ?>" headers=""><?php echo $ndeu[0] ?></td>
                                 </tr>   

                    <?php
                                    while($DE = mysqli_fetch_row($deu)){
                    ?>
                                            <tr>
                                                <td><?php echo $DE[0] ?></td>
                                            </tr>
                    <?php
                                    }
                    ?>  
                                    
                               
                                
                    <?php            
                            }

                        }    
                        
                    }                 
                ?> 
            </body>  
        </table>
    </body>
</html>