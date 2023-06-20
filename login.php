<?php
#Llamamos a la clase de conexión
require_once ('./Conexion/conexionPDO.php');
@session_start();
$con=new conexionPDO();
$user       = $_POST["txtUsuario"];
$pass       = $_POST["txtPass"];
$anno       = $_POST['sltAnno'];
$compania   = $_POST['sltTercero'];
$ter        = strtoupper($_POST['txtIdentificacion']);
$ter = str_replace(" ","%",$ter);
$ter = str_replace("ñ","Ñ",$ter);
#Consulta para validar usuario
$sql=$con->Listar("SELECT U.USUARIO,U.CONTRASEN,U.TERCERO, U.ID_UNICO, TC.TIPO_COMPANIA,
            U.ESTADO , T.NUMEROIDENTIFICACION 
            FROM GS_USUARIO U 
            LEFT JOIN GF_TERCERO T ON U.TERCERO = T.ID_UNICO 
            LEFT JOIN GF_TERCERO TC ON T.COMPANIA = TC.ID_UNICO 
            WHERE U.USUARIO='$user' AND U.CONTRASEN='$pass' 
   AND T.COMPANIA = $compania
    AND  T.NOMBREUNO || ' ' || T.NOMBREDOS || ' ' || T.APELLIDOUNO || ' ' || 
               T.APELLIDODOS || ' '  || ' ' || T.RAZONSOCIAL || ' ' || 
               T.NUMEROIDENTIFICACION || ' ' || T.DIGITOVERFICACION  LIKE '%$ter%'");
if( count($sql)>0){
    $estado=$sql[0][5];

   
#Variables
if($estado==1){
    $usuario =$sql[0][0];
    $contra =$sql[0][1];
    if(($user == $usuario) && ($contra == $pass)){
        #Si usuario y contraseña son validos definimos sesion y guardamos los datos    
        $ingreado = "SI";    
        #Carmagmos la variable de parametrizacion año
        $_SESSION['anno'] = $anno;     
        #Cargamos de la variable compania
        $_SESSION['compania'] = $compania;    
        #Carmagos la variable nombre de usuario
        $_SESSION['usuario'] = $user;
        #Cargamos el id del tercero que se relaciona al logueado
        $_SESSION['usuario_tercero'] = $sql[0][2];
        $_SESSION['id_usuario'] = $sql[0][3];
        $_SESSION['tipo_compania'] = $sql[0][4];
        $_SESSION['num_usuario'] = $sql[0][6];
        echo 2;
    }else{    
        echo 1;
    }
} elseif($estado==3){
    echo 3;
} else {
    echo 1;
}
}
?>
