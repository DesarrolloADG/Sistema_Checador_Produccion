<?php
namespace App\models;
defined("APPPATH") OR die("Access denied");

use \Core\Database;
use \App\interfaces\Crud;
use \App\controllers\UtileriasLog;

class Colaboradores implements Crud{

    public static function getNumero_Identificacion($clave, $id_catalogo_lector){

        $mysqli = Database::getInstance(true);
        $query =<<<sql
        SELECT * FROM catalogo_colaboradores WHERE numero_identificador = '$clave' and catalogo_lector_id = '$id_catalogo_lector';
sql;
        $dato = $mysqli->queryOne($query);
        return ($dato>=1) ;
    }

    public static function getPlantilla($id_catalogo_lector){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT (SELECT plantilla_autorizada FROM catalogo_personal_autorizado WHERE catalogo_lector_id= '$id_catalogo_lector') AS plantilla_autorizada, 
               (SELECT COUNT(*) AS Activos FROM catalogo_colaboradores AS A WHERE STATUS= 1 AND catalogo_lector_id = $id_catalogo_lector and PAGO = 'SEMANAL') AS activos, 
               (SELECT plantilla_autorizada FROM catalogo_personal_autorizado WHERE catalogo_lector_id= '$id_catalogo_lector' and PAGO = 'SEMANAL') - 
               (SELECT COUNT(*) AS Activos FROM catalogo_colaboradores AS A WHERE STATUS= 1 AND catalogo_lector_id = $id_catalogo_lector and PAGO = 'SEMANAL')  AS faltantes
sql;
        return $mysqli->queryAll($query);
    }

    public static function getXochimilco(){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT (SELECT plantilla_autorizada FROM catalogo_personal_autorizado WHERE unidad= 'Xochimilco') AS plantilla_autorizada, 
               (SELECT COUNT(*) AS Activos FROM catalogo_colaboradores AS A WHERE STATUS= 1 AND PAGO = 'SEMANAL' AND identificador_noi = 'XOCHIMILCO') AS activos, 
               (SELECT plantilla_autorizada FROM catalogo_personal_autorizado WHERE unidad= 'Xochimilco') - 
               (SELECT COUNT(*) AS Activos FROM catalogo_colaboradores AS A WHERE STATUS= 1 AND PAGO = 'SEMANAL' AND identificador_noi = 'XOCHIMILCO')  AS faltantes,
               
                   IF((SELECT plantilla_autorizada FROM catalogo_personal_autorizado WHERE unidad= 'Xochimilco') < (SELECT COUNT(*) AS Activos FROM catalogo_colaboradores AS A WHERE STATUS= 1  AND PAGO = 'SEMANAL' AND identificador_noi = 'XOCHIMILCO'), 
                      
                  IF((SELECT plantilla_autorizada FROM catalogo_personal_autorizado WHERE unidad= 'Xochimilco') > (SELECT COUNT(*) AS Activos FROM catalogo_colaboradores AS A WHERE STATUS= 1 AND  PAGO = 'SEMANAL' AND identificador_noi = 'XOCHIMILCO'), 'Incompleta','Sobrepasa'),
                  
                      IF((SELECT plantilla_autorizada FROM catalogo_personal_autorizado WHERE unidad= 'Xochimilco') = (SELECT COUNT(*) AS Activos FROM catalogo_colaboradores AS A WHERE STATUS= 1  AND PAGO = 'SEMANAL' AND identificador_noi = 'XOCHIMILCO'),'Completa','Incompleta'))AS color
sql;
        return $mysqli->queryAll($query);
    }

    public static function getVallejo(){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT (SELECT plantilla_autorizada FROM catalogo_personal_autorizado WHERE unidad= 'Vallejo') AS plantilla_autorizada, 
               (SELECT COUNT(*) AS Activos FROM catalogo_colaboradores AS A WHERE STATUS= 1 AND CATALOGO_LECTOR_ID = 2 AND PAGO = 'SEMANAL' AND identificador_noi = 'VALLEJO') AS activos, 
               (SELECT plantilla_autorizada FROM catalogo_personal_autorizado WHERE unidad= 'Vallejo') - 
               (SELECT COUNT(*) AS Activos FROM catalogo_colaboradores AS A WHERE STATUS= 1 AND CATALOGO_LECTOR_ID = 2 AND PAGO = 'SEMANAL' AND identificador_noi = 'VALLEJO')  AS faltantes,
               
                   IF((SELECT plantilla_autorizada FROM catalogo_personal_autorizado WHERE unidad= 'Vallejo') < (SELECT COUNT(*) AS Activos FROM catalogo_colaboradores AS A WHERE STATUS= 1 AND CATALOGO_LECTOR_ID = 2 AND PAGO = 'SEMANAL' AND identificador_noi = 'VALLEJO'), 
                      
                  IF((SELECT plantilla_autorizada FROM catalogo_personal_autorizado WHERE unidad= 'Vallejo') > (SELECT COUNT(*) AS Activos FROM catalogo_colaboradores AS A WHERE STATUS= 1 AND CATALOGO_LECTOR_ID = 2 AND PAGO = 'SEMANAL' AND identificador_noi = 'VALLEJO'), 'Incompleta','Sobrepasa'),
                  
                      IF((SELECT plantilla_autorizada FROM catalogo_personal_autorizado WHERE unidad= 'Vallejo') = (SELECT COUNT(*) AS Activos FROM catalogo_colaboradores AS A WHERE STATUS= 1 AND CATALOGO_LECTOR_ID = 2 AND PAGO = 'SEMANAL' AND identificador_noi = 'VALLEJO'),'Completa','Incompleta'))AS color
sql;
        return $mysqli->queryAll($query);
    }

