<?php
namespace App\models;
defined("APPPATH") OR die("Access denied");

use \Core\Database;
use \App\interfaces\Crud;
use \App\controllers\UtileriasLog;

class Incentivo implements Crud{

    public static function getAll(){
        $mysqli = Database::getInstance();
        $query=<<<sql

sql;
        return $mysqli->queryAll($query);
    }

    public static function getById($id){

    }

    public static function insert($data){

    }

    public static function update($data){

    }

    public static function getColaboradores($tipo, $perfilUsuario, $catalogoDepartamentoId, $catalogoPlantaId, $estatusRH){
        $mysqli = Database::getInstance();

        if($perfilUsuario == 1 || $perfilUsuario == 4){            
            $query=<<<sql
SELECT 
cc.catalogo_colaboradores_id, cc.identificador_noi, cc.nombre, cc.apellido_paterno, cc.apellido_materno, cc.numero_identificador, cc.catalogo_departamento_id,
cc.pago, cc.foto, cd.nombre AS nombre_departamento, cp.nombre AS nombre_puesto, cu.nombre nombre_ubicacion, cc.catalogo_ubicacion_id
FROM catalogo_colaboradores cc 
INNER JOIN catalogo_departamento cd USING (catalogo_departamento_id)
INNER JOIN catalogo_puesto cp USING (catalogo_puesto_id)
INNER JOIN catalogo_ubicacion cu USING (catalogo_ubicacion_id) 
sql;
            if($estatusRH == 1){
                $query.=<<<sql
WHERE cc.pago = "$tipo" AND cc.status = 1 AND cc.catalogo_ubicacion_id = "$catalogoPlantaId"
sql;
            }

            if($estatusRH == 2){
                $query.=<<<sql
WHERE cc.pago = "$tipo" AND cc.status = 1 AND cc.catalogo_ubicacion_id = "$catalogoPlantaId" AND cc.catalogo_departamento_id = "$catalogoDepartamentoId"
sql;
            }

        }

        // PERFIL PARA 4 "Administrador" y 5 "Personalizado"
        if($perfilUsuario == 5){
            $query =<<<sql
SELECT 
cc.catalogo_colaboradores_id, cc.identificador_noi, cc.nombre, cc.apellido_paterno, cc.apellido_materno, cc.numero_identificador, cc.catalogo_departamento_id,
cc.pago, cc.foto, cd.nombre AS nombre_departamento, cp.nombre AS nombre_puesto, cu.nombre nombre_ubicacion, cc.catalogo_ubicacion_id
FROM catalogo_colaboradores cc 
INNER JOIN catalogo_departamento cd USING (catalogo_departamento_id)
INNER JOIN catalogo_puesto cp USING (catalogo_puesto_id)
INNER JOIN catalogo_ubicacion cu USING (catalogo_ubicacion_id)
WHERE cc.pago = "$tipo" AND cc.status = 1 AND cc.catalogo_departamento_id = "$catalogoDepartamentoId"
sql;

        }

        if($perfilUsuario == 6){
            $query=<<<sql
SELECT 
cc.catalogo_colaboradores_id, cc.identificador_noi, cc.nombre, cc.apellido_paterno, cc.apellido_materno, cc.numero_identificador, cc.catalogo_departamento_id,
cc.pago, cc.foto, cd.nombre AS nombre_departamento, cp.nombre AS nombre_puesto, cu.nombre nombre_ubicacion, cc.catalogo_ubicacion_id
FROM catalogo_colaboradores cc 
INNER JOIN catalogo_departamento cd USING (catalogo_departamento_id)
INNER JOIN catalogo_puesto cp USING (catalogo_puesto_id)
INNER JOIN catalogo_ubicacion cu USING (catalogo_ubicacion_id) 
sql;
            if($estatusRH == 1){
                $query.=<<<sql
WHERE cc.pago = "$tipo" AND cc.status = 1 AND cc.catalogo_ubicacion_id = "$catalogoPlantaId"
sql;
            }

            if($estatusRH == 2){
                $query.=<<<sql
WHERE cc.pago = "$tipo" AND cc.status = 1 
sql;
            }


        }


        return $mysqli->queryAll($query);
    }

