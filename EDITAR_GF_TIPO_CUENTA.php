<?php
#05/04/2017 --- Nestor B --- se agrego el atributo mb para que tome las tildes 
//llamado a la cabez del formulario
	require_once 'head.php'; 
	//Llamado a la clase de conexión
	require_once('Conexion/conexionPDO.php');
	$con = new ConexionPDO();
	//Variable de sessión
	
	
	//Declaración de la variable que recibe la id 
	$id_tipodoc = "";
	//validación preguntando si la variable enviada del listar viene vacia
	if(isset($_GET["id_tipoC"])){
		//Se carga la variable id con el valor traido de la url
		$id_tipodoc = $_GET["id_tipoC"];
		//Query o sql de consulta 
		switch ($_SESSION['conexion']) {
			case '1':
			  $row = $con->Listar("SELECT Id_Unico, Nombre,equivalente_NE FROM gf_tipo_cuenta WHERE (Id_Unico) = '$id_tipodoc'");
			break;
			case '2':
			  $row = $con->Listar("SELECT Id_Unico, Nombre,equivalente_NE FROM gf_tipo_cuenta WHERE (Id_Unico) = '$id_tipodoc'");
		  break;
		  }
		$sql = "";
	}
?>
	<!-- Titulo del formulario -->
	<title>Modificar Tipo Cuenta</title>
</head>
<body>
	<!-- Division del contenedor principal -->
	<div class="container-fluid text-center">
		<!-- Inicion de la fila y contenido -->
		<div class="row content">
			<?php require_once 'menu.php'; ?>
			<!-- Inicio de contenido -->
			<div class="col-sm-10 text-left">
				
				<!-- Inicio de Titulo -->
				<h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;"> Modificar Tipo Cuenta</h2>
				<!-- Fin de Titulo -->
				<!-- Inicio de  división o contenedor del formulario-->
				<div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
					<!-- Inicio del formulario -->
					<form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:modificar()">
						
					<?php for ($i=0; $i <count($row) ; $i++) {?>
					<!-- Campo oculto para la id -->
						<input type="hidden" name="id" value="<?php echo $row[$i][0] ?>">
						<!-- Fin de campo oculto para la id -->
						<!-- Incio de párrafo de campos obligatorios -->
						<p align="center" style="margin-bottom: 25px; margin-top:25px; margin-left:30px; font-size:80%;">
							Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.
						</p>
						<!-- Fin de párrafo de campos obligatorios -->
						<!-- División o Contenedor del campo Nombre -->
						<div class="form-group" style="margin-top: -10px;">
							<label for="nombre" class="col-sm-5 control-label">
								<strong style="color:#03C1FB;">*</strong>Nombre:
							</label>
							<input type="text" name="nombre" maxlength="100" id="nombre" class="form-control" title="Ingrese el nombre" value="<?php echo ucwords(mb_strtolower($row[$i][1])); ?>" onkeypress="return txtValida(event,'car')" placeholder="Nombre">
						</div>
                         <!-- Fin de contenedor de campo nombre -->
						 <!-- División o Contenedor del NOMINA ELECTRONICA -->
						<div class="form-group" style="margin-top: -10px;">
							<label for="nominaE" class="col-sm-5 control-label">
								<strong style="color:#03C1FB;">*</strong>Equivalente Nómina Electrónica:
							</label>
							<input type="text" name="nominaE" maxlength="100" id="nominaE" class="form-control" title="Ingrese el codigo de nomina electronica" value="<?php echo ($row[$i][2]); ?>" onkeypress="return txtValida(event,'car')" placeholder="Codigo Nomina Electronica">
						</div>
						
						<!-- Inicio de Bóton de Guardado -->
						<div class="form-group" style="margin-top: 10px;">
             				 <label for="no" class="col-sm-5 control-label"></label>
             				   <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
          			    </div>
						<!-- Fin de Bóton de Guardado -->
						<!-- Campo oculto-->
						<input type="hidden" name="MM_insert">
						<!-- Fin de Campo oculto-->
					<!-- Fin de Formulario -->
					<?php } ?>
					</form>
				<!-- Fin de división y contenedor del formulario -->
				</div>
			<!-- Fin de Contenido -->
			</div>			
		<!--Fin de la fila y contenido -->	
		</div>		
		<!-- Fin del Contenedor principal -->
	</div>
	<!-- Llamado al pie de pagina -->
	<script>
                        function modificar(){
                            jsShowWindowLoad('Modificando Datos ...');
                            var formData = new FormData($("#form")[0]);
                            $.ajax({
                                type: 'POST',
                                url: "Json/modificarTipoCuentaJson.php?action=3",
                                data:formData,
                                contentType: false,
                                processData: false,
                                success: function(response)
                                {
                                    jsRemoveWindowLoad();
                                    console.log(response);
                                    if(response==1){
                                        $("#mensaje").html('Información Modificada Correctamente');
                                        $("#modalMensajes").modal("show");
                                       
                                            document.location='LISTAR_GF_TIPO_CUENTA.php';
                                       
                                    } else {
                                        $("#mensaje").html('No Se Ha Podido Modificar Información');
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                            $("#modalMensajes").modal("hide");
                                        })

                                    }
                                }
                            });
                        }
                    </script>
	<?php require_once 'footer.php'; ?>
</body>
</html>