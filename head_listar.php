<?php
session_start();
require_once('Conexion/conexionPDO.php');
$con= new conexionPDO();
$imp = 0;
if (empty($_SESSION['usuario']) || empty($_SESSION['compania']) || empty($_SESSION['anno']) || empty($_SESSION['usuario_tercero'])) {
header('Location:index.php');    ?> 
    <script>
        window.location = 'index.php';
    </script>
    
<?php } else {

    $annoih = $_SESSION['anno'];
    $anh =  $con->Listar("SELECT pa.anno, T.NOMBREUNO || ' ' || T.NOMBREDOS || ' ' || T.APELLIDOUNO || ' ' || 
    T.APELLIDODOS || ' '  || ' ' || T.RAZONSOCIAL || ' ' || 
    T.NUMEROIDENTIFICACION || ' ' || T.DIGITOVERFICACION  AS NOMBRE, pb.valor 
   FROM gf_parametrizacion_anno pa 
   LEFT JOIN gf_tercero t ON pa.compania = t.id_unico 
   LEFT JOIN gs_parametros_basicos_sistema pb ON pb.nombre = 'version'
   WHERE pa.id_unico = $annoih");
   

   if (count($anh)>0 ) {
       $ah   =$anh[0][0];
       $ncom =$anh[0][1];
       $ver    =$anh[0][2];
    }
  if(empty($ver)){
    $version = '2020-01';
   } else {
    $version = $ver;
   }

    ?>
    <!DOCTYPE html>
    <html lang="en">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
            <meta class="viewport" content="width=device-width, initial-scale=1.0, minimun-scalable=1.0"></meta>
            <link rel="icon" href="img/AAA.ico" />
            <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
            <link rel="stylesheet" href="css/style.css">
            <link rel="stylesheet" href="css/jquery-ui.css" type="text/css" media="screen" title="default" />
            <script src="js/jquery.min.js"></script>
            <script src="js/jquery-ui.js" type="text/javascript"></script>
            <script type="text/javascript" language="javascript" src="js/jquery-1.10.2.js"></script>
            <link rel="stylesheet" href="css/normalize.css"/>
            <link rel="stylesheet" href="css/dataTables.jqueryui.min.css" type="text/css" media="screen" title="default" />
            <script src="js/jquery.dataTables.min.js" type="text/javascript"></script>
            <script src="js/dataTables.jqueryui.min.js" type="text/javascript"></script>
            <link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">

            <link rel="stylesheet" href="css/dataTables.jqueryui.min.css" type="text/css" media="screen" title="default" />
            <link rel="stylesheet" href="css/custom.css"/>
            <link rel="stylesheet" href="css/notificaciones.css" type="text/css" />
            <script type="text/javascript">
              $(document).ready(function () {
                  var i = 1;
                  $('#tabla thead th').each(function () {
                      if (i != 1) {
                          var title = $(this).text();
                          switch (i) {
                              case 3:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 4:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 5:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 6:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 6:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 7:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 8:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 9:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 10:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 11:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 12:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 13:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 14:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 15:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 16:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 17:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 18:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 19:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 20:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 21:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 22:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 23:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 24:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 25:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 26:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 27:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 28:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 29:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 30:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 31:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 32:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 33:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 34:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 35:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 36:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 37:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 38:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 39:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 40:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 41:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 42:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 43:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 44:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 45:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 46:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 47:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 48:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 49:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 50:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                          }
                          i = i + 1;
                      } else {
                          i = i + 1;
                      }
                  });

                  // DataTable
                  var table = $('#tabla').DataTable({
                      "autoFill": true,
                      "scrollX": true,
                      "pageLength": 5,
                      "language": {
                          "lengthMenu": "Mostrar _MENU_ registros",
                          "zeroRecords": "No Existen Registros...",
                          "info": "Página _PAGE_ de _PAGES_ ",
                          "infoEmpty": "No existen datos",
                          "infoFiltered": "(Filtrado de _MAX_ registros)",
                          "sInfo": "Mostrando _START_ - _END_ de _TOTAL_ registros", "sInfoEmpty": "Mostrando 0 - 0 de 0 registros"
                      },
                      'columnDefs': [{
                              'targets': 0,
                              'searchable': false,
                              'orderable': false,
                              'className': 'dt-body-center'
                          }]
                  });
                  var i = 0;
                  table.columns().every(function () {
                      var that = this;
                      if (i != 0) {
                          $('input', this.header()).on('keyup change', function () {
                              if (that.search() !== this.value) {
                                  that
                                          .search(this.value)
                                          .draw();
                              }
                          });
                          i = i + 1;
                      } else {
                          i = i + 1;
                      }
                  });
              });
            </script>

            <style>
                /* Remove the navbar's default margin-bottom and rounded borders */
                .navbar {
                    margin-bottom: 0;
                    border-radius: 0;
                }

                /* Set height of the grid so .sidenav can be 100% (adjust as needed) */
                .row.content {height: 510px}

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
                    .row.content {height:auto;}
                }
            </style>
        </head>
        <div class="col-md-14">
            <img src="RECURSOS/TOP/Fondo---Top.png">
            <div align="right" style="margin-top:-86px">
                <img src="RECURSOS/TOP/Caja---Cliente.png">
            </div>
            <div class="form-group form-inline " align="left" style="margin-top:-87px; height: 76px">
          
                    <img src="RECURSOS/TOP/Caja---Logo.png">
                    <img style="margin-left:-200px; margin-top:-40px" src="RECURSOS/TOP/Logos-Sigiep---Blanco.png">
                    <a href="index2.php">
                        <img style="margin-left:-400px;max-width: 45px; margin-top: -40px" src="img/home.png" style="max-width: 10%">
                    </a>
                    <label class="form-group form-inline" style="font-size: 40px; color:white; font-family: -webkit-body; margin-left:21px; margin-top: -35px"><?php echo $ah; ?></label>
                    <label class="form-group form-inline" style="font-size: 15px; color:white; font-family: -webkit-body; margin-left:-200px; margin-top: 30px; width: 250px; text-align: center;line-height: 12px;"><i><strong><?php echo ucwords(mb_strtolower($ncom)); ?></strong></i></label>
                    <label class="form-group form-inline" style="font-size: 15px; color:white; font-family: -webkit-body; margin-left:50px; margin-top: 40px"><i><strong>Versión <?=$version?></strong></i></label>
                    <div align="left" class="form-group form-inline" style="margin-bottom: 10px; margin-left:600px">
                        <p class="form-group form-inline" style="color:white; font-size: 15px; font-family: cursive">
                    </div>  
                    <?php  ?>
            </div>
        </div> 
        <script type="text/javascript" src="js/txtValida.js"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <script src="js/prefixfree.min.js"></script>
        <script src="js/modernizr.js"></script>
        <link href="skins/page.css" rel="stylesheet" />
        <link href="skins/blue/accordion-menu.css" rel="stylesheet" />
        <style>
            ul li{margin:10px 0;}
        </style>
<?php }  ?>