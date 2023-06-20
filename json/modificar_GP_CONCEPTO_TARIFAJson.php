<?php
// require_once('../Conexion/conexion.php');
// session_start();
// $id = $_GET['id'];
// $concepto = $_GET['concepto'];
// $nombre  = $_GET['nombre'];
// $tarifa  = $_GET['tarifa'];
// $cadena1 = strtolower($nombre);

// $exp_regular = array();
// $exp_regular[0] = '/á/';
// $exp_regular[1] = '/é/';
// $exp_regular[2] = '/í/';
// $exp_regular[3] = '/ó/';
// $exp_regular[4] = '/ú/';
// $exp_regular[4] = '/ñ/';

// $cadena_nueva = array();
// $cadena_nueva[0] = 'a';
// $cadena_nueva[1] = 'e';
// $cadena_nueva[2] = 'i';
// $cadena_nueva[3] = 'o';
// $cadena_nueva[4] = 'u';
// $cadena_nueva[4] = 'n';
// $nombreC = preg_replace($exp_regular, $cadena_nueva, $cadena1);

// $queryUA =SELECT nombre, concepto, tarifa FROM gp_concepto_tarifa "
//     . "WHERE id_unico = '$id'";
// $carA = $mysqli->query($queryUA);
// $numA =  mysqli_fetch_row($carA);

// $queryU = "SELECT * FROM gp_concepto_tarifa "
//     . "WHERE concepto = $concepto "
//     . "AND LOWER(nombre) ='$nombreC' "
//     . "AND tarifa  =$tarifa ";
// $car = $mysqli->query($queryU);
// $num = mysqli_num_rows($car);
// if (
//     strtolower($numA[0]) == $nombreC && $numA[1] == $concepto
//     && $numA[2] == $tarifa
// ) {

//     $resultado = '1';
// } else {
//     if ($num == 0) {

//         $update = "UPDATE gp_concepto_tarifa  "
//             . "SET nombre ='$nombre', "
//             . "concepto = '$concepto', "
//             . "tarifa ='$tarifa' "
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
// echo json_encode($resultado);

?>

<?php
require_once('../Conexion/conexionPDO.php');
session_start();
$con = new ConexionPDO();
$action = $_REQUEST['action'];
$anno = $_SESSION['anno'];
$compania   = $_SESSION['compania'];

switch ($action) {
    case 1:
        // $id = $_GET['id'];
        // $query = "DELETE FROM gp_concepto_tarifa WHERE id_unico = $id";
        // $resultado = $mysqli->query($query);

        $id = $_REQUEST['id'];
        $sql_cons = "DELETE FROM gp_concepto_tarifa WHERE id_unico = :id_unico";
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
        $concepto = $_REQUEST['concepto'];
        $tarifa   = $_REQUEST['tarifa'];
        $nombre   = $_REQUEST['nombre'];
        $unidad   = $_REQUEST['unidad'];

        $num   = $con->Listar("SELECT * FROM gp_concepto_tarifa WHERE concepto = $concepto 
        AND elemento_unidad = $unidad
        AND tarifa  =$tarifa");

        $num = count($num);
        if ($num == 0) {
            // $insert = "INSERT INTO gp_concepto_tarifa  (concepto, nombre, tarifa, elemento_unidad)
            //            VALUES($concepto, '$nombre', $tarifa, $unidad)";
            // $resultado = $mysqli->query($insert);
            $sql_cons  = "INSERT INTO gp_concepto_tarifa (concepto, nombre, tarifa, elemento_unidad, parametrizacionanno) 
            VALUES (:concepto, :nombre, :tarifa, :unidad, :anno)";
            $sql_dato = array(
                array(":concepto", $concepto),
                array(":nombre", $nombre),
                array(":tarifa", $tarifa),
                array(":unidad", $unidad),
                array(":anno", $anno),
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
        $concepto = $_REQUEST['concepto'];
        $nombre  = $_REQUEST['nombre'];
        $tarifa  = $_REQUEST['tarifa'];
        $cadena1 = strtolower($nombre);

        $exp_regular = array();
        $exp_regular[0] = '/á/';
        $exp_regular[1] = '/é/';
        $exp_regular[2] = '/í/';
        $exp_regular[3] = '/ó/';
        $exp_regular[4] = '/ú/';
        $exp_regular[4] = '/ñ/';

        $cadena_nueva = array();
        $cadena_nueva[0] = 'a';
        $cadena_nueva[1] = 'e';
        $cadena_nueva[2] = 'i';
        $cadena_nueva[3] = 'o';
        $cadena_nueva[4] = 'u';
        $cadena_nueva[4] = 'n';
        $nombreC = preg_replace($exp_regular, $cadena_nueva, $cadena1);

        $numA = $con->Listar("SELECT nombre, concepto, tarifa FROM gp_concepto_tarifa WHERE id_unico = $id");

        $num = $con->Listar("SELECT * FROM gp_concepto_tarifa WHERE concepto = $concepto AND LOWER(nombre) =$nombreC AND tarifa  =$tarifa");

        if ($num == 0) {
            // $update = "UPDATE gp_concepto_tarifa 
            //     SET nombre =$nombre,
            //     concepto = $concepto,
            //     tarifa =$tarifa
            //     WHERE id_unico = $id";
            // $resul = $mysqli->query($update);

            // $resultado = '1';
            $sql_cons = "UPDATE gp_concepto_tarifa 
            SET nombre=:nombre, concepto=:concepto, tarifa=:tarifa
            WHERE id_unico = :id_unico";
            $sql_dato = array(
                array(":nombre", $nombre),
                array(":concepto", $concepto),
                array(":tarifa", $tarifa),
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
        break;
    default:
        break;
}
?>