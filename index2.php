<?php
require_once ('head.php');
require_once 'Conexion/conexionPDO.php';
$con= new conexionPDO();
?>
<title>Inicio</title>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <?php
                #************Datos Compañia************#
                $compania = $_SESSION['compania'];

                $sqlC = $con->Listar( "SELECT 	TER.ID_UNICO,
                                    TER.RAZONSOCIAL,
                                    TER.RUTA_LOGO
                    FROM GF_TERCERO TER 
                    WHERE TER.ID_UNICO = $compania");
                    $razonsocial=$sqlC[0][1];
                    $rutalogo=$sqlC[0][2];
                ?>
                <table align="center">
                    <tr>
                    <br/><br/>
                    </tr>
                    <tr>
                        <td style="font-size: 20px;color:#002952; font-family: monospace"><center><?php echo mb_strtoupper($razonsocial) . ' - '  ?></center></td>

                    </tr>
                    <tr>
                    <?php if (!empty($rutalogo)) { ?>
                            <td><center><img src="<?php echo $rutalogo ?>" style="max-width: 30%; height: auto;"></center></td>
                    <?php } ?>
                    </tr>

                </table>

            </div>
        </div>
    </div>
    <div class="modal fade" id="error" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content" style="width: 700px">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <img src="img/Sigiepcito.png" style="width:20%">
                <div class="modal-body" style="margin-top: 8px;display: inline-block;">
                    
                    <p style="text-align: justify; font-size: 14px">
                    <?php if(date('m')=='01'){echo 'Grupo AAA Asesores SAS les desea un exitoso año '.date('Y').'<br/>Informamos ';} else { 
                        echo 'Grupo AAA Asesores  '.'<br/>Informa ';
                        //echo ' '.'<br/>'.' Grupo AAA Asesores informa';
                    }?>
                    que su contrato de soporte y actualización caducó el 
                    <label id="fechac"></label>
                    <br/> Lo Invitamos a comunicarse con el área comercial.
                    <br/><span><i class="glyphicon glyphicon-envelope"></i></span> <strong>Correo electrónico:</strong> comercial@sigiep.com
                    <br/><span><i class="glyphicon glyphicon-earphone"></i></span> <strong>Teléfono:</strong> 311 847 7257 - 310 282 0998
                    </p>
                </div>
                <img src="img/Eslogan_2.png" style="width:50%; margin-left: 20%; margin-top:-20%">
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="msje" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>

</body>
</html>