    public static function getGATSA(){
        $mysqli = Database::getInstance();
        $query=<<<sql
           SELECT (SELECT plantilla_autorizada FROM catalogo_personal_autorizado WHERE unidad= 'GATSA') AS plantilla_autorizada, 
               (SELECT COUNT(*) AS Activos FROM catalogo_colaboradores AS A WHERE STATUS= 1 AND CATALOGO_LECTOR_ID = 3 AND PAGO = 'SEMANAL' AND identificador_noi = 'GATSA') AS activos, 
               (SELECT plantilla_autorizada FROM catalogo_personal_autorizado WHERE unidad= 'GATSA') - 
               (SELECT COUNT(*) AS Activos FROM catalogo_colaboradores AS A WHERE STATUS= 1 AND CATALOGO_LECTOR_ID = 3 AND PAGO = 'SEMANAL' AND identificador_noi = 'GATSA')  AS faltantes,
               
                   IF((SELECT plantilla_autorizada FROM catalogo_personal_autorizado WHERE unidad= 'GATSA') < (SELECT COUNT(*) AS Activos FROM catalogo_colaboradores AS A WHERE STATUS= 1 and CATALOGO_LECTOR_ID = 3 AND PAGO = 'SEMANAL' AND identificador_noi = 'GATSA'), 
                      
                  IF((SELECT plantilla_autorizada FROM catalogo_personal_autorizado WHERE unidad= 'GATSA') > (SELECT COUNT(*) AS Activos FROM catalogo_colaboradores AS A WHERE STATUS= 1 and CATALOGO_LECTOR_ID = 3 AND PAGO = 'SEMANAL' AND identificador_noi = 'GATSA'), 'Incompleta','Sobrepasa'),
                  
                      IF((SELECT plantilla_autorizada FROM catalogo_personal_autorizado WHERE unidad= 'GATSA') = (SELECT COUNT(*) AS Activos FROM catalogo_colaboradores AS A WHERE STATUS= 1 and CATALOGO_LECTOR_ID = 3 AND PAGO = 'SEMANAL' AND identificador_noi = 'GATSA'),'Completa','Incompleta'))AS color
sql;
        return $mysqli->queryAll($query);
    }

    public static function getUNIDESH(){
        $mysqli = Database::getInstance();
        $query=<<<sql
                SELECT (SELECT plantilla_autorizada FROM catalogo_personal_autorizado WHERE unidad= 'UNIDESH') AS plantilla_autorizada, 
               (SELECT COUNT(*) AS Activos FROM catalogo_colaboradores AS A WHERE STATUS= 1 AND identificador_noi = 'UNIDESH') AS activos, 
               (SELECT plantilla_autorizada FROM catalogo_personal_autorizado WHERE unidad= 'UNIDESH') - 
               (SELECT COUNT(*) AS Activos FROM catalogo_colaboradores AS A WHERE STATUS= 1 AND identificador_noi = 'UNIDESH')  AS faltantes,
               
                   IF((SELECT plantilla_autorizada FROM catalogo_personal_autorizado WHERE unidad= 'UNIDESH') < (SELECT COUNT(*) AS Activos FROM catalogo_colaboradores AS A WHERE STATUS= 1 AND identificador_noi = 'UNIDESH'), 
                      
                  IF((SELECT plantilla_autorizada FROM catalogo_personal_autorizado WHERE unidad= 'UNIDESH') > (SELECT COUNT(*) AS Activos FROM catalogo_colaboradores AS A WHERE STATUS= 1 AND identificador_noi = 'UNIDESH'), 'Incompleta','Sobrepasa'),
                  
                      IF((SELECT plantilla_autorizada FROM catalogo_personal_autorizado WHERE unidad= 'UNIDESH') = (SELECT COUNT(*) AS Activos FROM catalogo_colaboradores AS A WHERE STATUS= 1 AND identificador_noi = 'UNIDESH'),'Completa','Incompleta'))AS color
sql;
        return $mysqli->queryAll($query);
    }

