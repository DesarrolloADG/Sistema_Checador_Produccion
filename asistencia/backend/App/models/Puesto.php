<?php
namespace App\models;
defined("APPPATH") OR die("Access denied");

use \Core\Database;
use \App\interfaces\Crud;
use \App\controllers\UtileriasLog;

class Puesto implements Crud{

    public static function getAll(){

	$mysqli = Database::getInstance();
        $query=<<<sql
        SELECT
          p.catalogo_puesto_id,
          p.nombre,
          p.descripcion,
          s.nombre AS status
        FROM catalogo_puesto p
        JOIN catalogo_status s
        ON p.status = s.catalogo_status_id
        WHERE p.status != 2
sql;
        return $mysqli->queryAll($query);
    }

    public static function insert($puesto){
	    $mysqli = Database::getInstance(1);
      $query=<<<sql
        INSERT INTO catalogo_puesto VALUES(
          null,
          :nombre,
          :descripcion,
          :status,
          :numero_incentivos,
          :total_valor_incentivos,
          :incentivo1,
          :porcentaje1,
          :incentivo2,
          :porcentaje2,
          :incentivo3,
          :porcentaje3,
          :incentivo4,
          :porcentaje4,
          :incentivo5,
          :porcentaje5,
          :incentivo6,
          :porcentaje6,
          :incentivo7,
          :porcentaje7,
          :incentivo8,
          :porcentaje8,
          :incentivo9,
          :porcentaje9,
          :incentivo10,
          :porcentaje10
        )
sql;
        $parametros = array(
          ':nombre'=>$puesto->_nombre,
          ':descripcion'=>$puesto->_descripcion,
          ':status'=>$puesto->_status,
          ':numero_incentivos'=>$puesto->_numeroi,
          ':total_valor_incentivos'=>$puesto->_ti,
          ':incentivo1'=>$puesto->_incentivo1,
          ':porcentaje1'=>$puesto->_por1,
          ':incentivo2'=>$puesto->_incentivo2,
          ':porcentaje2'=>$puesto->_por2,
          ':incentivo3'=>$puesto->_incentivo3,
          ':porcentaje3'=>$puesto->_por3,
          ':incentivo4'=>$puesto->_incentivo4,
          ':porcentaje4'=>$puesto->_por4,
          ':incentivo5'=>$puesto->_incentivo5,
          ':porcentaje5'=>$puesto->_por5,
          ':incentivo6'=>$puesto->_incentivo6,
          ':porcentaje6'=>$puesto->_por6,
          ':incentivo7'=>$puesto->_incentivo7,
          ':porcentaje7'=>$puesto->_por7,
          ':incentivo8'=>$puesto->_incentivo8,
          ':porcentaje8'=>$puesto->_por8,
          ':incentivo9'=>$puesto->_incentivo9,
          ':porcentaje9'=>$puesto->_por9,
          ':incentivo10'=>$puesto->_incentivo10,
          ':porcentaje10'=>$puesto->_por10
        );
        $id = $mysqli->insert($query,$parametros);
        $accion = new \stdClass();
        $accion->_sql= $query;
        $accion->_parametros = $parametros;
        $accion->_id = $id;

        UtileriasLog::addAccion($accion);
        return $id;
    }


