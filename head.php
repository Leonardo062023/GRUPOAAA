<?php
###############MODIFICACIONES####################
#21/02/2017 |Erica G. |Cerrar Sesión y Personalizar Licencia
#################################################
require_once('Conexion/ConexionPDO.php');
@session_start();
$con = new conexionPDO();
$imp = 0;
$usuario = $_SESSION['usuario'];
$com = $_SESSION['compania'];
$a = $_SESSION['anno'];
$t = $_SESSION['usuario_tercero'];
if (empty($_SESSION['usuario']) || empty($_SESSION['compania']) || empty($_SESSION['anno']) || empty($_SESSION['usuario_tercero'])) {
    header('Location:index.php');    ?>
    <script>
        window.location = 'index.php';
    </script>

<?php
} else {

    $annoih = $_SESSION['anno'];
    $anh = $con->Listar("SELECT pa.anno, T.NOMBREUNO || ' ' || T.NOMBREDOS || ' ' || T.APELLIDOUNO || ' ' || 
                T.APELLIDODOS || ' '  || ' ' || T.RAZONSOCIAL || ' ' || 
                T.NUMEROIDENTIFICACION || ' ' || T.DIGITOVERFICACION  AS NOMBRE, pb.valor 
               FROM gf_parametrizacion_anno pa 
               LEFT JOIN gf_tercero t ON pa.compania = t.id_unico 
               LEFT JOIN gs_parametros_basicos_sistema pb ON pb.nombre = 'version'
               WHERE pa.id_unico = $annoih");
    // Ejecutar la sentencia
    if (count($anh) > 0) {
        $ah = $anh[0][0];
        $ncom    = $anh[0][1];
    }
    $ver    = $anh[0][2];

    if (empty($ver)) {
        $version = '2020-01';
    } else {
        $version = $ver;
    }
?>
    <?php ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta class="viewport" content="width=device-width, initial-scale=1.0, minimun-scalable=1.0">
        </meta>
        <link rel="icon" href="img/AAA.ico" />
        <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/jquery-ui.css" type="text/css" media="screen" title="default" />
        <link rel="stylesheet" href="css/normalize.css" />
        <script src="js/jquery.min.js"></script>
        <script src="js/jquery-ui.js"></script>
        <script type="text/javascript" language="javascript" src="js/jquery-1.10.2.js"></script>
        <link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">
        <link rel="stylesheet" href="css/dataTables.jqueryui.min.css" type="text/css" media="screen" title="default" />
        <script src="js/jquery.dataTables.min.js" type="text/javascript"></script>
        <script src="js/dataTables.jqueryui.min.js" type="text/javascript"></script>
        <link rel="stylesheet" href="css/dataTables.jqueryui.min.css" type="text/css" media="screen" title="default" />
        <link rel="stylesheet" href="css/notificaciones.css" type="text/css" />
        <style>
            /* Remove the navbar's default margin-bottom and rounded borders */
            .navbar {
                margin-bottom: 0;
                border-radius: 0;
            }

            /* Set height of the grid so .sidenav can be 100% (adjust as needed) */
            .row.content {
                height: 510px
            }

            /* Set gray background color and 100% height */
            .sidenav {
                padding-top: 20px;
                background-color: #f1f1f1;
                height: 100%;
            }

            /* Set black background color, white text and some padding */
            footer {
                background-color: #555;
                color: white;
                padding: 15px;
            }

            /* On small screens, set height to 'auto' for sidenav and grid */
            @media screen and (max-width: 767px) {
                .sidenav {
                    height: auto;
                    padding: 15px;
                }

                .row.content {
                    height: auto;
                }
            }
        </style>
        <div class="col-md-14">
            <img src="RECURSOS/TOP/Fondo---Top.png">
            <div align="right" style="margin-top:-86px">
                <img src="RECURSOS/TOP/Caja---Cliente.png">
            </div>
            <div class="form-group form-inline " align="left" style="margin-top:-87px; height: 76px">
                <?php
                ?>
                <img src="RECURSOS/TOP/Caja---Logo.png">
                <img style="margin-left:-200px; margin-top:-40px" src="RECURSOS/TOP/Logos-Sigiep---Blanco.png">
                <a href="index2.php">
                    <img style="margin-left:-400px;max-width: 45px; margin-top: -40px" src="img/home.png" style="max-width: 10%">
                </a>

                <label class="form-group form-inline" style="font-size: 40px; color:white; font-family: -webkit-body; margin-left:21px; margin-top: -35px"><?php echo $ah; ?></label>
                <label class="form-group form-inline" style="font-size: 15px; color:white; font-family: -webkit-body; margin-left:-200px; margin-top: 30px; width: 250px; text-align: center; line-height: 12px;"><i><strong><?php echo ucwords(mb_strtolower($ncom)); ?></strong></i></label>
                <label class="form-group form-inline" style="font-size: 15px; color:white; font-family: -webkit-body; margin-left:50px; margin-top: 40px"><i><strong>Versión <?= $version ?></strong></i></label>
                <div align="left" class="form-group form-inline" style="margin-bottom: 10px; margin-left:600px">
                    <p class="form-group form-inline" style="color:white; font-size: 15px; font-family: cursive">
                </div>
                <?php  ?>
            </div>
        </div>
        <link href="skins/page.css" rel="stylesheet" />
        <link href="skins/blue/accordion-menu.css" rel="stylesheet" />
        <script src="js/accordion-menu.js"></script>
        <link rel="stylesheet" type="text/css" href="css/custom.css">
        <script src="js/prefixfree.min.js"></script>
        <script src="js/modernizr.js"></script>
        <script type="text/javascript" src="js/txtValida.js"></script>
        <style>
            ul li {
                margin: 10px 0;
            }
        </style>
        <div id="footer">
        </div>

    <?php }   ?>