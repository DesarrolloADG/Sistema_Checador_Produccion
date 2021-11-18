<?php
namespace App\models;
defined("APPPATH") OR die("Access denied");

use \Core\Database;
use \App\interfaces\Crud;
use \App\controllers\UtileriasLog;

class ResumenSemanal implements Crud{
  public static function getAll(){
    $mysqli = Database::getInstance();
    $query=<<<sql
      SELECT
        c.catalogo_colaboradores_id,
        c.numero_empleado,
        c.nombre,
        c.apellido_paterno,
        c.apellido_materno,
        d.nombre AS nombre_departamento
      FROM catalogo_colaboradores AS c
      INNER JOIN catalogo_departamento AS d
      ON (c.catalogo_departamento_id = d.catalogo_departamento_id)
      WHERE c.pago = 'Semanal'
sql;
        return $mysqli->queryAll($query);
    }

    public static function getAllColaboradoresPago($pago,$id_administrador,$usuario){        
        //$distinct = ($id_administrador != '')? '' : ' DISTINCT ';
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT 
            DISTINCT 
            c.catalogo_colaboradores_id, 
            c.numero_identificador AS numero_empleado, 
            c.nombre, 
            c.apellido_paterno, 
            c.apellido_materno, 
            c.horario_tipo, 
            c.identificador_noi, 
            d.nombre AS nombre_departamento,
            l.nombre AS nombre_planta, 
            l.identificador,
            c.pago, c.privilegiado
        FROM catalogo_colaboradores AS c 
        INNER JOIN catalogo_departamento AS d ON (c.catalogo_departamento_id = d.catalogo_departamento_id) 
        JOIN catalogo_lector l ON (l.catalogo_lector_id = c.catalogo_lector_id)
        WHERE c.pago = '$pago'
sql;
        return $mysqli->queryAll($query);
    }

    public static function getAllColaboradoresPagoById($colaborador_id){
        $mysqli = Database::getInstance();
        $query=<<<sql
      SELECT
        DISTINCT
        c.catalogo_colaboradores_id,
        c.numero_identificador AS numero_empleado,
        c.nombre,
        c.apellido_paterno,
        c.apellido_materno,
        c.horario_tipo,
        d.nombre AS nombre_departamento,
        l.nombre AS nombre_planta
      FROM catalogo_colaboradores AS c
      INNER JOIN catalogo_departamento AS d
      ON (c.catalogo_departamento_id = d.catalogo_departamento_id)   
      JOIN utilerias_administradores_departamentos uad
      ON uad.catalogo_departamento_id = c.catalogo_departamento_id
      JOIN catalogo_lector l
      ON l.catalogo_lector_id = c.catalogo_lector_id
      WHERE c.catalogo_colaboradores_id = $colaborador_id
sql;
        return $mysqli->queryAll($query);
    }

    public static function getColaboradoresPago($pago,$id_administrador){
        $mysqli = Database::getInstance();
        $query=<<<sql
      SELECT
        c.catalogo_colaboradores_id,
        c.numero_empleado,
        c.nombre,
        c.apellido_paterno,
        c.apellido_materno,
        c.horario_tipo,
        d.nombre AS nombre_departamento
      FROM catalogo_colaboradores AS c
      INNER JOIN catalogo_departamento AS d
      ON (c.catalogo_departamento_id = d.catalogo_departamento_id)   
      JOIN utilerias_administradores_departamentos uad
      ON uad.catalogo_departamento_id = c.catalogo_departamento_id
      WHERE c.pago = '$pago' AND c.catalogo_colaboradores_id = '$id_administrador' GROUP BY c.catalogo_colaboradores_id
sql;
        return $mysqli->queryAll($query);
    }

    public static function getAllColaboradores($departamento){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT
          c.catalogo_colaboradores_id,
          c.numero_empleado,
          c.nombre,
          c.apellido_paterno,
          c.apellido_materno,
          d.nombre AS nombre_departamento
        FROM catalogo_colaboradores AS c
        INNER JOIN catalogo_departamento AS d
        ON (c.catalogo_departamento_id = d.catalogo_departamento_id)
        WHERE c.catalogo_departamento_id = '$departamento'
sql;
        return $mysqli->queryAll($query);
    }

