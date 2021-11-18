<?php
namespace App\models;
defined("APPPATH") OR die("Access denied");

use \Core\Database;
use \Core\MasterDom;
use \App\interfaces\Crud;
use \App\controllers\UtileriasLog;

class Administradores implements Crud{

    public static function getAll(){
      $mysqli = Database::getInstance();
      $query=<<<sql
SELECT a.administrador_id, a.nombre, a.usuario, a.perfil_id, a.descripcion, a.status, s.nombre AS nombre_status, p.nombre AS nombre_perfil, a.identificador, cp.nombre AS nombre_planta,
per.permisos_globales, per.seccion_empresas, per.seccion_plantas, per.seccion_horarios, per.seccion_departamentos, per.seccion_ubicaciones,
per.seccion_lectores, per.seccion_dias_festivos, per.seccion_motivo_bajas, per.seccion_incidencias, per.seccion_puestos, per.seccion_incentivos, per.seccion_colaboradores,
per.seccion_incentivosadd, per.seccion_periodo, per.seccion_registro_incidencias, per.seccion_resumen, per.seccion_prorrateo
FROM utilerias_administradores AS a
INNER JOIN utilerias_permisos AS per ON (a.usuario = per.usuario)
INNER JOIN catalogo_status AS s ON (a.status = s.catalogo_status_id)
INNER JOIN catalogo_planta AS cp USING (catalogo_planta_id)
INNER JOIN utilerias_perfiles AS p ON(a.perfil_id = p.perfil_id)
WHERE a.usuario = per.usuario AND a.status = 1
sql;
        return $mysqli->queryAll($query);
    }

    public static function getDepartamentosAdministrador($id){
      $mysqli = DataBase::getInstance();
      $query=<<<sql
SELECT cd.nombre, ua.usuario FROM catalogo_departamento AS cd INNER JOIN utilerias_administradores_departamentos AS uad ON (cd.catalogo_departamento_id = uad.catalogo_departamento_id) INNER JOIN utilerias_administradores AS ua WHERE ua.administrador_id = uad.id_administrador AND uad.id_administrador = $id
sql;
      return $mysqli->queryAll($query);
    }

    public static function insert($administradores){
	    $mysqli = Database::getInstance(1);
      $query=<<<sql
        INSERT INTO utilerias_administradores(nombre, usuario, contrasena, perfil_id, descripcion, identificador, catalogo_planta_id, status) 
        VALUES (:nombre, :usuario, :contrasena, :perfil_id, :descripcion, :identificador, :catalogo_planta_id, :status)
sql;
        $parametros = array(
          ':nombre'=>$administradores->_nombre,
          ':usuario'=>$administradores->_usuario,
          ':contrasena'=>$administradores->_contrasena,
          ':perfil_id'=>$administradores->_perfil_id,
          ':descripcion'=>$administradores->_descripcion,
          ':identificador'=>$administradores->_identificador,
          ':catalogo_planta_id'=>$administradores->_planta,
          ':status'=>$administradores->_status
        );

        $id = $mysqli->insert($query,$parametros);

        $accion = new \stdClass();
        $accion->_sql= $query;
        $accion->_parametros = $parametros;
        $accion->_id = $id;

        UtileriasLog::addAccion($accion);
        return $id;
    }

