<?php

/**
 * Created by PhpStorm.
 * User: SERVIDOR
 * Date: 26/04/2018
 * Time: 10:06
 */
require_once './clases/concepto_tarifa.php';
require_once './clases/tarifa.php';
require_once './modelFactura/factura.php';
require_once './modelAlmacen/inventario.php';
class conceptoTarifaController
{

    private $concepto;
    private $tarifa;
    private $fat;
    private $inv;

    public function __construct()
    {
        $this->concepto = new concepto_tarifa();
        $this->tarifa   = new tarifa();
        $this->fat      = new factura();
        $this->inv = new inventario();
    }

    public function registrar()
    {
        try {
            $concepto = htmlspecialchars(filter_input(INPUT_POST, 'txtConcepto'), ENT_QUOTES, 'UTF-8');
            $this->concepto->setConcepto($concepto);
            $this->concepto->setTarifa(htmlspecialchars(filter_input(INPUT_POST, 'sltTarifa'), ENT_QUOTES, 'UTF-8'));
            $data = $this->concepto->guardar();
            echo json_encode($data);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function eliminar()
    {
        try {
            $concepto = htmlspecialchars(filter_input(INPUT_POST, 'concepto'), ENT_QUOTES, 'UTF-8');
            $data     = $this->fat->obtenerDataConceptoTarifa($concepto);
            if (count($data) > 0) {
                $xxx = $this->fat->eliminarConceptoTarifa($concepto);
                if ($xxx == true) {
                    $this->fat->eliminarTarifa($data[0][0]);
                    $this->fat->eliminarElementoUnidad($data[0][1]);
                    //$this->fat->eliminarConcepto($data[0][2]);
                }
                echo $data;
            } else {
                echo false;
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function GuardarDataTarifa()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', '1');
        $tarifa                         = new tarifa();
        $tarifa->tipo_taria             = htmlspecialchars(filter_input(INPUT_POST, 'tipo'), ENT_QUOTES, 'UTF-8');
        $tarifa->uso                    = NULL;
        $tarifa->periodo                = NULL;
        $tarifa->estrato                = NULL;
        $tarifa->porcentaje_iva         = $_REQUEST['iva'];
        $tarifa->porcentaje_impoconsumo = $_REQUEST['impo'];
        $tarifa->valor                  = $_REQUEST['valor'];
        $res = $this->tarifa->registrar($tarifa);
        //var_dump($res);
        if ($res == true) {
            $xxx = $this->tarifa->obtenerUltimoRegistro($tarifa->tipo_taria);
            $xel = $this->concepto->registrarUnidadE($_REQUEST['unidad'], $_REQUEST['factor']);
            if ($xel == true) {
                $xug = $this->concepto->obtenerUltimoElementoUnidad();
                $this->concepto->concepto        = $_REQUEST['txtConcepto'];
                $this->concepto->tarifa          = $xxx;
                $this->concepto->nombre          = NULL;
                $this->concepto->elemento_unidad = $xug;
                $this->concepto->porcentajeI     = $_REQUEST['porcentaje'];
                $this->concepto->parametrizacion = $_SESSION['anno'];
                $data = $this->concepto->guardar();
                echo $data;
            }
        }
    }

    public function ModificarTarifa()
    {
        if (!empty($_REQUEST['id'])) {
            if (stripos($_REQUEST['valor'], ",") !== false) {
                $valor = str_replace(",", "", $_REQUEST['valor']);
            } else {
                $valor = $_REQUEST['valor'];
            }
            $data = $this->tarifa->editarTarifa($_REQUEST['id'], $valor, $_REQUEST['iva'], $_REQUEST['impo']);
            if ($data == true) {
                $xelu = $this->fat->obtenerIdElementoUnidad($_REQUEST['concepto'], $_REQUEST['xunidad']);
                if (!empty($xelu)) {
                    $this->fat->modificarElementoUnidad($xelu, $_REQUEST['factor'], $_REQUEST['unidad']);
                    $this->concepto->modificarPorcentaje($_REQUEST['concepto'], $_REQUEST['id'], $xelu, $_REQUEST['porcentaje']);
                }
            }
            echo json_encode(["res" => $data]);
        }
    }

    public function obtenerCantidadElementos()
    {
        $html = "<option value=''>Unidad Empaque</option>";
        if (!empty($_REQUEST['elemento'])) {
            $data = $this->inv->obtenerUnidadF($_REQUEST['elemento']);
            foreach ($data as $row) {
                $html .= "<option value='". $row[0] ."'>". $row[1] ."</option>";
            }
        }
        echo $html;
    }
}
