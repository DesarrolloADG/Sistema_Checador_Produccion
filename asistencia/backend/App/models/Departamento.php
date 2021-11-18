<?php
namespace App\models;
defined("APPPATH") OR die("Access denied");

use \Core\Database;
use \App\interfaces\Crud;
use \App\controllers\UtileriasLog;

class Departamento implements Crud{

    public static function getAll(){

	$mysqli = Database::getInstance();
        $query=<<<sql
        SELECT
          d.catalogo_departamento_id,
          d.nombre,
          s.nombre AS status
        FROM catalogo_departamento d
        JOIN catalogo_status s
        ON d.status = s.catalogo_status_id WHERE d.status!=2
sql;
        return $mysqli->queryAll($query);
    }

    public static function insert($empresa){
	    $mysqli = Database::getInstance(1);
      $query=<<<sql
        INSERT INTO catalogo_departamento VALUES(null, :nombre, :status)
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
      UPDATE catalogo_departamento SET nombre = :nombre, status = :status WHERE catalogo_departamento_id = :id
sql;
      $parametros = array(
        ':id'=>$empresa->_catalogo_departamento_id,
        ':nombre'=>$empresa->_nombre,
        ':status'=>$empresa->_status
      );
      $accion = new \stdClass();
      $accion->_sql= $query;
      $accion->_parametros = $parametros;
      $accion->_id = $empresa->_catalogo_departamento_id;
      UtileriasLog::addAccion($accion);
      return $mysqli->update($query, $parametros);
    }

    public static function delete($id){
      $mysqli = Database::getInstance();
      $select = <<<sql
      SELECT e.catalogo_departamento_id FROM catalogo_departamento e JOIN catalogo_colaboradores c
      ON e.catalogo_departamento_id = c.catalogo_departamento_id WHERE e.catalogo_departamento_id = $id
sql;

      $sqlSelect = $mysqli->queryAll($select);
      if(count($sqlSelect) >= 1){
        return array('seccion'=>2, 'id'=>$id); // NO elimina
      }else{
        $query = <<<sql
        UPDATE catalogo_departamento SET status = 2 WHERE catalogo_departamento.catalogo_departamento_id = $id;
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
      SELECT cd.catalogo_departamento_id, cd.nombre, cd.status, cs.catalogo_status_id, cs.nombre AS nombre_status FROM catalogo_departamento AS cd INNER JOIN catalogo_status AS cs WHERE catalogo_departamento_id = $id AND cd.status = cs.catalogo_status_id
sql;

      return $mysqli->queryOne($query);
    }

    public static function getByIdReporte($id){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT
        d.catalogo_departamento_id,
        d.nombre,
        s.nombre AS status
      FROM catalogo_departamento d
      JOIN catalogo_status s
      ON d.status = s.catalogo_status_id WHERE d.status!=2 AND d.catalogo_departamento_id = $id
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

    public static function getNombreDepartamento($nombre_departamento){
      $mysqli = Database::getInstance();
      $query =<<<sql
      SELECT * FROM catalogo_departamento WHERE nombre LIKE '$nombre_departamento' 
sql;
      $dato = $mysqli->queryOne($query);
      return ($dato>=1) ? 1 : 2 ;
    }

    public static function verificarRelacion($id){
      $mysqli = Database::getInstance();
      $select = <<<sql
      SELECT cd.catalogo_departamento_id FROM catalogo_departamento cd JOIN catalogo_colaboradores c ON cd.catalogo_departamento_id = c.catalogo_departamento_id WHERE cd.catalogo_departamento_id = $id
sql;
      $sqlSelect = $mysqli->queryAll($select);
      if(count($sqlSelect) >= 1)
        return array('seccion'=>2, 'id'=>$id); // NO elimina
      else
        return array('seccion'=>1, 'id'=>$id); // Cambia el status a eliminado
      
    }

}