    public static function getProduccionXochimilco(){
        $mysqli = Database::getInstance();
        $query=<<<sql
                  
 SELECT (SELECT plantilla_autorizada FROM catalogo_personal_autorizado WHERE unidad= 'Administrativos') AS plantilla_autorizada, 
               (SELECT COUNT(*) AS Activos FROM catalogo_colaboradores AS A WHERE STATUS= 1 AND IDENTIFICADOR_NOI = '') AS activos, 
               (SELECT plantilla_autorizada FROM catalogo_personal_autorizado WHERE unidad= 'Administrativos') - 
               (SELECT COUNT(*) AS Activos FROM catalogo_colaboradores AS A WHERE STATUS= 1 AND IDENTIFICADOR_NOI = '')  AS faltantes,
               
                   IF((SELECT plantilla_autorizada FROM catalogo_personal_autorizado WHERE unidad= 'Administrativos') < (SELECT COUNT(*) AS Activos FROM catalogo_colaboradores AS A WHERE STATUS= 1 AND IDENTIFICADOR_NOI = ''), 
                      
                  IF((SELECT plantilla_autorizada FROM catalogo_personal_autorizado WHERE unidad= 'Administrativos') > (SELECT COUNT(*) AS Activos FROM catalogo_colaboradores AS A WHERE STATUS= 1 AND IDENTIFICADOR_NOI = ''), 'Incompleta','Sobrepasa'),
                  
                      IF((SELECT plantilla_autorizada FROM catalogo_personal_autorizado WHERE unidad= 'Administrativos') = (SELECT COUNT(*) AS Activos FROM catalogo_colaboradores AS A WHERE STATUS= 1 AND IDENTIFICADOR_NOI = ''),'Completa','Incompleta'))AS color
sql;
        return $mysqli->queryAll($query);
    }

    public static function getAll(){
      $mysqli = Database::getInstance();
      $query=<<<sql
        SELECT
          c.catalogo_colaboradores_id,
          c.clave_noi,
          c.nombre,
          c.apellido_paterno,
          c.apellido_materno,
          s.nombre AS status,
          c.motivo,
          c.sexo,
          c.numero_identificador,
          c.rfc,
          e.nombre AS catalogo_empresa_id,
          u.nombre AS catalogo_ubicacion_id,
          d.nombre AS catalogo_departamento_id,
          p.nombre AS catalogo_puesto_id,
          h.nombre AS catalogo_horario_id,
          c.fecha_alta,
          c.fecha_baja,
          c.foto,
          c.pago,
          c.opcion,
          c.numero_empleado
        FROM catalogo_colaboradores c
        JOIN catalogo_empresa e ON e.catalogo_empresa_id = c.catalogo_empresa_id
        JOIN catalogo_ubicacion u ON u.catalogo_ubicacion_id = c.catalogo_ubicacion_id
        JOIN catalogo_departamento d ON d.catalogo_departamento_id = c.catalogo_departamento_id
        JOIN catalogo_puesto p ON p.catalogo_puesto_id = c.catalogo_puesto_id
        JOIN catalogo_horario h ON h.catalogo_horario_id = c.catalogo_horario_id
        JOIN catalogo_status s ON s.catalogo_status_id = c.status WHERE c.status !=2
sql;
        return $mysqli->queryAll($query);
    }

    public static function getAllColaboradores($perfilId, $plantaId, $departamentoId, $accion, $departamento, $planta, $usuario, $perfil, $propios, $filtro){
      $mysqli = Database::getInstance();
      $query=<<<sql

SELECT c.catalogo_colaboradores_id, c.nombre, c.apellido_paterno, c.apellido_materno, s.nombre AS status, c.motivo, c.sexo, c.numero_identificador, c.rfc, e.nombre AS catalogo_empresa_id, u.nombre AS catalogo_ubicacion_id, d.nombre AS catalogo_departamento_id, p.nombre AS catalogo_puesto_id, h.nombre AS catalogo_horario_id, c.fecha_alta, c.fecha_baja, c.foto, c.pago, c.opcion, c.numero_empleado, p.nombre AS nombre_puesto, e.nombre AS nombre_empresa, d.nombre AS nombre_departamento, cp.catalogo_planta_id, c.identificador_noi
FROM catalogo_colaboradores c
JOIN catalogo_empresa e ON e.catalogo_empresa_id = c.catalogo_empresa_id
JOIN catalogo_departamento d ON d.catalogo_departamento_id = c.catalogo_departamento_id
JOIN catalogo_status s ON s.catalogo_status_id = c.status 
JOIN catalogo_ubicacion u ON u.catalogo_ubicacion_id = c.catalogo_ubicacion_id 
JOIN catalogo_puesto p ON p.catalogo_puesto_id = c.catalogo_puesto_id 
JOIN catalogo_planta cp ON cp.catalogo_planta_id = c.catalogo_ubicacion_id 
JOIN catalogo_horario h ON h.catalogo_horario_id = c.catalogo_horario_id 
sql;


      if($perfilId == 1 || $perfilId == 4 ){

        if($accion == 1){
          $query .=<<<sql
WHERE c.status = 1 AND c.catalogo_departamento_id = "$departamentoId" 
sql;
        }

        if($accion == 2){
          $query .=<<<sql
WHERE c.status = 1 
sql;
        }

      }

      if($perfilId == 6){
        if($accion == 1){
          $query .=<<<sql
WHERE c.status = 1 AND c.catalogo_departamento_id = "$departamentoId"
sql;
        }

        if($accion == 2){
          $query .=<<<sql
WHERE c.status = 1 
sql;
        }

        if($accion == 4){
          $query .=<<<sql
WHERE c.status = 1 AND c.catalogo_departamento_id = "$departamentoId"
sql;
        }


        if($accion == 6){
          $query .=<<<sql
WHERE c.status = 1 AND cp.catalogo_planta_id = "$plantaId"
sql;
        }
      }



      if($perfilId == 5){
        $query .=<<<sql
WHERE c.status = 1 AND c.catalogo_departamento_id = "$departamentoId" 
sql;
      }

      if($filtro == "AND c.identificador_noi = 'vacio' "){
        $filtro = "AND c.identificador_noi = '' ";
      }

        $query .=<<<sql
$filtro ORDER BY c.apellido_paterno ASC 
sql;

      return $mysqli->queryAll($query);

    }

