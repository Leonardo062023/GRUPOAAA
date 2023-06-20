<?php
//   require_once('../Conexion/conexion.php');
//   session_start();
//   $id  = $mysqli->real_escape_string(''.$_POST['id'].'');
//   $uso = $mysqli->real_escape_string(''.$_POST['uso'].'');
//   $periodo  = $mysqli->real_escape_string(''.$_POST['periodo'].'');
//   $estrato  = $mysqli->real_escape_string(''.$_POST['estrato'].'');
//   $tipoT  = $mysqli->real_escape_string(''.$_POST['tipoT'].'');
//   $valor  = $mysqli->real_escape_string(''.$_POST['valor'].'');
//   $porcIva = $mysqli->real_escape_string(''.$_POST['porcIva'].'');
//   $porcIm  =$mysqli->real_escape_string(''.$_POST['porcIm'].'');
//   if ($uso=='""' || $uso==NULL){
//      $uso='NULL'; 
//      $usoB='IS NULL'; 
//      $usoC = '';
//   } else {
//       $usoB='= "'.$uso.'"';
//       $usoC = $uso;
//   }
//   if ($periodo=='""'|| $periodo==NULL){
//      $periodo='NULL'; 
//      $periodoB='IS NULL'; 
//      $periodoC = '';
//   } else {
//       $periodoB='="'.$periodo.'"';
//       $periodoC = $periodo;
//   }
//   if ($estrato=='""' || $estrato==NULL){
//      $estrato='NULL'; 
//      $estratoB='IS NULL'; 
//      $estratoC = '';
//   } else {
//       $estratoB='="'.$estrato.'"';
//       $estratoC = $estrato;
//   }

//   $queryUA="SELECT uso, periodo, estrato, tipo_tarifa, valor, porcentaje_iva, porcentaje_impoconsumo  FROM gp_tarifa "
//           . "WHERE id_unico = '$id'";
//   $carA = $mysqli->query($queryUA);
//   $numA=  mysqli_fetch_row($carA);

//     $queryU="SELECT * FROM gp_tarifa "
//           . "WHERE uso $usoB "
//           . "AND periodo $periodoB "
//           . "AND estrato  $estratoB "
//           . "AND tipo_tarifa = $tipoT "
//           . "AND valor = $valor "
//           . "AND porcentaje_iva = $porcIva "
//           . "AND porcentaje_impoconsumo= $porcIm";
//   $car = $mysqli->query($queryU);
//   $num=mysqli_num_rows($car);

//   if($numA[0]==$usoC && $numA[1]==$periodoC
//      && $numA[2]==$estratoC && $numA[3]==$tipoT && $numA[4]==$valor 
//      && $numA[5]==$porcIva && $numA[6]==$porcIm ){

//           $resultado = '1';
//   }else {
//         if($num == 0)
//         {

//        $update = "UPDATE gp_tarifa "
//               . "SET uso =$uso, "
//               . "periodo = $periodo, "
//               . "estrato =$estrato, "
//               . "tipo_tarifa =$tipoT, "
//               . "valor =$valor, "
//               . "porcentaje_iva = $porcIva,"
//               . "porcentaje_impoconsumo = $porcIm "
//               . "WHERE id_unico = $id";
//          $resul = $mysqli->query($update);

