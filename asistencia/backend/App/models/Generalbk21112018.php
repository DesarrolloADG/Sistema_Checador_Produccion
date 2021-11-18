<?php
namespace App\models;
defined("APPPATH") OR die("Access denied");

use \Core\Database;
use \App\interfaces\Crud;
use \App\controllers\UtileriasLog;

class General implements Crud{

  // perfil_id -> 1.- ROOT 4.- Admin 5.- Personalizado 6. Recursos humanos
  // identificador_noi -> "" | "GATSA -> Pam liquidos" | "UNIDESH -> Pan deshidratados" | "VALLEJO" | "XOCHIMILCO"
  // planta_id -> "" | "GATSA -> Pam liquidos" | "UNIDESH -> Pan deshidratados" | "VALLEJO" | "XOCHIMILCO"
  public static function getAllColaboradores($perfil, $identificador, $planta, $departamento, $filtro){
    $identificador = explode("_", $identificador);
    $identificador = strtoupper($identificador[0]);
    $html =<<<html
      perfil -> {$perfil} <br>
      identificador -> {$identificador} <br>
      planta -> {$planta} <br>
      departamento -> {$departamento} <br>
html;

    $mysqli = Database::getInstance();
    $query =<<<sql
SELECT c.catalogo_colaboradores_id, c.catalogo_colaboradores_id, c.nombre, c.apellido_paterno, c.apellido_materno, c.identificador_noi, c.foto, c.numero_empleado, c.pago, ce.nombre AS nombre_empresa, cp.nombre AS nombre_puesto, cd.nombre AS nombre_departamento FROM catalogo_colaboradores c 
INNER JOIN catalogo_empresa AS ce USING (catalogo_empresa_id) 
INNER JOIN catalogo_puesto AS cp USING (catalogo_puesto_id) 
INNER JOIN catalogo_departamento AS cd USING (catalogo_departamento_id) 
sql;
    // FILTRO POR PERFIL
    if($perfil == 1){ // PERFIL ROOT
$query .=<<<sql
WHERE c.status = 1 
sql;
    }elseif($perfil == 4){ // PERFIL ADMINISTRADOR
      $query .=<<<sql
WHERE c.status = 1 
sql;
    }elseif($perfil == 5){ // PERFIL PERSONALIZADO
// WHERE c.status = 1 AND c.identificador_noi = "$identificador" 
      $query .=<<<sql
WHERE c.status = 1 AND c.catalogo_departamento_id = "$departamento" 
sql;
    }elseif($perfil == 6){ // PERFIL RECURSOS HUMANOS
      if($planta == 1){
        $query .=<<<sql
WHERE c.status = 1 
sql;
      }else{
        $query .=<<<sql
WHERE c.status = 1 AND c.identificador_noi = "$identificador" 
sql;
      }
    }else{ 
      $query .=<<<sql
WHERE c.status = 3 
sql;
    }

    $nuevoFiltro = "";
    foreach ($filtro as $key => $value) {
      if(!empty($value)){
        if($value == 'vacio' && $key == 'c.identificador_noi') $nuevoFiltro .= " AND " . $key . " = " . "''" . " ";
        else $nuevoFiltro .= " AND " . $key . " = " . " '$value' " . " ";
      }
    }

    $query .=<<<sql
{$nuevoFiltro} ORDER BY c.apellido_paterno ASC 
sql;
    return $mysqli->queryAll($query);
  }

  public static function getPeriodo($data){
    $mysqli = Database::getInstance();
    if($data->_tipo_busqueda == 0){ /* CUANDO SE BUSCA UN UNICO PERIODO ABIERTO*/
      $query =<<<sql
SELECT * FROM prorrateo_periodo WHERE status = 0 AND tipo = "$data->_tipo" ORDER BY prorrateo_periodo_id ASC 
sql;
    }

    if($data->_tipo_busqueda == 1){ /* CUANDO SE BUSCA POR SEMANALES O QUINCENALES HISTORICOS */
      $query =<<<sql
SELECT * FROM prorrateo_periodo WHERE status != 0 AND tipo = "$data->_tipo" ORDER BY fecha_inicio DESC
sql;
    }

    if($data->_tipo_busqueda == 2){ /* CUANDO SE BUSCA UN UNICO PERIODO POR ID */
      $query =<<<sql
SELECT * FROM prorrateo_periodo WHERE prorrateo_periodo_id = "$data->_prorrateo_periodo_id" 
sql;
    }

    return $mysqli->queryAll($query);
  }

  public static function getStatus(){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT * FROM catalogo_status
sql;
        return $mysqli->queryAll($query);
    }

    public static function getAll(){

	$mysqli = Database::getInstance();
        $query=<<<sql
        SELECT * FROM catalogo_dia_festivo;
sql;
        return $mysqli->queryAll($query);
    }

