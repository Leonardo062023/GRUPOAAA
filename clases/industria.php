<?php
require_once './Conexion/db.php';
class industria{

    private $mysqli;

    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    public function obtenerListadoContribuyentes($term){
        try {
            $str = "SELECT    gce.id_unico, 
                              (
                                IF(
                                  CONCAT_WS(' ', gtr.nombreuno, gtr.nombredos, gtr.apellidouno, gtr.apellidodos) = ' ',
                                  gtr.razonsocial,
                                  CONCAT_WS(' ', gtr.nombreuno, gtr.nombredos, gtr.apellidouno, gtr.apellidodos)
                                )
                              ) AS nomce
                    FROM      gc_contribuyente AS gce
                    LEFT JOIN gf_tercero       AS gtr ON gce.tercero = gtr.id_unico
                    WHERE     (gtr.numeroidentificacion LIKE '%$term%')
                    OR        (gce.codigo_mat           LIKE '%$term%')
                    OR        ( CONCAT_WS(' ', gtr.nombreuno, gtr.nombredos, gtr.apellidouno, gtr.apellidodos)  LIKE '%$term%' )
                    OR        ( gtr.razonsocial LIKE '%$term%')";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerCompania($id){
        try {
            $data = array();
            $str  = "SELECT UPPER(ter.razonsocial), CONCAT_WS(' ',ter.numeroidentificacion, ter.digitoverficacion), tel.valor, dir.direccion, ciu.nombre, dep.nombre, ter.ruta_logo 
                     FROM gf_tercero AS ter 
                     LEFT JOIN gf_telefono     AS tel ON ter.id_unico         = tel.tercero
                     LEFT JOIN gf_direccion    AS dir ON ter.id_unico         = dir.tercero
                     LEFT JOIN gf_ciudad       AS ciu ON dir.ciudad_direccion = ciu.id_unico
                     LEFT JOIN gf_departamento AS dep ON ciu.departamento     = dep.id_unico
                     WHERE ter.id_unico = $id";
            $res  = $this->mysqli->query($str);
            if($res->num_rows > 0){
                $row = $res->fetch_row();
                $data['razon'] = $row[0];
                $data['nit']   = $row[1];
                $data['tel']   = $row[2];
                $data['dir']   = $row[3];
                $data['ciu']   = $row[4];
                $data['dep']   = $row[5];
                $data['log']   = $row[6];
            }
            return $data;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerDataContribuyente($id){
        try {
            $xxx = array();
            echo $str = "SELECT    gct.id_unico,
                              (
                                  IF(
                                      gtr.razonsocial IS NULL,
                                      CONCAT_WS(' ', gtr.nombreuno, gtr.nombredos, gtr.apellidouno, gtr.apellidodos),
                                      gtr.razonsocial                                   
                                  )
                              ) AS razon,
                              gct.codigo_mat,
                              (
                                  IF(
                                      CONCAT_WS(' ', grp.nombreuno, grp.nombredos, grp.apellidouno, grp.apellidodos) = ' ',
                                      grp.razonsocial,
                                      CONCAT_WS(' ', grp.nombreuno, grp.nombredos, grp.apellidouno, grp.apellidodos)
                                  )
                              ) AS rep,
                              CONCAT_WS(' ', gtr.numeroidentificacion, gtr.digitoverficacion) AS num,
                              gct.dir_correspondencia,
                              gct.telefono,
                              UPPER(gtm.nombre)
                                   
                    FROM      gc_contribuyente AS gct
                    LEFT JOIN gf_tercero       AS gtr ON gct.tercero     = gtr.id_unico
                    LEFT JOIN gf_tercero       AS grp ON gct.repre_legal = grp.id_unico
                    LEFT JOIN gf_tipo_regimen  AS gtm ON grp.tiporegimen = gtm.id_unico
                    WHERE  md5(gct.id_unico)  = '$id'";
            $res = $this->mysqli->query($str);
            if($res->num_rows > 0){
                $row = $res->fetch_row();
                $xxx['id']    = $row[0];
                $xxx['razon'] = $row[1];
                $xxx['cod']   = $row[2];
                $xxx['rep']   = $row[3];
                $xxx['nit']   = $row[4];
                $xxx['dir']   = $row[5];
                $xxx['tel']   = $row[6];
                $xxx['tpr']   = $row[7];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerUltimaFechaPago($contribuyente){
        try {
            $xxx = 0;
            $str = "SELECT    DATE_FORMAT(MAX(grc.fecha), '%d/%m/%Y') AS fecha
                    FROM      gc_recaudo_comercial AS grc
                    LEFT JOIN gc_declaracion       AS gdc ON grc.declaracion = gdc.id_unico
                    WHERE     gdc.contribuyente = $contribuyente";
            $res = $this->mysqli->query($str);
            if($res->num_rows > 0){
                $row = $res->fetch_row();
                $xxx = $row[0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}