    /*
        Busqueda de incentivo 
        @params
        @tipo: SEMANAL O QUINCENAL
        @statis: 1 es Cerrado y 0 es Cerrado
    */

    public static function searchPeriodos($tipo, $status){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT * FROM prorrateo_periodo 
WHERE tipo = "$tipo" AND status = "$status" 
ORDER BY prorrateo_periodo.fecha_inicio DESC
sql;
        return $mysqli->queryAll($query);
    }

    public static function searchLastPeriodos($tipo, $status){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT * FROM prorrateo_periodo WHERE tipo = "SEMANAL" AND status != 0 ORDER BY fecha_inicio ASC LIMIT 1 
sql;
        return $mysqli->queryAll($query);
    }

    /*
        Busqueda de incentivos por cada colaborador
        @idColaborador: Id del colaborador a buscar
    */
    public static function getIncentivosPorColabordor($idColaborador){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT * FROM incentivo_colaborador ic 
INNER JOIN catalogo_incentivo ci USING (catalogo_incentivo_id) 
WHERE ic.catalogo_colaboradores_id = "$idColaborador" AND ic.catalogo_incentivo_id != 47 AND ic.catalogo_incentivo_id != 48 
sql;
        return $mysqli->queryAll($query);
    }

    /*
        Obtiene el periodo 
        @tipoPeriodo: SEMANAL O QUINCENAL
    */
    public static function getTipoPeriodo($tipoPeriodo){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT * FROM prorrateo_periodo  
WHERE tipo = "$tipoPeriodo" AND status != 0
ORDER BY prorrateo_periodo.fecha_inicio  DESC
sql;
        return $mysqli->queryAll($query);
    }

    public static function getAllperiodosSemanales(){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT * FROM prorrateo_periodo WHERE tipo = "SEMANAL" ORDER BY prorrateo_periodo_id DESC 
sql;
        return $mysqli->queryAll($query);
    }

    /*
        Obtiene el periodo 
        @idPeriodo: id del periodo
    */
    public static function getPeriodo($idPeriodo){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT * FROM prorrateo_periodo  
WHERE prorrateo_periodo_id = "$idPeriodo"
sql;
        return $mysqli->queryOne($query);
    }

   public static function getPeriodoLast(){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT * FROM prorrateo_periodo WHERE tipo = "SEMANAL" ORDER BY prorrateo_periodo.prorrateo_periodo_id DESC LIMIT 1 
sql;
        return $mysqli->queryOne($query);
    }

     /*
        Obtiene el periodo ultimo periodo ya sea semanal o quincenal
        @idPeriodo: id del periodo
    */
    public static function getUltimoPeriodoHistorico($tipo){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT * FROM prorrateo_periodo 
WHERE tipo = "$tipo" AND status != 0
ORDER BY prorrateo_periodo.fecha_inicio DESC LIMIT 1 
sql;
        return $mysqli->queryOne($query);
    }

    /*
        Obtiene las horas extra del colaborador dependiendo el periodo
        @idColaborador -> Entero 
        @idPeriodo -> Periodo / status Abierto "0" o status cerrado "1"
    */
    public static function getHorasExtraPeriodo($idColaborador, $idPeriodo){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT horas_extra FROM prorrateo_horas_extra WHERE catalogo_colaboradores_id = "$idColaborador" AND prorrateo_periodo_id = "$idPeriodo"
sql;
        $id = $mysqli->queryOne($query);
        return $id;
    }

