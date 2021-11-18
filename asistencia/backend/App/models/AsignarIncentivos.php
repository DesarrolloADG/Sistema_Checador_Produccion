<?php
namespace App\models;
defined("APPPATH") OR die("Access denied");

use \Core\Database;
use \App\interfaces\Crud;

class AsignarIncentivos implements Crud{

    public static function getAll(){

	$mysqli = Database::getInstance();
        $query=<<<sql
        
sql;
        return $mysqli->queryAll($query);
    }

    public static function getAllColaboradores($departamento, $planta, $perfil){
        $queryPlanta;
        if($perfil == 1) // ROOT
            $queryPlanta = "WHERE s.catalogo_status_id != 2 AND c.catalogo_departamento_id = '$departamento' ";

        if($perfil == 6) // RECURSOS HUMANOS
            $queryPlanta = "WHERE c.catalogo_departamento_id = '$departamento' AND s.catalogo_status_id != 2 AND c.identificador_noi = '$planta'"; 

        if($perfil == 4 || $perfil == 5) // ADMIN y PERSONALIZADO
            $queryPlanta = "WHERE c.catalogo_departamento_id = '$departamento' AND s.catalogo_status_id != 2  "; 
        

        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT c.catalogo_colaboradores_id, c.foto, c.numero_empleado, c.identificador_noi, s.nombre AS status, c.nombre, 
c.apellido_paterno, c.apellido_materno, d.nombre AS nombre_departamento, e.nombre AS nombre_empresa 
FROM catalogo_colaboradores AS c 
INNER JOIN catalogo_empresa e ON (e.catalogo_empresa_id = c.catalogo_empresa_id) 
INNER JOIN catalogo_departamento AS d ON (c.catalogo_departamento_id = d.catalogo_departamento_id) 
INNER JOIN catalogo_status AS s On (s.catalogo_status_id = c.status) 
$queryPlanta
sql;

        return $mysqli->queryAll($query);
    }

    public static function getPermisosGlobales($usuario){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT permisos_globales FROM utilerias_permisos WHERE usuario = "$usuario"
sql;
        return $mysqli->queryOne($query);
    }

    public static function getAllColaboradoresRH($tipo, $planta){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT cc.foto, cc.catalogo_colaboradores_id, cc.nombre, cc.apellido_paterno, cc.identificador_noi, cc.apellido_materno, cc.pago, cd.nombre AS nombre_departamento 
FROM catalogo_colaboradores AS cc
INNER JOIN catalogo_departamento AS cd USING (catalogo_departamento_id)
WHERE cc.pago = "$tipo" AND cc.identificador_noi = "$planta"
sql;
        return $mysqli->queryAll($query);
    }

    public static function getAllColaboradoresPeriodo($departamento, $periodo){
        $filtro;
        if($periodo == "ambos")
            $filtro = "";

        if($periodo == "SEMANAL")
            $filtro = "AND c.pago = \"SEMANAL\"";

        if($periodo == "QUINCENAL")
            $filtro = "AND c.pago = \"QUINCENAL\"";

        
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT c.pago, c.catalogo_colaboradores_id, c.foto, c.numero_empleado, s.nombre AS status, c.nombre, c.apellido_paterno, c.apellido_materno, d.nombre AS nombre_departamento, e.nombre AS nombre_empresa FROM catalogo_colaboradores AS c INNER JOIN catalogo_empresa e ON (e.catalogo_empresa_id = c.catalogo_empresa_id) INNER JOIN catalogo_departamento AS d ON (c.catalogo_departamento_id = d.catalogo_departamento_id) INNER JOIN catalogo_status AS s On (s.catalogo_status_id = c.status) WHERE c.catalogo_departamento_id = '$departamento' AND s.catalogo_status_id != 2 {$filtro}
sql;

        return $mysqli->queryAll($query);   
    }

    // Obtener los datos del usuario que esta logeado como admin u otro tipo de user_profile_update_errors
    public static function getDatosUsuarioLogeado($user){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT *, cp.nombre AS nombre_planta FROM 
        utilerias_administradores AS a 
        INNER JOIN catalogo_planta AS cp USING (catalogo_planta_id)
        WHERE usuario LIKE '$user'
sql;
        return $mysqli->queryOne($query);
    }

