<?php
// session_start();
// require_once '../Conexion/conexion.php';
// $id = $_POST['id'];
// $obligatorio = '"' . $mysqli->real_escape_string('' . $_POST['obligatorio'] . '') . '"';
// $autogenerado = '"' . $mysqli->real_escape_string('' . $_POST['autogenerado'] . '') . '"';
// $sql = "update gf_ficha_inventario set obligatorio=$obligatorio,autogenerado=$autogenerado where id_unico = $id";
// $result = $mysqli->query($sql);
// echo json_encode($result);
?>

<?php
require_once('../Conexion/conexionPDO.php');
$con = new ConexionPDO();

$action = $_REQUEST['action'];
$compania = $_SESSION['compania'];

switch ($action) {
    case 1:
        $id = $_REQUEST['id'];

        $sql_cons = "DELETE FROM gf_ficha_inventario WHERE id_unico = :id_unico";
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
        $nombreFicha = $_REQUEST['sltFicha'];
        $obligatorio = $_REQUEST['optObligatorio'];
        $autogenerado = $_REQUEST['optAutoGenerado'];
        $elementoFicha = $_REQUEST['sltElementoFicha'];
        $filas = $con->Listar("select ficha,elementoficha from gf_ficha_inventario where ficha=$nombreFicha AND elementoficha=$elementoFicha");
        $filas = count($filas);
        try {
            if (($filas == 0)) {
                $sql_cons  = "INSERT INTO gf_ficha_inventario (ficha, obligatorio, autogenerado, elementoficha) 
                VALUES (:ficha, :obligatorio, :autogenerado, :elementoficha)";
                $sql_dato = array(
                    array(":ficha", $nombreFicha),
                    array(":obligatorio", $obligatorio),
                    array(":autogenerado", $autogenerado),
                    array(":elementoficha", $elementoFicha),
                );

                $obj_resp = $con->InAcEl($sql_cons, $sql_dato);
                if (empty($obj_resp)) {
                    echo 1;
                } else {
                    echo 2;
                }
            }else{
                echo 2;
            }
        } catch (Exception $exc) {
        }

        break;
    case 3:
        $id = $_REQUEST['id'];
        $obligatorio = $_REQUEST['obligatorio'];
        $autogenerado = $_REQUEST['autogenerado'];
        $sql_cons = "UPDATE gf_ficha_inventario 
                SET obligatorio=:obligatorio, autogenerado=:autogenerado
                WHERE id_unico = :id_unico";

        $sql_dato = array(
            array(":obligatorio", $obligatorio),
            array(":autogenerado", $autogenerado),
            array(":id_unico", $id),
        );
        $obj_resp = $con->InAcEl($sql_cons, $sql_dato);
        if (empty($obj_resp)) {
            echo 1;
        } else {
            echo 2;
        }
        break;
    default:
        # code...
        break;
}

?>