    /*
        Obtiene los datos del colaborador
        @id-> Id del colaborador
    */
    public static function getColaborador($id){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT cc.catalogo_colaboradores_id, cc.nombre, cc.apellido_paterno, cc.apellido_materno, cc.sexo, cc.numero_empleado, cc.numero_identificador, cu.nombre AS nombre_ubicacion, cl.nombre AS nombre_lector, cp.nombre AS nombre_puesto, cc.foto, cc.pago, cl.catalogo_lector_id, cc.clave_noi
FROM catalogo_colaboradores cc
INNER JOIN catalogo_ubicacion cu USING (catalogo_ubicacion_id)
INNER JOIN catalogo_lector cl USING (catalogo_lector_id)
INNER JOIN catalogo_puesto cp USING (catalogo_puesto_id)
WHERE cc.catalogo_colaboradores_id = $id
sql;
        return $mysqli->queryOne($query);
    }

    /*
        Busca si existe horas extra
    */
    public static function buscarHorasExtra($colaboradorId, $periodoId){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT * FROM prorrateo_horas_extra WHERE catalogo_colaboradores_id = "$colaboradorId" AND prorrateo_periodo_id = "$periodoId" 
sql;
        $id = $mysqli->update($query);
        return $id;
    }

    /*
        Inserta las horas extra de un colaborador
    */

    public static function insertHorasExtraColaborador($data){
        $mysqli = Database::getInstance();
        $query=<<<sql
INSERT INTO prorrateo_horas_extra (catalogo_colaboradores_id, horas_extra, prorrateo_periodo_id) VALUES (:catalogo_colaboradores_id, :horas_extra, :prorrateo_periodo_id)
sql;
        $parametros = array(
          ':catalogo_colaboradores_id'=>$data->_catalogo_colaboradores_id,
          ':horas_extra'=>$data->_horas_extra,
          ':prorrateo_periodo_id'=>$data->_prorrateo_periodo_id
        );

        $id = $mysqli->insert($query,$parametros);
        $accion = new \stdClass();
        $accion->_sql= $query;
        $accion->_parametros = $parametros;
        $accion->_id = $id;

        UtileriasLog::addAccion($accion);
        return $id;
    }

    /*
        Elimina la hora extra
    */
    public static function deleteHorasExtra($catalogo_colaboradores_id, $prorrateo_periodo_id){
        $mysqli = Database::getInstance();
        $query=<<<sql
        DELETE FROM prorrateo_horas_extra WHERE catalogo_colaboradores_id = '$catalogo_colaboradores_id' AND prorrateo_periodo_id = '$prorrateo_periodo_id' ;
sql;
        $id = $mysqli->update($query);
        return $id;
    }

    /*
        Inserta la hora extra si ha sido cambiada
    */
    public static function updateHorasExtra($horas_extra, $catalogo_colaboradores_id, $prorrateo_periodo_id){
        $mysqli = Database::getInstance();
        $query=<<<sql
        UPDATE prorrateo_horas_extra SET horas_extra = '$horas_extra' WHERE catalogo_colaboradores_id = '$catalogo_colaboradores_id' AND prorrateo_periodo_id = '$prorrateo_periodo_id' ;
sql;
        $id = $mysqli->update($query);
        return $id;
    }

    /*
        Cantidad de incentivos que se le han asignado al colaborador
    */
    public static function getIncentivosColaboradorAsignados($idColaborador,$idPeriodo){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT * FROM incentivos_asignados i 
        INNER JOIN catalogo_incentivo ci ON (ci.catalogo_incentivo_id = i.catalogo_incentivo_id)
        INNER JOIN prorrateo_periodo pp ON (pp.prorrateo_periodo_id = i.prorrateo_periodo_id) 
        WHERE i.colaborador_id = "$idColaborador" AND i.prorrateo_periodo_id = "$idPeriodo"
sql;
        return $mysqli->queryAll($query);
    }

