<?php
namespace App\models;
defined("APPPATH") OR die("Access denied");

use \Core\Database;
use \App\interfaces\Crud;
use \App\controllers\UtileriasLog;

class Resumenes implements Crud{

    public static function getAll(){}

    public static function getById($id){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT * FROM catalogo_colaboradores WHERE catalogo_colaboradores_id = "$id"
sql;
        return $mysqli->queryOne($query);
    }

    public static function getHorariosColaborador($id){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT * FROM colaboradores_horario 
INNER JOIN catalogo_horario USING (catalogo_horario_id) 
WHERE catalogo_colaboradores_id = $id ORDER BY catalogo_colaboradores_id ASC 
sql;
        return $mysqli->queryAll($query);
    }

    public static function getDiasLaboralesColaborador($colaboradorId, $catalogoHorario){
        $mysqli = Database::getInstance();
        $query = <<<sql
SELECT dl.nombre FROM horario_dias_laborales cdl 
INNER JOIN colaboradores_horario ch ON (ch.catalogo_horario_id = cdl.catalogo_horario_id) 
INNER JOIN dias_laborales dl ON (dl.dias_laborales_id = cdl.dias_laborales_id) 
WHERE cdl.catalogo_horario_id = "$catalogoHorario" AND ch.catalogo_colaboradores_id = "$colaboradorId"
sql;
        return $mysqli->queryAll($query);
    }



    public static function insert($data){}

    public static function update($data){}

    public static function delete($id){}

    public static function getFaltasByPeriodoColaborador($colaborador_id, $periodo){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT count(*) AS faltas
        FROM prorrateo_periodo_colaboradores
        WHERE fecha >= '$periodo->fecha_inicio'
        AND fecha <= '$periodo->fecha_fin'
        AND estatus in (-1, -22,-26)
        AND catalogo_colaboradores_id = $colaborador_id

sql;
        return $mysqli->queryOne($query);
    }