    public static function getAllReporte($filtro){
      $mysqli = Database::getInstance();
      $query=<<<sql
SELECT c.catalogo_colaboradores_id, c.nombre, c.apellido_paterno, c.apellido_materno, s.nombre AS status, c.motivo, c.sexo, c.numero_identificador, c.rfc, e.nombre AS catalogo_empresa_id, u.nombre AS catalogo_ubicacion_id, d.nombre AS catalogo_departamento_id, p.nombre AS catalogo_puesto_id, h.nombre AS catalogo_horario_id, c.fecha_alta, c.fecha_baja, c.foto, c.pago, c.opcion, c.numero_empleado
FROM catalogo_colaboradores c
JOIN catalogo_empresa e ON e.catalogo_empresa_id = c.catalogo_empresa_id
JOIN catalogo_ubicacion u ON u.catalogo_ubicacion_id = c.catalogo_ubicacion_id
JOIN catalogo_departamento d ON d.catalogo_departamento_id = c.catalogo_departamento_id
JOIN catalogo_puesto p ON p.catalogo_puesto_id = c.catalogo_puesto_id
JOIN catalogo_horario h ON h.catalogo_horario_id = c.catalogo_horario_id
JOIN catalogo_status s ON s.catalogo_status_id = c.status WHERE c.status !=2 $filtro 
sql;
      //print_r($query);
      return $mysqli->queryAll($query);
    }

    public static function insert($colaborador){
        $mysqli = Database::getInstance();
        $query=<<<sql
      INSERT INTO catalogo_colaboradores
      VALUES (NULL, :clave_noi, :identificador, :nombre, :apellido_paterno, :apellido_materno, :status, :motivo,
	:genero, :numero_identificador, :rfc, :fecha_nacimiento, :catalogo_empresa_id, :catalogo_lector_id, :catalogo_ubicacion_id,1,
	:catalogo_departamento_id, :catalogo_puesto_id, :catalogo_horario_id, :fecha_alta, :fecha_baja, :foto, :pago,
	:opcion, :letra, :privilegiado, :tipo_horario, :catalogo_lector_secundario_id,0);
sql;
        $parametros = array(
            ':nombre' => $colaborador->_nombre,
            ':apellido_paterno' => $colaborador->_apellido_paterno,
            ':apellido_materno' => $colaborador->_apellido_materno,
            ':motivo' => $colaborador->_motivo,
            ':genero' => $colaborador->_genero,
            ':numero_identificador' => $colaborador->_numero_identificacion,
            ':rfc' => $colaborador->_rfc,
            ':fecha_nacimiento' => $colaborador->_fecha_nac,
            ':catalogo_empresa_id' => $colaborador->_id_catalogo_empresa,
            ':catalogo_lector_id' => $colaborador->_id_catalogo_lector,
            ':catalogo_ubicacion_id' => $colaborador->_id_catalogo_ubicacion,
            ':catalogo_departamento_id' => $colaborador->_id_catalogo_departamento,
            ':catalogo_puesto_id' => $colaborador->_id_catalogo_puesto,
            ':catalogo_horario_id' => $colaborador->_horario,
            ':fecha_alta' => $colaborador->_fecha_alta,
            ':fecha_baja' => $colaborador->_fecha_baja,
            ':foto' => $colaborador->_foto,
            ':pago' => $colaborador->_pago,
            ':opcion' => $colaborador->_opcion,
            ':status' => $colaborador->_status,
            ':letra' => $colaborador->_letra_ubicacion,
            ':clave_noi' => $colaborador->_clave_noi,
            ':identificador' => $colaborador->_identificador,
            ':privilegiado' => $colaborador->_privilegiado,
            ':tipo_horario' => $colaborador->_tipo_horario,
            ':catalogo_lector_secundario_id' => $colaborador->_id_catalogo_lector_secundario
        );
        $id = $mysqli->insert($query,$parametros);
        $accion = new \stdClass();
        $accion->_sql= $query;
        $accion->_parametros = $parametros;
        $accion->_id = $id;

        UtileriasLog::addAccion($accion);
        return $id;
    }