//          $resultado ='1';
//          } else {
//              if($num > 0){
//                  $resultado ='3';
//              }else {
//                  $resultado= false;
//              }
//          } 
//   }
?>
<?php
require_once('../Conexion/conexionPDO.php');
require_once('../Conexion/conexion.php');
@session_start();
$con = new ConexionPDO();
$action = $_REQUEST['action'];
$anno = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
$conexion = $_SESSION['conexion'];
switch ($action) {
    case 1:
        $id = $_REQUEST['id'];
        // $query = "DELETE FROM gp_tarifa WHERE id_unico = $id";
        // $resultado = $mysqli->query($query);
        $sql_cons = "DELETE FROM gp_tarifa WHERE id_unico=:id_unico";
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
        $uso = $_REQUEST['uso'];
        $periodo  = $_REQUEST['periodo'];
        $estrato  = $_REQUEST['estrato'];
        $tipoT  = $_REQUEST['tipoT'];
        $valor  = $_REQUEST['valor'];
        $porcIva = $_REQUEST['porcIva'];
        $porcIm  = $_REQUEST['porcIm'];

        if ($uso == "") {
            $uso = NULL;
            $usoB = 'IS NULL';
        } else {
            $usoB = '=' . $uso;
        }
        if ($periodo == "") {
            $periodo = NULL;
            $periodoB = 'IS NULL';
        } else {
            $periodoB = '=' . $periodo;
        }
        if ($estrato == "") {
            $estrato = NULL;
            $estratoB = 'IS NULL';
        } else {
            $estratoB = '=' . $estrato;
        }

        $num = $con->Listar("SELECT * FROM gp_tarifa 
            WHERE uso $usoB 
            AND periodo $periodoB 
            AND estrato  $estratoB 
            AND tipo_tarifa = $tipoT 
            AND valor = $valor 
            AND porcentaje_iva = $porcIva 
            AND porcentaje_impoconsumo= $porcIm");
        $num = count($num);
        if ($num == 0) {
            //     $insert = "INSERT INTO gp_tarifa  (uso, periodo, estrato,tipo_tarifa, valor, porcentaje_iva,porcentaje_impoconsumo) "
            //         . "VALUES($uso, $periodo, $estrato, $tipoT, $valor, $porcIva,$porcIm)";
            //     $resultado = $mysqli->query($insert);
            // } else { 
            if($conexion==2){
                $valor = str_replace('.', ',', $valor);
            }
            $sql_cons  = "INSERT INTO gp_tarifa(uso, periodo, estrato, tipo_tarifa, valor, porcentaje_iva, porcentaje_impoconsumo) 
            VALUES (:uso, :periodo, :estrato, :tipoT, :valor, :porcIva, :porcIm)";
            $sql_dato = array(
                array(":uso", $uso),
                array(":periodo", $periodo),
                array(":estrato", $estrato),
                array(":tipoT", $tipoT),
                array(":valor", $valor),
                array(":porcIva", $porcIva),
                array(":porcIm", $porcIm),
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
        $id  = $_REQUEST['id'];
        $uso = $_REQUEST['uso'];
        $periodo  = $_REQUEST['periodo'];
        $estrato  = $_REQUEST['estrato'];
        $tipoT  = $_REQUEST['tipoT'];
        $valor  = $_REQUEST['valor'];
        $porcIva = $_REQUEST['porcIva'];
        $porcIm  = $_REQUEST['porcIm'];
        if ($uso == "" || $uso == NULL) {
            $uso = NULL;
            $usoB = 'IS NULL';
            $usoC = '';
        } else {
            $usoB = '= "' . $uso . '"';
            $usoC = $uso;
        }
        if ($periodo == "" || $periodo == NULL) {
            $periodo = NULL;
            $periodoB = 'IS NULL';
            $periodoC = '';
        } else {
            $periodoB = '="' . $periodo . '"';
            $periodoC = $periodo;
        }
        if ($estrato == "" || $estrato == NULL) {
            $estrato = NULL;
            $estratoB = 'IS NULL';
            $estratoC = '';
        } else {
            $estratoB = '="' . $estrato . '"';
            $estratoC = $estrato;
        }

        $numA = $con->Listar("SELECT uso, periodo, estrato, tipo_tarifa, valor, porcentaje_iva, porcentaje_impoconsumo  FROM gp_tarifa
        WHERE id_unico = $id");

        $num = $con->Listar("SELECT * FROM gp_tarifa 
            WHERE uso $usoB 
            AND periodo $periodoB 
            AND estrato  $estratoB 
            AND tipo_tarifa = $tipoT 
            AND valor = $valor 
            AND porcentaje_iva = $porcIva 
            AND porcentaje_impoconsumo= $porcIm");

        if ($num == 0) {
            // $update = "UPDATE gp_tarifa "
            //     . "SET uso =$uso, "
            //     . "periodo = $periodo, "
            //     . "estrato =$estrato, "
            //     . "tipo_tarifa =$tipoT, "
            //     . "valor =$valor, "
            //     . "porcentaje_iva = $porcIva,"
            //     . "porcentaje_impoconsumo = $porcIm "
            //     . "WHERE id_unico = $id";
            // $resul = $mysqli->query($update);

            if($conexion==2){
                $valor = str_replace('.', ',', $valor);
            }
            $sql_cons = "UPDATE gp_tarifa 
            SET uso=:uso, periodo=:periodo, estrato=:estrato, tipo_tarifa=:tipo_tarifa, valor=:valor, porcentaje_iva=:porcentaje_iva,
            porcentaje_impoconsumo=:porcentaje_impoconsumo
            WHERE id_unico = :id_unico";
            $sql_dato = array(
                array(":uso", $uso),
                array(":periodo", $periodo),
                array(":estrato", $estrato),
                array(":tipo_tarifa", $tipoT),
                array(":valor", $valor),
                array(":porcentaje_iva", $porcIva),
                array(":porcentaje_impoconsumo", $porcIm),
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
        # code...
        break;
}

?>