<?php
namespace App\controllers;
defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \Core\Controller;
use \App\models\Incidencia as IncidenciaDao;
use \App\models\General AS GeneralDao;
use \App\models\Colaboradores AS ColaboradoresDao;
use \App\models\AdminIncidencia AS AdminIncidenciaDao;
use \App\models\Checador AS ChecadorDao;

class Checador extends Controller{

    function __construct(){
    parent::__construct();
    $this->_contenedor = new Contenedor;
    View::set('header',$this->_contenedor->header());
    View::set('footer',$this->_contenedor->footer());
  }

  public function index(){}

  public function getFiltro($post){
    $datos = array();
    $datos['cc.catalogo_empresa_id'] = MasterDom::getData('catalogo_empresa_id');
    $datos['cc.catalogo_ubicacion_id'] = MasterDom::getData('catalogo_ubicacion_id');
    $datos['cc.catalogo_departamento_id'] = MasterDom::getData('catalogo_departamento_id');
    $datos['cc.catalogo_puesto_id'] = MasterDom::getData('catalogo_puesto_id');
    $datos['cc.identificador_noi'] = (!empty(MasterDom::getData('status'))) ? MasterDom::getData('status') : "";

    $filtro = '';
    foreach ($datos as $key => $value) {
      if($value!=''){
        if($key == 'c.pago') $filtro .= "AND {$key} = '$value' ";
        else $filtro .= "AND {$key} = '$value' ";
      }
    }
    return $datos;
  }

  public function getTabla($tipo, $idPeriodo, $vista, $perfilUsuario, $catalogDepartamentoId, $catalogoPlantaId, $estatusRH, $nombrePlanta){

    $filtros = "";
    if($_POST != "")
      $filtros = $this->getFiltro($post);


      $tabla = "";
      $periodo = IncidenciaDao::searchPeriodoById($idPeriodo);
      $hidden = ($periodo['status'] == 0)? '': ' hidden ';
      foreach (GeneralDao::getColaboradores($tipo, $perfilUsuario, $catalogDepartamentoId, $catalogoPlantaId, $estatusRH, $nombrePlanta, $filtros) as $key => $value) {
      //echo "<pre>"; print_r($value); echo "</pre>";
      $value['apellido_materno'] = utf8_encode($value['apellido_materno']);
      $value['apellido_paterno'] = utf8_encode($value['apellido_paterno']);
      $value['nombre'] = utf8_encode($value['nombre']);
        $tabla .=<<<html
        <tr>
          <td style="text-align:center; vertical-align:middle;"><img class="foto" src="/img/colaboradores/{$value['foto']}"/></td>
          <td style="text-align:center; vertical-align:middle;">{$value['nombre']} <br>{$value['apellido_paterno']}<br> {$value['apellido_materno']}</td>
          <td style="text-align:center; vertical-align:middle;">
            <b>Depmt</b>: {$value['nombre_departamento']} <br>
            <b>Puesto</b>: {$value['nombre_puesto']} <br>
                  <b>Ubicaci&oacute;n</b>: {$value['nombre_ubicacion']} <br>
            <b>TIPO</b>: {$value['pago']}
          </td>
          <td style="text-align:center; vertical-align:middle;">{$value['nombre_empresa']}</td>
          <td style="text-align:center; vertical-align:middle;">{$value['numero_empleado']}</td>
          <td style="text-align:center; vertical-align:middle;">
            <a href="/Checador/checadorFechas/{$value['catalogo_colaboradores_id']}/{$idPeriodo}/{$vista}/{$accion}" type="submit" name="id_empresa" class="btn btn-primary"><i class="fa fa-plus"></i> </a>
          </td>
        </tr>
html;
      }
      View::set('hidden', $hidden);
      return $tabla;
    }

  public function semanales(){
      $user = GeneralDao::getDatosUsuario($this->__usuario);
      
      if($user['perfil_id'] == 6){
      $val = ($user['catalogo_planta_id'] == 1) ? 1 : 5;
          $tituloVista = "Checador propias <b>" . strtoupper($user['nombre_planta']) . "</b> - depto. <b>" . strtoupper($user['nombre']) . "</b>";
      }else{
          if($user['perfil_id'] == 6) // Si el usuario es de RH
            $tituloVista = "Checador de Recursos Humanos - Planta " . strtolower($user['nombre_planta']);

          if($user['perfil_id'] == 4 || $user['perfil_id'] == 5 ){ // Si el usuario es de perfil personalizado o admin
            $tituloVista = "Checador del depto. " . strtolower($user['nombre']);
            if($user['perfil_id'] == 4){
                $val = 10;
            }else{
                $val = 11;
            }
        }

          if($user['perfil_id'] == 1 ){ // Si el usuario es root
            $val = 6;
            $tituloVista = "Checador de TODAS LAS PLANTAS ";
          }
    }

    // INCLUCION DE FILTROS PARA LA TABLA
    View::set('nomina',$this->getNominas());
    View::set('idPuesto',$this->getPuestos());
    View::set('idEmpresa',$this->getEmpresas());
    View::set('idUbicacion',$this->getUbicacion());
    View::set('idDepartamento',$this->getDepartamentos());
    // TERMINO DE FILTROS DE LA TABLA
    

    $idPeriodo = $this->getIdPeriodo("SEMANAL", 0);
    View::set('msjPeriodo',$this->getPeriodo("SEMANAL", 0));
    View::set('tipoPeriodo',$tituloVista); // Identificacion del periodo
    View::set('tituloIncentivos',"Semanales");
    View::set('tabla',$this->getTabla("Semanal",$idPeriodo,"semanales", $user['perfil_id'], $user['catalogo_departamento_id'], $user['catalogo_planta_id'], $val, $user['nombre_planta'])); // Obtener la tabla
    View::set('form',"/Checador/semanales/");
    View::set('header',$this->_contenedor->header($this->getHeader()));
    View::set('footer',$this->_contenedor->footer($this->getFooter()));
    View::render("checador_abierta");
  }

  public function quincenales(){
      $user = GeneralDao::getDatosUsuario($this->__usuario);
      
    if($user['perfil_id'] == 6){
          $val = ($user['catalogo_planta_id'] == 1) ? 1 : 5;
          $tituloVista = "Checador propios <b>" . strtoupper($user['nombre_planta']) . "</b> - depto. <b>" . strtoupper($user['nombre']) . "</b>";
    }else{
      if($user['perfil_id'] == 6) // Si el usuario es de RH
        $tituloVista = "Checador de Recursos Humanos - Planta " . strtolower($user['nombre_planta']);

      if($user['perfil_id'] == 4 || $user['perfil_id'] == 5 ){ // Si el usuario es de perfil personalizado o admin
        $tituloVista = "Checador del depto. " . strtolower($user['nombre']);
        if($user['perfil_id'] == 4){
              $val = 10;
            }else{
              $val = 11;
            }
      }

      if($user['perfil_id'] == 1 ) {// Si el usuario es root
        $tituloVista = "Checador de TODAS LAS PLANTAS Periodo Abierto";
        $val = 6;
      }
    }

    // INCLUCION DE FILTROS PARA LA TABLA
    View::set('nomina',$this->getNominas());
    View::set('idPuesto',$this->getPuestos());
    View::set('idEmpresa',$this->getEmpresas());
    View::set('idUbicacion',$this->getUbicacion());
    View::set('idDepartamento',$this->getDepartamentos());
    // TERMINO DE FILTROS DE LA TABLA
    
    $idPeriodo = $this->getIdPeriodo("QUINCENAL", 0);
    View::set('msjPeriodo',$this->getPeriodo("QUINCENAL", 0));
    View::set('tipoPeriodo',$tituloVista); // Identificacion del periodo
    View::set('tituloIncentivos',"Quincenales");
    View::set('tabla',$this->getTabla("Quincenal",$idPeriodo,"quincenales", $user['perfil_id'], $user['catalogo_departamento_id'], $user['catalogo_planta_id'], $val, $user['nombre_planta'])); // Obtener la tabla
    View::set('form',"/Checador/quincenales/");
    View::set('header',$this->_contenedor->header($this->getHeader()));
    View::set('footer',$this->_contenedor->footer($this->getFooter()));
    View::render("checador_abierta");
  }