    public static function getIncentivosColaboradorResumen($idColaborador,$idPeriodo){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT ia.incentivos_asignados_id, ia.colaborador_id, ia.prorrateo_periodo_id, ia.cantidad, ci.nombre, ci.tipo, ci.fijo, ci.repetitivo,  ci.descripcion, ci.catalogo_incentivo_id, ia.asignado 
        FROM incentivos_asignados AS ia 
        INNER JOIN catalogo_incentivo AS ci USING(catalogo_incentivo_id) 
        WHERE ia.colaborador_id = "$idColaborador" AND ia.prorrateo_periodo_id = "$idPeriodo"
sql;
        return $mysqli->queryAll($query);
    }

    public static function getIncentivosColaboradorResumenTest($idColaborador,$idPeriodo){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT * 
        FROM incentivos_asignados AS ia 
        INNER JOIN catalogo_incentivo AS ci USING(catalogo_incentivo_id) 
        WHERE ia.colaborador_id = "$idColaborador" AND ia.prorrateo_periodo_id = "$idPeriodo"
sql;
        return $mysqli->queryAll($query);
    }

    /*
        Obtiene los incentivos que se podran asignar al colaborador
    */
    public static function getIncentivoColaborador($idColaborador){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT * FROM incentivo_colaborador ic 
INNER JOIN catalogo_incentivo ci USING (catalogo_incentivo_id) 
WHERE ic.catalogo_colaboradores_id = "$idColaborador" AND ic.catalogo_incentivo_id != 47 AND ic.catalogo_incentivo_id != 48 
sql;
        return $mysqli->queryAll($query);
    }

    public static function getSumaIncentivos($idColaborador){
        $mysqli = Database::getInstance();
        $query=<<<sql
            SELECT SUM(cantidad) AS suma_incentivos FROM incentivo_colaborador
            INNER JOIN catalogo_incentivo ci USING (catalogo_incentivo_id)
            WHERE catalogo_colaboradores_id = "$idColaborador"
sql;
        return $mysqli->queryOne($query);
    }


    public static function getSumaIncentivosAsginados($idColaborador, $prorrateo_periodo_id){
        $mysqli = Database::getInstance();
        $query=<<<sql
            SELECT SUM(cantidad) AS cantidad_incentivos_asignados FROM incentivos_asignados WHERE prorrateo_periodo_id = "$prorrateo_periodo_id" AND colaborador_id = "$idColaborador" 
sql;
        return $mysqli->queryOne($query);
    }


    /*
        Agregar los incentivos que se pueden asignar al colaborador
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

    /*
        Elimina los incentivos que se le asignaron al colaborador
    */
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

    public static function eliminarIncentivosSeleccionado($id){
        $mysqli = Database::getInstance();
        $query=<<<sql
        DELETE FROM incentivos_asignados WHERE incentivos_asignados.incentivos_asignados_id = "$id"
sql;
        $id = $mysqli->delete($query);
        return $id;
    }

    public static function eliminarIncentivosColaborador($data){
        $mysqli = Database::getInstance();
        $query=<<<sql
        DELETE FROM incentivos_asignados WHERE colaborador_id = :colaborador_id AND prorrateo_periodo_id = :prorrateo_periodo_id
sql;
        $params = array(
            ':colaborador_id'=>$data->_colaborador_id,
            ':prorrateo_periodo_id'=>$data->_prorrateo_periodo_id
        );

        $id = $mysqli->delete($query,$params);
        return $id;
    }


    /*
        Elimina los incentivos que ya han sido asignados al colaborador
    */
    public static function delete($id){
        $mysqli = Database::getInstance();
        $query=<<<sql
        DELETE FROM incentivos_asignados WHERE incentivos_asignados.incentivos_asignados_id = "$id"
sql;
        return $mysqli->update($query);
    }

