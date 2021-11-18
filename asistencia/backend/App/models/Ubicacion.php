<?php
namespace App\models;
defined("APPPATH") OR die("Access denied");

use \Core\Database;
use \App\interfaces\Crud;
use \App\controllers\UtileriasLog;

class Ubicacion implements Crud{

    public static function getAll(){

	$mysqli = Database::getInstance();
        $query=<<<sql
        SELECT
          u.catalogo_ubicacion_id,
          u.nombre,
          s.nombre AS status
        FROM catalogo_ubicacion u
        JOIN catalogo_status s
        ON u.status = s.catalogo_status_id WHERE u.status!=2
sql;
        return $mysqli->queryAll($query);
    }

    public static function insert($empresa){
	    $mysqli = Database::getInstance(1);
      $query=<<<sql
        INSERT INTO catalogo_ubicacion VALUES(null, :nombre, :status)
sql;
        $parametros = array(
          ':nombre'=>$empresa->_nombre,
          ':status'=>$empresa->_status
        );
        $id = $mysqli->insert($query,$parametros);
        $accion = new \stdClass();
        $accion->_sql= $query;
        $accion->_parametros = $parametros;
        $accion->_id = $id;

        UtileriasLog::addAccion($accion);
        return $id;
    }


    public static function update($empresa){
      $mysqli = Database::getInstance(true);
      $query=<<<sql
      UPDATE catalogo_ubicacion SET nombre = :nombre, status = :status WHERE catalogo_ubicacion_id = :id
sql;
      $parametros = array(
        ':id'=>$empresa->_catalogo_ubicacion_id,
        ':nombre'=>$empresa->_nombre,
        ':status'=>$empresa->_status
      );
      $accion = new \stdClass();
      $accion->_sql= $query;
      $accion->_parametros = $parametros;
      $accion->_id = $empresa->_catalogo_ubicacion_id;
      UtileriasLog::addAccion($accion);
        return $mysqli->update($query, $parametros);
    }

    public static function delete($id){
      $mysqli = Database::getInstance();
      $select = <<<sql
      SELECT u.catalogo_ubicacion_id FROM catalogo_ubicacion u JOIN catalogo_colaboradores c
      ON u.catalogo_ubicacion_id = c.catalogo_ubicacion_id WHERE u.catalogo_ubicacion_id = $id
sql;
      $sqlSelect = $mysqli->queryAll($select);
      if(count($sqlSelect) >= 1){
        return array('seccion'=>2, 'id'=>$id); // NO elimina
      }else{
        $query = <<<sql
        UPDATE catalogo_ubicacion SET status = '2' WHERE catalogo_ubicacion.catalogo_ubicacion_id = $id;
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
      SELECT cu.catalogo_ubicacion_id, cu.nombre, cu.status, cs.catalogo_status_id, cs.nombre AS nombre_status FROM catalogo_ubicacion AS cu INNER JOIN catalogo_status AS cs WHERE catalogo_ubicacion_id = $id AND cu.status = cs.catalogo_status_id 
sql;

      return $mysqli->queryOne($query);
    }

    public static function getByIdReporte($id){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT
        u.catalogo_ubicacion_id,
        u.nombre,
        s.nombre AS status
      FROM catalogo_ubicacion u
      JOIN catalogo_status s
      ON u.status = s.catalogo_status_id WHERE u.status!=2 AND u.catalogo_ubicacion_id = $id
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

    public static function getNombreUbicacion($nombre_ubicacion){
      $mysqli = Database::getInstance();
      $query =<<<sql
      SELECT * FROM `catalogo_ubicacion` WHERE `nombre` LIKE '$nombre_ubicacion' 
sql;
      $dato = $mysqli->queryOne($query);
      return ($dato>=1) ? 1 : 2 ;
    }

    public static function verificarRelacion($id){
      $mysqli = Database::getInstance();
      $select = <<<sql
      SELECT e.catalogo_ubicacion_id FROM catalogo_ubicacion e JOIN catalogo_colaboradores c ON e.catalogo_ubicacion_id = c.catalogo_ubicacion_id WHERE e.catalogo_ubicacion_id = $id
sql;
      $sqlSelect = $mysqli->queryAll($select);
      if(count($sqlSelect) >= 1)
        return array('seccion'=>2, 'id'=>$id); // NO elimina
      else
        return array('seccion'=>1, 'id'=>$id); // Cambia el status a eliminado
    }
      

}