  public function historicosSemanales(){
      $user = GeneralDao::getDatosUsuario($this->__usuario);

      if(empty($_POST)){
        $ultimoPeriodoHistorico = IncidenciaDao::getUltimoPeriodoHistorico("SEMANAL");
        $idPeriodo = $ultimoPeriodoHistorico['prorrateo_periodo_id'];
      }else{
        $idPeriodo = MasterDom::getData('tipo_periodo');
      }

      if($user['perfil_id'] == 6){
          $val = ($user['catalogo_planta_id'] == 1) ? 1 : 5;
          $tituloVista = "Checador propios <b>" . strtoupper($user['nombre_planta']) . "</b> - depto. <b>" . strtoupper($user['nombre']) . "</b>";
      }else{
        if($user['perfil_id'] == 6) // Si el usuario es de RH
          $tituloVista = "TODOS los Incidencias de Recursos Humanos - Planta " . strtolower($user['nombre_planta']);

          if($user['perfil_id'] == 4 || $user['perfil_id'] == 5 ){ // Si el usuario es de perfil personalizado o admin
            $tituloVista = "TODOS los Incidencias del depto. " . strtolower($user['nombre']);
            if($user['perfil_id'] == 4){
                $val = 10;
            }else{
                $val = 11;
            }
          }

        if($user['perfil_id'] == 1 ){ // Si el usuario es root
          $tituloVista = "TODOS los Incidencias de TODAS LAS PLANTAS ";
          $val = 6;
        }
      }

      // INCLUCION DE FILTROS PARA LA TABLA
    View::set('nomina',$this->getNominas());
    View::set('idPuesto',$this->getPuestos());
    View::set('idEmpresa',$this->getEmpresas());
    View::set('idUbicacion',$this->getUbicacion());
    View::set('idDepartamento',$this->getDepartamentos());
    // TERMINO DE FILTROS DE LA TABLA
        
        View::set('tipo_periodo',$idPeriodo); 
      View::set('tipoPeriodo',$tituloVista); // Identificacion del periodo
      View::set('tituloIncentivos',"Semanales");
      View::set('option',$this->getPeriodosHistoricos("SEMANAL",$idPeriodo)); // Optiene todos los periodos procesados(historicos) semanales

      View::set('tabla',$this->getTabla("Semanal",$idPeriodo,"historicosSemanales", $user['perfil_id'], $user['catalogo_departamento_id'], $user['catalogo_planta_id'], $val, $user['nombre_planta'])); // Obtener la tabla
      View::set('busqueda',"/Checador/historicosSemanales/");
      View::set('header',$this->_contenedor->header($this->getHeader()));
      View::set('footer',$this->_contenedor->footer($this->getFooter()));
      View::render("checador_historico");
  }

  public function historicosQuincenales(){
    $user = GeneralDao::getDatosUsuario($this->__usuario);

    if(empty($_POST)){
      $ultimoPeriodoHistorico = IncidenciaDao::getUltimoPeriodoHistorico("QUINCENAL");
      $idPeriodo = $ultimoPeriodoHistorico['prorrateo_periodo_id'];
    }else{
      $idPeriodo = MasterDom::getData('tipo_periodo');
    }

    if($user['perfil_id'] == 6){
      $val = ($user['catalogo_planta_id'] == 1) ? 1 : 5;
      $tituloVista = "Checador propios <b>" . strtoupper($user['nombre_planta']) . "</b> - depto. <b>" . strtoupper($user['nombre']) . "</b>";
    }else{
      if($user['perfil_id'] == 6) // Si el usuario es de RH
        $tituloVista = "TODOS Recursos Humanos - Planta " . strtolower($user['nombre_planta']);

      if($user['perfil_id'] == 4 || $user['perfil_id'] == 5 ){ // Si el usuario es de perfil personalizado o admin
        $tituloVista = "TODOSl depto. " . strtolower($user['nombre']);
        if($user['perfil_id'] == 4){
              $val = 10;
            }else{
              $val = 11;
            }
      }

      if($user['perfil_id'] == 1 ){ // Si el usuario es root
        $tituloVista = "TODOS TODAS LAS PLANTAS ";
        $val = 6;
      }
    }

    // INCLUCION DE FILTROS PARA LA TABLA
    View::set('nomina',$this->getNominas());
    View::set('idPuesto',$this->getPuestos());
    View::set('idEmpresa',$this->getEmpresas());
    View::set('idUbicacion',$this->getUbicacion());
    View::set('idDepartamento',$this->getDepartamentos());
    // TERMINO DE FILTROS DE LA TABLA

    View::set('tipo_periodo',$idPeriodo); 
    View::set('tipoPeriodo',$tituloVista); // Identificacion del periodo
    View::set('tituloIncentivos',"Quincenales");
    View::set('option',$this->getPeriodosHistoricos("QUINCENAL")); // Optiene todos los periodos procesados(historicos) semanales
    View::set('tabla',$this->getTabla("Quincenal",$idPeriodo,"historicosQuincenales", $user['perfil_id'], $user['catalogo_departamento_id'], $user['catalogo_planta_id'], $val, $user['nombre_planta'])); // Obtener la tabla
    View::set('form',"/Checador/historicosQuincenales/");
    View::set('header',$this->_contenedor->header($this->getHeader()));
    View::set('footer',$this->_contenedor->footer($this->getFooter()));
    View::render("checador_historico");
  }


  public function propiosSemanalesHistoricos(){
    $user = GeneralDao::getDatosUsuario($this->__usuario);

    if(empty($_POST)){
      $ultimoPeriodoHistorico = IncidenciaDao::getUltimoPeriodoHistorico("SEMANAL");
      $idPeriodo = $ultimoPeriodoHistorico['prorrateo_periodo_id'];
    }else{
      $idPeriodo = MasterDom::getData('tipo_periodo');
    }

    if($user['perfil_id'] == 6){ // Si el usuario es de RH
      $tituloVista = "Checador - Planta " . strtolower($user['nombre_planta']) . " - Depto. " . $user['nombre'];
      $val = ($user['catalogo_planta_id'] == 1) ? 1 : 4;
    }

    if($user['perfil_id'] == 1){ // Si el usuario es de RH
      $tituloVista = "Checador - Planta " . strtolower($user['nombre_planta']) . " - Depto. " . $user['nombre'];
      $val = 4; // TIENE INCENTIVOS PROPIOS
    }


    // INCLUCION DE FILTROS PARA LA TABLA
    View::set('nomina',$this->getNominas());
    View::set('idPuesto',$this->getPuestos());
    View::set('idEmpresa',$this->getEmpresas());
    View::set('idUbicacion',$this->getUbicacion());
    View::set('idDepartamento',$this->getDepartamentos());
    // TERMINO DE FILTROS DE LA TABLA

        View::set('tipo_periodo',$idPeriodo); 
    View::set('tipoPeriodo',$tituloVista); // Identificacion del periodo
    View::set('tituloIncentivos',"Semanales");
    View::set('option',$this->getPeriodosHistoricos("SEMANAL",$idPeriodo)); // Optiene todos los periodos procesados(historicos) semanales
    View::set('tabla',$this->getTabla("Semanal",$idPeriodo,"propiosSemanalesHistoricos", $user['perfil_id'], $user['catalogo_departamento_id'], $user['catalogo_planta_id'], $val, $user['nombre_planta'] )); // Obtener la tabla
    View::set('busqueda',"/Checador/propiosSemanalesHistoricos/");
    View::set('header',$this->_contenedor->header($this->getHeader()));
    View::set('footer',$this->_contenedor->footer($this->getFooter()));
    View::render("checador_historico");
  }

  public function propiosQuincenalesHistoricos(){
    $user = GeneralDao::getDatosUsuario($this->__usuario);

    if(empty($_POST)){
      $ultimoPeriodoHistorico = IncidenciaDao::getUltimoPeriodoHistorico("QUINCENAL");
      $idPeriodo = $ultimoPeriodoHistorico['prorrateo_periodo_id'];
    }else{
      $idPeriodo = MasterDom::getData('tipo_periodo');
    }

    if($user['perfil_id'] == 6){ // Si el usuario es de RH
      $tituloVista = "Checador de Recursos Humanos - Planta " . strtolower($user['nombre_planta']) . " - Depto. " . $user['nombre'];
      $val = ($user['catalogo_planta_id'] == 1) ? 1 : 4;
    }

    if($user['perfil_id'] == 1){ // Si el usuario es de RH
      $tituloVista = "Checador de ROOT - Planta " . strtolower($user['nombre_planta']) . " - Depto. " . $user['nombre'];
      $estatusRH = ($user['perfil_id'] == 1) ? 2 : 0;
      $val = 4; // TIENE INCENTIVOS PROPIOS
    }

    // INCLUCION DE FILTROS PARA LA TABLA
    View::set('nomina',$this->getNominas());
    View::set('idPuesto',$this->getPuestos());
    View::set('idEmpresa',$this->getEmpresas());
    View::set('idUbicacion',$this->getUbicacion());
    View::set('idDepartamento',$this->getDepartamentos());
    // TERMINO DE FILTROS DE LA TABLA

    View::set('tipo_periodo',$idPeriodo); 
    View::set('tipoPeriodo',$tituloVista); // Identificacion del periodo
    View::set('tituloIncentivos',"Quincenal");
    View::set('option',$this->getPeriodosHistoricos("QUINCENAL",$idPeriodo)); // Optiene todos los periodos procesados(historicos) semanales
    View::set('tabla',$this->getTabla("Quincenal",$idPeriodo,"propiosQuincenalesHistoricos", $user['perfil_id'], $user['catalogo_departamento_id'], $user['catalogo_planta_id'], $val, $user['nombre_planta'] )); // Obtener la tabla
    View::set('busqueda',"/Checador/propiosQuincenalesHistoricos/");
    View::set('form',"/Checador/propiosQuincenalesHistoricos/");
    View::set('header',$this->_contenedor->header($this->getHeader()));
    View::set('footer',$this->_contenedor->footer($this->getFooter()));
    View::render("checador_historico");
  }

