<?php
require_once ('./modelAlmacen/salida.php');
/**
 * Control de Salida
 */
class salidaController{

    private $salida;

    public function __construct(){
        $this->salida = new salida();
    }

    public function obtnerCantidadPlan(){
        if($_REQUEST['sltElemento']){
            $xe = $this->salida->obtnerCantidadProductosPlan($_REQUEST['sltElemento']);
            $xs = $this->salida->obtnerCantidadProductosPlanSalida($_REQUEST['sltElemento']);
            $cantidad = $xe - $xs;
            echo json_encode($cantidad);
        }else{
            echo json_encode(0);
        }
    }

    public function obnterValorU(){
        if($_REQUEST['sltElemento']){
            $xe = $this->salida->obtenerSaldoEntradaPlan($_REQUEST['sltElemento']);
            $xs =$this->salida->obtenerSaldoSalidaPlan($_REQUEST['sltElemento']);
            $xx = $xe - $xs;
            $xsaldoV=$xx;
            $xe1 = $this->salida->obtnerCantidadProductosPlan($_REQUEST['sltElemento']);
            $xs1 = $this->salida->obtnerCantidadProductosPlanSalida($_REQUEST['sltElemento']);
            $xx1 = $xe1 - $xs1;
            $xsaldoC = $xx1;
            $xCantE = $this->salida->obtenerSaldoEntradaPlan($_REQUEST['sltElemento']);
            $xvalor     = 0;
            if(!empty($xsaldoV) || !empty($xsaldoC)){
                $xvalor  = ((( $xsaldoV / $xsaldoC ) * 1 ) / 1 );
                //$xvalor=number_format($xvalor, 2, '.', '');
            }

            if($xsaldoV < 0){

                $xvalor= $this->salida->buscarValorMaximoElemento($_REQUEST['sltElemento']);
            }

            if($xsaldoC < 0 || empty($xsaldoC)){
                if(empty($xCantE)){
                    $xvalor = 0;
                }else{
                    $xvalor= $this->salida->buscarValorMaximoElemento($_REQUEST['sltElemento']);
                }
            }

            echo json_encode($xvalor);
        }else{
            echo json_encode(0);
        }
    }

    public function guardar_detalle(){
        $elemento  = $_REQUEST['sltElemento'];
        $cantidad  = $_REQUEST['txtCantI'];
        $data_sld  = $this->salida->buscarDatosSalida($_REQUEST['id_mov']);
        $res       = $this->salida->guardarDetalleSalida($cantidad, $_REQUEST['txtValorU'], 0, $data_sld[0], 'NULL', $_REQUEST['sltElemento']);
        if($res == true){
            header('location:./jsonAlmacen/json_mov_almacen.php?action=registrado&mov='.$_REQUEST['id_mov']);
        }else{
            header('location:./jsonAlmacen/json_mov_almacen.php?action=noregistrado&mov='.$_REQUEST['id_mov']);
        }
    }
}