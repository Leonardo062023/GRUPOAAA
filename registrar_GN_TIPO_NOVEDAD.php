<?php
require_once ('head.php');
require_once ('./Conexion/conexion.php');
#session_start();
?>

   <title>Registrar Tipo Novedad</title>
   
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php'; ?>
                  <div class="col-sm-10 text-left">
                  
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Registrar   Tipo Novedad</h2>
                      
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarTipoNovedadJson.php">
                          
                              <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                             <div class="form-group" style="margin-top: -10px;">
                                 <label for="nombre" class="col-sm-5 control-label"><strong class="obligado">*</strong>Nombre:</label>
                                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event,'car')" placeholder="Nombre" required>
                            </div>
                            <div class="form-group" style="margin-top: -10px;">
                                 <label for="tipo" class="col-sm-5 control-label"><strong class="obligado"></strong>Tipo:</label>
                                 <input type="text" name="tipo"  id="tipo" class="form-control" maxlength="100" title="Ingrese el tipo" onkeypress="return txtValida(event,'car')" placeholder="Tipo">
                            </div>  
                            <div class="form-group" style="margin-top: -10px;">
                                 <label for="nominaE" class="col-sm-5 control-label"><strong class="obligado"></strong>Equivalente Nómina Electrónica:</label>
                                 <input type="text" name="nominaE"  id="nominaE" class="form-control" maxlength="100" title="Ingrese el codigo nónima electrónica" onkeypress="return txtValida(event,'car')" placeholder="Codigo Nómina Electrónica">
                            </div>    
                            <div class="form-group" style="margin-top: 10px;">
                               <label for="no" class="col-sm-5 control-label"></label>
                               <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px;margin-left: 0px  ;">Guardar</button>
                            </div>
                          </form>
                      </div>
                  </div>                  
              </div>
        </div>
        <?php require_once './footer.php'; ?>
    </body>
</html>