  public function propiosSemanales(){

    $user = GeneralDao::getDatosUsuario($this->__usuario);

    if($user['perfil_id'] == 6){ // Si el usuario es de RH
      $tituloVista = "Checador de Recursos Humanos - Planta " . strtolower($user['nombre_planta']) . " - Depto. " . $user['nombre'];
      //$val = 3; // TIENE INCENTIVOS PROPIOS
      $val = ($user['catalogo_planta_id'] == 1) ? 1 : 4;
    }

    if($user['perfil_id'] == 1){ // Si el usuario es de RH
      $tituloVista = "Checador de ROOT - Planta " . strtolower($user['nombre_planta']) . " - Depto. " . $user['nombre'];
      $val = 4; // TIENE INCENTIVOS PROPIOS
    }

    // INCLUCION DE FILTROS PARA LA TABLA
    View::set('nomina',$this->getNominas());
    View::set('idPuesto',$this->getPuestos());
    View::set('idEmpresa',$this->getEmpresas());
    View::set('idUbicacion',$this->getUbicacion());
    View::set('idDepartamento',$this->getDepartamentos());
    // TERMINO DE FILTROS DE LA TABLA
      
    $idPeriodo = $this->getIdPeriodo("SEMANAL", 0);
    View::set('msjPeriodo',$this->getPeriodo("SEMANAL", 0));
    View::set('tituloIncentivos',"Semanales");
    View::set('tipoPeriodo',$tituloVista); // Identificacion del periodo
    View::set('tabla',$this->getTabla("Semanal",$idPeriodo,"propiosSemanales",$user['perfil_id'], $user['catalogo_departamento_id'], $user['catalogo_planta_id'], $val, $user['nombre_planta'] )); // Obtener la tabla
    View::set('form',"/Checador/propiosSemanales/");
    View::set('header',$this->_contenedor->header($this->getHeader()));
    View::set('footer',$this->_contenedor->footer($this->getFooter()));
    View::render("checador_abierta");
    }

  public function propiosQuincenales(){
    
    $user = GeneralDao::getDatosUsuario($this->__usuario);

    if($user['perfil_id'] == 6){ // Si el usuario es de RH
      $tituloVista = "Checador propias de Recursos Humanos - Planta " . strtolower($user['nombre_planta']) . " - Depto. " . $user['nombre'];
      $val = ($user['catalogo_planta_id'] == 1) ? 1 : 4;
    }

    if($user['perfil_id'] == 1){ // Si el usuario es de RH
      $tituloVista = "Checador propias - Planta " . strtolower($user['nombre_planta']) . " - Depto. " . $user['nombre'];
      $estatusRH = ($user['perfil_id'] == 1) ? 2 : 0;
      $val = 4; // TIENE INCENTIVOS PROPIOS
    }

    // INCLUCION DE FILTROS PARA LA TABLA
    View::set('nomina',$this->getNominas());
    View::set('idPuesto',$this->getPuestos());
    View::set('idEmpresa',$this->getEmpresas());
    View::set('idUbicacion',$this->getUbicacion());
    View::set('idDepartamento',$this->getDepartamentos());
    // TERMINO DE FILTROS DE LA TABLA

    $idPeriodo = $this->getIdPeriodo("QUINCENAL", 0);
    View::set('msjPeriodo',$this->getPeriodo("QUINCENAL", 0));
    View::set('tituloIncentivos',"Quincenales");
    View::set('tipoPeriodo',$tituloVista); // Identificacion del periodo
    View::set('tabla',$this->getTabla("Quincenal",$idPeriodo,"propiosQuincenales",$user['perfil_id'], $user['catalogo_departamento_id'], $user['catalogo_planta_id'], $val, $user['nombre_planta'] )); // Obtener la tabla
    View::set('form',"/Checador/propiosQuincenales/");
    View::set('header',$this->_contenedor->header($this->getHeader()));
    View::set('footer',$this->_contenedor->footer($this->getFooter()));
    View::render("checador_abierta");
  }
  /*
      @$tipo -> SEMANAL o QUINCENAL
    @status -> 1 Abierto y 0 Cerrado
  */
    public function getPeriodo($tipo, $status){
      $periodo = IncidenciaDao::searchPeriodos($tipo, $status);
      if(empty($periodo[0])){
        View::set('error',"Error Periodo");
        View::set('tituloError',"Al parecer no hay periodo Abierto");
        View::set('mensajeError',"Debe existir un periodo Abierto, para checar los incentivos");
        $display = "style=\"display:none;\" ";
        View::set('visualizar', $display);
        View::render("error");
        exit;
      }else{
        $fechaIni = MasterDom::getFecha($periodo[0]['fecha_inicio']);
        $fechaFin = MasterDom::getFecha($periodo[0]['fecha_fin']);
        $status = ($periodo[0]['status'] == 0) ? "Abierto": "Cerrado";
        $label = ($periodo[0]['status'] == 0) ? "success": "danger";
      $htmlPeriodo = <<<html
      <b>( {$fechaIni} al {$fechaFin} )</b> <label class="label label-{$label}"> periodo {$status}</label>
html;
      } 
      return $htmlPeriodo;
    }

    /*
      @$tipo -> SEMANAL o QUINCENAL
    @status -> 1 Abierto y 0 Cerrado
  */
    public function getPeriodoProcesado($tipo, $idPeriodo){
      $periodo = IncidenciaDao::searchPeriodoProcesado($tipo, $idPeriodo);
      $status = ($periodo[0]['status'] == 0) ? "Abierto": "Cerrado";

    $fechaIni = MasterDom::getFecha($periodo[0]['fecha_inicio']);
    $fechaFin = MasterDom::getFecha($periodo[0]['fecha_fin']);
    $htmlPeriodo = <<<html
      <b>( {$fechaIni} al {$fechaFin} )</b> <small> periodo {$status}</small>
html;
      return $htmlPeriodo;
    }

    /*
      @$idPeriodo -> id del periodo
  */
    public function getDataPeriodoById($idPeriodo){
      $periodo = IncidenciaDao::searchPeriodoById($idPeriodo);
      $status = ($periodo['status'] == 0) ? "Abierto": "Cerrado";

    $fechaIni = MasterDom::getFecha($periodo['fecha_inicio']);
    $fechaFin = MasterDom::getFecha($periodo['fecha_fin']);
    $htmlPeriodo = <<<html
      <b>( {$fechaIni} al {$fechaFin} )</b> <small> periodo {$status}</small>
html;
      return $htmlPeriodo;
    }

    /*
      @$tipo -> SEMANAL o QUINCENAL
    @status -> 1 Abierto y 0 Cerrado
  */
    public function getIdPeriodo($tipo, $status){
    $periodo = IncidenciaDao::searchPeriodos($tipo, $status);
    return $periodo[0]['prorrateo_periodo_id'];
    }

    /*
    Obtiene los incentivos SEMANALES O QUINCENALES, que ya han sido procesados
    */
    public function getPeriodosHistoricos($tipo, $periodoObtenido){
      $periodos = IncidenciaDao::getTipoPeriodo($tipo,1);
      $option = "";
      foreach ($periodos as $key => $value) {
        $selected = ($value['prorrateo_periodo_id'] == $periodoObtenido) ? "selected" : "";
      $fechaIni = MasterDom::getFecha($value['fecha_inicio']);
      $fechaFin = MasterDom::getFecha($value['fecha_fin']);
      $option .=<<<html
        <option {$selected} value="{$value['prorrateo_periodo_id']}">({$fechaIni}) al ({$fechaFin})</option>
html;
      }
      return $option;
    }

    /*
    Obtiene toda los colaboradores de la granja
    */
  public function getTabla1($tipo, $idPeriodo, $vista, $perfilUsuario, $catalogDepartamentoId, $catalogoPlantaId, $estatusRH, $nombrePlanta){

    $direccionamiento = $this->direccionamiento(MasterDom::getData('action'));
    $direccionamiento = "checadorFechas";

    $tabla = "";
    foreach (GeneralDao::getColaboradores($tipo, $perfilUsuario, $catalogDepartamentoId, $catalogoPlantaId, $estatusRH, $nombrePlanta) as $key => $value) {
      $tabla .=<<<html
        <tr>
          <td style="text-align:center; vertical-align:middle;"><img class="foto" src="/img/colaboradores/{$value['foto']}"/></td>
          <td style="text-align:center; vertical-align:middle;">{$value['nombre']} <br>{$value['apellido_paterno']}<br> {$value['apellido_materno']}</td>
          <td style="text-align:center; vertical-align:middle;">
            <b>Depmt</b>: {$value['nombre_departamento']} <br>
            <b>Puesto</b>: {$value['nombre_puesto']} <br>
            <b>Ubicaion</b>: {$value['nombre_ubicacion']}
          </td>
          <td style="text-align:center; vertical-align:middle;">{$value['nombre_empresa']}</td>
          <td style="text-align:center; vertical-align:middle;">{$value['numero_empleado']}</td>
          <td style="text-align:center; vertical-align:middle;">
            <a href="/Checador/{$direccionamiento}/{$value['catalogo_colaboradores_id']}/{$idPeriodo}/{$vista}/{$accion}" type="submit" name="id_empresa" class="btn btn-primary"><i class="fa fa-plus"></i> </a>
          </td>
        </tr>
html;
      }
      return $tabla;
    }

