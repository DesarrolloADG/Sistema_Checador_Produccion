<?php
namespace App\models;
defined("APPPATH") OR die("Access denied");

use \Core\Database;
use \Core\MasterDom;
use \App\interfaces\Crud;
use \App\controllers\UtileriasLog;

class Prorrateo implements Crud{

    public static function getAll(){}

    public static function getAllColaboradoresPago($id_administrador){
      $where = ($id_administrador != '')? " AND uad.id_administrador = $id_administrador": '';
      $distinct = ($id_administrador != '')? '': ' DISTINCT ';
      $mysqli = Database::getInstance();
      $query=<<<sql
SELECT $distinct c.catalogo_colaboradores_id, c.numero_empleado, c.nombre, c.apellido_paterno, c.apellido_materno, c.horario_tipo, d.nombre AS nombre_departamento
FROM catalogo_colaboradores AS c 
INNER JOIN catalogo_departamento AS d ON (c.catalogo_departamento_id = d.catalogo_departamento_id)   
JOIN utilerias_administradores_departamentos uad ON uad.catalogo_departamento_id = c.catalogo_departamento_id
WHERE c.pago = 'Semanal' $where AND c.status = 1 
sql;
      return $mysqli->queryAll($query);
    }

    public static function getColaboradores($tipo){
      $mysqli = Database::getInstance();
/*
SELECT * FROM operacion_noi cn
INNER JOIN catalogo_colaboradores cc ON (cn.clave = cc.clave_noi)
WHERE cc.pago = "$tipo"
*/
      $query = <<<sql
SELECT cc.catalogo_colaboradores_id, cn.clave, cc.nombre, cc.apellido_paterno, cc.apellido_materno, cn.sal_diario, cn.sdi, cl.nombre AS nombre_planta FROM operacion_noi cn INNER JOIN catalogo_colaboradores cc ON (cn.clave = cc.clave_noi) AND (cn.identificador = cc.identificador_noi) INNER JOIN catalogo_lector cl ON (cl.catalogo_lector_id = cc.catalogo_lector_id) WHERE cc.pago = "$tipo" AND cc.clave_noi != "" 
sql;
      return $mysqli->queryAll($query);
    }

    public static function getColaboradoresProrrateo($tipo, $identificador){
      $mysqli = Database::getInstance();
      $query = <<<sql
SELECT cc.catalogo_colaboradores_id, cn.clave, cc.nombre, cc.apellido_paterno, cc.apellido_materno, cn.sal_diario, cn.sdi, cl.nombre AS nombre_planta, cl.identificador 
FROM operacion_noi cn 
INNER JOIN catalogo_colaboradores cc ON (cn.clave = cc.clave_noi) AND (cn.identificador = cc.identificador_noi) 
INNER JOIN catalogo_lector cl ON (cl.catalogo_lector_id = cc.catalogo_lector_id) 
WHERE cc.pago = "$tipo" AND cc.clave_noi != "" AND cc.identificador_noi = "$identificador" AND cc.status = 1 ORDER BY `cc`.`apellido_paterno` ASC
sql;
      return $mysqli->queryAll($query);
    }

    public static function busquedaFaltas($catalogo_colaboradores_id, $prorrateo_periodo_id, $identificador){
      $mysqli = Database::getInstance();
     
      /*
      $query = <<<sql
SELECT fecha, estatus FROM prorrateo_periodo_colaboradores WHERE catalogo_colaboradores_id = "$catalogo_colaboradores_id" AND prorrateo_periodo_id = "$prorrateo_periodo_id" AND estatus != 0
sql;
      */
      $query =<<<sql
SELECT fecha, estatus FROM catalogo_colaboradores cc
INNER JOIN prorrateo_periodo_colaboradores AS ppc USING (catalogo_colaboradores_id)
WHERE clave_noi = "$catalogo_colaboradores_id" AND prorrateo_periodo_id = "$prorrateo_periodo_id"
AND ppc.estatus != 0 AND cc.status = 1
AND cc.identificador_noi like "$identificador"
sql;


      return $mysqli->queryAll($query);
    }

