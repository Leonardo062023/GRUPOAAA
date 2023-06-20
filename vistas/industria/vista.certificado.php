<?php
require './Conexion/conexion.php';
require './head.php';
?>
    <title>Cerificado</title>
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <link rel="stylesheet" href="css/jquery-ui.css">
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require './menu.php'; ?>
            <div class="col-sm-10 col-md-10 col-lg-10 text-left">
                <h2 align="center" class="tituloform" style="margin-top: 0;">CERTIFICADO</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form col-sm-12 col-md-12 col-lg-12">
                    <form name="form" id="form" action="access.php?controller=Industria&action=certificado" class="form-horizontal" method="POST"  enctype="multipart/form-data" target="_blank">
                        <p align="center" class="parrafoO" style="margin-bottom:5px">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                        <div class="form-group">
                            <label class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Contribuyente:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <input type="text" name="txtContribuyente" id="txtContribuyente" placeholder="Contribuyente" class="form-control" style="width: 100%;" required />
                                <input type="hidden" name="txtIdC" id="txtIdC" required />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label col-sm-5 col-md-5 col-lg-5"></label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <button class="btn btn-primary"><span class="fa fa-download"></span></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php require './footer.php'; ?>
            <script src="js/jquery-ui.js"></script>
            <script type="text/javascript" src="js/select2.js"></script>
            <script>
                $(".select").select2();

                $("#txtContribuyente").autocomplete({
                    source: "access.php?controller=Industria&action=obtenerContribuyentes",
                    minlength: 3,
                    select: function (e, ui) {
                        $("#txtContribuyente").val(ui.item.value);
                        $("#txtIdC").val(ui.item.id);
                    }
                });
            </script>
        </div>
    </div>
</body>
</html>