  public function checadorFechas($idColaborador,$idPeriodo,$vista,$accion){
    $colaborador = IncidenciaDao::getById($idColaborador);
    $periodo = IncidenciaDao::getPeriodoById($idPeriodo);
    $colaborador_id = $idColaborador;

    $estatusPeriodo = $periodo['status'];
    $fecha_inicio = new \DateTime($periodo['fecha_inicio']);
    $fecha_final = new \DateTime($periodo['fecha_fin']);
    $dias = array('Monday'=>'Lunes','Tuesday'=>'Martes','Wednesday'=>'Miercoles','Thursday'=>'Jueves','Friday'=>'Viernes','Saturday'=>'Sabado','Sunday'=>'Domingo');

    $incidencias_colaborador = IncidenciaDao::getProrrateoColaboradorIncidenciaById($colaborador['catalogo_colaboradores_id']);
    $datos = new \stdClass();
    $datos->numero_empleado = $colaborador['numero_identificador'];
    $datos->catalogo_colaboradores_id = $colaborador['catalogo_colaboradores_id'];    
    $datos->catalogo_lector_id = $colaborador['catalogo_lector_id'];

    $tabla =<<<html
      <table class="table table-striped table-bordered table-hover" id="muestra-colaboradores">
        <thead>
          <tr>
            <th>Dia</th>
            <th>Fecha</th>
            <th>Entrada</th>
            <th>Salida</th>
            <th>Entrada Registrada</th>
          </tr>
        </thead>
        <tbody id="registros">
html;

        $dia_aux = '';
        $ultimo_horario = IncidenciaDao::getUltimoHorario($datos);
        $horarios = IncidenciaDao::getHorariosById($datos);
      $num_semana = 0;
      while($fecha_inicio <= $fecha_final){
        
        $value = '';

        /******************************************************************************************************************************************************/
        if($colaborador['horario_tipo'] == 'semanal'){
        $nombre_dia_semana = $dias[$fecha_inicio->format('l')];

            if( $num_semana != $fecha_inicio->format('W') ){
                $num_semana = $fecha_inicio->format('W');
                //echo "Numero de semana:::$num_semana<br>";
                $catalogo_horario_id_anterior = $catalogo_horario_id;
                for($llave=0; $llave<count($horarios); $llave++) {
                    $valor = $horarios[$llave];
                    
                    if(count($ultimo_horario) > 0){
                        if($valor['catalogo_horario_id'] == $ultimo_horario['catalogo_horario_id']){
                          if( ($llave+1) >= count($horarios) ){
                            $catalogo_horario_id = $horarios[0]['catalogo_horario_id'];
                          }else{
                            $catalogo_horario_id = $horarios[intval($llave)+1]['catalogo_horario_id'];
                          }
                        }
                    }else{
                      if($ultimo_horario == ''){
                        $catalogo_horario_id = $horarios[0]['catalogo_horario_id'];
                      }elseif($catalogo_horario_id == 0){
                            //echo "Catalogo horario id: $catalogo_horario_id:::::::::";
                            $catalogo_horario_id = $horarios[0]['catalogo_horario_id'];
                            //echo ">$catalogo_horario_id<br><br>";
                        }else{
                            //echo "Catalogo_horario_id = $catalogo_horario_id:::::::::";
                            if($valor['catalogo_horario_id'] == $catalogo_horario_id){
                              if( ($llave+1) >= count($horarios) ){
                                $catalogo_horario_id = $horarios[0]['catalogo_horario_id'];
                              }else{
                                $catalogo_horario_id = $horarios[intval($llave)+1]['catalogo_horario_id'];
                              }
                            }
                        }
                    }
                    if($catalogo_horario_id != $catalogo_horario_id_anterior){break;}
                }
            }
            
        }
        $datos->catalogo_horario_id = $catalogo_horario_id;
        /******************************************************************************************************************************************************/

        foreach (IncidenciaDao::getHorarioLaboralById($datos) as $llave => $valor) {
          if($valor['dia_semana'] == $dias[$fecha_inicio->format('l')]){
        if($colaborador['horario_tipo'] == 'semanal'){
          $value = $valor;
          break;
        }elseif ($colaborador['horario_tipo'] == 'diario') {
          if($valor['dia_semana'] == $dias[$fecha_inicio->format('l')]){
            $value = $valor;
            break;
          }
        }
          }
    }

/***********************************SI NO ES DIA DE TRABAJO VERIFICA QUE NO HAYA REGISTROS DE ASISTENCIA*********/
    if($valor['dia_semana'] != ''){

      if(preg_match("/nocturno/", strtolower($value['horario']))){
                  $nueva_fecha = new \DateTime($fecha_inicio->format('Y-m-d').' '.$value['hora_entrada']);
                  $nueva_fecha->modify('-2 hours');
                  //$fecha_inicio->modify('+0 minute');
                  //$fecha_inicio->modify('-0 second');
                  $datos->fecha_inicio = $nueva_fecha->format('Y-m-d H:i:s'); 
                  $fecha_aux = new \DateTime($fecha_inicio->format('Y-m-d'));


                    $fecha_aux->add(new \DateInterval('P1D'));
                    $nueva_fecha= $fecha_aux->format('Y-m-d').' '.$value['hora_salida'];
                    $fecha_aux = new \DateTime($nueva_fecha);      
                    $fecha_aux->add(new \DateInterval('PT4H0S'));

                    $datos->fecha_fin = $fecha_aux->format('Y-m-d H:i:s');
                    $registro_entrada_array = IncidenciaDao::getAsistenciaModificada($datos);

                  }else{
                    $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' 00:00';
          $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' 23:59';
          
                  }

      
    }else{
      $nueva_fecha = new \DateTime($fecha_inicio->format('Y-m-d').' '.$value['hora_entrada']);
        $nueva_fecha->modify('-2 hours');
        //$fecha_inicio->modify('+0 minute');
        //$fecha_inicio->modify('-0 second');

        $datos->fecha_inicio = $nueva_fecha->format('Y-m-d H:i:s'); 
        $fecha_aux = new \DateTime($fecha_inicio->format('Y-m-d'));
        
        if(preg_match("/nocturno/", strtolower($value['horario']))){
          $fecha_aux->add(new \DateInterval('P1D'));
          $nueva_fecha= $fecha_aux->format('Y-m-d').' '.$value['hora_salida'];
          $fecha_aux = new \DateTime($nueva_fecha);      
          $fecha_aux->add(new \DateInterval('PT4H0S'));
        }else{
          $nueva_fecha= $fecha_inicio->format('Y-m-d').' '.$value['hora_salida'];
          $fecha_aux = new \DateTime($nueva_fecha);      
          $fecha_aux->add(new \DateInterval('PT4H0S'));
          //$fecha_aux->add(new \DateInterval('P0Y0M0DT2H0M0'));
        }

      $datos->fecha_fin = $fecha_aux->format('Y-m-d H:i:s');
      $registro_entrada_array = IncidenciaDao::getAsistenciaModificada($datos);
    }
/***************************************************************************************************************/
    if(count($registro_entrada_array) >= 1){
      $registro_entrada = array_shift($registro_entrada_array);
      if($registro_entrada['date_check'] != ''){
        $registro_entrada = $registro_entrada['date_check'];
      }else{
        $registro_entrada = 'Sin registro';
      }

      if(count($registro_entrada_array)>=1){
        $registro_salida = $registro_entrada_array[count($registro_entrada_array)-1];
        if($registro_salida['date_check'] != '')
          $registro_salida = $registro_salida['date_check'];
        else
          $registro_salida = 'Sin registro';

        }else{
        $registro_salida = 'Sin registro';
        }
    }else{


      $registro_entrada = 'Sin registro';
      $registro_salida = 'Sin registro';
    }

        $colaborador_id = MasterDom::getData('catalogo_colaboradores_id');

        $incidencia = '';
        
        foreach ($incidencias_colaborador as $llave => $valor) {
      if( $fecha_inicio->format('Y-m-d') === $valor['fecha_incidencia']){
        $incidencia = $valor['nombre'];
        $comentario = $valor['comentario'];
        break;
      }
    }

    // COLOR PARA UN DIA FESTIVO
    $diaFestivo = IncidenciaDao::getDiaFestivo($fecha_inicio->format('Y-m-d'));
    $colorDiaFestivo = (!empty($diaFestivo)) ? "#b9e6ff;" : "";

    // COLOR PARA CHECAR CUANDO EL DIA NO ES LABORADO
    $x_hora_entrada = ($value['hora_entrada'] != "" ) ? $value['hora_entrada'] : "No Laboral";
    $x_hora_salida = ($value['hora_salida'] != "" ) ? $value['hora_salida'] : "No Laboral";
    $x_registro_entrada = $registro_entrada; //($value['hora_entrada'] != "" ) ? $registro_entrada : "";
    $x_registro_salida = $registro_salida; //($value['hora_entrada'] != "" ) ? $registro_salida : "";

    if($x_hora_entrada == "No Laboral" && $x_hora_salida == "No Laboral" && $x_registro_entrada == "Sin registro"){
      $x_registro_entrada = "";
    }

    if($x_hora_entrada == "No Laboral" && $x_hora_salida == "No Laboral" && $x_registro_salida == "Sin registro"){
      $x_registro_salida = "";
    }

    $reg = ChecadorDao::getAsistenciaModificada($datos);

    $setFechasTotales = $this->getFechasTotales($reg);


    $tabla .=<<<html
      <tr>
        <td style="vertical-align:middle;">{$dias[$fecha_inicio->format('l')]}</td>
        <td style="vertical-align:middle;">{$fecha_inicio->format('Y-m-d')}</td>
        <td style="vertical-align:middle;">{$x_hora_entrada}</td>
        <td style="vertical-align:middle;">{$x_hora_salida}</td>
        <td style="vertical-align:middle;">{$setFechasTotales}</td>
html;
    $tabla.=<<<html
      </tr>
html;
            

        $fecha_inicio->add(new \DateInterval('P1D'));
      }//fin del while rango de fechas
      
      $tabla .=<<<html
      </tbody>
    </table>
html;

      $colaborador = IncidenciaDao::getById($idColaborador);
      $tipoPeriodo = ($vista == "semanales") ? "SEMANAL" : "QUINCENAL";
      

    if($vista == "semanales"){
      $vista = "/Checador/semanales/";
      $tipoPeriodo = "semanales";
    }

    if($vista == "quincenales"){
      $vista = "/Checador/quincenales/";
      $tipoPeriodo = "quincenales";
    }

    if($vista == "historicosSemanales"){
      $vista = "/Checador/historicosSemanales/";
      $tipoPeriodo = "historicosSemanales";
    }

    if($vista == "historicosQuincenales"){
      $vista = "/Checador/historicosQuincenales/";
      $tipoPeriodo = "historicosQuincenales";
    }


      //print_r($colaborador);
      View::set('direccionamiento',$vista);
      View::set('msjPeriodo',$this->getDataPeriodoById($idPeriodo)); // Obtiene el periodo de la incidencia
      View::set('tipoPeriodo',$tipoPeriodo); // Identificacion del periodo
      View::set('colaborador',$colaborador);
      View::set('idColaborador',$idColaborador);
      View::set('idPeriodo',$idPeriodo);
      View::set('tabla',$tabla);
      View::set('header',$this->_contenedor->header($this->getHeader()));
      View::set('footer',$this->_contenedor->footer($this->getFooter()));
      View::render('incentivo_checador');
    }

