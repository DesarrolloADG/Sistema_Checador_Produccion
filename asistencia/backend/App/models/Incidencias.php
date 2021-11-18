<?php
namespace App\models;
defined("APPPATH") OR die("Access denied");

use \Core\Database;
use \App\interfaces\Crud;
use \App\controllers\UtileriasLog;

class Incidencias implements Crud{

    public static function getAll(){
      $mysqli = Database::getInstance();
      $query=<<<sql
        SELECT
          i.catalogo_incidencia_id,
          i.identificador_incidencia,
          i.nombre,
          i.descripcion,
          i.color,
          s.nombre AS status
        FROM catalogo_incidencia i
        JOIN catalogo_status s
        ON i.status = s.catalogo_status_id
        WHERE i.status != 2
sql;
        return $mysqli->queryAll($query);
    }

    public static function insert($datas){
	    $mysqli = Database::getInstance();
      $query=<<<sql
INSERT INTO catalogo_incidencia VALUES (NULL, :identificador_incidencia, :nombre, :descripcion, :color, :status, :genera_falta);
sql;
        $parametros = array(
          ':identificador_incidencia'=>$datas->_identificador_incidencia,
          ':nombre'=>$datas->_nombre,
          ':descripcion'=>$datas->_descripcion,
          ':color'=>$datas->_color,
          ':status'=>$datas->_status,
	  ':genera_falta' => $datas->_genera_falta
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
      $mysqli = Database::getInstance();
      $query=<<<sql
UPDATE catalogo_incidencia SET genera_falta = :genera_falta,identificador_incidencia = :identificador_incidencia, nombre = :nombre, descripcion = :descripcion, color = :color, status = :status WHERE catalogo_incidencia.catalogo_incidencia_id = :catalogo_incidencia_id;
sql;
      $parametros = array(
        ':catalogo_incidencia_id'=>$datas->_catalogo_incidencia_id,
        ':identificador_incidencia'=>$datas->_identificador_incidencia,
        ':nombre'=>$datas->_nombre,
        ':descripcion'=>$datas->_descripcion,
        ':color'=>$datas->_color,
        ':status'=>$datas->_status,
	':genera_falta' => $datas->_genera_falta
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
      $select = <<<sql
      SELECT e.catalogo_incidencia_id FROM catalogo_incidencia e JOIN catalogo_colaboradores c ON e.catalogo_incidencia_id = c.catalogo_incidencia_id WHERE e.catalogo_incidencia_id = $id
sql;

      $sqlSelect = $mysqli->queryAll($select);
      if(count($sqlSelect) >= 1){
        return array('seccion'=>2, 'id'=>$id); // NO elimina
      }else{
        $query = <<<sql
        UPDATE catalogo_incidencia SET status = 2 WHERE catalogo_incidencia.catalogo_incidencia_id = $id
sql;
        $mysqli->update($query);

        $accion = new \stdClass();
        $accion->_sql= $query;
        $accion->_parametros = $parametros;
        $accion->_id = $id;
        UtileriasLog::addAccion($accion);
        return array('seccion'=>1, 'id'=>$id); // Cambia el status a eliminado
      }
      /*$mysqli = Database::getInstance();
      $query=<<<sql
      UPDATE catalogo_incidencia SET status = 2 WHERE catalogo_incidencia_id = $id
sql;
      $accion = new \stdClass();
      $accion->_sql= $query;
      $accion->_parametros = $parametros;
      $accion->_id = $id;
      UtileriasLog::addAccion($accion);
      return $mysqli->update($query);*/
    }

    public static function getById($id){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT ci.genera_falta ,ci.catalogo_incidencia_id, ci.nombre, ci.identificador_incidencia, ci.status, ci.color, ci.descripcion, cs.catalogo_status_id, cs.nombre AS nombre_status FROM catalogo_incidencia AS ci INNER JOIN catalogo_status AS cs WHERE catalogo_incidencia_id = $id AND status = cs.catalogo_status_id 
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
        i.color,
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

    public static function verificarRelacion($id){
      $mysqli = Database::getInstance();
      $select = <<<sql
      SELECT e.catalogo_incidencia_id FROM catalogo_incidencia e JOIN catalogo_colaboradores c
      ON e.catalogo_incidencia_id = c.catalogo_incidencia_id WHERE e.catalogo_incidencia_id = $id
sql;
      $sqlSelect = $mysqli->queryAll($select);
      if(count($sqlSelect) >= 1)
        return array('seccion'=>2, 'id'=>$id); // NO elimina
      else
        return array('seccion'=>1, 'id'=>$id); // Cambia el status a eliminado
      
    }
}