    public static function insertPermisos($permisos){

      $mysqli = Database::getInstance(1);
      $query=<<<sql
      INSERT INTO utilerias_permisos (usuario, permisos_globales, seccion_empresas, seccion_plantas, seccion_horarios, seccion_departamentos, seccion_ubicaciones, seccion_lectores, seccion_dias_festivos, seccion_motivo_bajas, seccion_incidencias, seccion_puestos, seccion_incentivos, seccion_colaboradores, seccion_incentivosadd, seccion_periodo, seccion_registro_incidencias, seccion_resumen, seccion_prorrateo) VALUES (:usuario, :permisos_globales, :seccion_empresas, :seccion_plantas, :seccion_horarios, :seccion_departamentos, :seccion_ubicaciones, :seccion_lectores, :seccion_dias_festivos, :seccion_motivo_bajas, :seccion_incidencias, :seccion_puestos, :seccion_incentivos, :seccion_colaboradores, :seccion_incentivosadd, :seccion_periodo, :seccion_registro_incidencias, :seccion_resumen, :seccion_prorrateo);
sql;
      $parametros = array(
        ':usuario'=>$permisos->_usuario,
        ':permisos_globales'=>$permisos->_permisos_globales,
        ':seccion_empresas'=>$permisos->_seccion_empresas,
        ':seccion_plantas'=>$permisos->_seccion_plantas,
        ':seccion_horarios'=>$permisos->_seccion_horarios,
        ':seccion_departamentos'=>$permisos->_seccion_departamentos,
        ':seccion_ubicaciones'=>$permisos->_seccion_ubicaciones,
        ':seccion_lectores'=>$permisos->_seccion_lectores,
        ':seccion_dias_festivos'=>$permisos->_seccion_dias_festivos,
        ':seccion_motivo_bajas'=>$permisos->_seccion_motivo_bajas,
        ':seccion_incidencias'=>$permisos->_seccion_incidencias,
        ':seccion_puestos'=>$permisos->_seccion_puestos,
        ':seccion_incentivos'=>$permisos->_seccion_incentivos,
        ':seccion_colaboradores'=>$permisos->_seccion_colaboradores,
        ':seccion_incentivosadd'=>$permisos->_seccion_Asignar_incentivos,
        ':seccion_periodo'=>$permisos->_seccion_Periodo,
        ':seccion_registro_incidencias'=>$permisos->_seccion_Registro_incidencias,
        ':seccion_resumen'=>$permisos->_seccion_Resumen,
        ':seccion_prorrateo'=>$permisos->_seccion_Prorrateo
      );

      $id = $mysqli->insert($query,$parametros);

      $accion = new \stdClass();
      $accion->_sql= $query;
      $accion->_parametros = $parametros;
      $accion->_id = $id;

      UtileriasLog::addAccion($accion);
      return $id;
    }

    public static function insertarDepartamentos($id_administrador,$id_departamento){
      $mysqli = Database::getInstance(1);
      $query=<<<sql
      INSERT INTO utilerias_administradores_departamentos (id_administrador, catalogo_departamento_id) VALUES ("$id_administrador", "$id_departamento");
sql;

      $id = $mysqli->insert($query);

      $accion = new \stdClass();
      $accion->_sql= $query;
      $accion->_parametros = $parametros;
      $accion->_id = $id;

      UtileriasLog::addAccion($accion);
      return $id;
    }

    public static function update($administradores){
      $mysqli = Database::getInstance(true);
      $query=<<<sql
      UPDATE utilerias_administradores SET perfil_id = :perfil_id, descripcion = :descripcion, identificador = :identificador, status = :status WHERE utilerias_administradores.administrador_id = :administrador_id;
sql;
      $parametros = array(
        ':administrador_id'=>$administradores->_administrador_id,
        ':perfil_id'=>$administradores->_perfil_id,
        ':descripcion'=>$administradores->_descripcion,
        ':identificador'=>$administradores->_identificador,
        ':status'=>$administradores->_status
      );
        $accion = new \stdClass();
        $accion->_sql= $query;
        $accion->_parametros = $parametros;
        $accion->_id = $administradores->_administrador_id;
        UtileriasLog::addAccion($accion);
        return $mysqli->update($query, $parametros);
    }

    public static function delete($id){
      $mysqli = Database::getInstance();
      $query = <<<sql
      UPDATE utilerias_administradores SET status = 2 WHERE utilerias_administradores.administrador_id = $id;
sql;
      $sql = $mysqli->update($query);
      $accion = new \stdClass();
      $accion->_sql= $query;
      $accion->_parametros = $parametros;
      $accion->_id = $id;
      UtileriasLog::addAccion($accion);
      return array('seccion'=>1, 'id'=>$id); // Cambia el status a eliminado
      //return $mysqli->update($query);
    }

