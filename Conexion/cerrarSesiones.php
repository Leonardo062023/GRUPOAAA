<?php
###############MODIFICACIONES####################
#21/02/2017 |Erica G. |Archivo Creado, Destruye SesionesSSSSSSSSSSSSSS
#################################################
require_once('conexion.php');
session_start();
session_destroy();
header("Location: ../index.php");
exit;
