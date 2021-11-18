<?php
namespace App\models;
defined("APPPATH") OR die("Access denied");

use \Core\Database;
use \App\interfaces\Crud;

class HorarioIncidencia implements Crud{

    public static function getAll(){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT * FROM `test`
sql;
        return $mysqli->queryAll($query);
    }

    public static function getAllColaboradores($departamento){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT c.catalogo_colaboradores_id, c.foto, c.numero_empleado, s.nombre AS status, c.nombre, c.apellido_paterno, c.apellido_materno, d.nombre AS nombre_departamento, e.nombre AS nombre_empresa FROM catalogo_colaboradores AS c INNER JOIN catalogo_empresa e ON (e.catalogo_empresa_id = c.catalogo_empresa_id) INNER JOIN catalogo_departamento AS d ON (c.catalogo_departamento_id = d.catalogo_departamento_id) INNER JOIN catalogo_status AS s On (s.catalogo_status_id = c.status) WHERE c.catalogo_departamento_id = '$departamento' AND s.catalogo_status_id != 2 
sql;
        return $mysqli->queryAll($query);
    }

    public static function getById($id){

    }

    public static function insert($data){

    }

    public static function update($data){

    }

    public static function delete($id){

    }

    public static function getDatosUsuarioLogeado($user){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT * FROM utilerias_administradores AS a WHERE usuario LIKE '$user'
sql;
        return $mysqli->queryOne($query);
    }

    public static function getDepartamentos($id){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT ad.catalogo_departamento_id, d.nombre AS nombre_departamento FROM utilerias_administradores_departamentos AS ad
INNER JOIN catalogo_departamento AS d ON (d.catalogo_departamento_id = ad.catalogo_departamento_id)
WHERE id_administrador = '$id'
sql;
        return $mysqli->queryAll($query);
    }

    public static function getDatosColaborador($id){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT * FROM catalogo_colaboradores WHERE catalogo_colaboradores_id = '$id' 
sql;
        return $mysqli->queryOne($query);
    }

    public static function getDatosChecador($fecha_ini, $fecha_fin, $numero_empleado){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT c.numero_empleado, c.catalogo_colaboradores_id, op.date_check, h.hora_entrada, h.hora_salida, h.tolerancia_entrada 
FROM catalogo_colaboradores AS c 
INNER JOIN catalogo_horario AS h ON (h.catalogo_horario_id = c.catalogo_horario_id) 
INNER JOIN operacion_checador AS op 
WHERE c.catalogo_colaboradores_id = 1 
AND op.numero_empleado = '$numero_empleado' 
AND op.date_check >= '$fecha_ini' AND op.da
sql;
/*
SELECT c.numero_empleado, c.catalogo_colaboradores_id, op.date_check, h.hora_entrada, h.hora_salida, h.tolerancia_entrada FROM catalogo_colaboradores AS c INNER JOIN catalogo_horario AS h ON (h.catalogo_horario_id = c.catalogo_horario_id) INNER JOIN operacion_checador AS op WHERE c.catalogo_colaboradores_id = 1 AND op.numero_empleado = '8201' AND op.date_check >= '2017-09-04 00:00:00' AND op.date_check <= '2017-09-04 23:59:59'

SELECT c.numero_empleado, c.catalogo_colaboradores_id, op.date_check, h.hora_entrada, h.hora_salida, h.tolerancia_entrada FROM catalogo_colaboradores AS c INNER JOIN catalogo_horario AS h ON (h.catalogo_horario_id = c.catalogo_horario_id) INNER JOIN operacion_checador AS op WHERE c.catalogo_colaboradores_id = 1 AND op.numero_empleado = '' AND op.date_check >= '2017-09-04 00:00:00' AND op.date_check <= '2017-09-04 23:59:59' 
*/
        return $mysqli->queryAll($query);
    }
}