    public static function insertIngresoProyecto($colaborador){
        $mysqli = Database::getInstance();
        $query=<<<sql
        INSERT INTO ingreso_proyecto
      VALUES (NULL, :id_colaborador, 'NO SE HA CARGADO' ,'NO SE HA CARGADO', 'NO SE HA DEFINIDO');
sql;
        $parametros = array(
            ':id_colaborador' => $colaborador->_id_colaborador
        );
        $id = $mysqli->insert($query,$parametros);
        $accion = new \stdClass();
        $accion->_sql= $query;
        $accion->_parametros = $parametros;
        $accion->_id = $id;

        UtileriasLog::addAccion($accion);
        return $id;
    }

    public static function insertPuestosOcupados($puestos_ocupados){
        $mysqli = Database::getInstance();
        $query=<<<sql
        INSERT INTO puestos_ocupados
      VALUES (NULL, :catalogo_puesto_id, :catalogo_colaboradores_id , :fecha_actualizacion);
sql;
        $parametros = array(
            ':catalogo_puesto_id' => $puestos_ocupados->_id_puesto,
            ':catalogo_colaboradores_id' => $puestos_ocupados->_id_colaborador,
            ':fecha_actualizacion' => $puestos_ocupados->_fecha
        );
        $id = $mysqli->insert($query,$parametros);
        $accion = new \stdClass();
        $accion->_sql= $query;
        $accion->_parametros = $parametros;
        $accion->_id = $id;

        UtileriasLog::addAccion($accion);
        return $id;
    }

    public static function insertUsuariosEncuestas($usuarios_encuestas){
        $mysqli = Database::getInstance();
        $query=<<<sql
        INSERT INTO usuarios_encuestas
      VALUES (NULL, :nombre, :usuario, MD5(:contrasena), :status, :id_colaborador);
sql;
        $parametros = array(
            ':nombre' => $usuarios_encuestas->_nombre,
            ':usuario' => $usuarios_encuestas->_usuario_clave,
            ':contrasena' => $usuarios_encuestas->_contrasena,
            ':status' => 1,
            ':id_colaborador' => $usuarios_encuestas->_id_colaborador
        );
        $id = $mysqli->insert($query,$parametros);
        $accion = new \stdClass();
        $accion->_sql= $query;
        $accion->_parametros = $parametros;
        $accion->_id = $id;

        UtileriasLog::addAccion($accion);
        return $id;
    }
    public static function insertCuestionarioIngreso($cuestionario){
        $mysqli = Database::getInstance();
        $query=<<<sql
        INSERT INTO cuestionario_colaborador
      VALUES (NULL, 1, :id_colaborador,  0);
sql;
        $parametros = array(
            ':id_colaborador' => $cuestionario->_id_colaborador
        );
        $id = $mysqli->insert($query,$parametros);
        $accion = new \stdClass();
        $accion->_sql= $query;
        $accion->_parametros = $parametros;
        $accion->_id = $id;

        UtileriasLog::addAccion($accion);
        return $id;
    }


    public static function insertSueldoInicial($cuestionario){
        $mysqli = Database::getInstance();
        $query=<<<sql
        INSERT INTO sueldos_historico (id_sueldo, clave, sal_diario, sdi, numero, fecha_alta, identificador)
        (SELECT NULL, clave, sal_diario,sdi,1,:fecha_alta, identificador
        FROM operacion_noi WHERE clave = :clave)
sql;
        $parametros = array(
            ':clave' => $cuestionario->_clave,
            ':fecha_alta' => $cuestionario->_fecha_alta
        );
        $id = $mysqli->insert($query,$parametros);
        $accion = new \stdClass();
        $accion->_sql= $query;
        $accion->_parametros = $parametros;
        $accion->_id = $id;

        UtileriasLog::addAccion($accion);
        return $id;
    }