    public static function getDatosUsuarioLogeado($user){
        $mysqli = Database::getInstance();
        $query1=<<<sql
SELECT *, cp.nombre AS nombre_planta, cp.catalogo_planta_id 
FROM utilerias_administradores AS a 
INNER JOIN catalogo_planta AS cp USING (catalogo_planta_id)
WHERE usuario LIKE '$user'
sql;
      $query=<<<sql
SELECT u.administrador_id, u.usuario, u.perfil_id, u.identificador, u.catalogo_planta_id,
cd.nombre AS nombre_departamento, cd.catalogo_departamento_id
FROM `utilerias_administradores` u 
INNER JOIN utilerias_administradores_departamentos ud ON (u.administrador_id = ud.id_administrador)
INNER JOIN catalogo_departamento cd ON (ud.catalogo_departamento_id = cd.catalogo_departamento_id)
WHERE u.status = 1 AND u.usuario = "$user"
sql;
        return $mysqli->queryOne($query);
    }

    public static function getDatosColaborador($idColaborador){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT cc.catalogo_colaboradores_id, cc.clave_noi, cc.identificador_noi, cc.nombre, o.sal_diario, o.sdi
FROM catalogo_colaboradores cc 
INNER JOIN operacion_noi o ON (cc.clave_noi = o.clave) 
WHERE cc.catalogo_colaboradores_id = "$idColaborador" AND cc.identificador_noi = o.identificador 
sql;
        return $mysqli->queryOne($query);
    }


    public static function getDatosUsuario($user){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT ua.administrador_id, ua.nombre, ua.perfil_id, ua.catalogo_planta_id, up.nombre AS nombre_perfil, cd.catalogo_departamento_id, cd.nombre, cp.nombre AS nombre_planta
FROM utilerias_administradores ua
JOIN utilerias_perfiles up USING( perfil_id )
JOIN catalogo_planta cp USING ( catalogo_planta_id )
JOIN utilerias_administradores_departamentos uad ON ( uad.id_administrador = ua.administrador_id )
JOIN catalogo_departamento cd ON ( cd.catalogo_departamento_id = uad.catalogo_departamento_id )
WHERE ua.usuario = "$user"
sql;
        return $mysqli->queryOne($query);
    }

    public static function insert($datos){
	      $mysqli = Database::getInstance(1);
        $query=<<<sql
        INSERT INTO catalogo_dia_festivo (catalogo_dia_festivo_id, nombre, descripcion, fecha, status) VALUES (NULL, :nombre, :descripcion, :fecha, '1');
sql;

    	$parametros = array(
    		':nombre'=>$datos->_nombre,
    		':descripcion'=>$datos->_descripcion,
    		':fecha'=>$datos->_fecha,
    	);
      $id = $mysqli->insert($query,$parametros);
      $accion = new \stdClass();
      $accion->_sql= $query;
      $accion->_parametros = $parametros;
      $accion->_id = $id;

      UtileriasLog::addAccion($accion);
      return $id;
    }


    public static function update($datos){
        $mysqli = Database::getInstance(true);
      $query=<<<sql
UPDATE catalogo_dia_festivo SET nombre = '122', descripcion = '1233', fecha = '2017-08-24', status = 2 WHERE catalogo_dia_festivo.catalogo_dia_festivo_id = :catalogo_dia_festivo_id;
sql;
      $parametros = array(
          ':catalogo_dia_festivo_id'=>$lectores->_catalogo_dia_festivo_id,
          ':nombre'=>$lectores->_nombre,
          ':descripcion'=>$lectores->_descripcion,
          ':fecha'=>$lectores->_fecha,
          ':status'=>$lectores->_status
        );
        $accion = new \stdClass();
        $accion->_sql= $query;
        $accion->_parametros = $parametros;
        $accion->_id = $lectores->_catalogo_dia_festivo_id;
        UtileriasLog::addAccion($accion);
        return $mysqli->update($query, $parametros);
    }

    public static function delete($id){

	$mysqli = Database::getInstance();
        $query=<<<sql
DELETE FROM `catalogo_dia_festivo` WHERE `catalogo_dia_festivo`.`catalogo_dia_festivo_id` = $id
sql;
        $parametros = array(':id'=>$id);
        $accion = new \stdClass();
        $accion->_sql= $query;
        $accion->_parametros = $parametros;
        $accion->_id = $id;
        UtileriasLog::addAccion($accion);
        return $mysqli->update($query, $parametros);
    }

    public static function deleteById($id){
        $mysqli = Database::getInstance();
        $query=<<<sql
DELETE FROM catalogo_dia_festivo WHERE catalogo_dia_festivo.catalogo_dia_festivo_id = $id
sql;
      $accion = new \stdClass();
      $accion->_sql= $query;
      $accion->_parametros = $parametros;
      $accion->_id = $id;
      UtileriasLog::addAccion($accion);
        return $mysqli->queryOne($query);
    }

    public static function getById($id){
        $mysqli = Database::getInstance();
        $query=<<<sql
SELECT *  FROM catalogo_dia_festivo WHERE catalogo_dia_festivo_id = $id
sql;

      return $mysqli->queryOne($query);
    }

    public static function getPermisos($usuario){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT * FROM utilerias_permisos WHERE usuario LIKE '$usuario'   
sql;
      return $mysqli->queryAll($query);
    }