    /*
        Busqueda de incentivo 
        @params
        @tipo: SEMANAL O QUINCENAL
        @statis: 1 es Cerrado y 0 es Cerrado
    */
    public static function searchPeriodos($tipo, $status){
        $where = ($status == 0)? " AND status = $status" : " AND status != 0";
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT * FROM prorrateo_periodo 
WHERE tipo = '$tipo' $where
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
        Obtiene el periodo ultimo periodo ya sea semanal o quincenal
        @idPeriodo: id del periodo
    */
    public static function getUltimoPeriodoHistorico($tipo){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT * FROM prorrateo_periodo 
WHERE tipo = "$tipo" AND status != 0
ORDER BY prorrateo_periodo.prorrateo_periodo_id DESC  
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
SELECT * FROM prorrateo_periodo  
WHERE tipo = "$tipoPeriodo" AND status = 1
ORDER BY prorrateo_periodo.fecha_inicio  DESC
sql;
        return $mysqli->queryAll($query);
    }


    public static function getPeriodoById($id){
        $mysqli = Database::getInstance();
        $query =<<<sql
        SELECT * FROM prorrateo_periodo
        WHERE prorrateo_periodo_id = $id
sql;
        return $mysqli->queryOne($query);
	}


    public static function getAllColaboradoresPago($pago, $id_administrador, $usuario, $departamento_id, $planta_id){
        $whereDep = ($departamento_id != '')? " AND c.catalogo_departamento_id = $departamento_id ": '' ;
        $distinct = ($id_administrador != '')? '' : ' DISTINCT ';

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
	    c.catalogo_lector_secundario_id
        FROM catalogo_colaboradores AS c
        INNER JOIN catalogo_departamento AS d 
        ON (c.catalogo_departamento_id = d.catalogo_departamento_id)   
        JOIN utilerias_administradores_departamentos uad 
        ON uad.catalogo_departamento_id = c.catalogo_departamento_id
        JOIN catalogo_lector l 
        ON l.catalogo_lector_id = c.catalogo_lector_id
        WHERE c.pago = '$pago' $whereDep
sql;

        //echo $query;
        return $mysqli->queryAll($query);
    }

    public static function getAllColaboradores($perfil_id, $tipoPeriodo, $catalogo_planta_id, $catalogo_departamento_id, $catalodo_planta_nombre, $accion, $where){
        $mysqli = Database::getInstance();

        //echo "<pre>";print_r($perfil_id);echo "<br>";print_r($accion);echo "</pre>";
        $query=<<<sql
        SELECT 
            DISTINCT 
            c.catalogo_colaboradores_id, 
            c.numero_identificador AS numero_empleado, 
            c.nombre, 
            c.apellido_paterno, 
            c.apellido_materno, 
            c.clave_noi,
            c.horario_tipo, 
            c.identificador_noi, 
            d.nombre AS nombre_departamento,
            l.nombre AS nombre_planta, 
            l.identificador,
            c.privilegiado,
            c.pago,
	    c.catalogo_lector_secundario_id 
        FROM catalogo_colaboradores AS c 
        INNER JOIN catalogo_departamento AS d ON (c.catalogo_departamento_id = d.catalogo_departamento_id) 
        JOIN catalogo_lector l ON (l.catalogo_lector_id = c.catalogo_lector_id) 
sql;
        //JOIN utilerias_administradores_departamentos uad ON (uad.catalogo_departamento_id = c.catalogo_departamento_id)
        if($perfil_id == 1 || $perfil_id == 2){
            
            if($accion == 1){
                $query .=<<<sql
            WHERE c.fecha_baja != '0000-00-00' AND c.pago = "$tipoPeriodo" AND c.status = 1 AND c.catalogo_departamento_id = "$catalogo_departamento_id" AND c.catalogo_ubicacion_id = "$catalogo_planta_id" AND l.identificador = "$catalodo_planta_nombre"
sql;
            }

            if($accion == 2){
                $query .=<<<sql
            WHERE c.pago = "$tipoPeriodo" AND c.status = 1 
sql;
            }

            if($accion == 3){
                $query .=<<<sql
            WHERE c.pago = "$tipoPeriodo" AND c.status = 1 AND c.catalogo_departamento_id = "$catalogo_departamento_id" 
sql;
            }
        }//fin perfil_id = 1


        if($perfil_id == 6){
            //WHERE c.pago = "$tipoPeriodo" AND c.status = 1 AND c.catalogo_departamento_id = "$catalogo_departamento_id" AND c.catalogo_ubicacion_id = "$catalogo_planta_id" AND c.identificador_noi = "$catalodo_planta_nombre"
            if($accion == 1){
            $query .=<<<sql
            WHERE c.pago = "$tipoPeriodo" AND c.status = 1 AND c.catalogo_departamento_id = "$catalogo_departamento_id" AND l.nombre = "$catalodo_planta_nombre"
sql;
            }

            if($accion == 2){
            $query .=<<<sql
            WHERE c.pago = "$tipoPeriodo" AND c.status = 1 
sql;
            }

            if($accion == 3){
            $query .=<<<sql
            WHERE c.pago = "$tipoPeriodo" AND c.status = 1 AND c.catalogo_ubicacion_id = "$catalogo_planta_id"
sql;
            }

            if($accion == 4){
            $query .=<<<sql
            WHERE c.pago = "$tipoPeriodo" AND c.status = 1 AND c.catalogo_lector_id = "$catalogo_planta_id" 
sql;
            }
            
            
        }

        if($perfil_id == 5 || $perfil_id == 4){
// WHERE c.pago = "$tipoPeriodo" AND c.status = 1 AND c.catalogo_departamento_id = "$catalogo_departamento_id" AND c.catalogo_ubicacion_id = "$catalogo_planta_id" AND c.identificador_noi = "$catalodo_planta_nombre"            
            $query .=<<<sql
            JOIN utilerias_administradores_departamentos uad ON (uad.catalogo_departamento_id = c.catalogo_departamento_id)
            WHERE c.pago = "$tipoPeriodo" AND c.status = 1 AND c.catalogo_departamento_id = "$catalogo_departamento_id" AND c.catalogo_ubicacion_id = "$catalogo_planta_id" AND l.nombre = "$catalodo_planta_nombre"
sql;
        }
        $query .= ' '.$where;
            $queryTest .= <<<sql
  GROUP BY c.apellido_paterno ASC 
sql;

//AND c.catalogo_colaboradores_id = 22
//JUAN CAMBIO
        
        //echo $query.'<br><br>
            $query .=<<<sql
		 
sql;
        return $mysqli->queryAll($query);
    }

        public static function getAllColaboradorById($id){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT DISTINCT c.catalogo_colaboradores_id, c.numero_identificador AS numero_empleado, c.nombre, c.apellido_paterno, c.apellido_materno, c.horario_tipo, c.identificador_noi, d.nombre AS nombre_departamento, l.nombre AS nombre_planta, l.identificador, c.privilegiado, c.pago, c.catalogo_lector_secundario_id FROM catalogo_colaboradores AS c INNER JOIN catalogo_departamento AS d ON (c.catalogo_departamento_id = d.catalogo_departamento_id) JOIN catalogo_lector l ON (l.catalogo_lector_id = c.catalogo_lector_id) WHERE c.catalogo_colaboradores_id = $id 
sql;

        return $mysqli->queryAll($query);
    }

    public static function getIncidencia($datos){
		$mysqli = Database::getInstance();
		$query =<<<sql
SELECT ci.* 
FROM catalogo_incidencia ci
JOIN prorrateo_colaboradores_incidencia pci	ON pci.catalogo_incidencia_id = ci.catalogo_incidencia_id
WHERE pci.catalogo_colaboradores_id = $datos->_catalogo_colaboradores_id AND pci.fecha_incidencia = '$datos->_fecha'
sql;
	       return $mysqli->queryAll($query);
    }

    public static function getHorarioLaboral($catalogo_colaboradores_id){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT ch.hora_entrada, ch.hora_salida, ch.tolerancia_entrada, ch.numero_retardos, dl.nombre AS dia_semana, ch.nombre horario, ch.catalogo_horario_id
FROM catalogo_horario ch
JOIN horario_dias_laborales hdl ON hdl.catalogo_horario_id = ch.catalogo_horario_id
JOIN dias_laborales dl ON dl.dias_laborales_id = hdl.dias_laborales_id
JOIN colaboradores_horario clh ON clh.catalogo_horario_id = ch.catalogo_horario_id
WHERE clh.catalogo_colaboradores_id = $catalogo_colaboradores_id
ORDER BY dl.dias_laborales_id
sql;
        return $mysqli->queryAll($query);
    }

    public static function getAsistencia($datos, $nombre_planta){
        $mysqli = Database::getInstance();
        $query =<<<sql
SELECT oc.*
FROM operacion_checador oc
WHERE oc.date_check >= '$datos->fecha_inicio' AND oc.date_check <= '$datos->fecha_fin' AND numero_empleado = '$datos->numero_empleado' AND identificador = '{$nombre_planta}'
sql;
        /***********
            //echo '<br>';
            echo $query;
            echo '<br>';
        /***********/
        return $mysqli->queryAll($query);
    }

    public static function getLastHorario($catalogo_colaboradores_id){
        $mysqli = Database::getInstance();
        $query =<<<sql
SELECT *
FROM prorrateo_colaborador_horario 
WHERE catalogo_colaboradores_id = '$catalogo_colaboradores_id'
ORDER BY prorrateo_colaborador_horario_id DESC
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

    public static function getRegistro($datos){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT 
            *  
        FROM prorrateo_periodo_colaboradores 
        WHERE prorrateo_periodo_id = $datos->periodo_id
        AND catalogo_colaboradores_id = $datos->catalogo_colaboradores_id
sql;
        return $mysqli->queryAll($query);
    }

        public static function getIncentivosValor($colaborador_id, $prorrateo_periodo_id){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT SUM(cantidad) AS total_incentivo FROM incentivos_asignados WHERE colaborador_id = "$colaborador_id" AND prorrateo_periodo_id = "$prorrateo_periodo_id"
sql;
        return $mysqli->queryOne($query);
    }


    public static function getIncidenciaById($id){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT * FROM catalogo_incidencia WHERE catalogo_incidencia_id = $id
sql;
        return $mysqli->queryOne($query);
    }

    public static function getValoresNoHorasExtra($data){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT horas_extra FROM prorrateo_horas_extra WHERE catalogo_colaboradores_id = :catalogo_colaboradores_id AND prorrateo_periodo_id = :prorrateo_periodo_id 
sql;
        return $mysqli->queryOne($query, array(":catalogo_colaboradores_id"=>$data->_catalogo_colaboradores_id, ":prorrateo_periodo_id"=>$data->_prorrateo_periodo_id));
    }

    public static function getDomingoProcesosLaborado($data){
        $mysqli = Database::getInstance();
        $query1=<<<sql
        SELECT * FROM prorrateo_domigo_procesos WHERE catalogo_colaboradores_id = $data->_catalogo_colaboradores_id AND prorrateo_periodo_id = $data->_prorrateo_periodo_id
sql;
        $query2=<<<sql
        SELECT * FROM prorrateo_domigo_laborado WHERE catalogo_colaboradores_id = $data->_catalogo_colaboradores_id AND prorrateo_periodo_id = $data->_prorrateo_periodo_id 
sql;
        $result1 = $mysqli->queryOne($query1);
        $result2 = $mysqli->queryOne($query2);

        return array(
                    "procesos"=>(!empty($result1['domigo_procesos'])) ? $result1['domigo_procesos'] : 0,
                    "laborado"=>(!empty($result2['domingo_laborado'])) ? $result2['domingo_laborado'] : 0
                );
    }

    public static function getIncentivosBotes($data){
        $mysqli = Database::getInstance();
        $query = <<<sql
SELECT * FROM incentivos_asignados 
WHERE colaborador_id = :colaborador_id AND prorrateo_periodo_id = :prorrateo_periodo_id AND catalogo_incentivo_id = 47 
sql;
        $params = array(
            ':colaborador_id'=>$data->_colaborador_id,
            ':prorrateo_periodo_id'=>$data->_prorrateo_periodo_id
        );
        return $mysqli->queryOne($query, $params);
    }
    
}
