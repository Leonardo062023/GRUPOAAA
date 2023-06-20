<?php
require '../Conexion/ConexionPDO.php';
require '../Conexion/conexion.php';
require '../jsonNomina/funcionesNomina.php';
@session_start();
setlocale(LC_ALL,"es_ES");
date_default_timezone_set("America/Bogota");
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];
$panno      = $_SESSION['anno'];
$usuario_t  = $_SESSION['usuario_tercero'];

$empleador  = $_REQUEST['sltEmpleado'];  
$periodo    = $_REQUEST['sltPeriodo'];  

$rowp        = $con->Listar("SELECT p.fechainicio, p.fechafin, p.dias_nomina, pa.anno, month(p.fechainicio)  FROM  gn_periodo p 
    LEFT JOIN gf_parametrizacion_anno pa oN p.parametrizacionanno = pa.id_unico 
 WHERE p.id_unico = $periodo");

$fechaInicio = $rowp[0][0];
$fechaFin    = $rowp[0][1];        
$diasPeriodo = $rowp[0][2];       
$fechaInicial =  $rowp[0][3].'-01-01';  
$mes = intval($rowp[0][4]);

if($empleador ==2){
    $rowe = $con->Listar("SELECT DISTINCT e.id_unico, 
           tc.categoria, 
           c.salarioactual,
           (SELECT MAX(vr.fecha) FROM gn_vinculacion_retiro vr WHERE vr.empleado = e.id_unico and vr.estado = 1 limit 1 ) ulmv, 
           (SELECT vr2.estado FROM gn_vinculacion_retiro vr2 WHERE vr2.empleado = e.id_unico AND vr2.fechaacto = (SELECT MAX(vr.fechaacto) FROM gn_vinculacion_retiro vr WHERE vr.empleado = e.id_unico) LIMIT 1) ulmve,
           CONCAT_WS(' ',t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos ), 
           (SELECT et.tipo FROM  gn_empleado_tipo et 
            LEFT JOIN  gn_tipo_empleado te ON  et.tipo = te.id_unico 
           WHERE et.empleado =e.id_unico  ORDER BY et.fechainicio DESC LIMIT 1 ) AS tipo, 
            (SELECT  te.porcentaje_retroactivo FROM  gn_empleado_tipo et 
            LEFT JOIN  gn_tipo_empleado te ON  et.tipo = te.id_unico 
           WHERE et.empleado =e.id_unico ORDER BY et.fechainicio DESC LIMIT 1 ) AS porcentaje_retroactivo,            
           c.salarioactual , cr.valor 
        FROM gn_empleado e 
        LEFT JOIN gf_tercero t on e.tercero = t.id_unico
        LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
        LEFT JOIN gn_categoria c ON c.id_unico = tc.categoria
        LEFT JOIN gn_grupo_gestion gg oN e.grupogestion = gg.id_unico         
        LEFT JOIN gn_categoria_riesgos cr ON e.tipo_riesgo = cr.id_unico 
        WHERE e.id_unico != 2 AND e.cesantias = 2
        AND (((SELECT vr.estado FROM gn_vinculacion_retiro vr where vr.empleado = e.id_unico AND vr.fechaacto<= '$fechaFin'  ORDER BY vr.fechaacto DESC LIMIT 1)=1) or  ((SELECT vr.estado FROM gn_vinculacion_retiro vr where vr.empleado = e.id_unico AND vr.fechaacto BETWEEN '$fechaInicio' AND '$fechaFin' ORDER BY vr.fechaacto DESC LIMIT 1)=2 ))
        ORDER BY e.id_unico"); 
} else {
    $rowe = $con->Listar("SELECT DISTINCT e.id_unico, 
           tc.categoria, 
           c.salarioactual,
           (SELECT MAX(vr.fecha) FROM gn_vinculacion_retiro vr WHERE vr.empleado = e.id_unico and vr.estado = 1 limit 1 ) ulmv, 
           (SELECT vr2.estado FROM gn_vinculacion_retiro vr2 WHERE vr2.empleado = e.id_unico AND vr2.fechaacto = (SELECT MAX(vr.fechaacto) FROM gn_vinculacion_retiro vr WHERE vr.empleado = e.id_unico) LIMIT 1) ulmve,
           CONCAT_WS(' ',t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos ), 
           (SELECT et.tipo FROM  gn_empleado_tipo et 
            LEFT JOIN  gn_tipo_empleado te ON  et.tipo = te.id_unico 
           WHERE et.empleado =e.id_unico  ORDER BY et.fechainicio DESC LIMIT 1 ) AS tipo, 
            (SELECT  te.porcentaje_retroactivo FROM  gn_empleado_tipo et 
            LEFT JOIN  gn_tipo_empleado te ON  et.tipo = te.id_unico 
           WHERE et.empleado =e.id_unico ORDER BY et.fechainicio DESC LIMIT 1 ) AS porcentaje_retroactivo,            
           c.salarioactual , cr.valor  
        FROM gn_empleado e 
        LEFT JOIN gf_tercero t on e.tercero = t.id_unico
        LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
        LEFT JOIN gn_categoria c ON c.id_unico = tc.categoria
        LEFT JOIN gn_grupo_gestion gg oN e.grupogestion = gg.id_unico         
        LEFT JOIN gn_categoria_riesgos cr ON e.tipo_riesgo = cr.id_unico
        WHERE e.id_unico = $empleador  AND e.cesantias = 2
        AND (((SELECT vr.estado FROM gn_vinculacion_retiro vr where vr.empleado = e.id_unico AND vr.fechaacto<= '$fechaFin'  ORDER BY vr.fechaacto DESC LIMIT 1)=1) or  ((SELECT vr.estado FROM gn_vinculacion_retiro vr where vr.empleado = e.id_unico AND vr.fechaacto BETWEEN '$fechaInicio' AND '$fechaFin' ORDER BY vr.fechaacto DESC LIMIT 1)=2 )) 
        ORDER BY e.id_unico"); 
}

$rta =0;
for ($i=0; $i < count($rowe); $i++) {
    $salario = sueldo($empleado);

    $empleado = $rowe[$i][0];
    $rowP = $con->Listar("SELECT id_unico, vigencia, salmin, tope_aux_transporte, auxt, talimentacion, primaA, tipo_liquidaciond, dias_primav 
        FROM gn_parametros_liquidacion WHERE vigencia = $panno AND tipo_empleado=".$rowe[0][6]);

    $pid        = $rowP[0][0]; 
    $pvi        = $rowP[0][1]; 
    $psm        = $rowP[0][2]; 
    $ptat       = $rowP[0][3]; 
    $pat        = $rowP[0][4];
    $ptaa       = $rowP[0][5];
    $paa        = $rowP[0][6];


    #* Eliminar COnceptos 
    $ld = "DELETE  n.* FROM gn_novedad n 
        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
        WHERE n.periodo = $periodo AND n.empleado = $empleado AND n.aplicabilidad in(1,2,3) ";
    $resultado1 = $mysqli->query($ld);


    #Fecha Ingreso  - Salida 
    $rowing = $con->Listar("SELECT fechaacto, DATE_FORMAT( fechaacto, '%d'), DATE_FORMAT( fechaacto, '%m'), DATE_FORMAT( fechaacto, '%Y')  FROM gn_vinculacion_retiro where empleado = $empleado  AND fechaacto <='$fechaFin' and estado = 1 
        ORDER BY fechaacto  DESC LIMIT 1");
    $fechaIngreso = $rowing[0][0];

    $rowsal = $con->Listar("SELECT fechaacto, DATE_FORMAT( fechaacto, '%d'), DATE_FORMAT( fechaacto, '%m'), DATE_FORMAT( fechaacto, '%Y')  FROM gn_vinculacion_retiro where empleado = $empleado  AND fechaacto <='$fechaFin' and estado = 2 
        ORDER BY fechaacto  DESC LIMIT 1");
    if(empty($rowsal[0][0])){
        $fechaSalida  = $fechaFin;
    } else {
        $fechaSalida  = $rowsal[0][0];    
    }


    $fing = new DateTime($fechaIngreso);
    $fsal = new DateTime($fechaSalida);


    #Sueldo
    guardarActual($empleado,'001', $periodo );

    #Auxilio Transporte
    guardarActual($empleado,'953', $periodo );
    #* Auxilio Alimentación
    guardarActual($empleado,'1005', $periodo );



    #Prima Navidad
    $id_cpn     =  id_concepto('158');
    $valorpn    = ultimoValor($empleado,$id_cpn,$periodo, $fechaIngreso);
    $id_cpnf    =  id_concepto('1011');
    if($valorpn!=0){
        guardarNovedad($valorpn, $empleado, $periodo, $id_cpnf);    
    }

    #Prima Vacaciones
    $id_cpv     =  id_concepto('175');
    $valorpv    = doceavaAcumulado($empleado,$id_cpv,$periodo, $fechaIngreso);
    $id_cpvf    =  id_concepto('804');
    if($valorpv!=0){
        guardarNovedad($valorpv, $empleado, $periodo, $id_cpvf);    
    }


    #Prima Semestral
    $id_cps     =  id_concepto('160');
    $valorps    = doceavaAcumulado($empleado,$id_cps,$periodo, $fechaIngreso);
    $id_cpsf    =  id_concepto('1002');
    if($valorps!=0){
        guardarNovedad($valorps, $empleado, $periodo, $id_cpsf);    
    }

    #Prima Antiguedad
    $id_cpa     =  id_concepto('150');
    $valorpa    =  ultimoValor($empleado,$id_cpa,$periodo, $fechaIngreso);
    $id_cpaf    =  id_concepto('806');
    if($valorpa!=0){
        guardarNovedad($valorpa, $empleado, $periodo, $id_cpaf);    
    }

    #Horas Extras
    $valorhe    = doceavaHE($empleado,$periodo, $fechaIngreso);
    $id_chef    =  id_concepto('1008');
    if($valorhe!=0){
        guardarNovedad($valorhe, $empleado, $periodo, $id_chef);    
    }

    #Prima Bonificacin por servicios prestados
    $id_cbs     =  id_concepto('161');
    $valorbs    = doceavaAcumulado($empleado,$id_cbs,$periodo, $fechaIngreso);
    $id_cbsf    =  id_concepto('956');
    if($valorbs!=0){
        guardarNovedad($valorbs, $empleado, $periodo, $id_cbsf);    
    }



    #******CESANTIAS******#

    #C14
    $id_c169    =  id_concepto('169');
    $v169       =  valorConceptoFechasP($empleado, $fechaIngreso, $fechaSalida, $id_c169, $periodo);
    $id_cc14    =  id_concepto('C14');
    if($v169!=0){
        guardarNovedad($v169, $empleado, $periodo, $id_cc14);    
    }


    #Ultimo periodo cesantias
    $fechar = new DateTime($fechaSalida);
    $diasc  = diasPendientes(0, $empleado, $fechaIngreso, $fechar, $fechaSalida);
    $id_dcn =  id_concepto('1030');
    guardarNovedad($diasc, $empleado, $periodo, $id_dcn);    
     
    #Base Cesantías
    $salario = sueldo($empleado);
    $auxt    = valorConceptoPeriodo($empleado,$periodo, '953');
    $palim   = valorConceptoPeriodo($empleado,$periodo, '1005');

    #Salario + Prima de Navidad1011 +Prima de vacaciones804 + Prima Semestral1002 + Bonificación Por servicios prestados956 +  Prima Antiguedad806 + Horas Extra1008 Auxilio Transporte + Auxilio Alimentación 
    $valorbc = $salario+ $valorpn + $valorpv +$valorps +$valorbs +$valorpa +$valorhe + $auxt +$palim;
    $id_bcn  =  id_concepto('850');
    guardarNovedad($valorbc, $empleado, $periodo, $id_bcn);    

    #Cesantias Retroactivas 
    $vcenr      = ROUND(($valorbc * $diasc)/360);
    $id_ccn     = id_concepto('189');
    guardarNovedad($vcenr, $empleado, $periodo, $id_ccn);


    #c15
    $v169p      = valorConceptoPeriodo($empleado,$periodo, '169');
    $vc15       = $vcenr - $v169 -$v169p;
    $id_cc15    = id_concepto('C15');
    guardarNovedad($vc15, $empleado, $periodo, $id_cc15);

    #171
    $v171       = ROUND(($v169p*1*$mes)/100);
    $id_c171    = id_concepto('171');
    guardarNovedad($v171, $empleado, $periodo, $id_c171);
    
    /*#RETENCION FTE
    retencion($empleado,$periodo);
     */

    #Devengos
    $dvg = $con->Listar("SELECT SUM(n.valor) FROM gn_novedad n 
        LEFT JOIN gn_concepto c oN n.concepto = c.id_unico 
        WHERE n.periodo = $periodo
        AND n.empleado = $empleado 
        AND c.clase = 1 and c.unidadmedida = 1");

    if(empty($dvg[0][0])){
        $tdv = 0;
    } else {
        $tdv = $dvg[0][0];   
    }
    $id_conceptotd = id_concepto('097');
    guardarNovedad($tdv , $empleado, $periodo, $id_conceptotd);

    #Descuentos
    $dv = $con->Listar("SELECT SUM(n.valor) FROM gn_novedad n 
        LEFT JOIN gn_concepto c oN n.concepto = c.id_unico 
        WHERE n.periodo = $periodo
        AND n.empleado = $empleado 
        AND c.clase = 2 and c.unidadmedida = 1");
    if(empty($dv[0][0])){
        $tds = 0;
    } else {
        $tds = $dv[0][0];
    }
    $id_conceptods = id_concepto('140');
    guardarNovedad($tds , $empleado, $periodo, $id_conceptods);


    #Neto
    $np = $tdv -$tds;
    $id_conceptonp = id_concepto('144');
    $ge = guardarNovedad($np , $empleado, $periodo, $id_conceptonp);

    $rta =0;
    if(empty($ge)){
        $rta +=1;
    }   

}

if($_REQUEST['t']==1) { 
?>
    <html>
    <head>
     <meta charset="utf-8">
     <meta name="viewport" content="width=device-width, initial-scale=1">
     <link rel="stylesheet" href="../css/bootstrap.min.css">
     <link rel="stylesheet" href="../css/style.css">
     <script src="../js/md5.pack.js"></script>
     <script src="../js/jquery.min.js"></script>
     <link rel="stylesheet" href="../css/jquery-ui.css" type="text/css" media="screen" title="default" />
     <script type="text/javascript" language="javascript" src="../js/jquery-1.10.2.js"></script>
    </head>
    <body>
    </body>
    </html>

    <div class="modal fade mdl-info" id="mdlInfo" role="dialog" align="center" >
        <div class="modal-dialog">
          <div class="modal-content">
            <div id="forma-modal" class="modal-header">
              <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <label id="mensaje" name="mensaje"></label>
            </div>
            <div id="forma-modal" class="modal-footer">
              <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
          </div>
        </div>
      </div>
      <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
      <script src="../js/bootstrap.min.js"></script>
    <script>  
    $(document).ready(function() {
        let response = <?php echo $rta; ?>;
        if(response>=1){
            $("#mensaje").html('Información Guardada Correctamente');  
            $("#mdlInfo").modal('show'); 
            $("#ver1").click(function(){
                document.location ='../liquidar_GN_LIQUIDACION_CESANTIASR.php';
               //document.location ='../informes_nomina/generar_INF_SABANA_LIQUIDACIONF.php?t=1&sltPeriodo=<?=$periodo?>';
            })
            
        } else {
            $("#mensaje").html('No Se Ha Podido Guardar La Información');  
            $("#mdlInfo").modal('show'); 
            document.location ='../liquidar_GN_LIQUIDACION_CESANTIASR.php';
        }
    })
    </script>

<?php } else {
    echo $rta;
}?>