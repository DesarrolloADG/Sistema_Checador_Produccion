<?php
namespace App\models;
defined("APPPATH") OR die("Access denied");

use \Core\Database;
use \App\interfaces\Crud;

class Checador implements Crud{

	public static function getAll(){
		$mysqli = Database::getInstance();
		$query=<<<sql
SELECT * FROM `test`
sql;
		return $mysqli->queryAll($query);
	}

    public static function getById($id){
    	$mysqli = Database::getInstance();
		$query=<<<sql
SELECT *, cc.nombre AS nombre_colaborador FROM catalogo_colaboradores AS cc INNER JOIN catalogo_lector AS cl USING (catalogo_lector_id) WHERE catalogo_colaboradores_id = :catalogo_colaboradores_id 
sql;
		return $mysqli->queryOne($query, array(':catalogo_colaboradores_id'=>$id));
    }
    public static function insert($data){}
    public static function update($data){}
    public static function delete($id){}

	public static function getPeriodoId($id){
		$mysqli = Database::getInstance();
		$query=<<<sql
SELECT * FROM prorrateo_periodo WHERE prorrateo_periodo_id = $id
sql;
		return $mysqli->queryOne($query);
	}

    public static function getLastPeriodoSemanal(){
		$mysqli = Database::getInstance();
		$query=<<<sql
SELECT * FROM prorrateo_periodo WHERE tipo = 'SEMANAL' AND status != '-1' ORDER BY fecha_inicio DESC LIMIT 1 
sql;
		return $mysqli->queryOne($query);
	}

	public static function getLastPeriodoQuincenal(){
		$mysqli = Database::getInstance();
		$query=<<<sql
SELECT * FROM prorrateo_periodo WHERE tipo = 'QUINCENAL' AND status != '-1' ORDER BY fecha_inicio DESC LIMIT 1 
sql;
		return $mysqli->queryOne($query);
	}

	public static function getAllPeriodosSemanales(){
		$mysqli = Database::getInstance();
		$query=<<<sql
SELECT * FROM prorrateo_periodo WHERE tipo = 'QUINCENAL' AND status != '-1' ORDER BY `prorrateo_periodo`.`prorrateo_periodo_id` DESC 
sql;
		return $mysqli->queryOne($query);
	}


	public static function getAllPeriodosQuincenales(){
		$mysqli = Database::getInstance();
		$query=<<<sql
SELECT * FROM prorrateo_periodo WHERE tipo = 'SEMANAL' AND status != '-1' ORDER BY `prorrateo_periodo`.`prorrateo_periodo_id` DESC 
sql;
		return $mysqli->queryOne($query);
	}

	public static function getChecador($data){
		$mysqli = Database::getInstance();
		$query=<<<sql
SELECT * FROM operacion_checador WHERE numero_empleado = :numero_empleado AND identificador = :identificador AND date_check >= :date_check_1 AND date_check <= :date_check_2 
sql;
		$array = array(
			':numero_empleado'=>$data->_numero_empleado,
			':identificador'=>$data->_identificador,
			':date_check_1'=>$data->_date_check_1,
			':date_check_2'=>$data->_date_check_2
		);
		return $mysqli->queryAll($query, $array);
	}

	public static function getAllPeriodos($tipo){
		$t = ($tipo == 'semanales')? "SEMANAL":"QUINCENAL";

		$mysqli = Database::getInstance();
		$query=<<<sql
SELECT * FROM prorrateo_periodo WHERE tipo = "$t" AND status != '-1' ORDER BY fecha_inicio DESC 
sql;
		return $mysqli->queryAll($query);
	}


	public static function getAsistenciaModificada($datos){
        $mysqli = Database::getInstance();
        $query =<<<sql
        SELECT oc.*
        FROM operacion_checador oc
        JOIN catalogo_lector cl ON (cl.identificador = oc.identificador AND cl.catalogo_lector_id = $datos->catalogo_lector_id)
        WHERE oc.date_check >= '$datos->fecha_inicio'
        AND  oc.date_check <= '$datos->fecha_fin'
        AND numero_empleado = $datos->numero_empleado 
        ORDER BY oc.date_check ASC
sql;
        return $mysqli->queryAll($query);
    }


}
