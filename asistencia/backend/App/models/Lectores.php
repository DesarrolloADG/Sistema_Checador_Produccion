<?php
namespace App\models;
defined("APPPATH") OR die("Access denied");

use \Core\Database;
use \App\interfaces\Crud;
use \App\controllers\UtileriasLog;

class Lectores implements Crud{

    public static function getAll(){
      $mysqli = Database::getInstance();
      $query=<<<sql
SELECT l.catalogo_lector_id, l.nombre ubicacion, l.tipo_comunicacion, l.ip_lector, l.puerto, l.descripcion, s.nombre AS status, identificador FROM catalogo_lector l INNER JOIN catalogo_status AS s ON l.status = s.catalogo_status_id WHERE l.status!=2
sql;

        return $mysqli->queryAll($query);
    }

    public static function insert($lectores){
	    $mysqli = Database::getInstance(1);
      $query=<<<sql
INSERT INTO catalogo_lector (catalogo_lector_id, tipo_comunicacion, ip_lector, puerto, descripcion, status, nombre, identificador) VALUES (NULL, :tipo_comunicacion, :ip_comunicacion, :puerto, :descripcion, :status, :nombre, :identificador);
sql;
        $parametros = array(
          ':tipo_comunicacion'=>$lectores->_tipo_comunicacion,
          ':ip_comunicacion'=>$lectores->_ip_comunicacion,
          ':puerto'=>$lectores->_puerto,
          ':descripcion'=>$lectores->_descripcion,
          ':status'=>$lectores->_status,
          ':identificador'=>$lectores->_identificador,
          ':nombre'=>$lectores->_nombre
        );
        $id = $mysqli->insert($query,$parametros);
        $accion = new \stdClass();
        $accion->_sql= $query;
        $accion->_parametros = $parametros;
        $accion->_id = $id;

        UtileriasLog::addAccion($accion);
        return $id;
    }


    public static function update($lectores){
      $mysqli = Database::getInstance(true);
      $query=<<<sql
UPDATE catalogo_lector SET nombre = :nombre, tipo_comunicacion = :tipo_comunicacion, ip_lector = :ip_comunicacion, puerto = :puerto, descripcion = :descripcion, status = :status WHERE catalogo_lector.catalogo_lector_id = :catalogo_lector_id;
sql;
      $parametros = array(
          ':catalogo_lector_id'=>$lectores->_catalogo_lector_id,
          ':nombre'=>$lectores->_nombre,
          ':tipo_comunicacion'=>$lectores->_tipo_comunicacion,
          ':ip_comunicacion'=>$lectores->_ip_comunicacion,
          ':puerto'=>$lectores->_puerto,
          ':descripcion'=>$lectores->_descripcion,
          ':status'=>$lectores->_status
        );
        $accion = new \stdClass();
        $accion->_sql= $query;
        $accion->_parametros = $parametros;
        $accion->_id = $lectores->_catalogo_lector_id;
        UtileriasLog::addAccion($accion);
        return $mysqli->update($query, $parametros);
    }

    public static function delete($id){
      $mysqli = Database::getInstance();
      $select = <<<sql
      SELECT e.catalogo_lector_id FROM catalogo_lector e JOIN catalogo_colaboradores c ON e.catalogo_lector_id = c.catalogo_lector_id WHERE e.catalogo_lector_id = $id
sql;

      $sqlSelect = $mysqli->queryAll($select);
      if(count($sqlSelect) >= 1){
        return array('seccion'=>2, 'id'=>$id); // NO elimina
      }else{
        $query = <<<sql
        UPDATE catalogo_lector SET status = 2 WHERE catalogo_lector.catalogo_lector_id = $id
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
      SELECT * FROM catalogo_lector WHERE catalogo_lector_id = $id
sql;

      return $mysqli->queryOne($query);
    }

    public static function getByIdReporte($id){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT
        l.catalogo_lector_id,
        u.nombre ubicacion,
        l.tipo_comunicacion,
        l.ip_lector,
        l.puerto,
        l.descripcion,
        s.nombre AS status
      FROM catalogo_lector l
      JOIN catalogo_ubicacion u
      ON u.catalogo_ubicacion_id = l.ubicacion_id
      JOIN catalogo_status s
      ON l.status = s.catalogo_status_id WHERE l.status!=2 AND l.catalogo_lector_id = $id
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
      SELECT * FROM catalogo_ubicacion WHERE status != 2
sql;
        return $mysqli->queryAll($query);
    }

    public static function getDataLector($id){
      $mysqli = Database::getInstance();
/*
      $query=<<<sql
      SELECT cl.catalogo_lector_id, cl.ubicacion_id, cl.tipo_comunicacion, cl.ip_lector, cl.puerto, cl.descripcion, cl.status, cs.catalogo_status_id, cs.nombre As nombre_status, cu.catalogo_ubicacion_id, cu.nombre FROM catalogo_lector AS cl INNER JOIN catalogo_status As cs INNER JOIN catalogo_ubicacion AS cu WHERE cl.catalogo_lector_id = $id AND cs.catalogo_status_id = cl.status AND cl.ubicacion_id = cu.catalogo_ubicacion_id
sql;
*/
	$query=<<<sql
SELECT cl.catalogo_lector_id, cl.tipo_comunicacion, cl.ip_lector, cl.puerto, cl.descripcion, cl.status, cs.catalogo_status_id, cs.nombre As nombre_status, cl.nombre FROM catalogo_lector AS cl INNER JOIN catalogo_status As cs WHERE cs.catalogo_status_id = cl.status AND cl.catalogo_lector_id = $id
sql;
        return $mysqli->queryOne($query);
    }

    public static function verificarRelacion($id){
      $mysqli = Database::getInstance();
      $select = <<<sql
      SELECT e.catalogo_lector_id FROM catalogo_lector e JOIN catalogo_colaboradores c ON e.catalogo_lector_id = c.catalogo_lector_id WHERE e.catalogo_lector_id = $id
sql;
      $sqlSelect = $mysqli->queryAll($select);
      if(count($sqlSelect) >= 1)
        return array('seccion'=>2, 'id'=>$id); // NO elimina
      else
        return array('seccion'=>1, 'id'=>$id); // Cambia el status a eliminado
      
    }

    public static function getNombreLector($nombreLector){
      $mysqli = Database::getInstance();
      $query =<<<sql
      SELECT * FROM catalogo_lector WHERE nombre LIKE '$nombreLector' 
sql;
      $dato = $mysqli->queryOne($query);
      return ($dato>=1) ? 1 : 2 ;
    }
}
