<?php
namespace App\models;
defined("APPPATH") OR die("Access denied");

use \Core\Database;
use \App\interfaces\Crud;
use \App\controllers\UtileriasLog;

class Incentivos implements Crud{

    public static function getAll(){
      $mysqli = Database::getInstance();
      $query=<<<sql
        SELECT
          i.catalogo_incentivo_id,
          i.nombre,
          i.descripcion,
          i.color,
          i.fijo,
          i.tipo,
          i.repetitivo,
          s.nombre AS status
        FROM catalogo_incentivo i
        JOIN catalogo_status s
        ON i.status = s.catalogo_status_id
        WHERE i.status != 2
sql;
        return $mysqli->queryAll($query);
    }

    public static function insert($datas){
	    $mysqli = Database::getInstance(1);
      $query=<<<sql
      INSERT INTO catalogo_incentivo (catalogo_incentivo_id, nombre, descripcion, color, fijo, repetitivo, tipo, status) VALUES (NULL, :nombre, :descripcion, :color, :fijo, :repetitivo, :tipo, :status);
sql;
        $parametros = array(
          ':nombre'=>$datas->_nombre,
          ':descripcion'=>$datas->_descripcion,
          ':color'=>$datas->_color,
          ':fijo'=>$datas->_fijo,
          ':repetitivo'=>$datas->_repetitivo,
          ':tipo'=>$datas->_tipo,
          ':status'=>$datas->_status
        );
        $id = $mysqli->insert($query,$parametros);
        $accion = new \stdClass();
        $accion->_sql= $query;
        $accion->_parametros = $parametros;
        $accion->_id = $id;

        UtileriasLog::addAccion($accion);
        return $id;
    }


    public static function update($datas){
      $mysqli = Database::getInstance(true);

      $query=<<<sql
UPDATE catalogo_incentivo SET nombre = :nombre, descripcion = :descripcion, color = :color, fijo = :fijo, repetitivo = :repetitivo, tipo = :tipo, status = :status WHERE catalogo_incentivo.catalogo_incentivo_id = :catalogo_incentivo_id;
sql;
      $parametros = array(
        ':catalogo_incentivo_id'=>$datas->_catalogo_incentivo_id,
        ':nombre'=>$datas->_nombre,
        ':descripcion'=>$datas->_descripcion,
        ':color'=>$datas->_color,
        ':fijo'=>$datas->_fijo,
        ':repetitivo'=>$datas->_repetitivo,
        ':tipo'=>$datas->_tipo,
        ':status'=>$datas->_status
      );
      $accion = new \stdClass();
      $accion->_sql= $query;
      $accion->_parametros = $parametros;
      $accion->_id = $datas->_catalogo_incentivo_id;
      UtileriasLog::addAccion($accion);
      return $mysqli->update($query, $parametros);
    }



    public static function delete($id){
      $mysqli = Database::getInstance();
      $select = <<<sql
      SELECT ci.catalogo_incentivo_id, ic.catalogo_incentivo_id FROM catalogo_incentivo AS ci INNER JOIN incentivo_colaborador AS ic On ci.catalogo_incentivo_id = ic.catalogo_incentivo_id WHERE ci.catalogo_incentivo_id = $id
sql;

      $sqlSelect = $mysqli->queryAll($select);
      if(count($sqlSelect) >= 1){
        return array('seccion'=>2, 'id'=>$id); // NO elimina
      }else{
        $query = <<<sql
        UPDATE catalogo_incentivo SET status = '2' WHERE catalogo_incentivo.catalogo_incentivo_id = $id;
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
      SELECT ci.catalogo_incentivo_id, ci.repetitivo, ci.nombre, ci.descripcion, ci.color, ci.fijo, ci.tipo, ci.status, cs.catalogo_status_id, cs.nombre AS nombre_status FROM catalogo_incentivo AS ci INNER JOIN catalogo_status AS cs WHERE ci.catalogo_incentivo_id = $id AND ci.status = cs.catalogo_status_id
sql;
      return $mysqli->queryOne($query);
    }

    public static function getByIdReporte($id){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT
        i.catalogo_incentivo_id,
        i.nombre,
        i.descripcion,
        i.color,
        s.nombre AS status
      FROM catalogo_incentivo i
      JOIN catalogo_status s
      ON i.status = s.catalogo_status_id
      WHERE i.status != 2 AND i.catalogo_incentivo_id = $id
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


    public static function getUbicacionId(){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT * FROM catalogo_ubicacion
sql;
        return $mysqli->queryAll($query);
    }

    public static function getNombreIncentivo($nombre_incentivo){
      $mysqli = Database::getInstance();
      $query =<<<sql
      SELECT * FROM `catalogo_incentivo` WHERE `nombre` LIKE '$nombre_incentivo'
sql;
      $dato = $mysqli->queryOne($query);
      return ($dato>=1) ? 1 : 2 ;
    }

    public static function verificarRelacion($id){
      $mysqli = Database::getInstance();
      $select = <<<sql
      SELECT ci.catalogo_incentivo_id, ic.catalogo_incentivo_id FROM catalogo_incentivo AS ci INNER JOIN incentivo_colaborador AS ic On ci.catalogo_incentivo_id = ic.catalogo_incentivo_id WHERE ci.catalogo_incentivo_id = $id
sql;
      $sqlSelect = $mysqli->queryAll($select);
      if(count($sqlSelect) >= 1)
        return array('seccion'=>2, 'id'=>$id); // NO elimina
      else
        return array('seccion'=>1, 'id'=>$id); // Cambia el status a eliminado
      
    }

}