    public static function getIdentificadorIncidencia($id){
      $mysqli = Database::getInstance();
      $query=<<<sql
SELECT identificador_incidencia AS id_i FROM catalogo_incidencia WHERE catalogo_incidencia_id = $id
sql;
      return $mysqli->queryOne($query);
    }    

    public static function insert($empresa){
	    $mysqli = Database::getInstance(1);
      $query=<<<sql
sql;
        $parametros = array();
        $id = $mysqli->insert($query,$parametros);
        $accion = new \stdClass();
        $accion->_sql= $query;
        $accion->_parametros = $parametros;
        $accion->_id = $id;
        UtileriasLog::addAccion($accion);
        return $id;
    }

    public static function update($empresa){}
    public static function delete($id){}

    public static function getById($id){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT 
        * 
      FROM catalogo_colaboradores 
      WHERE catalogo_colaboradores_id = $id
sql;
      return $mysqli->queryOne($query);
    }

    public static function getPeriodos(){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT 
        *,
        if(status = 0, 'Abierto', 'Cerrado') AS status_name
      FROM prorrateo_periodo 
      WHERE tipo='SEMANAL'
      ORDER BY prorrateo_periodo_id DESC
sql;
      return $mysqli->queryAll($query);
    }

    public static function getPeriodoById($periodo_id){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT 
        * 
      FROM prorrateo_periodo 
      WHERE prorrateo_periodo_id = $periodo_id
sql;
      return $mysqli->queryOne($query);
    }

    public static function getLastPeriodoProcesado(){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT * FROM prorrateo_periodo WHERE tipo = "SEMANAL" AND status > 1
sql;
      return $mysqli->queryOne($query);
    }

    public static function getAdministradorId($usuario){
      $mysqli = Database::getInstance();
      $query =<<<sql
      SELECT administrador_id, nombre, usuario, perfil_id, tipo FROM utilerias_administradores WHERE usuario = '$usuario'
sql;
      return $mysqli->queryOne($query);
    }

    public static function getFaltasColaborador($colaborador_id, $periodo_id){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT * 
      FROM prorrateo_periodo_colaboradores 
      WHERE estatus = -1
      AND catalogo_colaboradores_id = "$colaborador_id" 
      AND prorrateo_periodo_id = "$periodo_id" 
sql;
      return $mysqli->queryAll($query);
    }

    public static function getFaltasColaboradorFaltas($colaborador_id, $periodo_id){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT * FROM prorrateo_periodo_colaboradores WHERE prorrateo_periodo_id = "$colaborador_id" AND catalogo_colaboradores_id = "$periodo_id" 
sql;
      return $mysqli->queryAll($query);
    }

    public static function getIncidenciaColaborador($datos){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT 
        * 
      FROM prorrateo_colaboradores_incidencia
      WHERE catalogo_colaboradores_id = $datos->_catalogo_colaboradores_id
      AND fecha_incidencia >= '$datos->_fecha_inicio'
      AND fecha_incidencia <= '$datos->_fecha_fin'
      $datos->_where
sql;
      return $mysqli->queryAll($query);
    }

