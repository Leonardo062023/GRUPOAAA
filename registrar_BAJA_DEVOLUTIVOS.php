<?php
require_once ('head.php');
require_once ('./modelAlmacen/bajaElementos.php');
$baja = new baja();
include ('Conexion/ConexionPDO.php');
require_once('jsonSistema/funcionCierre.php');
$con        = new ConexionPDO();


list($compania, $param, $dep, $objB, $res_dep) = array($_SESSION['compania'], $_SESSION['anno'], "", "", "");

list($depnd, $respo, $tipoB, $numero, $fechaB, $id) = array("", "", "", "", "", 0);
if(!empty($_GET['sltDep'])){
    $dep = $_GET['sltDep'];
}

if(!empty($_GET['sltResponsable'])){
    $res_dep = $_GET['sltResponsable'];
}
$cierre = 0;
if(!empty($_GET['baja'])){
    $objB = $_GET['baja'];
    $data = $baja->obtnerBaja($_GET['baja']);
    $numero = $data['numero'];
    $fechaB = $data['fecha'];
    $id     = $data['id'];
    $tipoB  = strtoupper($data['sigla'])." ".ucwords(mb_strtolower($data['tpnom']));
    $depnd  = strtoupper($data['cdep'])." ".ucwords(mb_strtolower($data['depnom']));
    $respo  = ucwords(mb_strtolower($data['ternom']." ".$data['doc']));
    $descripcion=$data['descripcion'];
    $cierre = cierreFecha($data['fechac']);
}

