<?php
namespace App\models;
defined("APPPATH") OR die("Access denied");

use \Core\Database;
use \App\interfaces\Crud;
use \App\controllers\UtileriasLog;

class Horario implements Crud{

    public static function getAll(){

	$mysqli = Database::getInstance();
        $query=<<<sql
        SELECT
          h.catalogo_horario_id,
          h.nombre,
          h.hora_entrada,
          h.hora_salida,
          h.tolerancia_entrada,
          h.dias_laborales,
          h.numero_retardos,
          s.nombre AS status
        FROM catalogo_horario h
        JOIN catalogo_status s
        ON h.status = s.catalogo_status_id WHERE status != 2
sql;
        return $mysqli->queryAll($query);
    }

    public static function insert($horario){
	    $mysqli = Database::getInstance(1);
      $query=<<<sql
        INSERT INTO catalogo_horario VALUES(null, :nombre, :hora_entrada, :hora_salida, :tolerancia_entrada, :dias_laborales, :numero_retardos, :status)
sql;
        $parametros = array(
           ':nombre'=>$horario->_nombre,
           ':hora_entrada'=>$horario->_hora_entrada,
           ':hora_salida'=>$horario->_hora_salida,
           ':tolerancia_entrada'=>$horario->_tolerancia_entrada,
           ':dias_laborales'=>$horario->_dias_laborales,
           ':numero_retardos'=>$horario->_numero_retardos,
           ':status'=>$horario->_status
        );
        $id = $mysqli->insert($query,$parametros);
        $accion = new \stdClass();
        $accion->_sql= $query;
        $accion->_parametros = $parametros;
        $accion->_id = $id;

        UtileriasLog::addAccion($accion);
        return $id;
    }


    public static function update($horario){

      $mysqli = Database::getInstance(true);
      $query=<<<sql
      UPDATE catalogo_horario SET
        nombre = :nombre,
        hora_entrada = :hora_entrada,
        hora_salida = :hora_salida,
        tolerancia_entrada = :tolerancia_entrada,
        dias_laborales = :dias_laborales,
        numero_retardos = :numero_retardos,
        status = :status
      WHERE catalogo_horario_id = :catalogo_horario_id
sql;
      $parametros = array(
         ':catalogo_horario_id'=>$horario->_catalogo_horario_id,
         ':nombre'=>$horario->_nombre,
         ':hora_entrada'=>$horario->_hora_entrada,
         ':hora_salida'=>$horario->_hora_salida,
         ':tolerancia_entrada'=>$horario->_tolerancia_entrada,
         ':dias_laborales'=>$horario->_dias_laborales,
         ':numero_retardos'=>$horario->_numero_retardos,
         ':status'=>$horario->_status
      );
      $accion = new \stdClass();
      $accion->_sql= $query;
      $accion->_parametros = $parametros;
      UtileriasLog::addAccion($accion);
        return $mysqli->update($query, $parametros);
    }

    public static function delete($id){
      $mysqli = Database::getInstance();
      $select = <<<sql
      SELECT e.catalogo_horario_id FROM catalogo_horario e JOIN catalogo_colaboradores c
      ON e.catalogo_horario_id = c.catalogo_horario_id WHERE e.catalogo_horario_id = $id
sql;

      $sqlSelect = $mysqli->queryAll($select);
      if(count($sqlSelect) >= 1){
        return array('seccion'=>2, 'id'=>$id); // NO elimina
      }else{
        $query = <<<sql
        UPDATE catalogo_horario SET status = 2 WHERE catalogo_horario.catalogo_horario_id = $id;
sql;
        $mysqli->update($query);
        $accion = new \stdClass();
        $accion->_sql= $query;
        $accion->_parametros = $parametros;
        UtileriasLog::addAccion($accion);
        return array('seccion'=>1, 'id'=>$id); // Cambia el status a eliminado
    }

    public static function getById($id){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT ch.catalogo_horario_id, ch.nombre, ch.hora_entrada, ch.hora_salida, ch.tolerancia_entrada, ch.numero_retardos, ch.status, cs.nombre AS nombre_status, cs.catalogo_status_id FROM catalogo_horario AS ch INNER JOIN catalogo_status AS cs WHERE catalogo_horario_id = $id AND ch.status = cs.catalogo_status_id 
sql;
      return $mysqli->queryOne($query);
    }

    public static function getByIdReporte($id){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT
        h.catalogo_horario_id,
        h.nombre,
        h.hora_entrada,
        h.hora_salida,
        h.tolerancia_entrada,
        h.dias_laborales,
        h.numero_retardos,
        s.nombre AS status
      FROM catalogo_horario h
      JOIN catalogo_status s
      ON h.status = s.catalogo_status_id WHERE status != 2 AND h.catalogo_horario_id = $id AND status != 2
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

}