    /*
        Agregar el domingo_laborado
    */
    public static function insertDomingoProcesos($data){
        $mysqli = Database::getInstance();
        $query=<<<sql
        INSERT INTO prorrateo_domigo_procesos (catalogo_colaboradores_id, prorrateo_periodo_id, domigo_procesos) VALUES (:catalogo_colaboradores_id, :prorrateo_periodo_id, :domigo_procesos);
sql;

        $params = array(
            ':catalogo_colaboradores_id'=>$data->_catalogo_colaboradores_id,
            ':prorrateo_periodo_id'=>$data->_prorrateo_periodo_id,
            ':domigo_procesos'=>$data->_domigo_procesos
        );

        $id = $mysqli->insert($query,$params);
        return $id;
    }

    /*
        Agregar el domingo_de_procesos
    */
    public static function insertDomingoLaborado($data){
        $mysqli = Database::getInstance();
        $query=<<<sql
        INSERT INTO prorrateo_domigo_laborado (catalogo_colaboradores_id, prorrateo_periodo_id, domingo_laborado) VALUES (:catalogo_colaboradores_id, :prorrateo_periodo_id, :domingo_laborado)
sql;

        $params = array(
            ':catalogo_colaboradores_id'=>$data->_catalogo_colaboradores_id,
            ':prorrateo_periodo_id'=>$data->_prorrateo_periodo_id,
            ':domingo_laborado'=>$data->_domingo_laborado
        );

        $id = $mysqli->insert($query,$params);
        return $id;
    }

    /*
        Agregar el domingo_de_procesos
    */
    public static function insertIncentivoNoche($data){
        $mysqli = Database::getInstance();
        $query=<<<sql
        INSERT INTO prorrateo_incentivo_noche (catalogo_colaboradores_id, prorrateo_periodo_id, incentivo_noche) VALUES (:catalogo_colaboradores_id, :prorrateo_periodo_id, :incentivo_noche)
sql;

        $params = array(
            ':catalogo_colaboradores_id'=>$data->_catalogo_colaboradores_id,
            ':prorrateo_periodo_id'=>$data->_prorrateo_periodo_id,
            ':incentivo_noche'=>$data->_incentivo_noche
        );

        $id = $mysqli->insert($query,$params);
        return $id;
    }


    /*
        Agrega la cantidad de proceosos laborados cuando es dia domingo de procesos
    */
    public static function getDomingoProcesos($idColaborador, $idPeriodo){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT * FROM prorrateo_domigo_procesos WHERE catalogo_colaboradores_id = "$idColaborador" AND prorrateo_periodo_id = "$idPeriodo"
sql;
        return $mysqli->queryOne($query);
    }

    /*
        Agrega la cantidad de dia laborar cuando se trabaja en domingo
    */
    public static function getDomingoLaborado($idColaborador, $idPeriodo){
        $mysqli = Database::getInstance();
        $query = <<<sql
        SELECT * FROM prorrateo_domigo_laborado WHERE catalogo_colaboradores_id = "$idColaborador" AND prorrateo_periodo_id = "$idPeriodo"
sql;
        return $mysqli->queryOne($query);
    }

    /*
        Agrega la cantidad de incentivos noche
    */
    public static function getIncentivosNoche($idColaborador, $idPeriodo){
        $mysqli = Database::getInstance();
        $query = <<<sql
        SELECT * FROM prorrateo_incentivo_noche WHERE catalogo_colaboradores_id = "$idColaborador" AND prorrateo_periodo_id = "$idPeriodo"
sql;
        return $mysqli->queryOne($query);
    }

    public static function redelete($tabla, $idColaborador, $idPeriodo){
        $mysqli = Database::getInstance();
        $query = <<<sql
        DELETE FROM {$tabla} WHERE catalogo_colaboradores_id = "$idColaborador" AND prorrateo_periodo_id = "$idPeriodo"
sql;
        return $mysqli->update($query);   
    }

