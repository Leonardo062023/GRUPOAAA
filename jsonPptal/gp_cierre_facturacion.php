<?php
require '../Conexion/ConexionPDO.php';               
require '../Conexion/conexion.php';               
require './../jsonPptal/funcionesPptal.php';               
require './../jsonServicios/funcionesServicios.php';               
require './calcular.php';               
ini_set('max_execution_time', 0);
ini_set('memory_limit','160000M');
@session_start();
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];
$fechaE     = date('Y-m-d'); 
$anno       = $_SESSION['anno'];
$panno      = $_SESSION['anno'];
$action     = 1;
$cc         = $con->Listar("SELECT id_unico FROM gf_centro_costo WHERE nombre = 'Varios' AND parametrizacionanno = $anno");
$centroc    = $cc[0][0];
$usuario_t  = $_SESSION['usuario_tercero'];
$Cal        = new Field_calculate();
switch ($action){
    #** Guardar Facturación **#
    case 1:
    
        $html       ='';
        $rta        = 0;
        $periodo    = 12;
        $fecha_f    = '2020-12-23';
        $tfr        = 0;
        #*** Buscar Perido Anterior ***#
        $periodoa   = 11;
        if($periodoa==""){
            $html = 'No Se Encontró Periodo Anterior';
            $rta  = 1;
        } else {
            #*** Buscar Tipo Factura ***#
            $tf = $con->Listar("SELECT * FROM gp_tipo_factura WHERE servicio = 1 AND compania = $compania");
            if(count($tf)>0){
                $tipo_factura = $tf[0][0];
                $rowl = $con->Listar("SELECT DISTINCT  uvms.id_unico, uv.estrato, 
                    uv.codigo_ruta, uv.codigo_interno,
                    pr.codigo_catastral,  t.id_unico, 
                    uvs.id_unico, uv.id_unico , 
                    m.id_unico, m.estado_medidor , 
                    uv.uso, uv.deshabilitado , 
                    CONCAT_WS(' ',s.codigo,s.nombre), 
                    uv.codigo_ruta, uv.codigo_interno, 
                    IF(t.razonsocial IS NULL OR t.razonsocial ='', 
                    CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos), t.razonsocial), 
                    uv.direccion, CONCAT_WS(' ',est.codigo,est.nombre), 
                    CONCAT_WS(' ',uso.codigo,uso.nombre), 
                    m.numero_medidor , t.id_unico, m.estado_medidor , 
                    uso.codigo , est.codigo , uv.deuda, uv.cuota

                    FROM gp_unidad_vivienda_medidor_servicio uvms 
                    LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
                    LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico  
                    LEFT JOIN gp_medidor m ON uvms.medidor = m.id_unico 
                    LEFT JOIN gp_sector s ON uv.sector = s.id_unico 
                    LEFT JOIN gp_tipo_servicio ts ON uvs.tipo_servicio = ts.id_unico 
                    LEFT JOIN gf_tercero t ON t.id_unico = uv.tercero 
                    LEFT JOIN gp_predio1 pr ON uv.predio = pr.id_unico 
                    LEFT JOIN gp_estrato est ON uv.estrato = est.id_unico 
                    LEFT JOIN gp_uso uso ON uv.uso = uso.id_unico 
                    WHERE uv.estado !=6  
                    ORDER BY uvms.id_unico ");
                ECHO "SELECT DISTINCT  uvms.id_unico, uv.estrato, 
                    uv.codigo_ruta, uv.codigo_interno,
                    pr.codigo_catastral,  t.id_unico, 
                    uvs.id_unico, uv.id_unico , 
                    m.id_unico, m.estado_medidor , 
                    uv.uso, uv.deshabilitado , 
                    CONCAT_WS(' ',s.codigo,s.nombre), 
                    uv.codigo_ruta, uv.codigo_interno, 
                    IF(t.razonsocial IS NULL OR t.razonsocial ='', 
                    CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos), t.razonsocial), 
                    uv.direccion, CONCAT_WS(' ',est.codigo,est.nombre), 
                    CONCAT_WS(' ',uso.codigo,uso.nombre), 
                    m.numero_medidor , t.id_unico, m.estado_medidor , 
                    uso.codigo , est.codigo , uv.deuda, uv.cuota

                    FROM gp_unidad_vivienda_medidor_servicio uvms 
                    LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvms.unidad_vivienda_servicio = uvs.id_unico 
                    LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico  
                    LEFT JOIN gp_medidor m ON uvms.medidor = m.id_unico 
                    LEFT JOIN gp_sector s ON uv.sector = s.id_unico 
                    LEFT JOIN gp_tipo_servicio ts ON uvs.tipo_servicio = ts.id_unico 
                    LEFT JOIN gf_tercero t ON t.id_unico = uv.tercero 
                    LEFT JOIN gp_predio1 pr ON uv.predio = pr.id_unico 
                    LEFT JOIN gp_estrato est ON uv.estrato = est.id_unico 
                    LEFT JOIN gp_uso uso ON uv.uso = uso.id_unico 
                    WHERE uv.estado !=6  
                    ORDER BY uvms.id_unico ";
                for ($i = 0; $i < count($rowl); $i++) {
                    $uvms       = $rowl[$i][0];
                    $uv         = $rowl[$i][7];
                    #************* Guardar Factura **************#
                    $sql = $con->Listar("SELECT MAX(cast(numero_factura as unsigned))+1 FROM gp_factura where tipofactura = $tipo_factura ");
                    $numero = $sql[0][0];
                    $descripcion= 'Factura Unidad Vivienda:'.$rowl[$i][3];
            
                    
                    $sql_cons ="INSERT INTO `gp_factura` 
                            ( `numero_factura`, `tercero`, `tipofactura`,
                        `unidad_vivienda_servicio`,`periodo`,`fecha_factura`,
                        `fecha_vencimiento`,`descripcion`,`lectura`,
                        `parametrizacionanno`,`estado_factura`,`centrocosto`) 
                    VALUES  (:numero_factura,  :tercero, :tipofactura, 
                        :unidad_vivienda_servicio,:periodo,:fecha_factura,
                        :fecha_vencimiento,:descripcion,:lectura,
                        :parametrizacionanno,:estado_factura,:centrocosto)";
                    $sql_dato = array(
                            array(":numero_factura",$numero),
                            array(":tercero",$rowl[$i][20]),
                            array(":tipofactura",$tipo_factura),
                            array(":unidad_vivienda_servicio",$uvms),
                            array(":periodo",$periodo),
                            array(":fecha_factura",$fecha_f),
                            array(":fecha_vencimiento",$fecha_f),
                            array(":descripcion",$descripcion),
                            array(":lectura",NULL),
                            array(":parametrizacionanno",$anno),
                            array(":estado_factura",4),
                            array(":centrocosto",12),
                    );
                    $resp       = $con->InAcEl($sql_cons,$sql_dato);
                    
                    $fi         = $con->Listar("SELECT * FROM gp_factura WHERE unidad_vivienda_servicio = $uvms AND periodo = $periodo AND tipofactura = $tipo_factura");
                    $id_factura = $fi[0][0];
                    

                    
                    
             /*
                    #********************************************#
                    #*** Buscar Lectura Anterior ***#
                    $la = $con->Listar("SELECT valor FROM gp_lectura 
                        WHERE unidad_vivienda_medidor_servicio = $uvms AND periodo = $periodoa");
                    $la = $la[0][0];
                    
                    $ids_uv = $uvms;
                    #********* Buscar Si existe deuda anterior **********#
                    $deuda_anterior = 0;
                    if(!empty($rowl[$i][24])){
                        $deuda_anterior = $rowl[$i][24];
                    }
                    $cuota = 0;
                    if(!empty($rowl[$i][25])){
                        $cuota = $rowl[$i][25];
                    }
                    
                   
                    
                    #** Atraso **#  
                    $datr = $con->Listar("SELECT GROUP_CONCAT(df.id_unico), df.factura 
                        FROM gp_detalle_factura df 
                        LEFT JOIN gp_factura f ON f.id_unico = df.factura 
                        WHERE f.unidad_vivienda_servicio IN ($ids_uv) AND f.periodo <= $periodoa 
                        GROUP BY df.factura ");
                    $atraso = 0; 
                    if(count($datr)>0){
                        for ($at= 0; $at < count($datr); $at++) {
                            #*** Buscar Recaudo ***#
                            $id_df      = $datr[$at][0];
                            $dav = $con->Listar("SELECT SUM(df.valor_total_ajustado) 
                            FROM gp_detalle_factura df 
                            LEFT JOIN gp_factura f ON f.id_unico = df.factura 
                            WHERE df.id_unico IN ($id_df)");
                            $valor_f    = $dav[0][0];
                            $rc = $con->Listar("SELECT SUM(dp.valor) FROM gp_detalle_pago dp 
                                LEFT JOIN gp_pago p ON dp.pago = p.id_unico 
                                WHERE p.fecha_pago <'$fecha_f' AND dp.detalle_factura IN ($id_df)");
                            if(count(($rc))>0 && !empty($rc[0][0])){
                                $recaudo = $rc[0][0];
                            }else {
                                $recaudo = 0;
                            }
                            if(($valor_f -$recaudo)>0){
                                $atraso +=1;
                            }
                        }

                    }
                    #* Consumos Anteriores
                    $ba = $con->Listar("SELECT cantidad_facturada FROM gp_lectura 
                        WHERE unidad_vivienda_medidor_servicio = $uvms 
                            AND periodo < $periodo  ORDER BY periodo DESC");
                    $valor6 = $ba[0][0];
                    $valor5 = $ba[1][0];
                    $valor4 = $ba[2][0];
                    $valor3 = $ba[3][0];
                    $valor2 = $ba[4][0];
                    $valor1 = $ba[5][0];
                    
                    $promedio   = (($valor1+$valor2+$valor3+$valor4+$valor5+$valor6)/6);
                    $promedio   = round($promedio,0);
    
                    #******** Lectura Anterior ********#
                    $lab = $con->Listar("SELECT valor, cantidad_facturada FROM gp_lectura WHERE unidad_vivienda_medidor_servicio = $uvms AND periodo = $periodoa");
                    if(count($lab)>0){
                       $la = $lab[0][0];
                    } else {
                       $la = 0;
                    }
                    #*** Buscar Conceptos Acueducto***#
                    #Tarifa por uso
                    $tarifa_Fija            = 0;
                    $tarifa_Basico          = 0;
                    $tarifa_Complementario  = 0;
                    $tarifa_Suntuario       = 0;
                    $alcantarillado_fijo    = 0;    
                    $alcantarillado_Basico  = 0;    
                    $alcantarillado_Complementario  = 0;
                    $alcantarillado_Suntuario       = 0;
                    $subsidio_Aseo_Unico    = 0;



                    switch($rowl[$i][22]){
                        case 1:
                            switch($rowl[$i][23]){
                                case 1:
                                    $tarifa_Fija = 2055.26;
                                    $tarifa_Basico = 1097.35;
                                    $tarifa_Complementario = 2239.5;
                                    $tarifa_Suntuario = 2239.5;
                                    $alcantarillado_fijo = 1982.22; 
                                    $alcantarillado_Basico=637.91;  
                                    $alcantarillado_Complementario=708.79;
                                    $alcantarillado_Suntuario=708.79;
                                    $subsidio_Aseo_Unico    = 7509;
                                break;
                                case 2:
                                    $tarifa_Fija = 3385.13;
                                    $tarifa_Basico = 1634.83;
                                    $tarifa_Complementario = 2239.5;
                                    $tarifa_Suntuario = 2239.5;
                                    $alcantarillado_fijo = 1982.22; 
                                    $alcantarillado_Basico=673.35;  
                                    $alcantarillado_Complementario=708.79;
                                    $alcantarillado_Suntuario=708.79;
                                    $subsidio_Aseo_Unico    = 6763;
                                    
                                break;
                                case 3:
                                    $tarifa_Fija = 4594.1;
                                    $tarifa_Basico = 2127.52;
                                    $tarifa_Complementario = 2239.5;
                                    $tarifa_Suntuario = 2239.5;
                                    $alcantarillado_fijo = 2406.98; 
                                    $alcantarillado_Basico=708.79;  
                                    $alcantarillado_Complementario=708.79;
                                    $alcantarillado_Suntuario=708.79;
                                    $subsidio_Aseo_Unico    = 3084;
                                break;
                                case 4:
                                    $tarifa_Fija = 4835.9;
                                    $tarifa_Basico = 2239.5;
                                    $tarifa_Complementario = 2239.5;
                                    $tarifa_Suntuario = 2239.5;
                                    $alcantarillado_fijo = 2831.74; 
                                    $alcantarillado_Basico=708.79;  
                                    $alcantarillado_Complementario=708.79;
                                    $alcantarillado_Suntuario=708.79;
                                break;
                            
                            
                                case 5:
                                    $tarifa_Fija = 7253.84;
                                    $tarifa_Basico = 3359.24;
                                    $tarifa_Complementario = 3359.24;
                                    $tarifa_Suntuario = 3359.24;
                                    $alcantarillado_fijo = 4247.61; 
                                    $alcantarillado_Basico=1063.19; 
                                    $alcantarillado_Complementario=1063.19;
                                    $alcantarillado_Suntuario=1063.19;
                                break;
                                case 6:
                                    $tarifa_Fija = 7253.84;
                                    $tarifa_Basico = 3359.24;
                                    $tarifa_Complementario = 3359.24;
                                    $tarifa_Suntuario = 3359.24;
                                    $alcantarillado_fijo = 4247.61; 
                                    $alcantarillado_Basico=1063.19; 
                                    $alcantarillado_Complementario=1063.19;
                                    $alcantarillado_Suntuario=1063.19;
                                break;
                            
                            } 
                        break;
                    
                        case 3:
                            switch($rowl[$i][23]){
                                case (1):
                                case (2):
                                case (3):
                                case (4):
                                case (10):
                                case (11):
                                case (12):
                                case (14):
                                    $tarifa_Fija = 7253.84;
                                    $tarifa_Basico = 3359.24;
                                    $tarifa_Complementario = 3359.24;
                                    $tarifa_Suntuario = 3359.24;
                                    $alcantarillado_fijo = 4247.61; 
                                    $alcantarillado_Basico=1063.19; 
                                    $alcantarillado_Complementario=1063.19;
                                    $alcantarillado_Suntuario=1063.19;
                                break;
                            
                            } 
                        break;
                    
                        case 4:
                            switch($rowl[$i][23]){
                                case (1):
                                case (2):
                                case (3):
                                case (4):
                                case (10):
                                case (11):
                                case (12):
                                case (13):
                                case (14):
                                case (15):
                                case (16):
                                 
                                    $tarifa_Fija = 6286.66;
                                    $tarifa_Basico = 2911.34;
                                    $tarifa_Complementario = 2911.34;
                                    $tarifa_Suntuario = 2911.34;
                                    $alcantarillado_fijo = 3681.26; 
                                    $alcantarillado_Basico=921.43;  
                                    $alcantarillado_Complementario=921.43;
                                    $alcantarillado_Suntuario=921.43;
                                break;
                            
                            } 
                        break;
                        case 5:
                            switch($rowl[$i][23]){
                                case (1):
                                case (2):
                                case (3):
                                case (4):
                                case (10):
                                case (11):
                                case (12):
                                case (13):
                                case (14):
                                case (15):
                                case (16):
                                 
                                    $tarifa_Fija = 4835.9;
                                    $tarifa_Basico = 2239.5;
                                    $tarifa_Complementario = 2239.5;
                                    $tarifa_Suntuario = 2239.5;
                                    $alcantarillado_fijo = 2831.74; 
                                    $alcantarillado_Basico=708.79;  
                                    $alcantarillado_Complementario=708.79;
                                    $alcantarillado_Suntuario=708.79;
                                break;
                            
                            } 
                        break;
                    } 
                    
                  
       
                    $sql_cons ="INSERT INTO `gp_facturacion_servicios` 
                            ( `identificador`, `fecha`, `fecha_vencimiento`, 
                            `periodo`, `numero_factura`, `sector`, 
                            `codigo_ruta`, `codigo_interno`, `usuario`, 
                            `direccion`, `estrato`, `uso`, `numero_medidor`,                            
                            `consumo_mes_6`, `consumo_mes_5`, 
                            `consumo_mes_4`, `consumo_mes_3`, 
                            `consumo_mes_2`, `consumo_mes_1`, 
                            `promedio`, `deuda_anterior`, 
                            `atraso`, `estado_medidor`, 
                            `lectura_anterior`, 
                            `consumo_basico`, `consumo_complementario`, `consumo_suntuario`, 
                            `acueducto_valor_mtr_3`, `acueducto_cargo_fijo`, 
                            `acueducto_consumo_basico`, `acueducto_consumo_complementario`, 
                            `acueducto_consumo_suntuario`, `acueducto_subsido_cargo_fijo`, 
                            `acueducto_subsido_basico`, `acueducto_subsido_complementario`, 
                            `acueducto_subsido_suntuario`, `acueducto_contribucion`, 
                            `acueducto_mora`, 
                            `alcantarillado_valor_mtr_3`, `alcantarillado_cargo_fijo`, 
                            `alcantarillado_consumo_basico`, `alcantarillado_consumo_complementario`,
                            `alcantarillado_consumo_suntuario`, `alcantarillado_subsido_cargo_fijo`,
                            `alcantarillado_subsido_basico`, `alcantarillado_subsido_complementario`,
                            `alcantarillado_subsido_suntuario`, `alcantarillado_contribucion`, 
                            `alcantarillado_mora`, 
                             `aseo_valor_mtr_3`, `aseo_cargo_fijo`, `aseo_consumo_basico`, 
                             `aseo_consumo_complementario`, `aseo_consumo_suntuario`, 
                             `aseo_subsido_cargo_fijo`, `aseo_subsido_basico`, 
                             `aseo_subsido_complementario`, `aseo_subsido_suntuario`, 
                             `aseo_contribucion`, `aseo_mora`,`financiacion`) 
                    VALUES  (:identificador,  :fecha, :fecha_vencimiento, 
                        :periodo,:numero_factura,:sector,
                        :codigo_ruta,:codigo_interno,:usuario,
                        :direccion,:estrato,:uso, :numero_medidor, 
                        :consumo_mes_6, :consumo_mes_5, 
                        :consumo_mes_4, :consumo_mes_3, 
                        :consumo_mes_2, :consumo_mes_1, 
                        :promedio, :deuda_anterior, 
                        :atraso, :estado_medidor, 
                        :lectura_anterior, 
                        :consumo_basico, :consumo_complementario, :consumo_suntuario, 
                        :acueducto_valor_mtr_3, :acueducto_cargo_fijo, 
                        :acueducto_consumo_basico, :acueducto_consumo_complementario, 
                        :acueducto_consumo_suntuario, :acueducto_subsido_cargo_fijo, 
                        :acueducto_subsido_basico, :acueducto_subsido_complementario, 
                        :acueducto_subsido_suntuario, :acueducto_contribucion, 
                        :acueducto_mora, 
                        :alcantarillado_valor_mtr_3, :alcantarillado_cargo_fijo, 
                        :alcantarillado_consumo_basico, :alcantarillado_consumo_complementario, 
                        :alcantarillado_consumo_suntuario, :alcantarillado_subsido_cargo_fijo, 
                        :alcantarillado_subsido_basico, :alcantarillado_subsido_complementario, 
                        :alcantarillado_subsido_suntuario, :alcantarillado_contribucion, 
                        :alcantarillado_mora, 
                        :aseo_valor_mtr_3, :aseo_cargo_fijo, :aseo_consumo_basico, 
                        :aseo_consumo_complementario, :aseo_consumo_suntuario, :aseo_subsido_cargo_fijo, 
                        :aseo_subsido_basico, :aseo_subsido_complementario, :aseo_subsido_suntuario, 
                        :aseo_contribucion, :aseo_mora, :financiacion)";
                    $sql_dato = array(
                            array(":identificador",$uvms),
                            array(":fecha",$fecha_f),
                            array(":fecha_vencimiento",'2021-01-22'),
                            array(":periodo",$periodo),
                            array(":numero_factura",$numero),
                            array(":sector",$rowl[$i][12]),
                            array(":codigo_ruta",$rowl[$i][13]),
                            array(":codigo_interno",$rowl[$i][14]),
                            array(":usuario",$rowl[$i][15]),
                            array(":direccion",$rowl[$i][16]),
                            array(":estrato",$rowl[$i][17]),
                            array(":uso",$rowl[$i][18]),
                            array(":numero_medidor",$rowl[$i][19]),
                            array(":consumo_mes_6",$valor6),
                            array(":consumo_mes_5",$valor5),
                            array(":consumo_mes_4",$valor4),
                            array(":consumo_mes_3",$valor3),
                            array(":consumo_mes_2",$valor2),
                            array(":consumo_mes_1",$valor1),
                            array(":promedio",$promedio),
                            array(":deuda_anterior",$deuda_anterior),
                            array(":atraso",$atraso),
                            array(":estado_medidor",$rowl[$i][21]),
                            array(":lectura_anterior",$la),
                            array(":consumo_basico",11),
                            array(":consumo_complementario",100000),
                            array(":consumo_suntuario",1000000000),
                        
                        array(":acueducto_valor_mtr_3",$tarifa_Fija),
                        array(":acueducto_cargo_fijo",$tarifa_Fija),
                        array(":acueducto_consumo_basico",'IF(&consumo_facturado&<=&consumo_basico&,&acueducto_valor_mtr_3&*&consumo_facturado&,&acueducto_valor_mtr_3&*&consumo_basico&)'),
                        array(":acueducto_consumo_complementario",'IF(&consumo_facturado&<=&consumo_complementario&,&acueducto_valor_mtr_3&*(&consumo_facturado& - &consumo_basico&),&acueducto_valor_mtr_3&*(&consumo_facturado& - &consumo_basico&))'),                       
                        array(":acueducto_consumo_suntuario",0),
                        array(":acueducto_subsido_cargo_fijo",$la),
                        array(":acueducto_subsido_basico",$la),
                        array(":acueducto_subsido_complementario",$la),
                        array(":acueducto_subsido_suntuario",$la),
                        array(":acueducto_contribucion",$la),
                        array(":acueducto_mora",$la),

                        array(":alcantarillado_valor_mtr_3",$la),
                        array(":alcantarillado_cargo_fijo",$tarifa_Fija),
                        
                        array(":alcantarillado_consumo_basico",'IF(&consumo_facturado&<=&consumo_basico&,&alcantarillado_valor_mtr_3&*&consumo_facturado&,&alcantarillado_valor_mtr_3&*&consumo_basico&)'),
                        array(":alcantarillado_consumo_complementario",'IF(&consumo_facturado&<=&consumo_complementario&,&alcantarillado_valor_mtr_3&*(&consumo_facturado& - &consumo_basico&),&alcantarillado_valor_mtr_3&*(&consumo_facturado& - &consumo_basico&))'),                       
                                            
                        array(":alcantarillado_consumo_suntuario",$la),
                        array(":alcantarillado_subsido_cargo_fijo",$la),
                        array(":alcantarillado_subsido_basico",$la),
                        array(":alcantarillado_subsido_complementario",$la),
                        array(":alcantarillado_subsido_suntuario",$la),
                        array(":alcantarillado_contribucion",$la),
                        array(":alcantarillado_mora",$la),
                        
                        array(":aseo_valor_mtr_3",$la),
                        array(":aseo_cargo_fijo",$tarifa_Fija),
                        array(":aseo_consumo_basico",$la),
                        array(":aseo_consumo_complementario",$la),                       
                        array(":aseo_consumo_suntuario",$la),
                        array(":aseo_subsido_cargo_fijo",$la),
                        array(":aseo_subsido_basico",$la),
                        array(":aseo_subsido_complementario",$la), 
                        array(":aseo_subsido_suntuario",$la),
                        array(":aseo_contribucion",$la),
                        array(":aseo_mora",$la),
                        array(":financiacion",$cuota),
                    );
                    $resp       = $con->InAcEl($sql_cons,$sql_dato);
                    var_dump($sql_cons);
                    
                    var_dump($sql_dato);
                    var_dump($resp);*/
                       $tfr +=1;
                }
            } else {
                $html = 'No Se Encontró Tipo Factura';
                $rta  = 1;
            }
        }
       
        $datos = array(); 
        $datos = array("html"=>$html,"rta"=>$rta,"total"=>$tfr);
        echo json_encode($datos);
    break;
    
}