  public function getFechasTotales($data){
    $html = "";
    foreach ($data as $key => $value) {
      //echo "<pre>";print_r($value);echo "</pre>";
      $html .=<<<html
        <p>- Se realiz&oacute; una chequeo el d&iacute;a <b>{$value['date_check']} </b> -</p>
html;
    }
    return $html;
  }

  public function getFechasTotalesPDF($data){
    $html = "";
    foreach ($data as $key => $value) {
      //echo "<pre>";print_r($value);echo "</pre>";
      $html .=<<<html
        <p>Chequeo el d&iacute;a <b>{$value['date_check']} </b></p>
html;
    }
    return $html;
  }

    /*
    Fechas de reporte del checador
    */
  public function editFechas($id) {
      $extraHeader =<<<html
        <link rel="stylesheet" type="text/css" media="all" href="/css/daterangepicker.css" />
html;
      $extraFooter =<<<html
        <script type="text/javascript" src="http://netdna.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
        <script src="/js/moment/moment.min.js"></script>
        <script type="text/javascript" src="/js/daterangepicker.js"></script>
        <script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>

        <script type="text/javascript">
          $(document).ready(function() {
            updateConfig();

            function updateConfig() {
              var options = {};
              $('#config-demo').daterangepicker(
                options,
                function(start, end, label) { 
                  console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')'
                );
              }
            );}
          });
        </script>

        <script>
          $(document).ready(function(){
            
            var checkAll = 0;
            $("#checkAll").click(function () {
              if(checkAll==0){
                $("input:checkbox").prop('checked', true);
                checkAll = 1;
              }else{
                $("input:checkbox").prop('checked', false);
                checkAll = 0;
              }
            });

            

            //$("#btnAdd").click(
            function accionar(){
              $.ajax({
                url: '/AdminIncidencia/getTablaRangoFechas',
                'type': 'POST',
                'data': {
                  'colaborador_id': $('#catalogo_colaboradores_id').val(),
                  'periodo': $('#periodo').val()
                },
                success: function(response){
                  $("#contendor_tabla").html(response);

                  $("#muestra-cupones").tablesorter();
                    var oTable = $('#muestra-cupones').DataTable({
                    "columnDefs": [{
                      "orderable": false,
                      "targets": 0
                    }],
                    "order": false,
                    "language": {
                      "emptyTable": "No hay datos disponibles",
                      "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                      "info": "Mostrar _START_ a _END_ de _TOTAL_ registros",
                      "infoFiltered":   "(Filtrado de _MAX_ total de registros)",
                      "lengthMenu": "Mostrar _MENU_ registros",
                      "zeroRecords":  "No se encontraron resultados",
                      "search": "Buscar:",
                      "processing": "Procesando...",
                      "paginate" : {
                          "next": "Siguiente",
                          "previous" : "Anterior"
                      }
                    }
                  });

                  // Remove accented character from search input as well
                  $('#muestra-cupones input[type=search]').keyup( function () {
                    var table = $('#example').DataTable();
                    table.search(jQuery.fn.DataTable.ext.type.search.html(this.value)).draw();
                  } );
                }
              });
            }
            //);

            $("#periodo").change(function(){
              $.ajax({
                url: '/AdminIncidencia/verificarPeriodo',
                'type': 'POST',
                'data': {
                  'periodo': $('#periodo').val()
                },
                success: function(response){
                  if(response == 0){
                    $("#alerta").removeClass('alert-danger');
                    $("#alerta").addClass('alert-success');
                    $("#alerta").html('<strong>Atención</strong> El periodo seleccionado está abierto.');

                    //$("a.btn-primary").attr("disabled",false);
                    //$("a.eliminar").attr("disabled",false);

                  }

                  if(response == 1){
                    $("#alerta").removeClass('alert-success');
                    $("#alerta").addClass('alert-danger');
                    $("#alerta").html('<strong>Atención</strong> El periodo seleccionado está cerrado.');

                    //$("a.btn-primary").attr("disabled",true);
                    //$("a.eliminar").attr("disabled",true);
                  }

                  accionar();
                }
              });
            });

            $("#periodo").change();
            $("#btnAdd").click();


          });
        </script>
html;
    $colaborador = IncidenciaDao::getById($id);
    $selectPeriodoFechas = "";
    foreach (IncidenciaDao::getPeriodoFechas(strtoupper($colaborador['pago'])) as $key => $value) {
      $select = ($value['status'] == 0) ? "selected":"";
      $mensaje  = ($value['status'] == 0) ? " de proceso":" terminado";
      $selectPeriodoFechas .= <<<html
        <option {$select} value="{$value['prorrateo_periodo_id']}">{$value['fecha_inicio']} - {$value['fecha_fin']} : Este periodo esta en estatus {$mensaje}</option>
html;
      }

    foreach (IncidenciaDao::getPeriodoFechasProceso(strtoupper($colaborador['pago'])) as $key => $value) {
      $f1 = $value['fecha_inicio'];
      $f2 = $value['fecha_fin'];
      $estatusPeriodo = $value['status'];
    }

