<?php
namespace App\models;
defined("APPPATH") OR die("Access denied");

use \Core\Database;
use \App\interfaces\Crud;

class Incidente implements Crud{

	public static function getAll(){
		$mysqli = Database::getInstance();
		$query = <<<sql
SELECT * FROM catalogo_colaboradores WHERE pago = "Semanal" AND status = 1 LIMIT 10
sql;
		return $mysqli->queryAll($query);
	}

	public static function insert($data){}
	public static function update($data){}
	public static function delete($id){}


	public static function getAllColaboradores($data){
		$mysqli = Database::getInstance();
		print_r($data);
		$query =<<<sql
SELECT * FROM `catalogo_colaboradores` WHERE pago = :pago AND status = 1 LIMIT 10 
sql;
		$params = array(
			':pago'=>$data->_pago
		);

		return $mysqli->queryAll($query, $params);
	}

	public static function getById($id){
		$mysqli = Database::getInstance();
		$query=<<<sql
SELECT c.*, s.nombre AS status FROM catalogo_colaboradores c
JOIN catalogo_status s ON c.status = s.catalogo_status_id
WHERE c.catalogo_colaboradores_id = $id
sql;
		return $mysqli->queryOne($query);
    }

	public static function getPeriodoById($id){
		$mysqli = Database::getInstance();
		$query=<<<sql
SELECT * FROM prorrateo_periodo WHERE prorrateo_periodo_id = "$id"
sql;
		return $mysqli->queryOne($query);
    }

    public static function getHorarioId($id){
    	$mysqli = Database::getInstance();
    	$query =<<<sql
SELECT * FROM catalogo_horario cah INNER JOIN colaboradores_horario coh USING (catalogo_horario_id) INNER JOIN horario_dias_laborales hdl USING (catalogo_horario_id ) INNER JOIN dias_laborales dl USING (dias_laborales_id) WHERE coh.catalogo_colaboradores_id = "$id" 
sql;
		return $mysqli->queryAll($query);
    }
}