    public static function getDias($colaborador){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT * FROM operacion_checador
        WHERE numero_empleado = ':numero_empleado'
        AND date_check >= date(:fecha_inicial)
        AND date_check <= date(:fecha_final)
        ORDER BY numero_empleado DESC
sql;
        $parametros = array(
            ':numero_empleado' => $colaborador->_numero_empleado,
            ':fecha_inicial' => $colaborador->_fecha_inicial,
            ':fecha_final' => $colaborador->_fecha_final
        );
        return $mysqli->queryAll($query, $parametros);
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
    public static function getById($id){

    }
    public static function insert($data){}
    public static function update($data){}
    public static function delete($id){}

    public static function getProrrateoColaboradorIncidenciaById($colaborador_id){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT p.*, i.nombre
        FROM prorrateo_colaboradores_incidencia p
        JOIN catalogo_incidencia i
        USING (catalogo_incidencia_id)
        WHERE catalogo_colaboradores_id = $colaborador_id
        ORDER BY prorrateo_colaboradores_incidencia_id DESC
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

      public static function getAsistenciaOne($datos){
        $mysqli = Database::getInstance();
        $query =<<<sql
        SELECT
          oc.*
        FROM operacion_checador oc
        WHERE oc.date_check >= '$datos->fecha_inicio'
        AND oc.date_check <= '$datos->fecha_fin'
        AND numero_empleado = '$datos->numero_empleado'
sql;
        return $mysqli->queryOne($query);
      }

      public static function getAsistencia($datos, $nombre_planta){
        $mysqli = Database::getInstance();
        $query =<<<sql
        SELECT
          oc.*
        FROM operacion_checador oc
        WHERE oc.date_check >= '$datos->fecha_inicio'
        AND oc.date_check <= '$datos->fecha_fin'
        AND numero_empleado = '$datos->numero_empleado'
        AND identificador LIKE '%$nombre_planta%'
sql;
        return $mysqli->queryAll($query);
      }

      public static function getPeriodos($tipo){
        $mysqli = Database::getInstance();
        $query =<<<sql
        SELECT * FROM prorrateo_periodo WHERE tipo = '$tipo' ORDER BY prorrateo_periodo_id DESC
sql;
        return $mysqli->queryAll($query);
      }

      //////
      public static function getPeriodoProceso(){
        $mysqli = Database::getInstance();
        $query =<<<sql
        SELECT * FROM prorrateo_periodo WHERE tipo = 'SEMANAL' ORDER BY prorrateo_periodo_id DESC
sql;
        return $mysqli->queryOne($query);
      }

      public static function getPeriodoById($id){
        $mysqli = Database::getInstance();
        $query =<<<sql
        SELECT * FROM prorrateo_periodo
        WHERE prorrateo_periodo_id = $id
sql;
        return $mysqli->queryOne($query);
      }

      public static function insertProrrateoColaboradorHorario($colaborador){
        $mysqli = Database::getInstance();
        $query =<<<sql
        INSERT INTO prorrateo_colaborador_horario
        VALUES (
          NULL,
          $colaborador->_catalogo_colaboradores_id,
          $colaborador->_catalogo_horario_id,
          '$colaborador->_fecha')
sql;
        

        $id = $mysqli->insert($query);
        $accion = new \stdClass();
        $accion->_sql= $query;
        $accion->_parametros = $parametros;
        $accion->_id = $id;

        UtileriasLog::addAccion($accion);
        return $id;
      }

      public static function getHorarioById($catalogo_colaboradores_id){
        $mysqli = Database::getInstance();
        $query =<<<sql
        SELECT
          h.*,
          ch.catalogo_colaboradores_id,
          ch.horario_default
        FROM catalogo_horario h
        JOIN colaboradores_horario ch
        USING (catalogo_horario_id)
        WHERE ch.catalogo_colaboradores_id = $catalogo_colaboradores_id
        ORDER BY ch.horario_default DESC
sql;
        return $mysqli->queryAll($query);
      }

      public static function getIncidencia($datos){
      	$mysqli = Database::getInstance();
      	$query =<<<sql
      	SELECT 
      		ci.* 
      	FROM catalogo_incidencia ci
      	JOIN prorrateo_colaboradores_incidencia pci
      	ON pci.catalogo_incidencia_id = ci.catalogo_incidencia_id
      	WHERE pci.catalogo_colaboradores_id = $datos->_catalogo_colaboradores_id
      	AND pci.fecha_incidencia = '$datos->_fecha'
sql;
	       return $mysqli->queryAll($query);
      }

      public static function insertPeriodoAsistencia($datos){
        $mysqli =Database::getInstance();
        $query =<<<sql
        INSERT INTO prorrateo_periodo_colaboradores 
        VALUES(NULL, $datos->_prorrateo_periodo_id, $datos->_catalogo_colaboradores_id, '$datos->_fecha',  $datos->_estatus)
sql;
        echo 'Query: '.$query.'<br>';
        //mail('jorge.manon@airmovil.com', 'Query', $query);
        $id = $mysqli->insert($query);
        $accion = new \stdClass();
        $accion->_sql= $query;
        $accion->_parametros = $parametros;
        $accion->_id = $id;

        UtileriasLog::addAccion($accion);
        return $id;
      }
      

      public static function updatePeriodo($prorrateo_periodo_id, $tipo_periodo){
        $status = ($tipo_periodo == 'quincenal')? '1': '2';
      	$mysqli = Database::getInstance();
      	$query =<<<sql
      	update prorrateo_periodo SET status = $status WHERE prorrateo_periodo_id = $prorrateo_periodo_id
sql;
        $id = $mysqli->update($query);
        $accion = new \stdClass();
        $accion->_sql= $query;
        $accion->_parametros = $parametros;
        $accion->_id = $id;

        UtileriasLog::addAccion($accion);
        return $id;
      }

      public static function getLastHorario($catalogo_colaboradores_id){
        $mysqli = Database::getInstance();
        $query =<<<sql
        SELECT 
          *
        FROM prorrateo_colaborador_horario 
        WHERE catalogo_colaboradores_id = '$catalogo_colaboradores_id'
        ORDER BY prorrateo_colaborador_horario_id DESC
sql;
        return $mysqli->queryOne($query);
      }

      public static function getListHorario($catalogo_colaboradores_id){
        $mysqli = Database::getInstance();
        $query =<<<sql
        SELECT 
          *
        FROM colaboradores_horario 
        WHERE catalogo_colaboradores_id = '$catalogo_colaboradores_id'
sql;
        return $mysqli->queryAll($query);
      }

      public static function getPeriodoBackup($periodo_id){
        $mysqli = Database::getInstance();
        $query =<<<sql
        SELECT 
          * 
        FROM prorrateo_periodo_colaboradores_backup 
        WHERE prorrateo_periodo_id = '$periodo_id'
sql;
         return $mysqli->queryAll($query);
      }

      

      public static function getPeriodo($periodo_id){
      	$mysqli = Database::getInstance();
      	$query =<<<sql
      	SELECT * FROM prorrateo_periodo WHERE prorrateo_periodo_id = '$periodo_id'
sql;
	       return $mysqli->queryOne($query);
      }

      public static function getMaxPeriodo(){
        $mysqli = Database::getInstance();
        $query =<<<sql
        SELECT MAX(prorrateo_periodo_id) AS id from prorrateo_periodo where tipo = 'SEMANAL'
sql;
        return $mysqli->queryOne($query);
      }

      public static function cancelarPeriodo($id){
        $mysqli = Database::getInstance();
        //$id = ResumenSemanal::getMaxPeriodo()['id'];
        $query =<<<sql
        UPDATE prorrateo_periodo SET status = 0 WHERE prorrateo_periodo_id = '$id';
        DELETE FROM prorrateo_periodo_colaboradores WHERE prorrateo_periodo_id = '$id';
        DELETE FROM prorrateo_periodo_resumenes WHERE prorrateo_periodo_id = '$id'
sql;
        $id = $mysqli->update($query);
        $accion = new \stdClass();
        $accion->_sql= $query;
        $accion->_parametros = $parametros;
        $accion->_id = $id;
        UtileriasLog::addAccion($accion);
        return $id;
      }

      public static function getPeriodoColaboradoresById($periodo_id){
        $mysqli = Database::getInstance();
        $query =<<<sql
        SELECT 
          * 
        FROM prorrateo_periodo_colaboradores
        WHERE prorrateo_periodo_id = '$periodo_id'
sql;
         return $mysqli->queryAll($query);
      }

      public static function insertPeriodoBackup($periodo){
        $mysqli = Database::getInstance();
        $query =<<<sql
        INSERT INTO prorrateo_periodo_colaboradores_backup 
        VALUES(
          $periodo->prorrateo_periodo_colaboradores_id,
          $periodo->prorrateo_periodo_id,
          $periodo->catalogo_colaboradores_id,
          '$periodo->fecha',
          $periodo->estatus
        )
sql;
        $id = $mysqli->insert($query);
        $accion = new \stdClass();
        $accion->_sql= $query;
        $accion->_parametros = $parametros;
        $accion->_id = $id;

        UtileriasLog::addAccion($accion);
        return $id;
      }

      public static function deletePeriodoBackup($periodo_id){
        $mysqli = Database::getInstance();
        $query =<<<sql
        DELETE FROM prorrateo_periodo_colaboradores_backup WHERE prorrateo_periodo_id = '$periodo_id'
sql;
         $id = $mysqli->update($query);
        $accion = new \stdClass();
        $accion->_sql= $query;
        $accion->_parametros = $parametros;
        $accion->_id = $id;

        UtileriasLog::addAccion($accion);
        return $id;
      }

      public static function restaurarPeriodo($periodo){
        $mysqli = Database::getInstance();
        $query =<<<sql
        INSERT INTO prorrateo_periodo_colaboradores 
        VALUES(
          $periodo->prorrateo_periodo_colaboradores_id,
          $periodo->prorrateo_periodo_id,
          $periodo->catalogo_colaboradores_id,
          '$periodo->fecha',
          $periodo->estatus
        )
sql;
         $id = $mysqli->insert($query);
        $accion = new \stdClass();
        $accion->_sql= $query;
        $accion->_parametros = $parametros;
        $accion->_id = $id;

        UtileriasLog::addAccion($accion);
        return $id;
      }

      public static function getColaboradoresPeriodo($periodo_id){
        $mysqli = Database::getInstance();
        $query =<<<sql
        SELECT distinct
          c.catalogo_colaboradores_id,
          c.numero_empleado,
          c.nombre,
          c.apellido_paterno,
          c.apellido_materno,
          d.nombre AS departamento
        FROM catalogo_colaboradores c
        JOIN catalogo_departamento d
        ON c.catalogo_departamento_id = d.catalogo_departamento_id
        JOIN prorrateo_periodo_colaboradores p
        ON p.catalogo_colaboradores_id = c.catalogo_colaboradores_id
        WHERE p.prorrateo_periodo_id = '$periodo_id'
sql;
        return $mysqli->queryAll($query);
      }

      public static function getFechaPeriodo($datos){
        $mysqli = Database::getInstance();
        $query =<<<sql
        SELECT 
          p.catalogo_colaboradores_id,
          p.fecha,
          p.estatus
        FROM prorrateo_periodo_colaboradores p
        WHERE p.prorrateo_periodo_id = $datos->_prorrateo_periodo_id
        AND p.catalogo_colaboradores_id = $datos->_catalogo_colaboradores_id
        AND p.fecha = '$datos->_fecha'
sql;
        return $mysqli->queryOne($query);
      }

      public static function getIncidenciaById($catalogo_incidencia_id){
        $mysqli = Database::getInstance();
        $query =<<<sql
        SELECT * FROM catalogo_incidencia WHERE catalogo_incidencia_id = $catalogo_incidencia_id
sql;
        return $mysqli->queryOne($query);
      }

      public static function getDiaFestivo($fecha){
        $mysqli = Database::getInstance();
        $query =<<<sql
        SELECT 
          *
        FROM  catalogo_dia_festivo 
        WHERE fecha = '$fecha' AND status = 1
sql;
        return $mysqli->queryAll($query);
      }
}
