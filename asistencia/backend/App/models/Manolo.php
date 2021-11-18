<?php
namespace App\models;
defined("APPPATH") OR die("Access denied");

use \Core\Database;
use \App\interfaces\Crud;
use \App\controllers\UtileriasLog;

class Manolo{

    public static function obtienJugadoresId($jugador){

	$mysqli = Database::getInstance();
        $query=<<<sql
        SELECT * FROM catalogo_departamento 
        WHERE 1 = 1 AND catalogo_departamento_id = :id
sql;

		$params = array(':id'=>$jugador);

        return $mysqli->queryAll($query, $params);
		
    }
	
	public static function obtienJugadoresAll(){

	$mysqli = Database::getInstance();
        $query=<<<sql
        SELECT * FROM catalogo_departamento 
sql;


        return $mysqli->queryAll($query);
		
    }
}