    public static function getById($id){
      $mysqli = Database::getInstance();
      $query=<<<sql
SELECT * FROM utilerias_administradores AS ud
INNER JOIN utilerias_administradores_departamentos uad ON (uad.id_administrador = ud.administrador_id)
WHERE administrador_id = $id
sql;
      return $mysqli->queryOne($query);
    }

    public static function getByIdReporte($id){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT
        a.administrador_id,
        a.nombre,
        a.usuario,
        a.perfil_id,
        a.descripcion,
        a.status,
        c.nombre AS nombre_status,
        p.nombre AS nombre_perfil
      FROM utilerias_administradores AS a
      INNER JOIN catalogo_status As c
      INNER JOIN utilerias_perfiles As p ON ( p.perfil_id = a.perfil_id)
      WHERE c.catalogo_status_id = a.status  AND a.status != 2 AND a.administrador_id = $id
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

    public static function getPerfiles(){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT * FROM utilerias_perfiles WHERE status != 2
sql;
      return $mysqli->queryAll($query);
    }

    public static function getPerfil(){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT * FROM utilerias_perfiles
sql;
      return $mysqli->queryAll($query);
    }

    public static function getUser($user){
      $mysqli = Database::getInstance();
      $query =<<<sql
      SELECT * FROM utilerias_administradores WHERE usuario LIKE '$user'
sql;
      $dato = $mysqli->queryOne($query);
      return ($dato>=1) ? 1 : 2 ;
    }

    public static function getUserDatos($user){
      $mysqli = Database::getInstance();
      $query =<<<sql
      SELECT a.administrador_id, a.nombre, a.usuario, a.descripcion, p.nombre AS nombre_perfil FROM utilerias_administradores AS a INNER JOIN utilerias_perfiles As p ON (a.perfil_id = p.perfil_id) WHERE a.usuario = "$user" 
sql;
      return $mysqli->queryOne($query);
    }

    public static function getSeccionesMenu(){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT * FROM utilerias_secciones ORDER BY utilerias_seccion_id
sql;
      return $mysqli->queryAll($query);
    }

    public static function getPermisosByUser($usuario){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT * FROM utilerias_permisos WHERE usuario = '$usuario'
sql;
      return $mysqli->queryOne($query);
    }

    public static function updateDataAdministrador($administrador){
      $mysqli = Database::getInstance();
      $query=<<<sql
      UPDATE utilerias_administradores SET perfil_id = :perfil_id, descripcion = :descripcion, identificador = :identificador, catalogo_planta_id = :catalogo_planta_id, status = :status WHERE usuario = :usuario;
sql;
      $parametros = array(
        ':usuario'=>$administrador->_usuario,
        ':perfil_id'=>$administrador->_perfil_id,
        ':descripcion'=>$administrador->_descripcion,
        ':catalogo_planta_id'=>$administrador->_planta,
        ':identificador'=>$administrador->_identificador,
        ':status'=>$administrador->_status
      );
      
      $accion = new \stdClass();
      $accion->_sql= $query;
      $accion->_parametros = $parametros;
      $accion->_id = $administrador->_administrador_id;
      UtileriasLog::addAccion($accion);
      return $mysqli->update($query, $parametros);
    }

    public static function updatePermisosUsuario($permisos){
      $mysqli = Database::getInstance();
      $query=<<<sql
      UPDATE utilerias_permisos SET permisos_globales = :permisos_globales, seccion_empresas = :seccion_empresas, seccion_plantas = :seccion_plantas, seccion_horarios = :seccion_horarios, seccion_departamentos = :seccion_departamentos, seccion_ubicaciones = :seccion_ubicaciones, seccion_lectores = :seccion_lectores, seccion_dias_festivos = :seccion_dias_festivos, seccion_motivo_bajas = :seccion_motivo_bajas, seccion_incidencias = :seccion_incidencias, seccion_puestos = :seccion_puestos, seccion_incentivos = :seccion_incentivos, seccion_colaboradores = :seccion_colaboradores, seccion_incentivosadd = :seccion_incentivosadd, seccion_periodo = :seccion_periodo, seccion_registro_incidencias = :seccion_registro_incidencias, seccion_resumen = :seccion_resumen,   seccion_prorrateo = :seccion_prorrateo WHERE utilerias_permisos.usuario = :usuario;
sql;
      $parametros = array(
        ':usuario'=>$permisos->_usuario,
        ':permisos_globales'=>$permisos->_permisos_globales,
        ':seccion_empresas'=>$permisos->_seccion_empresas,
        ':seccion_plantas'=>$permisos->_seccion_plantas,
        ':seccion_horarios'=>$permisos->_seccion_horarios,
        ':seccion_departamentos'=>$permisos->_seccion_departamentos,
        ':seccion_ubicaciones'=>$permisos->_seccion_ubicaciones,
        ':seccion_lectores'=>$permisos->_seccion_lectores,
        ':seccion_dias_festivos'=>$permisos->_seccion_dias_festivos,
        ':seccion_motivo_bajas'=>$permisos->_seccion_motivo_bajas,
        ':seccion_incidencias'=>$permisos->_seccion_incidencias,
        ':seccion_puestos'=>$permisos->_seccion_puestos,
        ':seccion_incentivos'=>$permisos->_seccion_incentivos,
        ':seccion_colaboradores'=>$permisos->_seccion_colaboradores,
        ':seccion_incentivosadd'=>$permisos->_seccion_Asignar_incentivos,
        ':seccion_periodo'=>$permisos->_seccion_Periodo,
        ':seccion_registro_incidencias'=>$permisos->_seccion_Registro_incidencias,
        ':seccion_resumen'=>$permisos->_seccion_Resumen,
        ':seccion_prorrateo'=>$permisos->_seccion_Prorrateo
      );
        $accion = new \stdClass();
        $accion->_sql= $query;
        $accion->_parametros = $parametros;
        $accion->_id = $administradores->_administrador_id;
        UtileriasLog::addAccion($accion);
        return $mysqli->update($query, $parametros);
        
    
    }

    public static function getPwdActual($pwd){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT * FROM utilerias_administradores WHERE contrasena = '$pwd' 
sql;
      return $mysqli->queryOne($query);
    }

    public static function updateNuevaContrasenia($pwd){
      $mysqli = Database::getInstance();
      $query=<<<sql
      UPDATE utilerias_administradores SET contrasena = MD5(:pwd) WHERE utilerias_administradores.usuario = :usuario;
sql;
      $parametros = array(
        ':usuario'=>$pwd->_usuario,
        ':pwd'=>$pwd->_pwd_nueva
      );
      
      return $mysqli->update($query, $parametros);
    
    }

    public static function getDepartamentos(){
      $mysqli = Database::getInstance();
      $query=<<<sql
      SELECT * FROM catalogo_departamento WHERE status != 2 ORDER BY catalogo_departamento.catalogo_departamento_id ASC 
sql;
      return $mysqli->queryAll($query);
    }

    public static function getDepartamentosById($id){
      $mysqli = Database::getInstance();
      $query =<<<sql
      SELECT 
        d.catalogo_departamento_id,
        d.nombre
      FROM utilerias_administradores_departamentos uad
      JOIN catalogo_departamento d 
      ON d.catalogo_departamento_id = uad.catalogo_departamento_id
      JOIN utilerias_administradores a
      ON a.administrador_id = uad.id_administrador
      where a.administrador_id = $id
sql;
      return $mysqli->queryAll($query);
    }

    public static function deleteDepartamentosId($id){
      $mysqli = Database::getInstance();
      $query =<<<sql
      DELETE FROM 
        utilerias_administradores_departamentos WHERE 
      id_administrador = $id
sql;
      return $mysqli->update($query);
    }

    public static function deleteDepartamento($id_administrador){
      $mysqli = Database::getInstance();
      $query =<<<sql
      DELETE FROM utilerias_administradores_departamentos WHERE id_administrador = "$id_administrador"
sql;
      return $mysqli->update($query);
    }

    public static function getPlantas(){
      $mysqli = Database::getInstance();
      $query =<<<sql
      SELECT * FROM catalogo_planta WHERE status = 1
sql;
      return $mysqli->queryAll($query);
    }
}