    public static function update($puesto){
      $mysqli = Database::getInstance(true);
      $query=<<<sql
      UPDATE catalogo_puesto SET
        nombre = :nombre,
        descripcion = :descripcion,
        status = :status,
        numero_incentivos = :numero_incentivos,
        total_valor_incentivos = :total_valor_incentivos,
        incentivo1 = :incentivo1,
        porcentaje1 = :porcentaje1,
        incentivo2 = :incentivo2,
        porcentaje2 = :porcentaje2,
        incentivo3 = :incentivo3,
        porcentaje3 = :porcentaje3,
        incentivo4 = :incentivo4,
        porcentaje4 = :porcentaje4,
        incentivo5 = :incentivo5,
        porcentaje5 = :porcentaje5,
        incentivo6 = :incentivo6,
        porcentaje6 = :porcentaje6,
        incentivo7 = :incentivo7,
        porcentaje7 = :porcentaje7,
        incentivo8 = :incentivo8,
        porcentaje8 = :porcentaje8,
        incentivo9 = :incentivo9,
        porcentaje9 = :porcentaje9,
        incentivo10 = :incentivo10,
        porcentaje10 = :porcentaje10
      WHERE catalogo_puesto_id = :id
sql;
      $parametros = array(
        ':id'=>$puesto->_catalogo_puesto_id,
        ':nombre'=>$puesto->_nombre,
        ':descripcion'=>$puesto->_descripcion,
        ':status'=>$puesto->_status,
        ':numero_incentivos'=>$puesto->_numeroi,
        ':total_valor_incentivos'=>$puesto->_ti,
        ':incentivo1'=>$puesto->_incentivo1,
        ':porcentaje1'=>$puesto->_por1,
        ':incentivo2'=>$puesto->_incentivo2,
        ':porcentaje2'=>$puesto->_por2,
        ':incentivo3'=>$puesto->_incentivo3,
        ':porcentaje3'=>$puesto->_por3,
        ':incentivo4'=>$puesto->_incentivo4,
        ':porcentaje4'=>$puesto->_por4,
        ':incentivo5'=>$puesto->_incentivo5,
        ':porcentaje5'=>$puesto->_por5,
        ':incentivo6'=>$puesto->_incentivo6,
        ':porcentaje6'=>$puesto->_por6,
        ':incentivo7'=>$puesto->_incentivo7,
        ':porcentaje7'=>$puesto->_por7,
        ':incentivo8'=>$puesto->_incentivo8,
        ':porcentaje8'=>$puesto->_por8,
        ':incentivo9'=>$puesto->_incentivo9,
        ':porcentaje9'=>$puesto->_por9,
        ':incentivo10'=>$puesto->_incentivo10,
        ':porcentaje10'=>$puesto->_por10
      );
      $accion = new \stdClass();
      $accion->_sql= $query;
      $accion->_parametros = $parametros;
      $accion->_id = $puesto->_catalogo_puesto_id;
      UtileriasLog::addAccion($accion);
      return $mysqli->update($query, $parametros);
    }

    public static function delete($id){
      $mysqli = Database::getInstance();
      $select = <<<sql
      SELECT e.catalogo_puesto_id FROM catalogo_puesto e JOIN catalogo_colaboradores c
      ON e.catalogo_puesto_id = c.catalogo_puesto_id WHERE e.catalogo_puesto_id = $id
sql;

      $sqlSelect = $mysqli->queryAll($select);
      if(count($sqlSelect) >= 1){
        return array('seccion'=>2, 'id'=>$id); // NO elimina
      }else{
        $query = <<<sql
        UPDATE catalogo_puesto SET status = '2' WHERE catalogo_puesto.catalogo_puesto_id = $id;
sql;
        $mysqli->update($query);
        $accion = new \stdClass();
        $accion->_sql= $query;
        $accion->_parametros = $parametros;
        $accion->_id = $id;
        UtileriasLog::addAccion($accion);
        return array('seccion'=>1, 'id'=>$id); // Cambia el status a eliminado
      }
    }

    public static function getById($id){
      $mysqli = Database::getInstance();
      $query=<<<sql
        SELECT
          cp.catalogo_puesto_id,
          cp.nombre,
          cp.descripcion,
          cp.status,
          cp.numero_incentivos,
          cp.total_valor_incentivos,
          cp.incentivo1,
          cp.porcentaje1,
          cp.incentivo2,
          cp.porcentaje2,
          cp.incentivo3,
          cp.porcentaje3,
          cp.incentivo4,
          cp.porcentaje4,
          cp.incentivo5,
          cp.porcentaje5,
          cp.incentivo6,
          cp.porcentaje6,
          cp.incentivo7,
          cp.porcentaje7,
          cp.incentivo8,
          cp.porcentaje8,
          cp.incentivo9,
          cp.porcentaje9,
          cp.incentivo10,
          cp.porcentaje10,
          cs.catalogo_status_id,
          cs.nombre AS nombre_status
        FROM catalogo_puesto AS cp
        INNER JOIN catalogo_status AS cs
        WHERE
          catalogo_puesto_id = $id AND
          cp.status = cs.catalogo_status_id
sql;
// print_r($query);
      return $mysqli->queryOne($query);
    }
//MRR
    public static function incentivoAsignado($ppi,$cid,$cii){
      $mysqli = Database::getInstance();
      $query =<<<sql
      SELECT
      	incentivos_asignados_id AS 'iai'
      FROM incentivos_asignados
      WHERE prorrateo_periodo_id = $ppi AND
        colaborador_id = $cid AND
        catalogo_incentivo_id = $cii
sql;
    // print_r($query);
      return $mysqli->queryOne($query);
    }

