<?php
// require_once('../Conexion/conexion.php');
// session_start();
// $id = $mysqli->real_escape_string('' . $_REQUEST['id'] . '');
// $nombre  = $mysqli->real_escape_string('' . $_REQUEST['nombre'] . '');
// if (!empty($_POST['tipoC'])) {
//     $tipo = $mysqli->real_escape_string('' . $_POST['tipoC'] . '');
// } else {
//     $tipo = 'NULL';
// }
// $cadena1 = strtolower($nombre);
// //cargo en el array los textos a sustituir
// $exp_regular = array();
// $exp_regular[0] = '/á/';
// $exp_regular[1] = '/é/';
// $exp_regular[2] = '/í/';
// $exp_regular[3] = '/ó/';
// $exp_regular[4] = '/ú/';
// $exp_regular[4] = '/ñ/';
// //cargo en el array los textos que pondremos en la sustitucion
// $cadena_nueva = array();
// $cadena_nueva[0] = 'a';
// $cadena_nueva[1] = 'e';
// $cadena_nueva[2] = 'i';
// $cadena_nueva[3] = 'o';
// $cadena_nueva[4] = 'u';
// $cadena_nueva[4] = 'n';


// $queryUA = "SELECT nombre FROM gp_tipo_pago "
//     . "WHERE id_unico = '$id'";
// $carA = $mysqli->query($queryUA);
// $numA =  mysqli_fetch_row($carA);
// $cadena2 = strtolower($numA[0]);

