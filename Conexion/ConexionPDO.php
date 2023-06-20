<?php
$_SESSION['conexion'] = 2;
class ConexionPDO
{

	public $db_serv = "192.168.20.13";
	public $db_nomb = "orcl";
	public $db_usua = "sigiep1"; //Usuario base de datos
	public $db_clav = "123"; // Clave de la base de datos

	public $obj_resu; //Objeto que contiene el resultado
	//Se realiza el primer piloto de GITHUB
	/**********************************************
	 Inicializacion de variable de la base de datos
	 ***********************************************/
	public function MET_CONEXION()
	{
		try {
			$dsn = 'oci:dbname=(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=' . $this->db_serv . ')(PORT=1521))(CONNECT_DATA=(SID=' . $this->db_nomb . ')))';
			$this->obj_resu = new PDO($dsn, $this->db_usua, $this->db_clav);
			$this->obj_resu->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (Exception $e) {
			$error = $e->getMessage();
			// Realiza acciones adicionales con el mensaje de error, como imprimirlo o almacenarlo en un archivo de registro
			// Ejemplo: echo $error; o file_put_contents('error.log', $error, FILE_APPEND);
			return $error;
		}
		return $this->obj_resu;
	}
	/**********************************************
	 METODO DE LISTAR
	 ***********************************************/
	public function Listar($arg_cons)
	{
		$loc_cone = null; //Conexion
		$loc_coma = null; //Comandos
		$loc_rows = null; //Filas de la consulta
		$loc_resu = null; //Resultado

		try {
			$loc_cone = $this->MET_CONEXION();
			$loc_coma = $loc_cone->prepare($arg_cons);
			$loc_coma->execute();

			while ($loc_rows = $loc_coma->fetch()) {
				$loc_resu[] = $loc_rows;
			}
		} catch (Exception $e) {
			$this->obj_resu = null;
			$this->obj_resu = $e->getMessage();
		}
		return $loc_resu;
	}
	/**********************************************
	 METODO DE INSERTAR, ACTUALIZAR Y ELIMINAR
	 ***********************************************/
	public function InAcEl($arg_cons, $arg_data)
	{
		$obj_resu = "";

		$loc_cone = null; //Conexion
		$loc_coma = null; //Comandos
		try {

			$loc_cone = $this->MET_CONEXION();
			$loc_coma = $loc_cone->prepare($arg_cons);

			for ($i = 0; $i < count($arg_data); $i++) {
				$loc_coma->bindParam($arg_data[$i][0], $arg_data[$i][1]);
			}
			if (!$loc_coma) {
				$this->obj_resu = "Error al crear el registro";
			} else {
				$loc_coma->execute();
			}
		} catch (Exception $e) {
			$obj_resu = $e->getMessage();
		}

		return $obj_resu;
	}
}