    public static function reInsert($tabla, $campo, $idPeriodo, $idColaborador, $cantidad){
        $mysqli = Database::getInstance();
        $query = <<<sql
        INSERT INTO {$tabla} (catalogo_colaboradores_id, prorrateo_periodo_id, {$campo} VALUES ('$idColaborador', '$idColaborador', '$cantidad')
sql;
        return $mysqli->insert($query);   
    }

    public static function getIncentivoById($idColaborador, $idPeriodo, $idIncentivo){
        $mysqli = Database::getInstance();
        $query = <<<sql
        SELECT incentivos_asignados_id, cantidad FROM incentivos_asignados WHERE colaborador_id = "$idColaborador" AND prorrateo_periodo_id = "$idPeriodo" AND catalogo_incentivo_id = "$idIncentivo"
sql;
        return $mysqli->queryOne($query);
    }

    public static function getSalarioDiario($clave){
        $mysqli = Database::getInstance();
        $query = <<<sql
        SELECT * FROM operacion_noi WHERE clave = :clave 
sql;
        return $mysqli->queryOne($query, array(':clave'=>$clave));
    }

    public static function getIncentivoBotes($catalogo_colaboradores_id){
        $mysqli = Database::getInstance();
        $query = <<<sql
SELECT * FROM incentivo_colaborador AS ic INNER JOIN catalogo_incentivo AS ci ON (ic.catalogo_incentivo_id = ci.catalogo_incentivo_id) WHERE ic.catalogo_incentivo_id = "47" AND ic.catalogo_colaboradores_id = "$catalogo_colaboradores_id"  
sql;
        return $mysqli->queryOne($query);
    }

    public static function getIncentivoBotesMeta($catalogo_colaboradores_id){
        $mysqli = Database::getInstance();
        $query = <<<sql
SELECT * FROM incentivo_colaborador AS ic INNER JOIN catalogo_incentivo AS ci ON (ic.catalogo_incentivo_id = ci.catalogo_incentivo_id) WHERE ic.catalogo_incentivo_id = "48" AND ic.catalogo_colaboradores_id = "$catalogo_colaboradores_id"  
sql;
        return $mysqli->queryOne($query);
    }


    public static function insertIncentivoBotes($data){
        $mysqli = Database::getInstance();
        $query = <<<sql
INSERT INTO incentivos_asignados (colaborador_id, prorrateo_periodo_id, catalogo_incentivo_id, cantidad, asignado, valido) VALUES (:colaborador_id, :prorrateo_periodo_id, :catalogo_incentivo_id, :cantidad, '0', '0');
sql;
        $params = array(
            ':colaborador_id'=>$data->_colaborador_id,
            ':prorrateo_periodo_id'=>$data->_prorrateo_periodo_id,
            ':catalogo_incentivo_id'=>$data->_catalogo_incentivo_id,
            ':cantidad'=>$data->_cantidad
        );
        return $mysqli->insert($query, $params);
    }


    public static function getBusquedaIncentivoBotes($data){
        $mysqli = Database::getInstance();
        $query = <<<sql
SELECT * FROM incentivos_asignados WHERE colaborador_id = $data->_colaborador_id AND prorrateo_periodo_id = $data->_prorrateo_periodo_id AND catalogo_incentivo_id = $data->_catalogo_incentivo_id 
sql;
        return $mysqli->queryOne($query);
    }

    public static function deleteIncentivoBotes($data){
        $mysqli = Database::getInstance();
        $query = <<<sql
DELETE FROM incentivos_asignados WHERE colaborador_id = :colaborador_id AND prorrateo_periodo_id = :prorrateo_periodo_id AND catalogo_incentivo_id = :catalogo_incentivo_id
sql;
        $params = array(
            ':colaborador_id'=>$data->_colaborador_id,
            ':prorrateo_periodo_id'=>$data->_prorrateo_periodo_id,
            ':catalogo_incentivo_id'=>$data->_catalogo_incentivo_id,
            ':cantidad'=>$data->_cantidad
        );
        return $mysqli->delete($query,$params);
    }

    public static function updateIncentivoBotes($incentivos_asignados_id, $cantidad){
        $mysqli = Database::getInstance();
        $query = <<<sql
UPDATE incentivos_asignados SET cantidad = "$cantidad" WHERE incentivos_asignados_id = $incentivos_asignados_id
sql;
        return $mysqli->update($query);
    }

    public static function getAllBotesPeriodo(){
        $mysqli = Database::getInstance();
        $query = <<<sql
SELECT * FROM botes ORDER BY prorrateo_periodo_id DESC 
sql;
        return $mysqli->queryAll($query);
    }

    public static function searchPeriodoBotes($data){
        $mysqli = Database::getInstance();
        $query = <<<sql
SELECT * FROM botes WHERE prorrateo_periodo_id = :prorrateo_periodo_id
sql;
        $params = array(
            ':prorrateo_periodo_id'=>$data->_prorrateo_periodo_id
        );
        return $mysqli->queryOne($query,$params);
    }

     public static function insertNewBotesMetas($data){
        $mysqli = Database::getInstance();
        $query = <<<sql
INSERT INTO botes (clara, yema, huevo_liquido, prorrateo_periodo_id) VALUES (:clara, :yema, :huevo_liquido, :prorrateo_periodo_id);
sql;
        $params = array(
            ':prorrateo_periodo_id'=>$data->_prorrateo_periodo_id,
            ':clara'=>$data->_clara,
            ':yema'=>$data->_yema,
            ':huevo_liquido'=>$data->_huevo_liquido
        );

        return $mysqli->insert($query, $params);
    }

    public static function getBotesByIdPerido($periodo){
        $mysqli = Database::getInstance();
        $query = <<<sql
SELECT * FROM botes WHERE prorrateo_periodo_id = "$periodo"
sql;
        return $mysqli->queryOne($query);
    }

    public static function deleteMetaBotes($id){
        $mysqli = Database::getInstance();
        $query = <<<sql
DELETE FROM botes WHERE botes_id = "$id"
sql;
        return $mysqli->delete($query);
    }

    public static function updateMetaBotes($data){
        $mysqli = Database::getInstance();
        $query = <<<sql
UPDATE botes SET clara = :clara, yema = :yema, huevo_liquido = :huevo_liquido WHERE prorrateo_periodo_id = :prorrateo_periodo_id;
sql;
        $params = array(
            ':prorrateo_periodo_id'=>$data->_prorrateo_periodo_id,
            ':clara'=>$data->_clara,
            ':yema'=>$data->_yema,
            ':huevo_liquido'=>$data->_huevo_liquido
        );

        return $mysqli->update($query,$params);
    }

    public static function getAllIncentivosAsignadosResumen(){
        $mysqli = Database::getInstance();
        $query = <<<sql

sql;
        return $mysqli->queryAll($query);
    }
    
    public static function insertBotesNuevos($data){
        $mysqli = Database::getInstance();
        $query = <<<sql
INSERT INTO incentivos_asignados (colaborador_id, prorrateo_periodo_id, catalogo_incentivo_id, cantidad, asignado, valido) VALUES (:colaborador_id, :prorrateo_periodo_id, :catalogo_incentivo_id, :cantidad, :asignado, '0');
sql;
        $params = array(
            ':colaborador_id'=>$data->_colaborador_id,
            ':prorrateo_periodo_id'=>$data->_prorrateo_periodo_id,
            ':catalogo_incentivo_id'=>$data->_catalogo_incentivo_id,
            ':cantidad'=>$data->_cantidad,
            ':asignado'=>$data->_precio_bote
        );

        return $mysqli->insert($query, $params);
    }

    public static function busquedaBotesNuevos($data){
        $mysqli = Database::getInstance();
        $query = <<<sql
SELECT * FROM incentivos_asignados WHERE colaborador_id = :colaborador_id AND prorrateo_periodo_id = :prorrateo_periodo_id AND catalogo_incentivo_id = :catalogo_incentivo_id AND cantidad = :cantidad 
sql;
        $params = array(
            ':colaborador_id'=>$data->_colaborador_id,
            ':prorrateo_periodo_id'=>$data->_prorrateo_periodo_id,
            ':catalogo_incentivo_id'=>$data->_catalogo_incentivo_id,
            ':cantidad'=>$data->_cantidad
        );

        return $mysqli->queryOne($query, $params);
    }

    public static function busquedaBotesNuevos1($data){
        $mysqli = Database::getInstance();
        $query = <<<sql
SELECT * FROM incentivos_asignados WHERE colaborador_id = :colaborador_id AND prorrateo_periodo_id = :prorrateo_periodo_id AND catalogo_incentivo_id = :catalogo_incentivo_id 
sql;
        $params = array(
            ':colaborador_id'=>$data->_colaborador_id,
            ':prorrateo_periodo_id'=>$data->_prorrateo_periodo_id,
            ':catalogo_incentivo_id'=>$data->_catalogo_incentivo_id
        );

        return $mysqli->queryOne($query, $params);
    }

    public static function deleteIncentivoBotes47($id){
        $mysqli = Database::getInstance();
        $query = <<<sql
DELETE FROM incentivos_asignados WHERE incentivos_asignados_id = "$id"
sql;
        return $mysqli->delete($query);
    }

    public static function updateIncentivo48($cantidad, $incentivos_asignados_id){
        $mysqli = Database::getInstance();
        $query = <<<sql
UPDATE incentivos_asignados SET cantidad = "$cantidad" WHERE incentivos_asignados_id = "$incentivos_asignados_id";
sql;
        return $mysqli->update($query);
    }


    public static function updateIncentivoBotesAsignado($id){
        $mysqli = Database::getInstance();
        $query = <<<sql
DELETE FROM incentivos_asignados WHERE incentivos_asignados_id = "$id"
sql;
        return $mysqli->delete($query);
    }

    public static function updateIncentivoBotesAsignados($cantidad, $asignado, $incentivos_asignados_id){
        $mysqli = Database::getInstance();
        $query = <<<sql
UPDATE incentivos_asignados SET cantidad = $cantidad, asignado = "$asignado" WHERE incentivos_asignados_id = "$incentivos_asignados_id"; 
sql;
        return $mysqli->update($query);
    }

    public static function getMetaBotes($idPeriodo){
        $mysqli = Database::getInstance();
        $query = <<<sql
SELECT * FROM botes WHERE prorrateo_periodo_id = "$idPeriodo" 
sql;
        return $mysqli->queryAll($query);
    }

    public static function getPrecioCompletoBotes(){
        $mysqli = Database::getInstance();
        $query = <<<sql
SELECT * FROM pago_botes WHERE pago_botes_id = 1 
sql;
        return $mysqli->queryOne($query);
    }

    public static function getPrecioNoCompletoBotes(){
        $mysqli = Database::getInstance();
        $query = <<<sql
SELECT * FROM pago_botes WHERE pago_botes_id = 2 
sql;
        return $mysqli->queryOne($query);
    }

    public static function updatePrecioBotes($data){
        $mysqli = Database::getInstance();
        $query = <<<sql
UPDATE pago_botes SET clara = :clara, yema = :yema, huevo_liquido = :huevo_liquido, mtime = NOW() WHERE pago_botes_id = :pago_botes_id 
sql;
        $params = array(
            ':clara'=>$data->_clara,
            ':yema'=>$data->_yema,
            ':huevo_liquido'=>$data->_huevo_liquido,
            ':pago_botes_id'=>$data->_pago_botes_id
        );
        return $mysqli->update($query, $params);
    }

    public static function updateIdValorBotes($incentivos_asignados_id, $cantidad, $asignado){
        $mysqli = Database::getInstance();
        $query = <<<sql
UPDATE incentivos_asignados SET cantidad = "$cantidad", asignado = "$asignado" WHERE incentivos_asignados_id = "$incentivos_asignados_id";
sql;
        return $mysqli->update($query);
    }

    
}