    public static function insertEficiencia($eficiencia){
	    $mysqli = Database::getInstance(1);
      $query=<<<sql
        INSERT INTO incentivos_porcentaje_eficiencia VALUES(
          null,
          :id_periodo_prorrateo,
          :porcentaje,
          :tipo,
          1
        )
sql;
      $parametros = array(
        ':id_periodo_prorrateo'=>$eficiencia->_id_periodo_prorrateo,
        ':porcentaje'=>$eficiencia->_porcentaje,
        ':tipo'=>$eficiencia->_tipo
      );

      $id = $mysqli->insert($query,$parametros);
      $accion = new \stdClass();
      $accion->_sql= $query;
      $accion->_parametros = $parametros;
      $accion->_id = $id;

      UtileriasLog::addAccion($accion);
      return $id;
    }

    public static function getEficiencia($ppi,$tipo){
      $mysqli = Database::getInstance();
      $query =<<<sql
      SELECT
      	porcentaje
      FROM incentivos_porcentaje_eficiencia
      WHERE
        id_periodo_prorrateo = $ppi AND
        tipo = '$tipo' AND
        id_porcentaje = (SELECT
		      MAX(id_porcentaje)
	      FROM incentivos_porcentaje_eficiencia
	      WHERE
		      id_periodo_prorrateo = $ppi AND
		      tipo = '$tipo')
sql;
    // print_r($query);
      return $mysqli->queryOne($query);
    }


    public static function incentivoC($id){
      $mysqli = Database::getInstance();
      $query =<<<sql
        SELECT
          catalogo_colaboradores_id AS 'cci',
        	catalogo_incentivo_id AS 'cii',
          cantidad AS 'c'
        FROM incentivo_colaborador
        WHERE catalogo_colaboradores_id = $id
sql;
    // print_r($query);
      return $mysqli->queryAll($query);
    }

//MRR
    public static function ultimoPeriodo($tipo){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT
        MAX(prorrateo_periodo_id) AS 'ppi'
      FROM `prorrateo_periodo`
      WHERE
        tipo = '$tipo' AND
        status = 0 OR status = 2
sql;
    // print_r($query);
      return $mysqli->queryOne($query);
    }
    public static function colaboradores(){
      $mysqli = Database::getInstance();
      $query =<<<sql
      SELECT
        c.catalogo_colaboradores_id as cci,
        cp.numero_incentivos as ni,
        c.catalogo_puesto_id as cpi
      FROM catalogo_colaboradores c
      INNER JOIN catalogo_puesto AS cp USING (catalogo_puesto_id)
      WHERE
        c.pago = 'SEMANAL' AND
        c.identificador_noi IN ('GATSA','UNIDESH')
sql;
    // print_r($query);
      return $mysqli->queryAll($query);
    }
    public static function updIncentivoP($cci,$cii,$cant){
      $mysqli = Database::getInstance(true);
      $query =<<<sql
        UPDATE incentivo_colaborador SET
          catalogo_incentivo_id = :cii,
          cantidad = :c
        WHERE
          catalogo_colaboradores_id = :cci AND catalogo_incentivo_id = :cii
sql;
      $data = array(
        ':cci'=>$cci,
        ':cii'=>$cii,
        ':c'=>$cant
      );
      return $mysqli->update($query,$data);
    }

    public static function delp($id){
      $mysqli = Database::getInstance();
      $query =<<<sql
      DELETE
      FROM incentivo_colaborador
      WHERE catalogo_colaboradores_id = $id
sql;
      return $mysqli->update($query);
    }

    public static function getIncentivos($puesto){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT * FROM catalogo_puesto WHERE catalogo_puesto_id = $puesto
sql;
// print_r($query);
      return $mysqli->queryOne($query);
    }

    public static function insertIncentivo($incentivo){

	    $mysqli = Database::getInstance();
      $query=<<<sql
      INSERT INTO incentivo_colaborador
      VALUES ($incentivo->_catalogo_colaboradores_id , $incentivo->_catalogo_incentivo_id,$incentivo->_cantidad);
sql;
      $accion = new \stdClass();
      $accion->_sql= $query;
      $accion->_parametros = $parametros;
      UtileriasLog::addAccion($accion);
      return $mysqli->insert($query);
    }

    public static function getIn($cci,$cii){
      $mysqli = Database::getInstance();
      $query = <<<sql
      SELECT
        catalogo_incentivo_id,
      	cantidad
      FROM incentivo_colaborador
      WHERE
        catalogo_colaboradores_id = $cci AND
        catalogo_incentivo_id = $cii
sql;
    return $mysqli->queryOne($query);
    }

