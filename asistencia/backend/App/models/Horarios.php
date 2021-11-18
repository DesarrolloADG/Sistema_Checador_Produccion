<?php
namespace App\models;
defined("APPPATH") OR die("Access denied");

use \Core\Database;
use \Core\MasterDom;
use \App\interfaces\Crud;
use \App\controllers\UtileriasLog;

class Horarios implements Crud{

    public static function getAll(){

	$mysqli = Database::getInstance();
        $query=<<<sql
        SELECT
          h.catalogo_horario_id,
          h.nombre,
          h.hora_entrada,
          h.hora_salida,
          h.tolerancia_entrada,
          h.numero_retardos,
          s.nombre AS status
        FROM catalogo_horario h
        JOIN catalogo_status s
        ON h.status = s.catalogo_status_id WHERE h.status != 2
sql;
        return $mysqli->queryAll($query);
    }

    public static function insert($horario){
	    $mysqli = Database::getInstance(1);
      $query=<<<sql
        INSERT INTO catalogo_horario VALUES(null, :nombre, :hora_entrada, :hora_salida, :tolerancia_entrada, :numero_retardos, :status)
sql;
        $parametros = array(
           ':nombre'=>$horario->_nombre,
           ':hora_entrada'=>$horario->_hora_entrada,
           ':hora_salida'=>$horario->_hora_salida,
           ':tolerancia_entrada'=>$horario->_tolerancia_entrada,
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
         ':numero_retardos'=>$horario->_numero_retardos,
         ':status'=>$horario->_status
      );
      $accion = new \stdClass();
      $accion->_sql= $query;
      $accion->_parametros = $parametros;
      $accion->_id = $horario->_catalogo_horario_id;
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
        UPDATE catalogo_horario SET status = '2' WHERE catalogo_horario.catalogo_horario_id = $id;
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
      SELECT ce.catalogo_horario_id, ce.nombre, ce.hora_entrada, ce.hora_salida, ce.tolerancia_entrada, ce.numero_retardos, ce.status AS nombre_status, cs.catalogo_status_id, cs.nombre AS nombre_status FROM catalogo_horario AS ce INNER JOIN catalogo_status AS cs WHERE catalogo_horario_id = $id AND ce.status = cs.catalogo_status_id 
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
        h.numero_retardos,
        s.nombre AS status
      FROM catalogo_horario h
      JOIN catalogo_status s
      ON h.status = s.catalogo_status_id
      WHERE h.status != 2 AND h.catalogo_horario_id = $id
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

    public static function getNombreHorario($nombre_horario){
      $mysqli = Database::getInstance();
      $query =<<<sql
      SELECT * FROM catalogo_horario WHERE nombre LIKE '$nombre_horario'
sql;
      $dato = $mysqli->queryOne($query);
      return ($dato>=1) ? 1 : 2 ;
    }

    public static function insertDiasLaborales($dias_laborales){
	    $mysqli = Database::getInstance(1);
      $query=<<<sql
        INSERT INTO horario_dias_laborales VALUES($dias_laborales->_catalogo_horario_id, $dias_laborales->_dias_laborales_id)
sql;

        $id = $mysqli->insert($query);
        $accion = new \stdClass();
        $accion->_sql= $query;
        $accion->_parametros = $parametros;
        $accion->_id = $id;

        UtileriasLog::addAccion($accion);
        return $id;
    }

    public static function getDiasLaborales(){
      $mysqli = Database::getInstance();
      $query =<<<sql
      SELECT * FROM dias_laborales ORDER BY dias_laborales_id
sql;
      return $mysqli->queryAll($query);
    }

    public static function getDiasLaboralesById($id){
      $mysqli = Database::getInstance();
      $query =<<<sql
      SELECT
        *
      FROM horario_dias_laborales
      WHERE catalogo_horario_id = $id
      ORDER BY dias_laborales_id
sql;
      return $mysqli->queryAll($query);
    }

    public static function deleteDiasLaborales($id){
      $mysqli = Database::getInstance();
      $query =<<<sql
      DELETE FROM horario_dias_laborales WHERE catalogo_horario_id = $id
sql;
      return $mysqli->update($query);
    }

    public static function getDiasLaboralesHorarioById($id){
      $mysqli = Database::getInstance();
      $query =<<<sql
      SELECT
      DISTINCT
        d.dias_laborales_id,
        d.nombre
      FROM horario_dias_laborales hd
      JOIN dias_laborales d
      ON d.dias_laborales_id = hd.dias_laborales_id
      WHERE hd.catalogo_horario_id = $id
      ORDER BY d.dias_laborales_id
sql;
      return $mysqli->queryAll($query);
    }

    public static function verificarRelacion($id){
      $mysqli = Database::getInstance();
      $select = <<<sql
      SELECT 
        ch.catalogo_horario_id 
      FROM catalogo_horario ch 
      JOIN catalogo_colaboradores c
      ON ch.catalogo_horario_id = c.catalogo_horario_id 
      WHERE ch.catalogo_horario_id = $id
sql;
      return $mysqli->queryAll($select);
    }


      
}
