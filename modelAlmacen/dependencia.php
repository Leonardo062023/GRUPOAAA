<?php
require_once ('../Conexion/db.php');
class dependencia{
	public $id_unico;
	public $nombre;
	public $sigla;
	public $movimiento;
	public $activa;
	public $compania;
	public $centrocosto;
	public $tipodependencia;

	private $mysqli;

    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    

    public function obtener_tipo_dep($nombre){
        try {

          $sql = "SELECT id_unico FROM gf_tipo_dependencia WHERE nombre LIKE '%$nombre%' ";
          $res = $this->mysqli->query($sql);
            return $res->fetch_row();
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtenerTercero($identificacion, $compania){
        try {
          $sql = "SELECT id_unico
                    FROM gf_tercero
                    WHERE cast(numeroidentificacion as unsigned) = '$identificacion' AND compania = $compania ";
          $res = $this->mysqli->query($sql);
            return $res->fetch_row();
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    
    public function obtenerElemento($codigo, $compania){
        try {
          $sql = "SELECT codi
                        FROM gf_plan_inventario
                        WHERE codi = '$codigo' AND compania = $compania";
          $res = $this->mysqli->query($sql);
            return $res->fetch_row();
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    
    public function registrar_dep(dependencia $data){
        try {
    $sql = "INSERT INTO gf_dependencia(
                                    sigla,
                                    nombre,
                                    movimiento,
                                    activa,
                                    compania,
                                    centrocosto,
                                    tipodependencia                                  
                                ) VALUES(
                                   '$data->sigla',
                                   '$data->nombre',
                                   \"$data->movimiento\",
                                   \"$data->activa\",
                                   \"$data->compania\",
                                   \"$data->centro\",
                                   \"$data->tipo\"                                 
                                );";
            $res = $this->mysqli->query($sql);

            if($res == true){
                $x = 1;
            }else{
                $x = 0;
            }

            return $x;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function registrar_dep_resp(dependencia $data){
        try {
    $sql = "INSERT INTO gf_dependencia_responsable(
                                    dependencia,
                                    responsable,
                                    movimiento,
                                    estado
                                ) VALUES(
                                    $data->dependencia,
                                    $data->responsable,
                                    $data->movimiento,
                                    $data->estado
                                );";
            $res = $this->mysqli->query($sql);
            if($res == true){
                $x = 1;
            }else{
                $x = 0;
            }
            return $x;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
        
    public function obtenerDependencia($sigla, $compania){
        try {
            $sql = "SELECT id_unico FROM gf_dependencia WHERE sigla = '$sigla' AND compania = $compania";
            $res = $this->mysqli->query($sql);
            return $res->fetch_row();
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    
    public function obtnerDependenciaresponsable($dependencia, $responsable){
        try{
            $str = "SELECT    dependencia
                    FROM      gf_dependencia_responsable
                    WHERE     dependencia = $dependencia AND responsable = $responsable";
            $res = $this->mysqli->query($str);
            return $res->fetch_row();
        }catch (Exception $e){
            die($e->getMessage());
        }
    }
}