// $queryU = "SELECT * FROM gp_tipo_pago "
//     . "WHERE LOWER(nombre) = '$nombre'";
// $car = $mysqli->query($queryU);
// $num = mysqli_num_rows($car);
// //Reemplazar caracteres en nombre para comparar anterior con el nuevo
// $nombreC1 = preg_replace($exp_regular, $cadena_nueva, $cadena1);
// $nombreC2 = preg_replace($exp_regular, $cadena_nueva, $cadena2);
// //comparación para que guarde
// if (!empty($_POST['banco'])) {
//     $banco  = '"' . $mysqli->real_escape_string('' . $_POST['banco'] . '') . '"';
// } else {
//     $banco = 'NULL';
// }
// if ($_POST['retencion'] == 1) {
//     $retencion = 1;
// } else {
//     $retencion = 'NULL';
// }
// if ($nombreC2 == $nombreC1) {
//     $update = "UPDATE gp_tipo_pago "
//         . "SET nombre ='$nombre',tipo_comprobante = $tipo, cuenta_bancaria= $banco, retencion = $retencion "
//         . "WHERE id_unico = '$id'";
//     $resul = $mysqli->query($update);
//     $resultado = '1';
// } else {
//     if ($num == 0) {
//         $update = "UPDATE gp_tipo_pago "
//             . "SET nombre ='$nombre',tipo_comprobante = $tipo, cuenta_bancaria= $banco, retencion = $retencion  "
//             . "WHERE id_unico = '$id'";
//         $resul = $mysqli->query($update);
//         $resultado = '1';
//     } else {
//         if ($num > 0) {
//             $resultado = '3';
//         } else {
//             $resultado = false;
//         }
//     }
// }
?>
<?php
session_start();
require_once('../Conexion/ConexionPDO.php');
$con = new ConexionPDO();
$action = $_REQUEST['action'];
$compania = $_SESSION['compania'];
switch ($action) {
    case 1:
        // $query = "DELETE FROM gp_tipo_pago WHERE id_unico = $id";
        $id = $_REQUEST['id'];
        $sql_cons = "DELETE FROM gp_tipo_pago WHERE id_unico = :id_unico";
        $sql_dato = array(
            array(":id_unico", $id)
        );
        $resp = $con->InAcEl($sql_cons, $sql_dato);
        if (empty($resp)) {
            echo 1;
        } else {
            echo 2;
        }
        break;
    case 2:
        $nombre  = $_REQUEST['nombre'];
        $cadena1 = strtolower($nombre);
        //cargo en el array los textos a sustituir
        $exp_regular = array();
        $exp_regular[0] = '/á/';
        $exp_regular[1] = '/é/';
        $exp_regular[2] = '/í/';
        $exp_regular[3] = '/ó/';
        $exp_regular[4] = '/ú/';
        $exp_regular[4] = '/ñ/';
        //cargo en el array los textos que pondremos en la sustitucion
        $cadena_nueva = array();
        $cadena_nueva[0] = 'a';
        $cadena_nueva[1] = 'e';
        $cadena_nueva[2] = 'i';
        $cadena_nueva[3] = 'o';
        $cadena_nueva[4] = 'u';
        $cadena_nueva[4] = 'n';
        $nombreC = preg_replace($exp_regular, $cadena_nueva, $cadena1);

        if (!empty($_REQUEST['tipoC'])) {
            $tipoC  = $_REQUEST['tipoC'];
        } else {
            $tipoC = NULL;
        }
        if (!empty($_REQUEST['banco'])) {
            $banco  = $_REQUEST['banco'];
        } else {
            $banco = NULL;
        }
        if ($_REQUEST['retencion'] == 1) {
            $retencion = 1;
        } else {
            $retencion = NULL;
        }
        $num = $con->Listar("SELECT * FROM gp_tipo_pago
                WHERE LOWER(nombre) = '$nombreC' AND compania = $compania");
        $num = count($num);
        if ($num == 0) {
            // $insert = "INSERT INTO gp_tipo_pago (nombre,tipo_comprobante, cuenta_bancaria, compania, retencion)
            //     VALUES($nombre,$tipoC, $banco,$compania, $retencion)";
            $sql_cons  = "INSERT INTO gp_tipo_pago (nombre,tipo_comprobante, cuenta_bancaria, compania, retencion) 
            VALUES (:nombre,:tipoC,:banco,:compania,:retencion)";
            $sql_dato = array(
                array(":nombre", $nombre),
                array(":tipoC", $tipoC),
                array(":banco", $banco),
                array(":compania", $compania),
                array(":retencion", $retencion),
            );
            $obj_resp = $con->InAcEl($sql_cons, $sql_dato);
            if (empty($obj_resp)) {
                echo 1;
            } else {
                echo 2;
            }
        } else {
            echo 3;
        }
        break;
    case 3:
        $id = $_REQUEST['id'];
        $nombre  = $_REQUEST['nombre'];
        if (!empty($_REQUEST['tipoC'])) {
            $tipo = $_REQUEST['tipoC'];
        } else {
            $tipo = NULL;
        }
        $cadena1 = strtolower($nombre);
        //cargo en el array los textos a sustituir
        $exp_regular = array();
        $exp_regular[0] = '/á/';
        $exp_regular[1] = '/é/';
        $exp_regular[2] = '/í/';
        $exp_regular[3] = '/ó/';
        $exp_regular[4] = '/ú/';
        $exp_regular[4] = '/ñ/';
        //cargo en el array los textos que pondremos en la sustitucion
        $cadena_nueva = array();
        $cadena_nueva[0] = 'a';
        $cadena_nueva[1] = 'e';
        $cadena_nueva[2] = 'i';
        $cadena_nueva[3] = 'o';
        $cadena_nueva[4] = 'u';
        $cadena_nueva[4] = 'n';


        $numA = $con->Listar("SELECT nombre FROM gp_tipo_pago WHERE id_unico = $id");

        $cadena2 = strtolower($numA[0][0]);

        $nombre = strtolower($nombre);
        $num = $con->Listar("SELECT * FROM gp_tipo_pago WHERE LOWER(nombre) = '$nombre'");
        $num = count($num);

        //Reemplazar caracteres en nombre para comparar anterior con el nuevo
        $nombreC1 = preg_replace($exp_regular, $cadena_nueva, $cadena1);
        $nombreC2 = preg_replace($exp_regular, $cadena_nueva, $cadena2);

        //comparación para que guarde
        if (!empty($_REQUEST['banco'])) {
            $banco  = $_REQUEST['banco'];
        } else {
            $banco = NULL;
        }
        if ($_REQUEST['retencion'] == 1) {
            $retencion = 1;
        } else {
            $retencion = NULL;
        }

        if ($nombreC2 == $nombreC1) {
            // $update = "UPDATE gp_tipo_pago
            //     SET nombre ='$nombre',tipo_comprobante = $tipo, cuenta_bancaria= $banco, retencion = $retencion
            //     WHERE id_unico = '$id'";

                $sql_cons = "UPDATE gp_tipo_pago 
                SET nombre=:nombre,tipo_comprobante=:tipo_comprobante,cuenta_bancaria=:cuenta_bancaria,retencion=:retencion
                WHERE id_unico = :id_unico";
                $sql_dato = array(
                    array(":nombre", $nombre),
                    array(":tipo_comprobante", $tipo),
                    array(":cuenta_bancaria", $banco),
                    array(":retencion", $retencion),
                    array(":id_unico", $id),
                );
                $obj_resp = $con->InAcEl($sql_cons, $sql_dato);
                if (empty($obj_resp)) {
                    echo 1;
                } else {
                    echo 2;
                }
            
        } else {
            if ($num == 0) {
                // $update = "UPDATE gp_tipo_pago
                //     SET nombre =$nombre,tipo_comprobante = $tipo, cuenta_bancaria= $banco, retencion = $retencion
                //     WHERE id_unico = '$id'";
                // $resul = $mysqli->query($update);
                // $resultado = '1';
                $sql_cons = "UPDATE gp_tipo_pago 
                SET nombre=:nombre,tipo_comprobante=:tipo_comprobante,cuenta_bancaria=:cuenta_bancaria,retencion=:retencion
                WHERE id_unico = :id_unico";
                $sql_dato = array(
                    array(":nombre", $nombre),
                    array(":tipo_comprobante", $tipo),
                    array(":cuenta_bancaria", $banco),
                    array(":retencion", $retencion),
                    array(":id_unico", $id),
                );
                $obj_resp = $con->InAcEl($sql_cons, $sql_dato);
                if (empty($obj_resp)) {
                    echo 1;
                } else {
                    echo 2;
                }
            } else {
                echo 3;
            }
        }
        break;
    default:
        break;
}
?>