    View::set('colaborador',$colaborador);
    View::set('rango',$f1 . " - " . $f2);
    View::set('selectPeriodoFechas', $selectPeriodoFechas);
    View::set('header', $this->_contenedor->header($extraHeader));
    View::set('footer', $this->_contenedor->footer($extraFooter));
    View::render("admin_incidencia_colaborador");
    }

    public static function getTablaRangoFechas(){
    $colaborador = IncidenciaDao::getById(MasterDom::getData('colaborador_id'));
    $periodo = IncidenciaDao::getPeriodoById(MasterDom::getData('periodo'));
    $colaborador_id = MasterDom::getData('colaborador_id');
    
    $estatusPeriodo = $periodo['status'];
    $fecha_inicio = new \DateTime($periodo['fecha_inicio']);
    $fecha_final = new \DateTime($periodo['fecha_fin']);
    $dias = array('Monday'=>'Lunes','Tuesday'=>'Martes','Wednesday'=>'Miercoles','Thursday'=>'Jueves','Friday'=>'Viernes','Saturday'=>'Sabado','Sunday'=>'Domingo');

    $incidencias_colaborador = IncidenciaDao::getProrrateoColaboradorIncidenciaById($colaborador['catalogo_colaboradores_id']);
    $datos = new \stdClass();
    $datos->numero_empleado = $colaborador['numero_identificador'];
    $datos->catalogo_colaboradores_id = $colaborador['catalogo_colaboradores_id'];
    $datos->catalogo_lector_id = $colaborador['catalogo_lector_id'];

    $tabla =<<<html
      <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
        <thead>
          <tr>
            <th>Dia</th>
            <th>Fecha</th>
            <th>Entrada</th>
            <th>Salida</th>
            <th>Entrada Registrada</th>
            <th>Salida Registrada</th>
            <th>Comentario</th>
            <th>Incidencia</th>
            <th>Acción</th>
          </tr>
        </thead>
        <tbody id="registros">
html;

      $dia_aux = '';
      while($fecha_inicio <= $fecha_final){
        $value = '';
        //$bandera_dias_descanso = false;
        $semanal = 0;
        foreach (IncidenciaDao::getHorarioLaboral($datos) as $llave => $valor) {
          if($colaborador['horario_tipo'] == 'semanal'){
            if($semanal != date('W')){
              $semanal = date('W');
              $value = $valor;
              break;
            }
          }elseif ($colaborador['horario_tipo'] == 'diario') {
            if($valor['dia_semana'] == $dias[$fecha_inicio->format('l')]){
              $value = $valor;
              break;
            }
          }
        }

    //$bandera_dias_descanso = true;
    $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' '.Incidencia::restarMinutos($value['hora_entrada'],60);
    $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' '.Incidencia::sumarMinutos($value['hora_entrada'],intval($value['tolerancia_entrada']));
    $registro_entrada_array = IncidenciaDao::getAsistenciaModificada($datos);
  
    if(count($registro_entrada_array) >= 1){
      $registro_entrada = array_shift($registro_entrada_array);
      if($registro_entrada['date_check'] != ''){
        $registro_entrada = $registro_entrada['date_check'];
      }else{
        $registro_entrada = 'Sin registro';
      }

      if(count($registro_entrada_array)>=1){
        $registro_salida = array_pop($registro_entrada_array);
        if($registro_salida['date_check'] != '')
          $registro_salida = $registro_salida['date_check'];
        else
          $registro_salida = 'Sin registro';

        }else{
        $registro_salida = 'Sin registro';
        }
    }else{
      $registro_entrada = 'Sin registro';
      $registro_salida = 'Sin registro';
    }

        $colaborador_id = MasterDom::getData('catalogo_colaboradores_id');

        $incidencia = '';
        
        foreach ($incidencias_colaborador as $llave => $valor) {
      if( $fecha_inicio->format('Y-m-d') === $valor['fecha_incidencia']){
        $incidencia = $valor['nombre'];
        $comentario = $valor['comentario'];
        break;
      }
    }

    $tabla .=<<<html
      <tr>
        <td>{$dias[$fecha_inicio->format('l')]}</td>
        <td>{$fecha_inicio->format('Y-m-d')}</td>
        <td>{$value['hora_entrada']}</td>
        <td>{$value['hora_salida']}</td>
        <td>$registro_entrada</td>
        <td>$registro_salida</td>
        <td>
html;
    foreach ($incidencias_colaborador as $llave => $valor) {
      if( $fecha_inicio->format('Y-m-d') === $valor['fecha_incidencia']){
        $tabla .= $valor['comentario'];
        break;
      }
    }
        
    $tabla.=<<<html
      </td>
        <td>
          <label>{$incidencia}</label>
        </td>
      <td>
html;

    if($estatusPeriodo == 0){
      $tabla.=<<<html
        <a class="btn btn-primary" href="/AdminIncidencia/add/{$colaborador['catalogo_colaboradores_id']}/{$fecha_inicio->format('Y-m-d')}/">
          <i class="fa fa-plus-square" aria-hidden="true"></i>
        </a>
html;
    }else{
      $tabla.=<<<html
        <a class="btn btn-default" ><i class="fa fa-plus-square" aria-hidden="true"></i></a>
html;
    }
    
    $datos->fecha = $fecha_inicio->format('Y-m-d');
  if(count(IncidenciaDao::getFechaIncidenciaById($datos)) > 0) {
              if($estatusPeriodo == 0){
                $tabla .= <<<html
                  <a  href="/AdminIncidencia/deleteIncidecia?id={$colaborador['catalogo_colaboradores_id']}&fecha={$fecha_inicio->format('Y-m-d')}" class="btn btn-danger eliminar">
                    <i class="fa fa-trash-o" aria-hidden="true"> </i>
                  </a>
html;
              }else{
                $tabla .= <<<html
                  <a class="btn btn-default eliminar">
                    <i class="fa fa-trash-o" aria-hidden="true"> </i>
                  </a>
html;
              }
            }else{
              $tabla .= <<<html
                        
html;
            }
              $tabla .=<<<html
                <!--/div-->
          </td>
        </tr>
html;
            

        $fecha_inicio->add(new \DateInterval('P1D'));
      }//fin del while rango de fechas
      $tabla .=<<<html
      </tbody>
    </table>
html;

      echo $tabla;
    }

    public function deleteIncidecia(){
    $delete = new \stdClass();
    $delete->_id = MasterDom::getData('id');
    $delete->_fecha = MasterDom::getData('fecha');
    $id = AdminIncidenciaDao::deleteIncidencia($delete);

    $idColaborador = MasterDom::getData('idColaborador');
    $idPeriodo = MasterDom::getData('idPeriodo');
    $vista = MasterDom::getData('vista');
      

      if($id>0){
        View::set('class',"success");
      View::set('regreso',"/Checador/checadorFechas/{$idColaborador}/{$idPeriodo}/{$vista}/");
      View::set('mensaje',"Se ha agreado correctamente la incidencia.");
    }else{
      //$this->alerta($id, 'error', MasterDom::getData('colaborador_id'));
      View::set('class',"error");
      View::set('regreso',"/Checador/checadorFechas/{$idColaborador}/{$idPeriodo}/{$vista}/");
      View::set('mensaje',"Ha ocurrido un problema al agregar la incidencia.");
    }
    
    View::set('header',$this->_contenedor->header($extraHeader));
    View::set('footer',$this->_contenedor->footer($extraFooter));
    View::render("alerta");
    }

    public function add($colaborador_id, $fecha, $vista,$idPeriodo){
    $extraFooter =<<<html
      <script src="/js/moment/moment.min.js"></script>
      <script src="/js/datepicker/scriptdatepicker.js"></script>
      <script src="/js/datepicker/datepicker2.js"></script>
      <script>
        $(document).ready(function(){

        $('#fecha').daterangepicker({
          singleDatePicker: true,
          calender_style: "picker_1"
          }, function(start, end, label) {
          console.log(start.toISOString(), end.toISOString(), label);
        });

        $('#fecha_fin').daterangepicker({
          singleDatePicker: true,
          calender_style: "picker_1"
        }, function(start, end, label) {
          console.log(start.toISOString(), end.toISOString(), label);
        });

        $("#rango_fechas").bootstrapSwitch();

        $('input[name="rango_fechas"]').on('switchChange.bootstrapSwitch', function(event, state) {
          if(state){
            $("#contenedor_fecha_fin").show();
          }else{
            $("#contenedor_fecha_fin").hide();
          }
        });

        $("#add").validate({
          rules:{
            fecha:{required: true},
            incidencia_id:{required: true}
          },
          messages:{
            fecha:{required: "Este campo es requerido"},
            incidencia_id:{required: "Este campo es requerido"}
          }
        });

      });
    </script>
html;
      
    $sIncidencia = '';  
    foreach(IncidenciaDao::getIncidencias() as $key => $value){
      $sIncidencia .=<<<html
        <option value="{$value['catalogo_incidencia_id']}">{$value['nombre']}</option>
html;
    }

    if($vista == "semanales")
      $direccionamiento = "/Checador/checadorFechas/{$colaborador_id}/{$idPeriodo}/semanales/abierto";
    
    if($vista == "quincenales")
      $direccionamiento = "/Checador/checadorFechas/{$colaborador_id}/{$idPeriodo}/quincenales/abierto";


    if($vista == "historicosSemanales")
      $direccionamiento = "/Checador/historicosSemanales/";

    if($vista == "historicosQuincenales")
      $direccionamiento = "/Checador/historicosQuincenales/";

    View::set('direccionamiento',$direccionamiento);
    View::set('colaborador_id', $colaborador_id);
    View::set('periodo_id', $idPeriodo);

    View::set('fecha', $fecha);
    View::set('vista', $vista);
    View::set('sIncidencia', $sIncidencia);
    View::set('header', $this->_contenedor->header());
    View::set('footer', $this->_contenedor->footer($extraFooter));
    View::render('admin_incidencia_add');
    }

    public function incidenciaAdd(){
    $datos = new \stdClass();
    $datos->catalogo_colaboradores_id = MasterDom::getData('colaborador_id');
    $datos->fecha = MasterDom::getData('fecha');
    $datos->catalogo_incidencia_id = MasterDom::getData('incidencia_id');
    $datos->comentario = MasterDom::getData('comentario');

      if(MasterDom::getData('rango_fechas') == 'on'){
        $fecha_inicio = new \DateTime(MasterDom::getData('fecha'));
        $fecha_fin = new \DateTime(MasterDom::getData('fecha_fin'));
        while($fecha_inicio <= $fecha_fin){
            $datos->fecha = $fecha_inicio->format('Y-m-d');
            $existe = count(IncidenciaDao::getFechaIncidenciaById($datos));
            if($existe>0){
                IncidenciaDao::deleteFechaIncidenciaById($datos);
                $id = IncidenciaDao::insertProrrateoColaboradorIncidencia($datos);
            }else{
                $id = IncidenciaDao::insertProrrateoColaboradorIncidencia($datos);
            }
            if($id < 0){
                break;
            }
            $fecha_inicio->add(new \DateInterval('P1D'));
        }
      }else{
        $id = IncidenciaDao::insertProrrateoColaboradorIncidencia($datos);
      }

        $periodo_id = MasterDom::getData('periodo_id');
        $colaborador_id = MasterDom::getData('colaborador_id');
        $vista = MasterDom::getData('vista');


    if($id > 0){
      //$this->alerta($id, 'add', MasterDom::getData('colaborador_id'));
      View::set('class',"success");
      View::set('regreso',"/Checador/checadorFechas/{$colaborador_id}/{$periodo_id}/{$vista}/");
      View::set('mensaje',"Se ha agreado correctamente la incidencia.");
    }else{
      //$this->alerta($id, 'error', MasterDom::getData('colaborador_id'));
      View::set('class',"error");
      View::set('regreso',"/Checador/checadorFechas/{$colaborador_id}/{$periodo_id}/{$vista}/");
      View::set('mensaje',"Ha ocurrido un problema al agregar la incidencia.");
    }
    
    View::set('header',$this->_contenedor->header($extraHeader));
    View::set('footer',$this->_contenedor->footer($extraFooter));
    View::render("alerta");
    }

    public function direccionamiento($action){
    $vista = "";
    if($action == 'historicosSemanales'){
      $vista = "editFechas";
    }elseif($action == 'historicosQuincenales'){
      $vista = "editFechas";
    }elseif($action == 'semanales'){
      $vista = "checadorFechas";
    }elseif($action == 'quincenales'){
      $vista = "checadorFechas";
    }

      return $vista;
    }


  public static function sumarMinutos($horaInicial,$minutoAnadir){
    $segundos_horaInicial=strtotime($horaInicial);
    $segundos_minutoAnadir=$minutoAnadir*60;
    $nuevaHora=date("H:i",$segundos_horaInicial+$segundos_minutoAnadir);
    return $nuevaHora;
  }

  public static function restarMinutos($horaInicial,$minutoAnadir){
    $segundos_horaInicial=strtotime($horaInicial);
    $segundos_minutoAnadir=$minutoAnadir*60;
    $nuevaHora=date("H:i",$segundos_horaInicial-$segundos_minutoAnadir);
    return $nuevaHora;
  }

  public function getEmpresas(){
    $empresas = '';
    foreach (ColaboradoresDao::getIdEmpresa() as $key => $value) {
      $empresas .=<<<html
      <option value="{$value['catalogo_empresa_id']}">{$value['nombre']}</option>
html;
    }
    return $empresas;
  }

  public function getUbicacion(){
    $ubicaciones = '';
      foreach (ColaboradoresDao::getIdUbicacion() as $key => $value) {
        $ubicaciones .=<<<html
        <option value="{$value['catalogo_ubicacion_id']}">{$value['nombre']}</option>
html;
    }
    return $ubicaciones;
  }

  public function getDepartamentos(){
    $departamentos = "";
    foreach (ColaboradoresDao::getIdDepartamento() as $key => $value) {
      $departamentos .=<<<html
      <option value="{$value['catalogo_departamento_id']}">{$value['nombre']}</option>
html;
    }
    return $departamentos;
  }

  public function getPuestos(){
    $puestos = '';
    foreach (ColaboradoresDao::getIdPuesto() as $key => $value) {
      $puestos .=<<<html
      <option value="{$value['catalogo_puesto_id']}">{$value['nombre']}</option>
html;
    }
    return $puestos;
  }

  public function getNominas(){
    $nomina = "";
    foreach (ColaboradoresDao::getNominaIdentificador() as $key => $value) {
      if(!empty($value['identificador_noi'])){
        $nomina .=<<<html
        <option value="{$value['identificador_noi']}">NOMINA NOI {$value['identificador_noi']}</option>
html;
        }else{
        $nomina .=<<<html
        <option value="vacio">SIN NOMINA NOI</option>
html;
      } 
    }
    return $nomina;
  }

    public function getHeader(){
      $extraHeader = <<<html
          <style>.foto{ width:100px; height:100px; border-radius: 50px;}</style>
          <link href="/js/tables/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
          <link href="/js/tables/vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
          <link href="/js/tables/vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
          <link href="/js/tables/vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
          <link href="/js/tables/vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">
html;
    return $extraHeader;  
    }

    /*

    */
    public function getFooter(){
      $extraFooter = <<<html
      <!-- Datatables -->
      <script src="/js/tables/vendors/datatables.net/js/jquery.dataTables.min.js"></script>
      <script src="/js/tables/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
      <script src="/js/tables/vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
      <script src="/js/tables/vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
      <script src="/js/tables/vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
      <script src="/js/tables/vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
      <script src="/js/tables/vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
      <script src="/js/tables/vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
      <script src="/js/tables/vendors/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
      <script src="/js/tables/vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
      <script src="/js/tables/vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
      <script src="/js/tables/vendors/datatables.net-scroller/js/dataTables.scroller.min.js"></script>
      <script src="/js/tables/vendors/jszip/dist/jszip.min.js"></script>
      <script src="/js/tables/vendors/pdfmake/build/pdfmake.min.js"></script>
      <script src="/js/tables/vendors/pdfmake/build/vfs_fonts.js"></script>
html;
    $extraFooter .=<<<html
      <script>
        $(document).ready(function(){
          $("#muestra-colaboradores").tablesorter();
          var oTable = $('#muestra-colaboradores').DataTable({
            "columnDefs": [{
              "orderable": false,
              "targets": 0
            }],
            "order": false
          });

          $('#muestra-colaboradores input[type=search]').keyup( function () {
            var table = $('#example').DataTable();
            table.search(
              jQuery.fn.DataTable.ext.type.search.html(this.value)
            ).draw();
          });

        });
      </script>
html;
    return $extraFooter;
    }

  public function generarPDF($idColaborador,$idPeriodo){
    $mpdf=new \mPDF('c');
    $mpdf->defaultPageNumStyle = 'I';
    $mpdf->h2toc = array('H5'=>0,'H6'=>1);
    $style =<<<html
      <link rel="stylesheet" href="/css/bootstrap/bootstrap.css">
html;
    $table = $this->generateTableToPDF($idColaborador,$idPeriodo);
    $periodo = IncidenciaDao::getPeriodoById($idPeriodo);
    $fechaIni = MasterDom::getFecha($periodo['fecha_inicio']);
    $fechaFin = MasterDom::getFecha($periodo['fecha_fin']);
    $colaborador = IncidenciaDao::getById($idColaborador);
    $imgHeader = $this->getIMGPDF();
    $html =<<<html
      
      <div class="row">
        <div>
          <img src="http://52.32.114.10:8070/img/ag_logo.jpg" style="float:right; width: 150px; height:90px; margin-right:30px;">
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12">
          <h1 style="font-size:20px; text-align:center;">TARJETA DE REGISTRO DE ASISTENCIAS</h1>

          <h2 style="font-size:16px; text-align:center;">PERIODO QUE CORRESPONDE A ESTA TARJETA DE REGISTRO DE ASISTENCIA</h2><br><br>


          <div style="border-bottom: 1px solid #232323; text-align:center; margin-left:15%; margin-right:15%;">{$fechaIni} <b>al</b> {$fechaFin}</div><br><br>


          <h2 style="text-align:center; font-size:18px;">AG Alimentos de la Granja</h2>

          <div class="panel-body">
            <div class="dataTable_wrapper">
              <!--table class="table table-striped table-bordered table-hover" id="muestra-cupones">
                <thead>
                  <tr>
                    <th><input type="checkbox" name="checkAll" id="checkAll" value=""/></th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Etatus</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>1231231</td>
                    <td>1231231</td>
                    <td>1231231</td>
                    <td>1231231</td>
                    <td>1231231</td>
                  </tr>
                </tbody>
              </table-->

              {$table}
            </div>
          </div>

          <p>
            Declaro que los horarios de entradas y salidas que se indican en la tarjeta han sido marcados exclusivamente por mi y corresponden al registro de mi asistencia en Alimentos de la Granja, S. A. de C. V.
          </p><br><br>
            
          <div style="border-bottom: 1px solid #232323; text-align:center; margin-left:25%; margin-right:25%;"></div>
          </div>
          <div style="text-align:center;">
            {$colaborador['nombre']} {$colaborador['apellido_paterno']} {$colaborador['apellido_materno']}
          </div>
        </div>
      </div>
html;
      $nombre_archivo = "MPDF_".uniqid().".pdf";/* se genera un nombre unico para el archivo pdf*/
      $data = "colaborador={$colaborador['nombre']}_{$colaborador['apellido_paterno']}_{$colaborador['apellido_materno']},periodo={$fechaIni}-{$fechaFin}";
      $mpdf->SetTitle($data);
      $mpdf->WriteHTML($style,1);
      $mpdf->WriteHTML($html,2);
      print_r($mpdf->Output());
      exit;
  }

  public function getIMGPDF(){
    $html =<<<html
      <img src="/img/ag_logo.png"/>
html;
    return $html;
  }

  public function generateTableToPDF($idColaborador,$idPeriodo){
    $colaborador = IncidenciaDao::getById($idColaborador);
    $periodo = IncidenciaDao::getPeriodoById($idPeriodo);
    $colaborador_id = $idColaborador;

    $estatusPeriodo = $periodo['status'];
    $fecha_inicio = new \DateTime($periodo['fecha_inicio']);
    $fecha_final = new \DateTime($periodo['fecha_fin']);
    $dias = array('Monday'=>'Lunes','Tuesday'=>'Martes','Wednesday'=>'Miercoles','Thursday'=>'Jueves','Friday'=>'Viernes','Saturday'=>'Sabado','Sunday'=>'Domingo');

    $incidencias_colaborador = IncidenciaDao::getProrrateoColaboradorIncidenciaById($colaborador['catalogo_colaboradores_id']);
    $datos = new \stdClass();
    $datos->numero_empleado = $colaborador['numero_identificador'];
    $datos->catalogo_colaboradores_id = $colaborador['catalogo_colaboradores_id'];    
    $datos->catalogo_lector_id = $colaborador['catalogo_lector_id'];

    $tabla =<<<html
      <table class="dataTable_wrapper" id="muestra-colaboradores" border="1" style="width:100%;">
        <thead>
          <tr>
            <th style="padding:5px; background: #3e648f; color:#FFF;">Dia</th>
            <th style="padding:5px; background: #3e648f; color:#FFF;">Fecha</th>
            <th style="padding:5px; background: #3e648f; color:#FFF;">Entrada</th>
            <th style="padding:5px; background: #3e648f; color:#FFF;">Salida</th>
            <th style="padding:5px; background: #3e648f; color:#FFF;">Entrada Registrada</th>
          </tr>
        </thead>
        <tbody id="registros">
html;

        $dia_aux = '';
        $ultimo_horario = IncidenciaDao::getUltimoHorario($datos);
        $horarios = IncidenciaDao::getHorariosById($datos);
      $num_semana = 0;
      while($fecha_inicio <= $fecha_final){
        
        $value = '';

        /******************************************************************************************************************************************************/
        if($colaborador['horario_tipo'] == 'semanal'){
        $nombre_dia_semana = $dias[$fecha_inicio->format('l')];

            if( $num_semana != $fecha_inicio->format('W') ){
                $num_semana = $fecha_inicio->format('W');
                //echo "Numero de semana:::$num_semana<br>";
                $catalogo_horario_id_anterior = $catalogo_horario_id;
                for($llave=0; $llave<count($horarios); $llave++) {
                    $valor = $horarios[$llave];
                    
                    if(count($ultimo_horario) > 0){
                        if($valor['catalogo_horario_id'] == $ultimo_horario['catalogo_horario_id']){
                          if( ($llave+1) >= count($horarios) ){
                            $catalogo_horario_id = $horarios[0]['catalogo_horario_id'];
                          }else{
                            $catalogo_horario_id = $horarios[intval($llave)+1]['catalogo_horario_id'];
                          }
                        }
                    }else{
                      if($ultimo_horario == ''){
                        $catalogo_horario_id = $horarios[0]['catalogo_horario_id'];
                      }elseif($catalogo_horario_id == 0){
                            //echo "Catalogo horario id: $catalogo_horario_id:::::::::";
                            $catalogo_horario_id = $horarios[0]['catalogo_horario_id'];
                            //echo ">$catalogo_horario_id<br><br>";
                        }else{
                            //echo "Catalogo_horario_id = $catalogo_horario_id:::::::::";
                            if($valor['catalogo_horario_id'] == $catalogo_horario_id){
                              if( ($llave+1) >= count($horarios) ){
                                $catalogo_horario_id = $horarios[0]['catalogo_horario_id'];
                              }else{
                                $catalogo_horario_id = $horarios[intval($llave)+1]['catalogo_horario_id'];
                              }
                            }
                        }
                    }
                    if($catalogo_horario_id != $catalogo_horario_id_anterior){break;}
                }
            }
            
        }
        $datos->catalogo_horario_id = $catalogo_horario_id;
        /******************************************************************************************************************************************************/

        foreach (IncidenciaDao::getHorarioLaboralById($datos) as $llave => $valor) {
          if($valor['dia_semana'] == $dias[$fecha_inicio->format('l')]){
        if($colaborador['horario_tipo'] == 'semanal'){
          $value = $valor;
          break;
        }elseif ($colaborador['horario_tipo'] == 'diario') {
          if($valor['dia_semana'] == $dias[$fecha_inicio->format('l')]){
            $value = $valor;
            break;
          }
        }
          }
    }

/***********************************SI NO ES DIA DE TRABAJO VERIFICA QUE NO HAYA REGISTROS DE ASISTENCIA*********/
    if($valor['dia_semana'] != ''){

      if(preg_match("/nocturno/", strtolower($value['horario']))){
                  $nueva_fecha = new \DateTime($fecha_inicio->format('Y-m-d').' '.$value['hora_entrada']);
                  $nueva_fecha->modify('-2 hours');
                  //$fecha_inicio->modify('+0 minute');
                  //$fecha_inicio->modify('-0 second');
                  $datos->fecha_inicio = $nueva_fecha->format('Y-m-d H:i:s'); 
                  $fecha_aux = new \DateTime($fecha_inicio->format('Y-m-d'));


                    $fecha_aux->add(new \DateInterval('P1D'));
                    $nueva_fecha= $fecha_aux->format('Y-m-d').' '.$value['hora_salida'];
                    $fecha_aux = new \DateTime($nueva_fecha);      
                    $fecha_aux->add(new \DateInterval('PT4H0S'));

                    $datos->fecha_fin = $fecha_aux->format('Y-m-d H:i:s');
                    $registro_entrada_array = IncidenciaDao::getAsistenciaModificada($datos);

                  }else{
                    $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' 00:00';
          $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' 23:59';
          
                  }

      
    }else{
      $nueva_fecha = new \DateTime($fecha_inicio->format('Y-m-d').' '.$value['hora_entrada']);
        $nueva_fecha->modify('-2 hours');
        //$fecha_inicio->modify('+0 minute');
        //$fecha_inicio->modify('-0 second');

        $datos->fecha_inicio = $nueva_fecha->format('Y-m-d H:i:s'); 
        $fecha_aux = new \DateTime($fecha_inicio->format('Y-m-d'));
        
        if(preg_match("/nocturno/", strtolower($value['horario']))){
          $fecha_aux->add(new \DateInterval('P1D'));
          $nueva_fecha= $fecha_aux->format('Y-m-d').' '.$value['hora_salida'];
          $fecha_aux = new \DateTime($nueva_fecha);      
          $fecha_aux->add(new \DateInterval('PT4H0S'));
        }else{
          $nueva_fecha= $fecha_inicio->format('Y-m-d').' '.$value['hora_salida'];
          $fecha_aux = new \DateTime($nueva_fecha);      
          $fecha_aux->add(new \DateInterval('PT4H0S'));
          //$fecha_aux->add(new \DateInterval('P0Y0M0DT2H0M0'));
        }

      $datos->fecha_fin = $fecha_aux->format('Y-m-d H:i:s');
      $registro_entrada_array = IncidenciaDao::getAsistenciaModificada($datos);
    }
/***************************************************************************************************************/
    if(count($registro_entrada_array) >= 1){
      $registro_entrada = array_shift($registro_entrada_array);
      if($registro_entrada['date_check'] != ''){
        $registro_entrada = $registro_entrada['date_check'];
      }else{
        $registro_entrada = 'Sin registro';
      }

      if(count($registro_entrada_array)>=1){
        $registro_salida = $registro_entrada_array[count($registro_entrada_array)-1];
        if($registro_salida['date_check'] != '')
          $registro_salida = $registro_salida['date_check'];
        else
          $registro_salida = 'Sin registro';

        }else{
        $registro_salida = 'Sin registro';
        }
    }else{


      $registro_entrada = 'Sin registro';
      $registro_salida = 'Sin registro';
    }

        $colaborador_id = MasterDom::getData('catalogo_colaboradores_id');

        $incidencia = '';
        
        foreach ($incidencias_colaborador as $llave => $valor) {
      if( $fecha_inicio->format('Y-m-d') === $valor['fecha_incidencia']){
        $incidencia = $valor['nombre'];
        $comentario = $valor['comentario'];
        break;
      }
    }

    // COLOR PARA UN DIA FESTIVO
    $diaFestivo = IncidenciaDao::getDiaFestivo($fecha_inicio->format('Y-m-d'));
    $colorDiaFestivo = (!empty($diaFestivo)) ? "#b9e6ff;" : "";

    // COLOR PARA CHECAR CUANDO EL DIA NO ES LABORADO
    $x_hora_entrada = ($value['hora_entrada'] != "" ) ? $value['hora_entrada'] : "No Laboral";
    $x_hora_salida = ($value['hora_salida'] != "" ) ? $value['hora_salida'] : "No Laboral";
    $x_registro_entrada = $registro_entrada; //($value['hora_entrada'] != "" ) ? $registro_entrada : "";
    $x_registro_salida = $registro_salida; //($value['hora_entrada'] != "" ) ? $registro_salida : "";

    if($x_hora_entrada == "No Laboral" && $x_hora_salida == "No Laboral" && $x_registro_entrada == "Sin registro"){
      $x_registro_entrada = "";
    }

    if($x_hora_entrada == "No Laboral" && $x_hora_salida == "No Laboral" && $x_registro_salida == "Sin registro"){
      $x_registro_salida = "";
    }

    $reg = ChecadorDao::getAsistenciaModificada($datos);

    $setFechasTotales = $this->getFechasTotalesPDF($reg);


    $tabla .=<<<html
      <tr>
        <td style="vertical-align:middle; background:#4876a9; color: #fff; padding:5px;"> {$dias[$fecha_inicio->format('l')]} </td>
        <td style="vertical-align:middle; padding:5px;">{$fecha_inicio->format('Y-m-d')} </td>
        <td style="vertical-align:middle; padding:5px;">{$x_hora_entrada} </td>
        <td style="vertical-align:middle; padding:5px;">{$x_hora_salida} </td>
        <td style="vertical-align:middle; padding:5px; text-align:center;">{$setFechasTotales} </td>
html;
    $tabla.=<<<html
      </tr>
html;
            

        $fecha_inicio->add(new \DateInterval('P1D'));
      }//fin del while rango de fechas
      
      $tabla .=<<<html
        </tbody>
      </table>
html;

      //echo $tabla;
      return $tabla;
    }

}