    public static function getAdministrador($user){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT administrador_id, nombre, usuario, perfil_id, descripcion, tipo, status FROM utilerias_administradores AS a WHERE usuario LIKE '$user'
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

     public static function getAllDepartamentos(){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT ad.catalogo_departamento_id, d.nombre AS nombre_departamento FROM utilerias_administradores_departamentos AS ad
INNER JOIN catalogo_departamento AS d ON (d.catalogo_departamento_id = ad.catalogo_departamento_id)
sql;
        return $mysqli->queryAll($query);
    }

    public static function getDepartamentosRh(){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT DISTINCT ad.catalogo_departamento_id, d.nombre AS nombre_departamento 
        FROM utilerias_administradores_departamentos AS ad
        INNER JOIN catalogo_departamento AS d 
        ON (d.catalogo_departamento_id = ad.catalogo_departamento_id)
sql;
        return $mysqli->queryAll($query);
    }


    public static function getIncentivosColaboradorAsignados($id,$idPeriodo){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT * FROM incentivos_asignados i 
        INNER JOIN catalogo_incentivo ci ON (ci.catalogo_incentivo_id = i.catalogo_incentivo_id)
        INNER JOIN prorrateo_periodo pp ON (pp.prorrateo_periodo_id = i.prorrateo_periodo_id) 
        WHERE i.colaborador_id = $id AND i.prorrateo_periodo_id = $idPeriodo
sql;
        return $mysqli->queryAll($query);
    }

    public static function getById($id){

    }

    public static function getColaboradorById($id){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT * FROM catalogo_colaboradores WHERE catalogo_colaboradores_id = $id
sql;
        return $mysqli->queryOne($query);
    }

    public static function getPeriodos($tipoPeriodo){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT * FROM prorrateo_periodo WHERE tipo = '$tipoPeriodo' ORDER BY prorrateo_periodo_id DESC 
sql;
        return $mysqli->queryAll($query);
    }

    public static function getPeriodoSemanal(){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT * FROM prorrateo_periodo WHERE tipo = "SEMANAL" 
sql;
        return $mysqli->queryOne($query);
    }

    public static function getLastPeriodoSemanal(){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT * FROM prorrateo_periodo WHERE tipo = "SEMANAL" ORDER BY prorrateo_periodo_id DESC LIMIT 1
sql;
        return $mysqli->queryOne($query);
    }

    public static function getLastPeriodoQuincenal(){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT * FROM prorrateo_periodo WHERE tipo = "QUINCENAL" ORDER BY prorrateo_periodo_id DESC LIMIT 1
sql;
        return $mysqli->queryOne($query);
    }

    public static function getPeriodo($tipo, $periodo){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT * FROM prorrateo_periodo WHERE tipo = "$tipo" AND prorrateo_periodo_id = "$periodo"
sql;
        return $mysqli->queryOne($query);
    }


    public static function getPeriodoSemanales(){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT * FROM prorrateo_periodo WHERE tipo = "SEMANAL" ORDER BY prorrateo_periodo_id DESC
sql;
        return $mysqli->queryAll($query);
    }

    public static function getPeriodoQuincenal(){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT * FROM prorrateo_periodo WHERE tipo = "QUINCENAL" 
sql;
        return $mysqli->queryAll($query);
    }

    public static function getPeriodoQuincenales(){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT * FROM prorrateo_periodo WHERE tipo = "QUINCENAL" ORDER BY prorrateo_periodo_id DESC
sql;
        return $mysqli->queryAll($query);
    }

    public static function getPeriodoSemanalProceso(){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT * FROM prorrateo_periodo WHERE tipo = "SEMANAL" AND status = 0 
sql;
        return $mysqli->queryAll($query);
    }

    public static function getPeriodoQuincenalProceso(){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT * FROM prorrateo_periodo WHERE tipo = "QUINCENAL"  AND status = 0 
sql;
        return $mysqli->queryAll($query);
    }

    public static function getStatusPeriodo($id){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT * FROM prorrateo_periodo WHERE prorrateo_periodo_id = $id
sql;
        return $mysqli->queryOne($query);
    }

    // Metodos ok
    public static function getTipoPeriodo($tipo){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT * FROM prorrateo_periodo WHERE tipo = "$tipo" ORDER BY prorrateo_periodo_id DESC 
sql;
        return $mysqli->queryAll($query);
    }

    public static function getTipoPeriodoProceso($tipo){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT * FROM prorrateo_periodo WHERE tipo = "$tipo" AND status = 0
sql;
        return $mysqli->queryOne($query);
    }

    public static function insert($data){

    }

    public static function insertIncentivo($data){
        $mysqli = Database::getInstance();
        $query=<<<sql
        INSERT INTO incentivos_asignados (colaborador_id, prorrateo_periodo_id, catalogo_incentivo_id, cantidad, asignado) VALUES (:colaborador_id, :prorrateo_periodo_id, :catalogo_incentivo_id, :cantidad, :asignado);
sql;
        $params = array(
            ':colaborador_id'=>$data->_colaborador_id,
            ':prorrateo_periodo_id'=>$data->_prorrateo_periodo_id,
            ':catalogo_incentivo_id'=>$data->_catalogo_incentivo_id,
            ':cantidad'=>$data->_cantidad,
            ':asignado'=>$data->_asignado
        );

        $id = $mysqli->insert($query,$params);
        return $id;
    }

    public static function deleteIncentivo($data){
        $mysqli = Database::getInstance();
        $query=<<<sql
        DELETE FROM incentivos_asignados WHERE colaborador_id = :colaborador_id AND prorrateo_periodo_id = :prorrateo_periodo_id AND catalogo_incentivo_id = :catalogo_incentivo_id AND cantidad = :cantidad AND asignado = :asignado LIMIT 1
sql;
        $params = array(
            ':colaborador_id'=>$data->_colaborador_id,
            ':prorrateo_periodo_id'=>$data->_prorrateo_periodo_id,
            ':catalogo_incentivo_id'=>$data->_catalogo_incentivo_id,
            ':cantidad'=>$data->_cantidad,
            ':asignado'=>$data->_asignado
        );

        $id = $mysqli->update($query,$params);
        return $id;
    }


    // obtener el estatus del periodo
    public static function getIncentivosColaboradorStatus($data){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT * FROM incentivos_asignados i 
        INNER JOIN prorrateo_periodo pp ON (pp.prorrateo_periodo_id = i.prorrateo_periodo_id) 
        WHERE i.colaborador_id = :colaborador_id AND i.prorrateo_periodo_id = :prorrateo_periodo_id AND pp.status = 0 
        GROUP BY colaborador_id
sql;
        $params = array(
            ':colaborador_id'=>$data->_colaborador_id,
            ':prorrateo_periodo_id'=>$data->_prorrateo_periodo_id
        );

        return $mysqli->queryAll($query,$params);
    }

    public static function update($data){

    }


    ////////////////// POSIBLES NUEVAS FUNCIONES PARA ESTE CONTROLADOR

    public static function getColaborador($id){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT cc.catalogo_colaboradores_id, cc.nombre, cc.apellido_paterno, cc.apellido_materno, cc.sexo, cc.numero_empleado, cc.numero_identificador, cu.nombre AS nombre_ubicacion, cl.nombre AS nombre_lector, cp.nombre AS nombre_puesto, cc.foto, cc.pago
FROM catalogo_colaboradores cc
INNER JOIN catalogo_ubicacion cu USING (catalogo_ubicacion_id)
INNER JOIN catalogo_lector cl USING (catalogo_lector_id)
INNER JOIN catalogo_puesto cp USING (catalogo_puesto_id)
WHERE cc.catalogo_colaboradores_id = $id
sql;
        return $mysqli->queryOne($query);
    }

    public static function getIncentivosAsignadosColaborador($id){
        $mysqli= Database::getInstance();
        $query=<<<sql
SELECT * FROM incentivo_colaborador INNER JOIN catalogo_incentivo USING (catalogo_incentivo_id) WHERE catalogo_colaboradores_id = $id
sql;
        return $mysqli->queryAll($query);
    }

    public static function getPeriodoBuscar($idPeriodo){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT * FROM prorrateo_periodo WHERE prorrateo_periodo_id = "$idPeriodo" ORDER BY prorrateo_periodo_id DESC
sql;
        return $mysqli->queryOne($query);
    }

    public static function getProrrateoPeriodoColaboradores($data){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT * FROM prorrateo_periodo_colaboradores WHERE catalogo_colaboradores_id = :catalogo_colaboradores_id AND prorrateo_periodo_id = :prorrateo_periodo_id
sql;
        $params = array(
            ':catalogo_colaboradores_id'=>$data->_catalogo_colaboradores_id,
            ':prorrateo_periodo_id'=>$data->_prorrateo_periodo_id
        );
        return $mysqli->queryAll($query, $params);
    }

    // Se procesa el incentivo y se asigna si es correcto
    public static function addIncentivoColaborador($data){
        $mysqli = Database::getInstance();
        $query=<<<sql
        INSERT INTO incentivos_asignados (colaborador_id, prorrateo_periodo_id, catalogo_incentivo_id, cantidad, asignado, valido) VALUES (:colaborador_id, :prorrateo_periodo_id, :catalogo_incentivo_id, :cantidad, :asignado, :valido);
sql;
        $params = array(
            ':colaborador_id'=>$data->_colaborador_id,
            ':prorrateo_periodo_id'=>$data->_prorrateo_periodo_id,
            ':catalogo_incentivo_id'=>$data->_catalogo_incentivo_id,
            ':cantidad'=>$data->_cantidad,
            ':asignado'=>$data->_asignado,
            ':valido'=>$data->_valido);

        $id = $mysqli->insert($query,$params);
        return $id;
    }

    public static function getIncentivosColaborador($idColaborador, $idPeriodo){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT * FROM `incentivos_asignados` INNER JOIN catalogo_incentivo ci USING (catalogo_incentivo_id) WHERE colaborador_id = "$idColaborador" AND prorrateo_periodo_id = "$idPeriodo"
sql;
        return $mysqli->queryAll($query);
    }

    public static function getIncentivosColaboradores($idColaborador){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT * FROM `incentivo_colaborador` 
        INNER JOIN catalogo_incentivo ci USING (catalogo_incentivo_id)
        WHERE catalogo_colaboradores_id = "$idColaborador"
sql;
        return $mysqli->queryAll($query);
    }

    public static function delete($id){
        $mysqli = Database::getInstance();
        $query=<<<sql
        DELETE FROM incentivos_asignados WHERE incentivos_asignados.incentivos_asignados_id = "$id"
sql;
        return $mysqli->update($query);
    }


    // Queries para buscar el periodo
    // y verificar que el usuario tiene un status bueno de asistencias o no

    // Obtener el periodo por su ID
    public static function getPeriodoById($id){
        $mysqli = Database::getInstance();
        $query =<<<sql
        SELECT * FROM prorrateo_periodo WHERE prorrateo_periodo_id = "$id"
sql;
        return $mysqli->queryOne($query);
    }

    // Busca al colaborador
    public static function getColaboradorDatos($data){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT * FROM catalogo_colaboradores WHERE pago = :pago AND catalogo_colaboradores_id = :catalogo_colaboradores_id
sql;
        $params = array(
            ':pago'=>$data->_pago, 
            ':catalogo_colaboradores_id'=>$data->_catalogo_colaboradores_id
        );
        return $mysqli->queryOne($query,$params);
    }

    // Busca, todos los dias del usuario con este horario
    public static function getHorarioLaboral($catalogo_colaboradores_id){
        $mysqli = Database::getInstance();
        $query=<<<sql
          SELECT
            ch.hora_entrada,
            ch.hora_salida,
            ch.tolerancia_entrada,
            ch.numero_retardos,
            dl.nombre AS dia_semana,
            ch.nombre horario,
            ch.catalogo_horario_id
          FROM catalogo_horario ch
          JOIN horario_dias_laborales hdl
          ON hdl.catalogo_horario_id = ch.catalogo_horario_id
          JOIN dias_laborales dl
          ON dl.dias_laborales_id = hdl.dias_laborales_id
          JOIN colaboradores_horario clh
          ON clh.catalogo_horario_id = ch.catalogo_horario_id
          WHERE clh.catalogo_colaboradores_id = "$catalogo_colaboradores_id"
          ORDER BY dl.dias_laborales_id
sql;
        return $mysqli->queryAll($query);
      }

    public static function getIncentivoColaborador($idColaborador){
        $mysqli = Database::getInstance();
        $query=<<<sql
            SELECT * FROM `incentivo_colaborador`
            INNER JOIN catalogo_incentivo ci USING (catalogo_incentivo_id)
            WHERE catalogo_colaboradores_id = "$idColaborador"
sql;
        return $mysqli->queryAll($query);
    }

    /*
    *
    *
    *
    *
    */


    public static function insertIncentivos($data){
        $mysqli = Database::getInstance();
        $query=<<<sql
        INSERT INTO incentivos_asignados (colaborador_id, prorrateo_periodo_id, catalogo_incentivo_id, cantidad, asignado, valido) VALUES (:colaborador_id, :prorrateo_periodo_id, :catalogo_incentivo_id, :cantidad, :asignado, :valido);
sql;
        $params = array(
            ':colaborador_id'=>$data->_colaborador_id,
            ':prorrateo_periodo_id'=>$data->_prorrateo_periodo_id,
            ':catalogo_incentivo_id'=>$data->_catalogo_incentivo_id,
            ':cantidad'=>$data->_cantidad,
            ':asignado'=>$data->_asignado,
            ':valido'=>$data->_valido
        );

        $id = $mysqli->insert($query,$params);
        return $id;
    }

    public static function eliminarIncentivos($data){
        $mysqli = Database::getInstance();
        $query=<<<sql
        DELETE FROM incentivos_asignados WHERE colaborador_id = :colaborador_id AND prorrateo_periodo_id = :prorrateo_periodo_id
sql;
        $params = array(
            ':colaborador_id'=>$data->_colaborador_id,
            ':prorrateo_periodo_id'=>$data->_prorrateo_periodo_id
        );

        $id = $mysqli->insert($query,$params);
        return $id;
    }

    public static function getPeriodosSemanales(){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT prorrateo_periodo_id FROM `prorrateo_periodo` WHERE tipo = "SEMANAL" LIMIT 1
sql;
        $id = $mysqli->queryOne($query);
        return $id;
    }

    public static function getPeriodosQuincenales(){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT prorrateo_periodo_id FROM `prorrateo_periodo` WHERE tipo = "QUINCENAL" LIMIT 1
sql;
        $id = $mysqli->queryOne($query);
        return $id;
    }

    // AGREGAR HORAS EXTRA
    public static function insertarHorasExtras($data){
        $mysqli = Database::getInstance();
        $query=<<<sql
        INSERT INTO prorrateo_horas_extra(catalogo_colaboradores_id, horas_extra, prorrateo_periodo_id) VALUES (:catalogo_colaboradores_id, :horas_extra, :prorrateo_periodo_id);
sql;
        $params = array(
            ':catalogo_colaboradores_id'=>$data->_catalogo_colaboradores_id,
            ':horas_extra'=>$data->_horas_extra,
            ':prorrateo_periodo_id'=>$data->_prorrateo_periodo_id
        );

        $id = $mysqli->insert($query,$params);
        return $id;
    }

    public static function getHorasExtraPeriodo($idColaborador, $idPeriodo){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT horas_extra FROM `prorrateo_horas_extra` WHERE catalogo_colaboradores_id = "$idColaborador" AND prorrateo_periodo_id = "$idPeriodo"
sql;
        $id = $mysqli->queryOne($query);
        return $id;
    }


    public static function updateHorasExtra($horas_extra, $catalogo_colaboradores_id, $prorrateo_periodo_id){
        $mysqli = Database::getInstance();
        $query=<<<sql
        UPDATE prorrateo_horas_extra SET horas_extra = '$horas_extra' WHERE catalogo_colaboradores_id = '$catalogo_colaboradores_id' AND prorrateo_periodo_id = '$prorrateo_periodo_id' ;
sql;
        $id = $mysqli->update($query);
        return $id;
    }

    public static function deleteHorasExtra($catalogo_colaboradores_id, $prorrateo_periodo_id){
        $mysqli = Database::getInstance();
        $query=<<<sql
        DELETE FROM prorrateo_horas_extra WHERE catalogo_colaboradores_id = '$catalogo_colaboradores_id' AND prorrateo_periodo_id = '$prorrateo_periodo_id' ;
sql;
        $id = $mysqli->update($query);
        return $id;
    }

    
    



}