    public static function getpid(){
      $mysqli = Database::getInstance();
      $query = <<<sql
      SELECT
      	prorrateo_periodo_id as 'ppi'
      FROM prorrateo_periodo
      WHERE
      	tipo = 'SEMANAL' AND
      	status = 0 OR status = 3
sql;
    return $mysqli->queryOne($query);
    }

    public static function insertIncentivos($data){
        $mysqli = Database::getInstance();
        $query=<<<sql
        INSERT INTO incentivos_asignados (
          colaborador_id,
          prorrateo_periodo_id,
          catalogo_incentivo_id,
          cantidad,
          asignado,
          valido
        )
        VALUES (
          :colaborador_id,
          :prorrateo_periodo_id,
          :catalogo_incentivo_id,
          :cantidad,
          :asignado,
          :valido
        )
sql;
        $params = array(
            ':colaborador_id'=>$data->_colaborador_id,
            ':prorrateo_periodo_id'=>$data->_prorrateo_periodo_id,
            ':catalogo_incentivo_id'=>$data->_catalogo_incentivo_id,
            ':cantidad'=>$data->_cantidad,
            ':asignado'=>$data->_asignado,
            ':valido'=>$data->_valido
        );
        return $mysqli->insert($query,$params);
    }

    public static function colaboradoresPuesto($id){
      $mysqli = Database::getInstance();
      $query =<<<sql
        SELECT
        	catalogo_colaboradores_id AS 'cci'
        FROM catalogo_colaboradores
        WHERE catalogo_puesto_id = $id
sql;
    // print_r($query);
      return $mysqli->queryAll($query);
    }

    public static function getIa($id,$pid){//,$cii
      $mysqli = Database::getInstance();
      $query =<<<sql
        SELECT
          incentivos_asignados_id AS 'iai'
        FROM incentivos_asignados
        WHERE
          colaborador_id = $id AND
          prorrateo_periodo_id = $pid
sql;
#AND
#catalogo_incentivo_id = $cii
    // print_r($query);
      return $mysqli->queryAll($query);
    }

    public static function delIa($getIa){
      $mysqli = Database::getInstance();
      $query =<<<sql
        DELETE
        FROM incentivos_asignados
        WHERE
        incentivos_asignados_id = $getIa
sql;
    // print_r($query);
      return $mysqli->delete($query);
    }
    public static function updIncentivoA($data){
      $mysqli = Database::getInstance(true);
      $query =<<<sql
        UPDATE incentivos_asignados SET cantidad = :cantidad WHERE incentivos_asignados_id = :iai
sql;
      $data = array(
        ':iai'=>$data->_iai,
        ':cantidad'=>$data->_cantidad
      );
      return $mysqli->update($query, $data);
    }

    public static function incentivop($id){
      $mysqli = Database::getInstance();
      $query=<<<sql
        SELECT
          nombre
        FROM catalogo_incentivo
        WHERE
          catalogo_incentivo_id = $id
sql;
// print_r($query);
      return $mysqli->queryOne($query);
    }

    public static function getByIdReporte($id){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT
        p.catalogo_puesto_id,
        p.nombre,
        p.descripcion,
        s.nombre AS status
      FROM catalogo_puesto p
      JOIN catalogo_status s
      ON p.status = s.catalogo_status_id
      WHERE p.status != 2 AND p.catalogo_puesto_id = $id
sql;

      return $mysqli->queryOne($query);
    }

    public static function getStatus(){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT * FROM catalogo_status
sql;
      return $mysqli->queryAll($query);
    }
    public static function getIncentivo(){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT * FROM catalogo_incentivo
sql;
      return $mysqli->queryAll($query);
    }

    public static function getNombrePuesto($nombre_puesto){
      $mysqli = Database::getInstance();
      $query =<<<sql
      SELECT * FROM catalogo_puesto WHERE nombre LIKE '$nombre_puesto'
sql;
      $dato = $mysqli->queryOne($query);
      return ($dato>=1) ? 1 : 2 ;
    }

    public static function verificarRelacion($id){
      $mysqli = Database::getInstance();
      $select = <<<sql
      SELECT cp.catalogo_puesto_id FROM catalogo_puesto cp JOIN catalogo_colaboradores c ON cp.catalogo_puesto_id = c.catalogo_puesto_id WHERE cp.catalogo_puesto_id = $id
sql;
// print_r($select);
      $sqlSelect = $mysqli->queryAll($select);
      if(count($sqlSelect) >= 1)
        return array('seccion'=>2, 'id'=>$id); // NO elimina
      else
        return array('seccion'=>1, 'id'=>$id); // Cambia el status a eliminado

    }
}