    public static function getUsuario($usuario){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT * FROM utilerias_permisos WHERE usuario LIKE '$usuario'   
sql;
      return $mysqli->queryOne($query);
    }

    /*
        Buscar los colaboradores 
        @tipo: SEMANAL o QUINCENAL
    */
    public static function getColaboradores($tipo, $perfilUsuario, $catalogoDepartamentoId, $catalogoPlantaId, $status, $identificadorNOI, $filtro){
        $mysqli = Database::getInstance();
        if($perfilUsuario == 1 || $perfilUsuario == 4){
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
            if($status == 1){
                $query.=<<<sql
WHERE cc.pago = "$tipo" AND cc.status = 1 AND cc.catalogo_ubicacion_id = "$catalogoPlantaId"
sql;
            }

            if($status == 2){
                $query.=<<<sql
WHERE cc.pago = "$tipo" AND cc.status = 1 AND cc.catalogo_ubicacion_id = "$catalogoPlantaId" AND cc.catalogo_departamento_id = "$catalogoDepartamentoId"
sql;
            }
            if($status == 3){
                $query.=<<<sql
WHERE cc.pago = "$tipo" AND cc.status = 1 AND cc.catalogo_ubicacion_id = "$catalogoPlantaId"
sql;
            }

            if($status == 4){
                $query.=<<<sql
WHERE cc.pago = "$tipo" AND cc.status = 1 AND cc.catalogo_departamento_id = "$catalogoDepartamentoId"
sql;
            }

            if($status == 5){ // TODAS LAS PLANTAS
                $query.=<<<sql
WHERE cc.status = 1 
sql;
            }

            if($status == 6){ // TODAS LAS PLANTAS
                $query.=<<<sql
WHERE cc.pago  = "$tipo" AND cc.status = 1 
sql;
            }

            if($status == 10){
                $query.=<<<sql
WHERE cc.pago = "$tipo" AND cc.status = 1 AND cc.identificador_noi = "$identificadorNOI"
sql;
            }
        }

        // PERFIL PARA 4 "Administrador" y 5 "Personalizado"
        if($perfilUsuario == 5){
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
            if($status == 1){ // ES DE RH XOCHIMILCO Y PUEDE VER TODO
                $query.=<<<sql
                WHERE cc.pago = "$tipo" AND cc.status = 1 
sql;
            }

            if($status == 2){ // ES DE RECUSOS HUMANOS Y PUEDE VER TODOS LOS DEPARTAMENTOS DE SU PLANTA EXCEPTO RH XOCHIMILCO
                $query.=<<<sql
                WHERE cc.pago = "$tipo" AND cc.status = 1 AND cc.catalogo_departamento_id = "$catalogoDepartamentoId" 
sql;
            }

             //WHERE cc.pago = "$tipo" AND cc.status = 1 AND cc.catalogo_ubicacion_id = "$catalogoPlantaId" AND cc.catalogo_departamento_id = "$catalogoDepartamentoId"
            if($status == 3){ // ES DE RH y tiene incentivos propios 
                $query.=<<<sql
                WHERE cc.pago = "$tipo" AND cc.status = 1 AND cc.identificador_noi = "$identificadorNOI" 
sql;
            }

            if($status == 4){ // ES DE RH y tiene incentivos propios 
                $query.=<<<sql
                WHERE cc.pago = "$tipo" AND cc.status = 1 AND cc.catalogo_ubicacion_id = "$catalogoPlantaId" AND cc.catalogo_departamento_id = "$catalogoDepartamentoId" 
sql;
            }

            if($status == 5){ // ES DE RH y tiene incentivos propios 
                $query.=<<<sql
                WHERE cc.pago = "$tipo" AND cc.status = 1 AND cc.catalogo_ubicacion_id = "$catalogoPlantaId"
sql;
            }

        }

        $nuevoFiltro = "";
        foreach ($filtro as $key => $value) {
          if(!empty($value)){
            if($value == 'vacio' && $key == 'c.identificador_noi') $nuevoFiltro .= " AND " . $key . " = " . "''" . " ";
            else $nuevoFiltro .= " AND " . $key . " = " . " '$value' " . " ";
          }
        }

        $query.= $nuevoFiltro;
        //echo $query;
        return $mysqli->queryAll($query);
    }

    public static function getLastPeriodo($tipo){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT * FROM `prorrateo_periodo` WHERE tipo = "$tipo" ORDER BY `prorrateo_periodo`.`fecha_inicio` DESC 
sql;
      return $mysqli->queryOne($query);
    }

    public static function getSalarioMinimo(){
      $mysqli = Database::getInstance();
      $query=<<<sql
SELECT * FROM `salario_minimo` ORDER BY `salario_minimo`.`id_salario` DESC LIMIT 1 
sql;
      return $mysqli->queryOne($query);
    }

    public static function insertSalarioMinimo($cantidad){
      $mysqli = Database::getInstance();
      $query=<<<sql
INSERT INTO salario_minimo (id_salario, cantidad) VALUES (NULL, '$cantidad');
sql;
      return $mysqli->insert($query);
    }


}