?>
    <script src="js/jquery-ui.js"></script>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <script type="text/javascript" src="js/md5.js" ></script>
    <title>Baja de Almacén</title>
    <style>
        .btn{
            box-shadow: 1px 1px 1px 1px gray;
            color:#fff;
            border-color:#1075C1;
        }

        .contBM {
            float: right;
            overflow:visible;
        }

        #contButton {
            margin-top: 5px;
        }

        table.dataTable thead th,
        table.dataTable thead td{
            padding: 1px 18px;
        }

        table.dataTable tbody td,
        table.dataTable tbody td{
            padding: 1px 0px 0px 0px;
        }

        .dataTables_wrapper .ui-toolbar{
            padding: 2px 0px;
        }

        .client-form input[type="text"]{
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row content">
            <?php require_once ('menu.php'); ?>
            <div id="contForm" class="col-sm-10 col-lg-10 text-left"> 
                <h2 style="margin-top:  0px; text-align: center;" class="tituloform">Baja de Almacén</h2>
                <div class="client-form contenedorForma">
                    <form name="form" id="form" class="form-horizontal" method="GET"  enctype="multipart/form-data" action="registrar_BAJA_DEVOLUTIVOS.php">
                        <p align="center" class="parrafoO" style="margin-bottom: 5px">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                         <div class="form-group" style=" margin-left: -10px">
                            <input type="hidden" name="txtIdMov" id="txtIdMov" value="<?php echo $id;?>">
                            <label for="sltDep" class="col-sm-1 col-md-1 col-lg-1 control-label"><strong class="obligado">*</strong>Dependencias:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2" style=" margin-left: 5px">
                                <select name="sltDep" id="sltDep" class="form-control select2" required="">
                                    <?php
                                    $html = "";
                                    if(empty($depnd)){
                                        if(empty($dep)){
                                            $html .= "<option value=''>Dependencia</option>";
                                            $datD = $baja->obtnerDep();
                                            foreach ($datD as $rowD) {
                                                $html .= "<option value=\"".md5($rowD[0])."\">$rowD[1] ".ucwords(mb_strtolower($rowD[2]))."</option>";
                                            }
                                        }else{
                                            $datD = $baja->obtnerDepId($dep);
                                            $html .= "<option value='".md5($datD[0])."'>$datD[1] ".ucwords(mb_strtolower($datD[2]))."</option>";
                                            $dtD = $baja->obtnerDepDif($dep);
                                            foreach ($dtD as $rowD) {
                                                $html .= "<option value=\"".md5($rowD[0])."\">$rowD[1] ".ucwords(mb_strtolower($rowD[2]))."</option>";
                                            }
                                        }
                                    }else{
                                        $html .= "<option value=''>$depnd</option>";
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                            <label for="sltResponsable" class="col-sm-1 col-md-1 col-lg-1 control-label" style="margin-left: -10px;"><strong class="obligado">*</strong>Responsable:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <select name="sltResponsable" id="sltResponsable" class="form-control select2" required="">
                                    <?php
                                    $html = "";
                                    if(!empty($respo)){
                                        $html .= "<option value=''>$respo</option>";
                                    }else{
                                        if(!empty($res_dep)){
                                            $ter = $baja->obnterResDep($res_dep);
                                            $html .= "<option>".ucwords(mb_strtolower("$ter[0] $ter[1]"))."</option>";
                                        }else{
                                            $html .= "<option value=''>Responsable</option>";
                                        }
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                            <label for="sltTipoT" class="control-label col-sm-1 col-md-1 col-lg-1 text-right "style="margin-left: -10px;"><strong class="obligado">*</strong>Tipo Baja:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <select name="sltTipoT" id="sltTipoT" class="form-control select2 text-left" title="Seleccione movimiento de traslado">
                                    <?php
                                    $html = "";
                                    if(empty($tipoB)){
                                        $html .=  "<option value=\"\">Tipo Baja</option>";
                                        $dataT = $baja->tiposBaja($compania);
                                        foreach ($dataT as $rowT) {
                                            $html .=  "<option value=\"".$rowT[0]."\">".$rowT[1]." ".ucwords(mb_strtolower($rowT[2]))."</option>";
                                        }
                                    }else{
                                        $html .= "<option value=''>$tipoB</option>";
                                    }
                                    echo $html;
                                    ?>
                                </select>
                            </div>
                            <label for="txtNumero" class="col-sm-1 col-md-1 col-lg-1 control-label" style="margin-left: -10px;"><strong class="obligado">*</strong>Número:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <input id="txtNumero" name="txtNumero" class="form-control" type="text" placeholder="Número" title="Número de movimiento" value="<?php echo $numero ?>" readonly="">
                            </div>
                        </div>
                        <div class="form-group"style=" margin-left: -10px">
                            
                            <label for="txtFecha" class="col-sm-1 col-md-1 col-lg-1 control-label" style=" margin-left: 5px"><strong class="obligado">*</strong>Fecha:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <input id="txtFecha" name="txtFecha" class="form-control" type="text" placeholder="Fecha" title="Fecha de movimiento" value="<?php echo $fechaB ?>" readonly="">
                            </div>
                            <label for="txtDescripcion" class="col-sm-1 col-md-1 col-lg-1 control-label" style="margin-left: -10px;"><strong class="obligado"></strong>Descripción:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <textarea id="txtDescripcion" name="txtDescripcion" class="form-control" type="text" placeholder="Descripción" title="Descripción" style="margin-top: 0px;width: 100%;height: 34px"><?php echo $descripcion ?></textarea> 
                            </div>
                            <label for="sltBuscar" class="col-sm-1 col-md-1 col-lg-1 control-label" style=" margin-left: -10px">Buscar:</label>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <select name="sltBuscar" id="sltBuscar" class="form-control select2">
                                    <option value="">Buscar</option>
                                </select>
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <button type="button" class="btn btn-primary glyphicon glyphicon-plus shadow  nuevo" id="btn-nuevo" onclick="nuevo()"></button>
                                <button type="button" class="btn btn-primary glyphicon glyphicon-print shadow imprimir" id="btn-imprimir"></button>
                                <?php if(!empty($_GET['baja'])) { ?>
                                    <a id="btnDocumento" title="Subir Documento" class="btn btn-primary shadow glyphicon glyphicon-cloud-upload"></a>
                                    <div id="response"></div>
                                    <script>
                                        $("#btnDocumento").click(function(){
                                            id =$("#txtIdMov").val(); 
                                            var form_data = {
                                            id: id,
                                            valor: 0, 
                                            almacen : 1
                                            }
                                            $.ajax({
                                                type: 'POST',
                                                url: "registrar_GF_DETALLE_COMPROBANTE_MOVIMIENTO_3.php",
                                                data: form_data,
                                                success: function (data) {
                                                    $('#response').html(data);
                                                    $(".movi1").modal("show");
                                                }
                                            })
                                        })
                                    </script>
                                    <?php } ?>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: -110px">
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-sm-10 col-md-10 col-lg-10" style="margin-top: 5px;">
                <div class="form-group contBM">
                    <a id="btnST" href="javascript:void(0)" class="btn btn-primary shadow glyphicon glyphicon-ok" title="Marcar Todos" onclick="checked_all()"></a>
                    <a id="btnSN" href="javascript:void(0)" class="btn btn-primary shadow glyphicon glyphicon-remove" title="Desmarcar Todos" onclick="not_checked_all()"></a>
                </div>
            </div>
            <div class="col-sm-10 col-md-10 col-lg-10" id="contTable">
                <div class="table-responsive">
                    <table id="tableO" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <td class="cabeza"><strong>Elemento</strong></td>
                                <td class="cabeza"><strong>Serie</strong></td>
                                <td class="cabeza"><strong>Descripción</strong></td>
                                <td class="cabeza"><strong>Valor</strong></td>
                                <td class="cabeza" width="3%"><strong></strong></td>
                            </tr>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th width="3%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $html = "";
                            if(!empty($_GET['baja'])){
                                $resDD = $baja->obtnerDetallesBaja($objB);
                                foreach($resDD as $rowDD){
                                    $vp = $con->Listar("SELECT DISTINCT ef.nombre as plan, pre.valor as serie 
                                        FROM      gf_movimiento_producto mpr
                                        LEFT JOIN gf_detalle_movimiento       dtm ON mpr.detallemovimiento = dtm.id_unico
                                        LEFT JOIN gf_plan_inventario          pln ON dtm.planmovimiento    = pln.id_unico
                                        LEFT JOIN gf_producto_especificacion pre ON pre.producto          = mpr.producto
                                        LEFT JOIN gf_ficha_inventario fi ON pre.fichainventario = fi.id_unico 
                                        LEFT JOIN gf_elemento_ficha ef ON fi.elementoficha = ef.id_unico 
                                        WHERE  mpr.detallemovimiento = ".$rowDD[0]." AND pre.fichainventario != 6");
                                    $descripcion = "";
                                    for ($j = 0; $j < count($vp); $j++) {
                                        $descripcion .= $vp[$j][0].': '.$vp[$j][1].'      ';
                                    }
                                    $html .= "\n\t<tr>";
                                    $html .= "\n\t\t<td style='text-align: left'>$rowDD[2]</td>";
                                    $html .= "\n\t\t<td class=\"text-center\">$rowDD[4]</td>";
                                    $html .= "\n\t\t<td class=\"text-left\">$descripcion</td>";
                                    $html .= "\n\t\t<td class=\"text-right\">".number_format($rowDD[1], 2, ',', '.')."</td>";
                                    $html .= "\n\t\t<td class=\"text-center\">";
                                    $af = $con->Listar("SELECT * FROM gf_detalle_movimiento WHERE detalleasociado = ".$row2[0]);
                                    if(empty($af[0][0])){
                                        if($cierre == 0){
                                            $html .= "<a href=\"javascript:void(0)\" onclick=\"eliminarD($rowDD[0],$rowDD[5])\"><span class=\"glyphicon glyphicon-trash\"></span></a>";
                                        }
                                    }
                                    $html .= "</td>";
                                    $html .= "\n\t</tr>";
                                }
                            }else{
                                if(!empty($dep)){
                                    $resP = $baja->obtnerProductos();
                                    foreach ($resP as $rowP) {
                                        $resUL = $baja->obtnerDetallesR($rowP[0]);
                                        foreach ($resUL as $rowUL) {
                                            $resDD = $baja->obtnerProductosDetalleDependencia($rowUL[0], $rowP[0], $dep);
                                            foreach ($resDD as $rowDD) {
                                                $vp = $con->Listar("SELECT DISTINCT ef.nombre as plan, pre.valor as serie ,pr.descripcion 
                                                    FROM      gf_movimiento_producto mpr
                                                    LEFT JOIN gf_detalle_movimiento       dtm ON mpr.detallemovimiento = dtm.id_unico
                                                    LEFT JOIN gf_plan_inventario          pln ON dtm.planmovimiento    = pln.id_unico
                                                    LEFT JOIN gf_producto_especificacion pre ON pre.producto          = mpr.producto
                                                    LEFT JOIN gf_ficha_inventario fi ON pre.fichainventario = fi.id_unico 
                                                    LEFT JOIN gf_elemento_ficha ef ON fi.elementoficha = ef.id_unico 
                                                    LEFT JOIN gf_producto pr ON mpr.producto = pr.id_unico 
                                                        WHERE  mpr.detallemovimiento = ".$rowDD[0]." AND pre.fichainventario != 6");
                                                $descripcion = "DESCRIPCIÓN: ".$vp[0][2].' ';
                                                for ($j = 0; $j < count($vp); $j++) {
                                                    $descripcion .= $vp[$j][0].': '.$vp[$j][1].'      ';
                                                }
                                                $html .= "\n\t<tr>";
                                                $html .= "\n\t\t<td style='text-align: left'>$rowDD[9] $rowDD[8]</td>";
                                                $html .= "\n\t\t<td class=\"text-center\">$rowDD[1]</td>";
                                                $html .= "\n\t\t<td class=\"text-left\">$descripcion</td>";
                                                $html .= "\n\t\t<td class=\"text-right\">".number_format($rowDD[2], 2, ',', '.')."</td>";
                                                $html .= "\n\t\t<td class=\"text-center\"><input type=\"checkbox\" name=\"chkT[]\" id=\"chkT".$rowDD[0]."\" title='Seleccione si desea transladar el elemento' value='$rowDD[0]-$rowDD[6]'/></td>";
                                                $html .= "\n\t</tr>";
                                            }
                                        }
                                    }
                                }
                            }
                            echo $html;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="contButton" class="col-sm-10 col-md-10 col-lg-10 text-right">
                <a id="btnT" href="javascript:void(0)" class="btn btn-primary shadow glyphicon glyphicon-floppy-disk guardar" title="Reintegrar" onclick="get_id_detail($('#sltDep').val(), <?php echo $compania ?>, <?php echo $param ?>, $('#sltTipoT').val())"></a>
            </div>
            <div class="modal fade" id="modalGuardado" role="dialog" align="center" >
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div id="forma-modal" class="modal-header">
                            <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                        </div>
                        <div class="modal-body" style="margin-top: 8px">
                            <p>Información guardada correctamente.</p>
                        </div>
                        <div id="forma-modal" class="modal-footer">
                            <button type="button" id="btnG" onclick="exit_process()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="modalNoGuardo" role="dialog" align="center" >
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div id="forma-modal" class="modal-header">
                            <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                        </div>
                        <div class="modal-body" style="margin-top: 8px">
                            <p>No se ha podido guardar la información.</p>
                        </div>
                        <div id="forma-modal" class="modal-footer">
                            <button type="button" id="btnG2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="modalMensaje" role="dialog" align="center" >
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div id="forma-modal" class="modal-header">
                            <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                        </div>
                        <div class="modal-body" style="margin-top: 8px">
                            <p id="mensaje"></p>
                        </div>
                        <div id="forma-modal" class="modal-footer">
                            <button type="button" id="btnGM" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php require_once ('footer.php'); ?>
        </div>
        <div class="modal fade" id="modalConfirmacion" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>¿Desea eliminar el registro seleccionado?</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btn-del" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                        <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalEliminado" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Información eliminada correctamente.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="ver1" onclick="window.location.reload()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalNoeliminado" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="ver2" class="btn" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="js/select2.js"></script>
    <script src="dist/jquery.validate.js"></script>
    <script>
        $(".select2").select2();

        $().ready(function(){
            var validator = $("#form").validate({
                ignore: "",
                errorElement:"em",
                errorPlacement: function(error, element){
                    error.addClass('help-block');
                },
                highlight: function(element, errorClass, validClass){
                    var elem = $(element);
                    if(elem.hasClass('select2-offscreen')){
                        $("#s2id_"+elem.attr("id")).addClass('has-error').removeClass('has-success');
                    }else{
                        $(elem).parents(".col-lg-5").addClass("has-error").removeClass('has-success');
                        $(elem).parents(".col-md-5").addClass("has-error").removeClass('has-success');
                        $(elem).parents(".col-sm-5").addClass("has-error").removeClass('has-success');
                    }
                    if($(element).attr('type') == 'radio'){
                        $(element.form).find("input[type=radio]").each(function(which){
                            $(element.form).find("label[for=" + this.id + "]").addClass("has-error");
                            $(this).addClass("has-error");
                        });
                    } else {
                        $(element.form).find("label[for=" + element.id + "]").addClass("has-error");
                        $(element).addClass("has-error");
                    }
                },
                unhighlight:function(element, errorClass, validClass){
                    var elem = $(element);
                    if(elem.hasClass('select2-offscreen')){
                        $("#s2id_"+elem.attr("id")).addClass('has-success').removeClass('has-error');
                    }else{
                        $(element).parents(".col-lg-5").addClass('has-success').removeClass('has-error');
                        $(element).parents(".col-md-5").addClass('has-success').removeClass('has-error');
                        $(element).parents(".col-sm-5").addClass('has-success').removeClass('has-error');
                    }
                    if($(element).attr('type') == 'radio'){
                        $(element.form).find("input[type=radio]").each(function(which){
                            $(element.form).find("label[for=" + this.id + "]").addClass("has-success").removeClass("has-error");
                            $(this).addClass("has-success").removeClass("has-error");
                        });
                    } else {
                        $(element.form).find("label[for=" + element.id + "]").addClass("has-success").removeClass("has-error");
                        $(element).addClass("has-success").removeClass("has-error");
                    }
                }
            });
        });

        function checked_all() {
            $('input[type=checkbox]').prop('checked', true);
        }

        function not_checked_all() {
            $('input[type=checkbox]').prop('checked', false);
        }

        $(document).ready(function() {
            var i= 0;
            $('#tableO thead th').each( function () {
                if(i => 0) {
                    var title = $(this).text();
                    switch (i){
                        case 0:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 1:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 2:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 3:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                    }
                    i = i+1;
                } else {
                    i = i+1;
                }
            });
            // DataTable
            var table = $('#tableO').DataTable({
                "autoFill": true,
                "language": {
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "zeroRecords": "No Existen Registros...",
                    "info": "Página _PAGE_ de _PAGES_ ",
                    "infoEmpty": "No existen datos",
                    "infoFiltered": "(Filtrado de _MAX_ registros)",
                    "sInfo":"Mostrando _START_ - _END_ de _TOTAL_ registros","sInfoEmpty":"Mostrando 0 - 0 de 0 registros"
                },
                "scrollY": 200,
                "scrollX": true,
                scrollCollapse: true,
                paging: false,
                fixedColumns:   {
                    leftColumns: 1
                },
                'columnDefs': [{
                    'targets': 0,
                    'searchable':false,
                    'orderable':false,
                    'className': 'dt-body-center'
                }]
            });
            var i = 0;
            table.columns().every( function () {
                var that = this;
                if(i!=0) {
                    $( 'input', this.header() ).on( 'keyup change', function () {
                        if ( that.search() !== this.value ) {
                            that
                                .search( this.value )
                                .draw();
                        }
                    });
                    i = i+1;
                } else {
                    i = i+1;
                }
            });
        });

        function bajar_productos(){
            var selected = '';
            $('input[type=checkbox]').each(function(){
                if (this.checked) {
                    selected += $(this).val()+',';
                }
            });
            var select = selected.substr(0, (selected.length) - 1);
            if(select.length > 0){
                console.log(select);
            }
        }

        $("#sltTipoT").change(function(e){
            var tipo = e.target.value;
            var num  = "";
            $("#txtNumero").empty();
            $.getJSON("access.php?controller=Baja&action=consecutivo&tipo="+tipo, function(data){
                num = JSON.parse(data);
                $("#txtNumero").val(num);
            });
        });

        function get_id_detail(dependencia, compania, param, tipo){
            
            if(tipo.length > 0){
                var selected = '';
                var numero   = $("#txtNumero").val();
                var fecha    = $("#txtFecha").val();
                if(numero.length > 0){
                    if(fecha.length > 0){
                        $('input[type=checkbox]').each(function(){
                            if (this.checked) {
                                selected += $(this).val()+',';
                            }
                        });

                        if(selected.length > 0) {
                            var select = selected.substring(0, (selected.length) - 1);
                            descripcion = $("#txtDescripcion").val();
                            var form_data = {
                                tipo:        tipo,
                                compania:    compania,
                                param:       param,
                                dependencia: dependencia,
                                marcados:    select,
                                numero:      numero,
                                fecha:       fecha, 
                                descripcion : descripcion
                            };

                            var result = "";

                            $.ajax({
                                type:   "POST",
                                url:    "access.php?controller=Baja&action=Registrar",
                                data:   form_data,
                                success:function(data, textStatus, jqXHR){
                                    console.log(data);
                                    result = JSON.parse(data);
                                    if(result == true) {
                                        $("#modalGuardado").modal('show');
                                    }else{
                                        $("#modalNoGuardo").modal('show');
                                    }
                                }
                            });
                        }else{
                            $("#modalMensaje").modal('show');
                            $("#mensaje").html("<p>No ha seleccionado ningún elemento del inventario</p>");
                            if($("#txtNumero").val().length == 0){
                                $("#txtNumero").parents(".col-lg-2").addClass("has-error");
                            }

                            if($("#txtFecha").val().length == 0){
                                $("#txtFecha").parents(".col-lg-2").addClass("has-error");
                            }

                            if($("#sltResponsable").val().length == 0){
                                $("#s2id_sltResponsable").addClass('has-error');
                            }

                            if($("#sltDep").val().length == 0){
                                $("#s2id_sltDep").addClass('has-error');
                            }
                        }
                    }else{
                        $("#txtFecha").parents(".col-lg-2").addClass("has-error");
                    }
                }else{
                    $("#txtNumero").parents(".col-lg-2").addClass("has-error");
                }
            }else{
                $("#modalMensaje").modal('show');
                $("#mensaje").html("<p>No ha seleccionado tipo de movimiento de reintegro</p>");
                $("#s2id_sltTipoT").addClass('has-error');
                if($("#txtNumero").val().length == 0){
                    $("#txtNumero").parents(".col-lg-2").addClass("has-error");
                }

                if($("#txtFecha").val().length == 0){
                    $("#txtFecha").parents(".col-lg-2").addClass("has-error");
                }

                if($("#sltResponsable").val().length == 0){
                    $("#s2id_sltResponsable").addClass('has-error');
                }

                if($("#sltDep").val().length == 0){
                    $("#s2id_sltDep").addClass('has-error');
                }
            }
        }

        function exit_process() {
            window.location.reload();
        }


        <?php
        $html = "";
        if(!empty($dep)){
            $html .= "$(\"#btn-imprimir\").attr(\"disabled\", true);\n";
        }else{
            $html .= "$(\"#btnST, #btnSN, #btnT\").attr(\"disabled\", true);";
            $html .= "\n\t$(\"#btnST, #btnSN, #btnT\").removeAttr(\"onclick\");";
            $html .= "$(\"#btn-nuevo\").attr(\"disabled\", false);\n";
        }

        if(!empty($objB)){
            $html .= "$(\"#btnT\").attr(\"disabled\", true)\n";
            $html .= "\t\t\t.removeAttr('onclick')\n";
            $html .= "\t\t$(\"#btnST, #btnSN\").css(\"display\", \"none\");\n";
            $html .= "\t\t$(\"#sltResponsable\").removeAttr('onchange');";
        }else{
            $html .= "\n\t$(\"#btn-imprimir\").attr(\"disabled\", true);";
        }
        echo $html;
        ?>

        $("#sltDep").change(function(e){
            var dependencia = e.target.value;
            $("#sltResponsable").empty();
            $.getJSON("access.php?controller=Baja&action=obtnerRes&dependencia="+dependencia, function(data){
                $("#sltResponsable").append('<option value="">Responsable</option>');
                $.each(data, function(i, objD){
                    $("#sltResponsable").append('<option value="'+objD.id_unico+'">'+objD.nombre+'</option>');
                });
            });
        });

        $("#sltResponsable").change(function(e){
            $("#form").submit();
        });

        function cargarBuscar(){
            $.get("access.php?controller=Baja&action=cargarBusqueda&compania="+<?php echo $compania ?>+'&param='+<?php  echo $param?>, function(data){
                $("#sltBuscar").html(data);
            });
        }

        function nuevo(){
            window.location = 'registrar_BAJA_DEVOLUTIVOS.php';
        }

        $("#sltBuscar").change(function(e){
            mov = e.target.value;
            if(mov.length > 0){
                window.location = 'registrar_BAJA_DEVOLUTIVOS.php?baja='+md5(mov)
            }
        });

        function eliminarD(detalle, producto){
            $("#modalConfirmacion").modal("show");
            $("#btn-del").click(function(){
                var result = "";
                $.ajax({
                    url:"access.php?controller=Baja&action=eliminar&detalle="+detalle+"&producto="+producto,
                    type:"GET",
                    success: function(data){
                        result = JSON.parse(data);
                        if(result == true){
                            $("#modalEliminado").modal("show");
                        }else if(result == false){
                            $("#modalNoEliminado").modal("show");
                        }
                    }
                });
            });
        }

        $(function(){
            var fecha = new Date();
            var dia = fecha.getDate();
            var mes = fecha.getMonth() + 1;
            if(dia < 10){
                dia = "0" + dia;
            }
            if(mes < 10){
                mes = "0" + mes;
            }
            //var fecAct = dia + "/" + mes + "/" + fecha.getFullYear();
            $.datepicker.regional['es'] = {
                closeText: 'Cerrar',
                prevText: 'Anterior',
                nextText: 'Siguiente',
                currentText: 'Hoy',
                monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                monthNamesShort: ['Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre'],
                dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
                dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
                dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
                weekHeader: 'Sm',
                dateFormat: 'dd/mm/yy',
                firstDay: 1,
                isRTL: false,
                showMonthAfterYear: false,
                yearSuffix: ''
            };
            $.datepicker.setDefaults($.datepicker.regional['es']);
            $("#txtFecha").datepicker({changeMonth: true}).val();

            cargarBuscar();
        });

        $("#btn-imprimir").click(function(){

            window.open('informes_almacen/inf_baja_almacen.php?mov=<?php echo md5($id)?>');
        })
    </script>
    <script>
       $("#txtFecha").change(function(){ 
            var fecha = $("#txtFecha").val();
            var form_data = {case: 4, fecha: fecha};
            $.ajax({
                type: "POST",
                url: "jsonSistema/consultas.php",
                data: form_data,
                success: function (response)
                {
                    console.log(response+'cierre');
                    if (response == 1) {
                        $("#txtFecha").val("").focus();
                         $("#txtmsjmdl").html("Periodo Cerrado");
                        $("#mdlMensaje").modal("show");
                    } else {
                    }
                }
            });
            
        })

    </script>
    <div class="modal fade" id="mdlMensaje" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label id="txtmsjmdl" name="txtmsjmdl" ></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnMsj" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

