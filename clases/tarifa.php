<?php
require_once('./Conexion/ConexionPDO.php');


class tarifa
{
    public $id_unico;
    public $uso;
    public $periodo;
    public $estrato;
    public $tipo_taria;
    public $porcentaje_iva;
    public $porcentaje_impoconsumo;
    public $valor;

    private $con;

    public function getIdUnico()
    {
        return $this->id_unico;
    }

    public function setIdUnico($id_unico)
    {
        $this->id_unico = $id_unico;
    }

    public function getUso()
    {
        return $this->uso;
    }

    public function setUso($uso)
    {
        $this->uso = $uso;
    }

    public function getPeriodo()
    {
        return $this->periodo;
    }

    public function setPeriodo($periodo)
    {
        $this->periodo = $periodo;
    }

    public function getEstrato()
    {
        return $this->estrato;
    }

    public function setEstrato($estrato)
    {
        $this->estrato = $estrato;
    }

    public function getTipoTaria()
    {
        return $this->tipo_taria;
    }

    public function setTipoTaria($tipo_taria)
    {
        $this->tipo_taria = $tipo_taria;
    }

    public function getPorcentajeIva()
    {
        return $this->porcentaje_iva;
    }

    public function setPorcentajeIva($porcentaje_iva)
    {
        $this->porcentaje_iva = $porcentaje_iva;
    }

    public function getPorcentajeImpoconsumo()
    {
        return $this->porcentaje_impoconsumo;
    }

    public function setPorcentajeImpoconsumo($porcentaje_impoconsumo)
    {
        $this->porcentaje_impoconsumo = $porcentaje_impoconsumo;
    }

    public function getValor()
    {
        return $this->valor;
    }

    public function setValor($valor)
    {
        $this->valor = $valor;
    }

    public function __construct()
    {
        $this->con = new ConexionPDO();
    }

    public function registrar(tarifa $data)
    {
        try {
            //require_once('./Conexion/Conexion.php');
            // $consulta = $this->con->Listar("ALTER SESSION SET NLS_NUMERIC_CHARACTERS = '.,'");
            // $consulta1 = $this->con->Listar("SELECT * FROM nls_database_parameters WHERE PARAMETER LIKE '%NLS_NUMERIC_CHARACTERS%'");
            // $sql = "INSERT INTO gp_tarifa (uso, periodo, estrato, tipo_tarifa, porcentaje_iva, porcentaje_impoconsumo, valor) 
            // VALUES ($data->uso, $data->periodo, $data->estrato, $data->estrato, $data->porcentaje_iva, $data->porcentaje_impoconsumo,  $data->valor);";
            //  $stmt = oci_parse($oracle, $sql);        // Preparar la sentencia
            //  $ok   = oci_execute( $stmt );            // Ejecutar la sentencia
            $conexion = $_SESSION['conexion'];
            $sql_cons  = "INSERT INTO gp_tarifa (uso, periodo, estrato, tipo_tarifa, porcentaje_iva, porcentaje_impoconsumo, valor)
            VALUES (:uso, :periodo, :estrato, :tipo, :porcentaje, :porcentaje_impo, :valor)";

            switch ($conexion) {
                case 1:
                    $valor = $data->valor;
                    break;
                case 2:
                    $valor = str_replace('.', ',', $data->valor);
                    break;
                default:
                    break;
            }

            $sql_dato = array(
                array(":uso", $data->uso),
                array(":periodo", $data->periodo),
                array(":estrato", $data->estrato),
                array(":tipo", $data->tipo_taria),
                array(":porcentaje", $data->porcentaje_iva),
                array(":porcentaje_impo", $data->porcentaje_impoconsumo),
                array(":valor", $valor),
            );
            $obj_resp = $this->con->InAcEl($sql_cons, $sql_dato);
            if (empty($obj_resp)) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function modificar(tarifa $data)
    {
        try {
            $sql_cons = "UPDATE gp_tarifa 
            SET uso=:uso, periodo=:periodo, estrato=:estrato, tipo_tarifa=:tipo_tarifa, porcentaje_iva=:porcentaje_iva, 
            porcentaje_impoconsumo=:porcentaje_impoconsumo, valor=:valor
            WHERE id_unico = :id_unico";
            $sql_dato = array(
                array(":uso", $data->uso),
                array(":periodo", $data->periodo),
                array(":estrato", $data->estrato),
                array(":tipo_tarifa", $data->tipo_taria),
                array(":porcentaje_iva", $data->porcentaje_iva),
                array(":porcentaje_impoconsumo", $data->porcentaje_impoconsumo),
                array(":valor", $data->valor),
                array(":id_unico", $data->id_unico),
            );

            $obj_resp = $this->con->InAcEl($sql_cons, $sql_dato);
            if (empty($obj_resp)) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function eliminar($id_unico)
    {
        try {
            $sql_cons = "DELETE FROM gp_tarifa WHERE id_unico = :id_unico";

            $sql_dato = array(
                array(":id_unico", $id_unico)
            );

            $resp = $this->con->InAcEl($sql_cons, $sql_dato);

            if (empty($resp)) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtenerListado()
    {

        $str = $this->con->Listar("SELECT id_unico, nombre, valor FROM gp_tarifa FETCH FIRST 1 ROW ONLY");
        return $str;
    }

    public function retornarConexion()
    {
        return $this->con;
    }

    public function obtenerUltimoRegistro($tipo)
    {
        $xxx = "";
        $res = $this->con->Listar("SELECT MAX(id_unico) FROM gp_tarifa WHERE tipo_tarifa = $tipo");
        if (count($res) > 0) {
            $xxx = $res[0][0];
        }
        return $xxx;
    }

    public function editarTarifa($id, $valor, $iva, $impo)
    {
        try {
            $conexion = $_SESSION['conexion'];
            if ($conexion == 2) {
                $valor = str_replace('.', ',', $valor);
                $iva = str_replace('.', ',', $iva);
                $impo = str_replace('.', ',', $impo);
            }


            $sql_cons = "UPDATE gp_tarifa 
            SET valor=:valor, porcentaje_iva=:iva, porcentaje_impoconsumo=:impo
            WHERE id_unico = :id_unico";
            $sql_dato = array(
                array(":valor", $valor),
                array(":iva", $iva),
                array(":impo", $impo),
                array(":id_unico", $id),
            );

            $obj_resp = $this->con->InAcEl($sql_cons, $sql_dato);

            if (empty($obj_resp)) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
