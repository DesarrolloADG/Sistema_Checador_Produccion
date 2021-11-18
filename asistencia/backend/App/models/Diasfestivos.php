<?php
namespace App\models;
defined("APPPATH") OR die("Access denied");

use \Core\Database;
use \App\interfaces\Crud;
use \App\controllers\UtileriasLog;

class Diasfestivos implements Crud{

    public static function getAll(){

	$mysqli = Database::getInstance();
        $query=<<<sql
        SELECT
          d.catalogo_dia_festivo_id,
          d.nombre,
          d.descripcion,
          d.fecha,
          s.nombre AS status
        FROM catalogo_dia_festivo d
        JOIN catalogo_status s
        on d.status = s.catalogo_status_id
        WHERE d.status != 2
sql;
        return $mysqli->queryAll($query);
    }

    public static function insert($datos){
	    $mysqli = Database::getInstance(1);
      $query=<<<sql
      INSERT INTO catalogo_dia_festivo (catalogo_dia_festivo_id, nombre, descripcion, fecha, status) VALUES (NULL, :nombre, :descripcion, :fecha, :status);
sql;

    	$parametros = array(
    		':nombre'=>$datos->_nombre,
    		':descripcion'=>$datos->_descripcion,
    		':fecha'=>$datos->_fecha,
        ':status'=>$datos->_status
    	);

      $id = $mysqli->insert($query,$parametros);
      $accion = new \stdClass();
      $accion->_sql= $query;
      $accion->_parametros = $parametros;
      $accion->_id = $id;

      UtileriasLog::addAccion($accion);
      return $id;
    }


    public static function update($datos){
      $mysqli = Database::getInstance(true);
      $query=<<<sql
UPDATE catalogo_dia_festivo SET nombre = :nombre, descripcion = :descripcion, fecha = :fecha, status = :status WHERE catalogo_dia_festivo.catalogo_dia_festivo_id = :catalogo_dia_festivo_id;
sql;
      $parametros = array(
          ':catalogo_dia_festivo_id'=>$datos->_catalogo_dia_festivo_id,
          ':nombre'=>$datos->_nombre,
          ':descripcion'=>$datos->_descripcion,
          ':fecha'=>$datos->_fecha,
          ':status'=>$datos->_status
        );
        $accion = new \stdClass();
        $accion->_sql= $query;
        $accion->_parametros = $parametros;
        $accion->_id = $datos->_catalogo_dia_festivo_id;
        UtileriasLog::addAccion($accion);
        return $mysqli->update($query, $parametros);
    }

    public static function delete($id){
      $mysqli = Database::getInstance();
      $query=<<<sql
      UPDATE catalogo_dia_festivo SET status = 2 WHERE catalogo_dia_festivo_id = $id
sql;
      $parametros = array(':id'=>$id);
      $accion = new \stdClass();
      $accion->_sql= $query;
      $accion->_parametros = $parametros;
      $accion->_id = $id;
      UtileriasLog::addAccion($accion);
      return $mysqli->update($query, $parametros);
    }

    public static function deleteById($id){
        $mysqli = Database::getInstance();
        $query=<<<sql
DELETE FROM catalogo_dia_festivo WHERE catalogo_dia_festivo.catalogo_dia_festivo_id = $id
sql;
      $accion = new \stdClass();
      $accion->_sql= $query;
      $accion->_parametros = $parametros;
      UtileriasLog::addAccion($accion);
        return $mysqli->queryOne($query);
    }

    public static function getById($id){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT cdf.catalogo_dia_festivo_id, cdf.nombre, cdf.descripcion, cdf.fecha, cdf.status, cs.catalogo_status_id, cs.nombre AS nombre_status FROM catalogo_dia_festivo AS cdf INNER JOIN catalogo_status AS cs WHERE cdf.catalogo_dia_festivo_id = $id AND cdf.status = cs.catalogo_status_id
sql;

      return $mysqli->queryOne($query);
    }

    public static function getByIdReporte($id){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT
          d.catalogo_dia_festivo_id,
          d.nombre,
          d.descripcion,
          d.fecha,
          s.nombre AS status
        FROM catalogo_dia_festivo d
        JOIN catalogo_status s
        on d.status = s.catalogo_status_id
        WHERE d.status != 2 AND d.catalogo_dia_festivo_id = $id
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

    public static function getNombreDiaFestivo($nombre_dia_festivo){
      $mysqli = Database::getInstance();
      $query =<<<sql
      SELECT * FROM catalogo_dia_festivo WHERE nombre LIKE '$nombre_dia_festivo' 
sql;
      $dato = $mysqli->queryOne($query);
      return ($dato>=1) ? 1 : 2 ;
    }

}
