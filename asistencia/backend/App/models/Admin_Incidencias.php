<?php
namespace App\models;
defined("APPPATH") OR die("Access denied");

use \Core\Database;
use \App\interfaces\Crud;
use \App\controllers\UtileriasLog;

class Admin_Incidencias implements Crud{

    public static function getAll(){
      $mysqli = Database::getInstance();
      $query=<<<sql
        SELECT
          i.catalogo_incidencia_id,
          i.identificador_incidencia,
          i.nombre,
          i.descripcion,
          s.nombre AS status
        FROM catalogo_incidencia i
        JOIN catalogo_status s
        ON i.status = s.catalogo_status_id
        WHERE i.status != 2
sql;
        return $mysqli->queryAll($query);
    }

    public static function insert($datas){
	    $mysqli = Database::getInstance(1);
      $query=<<<sql
INSERT INTO catalogo_incidencia (catalogo_incidencia_id, identificador_incidencia, nombre, descripcion, status) VALUES (NULL, :identificador_incidencia, :nombre, :descripcion, :status);
sql;
        $parametros = array(
          ':identificador_incidencia'=>$datas->_identificador_incidencia,
          ':nombre'=>$datas->_nombre,
          ':descripcion'=>$datas->_descripcion,
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
UPDATE catalogo_incidencia SET identificador_incidencia = :identificador_incidencia, nombre = :nombre, descripcion = :descripcion, status = :status WHERE catalogo_incidencia.catalogo_incidencia_id = :catalogo_incidencia_id;
sql;
      $parametros = array(
        ':catalogo_incidencia_id'=>$datas->_catalogo_incidencia_id,
        ':identificador_incidencia'=>$datas->_identificador_incidencia,
        ':nombre'=>$datas->_nombre,
        ':descripcion'=>$datas->_descripcion,
        ':status'=>$datas->_status
      );
      $accion = new \stdClass();
      $accion->_sql= $query;
      $accion->_parametros = $parametros;
      $accion->_id = $datas->_catalogo_incidencia_id;
      UtileriasLog::addAccion($accion);
      return $mysqli->update($query, $parametros);
    }

    public static function delete($id){
      $mysqli = Database::getInstance();
      $query=<<<sql
      UPDATE catalogo_incidencia SET status = 2 WHERE catalogo_incidencia_id = $id
sql;
      $accion = new \stdClass();
      $accion->_sql= $query;
      $accion->_parametros = $parametros;
      $accion->_id = $id;
      UtileriasLog::addAccion($accion);
      return $mysqli->update($query);
    }

    public static function getById($id){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT * FROM catalogo_incidencia WHERE catalogo_incidencia_id = $id
sql;
      return $mysqli->queryOne($query);
    }

    public static function getByIdReporte($id){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT
        i.catalogo_incidencia_id,
        i.identificador_incidencia,
        i.nombre,
        i.descripcion,
        s.nombre AS status
      FROM catalogo_incidencia i
      JOIN catalogo_status s
      ON i.status = s.catalogo_status_id
      WHERE i.status != 2 AND i.catalogo_incidencia_id = $id
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

    public static function getIdentificadorIncidencia($identificador_incidencia){
      $mysqli = Database::getInstance();
      $query =<<<sql
      SELECT * FROM catalogo_incidencia WHERE identificador_incidencia LIKE '$identificador_incidencia'
sql;
      $dato = $mysqli->queryOne($query);
      return ($dato>=1) ? 1 : 2 ;
    }

    public static function getNombreIncidencia($nombre_incidencia){
      $mysqli = Database::getInstance();
      $query =<<<sql
      SELECT * FROM catalogo_incidencia WHERE nombre LIKE '$nombre_incidencia' 
sql;
      $dato = $mysqli->queryOne($query);
      return ($dato>=1) ? 1 : 2 ;
    }
}
