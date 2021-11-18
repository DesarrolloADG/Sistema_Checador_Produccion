<?php
namespace App\models;
defined("APPPATH") OR die("Access denied");

use \Core\Database;
use \Core\MasterDom;
use \App\interfaces\Crud;
use \App\controllers\UtileriasLog;

class AdminPeriodo implements Crud{

    public static function getAll(){
      $mysqli = Database::getInstance();
      $query=<<<sql
        SELECT
          prorrateo_periodo_id,
          fecha_inicio,
          fecha_fin,
          tipo,
          status
        FROM prorrateo_periodo
sql;
      return $mysqli->queryAll($query);
    }

    public static function insert($periodo){
	    $mysqli = Database::getInstance(1);
      $query=<<<sql
        INSERT INTO prorrateo_periodo VALUES(NULL, :fecha_inicio, :fecha_fin, :tipo, 0)
sql;
	$parametros = array(
		':fecha_inicio' => $periodo->_fecha_inicio,
		':fecha_fin' => $periodo->_fecha_fin,
		':tipo' => $periodo->_tipo
	);
        $id = $mysqli->insert($query, $parametros);
        $accion = new \stdClass();
        $accion->_sql= $query;
        $accion->_parametros = $parametros;
        $accion->_id = $id;
        UtileriasLog::addAccion($accion);
        return $id;
    }


    public static function update($periodo){
      $mysqli = Database::getInstance(true);
      $query=<<<sql
      UPDATE prorrateo_periodo SET fecha_inicio = :fecha_inicio, fecha_fin = :fecha_fin, tipo = :tipo WHERE prorrateo_periodo_id = :id
sql;
      $parametros = array(
        ':id' => $periodo->_prorrateo_periodo_id,
        ':fecha_inicio' => $periodo->_fecha_inicio,
        ':fecha_fin' => $periodo->_fecha_fin,
        ':tipo' => $periodo->_tipo
      );
      $accion = new \stdClass();
      $accion->_sql= $query;
      $accion->_parametros = $parametros;
      $accion->_id = $periodo->_prorrateo_periodo_id;
      UtileriasLog::addAccion($accion);
        return $mysqli->update($query, $parametros);
    }

    public static function delete($id){
      $mysqli = Database::getInstance();
      $select = <<<sql
      SELECT e.catalogo_empresa_id FROM catalogo_empresa e JOIN catalogo_colaboradores c
      ON e.catalogo_empresa_id = c.catalogo_empresa_id WHERE e.catalogo_empresa_id = $id
sql;

      $sqlSelect = $mysqli->queryAll($select);
      if(count($sqlSelect) >= 1){
        return array('seccion'=>2, 'id'=>$id); // NO elimina
      }else{
        $query = <<<sql
        UPDATE catalogo_empresa SET status = 2 WHERE catalogo_empresa.catalogo_empresa_id = $id;
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
      SELECT ce.catalogo_empresa_id, ce.nombre, ce.descripcion, ce.status, cs.nombre AS nombre_status, cs.catalogo_status_id FROM catalogo_empresa AS ce INNER JOIN catalogo_status AS cs WHERE catalogo_empresa_id = $id AND ce.status = cs.catalogo_status_id
sql;
      return $mysqli->queryOne($query);
    }

    public static function getPeriodos($status){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT * FROM prorrateo_periodo WHERE status = "$status" ORDER BY fecha_inicio ASC 
sql;
      return $mysqli->queryAll($query);
    }

    public static function getPeriodosHistoricos($status, $tipo){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT * FROM prorrateo_periodo WHERE tipo = "$tipo" AND status = 1 ORDER BY fecha_inicio DESC
sql;
      return $mysqli->queryAll($query);
    }

    public static function getPeriodo($idPeriodo){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT * FROM prorrateo_periodo WHERE prorrateo_periodo_id = "$idPeriodo" 
sql;
      return $mysqli->queryOne($query);
    }

    public static function getPeriodoAbierto($tipo){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT * FROM `prorrateo_periodo` WHERE tipo = "$tipo" AND status = 0 OR status = 2 
sql;
      return $mysqli->queryOne($query);
    }

    public static function getPeriodoAbiertoFechas($data){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT * FROM `prorrateo_periodo` WHERE fecha_inicio = "$data->_fecha_inicio" AND fecha_fin = "$data->_fecha_fin" AND tipo LIKE "%$data->_tipo%"
sql;
      $id =  $mysqli->queryOne($query);
      
      $var = 0;
      if($id['status'] == -1){
        $var = '';
      }elseif($id['status'] == 0){
        $var = $id;
      }elseif($id['status'] == 1){
        $var = $id;
      }elseif($id['status'] == 2){
        $var = $id;
      }else{
        $var = '';
      }

      return $var;
      //
    }

    public static function updatePeriodo($idPeriodo, $status){
      $mysqli = Database::getInstance(true);
      $query=<<<sql
      UPDATE prorrateo_periodo SET status = "$status" WHERE prorrateo_periodo_id = "$idPeriodo"
sql;
      return $mysqli->update($query);
    }

    public static function updatePeriodoInfo($data){
      $mysqli = Database::getInstance(true);
      $query=<<<sql
      UPDATE prorrateo_periodo SET fecha_inicio = :fecha_inicio, fecha_fin = :fecha_fin, tipo = :tipo WHERE prorrateo_periodo_id = :id
sql;
      $parametros = array(
        ':id' => $data->_prorrateo_periodo_id,
        ':fecha_inicio' => $data->_fecha_inicio,
        ':fecha_fin' => $data->_fecha_fin,
        ':tipo' => $data->_tipo
      );
      $accion = new \stdClass();
      $accion->_sql= $query;
      $accion->_parametros = $parametros;
      $accion->_id = $periodo->_prorrateo_periodo_id;
      UtileriasLog::addAccion($accion);
      return $mysqli->update($query, $parametros);
    }

    /*
      MODIFICACION MIERCOLES 18 de Abril 2018 
      Cambiar toda la funcion anterior con el mismo nombre ---
    */
    public static function getPeriodoFechas($data){
      $mysqli = Database::getInstance();
      $query=<<<sql
SELECT * FROM prorrateo_periodo WHERE fecha_inicio = :fecha_inicio AND fecha_fin = :fecha_fin AND tipo = :tipo AND status >= 0
sql;
      $params = array(
        ':fecha_inicio'=>$data->_fecha_inicio,
        ':fecha_fin'=>$data->_fecha_fin,
        ':tipo'=>$data->_tipo
      );

      $id =  $mysqli->queryAll($query, $params);
      
      $var = 0;
      if($id['status'] == -1)
        $var = '';
      elseif($id['status'] == 0)
        $var = $id;
      elseif($id['status'] == 1)
        $var = $id;
      elseif($id['status'] == 2)
        $var = $id;
      else
        $var = '';

      return $var;
      //return $mysqli->queryAll($query, $params);
    }
}