    public static function getIncentivosColaborador($datos){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT 
        * 
      FROM incentivos_asignados
      WHERE 
      colaborador_id = $datos->_catalogo_colaboradores_id
      AND prorrateo_periodo_id = $datos->_periodo_id
sql;
      return $mysqli->queryAll($query);
    }

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
          WHERE clh.catalogo_colaboradores_id = $catalogo_colaboradores_id
          ORDER BY dl.dias_laborales_id
sql;
        return $mysqli->queryAll($query);
      }

    public static function getByIdReporte($id){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT 
          c.catalogo_colaboradores_id,
          c.nombre,
          c.apellido_paterno,
          c.apellido_materno,
          c.numero_empleado
        FROM catalogo_colaboradores c 
        WHERE c.catalogo_colaboradores_id = $id
sql;
      return $mysqli->queryOne($query);
    }

    public static function getIncentivosById($id){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT 
          i.*,
          ic.*
        FROM incentivo_colaborador ic 
        JOIN catalogo_incentivo i
        ON i.catalogo_incentivo_id = ic.catalogo_incentivo_id
        WHERE ic.catalogo_colaboradores_id = $id
sql;
      return $mysqli->queryAll($query);
    }

    // BUSCAR TODOS LOS INCENTIVOS DEL COLABORADOR 
    public static function getIncentivosColabordor($idColaborador, $idPeriodo){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT * FROM incentivos_asignados WHERE colaborador_id = "$idColaborador" AND prorrateo_periodo_id = "$idPeriodo"
sql;
      return $mysqli->queryAll($query,$params);
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

    public static function getPeriodoId($id){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT * FROM prorrateo_periodo 
WHERE prorrateo_periodo_id = "$id"
sql;
        return $mysqli->queryOne($query);
    }


    /*
      Busqueda de horas extra del colaborador
      @idColaborador
      @idPeriodo
    */
    public static function getHorasExtraColaboradorPeriodo($catalogo_colaboradores_id, $prorrateo_periodo_id){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT horas_extra FROM prorrateo_horas_extra WHERE catalogo_colaboradores_id = "$catalogo_colaboradores_id" AND prorrateo_periodo_id = "$prorrateo_periodo_id"
sql;
        return $mysqli->queryAll($query);
    }

    /*
      Busqueda de incentivos asignados del colaborador del periodo Actual de busqueda SEMANAL o QUINCENAL
    */
    public static function getIncentivosColabordorPeriodo($colaborador_id, $prorrateo_periodo_id){
        $mysqli = Database::getInstance();
//SELECT SUM(cantidad) AS suma_incentivos FROM incentivos_asignados WHERE colaborador_id = "$colaborador_id" AND prorrateo_periodo_id = "$prorrateo_periodo_id" 
// inceitivos_asignados_id = 4 : ESTE ES EL INCENTIVO DE NOCHE
//AND incentivos_asignados_id != 4 
        $query=<<<sql
SELECT SUM(cantidad) AS suma_incentivos FROM incentivos_asignados WHERE colaborador_id = "$colaborador_id" AND prorrateo_periodo_id = "$prorrateo_periodo_id" 
sql;
        return $mysqli->queryOne($query);
    }

    /*
      Busqueda de incentivos asignados del colaborador del periodo Actual de busqueda SEMANAL o QUINCENAL
    */
    public static function getIncentivoNoche($colaborador_id, $prorrateo_periodo_id){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT SUM(cantidad) AS suma_incentivos_noche FROM incentivos_asignados WHERE colaborador_id = "$colaborador_id" AND prorrateo_periodo_id = "$prorrateo_periodo_id" AND incentivos_asignados_id = 4 
sql;
        return $mysqli->queryOne($query);
    }

    /*
      Obtiene el periodo 
      @tipoPeriodo: SEMANAL O QUINCENAL
    */
    public static function getTipoPeriodo($tipoPeriodo){
      $mysqli = Database::getInstance();
      $query=<<<sql
SELECT * FROM prorrateo_periodo WHERE tipo = "$tipoPeriodo" AND status = 1 
ORDER BY prorrateo_periodo.fecha_inicio  DESC
sql;
      return $mysqli->queryAll($query);
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
      Obtiene el periodo ultimo periodo ya sea semanal o quincenal
      @idPeriodo: id del periodo
    */
    public static function getPrimaDominical($idColaborador, $idPeriodo){
      $mysqli = Database::getInstance();
      $query=<<<sql
SELECT domigo_procesos  FROM prorrateo_domigo_procesos WHERE catalogo_colaboradores_id = "$idColaborador" AND prorrateo_periodo_id = "$idPeriodo"
sql;
      return $mysqli->queryOne($query);
    }

    /*
      Obtiene el periodo ultimo periodo ya sea semanal o quincenal
      @idPeriodo: id del periodo
    */
    public static function getDomingoLaborado($idColaborador, $idPeriodo){
      $mysqli = Database::getInstance();
      $query=<<<sql
SELECT domingo_laborado FROM prorrateo_domigo_laborado WHERE catalogo_colaboradores_id = "$idColaborador" AND prorrateo_periodo_id = "$idPeriodo"
sql;
      return $mysqli->queryOne($query);
    }

    public static function getIdColaborador($idColaborador, $identificador){
      $mysqli = Database::getInstance();
      $query=<<<sql
SELECT clave_noi FROM catalogo_colaboradores WHERE clave_noi = "$idColaborador" AND identificador_noi = "$identificador" 
sql;
      return $mysqli->queryOne($query);
    }


    public static function insertProrrateoPeriodoResumen($datos){
      $mysqli = Database::getInstance();
      $query=<<<sql
INSERT INTO prorrateo_periodo_resumenes 
  (catalogo_colaboradores_id, clave_noi, prorrateo_periodo_id, premio_asistencia, premio_puntualidad, horas_extra, despensa_efectivo, incentivo, prima_dominical, domingo_trabajo, total_percepciones, identificador) 
  VALUES
  (:catalogo_colaboradores_id, :clave_noi, :prorrateo_periodo_id, :premio_asistencia, :premio_puntualidad, :horas_extra, :despensa_efectivo, :incentivo, :prima_dominical, :domingo_trabajo, :total_percepciones, :identificador) 
sql;
      $params = array(
        ":catalogo_colaboradores_id"=>$datos->_catalogo_colaboradores_id,
        ":clave_noi"=>$datos->_clave_noi,
        ":prorrateo_periodo_id"=>$datos->_prorrateo_periodo_id,
        ":premio_asistencia"=>$datos->_premio_asistencia,
        ":premio_puntualidad"=>$datos->_premio_puntualidad,
        ":horas_extra"=>$datos->_horas_extra,
        ":despensa_efectivo"=>$datos->_despensa_efectivo,
        ":incentivo"=>$datos->_incentivo,
        ":prima_dominical"=>$datos->_prima_dominical,
        ":domingo_trabajo"=>$datos->_domingo_trabajo,
        ":total_percepciones"=>$datos->_total_percepciones,
        ":identificador"=>$datos->_identificador
      );

      /*if($datos->_catalogo_colaboradores_id == 43){
        echo "<pre>";
          print_r($params);
        echo "</pre>";
      }*/

      
      return $mysqli->insert($query,$params);
    }

    public static function updatePeriodoProrrateo($id){
      $mysqli = Database::getInstance();
      $query=<<<sql
UPDATE prorrateo_periodo SET status = '1' WHERE prorrateo_periodo.prorrateo_periodo_id = "$id"
sql;
      return $mysqli->update($query);
    }

    public static function getRegistro($prorrateo_periodo_id, $idenficador){
      $mysqli = Database::getInstance();
      $query=<<<sql
SELECT count(*) AS contador  FROM prorrateo_periodo_resumenes WHERE prorrateo_periodo_id = "$prorrateo_periodo_id" AND identificador LIKE '% $idenficador %'
sql;
      return $mysqli->queryOne($query);
    }

    public static function getSiEsDiaFestivo($fecha){
      $mysqli = Database::getInstance();
      $query=<<<sql
SELECT * FROM catalogo_dia_festivo WHERE fecha = "$fecha" AND status = 1 
sql;
      return $mysqli->queryOne($query);
    }

    public static function verificarSiLaASistenciasFestivaExiste($numero_empleado, $fecha, $identificador){
      $mysqli = Database::getInstance();
      $query=<<<sql
SELECT count(*) AS cantidad FROM operacion_checador WHERE numero_empleado = "$numero_empleado" AND date_check >= "$fecha 00:00:00" AND date_check <= "$fecha 23:59:59" AND identificador = "$identificador"
sql;
      return $mysqli->queryOne($query);
    }

    public static function verificaAsistenciaFestiva($prorrateo_periodo_id, $catalogo_colaboradores_id, $fecha){
      $mysqli = Database::getInstance();
      $query = <<<sql
SELECT count(*) AS asistencia FROM prorrateo_periodo_colaboradores WHERE prorrateo_periodo_id = $prorrateo_periodo_id AND catalogo_colaboradores_id = $catalogo_colaboradores_id AND fecha = "$fecha" AND estatus = 0 
sql;
      return $mysqli->queryOne($query);
    }

}
