<?php
require '../Conexion/ConexionPDO.php';                                                  
require '../Conexion/conexion.php';                    
require './funcionesPptal.php';
ini_set('max_execution_time', 0);
require ('../ExcelR/Classes/PHPExcel/IOFactory.php');
@session_start();
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];
$panno      = $_SESSION['anno'];
$anno       = anno($panno);
$action     = $_REQUEST['action'];
$fechaa     = date('Y-m-d');
$nanno      = anno($panno);

$pro    = $con->Listar("SELECT * FROM gf_proyecto WHERE nombre='Varios' AND compania = $compania");
$proy   = $pro[0][0]; 
switch ($action){
    #* Modificar Vida útil
    case 1:
        $id_producto = $_REQUEST['producto'];
        $valor       =  $_REQUEST['valor'];
        $sql_cons ="UPDATE gf_producto 
                SET vida_util_remanente=:vida_util_remanente 
                WHERE id_unico=:id_unico";
        $sql_dato = array(
            array(":vida_util_remanente",$valor),
            array(":id_unico",$id_producto),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($resp)){
            $e=1;
        } else {
            $e=0;
        }
        echo $e;
    break;
    case 2:
        $id_producto = $_REQUEST['producto'];
        $valor       =  $_REQUEST['valor'];
        $sql_cons ="UPDATE gf_producto_especificacion 
                SET valor=:valor 
                WHERE id_unico=:id_unico";
        $sql_dato = array(
            array(":valor",$valor),
            array(":id_unico",$id_producto),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($resp)){
            $e=1;
        } else {
            $e=0;
        }
        echo $e;
    break;
    case 3:
        #BUscar si ya existe
        $ex = $con->Listar("SELECT * FROM gf_producto_especificacion WHERE producto =".$_REQUEST['producto']." AND fichainventario = ".$_REQUEST['ficha']);
        if(!empty($ex[0][0])){
            $sql_cons ="UPDATE gf_producto_especificacion 
                SET valor=:valor 
                WHERE id_unico=:id_unico";
            $sql_dato = array(
                array(":valor",$_REQUEST['valor']),
                array(":id_unico",$ex[0][0]),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
        } else { 
            $sql_cons ="INSERT INTO gf_producto_especificacion 
                    ( valor,producto,fichainventario) 
            VALUES (:valor, :producto, :fichainventario)";
            $sql_dato = array(
                array(":valor",$_REQUEST['valor']),
                array(":producto",$_REQUEST['producto']),
                array(":fichainventario",$_REQUEST['ficha']),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
        }
        if(empty($resp)){
            $e=1;
        } else {
            $e=0;
        }
        echo $e;
    break;
    #* Modificar Descripcion
    case 4:
        $id_producto = $_REQUEST['producto'];
        $valor       =  $_REQUEST['valor'];
        $sql_cons ="UPDATE gf_producto 
                SET descripcion=:descripcion 
                WHERE id_unico=:id_unico";
        $sql_dato = array(
            array(":descripcion",$valor),
            array(":id_unico",$id_producto),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($resp)){
            $e=1;
        } else {
            $e=0;
        }
        echo $e;
    break;
    
    #* Guardar Imagen producto
    case 5:
        $e=0;
        if(!empty($_FILES['file']['name'])) {
            $id         = $_REQUEST['txtProducto'];    
            $imagen     = $_FILES['file'];
            $nombre     = $_FILES['file']['name'];
            $directorio ='../documentos/imagenes_producto/';
            $nombre     = $id.$nombre;
            $ruta       = 'documentos/imagenes_producto/'.$nombre;
            //var_dump($_FILES['file']['tmp_name'],$directorio.$nombre);
            $upd = move_uploaded_file($_FILES['file']['tmp_name'],$directorio.$nombre); 
            if($upd == true){
                $sql_cons ="INSERT INTO gf_imagen_producto 
                    ( producto,ruta) 
                VALUES (:producto, :ruta)";
                $sql_dato = array(
                    array(":producto",$id),
                    array(":ruta",$nombre)
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
                if(empty($resp)){
                    $e=1;
                }
            }
        }
        echo $e;
    break;
    #Eliminar Producto
    case 6:
        $id         = $_REQUEST['id'];    
        $sql_cons  = "DELETE FROM gf_imagen_producto 
            WHERE id_unico=:id_unico";
        $sql_dato = array(
                array(":id_unico",$id),	
        );
        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($resp)){
            $e=1;
        } else {
            $e=0;
        }
        echo $e;
    break;

    #Verificar que no tenga comprobantes asociados (Con detalle)
    case 7:
        $id_m = $_REQUEST['id'];
        $row = $con->Listar("SELECT DISTINCT dc.comprobante FROM gf_detalle_movimiento dm 
        LEFT JOIN gf_detalle_comprobante dc ON dm.detalle_comprobante = dc.id_unico 
        WHERE dm.movimiento = $id_m AND dm.detalle_comprobante IS NOT NULL");
        if(count($row)>0){
            $e=trim($row[0][0]);
        } else {
            $e=0;
        }
        echo $e;
    break;

    #Validar Datos Cargar Entrada
    case 8:
        $htmlI ='Elementos No Encontrados:<br/>';
        $dtguardados =0;
        if (!empty($_FILES['file']['tmp_name'])) {
            $inputFileName= $_FILES['file']['tmp_name'];                                       
            $objReader = new PHPExcel_Reader_Excel2007();					
            $objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
            #Escoger Hoja Movimiento
            $objWorksheet1 = $objPHPExcel->setActiveSheetIndex(0);				
            $total_filas1 = $objWorksheet1->getHighestRow();					
            $total_columnas1 = PHPExcel_Cell::columnIndexFromString($objWorksheet1->getHighestColumn());
            for ($a = 2; $a <= $total_filas1; $a++) {
                $elemento  = $objWorksheet1->getCellByColumnAndRow(0, $a)->getCalculatedValue();
                #Buscar Elemento
                $rowe = $con->Listar("SELECT * FROM gf_plan_inventario WHERE compania = $compania 
                    AND codi ='".$elemento."'");
                if(empty($rowe[0][0])){
                    $dtguardados +=1;
                    $htmlI .=$a." - ".$elemento."<br/>";
                }
            }
        }
        $datos = array("msj"=>$htmlI,"rta"=>$dtguardados); 
        echo json_encode($datos);
    break;
    #Subir Datos Entrada N!
    case 9:
        $idMov = $_REQUEST['idMov'];
        $htmlI ='';
        $dtguardados =0;
        if (!empty($_FILES['file']['tmp_name'])) {
            $inputFileName= $_FILES['file']['tmp_name'];                                       
            $objReader = new PHPExcel_Reader_Excel2007();					
            $objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
            #Escoger Hoja Movimiento
            $objWorksheet1 = $objPHPExcel->setActiveSheetIndex(0);				
            $total_filas1 = $objWorksheet1->getHighestRow();					
            $total_columnas1 = PHPExcel_Cell::columnIndexFromString($objWorksheet1->getHighestColumn());
            for ($a = 2; $a <= $total_filas1; $a++) {
                $elemento  = $objWorksheet1->getCellByColumnAndRow(0, $a)->getCalculatedValue();
                #Buscar Elemento
                $rowe = $con->Listar("SELECT * FROM gf_plan_inventario WHERE compania = $compania 
                    AND codi ='".$elemento."'");
                if(!empty($rowe[0][0])){
                    $id_elemento = $rowe[0][0];
                    $cantidad = $objWorksheet1->getCellByColumnAndRow(1, $a)->getCalculatedValue();
                    $valor    = $objWorksheet1->getCellByColumnAndRow(2, $a)->getCalculatedValue();
                    $iva      = $objWorksheet1->getCellByColumnAndRow(3, $a)->getCalculatedValue();
                    if(empty($iva)){
                        $iva = 0;
                    }
                    $valort = ($valor+$iva)*$cantidad;
                    $sql_cons ="INSERT INTO gf_detalle_movimiento  
                        ( cantidad,valor, iva,
                        movimiento,detalleasociado,planmovimiento,
                        xvalor_t,n_registro,observaciones,cantidad_origen) 
                    VALUES (:cantidad,:valor, :iva, 
                        :movimiento, :detalleasociado, :planmovimiento, 
                        :xvalor_t, :n_registro,:observaciones, :cantidad_origen)";
                    $sql_dato = array(
                        array(":cantidad",$cantidad),
                        array(":valor",$valor), 
                        array(":iva",$iva), 
                        array(":movimiento",$idMov), 
                        array(":detalleasociado",NULL), 
                        array(":planmovimiento",$id_elemento), 
                        array(":xvalor_t",$valort), 
                        array(":n_registro",$a),
                        array(":observaciones",'Cargado Por Archivo Plano Entrada'),
                        array(":cantidad_origen",$cantidad),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato); 
                    $id_detalle = $con->Listar("SELECT MAX(id_unico) FROM gf_detalle_movimiento WHERE movimiento = $idMov");
                    $id_detalle = $id_detalle[0][0];
                    
                    
                    
                    $descripcion = $objWorksheet1->getCellByColumnAndRow(4, $a)->getCalculatedValue();
                    $altura      = $objWorksheet1->getCellByColumnAndRow(5, $a)->getCalculatedValue();
                    $marca       = $objWorksheet1->getCellByColumnAndRow(6, $a)->getCalculatedValue();
                    $referencia  = $objWorksheet1->getCellByColumnAndRow(7, $a)->getCalculatedValue();
                    $generacion  = $objWorksheet1->getCellByColumnAndRow(8, $a)->getCalculatedValue();
                    $serial      = $objWorksheet1->getCellByColumnAndRow(9, $a)->getCalculatedValue();
                    $especif     = $objWorksheet1->getCellByColumnAndRow(10, $a)->getCalculatedValue();
                    $color       = $objWorksheet1->getCellByColumnAndRow(11, $a)->getCalculatedValue();
                    $modelo      = $objWorksheet1->getCellByColumnAndRow(12, $a)->getCalculatedValue();
                    $estado      = $objWorksheet1->getCellByColumnAndRow(13, $a)->getCalculatedValue();
                    $procedencia = $objWorksheet1->getCellByColumnAndRow(14, $a)->getCalculatedValue();
                    $doc_soporte = $objWorksheet1->getCellByColumnAndRow(15, $a)->getCalculatedValue();
                    $n_doc_sop   = $objWorksheet1->getCellByColumnAndRow(16, $a)->getCalculatedValue();
                    
                    
                    if(!empty($descripcion) || !empty($altura) || !empty($marca) 
                        || !empty($referencia) || !empty($generacion) || !empty($serial) || !empty($especif) 
                        || !empty($color) || !empty($modelo) || !empty($estado) || !empty($procedencia) 
                        || !empty($doc_soporte) || !empty($n_doc_sop)){
                        
                        $cantidad = (int) $cantidad;
                        for ($f = 0; $f < $cantidad; $f++) {
                            $sql_cons ="INSERT INTO gf_producto 
                                    ( descripcion, valor,vida_util_remanente,fecha_adquisicion,fecha_anterior) 
                            VALUES (:descripcion, :valor, :vida_util_remanente, :fecha_adquisicion , :fecha_anterior)";
                            $sql_dato = array(
                                array(":descripcion",$descripcion),
                                array(":valor",$valor),
                                array(":vida_util_remanente",12),
                                array(":fecha_adquisicion",$nanno.'-01-01'),
                                array(":fecha_anterior",$fechaa),
                            );
                            $resp = $con->InAcEl($sql_cons,$sql_dato);
                            //Buscar Producto creado 
                            $bvp = $con->Listar("SELECT MAX(id_unico) FROM gf_producto WHERE valor =".$valor);
                            $producto = $bvp[0][0];
                            $sql_cons ="INSERT INTO gf_movimiento_producto 
                                    ( detallemovimiento, producto) 
                            VALUES (:detallemovimiento, :producto )";
                            $sql_dato = array(
                                array(":detallemovimiento",$id_detalle),
                                array(":producto",$bvp[0][0]),
                            );
                            $resp = $con->InAcEl($sql_cons,$sql_dato);
                            
                            //BUSCAR PLACA 
                            $pl = $con->Listar("select MAX(cast(prdes.valor as unsigned)) from gf_producto_especificacion prdes   
                            LEFT JOIN gf_movimiento_producto movp ON movp.producto = prdes.producto
                            LEFT JOIN gf_detalle_movimiento detm ON detm.id_unico = movp.detallemovimiento
                            LEFT JOIN gf_movimiento mto ON mto.id_unico = detm.movimiento
                            LEFT JOIN gf_ficha_inventario fin on prdes.fichainventario = fin.id_unico    
                            LEFT JOIN gf_elemento_ficha elm on fin.elementoficha = elm.id_unico
                            WHERE elm.nombre = 'Placa' AND mto.compania = $compania");
                            $placa = $pl[0][0]+1;
                            crearEspecificaciones($producto,$marca,$serial,$color,$modelo,$estado,$procedencia,$doc_soporte,$n_doc_sop, $altura, $referencia,$generacion, $especif);
                            $dtguardados++;
                        }
                        
                    }
                        
                    
                }
            }
        }
        echo $dtguardados;
    break;
    
    #Verificar que los elementos este configurados para la interfaz
    case 10:
        $htmlI ='Elementos Sin Configuración :';
        $dtguardados =0;
        $idMov = $_REQUEST['idMov'];
        $rowe = $con->Listar("SELECT DISTINCT dm.planmovimiento, m.tipomovimiento, m.parametrizacionanno, 
            pi.codi, pi.nombre,pi.predecesor
            FROM gf_detalle_movimiento dm 
            LEFT JOIN gf_movimiento m ON dm.movimiento = m.id_unico 
            LEFT JOIN gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico 
            WHERE dm.movimiento = ".$idMov);
        for ($e = 0; $e < count($rowe); $e++) {
            $rowc = $con->Listar("SELECT * FROM gf_configuracion_almacen ca 
            WHERE ca.plan_inventario = ".$rowe[$e][5]." AND ca.tipo_movimiento = ".$rowe[$e][1]."
            AND ca.parametrizacion_anno = ".$rowe[$e][2]."");
            if(count($rowc)<=0){
                $htmlI .=$rowe[$e][3].' - '.$rowe[$e][4];
                $dtguardados ++;
            }
        }
        $datos = array("msj"=>$htmlI,"rta"=>$dtguardados); 
         $datos1= $htmlI.'%'.$dtguardados;

        echo $datos1;
    break;
    
    #Crear Interfaz
    case 11:        
        $dtguardados =0;
        $idMov = $_REQUEST['idMov'];
        #DATOS COMPROBANTE
        $rowdc = $con->Listar("SELECT m.numero, m.fecha, m.descripcion, tc.id_unico, m.tercero2, m.centrocosto 
            FROM gf_movimiento m 
            LEFT JOIN gf_tipo_comprobante tc ON m.tipomovimiento = tc.tipo_movimiento 
            WHERE m.id_unico = ".$idMov);
        #crear c
        $sql_cons ="INSERT INTO gf_comprobante_cnt 
                ( numero, fecha,descripcion, tipocomprobante,compania,
                parametrizacionanno,tercero, usuario, fecha_elaboracion ) 
        VALUES (:numero, :fecha, :descripcion,  :tipocomprobante,:compania, 
                :parametrizacion_anno, :tercero, :usuario,:fecha_elaboracion)";
        $sql_dato = array(
                array(":numero",$rowdc[0][0]),
                array(":fecha",$rowdc[0][1]),
                array(":descripcion",$rowdc[0][2]),
                array(":tipocomprobante",$rowdc[0][3]),
                array(":compania",$compania),   
                array(":parametrizacion_anno",$panno),
                array(":tercero",$rowdc[0][4]),
                array(":usuario",$usuario),
                array(":fecha_elaboracion",$fechaa),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);

        #************* Insertar Detalles CNT *************#
        $cn = $con->Listar("SELECT * FROM gf_comprobante_cnt WHERE numero = '".$rowdc[0][0]."' AND tipocomprobante = ".$rowdc[0][3]." AND parametrizacionanno =$panno");
        $idcnt=$cn[0][0];
        
        $rowe = $con->Listar("SELECT DISTINCT GROUP_CONCAT(dm.planmovimiento), m.tipomovimiento, m.parametrizacionanno, 
            pi.codi, pi.nombre
            FROM gf_detalle_movimiento dm 
            LEFT JOIN gf_movimiento m ON dm.movimiento = m.id_unico 
            LEFT JOIN gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico 
            WHERE dm.movimiento = ".$idMov."");        
        $rowc = $con->Listar("SELECT  ca.cuenta_debito,ca.cuenta_credito,ca.cuenta_iva,ca.cuenta_impo,
               GROUP_CONCAT(dm.planmovimiento), cd.naturaleza, cc.naturaleza, ci.naturaleza, cim.naturaleza
                  FROM gf_detalle_movimiento dm
                  LEFT JOIN gf_movimiento m ON dm.movimiento = m.id_unico
                  LEFT JOIN gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico
                  LEFT JOIN gf_configuracion_almacen ca ON ca.plan_inventario=pi.predecesor
                  AND ca.tipo_movimiento=m.tipomovimiento
                  AND ca.parametrizacion_anno=m.parametrizacionanno
                  LEFT JOIN gf_cuenta cd  ON ca.cuenta_debito = cd.id_unico
                  LEFT JOIN gf_cuenta cc  ON ca.cuenta_credito = cc.id_unico
                  LEFT JOIN gf_cuenta ci  ON ca.cuenta_iva = ci.id_unico
                  LEFT JOIN gf_cuenta cim ON ca.cuenta_impo = cim.id_unico
                  WHERE dm.movimiento =$idMov");
        for ($c= 0; $c < count($rowc); $c++) {
            $total = 0;
            $vl = $con->Listar("SELECT SUM((dm.cantidad *dm.valor)+COALESCE(ajuste, 0)-COALESCE(descuento, 0)), 
                SUM(dm.cantidad*dm.iva), SUM(dm.cantidad*dm.impoconsumo), GROUP_CONCAT(dm.id_unico) 
                FROM gf_detalle_movimiento dm 
                WHERE dm.movimiento = $idMov AND dm.planmovimiento IN(".$rowc[$c][4].")");
            $total = $vl[0][0]+$vl[0][1]+$vl[0][2];
            #Cuenta Debito
            if($rowc[$c][5]==1){
                $vd = $vl[0][0];
            } else {
                $vd = $vl[0][0]*-1;
            }
            if($vd!=0){
                $sql_cons ="INSERT INTO gf_detalle_comprobante 
                        ( fecha, comprobante,valor,
                        cuenta,naturaleza,tercero, centrocosto, proyecto) 
                VALUES (:fecha,  :comprobante,:valor, 
                        :cuenta,:naturaleza, :tercero, :centrocosto, :proyecto)";
                $sql_dato = array(
                        array(":fecha",$rowdc[0][1]),
                        array(":comprobante",$idcnt),
                        array(":valor",($vd)),
                        array(":cuenta",$rowc[$c][0]),   
                        array(":naturaleza",$rowc[$c][5]),
                        array(":tercero",$rowdc[0][4]),
                        array(":centrocosto",$rowdc[0][5]),
                        array(":proyecto",$proy)
                    
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
            }
            #Cuenta Iva
            if($rowc[$c][6]==1){
                $vIv = $vl[0][1];
            } else {
                $vIv = $vl[0][1]*-1;
            }
            if($vIv!=0){
                $sql_cons ="INSERT INTO gf_detalle_comprobante 
                        ( fecha, comprobante,valor,
                        cuenta,naturaleza,tercero, centrocosto, proyecto) 
                VALUES (:fecha,  :comprobante,:valor, 
                        :cuenta,:naturaleza, :tercero, :centrocosto, :proyecto)";
                $sql_dato = array(
                        array(":fecha",$rowdc[0][1]),
                        array(":comprobante",$idcnt),
                        array(":valor",($vIv)),
                        array(":cuenta",$rowc[$c][1]),   
                        array(":naturaleza",$rowc[$c][6]),
                        array(":tercero",$rowdc[0][4]),
                        array(":centrocosto",$rowdc[0][5]),
                        array(":proyecto",$proy)
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
            }
            #Cuenta Impo
            if($rowc[$c][7]==1){
                $vIm = $vl[0][2];
            } else {
                $vIm = $vl[0][2]*-1;
            }
            if($vIm!=0){
                $sql_cons ="INSERT INTO gf_detalle_comprobante 
                        ( fecha, comprobante,valor,
                        cuenta,naturaleza,tercero, centrocosto, proyecto) 
                VALUES (:fecha,  :comprobante,:valor, 
                        :cuenta,:naturaleza, :tercero, :centrocosto, :proyecto)";
                $sql_dato = array(
                        array(":fecha",$rowdc[0][1]),
                        array(":comprobante",$idcnt),
                        array(":valor",($vIm)),
                        array(":cuenta",$rowc[$c][2]),   
                        array(":naturaleza",$rowc[$c][7]),
                        array(":tercero",$rowdc[0][4]),
                        array(":centrocosto",$rowdc[0][5]),
                        array(":proyecto",$proy)
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
            }
            #Cuenta Credito
            if($rowc[$c][8]==1){
               $vC = $total*-1;
            } else {
               $vC = $total;
            }
            if($vC!=0){
                $sql_cons ="INSERT INTO gf_detalle_comprobante 
                        ( fecha, comprobante,valor,
                        cuenta,naturaleza,tercero, centrocosto, proyecto) 
                VALUES (:fecha,  :comprobante,:valor, 
                        :cuenta,:naturaleza, :tercero, :centrocosto, :proyecto)";
                $sql_dato = array(
                        array(":fecha",$rowdc[0][1]),
                        array(":comprobante",$idcnt),
                        array(":valor",($vC)),
                        array(":cuenta",$rowc[$c][3]),   
                        array(":naturaleza",$rowc[$c][8]),
                        array(":tercero",$rowdc[0][4]),
                        array(":centrocosto",$rowdc[0][5]),
                        array(":proyecto",$proy)
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
            }
            $rowdp = $con->Listar("SELECT id_unico FROM gf_detalle_comprobante WHERE comprobante =$idcnt AND cuenta = ".$rowc[$c][0]);
            $id_detallec = $rowdp[0][0];
            #Modificar Datos
            $ids = $vl[0][3];
            $updsql = "UPDATE gf_detalle_movimiento SET detalle_comprobante=$id_detallec WHERE id_unico IN (".$ids.")";
            $resultado = $mysqli->query($updsql);
            
            $dtguardados ++;
        }
            
        
        echo $dtguardados;
    break;

    #Verificar que los elementos este configurados para la interfaz
    case 12:
        $htmlI ='Elementos Sin Configuración :';
        $dtguardados =0;
        $idMov = $_REQUEST['idMov'];
        $rowe = $con->Listar("SELECT DISTINCT dm.planmovimiento, m.tipomovimiento, m.parametrizacionanno, 
            pi.codi, pi.nombre,pi.predecesor
            FROM gf_detalle_movimiento dm 
            LEFT JOIN gf_movimiento m ON dm.movimiento = m.id_unico 
            LEFT JOIN gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico 
            WHERE md5(dm.movimiento) ='$idMov'");
        for ($e = 0; $e < count($rowe); $e++) {
            $rowc = $con->Listar("SELECT * FROM gf_configuracion_almacen ca 
            WHERE ca.plan_inventario = ".$rowe[$e][5]." AND ca.tipo_movimiento = ".$rowe[$e][1]."
            AND ca.parametrizacion_anno = ".$rowe[$e][2]."");
            if(count($rowc)<=0){
                $htmlI .=$rowe[$e][3].' - '.$rowe[$e][4];
                $dtguardados ++;
            }
        }
        $datos = array("msj"=>$htmlI,"rta"=>$dtguardados); 
         $datos1= $htmlI.'%'.$dtguardados;

        echo $datos1;
    break;
    
    #Crear Interfaz
    case 13:        
        $dtguardados =0;
        $idMov = $_REQUEST['idMov'];
        #DATOS COMPROBANTE
        $rowdc = $con->Listar("SELECT m.numero, m.fecha, m.descripcion, tc.id_unico, m.tercero2, m.centrocosto 
            FROM gf_movimiento m 
            LEFT JOIN gf_tipo_comprobante tc ON m.tipomovimiento = tc.tipo_movimiento 
            WHERE md5(m.id_unico) ='$idMov'");
        #crear c
        $sql_cons ="INSERT INTO gf_comprobante_cnt 
                ( numero, fecha,descripcion, tipocomprobante,compania,
                parametrizacionanno,tercero, usuario, fecha_elaboracion ) 
        VALUES (:numero, :fecha, :descripcion,  :tipocomprobante,:compania, 
                :parametrizacion_anno, :tercero, :usuario,:fecha_elaboracion)";
        $sql_dato = array(
                array(":numero",$rowdc[0][0]),
                array(":fecha",$rowdc[0][1]),
                array(":descripcion",$rowdc[0][2]),
                array(":tipocomprobante",$rowdc[0][3]),
                array(":compania",$compania),   
                array(":parametrizacion_anno",$panno),
                array(":tercero",$rowdc[0][4]),
                array(":usuario",$usuario),
                array(":fecha_elaboracion",$fechaa),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);

        #************* Insertar Detalles CNT *************#
        $cn = $con->Listar("SELECT * FROM gf_comprobante_cnt WHERE numero = '".$rowdc[0][0]."' AND tipocomprobante = ".$rowdc[0][3]." AND parametrizacionanno =$panno");
        $idcnt=$cn[0][0];
        
        $rowe = $con->Listar("SELECT DISTINCT GROUP_CONCAT(dm.planmovimiento), m.tipomovimiento, m.parametrizacionanno, 
            pi.codi, pi.nombre
            FROM gf_detalle_movimiento dm 
            LEFT JOIN gf_movimiento m ON dm.movimiento = m.id_unico 
            LEFT JOIN gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico 
            WHERE md5(dm.movimiento) ='$idMov'");        
        $rowc = $con->Listar("SELECT  ca.cuenta_debito,ca.cuenta_credito,ca.cuenta_iva,ca.cuenta_impo,
               GROUP_CONCAT(dm.planmovimiento), cd.naturaleza, cc.naturaleza, ci.naturaleza, cim.naturaleza
                  FROM gf_detalle_movimiento dm
                  LEFT JOIN gf_movimiento m ON dm.movimiento = m.id_unico
                  LEFT JOIN gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico
                  LEFT JOIN gf_configuracion_almacen ca ON ca.plan_inventario=pi.predecesor
                  AND ca.tipo_movimiento=m.tipomovimiento
                  AND ca.parametrizacion_anno=m.parametrizacionanno
                  LEFT JOIN gf_cuenta cd  ON ca.cuenta_debito = cd.id_unico
                  LEFT JOIN gf_cuenta cc  ON ca.cuenta_credito = cc.id_unico
                  LEFT JOIN gf_cuenta ci  ON ca.cuenta_iva = ci.id_unico
                  LEFT JOIN gf_cuenta cim ON ca.cuenta_impo = cim.id_unico
                  WHERE md5(dm.movimiento) ='$idMov'");
         
        for ($c= 0; $c < count($rowc); $c++) {
            $total = 0;
            $vl = $con->Listar("SELECT SUM((dm.cantidad *dm.valor)+COALESCE(ajuste, 0)-COALESCE(descuento, 0)), 
                SUM(dm.cantidad*dm.iva), SUM(dm.cantidad*dm.impoconsumo), GROUP_CONCAT(dm.id_unico) 
                FROM gf_detalle_movimiento dm 
                WHERE md5(dm.movimiento) = '$idMov' AND dm.planmovimiento IN(".$rowc[$c][4].")");
            $total = $vl[0][0]+$vl[0][1]+$vl[0][2];
            #Cuenta Debito
            if($rowc[$c][5]==1){
                $vd = $vl[0][0];
            } else {
                $vd = $vl[0][0]*-1;
            }
            if($vd!=0){
                $sql_cons ="INSERT INTO gf_detalle_comprobante 
                        ( fecha, comprobante,valor,
                        cuenta,naturaleza,tercero, centrocosto, proyecto) 
                VALUES (:fecha,  :comprobante,:valor, 
                        :cuenta,:naturaleza, :tercero, :centrocosto, :proyecto)";
                $sql_dato = array(
                        array(":fecha",$rowdc[0][1]),
                        array(":comprobante",$idcnt),
                        array(":valor",($vd)),
                        array(":cuenta",$rowc[$c][0]),   
                        array(":naturaleza",$rowc[$c][5]),
                        array(":tercero",$rowdc[0][4]),
                        array(":centrocosto",$rowdc[0][5]),
                        array(":proyecto",$proy)
                    
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
            }
            #Cuenta Iva
            if($rowc[$c][6]==1){
                $vIv = $vl[0][1];
            } else {
                $vIv = $vl[0][1]*-1;
            }
            if($vIv!=0){
                $sql_cons ="INSERT INTO gf_detalle_comprobante 
                        ( fecha, comprobante,valor,
                        cuenta,naturaleza,tercero, centrocosto, proyecto) 
                VALUES (:fecha,  :comprobante,:valor, 
                        :cuenta,:naturaleza, :tercero, :centrocosto, :proyecto)";
                $sql_dato = array(
                        array(":fecha",$rowdc[0][1]),
                        array(":comprobante",$idcnt),
                        array(":valor",($vIv)),
                        array(":cuenta",$rowc[$c][1]),   
                        array(":naturaleza",$rowc[$c][6]),
                        array(":tercero",$rowdc[0][4]),
                        array(":centrocosto",$rowdc[0][5]),
                        array(":proyecto",$proy)
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
            }
            #Cuenta Impo
            if($rowc[$c][7]==1){
                $vIm = $vl[0][2];
            } else {
                $vIm = $vl[0][2]*-1;
            }
            if($vIm!=0){
                $sql_cons ="INSERT INTO gf_detalle_comprobante 
                        ( fecha, comprobante,valor,
                        cuenta,naturaleza,tercero, centrocosto, proyecto) 
                VALUES (:fecha,  :comprobante,:valor, 
                        :cuenta,:naturaleza, :tercero, :centrocosto, :proyecto)";
                $sql_dato = array(
                        array(":fecha",$rowdc[0][1]),
                        array(":comprobante",$idcnt),
                        array(":valor",($vIm)),
                        array(":cuenta",$rowc[$c][2]),   
                        array(":naturaleza",$rowc[$c][7]),
                        array(":tercero",$rowdc[0][4]),
                        array(":centrocosto",$rowdc[0][5]),
                        array(":proyecto",$proy)
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
            }
            #Cuenta Credito
            if($rowc[$c][8]==1){
               $vC = $total*-1;
            } else {
               $vC = $total;
            }
            if($vC!=0){
                $sql_cons ="INSERT INTO gf_detalle_comprobante 
                        ( fecha, comprobante,valor,
                        cuenta,naturaleza,tercero, centrocosto, proyecto) 
                VALUES (:fecha,  :comprobante,:valor, 
                        :cuenta,:naturaleza, :tercero, :centrocosto, :proyecto)";
                $sql_dato = array(
                        array(":fecha",$rowdc[0][1]),
                        array(":comprobante",$idcnt),
                        array(":valor",($vC)),
                        array(":cuenta",$rowc[$c][3]),   
                        array(":naturaleza",$rowc[$c][8]),
                        array(":tercero",$rowdc[0][4]),
                        array(":centrocosto",$rowdc[0][5]),
                        array(":proyecto",$proy)
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
            }
            $rowdp = $con->Listar("SELECT id_unico FROM gf_detalle_comprobante WHERE comprobante =$idcnt AND cuenta = ".$rowc[$c][0]);
            $id_detallec = $rowdp[0][0];
            #Modificar Datos
            $ids = $vl[0][3];
            $updsql = "UPDATE gf_detalle_movimiento SET detalle_comprobante=$id_detallec WHERE id_unico IN (".$ids.")";
            $resultado = $mysqli->query($updsql);
            
            $dtguardados ++;
        }
            
        
        echo $dtguardados;
    break;

    case 14:
        $id_m = $_REQUEST['id'];
        $row = $con->Listar("SELECT DISTINCT dc.comprobante FROM gf_detalle_movimiento dm 
        LEFT JOIN gf_detalle_comprobante dc ON dm.detalle_comprobante = dc.id_unico 
        WHERE md5(dm.movimiento) = '$id_m' AND dm.detalle_comprobante IS NOT NULL");
        if(count($row)>0){
            $e=trim($row[0][0]);
        } else {
            $e=0;
        }
        echo $e;
    break;
}


function crearEspecificaciones($producto,$marca,$serial,$color,$modelo,$estado,$procedencia,$doc_soporte,$n_doc_sop, $altura, $referencia,$generacion, $especif){
    global $con;
    global $placa;
    
    #***Marca
    $sql_cons ="INSERT INTO gf_producto_especificacion 
            ( valor, producto, 
            fichainventario) 
    VALUES (:valor, :producto, 
            :fichainventario)";
    $sql_dato = array(
        array(":valor",$marca),
        array(":producto",$producto),
        array(":fichainventario",2),
    );
    $resp = $con->InAcEl($sql_cons,$sql_dato);
    #***serial
    $sql_cons ="INSERT INTO gf_producto_especificacion 
            ( valor, producto, 
            fichainventario) 
    VALUES (:valor, :producto, 
            :fichainventario)";
    $sql_dato = array(
        array(":valor",$serial),
        array(":producto",$producto),
        array(":fichainventario",5),
    );
    $resp = $con->InAcEl($sql_cons,$sql_dato);
    #***Color
    $sql_cons ="INSERT INTO gf_producto_especificacion 
            ( valor, producto, 
            fichainventario) 
    VALUES (:valor, :producto, 
            :fichainventario)";
    $sql_dato = array(
        array(":valor",$color),
        array(":producto",$producto),
        array(":fichainventario",9),
    );
    $resp = $con->InAcEl($sql_cons,$sql_dato);
    
    #***Modelo
    $sql_cons ="INSERT INTO gf_producto_especificacion 
            ( valor, producto, 
            fichainventario) 
    VALUES (:valor, :producto, 
            :fichainventario)";
    $sql_dato = array(
        array(":valor",$modelo),
        array(":producto",$producto),
        array(":fichainventario",13),
    );
    $resp = $con->InAcEl($sql_cons,$sql_dato);
    #***Estado
    $sql_cons ="INSERT INTO gf_producto_especificacion 
            ( valor, producto, 
            fichainventario) 
    VALUES (:valor, :producto, 
            :fichainventario)";
    $sql_dato = array(
        array(":valor",$estado),
        array(":producto",$producto),
        array(":fichainventario",14),
    );
    $resp = $con->InAcEl($sql_cons,$sql_dato);
    #***Procedencia
    $sql_cons ="INSERT INTO gf_producto_especificacion 
            ( valor, producto, 
            fichainventario) 
    VALUES (:valor, :producto, 
            :fichainventario)";
    $sql_dato = array(
        array(":valor",$procedencia),
        array(":producto",$producto),
        array(":fichainventario",15),
    );
    $resp = $con->InAcEl($sql_cons,$sql_dato);
    #***Doc_Soporte
    $sql_cons ="INSERT INTO gf_producto_especificacion 
            ( valor, producto, 
            fichainventario) 
    VALUES (:valor, :producto, 
            :fichainventario)";
    $sql_dato = array(
        array(":valor",$doc_soporte),
        array(":producto",$producto),
        array(":fichainventario",16),
    );
    $resp = $con->InAcEl($sql_cons,$sql_dato);
    #***Numero_Doc
    $sql_cons ="INSERT INTO gf_producto_especificacion 
            ( valor, producto, 
            fichainventario) 
    VALUES (:valor, :producto, 
            :fichainventario)";
    $sql_dato = array(
        array(":valor",$n_doc_sop),
        array(":producto",$producto),
        array(":fichainventario",17),
    );
    $resp = $con->InAcEl($sql_cons,$sql_dato);
    
    #***Altura
    $sql_cons ="INSERT INTO gf_producto_especificacion 
            ( valor, producto, 
            fichainventario) 
    VALUES (:valor, :producto, 
            :fichainventario)";
    $sql_dato = array(
        array(":valor",$altura),
        array(":producto",$producto),
        array(":fichainventario",1),
    );
    $resp = $con->InAcEl($sql_cons,$sql_dato);
    
    #***Referencia
    $sql_cons ="INSERT INTO gf_producto_especificacion 
            ( valor, producto, 
            fichainventario) 
    VALUES (:valor, :producto, 
            :fichainventario)";
    $sql_dato = array(
        array(":valor",$referencia),
        array(":producto",$producto),
        array(":fichainventario",3),
    );
    $resp = $con->InAcEl($sql_cons,$sql_dato);
    
    #***Generacion
    $sql_cons ="INSERT INTO gf_producto_especificacion 
            ( valor, producto, 
            fichainventario) 
    VALUES (:valor, :producto, 
            :fichainventario)";
    $sql_dato = array(
        array(":valor",$generacion),
        array(":producto",$producto),
        array(":fichainventario",4),
    );
    $resp = $con->InAcEl($sql_cons,$sql_dato);
    
    #***Especificaciones
    $sql_cons ="INSERT INTO gf_producto_especificacion 
            ( valor, producto, 
            fichainventario) 
    VALUES (:valor, :producto, 
            :fichainventario)";
    $sql_dato = array(
        array(":valor",$especif),
        array(":producto",$producto),
        array(":fichainventario",7),
    );
    $resp = $con->InAcEl($sql_cons,$sql_dato);
    
    
    #***Placa
    $sql_cons ="INSERT INTO gf_producto_especificacion 
            ( valor, producto, 
            fichainventario) 
    VALUES (:valor, :producto, 
            :fichainventario)";
    $sql_dato = array(
        array(":valor",$placa),
        array(":producto",$producto),
        array(":fichainventario",6),
    );
    $resp = $con->InAcEl($sql_cons,$sql_dato);
    $placa++; 
    
}