    public static function updateNumeroEmpleado($id, $numero_identificador){
      $mysqli = Database::getInstance();
      $query=<<<sql
      UPDATE catalogo_colaboradores SET
        numero_empleado = CONCAT(numero_empleado,$numero_identificador)
      WHERE catalogo_colaboradores_id = $id
sql;
      $accion = new \stdClass();
      $accion->_sql= $query;
      $accion->_parametros = $parametros;
      UtileriasLog::addAccion($accion);
      return $mysqli->update($query);
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
        SELECT * FROM catalogo_departamento WHERE status = 1 ORDER BY catalogo_departamento_id
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

    public static function update($colaborador){
      $mysqli = Database::getInstance(true);
      $query=<<<sql
      UPDATE catalogo_colaboradores SET
        clave_noi = :clave_noi,
        nombre = :nombre,
        apellido_paterno = :apellido_paterno,
        apellido_materno = :apellido_materno,
        status = :status,
        motivo = :motivo,
        sexo = :sexo,
        numero_identificador = :numero_identificador,
        rfc = :rfc,
        catalogo_empresa_id = :catalogo_empresa_id,
	catalogo_lector_id = :catalogo_lector_id,
	catalogo_lector_secundario_id = :catalogo_lector_secundario_id,
        catalogo_ubicacion_id = :catalogo_ubicacion_id,
        catalogo_departamento_id = :catalogo_departamento_id,
        catalogo_puesto_id = :catalogo_puesto_id,
        catalogo_horario_id = :catalogo_horario_id,
        fecha_alta = :fecha_alta,
        fecha_baja = :fecha_baja,
        foto = :foto,
        pago = :pago,
        opcion = :opcion,
        numero_empleado = :numero_empleado,
	      privilegiado = :privilegiado,
	      horario_tipo = :horario_tipo
      WHERE catalogo_colaboradores_id = :catalogo_colaboradores_id
sql;
      $parametros = array(
        ':catalogo_colaboradores_id' => $colaborador->_catalogo_colaboradores_id,
        ':nombre' => $colaborador->_nombre,
        ':apellido_paterno' => $colaborador->_apellido_paterno,
        ':apellido_materno' => $colaborador->_apellido_materno,
        ':status' => $colaborador->_status,
        ':motivo' => $colaborador->_motivo,
        ':sexo' => $colaborador->_genero,
        ':numero_identificador' => $colaborador->_numero_identificacion,
        ':rfc' => $colaborador->_rfc,
        ':catalogo_empresa_id' => $colaborador->_id_catalogo_empresa,
	':catalogo_lector_id' => $colaborador->_id_catalogo_lector,
	':catalogo_lector_secundario_id' => $colaborador->_id_catalogo_lector_secundario,
        ':catalogo_ubicacion_id' => $colaborador->_id_catalogo_ubicacion,
        ':catalogo_departamento_id' => $colaborador->_id_catalogo_departamento,
        ':catalogo_puesto_id' => $colaborador->_id_catalogo_puesto,
        ':catalogo_horario_id' => $colaborador->_horario,
        ':fecha_alta' => $colaborador->_fecha_alta,
        ':fecha_baja' => $colaborador->_fecha_baja,
        ':foto' => $colaborador->_foto,
        ':pago' => $colaborador->_pago,
        ':opcion' => $colaborador->_opcion,
        ':numero_empleado' => $colaborador->_numero_empleado,
        ':clave_noi' => $colaborador->_clave_noi,
      	':privilegiado' => $colaborador->_privilegiado,
        ':horario_tipo' => $colaborador->_tipo_horario
      );

      $accion = new \stdClass();
      $accion->_sql= $query;
      $accion->_parametros = $parametros;
      $accion->_id = $colaborador->_catalogo_colaboradores_id;
      UtileriasLog::addAccion($accion);
      return $mysqli->update($query, $parametros);
    }

    public static function delete($id){
      $mysqli = Database::getInstance();
      $query=<<<sql
      UPDATE catalogo_colaboradores set status = 2 WHERE catalogo_colaboradores_id = $id
sql;
      $accion = new \stdClass();
      $accion->_sql= $query;
      $accion->_parametros = $parametros;
      $accion->_id = $id;
      UtileriasLog::addAccion($accion);
      return $mysqli->update($query);
    }

    public static function getById($id){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT
        c.*,
        s.nombre AS status
      FROM catalogo_colaboradores c
      JOIN catalogo_status s
      ON c.status = s.catalogo_status_id
      WHERE c.catalogo_colaboradores_id = $id
sql;
      return $mysqli->queryOne($query);
    }

    public static function getByIdReporte($id){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT
        c.catalogo_colaboradores_id,
        c.clave_noi,
        c.nombre,
        c.apellido_paterno,
        c.apellido_materno,
        s.nombre AS status,
        c.motivo,
        c.sexo,
        c.numero_identificador,
        c.rfc,
        e.nombre AS catalogo_empresa_id,
        u.nombre AS catalogo_ubicacion_id,
        d.nombre AS catalogo_departamento_id,
        p.nombre AS catalogo_puesto_id,
        h.nombre AS catalogo_horario_id,
        c.fecha_alta,
        c.fecha_baja,
        c.foto,
        c.pago,
        c.opcion,
        c.numero_empleado
      FROM catalogo_colaboradores c
      JOIN catalogo_empresa e ON e.catalogo_empresa_id = c.catalogo_empresa_id
      JOIN catalogo_ubicacion u ON u.catalogo_ubicacion_id = c.catalogo_ubicacion_id
      JOIN catalogo_departamento d ON d.catalogo_departamento_id = c.catalogo_departamento_id
      JOIN catalogo_puesto p ON p.catalogo_puesto_id = c.catalogo_puesto_id
      JOIN catalogo_horario h ON h.catalogo_horario_id = c.catalogo_horario_id
      JOIN catalogo_status s ON s.catalogo_status_id = c.status WHERE c.status !=2 AND c.catalogo_colaboradores_id = '$id'
sql;
      return $mysqli->queryOne($query);
    }

    public static function getStatus(){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT * FROM catalogo_status
sql;
        return $mysqli->queryAll($query);
    }

    public static function getUbicacionId(){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT * FROM catalogo_ubicacion
sql;
        return $mysqli->queryAll($query);
    }

    public static function getIdLector(){

   	$mysqli = Database::getInstance();
	$query=<<<sql
SELECT * FROM catalogo_lector WHERE status = 1;
sql;

	return $mysqli->queryAll($query);
    }

    public static function getIdEmpresa(){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT * FROM catalogo_empresa WHERE status != 2 ORDER BY `catalogo_empresa`.`nombre` ASC
sql;
        return $mysqli->queryAll($query);
    }

    public static function getIdUbicacion(){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT * FROM catalogo_ubicacion WHERE status != 2  ORDER BY `catalogo_ubicacion`.`nombre` ASC
sql;
        return $mysqli->queryAll($query);
    }

    public static function getIdDepartamento(){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT * FROM catalogo_departamento WHERE status != 2 ORDER BY `catalogo_departamento`.`nombre` ASC
sql;
      return $mysqli->queryAll($query);
    }

    public static function getIdPuesto(){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT * FROM catalogo_puesto WHERE status != 2 ORDER BY `catalogo_puesto`.`nombre` ASC
sql;
      return $mysqli->queryAll($query);
    }

    public static function getIdHorario(){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT * FROM catalogo_horario WHERE status != 2
sql;
      return $mysqli->queryAll($query);
    }

    public static function getIdIncentivo(){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT * FROM catalogo_incentivo WHERE status != 2
sql;
      return $mysqli->queryAll($query);
    }

    public static function getIdMotivoBaja(){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT * FROM catalogo_motivo_baja WHERE status != 2
sql;
      return $mysqli->queryAll($query);
    }

    public static function insertIncentivo($incentivo){

	    $mysqli = Database::getInstance();
      $query=<<<sql
      INSERT INTO incentivo_colaborador
      VALUES ($incentivo->_catalogo_colaboradores_id , $incentivo->_catalogo_incentivo_id,$incentivo->_cantidad);
sql;
      $accion = new \stdClass();
      $accion->_sql= $query;
      $accion->_parametros = $parametros;
      UtileriasLog::addAccion($accion);
      return $mysqli->insert($query);
    }

    public static function getIncentivoById($id){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT
        i.*,
        c.cantidad
      FROM catalogo_incentivo i
      JOIN incentivo_colaborador c
      ON i.catalogo_incentivo_id = c.catalogo_incentivo_id  WHERE c.catalogo_colaboradores_id = $id
sql;
      return $mysqli->queryAll($query);
    }

    public static function deleteIncentivo($id){
	    $mysqli = Database::getInstance();
      $query=<<<sql
      DELETE FROM incentivo_colaborador WHERE catalogo_colaboradores_id = $id
sql;
      $accion = new \stdClass();
      $accion->_sql= $query;
      $accion->_parametros = $parametros;
      UtileriasLog::addAccion($accion);
      return $mysqli->update($query);
    }



    public static function insertHorario($horario){
	    $mysqli = Database::getInstance();
      $query=<<<sql
      INSERT INTO colaboradores_horario
      VALUES ($horario->_catalogo_colaboradores_id , $horario->_catalogo_horario_id, $horario->_default);
sql;
      $accion = new \stdClass();
      $accion->_sql= $query;
      $accion->_parametros = $parametros;
      UtileriasLog::addAccion($accion);
      return $mysqli->insert($query);
    }

    public static function deleteHorario($id){
	    $mysqli = Database::getInstance();
      $query=<<<sql
      DELETE FROM colaboradores_horario WHERE catalogo_colaboradores_id = $id
sql;
      $accion = new \stdClass();
      $accion->_sql= $query;
      $accion->_parametros = $parametros;
      UtileriasLog::addAccion($accion);
      return $mysqli->update($query);
    }

    public static function getHorarioById($id){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT
        h.*,
        ch.horario_default
      FROM colaboradores_horario ch
      JOIN catalogo_horario h
      ON ch.catalogo_horario_id = h.catalogo_horario_id  WHERE ch.catalogo_colaboradores_id = $id
      ORDER BY ch.horario_default DESC
sql;
      return $mysqli->queryAll($query);
    }

    public static function getIdentificador(){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT
        distinct identificador
      FROM operacion_noi
sql;
      return $mysqli->queryAll($query);
}

      public static function getOperacionNoi($filtro){
// SELECT t1.* FROM operacion_noi t1 LEFT JOIN catalogo_colaboradores t2 ON t2.clave_noi = t1.clave WHERE t2.clave_noi IS NULL
//        SELECT * FROM operacion_noi $filtro
        $mysqli = Database::getInstance();
/*
        $query=<<<sql
SELECT t1.* FROM operacion_noi t1 LEFT JOIN catalogo_colaboradores t2 ON t2.clave_noi = t1.clave WHERE t1.status != 'B' AND t2.clave_noi IS NULL $filtro ORDER BY t1.ap_pat ASC
sql;
*/
	$query=<<<sql
SELECT t1.* FROM operacion_noi t1 LEFT JOIN catalogo_colaboradores t2 ON (t2.clave_noi = t1.clave AND t1.identificador = t2.identificador_noi) WHERE t1.status != 'B' AND catalogo_colaboradores_id IS NULL $filtro GROUP BY operacion_noi_id ORDER BY `t1`.`nombre` ASC
sql;
        return $mysqli->queryAll($query);
      }

      public static function getOperacionNoiId($id){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT
          *
        FROM operacion_noi
        WHERE CONCAT(identificador,clave) LIKE '$id'
sql;
        return $mysqli->queryOne($query);
      }

      public static function getCatalogoEmpresa($id){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT cc.catalogo_colaboradores_id, cc.catalogo_empresa_id, ce.catalogo_empresa_id, ce.nombre AS nombre_empresa FROM catalogo_colaboradores AS cc INNER JOIN catalogo_empresa AS ce WHERE cc.catalogo_empresa_id = ce.catalogo_empresa_id AND catalogo_colaboradores_id = $id
sql;
        return $mysqli->queryOne($query);
      }

      public static function getCatalogoUbicacion($id){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT cc.catalogo_colaboradores_id, cc.catalogo_ubicacion_id, cu.catalogo_ubicacion_id, cu.nombre FROM catalogo_colaboradores AS cc INNER JOIN catalogo_ubicacion AS cu WHERE cc.catalogo_colaboradores_id = $id AND cc.catalogo_ubicacion_id = cu.catalogo_ubicacion_id
sql;
        return $mysqli->queryOne($query);
      }

      public static function getCatalogoDepartamento($id){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT cc.catalogo_colaboradores_id, cc.catalogo_departamento_id, cd.catalogo_departamento_id, cd.nombre FROM catalogo_colaboradores AS cc INNER JOIN catalogo_departamento AS cd WHERE cc.catalogo_colaboradores_id = $id AND cc.catalogo_departamento_id = cd.catalogo_departamento_id
sql;
        return $mysqli->queryOne($query);
      }

      public static function getCatalogoLector($id){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT cc.catalogo_colaboradores_id, cc.catalogo_lector_id, cd.catalogo_lector_id, cd.nombre FROM catalogo_colaboradores AS cc INNER JOIN catalogo_lector AS cd WHERE cc.catalogo_colaboradores_id = $id AND cc.catalogo_lector_id = cd.catalogo_lector_id
sql;
        return $mysqli->queryOne($query);
      }

      public static function getCatalogoPuesto($id){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT cc.catalogo_colaboradores_id, cc.catalogo_puesto_id,  cd.catalogo_puesto_id, cd.nombre FROM catalogo_colaboradores AS cc INNER JOIN catalogo_puesto AS cd WHERE cc.catalogo_colaboradores_id = $id AND cc.catalogo_puesto_id = cd.catalogo_puesto_id
sql;
        return $mysqli->queryOne($query);
      }

      public static function getIncentivosColaborador($id){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT ic.catalogo_colaboradores_id, ic.cantidad, cc.catalogo_colaboradores_id, ci.nombre FROM incentivo_colaborador AS ic INNER JOIN catalogo_colaboradores AS cc ON ic.catalogo_colaboradores_id = cc.catalogo_colaboradores_id INNER JOIN catalogo_incentivo AS ci ON ci.catalogo_incentivo_id = ic.catalogo_incentivo_id WHERE cc.catalogo_colaboradores_id = $id
sql;
        return $mysqli->queryAll($query);
      }

      public static function getStatusColaborador($id){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT cc.status, cs.nombre FROM catalogo_colaboradores cc JOIN catalogo_status cs WHERE cc.catalogo_colaboradores_id = $id AND cc.status = cs.catalogo_status_id
sql;
        return $mysqli->queryOne($query);
      }

      public static function getMotivoById($id){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT * FROM catalogo_motivo_baja where catalogo_motivo_baja_id = $id
sql;
        return $mysqli->queryOne($query);
      }

      public static function getNominaIdentificador(){
        $mysqli = Database::getInstance();
        $query=<<<sql
        SELECT identificador_noi FROM catalogo_colaboradores GROUP BY identificador_noi 
sql;
        return $mysqli->queryAll($query);
      }


      public static function getDiasLaboralesColaborador($catalogo_horario_id){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT dl.nombre AS dia_laboral FROM catalogo_horario ch
INNER JOIN horario_dias_laborales hdl ON (hdl.catalogo_horario_id = ch.catalogo_horario_id)
INNER JOIN dias_laborales dl ON (dl.dias_laborales_id = hdl.dias_laborales_id)
WHERE ch.catalogo_horario_id = $catalogo_horario_id 
sql;
        return $mysqli->queryAll($query);
      }



}
