<?php
namespace App\models;
defined("APPPATH") OR die("Access denied");

use \Core\Database;
use \App\interfaces\Crud;

class Incidencia implements Crud{

    public static function getAll(){}

    public static function insert($data){}

    public static function update($data){}

    public static function delete($id){}

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

    /*
        Busqueda de incentivo 
        @params
        @tipo: SEMANAL O QUINCENAL
        @statis: 1 es Cerrado y 0 es Cerrado
    */
    public static function searchPeriodoProcesado($tipo, $idPeriodo){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT * FROM prorrateo_periodo 
WHERE tipo = "$tipo" AND prorrateo_periodo_id = "$idPeriodo"
sql;
        return $mysqli->queryAll($query);
    }

    /*
        Busqueda de incentivo 
        @params
        @tipo: SEMANAL O QUINCENAL
        @statis: 1 es Cerrado y 0 es Cerrado
    */
    public static function searchPeriodoById($idPeriodo){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT * FROM prorrateo_periodo 
WHERE prorrateo_periodo_id = "$idPeriodo"
sql;
        return $mysqli->queryOne($query);
    }

    /*
        Buscar los colaboradores 
        @tipo: SEMANAL o QUINCENAL
    */
    public static function getColaboradores($tipo, $perfilUsuario, $catalogoDepartamentoId, $catalogoPlantaId, $estatusRH){
        $mysqli = Database::getInstance();

        // PERIL PARA EL USUARIO ROOT
        if($perfilUsuario == 1){
            $query=<<<sql
SELECT 
cc.catalogo_colaboradores_id, cc.identificador_noi, cc.nombre, cc.apellido_paterno, cc.apellido_materno, cc.numero_identificador, cc.catalogo_departamento_id,
cc.pago, cc.foto, cd.nombre AS nombre_departamento, cp.nombre AS nombre_puesto, cu.nombre nombre_ubicacion, cc.catalogo_ubicacion_id, ce.nombre AS nombre_empresa, cc.numero_empleado
FROM catalogo_colaboradores cc 
INNER JOIN catalogo_departamento cd USING (catalogo_departamento_id)
INNER JOIN catalogo_puesto cp USING (catalogo_puesto_id)
INNER JOIN catalogo_ubicacion cu USING (catalogo_ubicacion_id)
INNER JOIN catalogo_empresa ce USING (catalogo_empresa_id)
WHERE cc.pago = "$tipo" AND cc.status = 1
sql;
        }

        // PERFIL PARA 4 "Administrador" y 5 "Personalizado"
        if($perfilUsuario == 4 || $perfilUsuario == 5){
            $query =<<<sql
SELECT 
cc.catalogo_colaboradores_id, cc.identificador_noi, cc.nombre, cc.apellido_paterno, cc.apellido_materno, cc.numero_identificador, cc.catalogo_departamento_id,
cc.pago, cc.foto, cd.nombre AS nombre_departamento, cp.nombre AS nombre_puesto, cu.nombre nombre_ubicacion, cc.catalogo_ubicacion_id, ce.nombre AS nombre_empresa, cc.numero_empleado
FROM catalogo_colaboradores cc 
INNER JOIN catalogo_departamento cd USING (catalogo_departamento_id)
INNER JOIN catalogo_puesto cp USING (catalogo_puesto_id)
INNER JOIN catalogo_ubicacion cu USING (catalogo_ubicacion_id)
INNER JOIN catalogo_empresa ce USING (catalogo_empresa_id)
WHERE cc.pago = "$tipo" AND cc.status = 1 AND cc.catalogo_departamento_id = "$catalogoDepartamentoId"
sql;

        }

        if($perfilUsuario == 6){
            
            $query=<<<sql
SELECT 
cc.catalogo_colaboradores_id, cc.identificador_noi, cc.nombre, cc.apellido_paterno, cc.apellido_materno, cc.numero_identificador, cc.catalogo_departamento_id,
cc.pago, cc.foto, cd.nombre AS nombre_departamento, cp.nombre AS nombre_puesto, cu.nombre nombre_ubicacion, cc.catalogo_ubicacion_id, ce.nombre AS nombre_empresa, cc.numero_empleado
FROM catalogo_colaboradores cc 
INNER JOIN catalogo_departamento cd USING (catalogo_departamento_id)
INNER JOIN catalogo_puesto cp USING (catalogo_puesto_id)
INNER JOIN catalogo_ubicacion cu USING (catalogo_ubicacion_id) 
INNER JOIN catalogo_empresa ce USING (catalogo_empresa_id)
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
        Obtiene el periodo 
        @tipoPeriodo: SEMANAL O QUINCENAL
    */
    public static function getTipoPeriodo($tipoPeriodo, $status){
        $where = ($status == 0)? ' AND status = 0 ': 'AND status != 0';
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT * FROM prorrateo_periodo  
WHERE tipo = "$tipoPeriodo" $where
ORDER BY prorrateo_periodo.fecha_inicio  DESC
sql;
        return $mysqli->queryAll($query);
    }

    /*
        Obtiene los datos de un colaborador
    */
    public static function getById($id){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT c.*, s.nombre AS status
FROM catalogo_colaboradores c
JOIN catalogo_status s ON c.status = s.catalogo_status_id
WHERE c.catalogo_colaboradores_id = $id
sql;
      return $mysqli->queryOne($query);
    }

    /*
        Obtiene los datos del pago de la persona
    */
    public static function getPeriodoFechas($periodo){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT * FROM prorrateo_periodo 
WHERE tipo = "$periodo" AND status = 1 
ORDER BY `prorrateo_periodo`.`fecha_inicio` DESC 
sql;
        return $mysqli->queryAll($query);
    }

    /*
        Obtiene las fechas de los periodo ya procesados
    */
    public static function getPeriodoFechasProceso($periodo){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT * FROM prorrateo_periodo 
WHERE tipo = "$periodo" AND status = 1 
ORDER BY `prorrateo_periodo`.`fecha_inicio` DESC 
sql;
        return $mysqli->queryAll($query);
    }

    public static function getPeriodoById($id){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT * FROM prorrateo_periodo WHERE prorrateo_periodo_id = "$id"
sql;
        return $mysqli->queryOne($query);
    }

    public static function getProrrateoColaboradorIncidenciaById($colaborador_id){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT p.*, i.nombre
FROM prorrateo_colaboradores_incidencia p
JOIN catalogo_incidencia i USING (catalogo_incidencia_id)
WHERE catalogo_colaboradores_id = $colaborador_id
ORDER BY prorrateo_colaboradores_incidencia_id DESC
sql;


        return $mysqli->queryAll($query);
    }


    public static function getHorarioLaboral($datos){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT 
            ch.catalogo_horario_id,
            ch.hora_entrada, 
            ch.hora_salida, 
            ch.tolerancia_entrada, 
            ch.numero_retardos, 
            dl.nombre AS dia_semana, 
            ch.nombre horario
        FROM catalogo_horario ch JOIN horario_dias_laborales hdl
        ON hdl.catalogo_horario_id = ch.catalogo_horario_id JOIN dias_laborales dl
        ON dl.dias_laborales_id = hdl.dias_laborales_id JOIN colaboradores_horario clh
        ON clh.catalogo_horario_id = ch.catalogo_horario_id
        WHERE clh.catalogo_colaboradores_id = $datos->catalogo_colaboradores_id
        ORDER BY dl.dias_laborales_id
sql;
        return $mysqli->queryAll($query);
    }

    public static function getHorarioLaboralById($datos){
        $mysqli = Database::getInstance();
        $where = ($datos->catalogo_horario_id != '')? " AND ch.catalogo_horario_id = $datos->catalogo_horario_id ": "";
        $query=<<<sql
        SELECT 
            ch.catalogo_horario_id,
            ch.hora_entrada, 
            ch.hora_salida, 
            ch.tolerancia_entrada, 
            ch.numero_retardos, 
            dl.nombre AS dia_semana, 
            ch.nombre horario
        FROM catalogo_horario ch JOIN horario_dias_laborales hdl
        ON hdl.catalogo_horario_id = ch.catalogo_horario_id JOIN dias_laborales dl
        ON dl.dias_laborales_id = hdl.dias_laborales_id JOIN colaboradores_horario clh
        ON clh.catalogo_horario_id = ch.catalogo_horario_id
        WHERE clh.catalogo_colaboradores_id = $datos->catalogo_colaboradores_id
        $where
        ORDER BY dl.dias_laborales_id
sql;
        return $mysqli->queryAll($query);
    }


    public static function getUltimoHorario($datos){
        $mysqli = Database::getInstance();
        $query =<<<sql
        SELECT 
            *
        FROM prorrateo_colaborador_horario
        WHERE catalogo_colaboradores_id = $datos->catalogo_colaboradores_id
        ORDER BY fecha DESC
sql;
        return $mysqli->queryOne($query);
    }

    public static function getHorariosById($datos){
        $mysqli = Database::getInstance();
        $query =<<<sql
        SELECT 
            DISTINCT
            *
        FROM colaboradores_horario
        JOIN catalogo_horario USING(catalogo_horario_id)
        WHERE catalogo_colaboradores_id = $datos->catalogo_colaboradores_id
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
        //echo $query.'<br>';
        return $mysqli->queryAll($query);
    }

    public static function getFechaIncidenciaById($datos){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT * FROM prorrateo_colaboradores_incidencia
        WHERE fecha_incidencia LIKE '%$datos->fecha%'
        AND catalogo_colaboradores_id = $datos->catalogo_colaboradores_id
sql;
        return $mysqli->queryOne($query);
    }

        public static function insertProrrateoColaboradorIncidencia($datos){
      $mysqli = Database::getInstance();
      $query=<<<sql
      INSERT INTO prorrateo_colaboradores_incidencia
      VALUES(null, :catalogo_colaboradores_id, :fecha, :catalogo_incidencia_id, :comentario)
sql;
      $params = array(
        ':catalogo_colaboradores_id' => $datos->catalogo_colaboradores_id,
        ':fecha' => $datos->fecha,
        ':catalogo_incidencia_id' => $datos->catalogo_incidencia_id,
        ':comentario' => $datos->comentario
      );

      return $mysqli->insert($query, $params);
    }

    public static function deleteFechaIncidenciaById($datos){
        $mysqli = Database::getInstance();
        $query=<<<sql
        DELETE FROM prorrateo_colaboradores_incidencia
        WHERE fecha_incidencia LIKE '%$datos->fecha%'
        AND catalogo_colaboradores_id = $datos->catalogo_colaboradores_id
sql;
        echo $mysqli->update($query);
    }

    public static function getIncidencias(){
        $mysqli = Database::getInstance();
        $query =<<<sql
SELECT * FROM catalogo_incidencia
sql;
        return $mysqli->queryAll($query);
    }

    public static function getDiaFestivo($fecha){
        $mysqli = Database::getInstance();
        $query =<<<sql
SELECT * FROM catalogo_dia_festivo WHERE fecha = '$fecha' 
sql;
        return $mysqli->queryOne($query);
    }


}
