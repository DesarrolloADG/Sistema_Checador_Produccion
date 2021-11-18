<?php
namespace App\controllers;
defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \Core\Controller;
use \App\models\Incentivo as IncentivoDao;
use \App\models\ResumenSemanal AS ResumenSemanalDao;
use \App\models\General AS GeneralDao;
use \App\models\Resumenes AS ResumenesDao;
use \App\models\Colaboradores AS ColaboradoresDao;
use \App\models\Incidencia as IncidenciaDao;

class IncentivoRoy extends Controller{

  function __construct(){
    parent::__construct();
    $this->_contenedor = new Contenedor;
    View::set('header',$this->_contenedor->header());
    View::set('footer',$this->_contenedor->footer());
  }

  public function index() {
  }

  public function seccion($nomina /*SEMANAL - QUINCENAL - SHISTORICOS - QHISTORICOS*/, $tipoBusqueda /* 0.ABIERTOS - 1.PROCESO - 2.CERRADOS */) {
    $user = GeneralDao::getDatosUsuarioLogeado($this->__usuario);
    //$user = GeneralDao::getDatosUsuarioLogeado("lgarcia"); 
    //echo "<pre>"; print_r($user); echo "</pre>"; 
    $datos['c.pago'] = ucwords(strtolower($nomina));
    $filtros = $datos;
    View::set('mensaje',$this->getTituloPeriodo($nomina, $tipoBusqueda));
    View::set('tituloColaboradores',$this->getTituloColaboradores($user['perfil_id'], $user['identificador'], $user['catalogo_planta_id'], $user['nombre_departamento']));
    View::set('tabla',$this->getAllColaboradoresAsignados($user['perfil_id'], $user['identificador'], $user['catalogo_planta_id'], $user['catalogo_departamento_id'], $filtros));
    View::set('header',$this->_contenedor->header($this->getHeader()));
    View::set('footer',$this->_contenedor->footer($this->getFooter()));
    View::render("incentivos");
  }

  public function getAllColaboradoresAsignados($perfil, $identificador, $planta, $departamento, $filtros){
    $html = "";
    foreach (GeneralDao::getAllColaboradores($perfil, $identificador, $planta, $departamento, $filtros) as $key => $value) {
      $value['identificador_noi'] = (!empty($value['identificador_noi'])) ? $value['identificador_noi'] : "SIN<br>IDENTIFICADOR";
      $html .=<<<html
        <tr>
          <!--td style="text-align:center; vertical-align:middle;"><input type="checkbox" name="borrar[]" value="{$value['catalogo_colaboradores_id']}"/> {$value['catalogo_colaboradores_id']}</td-->
          <td style="text-align:center; vertical-align:middle;"><img class="foto" src="/img/colaboradores/{$value['foto']}"/></td>
          <td style="text-align:center; vertical-align:middle;"> {$value['apellido_paterno']} <br> {$value['apellido_materno']} <br> {$value['nombre']} </td>
          <td style="text-align:left; vertical-align:middle;">
            <b># EMPLEADO</b> {$value['numero_empleado']} <br>
            <b># PUESTO</b> {$value['nombre_puesto']}
          </td>
          <td style="text-align:center; vertical-align:middle;">
html;
      $html .= $this->getIncentivos($value['catalogo_colaboradores_id']);
      $html .=<<<html
          </td>
          <td style="text-align:center; vertical-align:middle;">
            <a href="/Incentivo/incentivos/{$value['catalogo_colaboradores_id']}/{$idPeriodo}/{$vista}" type="submit" name="id_empresa" class="btn btn-primary"><i class="fa fa-plus"></i> </a>
          </td>
        </tr>
html;
    }
    return $html;
  }

  public function getTituloColaboradores($perfil, $identificador, $planta, $nombreDepartamento){
    $identificador = explode("_", $identificador);
    $identificador = strtoupper($identificador[0]);
    $titulo = "";
    if($perfil == 1){ // PERFIL ROOT
      $titulo .= "Administra a todos los usuarios";
    }elseif($perfil == 4){ // PERFIL ADMINISTRADOR
      $titulo .= "Administra a todos los usuarios";
    }elseif($perfil == 5){ // PERFIL PERSONALIZADO
      $titulo .= "Administra unicamente a los usuario del departamento de {$nombreDepartamento}";
    }elseif($perfil == 6){ // PERFIL RECURSOS HUMANOS
      if($planta == 1) $titulo .= "Administra a todos los usuarios";
      else $titulo .= "Recursos humanos {$identificador}, Administra a usuarios de {$identificador}";
    }else{ // NO HAY PERFIL 
      $titulo .= " -> lo sentimos, no hay ningun perfil asignado para este usuario.";
    }
    return " " . $titulo;
  }

  public function getTituloPeriodo($nomina, $tipoBusqueda){

    $data = new \stdClass();

    if(1 == 1){
      $data->_prorrateo_periodo_id = 1; /* ID DEL PERIODO */
      $data->_tipo_busqueda = $tipoBusqueda; /*TRUE=> TODOS LOS PERIODOS o FALSE=> BUSQUEDA POR PERIODO_ID */
      $data->_status = 0;/* 0 - 1 - 2*/
      $data->_tipo = $nomina;/*SEMANAL o QUINCENAL*/
    }

    $tituloPeriodo = "";
    if($tipoBusqueda == 0){ /* CUANDO SE BUSCA UN PERIODO ABIERO, YA SEA SEMANAL O QUINCENAL */
      $fechasPeriodoAbierto = GeneralDao::getPeriodo($data);
      $fechaIni = MasterDom::getFecha($fechasPeriodoAbierto[0]['fecha_inicio']);
      $fechaFin = MasterDom::getFecha($fechasPeriodoAbierto[0]['fecha_fin']);
      $tituloPeriodo .= <<<html
      <b>( {$fechaIni} al {$fechaFin} )</b> <label class="{$statusColor}">periodo {$texto} <label class="label label-success"> es un periodo abierto</label></label>
html;
      View::set('msjPeriodo',$tituloPeriodo);
      View::set('hidden',"display:none");
    }elseif($tipoBusqueda == 1){ /* CUANDO SE BUSCA POR SEMANALES O QUINCENALES HISTORICOS */ 
      $periodo = GeneralDao::getPeriodo($data); 
      $periodoshtml = "";
      foreach ($periodo as $key => $value) {

        $periodoshtml .= <<<html
        <option value="">{$value['tipo']} - {$value['fecha_inicio']} - {$value['fecha_inicio']} </option>
html;
        if($key == 0){
          $fechaIni = MasterDom::getFecha($periodoshtml[0]['fecha_inicio']);
          $fechaFin = MasterDom::getFecha($periodoshtml[0]['fecha_fin']);
          $tituloPeriodo .= <<<html
            <b>( {$fechaIni} al {$fechaFin} )</b> <label class="{$statusColor}">periodo {$texto} <label class="label label-success"> es un periodo abierto</label></label>
html;
          View::set('msjPeriodo',$tituloPeriodo);
        }
      }
      View::set('hidden',"");
      View::set('periodos',$periodoshtml);
    }elseif($tipoBusqueda == 3){ /* CUANDO SE BUSCA UN UNICO PERIODO */ 

    }
    
  }

  /* ***************************************************************************** */
  /* ***************************************************************************** */
  /* ***************************************************************************** */

  public function index1(){}

  public function semanales(){
    $user = GeneralDao::getDatosUsuario($this->__usuario);
    //$user = GeneralDao::getDatosUsuarioLogeado("mmedina"); // PERFIL ADMINISTRADOR
    //$user = GeneralDao::getDatosUsuarioLogeado("lgarcia"); // PERFIL PERSONALIZADO
    //$user = GeneralDao::getDatosUsuarioLogeado("muriza"); // PERFIL RECURSOS HUNAMOS XOXHIMILCO
    //$user = GeneralDao::getDatosUsuarioLogeado("test"); // PERFIL RECURSOS HUMANOS NO XOCHIMICO
    //echo "<pre>"; print_r($user); echo "</pre>";


    if($user['perfil_id'] == 6 || $user['perfil_id'] == 5){
      $val = ($user['catalogo_planta_id'] == 1) ? 1 : 1;
      $tituloVista = "Incentivos propios <b>" . strtoupper($user['nombre_planta']) . "</b> - depto. <b>" . strtoupper($user['nombre']) . "</b>";
    }else{
      if($user['perfil_id'] == 6) // Si el usuario es de RH
        $tituloVista = "TODOS los Incentivos de Recursos Humanos - Planta " . strtolower($user['nombre_planta']);

      if($user['perfil_id'] == 4){ // Si el usuario es de perfil personalizado o admin
        $tituloVista = "TODOS los Incentivos semanales";
        $val = 6;
      }

      if($user['perfil_id'] == 1 ){ // Si el usuario es root
        $tituloVista = "TODOS los Incentivos de TODAS LAS PLANTAS ";
        $val = 6;
      }
      if($user['perfil_id'] == 2){ // Si el usuario es personal
        $tituloVista = "TODOS los Incentivos de TODAS LAS PLANTAS ";
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

    $i = IncentivoDao::getPeriodoLast();

    $idPeriodo = $this->getIdPeriodo("SEMANAL", $i['status']);

    View::set('msjPeriodo',$this->getPeriodo("SEMANAL", $i['status']));
    View::set('tipoPeriodo',$tituloVista); // Identificacion del periodo
    View::set('tituloIncentivos',"Semanales");
    View::set('tabla',$this->getTabla("Semanal",$idPeriodo,"semanales", $user['perfil_id'], $user['catalogo_departamento_id'], $user['catalogo_planta_id'], $val, $user['nombre_planta'])); // Obtener la tabla
    View::set('header',$this->_contenedor->header($this->getHeader()));
    View::set('footer',$this->_contenedor->footer($this->getFooter()));
    View::render("incentivos_abiertos");
  }

  public function quincenales(){
    $user = GeneralDao::getDatosUsuario($this->__usuario);

    if($user['perfil_id'] == 6){  
      $val = ($user['catalogo_planta_id'] == 1) ? 1 : 5;
      $tituloVista = "Incentivos propios <b>" . strtoupper($user['nombre_planta']) . "</b> - depto. <b>" . strtoupper($user['nombre']) . "</b>";
    }else{
      if($user['perfil_id'] == 6) // Si el usuario es de RH
        $tituloVista = "TODOS los Incentivos de Recursos Humanos - Planta " . strtolower($user['nombre_planta']);

      if($user['perfil_id'] == 4 || $user['perfil_id'] == 5 ) // Si el usuario es de perfil personalizado o admin
        $tituloVista = "TODOS los Incentivos del depto. " . strtolower($user['nombre']);

      if($user['perfil_id'] == 1 ){ // Si el usuario es root
        $tituloVista = "TODOS los Incentivos de TODAS LAS PLANTAS ";
        $val = 6;
      }
      if($user['perfil_id'] == 2){ // Si el usuario es personalizod
        $tituloVista = "TODOS los Incentivos de TODAS LAS PLANTAS ";
        $val = 6;
      }
    }
    
    $idPeriodo = $this->getIdPeriodo("QUINCENAL", 0);
    View::set('msjPeriodo',$this->getPeriodo("QUINCENAL", 0));
    View::set('tipoPeriodo',$tituloVista); // Identificacion del periodo
    View::set('tituloIncentivos',"Quincenales");
    View::set('tabla',$this->getTabla("Quincenal",$idPeriodo,"quincenales", $user['perfil_id'], $user['catalogo_departamento_id'], $user['catalogo_planta_id'], $val, $user['nombre_planta'])); // Obtener la tabla
    View::set('header',$this->_contenedor->header($this->getHeader()));
    View::set('footer',$this->_contenedor->footer($this->getFooter()));
    View::render("incentivos_abiertos");
  }

  public function historicosSemanales(){
    $user = GeneralDao::getDatosUsuario($this->__usuario); // ROOT
    //$user = GeneralDao::getDatosUsuarioLogeado("lgarcia"); // PERFIL PERSONALIZADO
    //$user = GeneralDao::getDatosUsuarioLogeado("mmedina"); // PERFIL ADMINISTRADOR
    //$user = GeneralDao::getDatosUsuarioLogeado("muriza"); // PERFIL RECURSOS HUNAMOS XOXHIMILCO
    //$user = GeneralDao::getDatosUsuarioLogeado("test"); // PERFIL RECURSOS HUMANOS NO XOCHIMICO

    if(empty($_POST)){
      $ultimoPeriodoHistorico = IncentivoDao::getUltimoPeriodoHistorico("SEMANAL");
      $idPeriodo = $ultimoPeriodoHistorico['prorrateo_periodo_id'];
    }else{
      $idPeriodo = MasterDom::getData('tipo_periodo');
    }

    if($user['perfil_id'] == 6){
        $tituloVista = "Incentivos propios <b>" . strtoupper($user['nombre_planta']) . "</b> - depto. <b>" . strtoupper($user['nombre']) . "</b>";
        $val = ($user['catalogo_planta_id'] == 1) ? 1 : 5;
    }else{
      if($user['perfil_id'] == 6) // Si el usuario es de RH
        $tituloVista = "TODOS los Incentivos de Recursos Humanos - Planta " . strtolower($user['nombre_planta']);

      if($user['perfil_id'] == 5 ){ // Si el usuario es de perfil personalizado o admin
        $tituloVista = "TODOS los Incentivos del depto. " . strtolower($user['nombre']);
      }

      if($user['perfil_id'] == 4){ // Si el usuario es de perfil personalizado o admin
        $tituloVista = "TODOS los usuarios con incentivos " . strtolower($user['nombre_planta']);
        $val = 6;
      }

      if($user['perfil_id'] == 1 ){ // Si el usuario es root
        $tituloVista = "TODOS los Incentivos de TODAS LAS PLANTAS ";
        $val = 6;
      }
      if($user['perfil_id'] == 2){ // Si el usuario es root
        $tituloVista = "TODOS los Incentivos de TODAS LAS PLANTAS ";
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

    View::set('periodo',$idPeriodo);

    View::set('form',"/Incentivo/historicosSemanales/");
        
    View::set('tipoPeriodo',$tituloVista); // Identificacion del periodo
    View::set('tituloIncentivos',"Semanales");

    View::set('option',$this->getAllperiodosSemanalesHistoricos($idPeriodo)); // Optiene todos los periodos procesados(historicos) semanales
    View::set('tabla',$this->getTabla("Semanal",$idPeriodo,"historicosSemanales", $user['perfil_id'], $user['catalogo_departamento_id'], $user['catalogo_planta_id'], $val, $user['nombre_planta'])); // Obtener la tabla
    View::set('busqueda',"/Incentivo/historicosSemanales/");
    View::set('header',$this->_contenedor->header($this->getHeader()));
    View::set('footer',$this->_contenedor->footer($this->getFooter()));
    View::render("incentivos_historicos");
  }

  public function historicosQuincenales(){

    $user = GeneralDao::getDatosUsuario($this->__usuario);

    if(empty($_POST)){
      $ultimoPeriodoHistorico = IncentivoDao::getUltimoPeriodoHistorico("SEMANAL");
      $idPeriodo = $ultimoPeriodoHistorico['prorrateo_periodo_id'];
    }else{
      $idPeriodo = MasterDom::getData('tipo_periodo');
    }

    if($user['perfil_id'] == 6){
      $tituloVista = "Incentivos propios <b>" . strtoupper($user['nombre_planta']) . "</b> - depto. <b>" . strtoupper($user['nombre']) . "</b>";
      $val = ($user['catalogo_planta_id'] == 1) ? 1 : 5;
    }else{
      if($user['perfil_id'] == 6) // Si el usuario es de RH
        $tituloVista = "TODOS los Incentivos de Recursos Humanos - Planta " . strtolower($user['nombre_planta']);

      if($user['perfil_id'] == 4 || $user['perfil_id'] == 5 ) // Si el usuario es de perfil personalizado o admin
        $tituloVista = "TODOS los Incentivos del depto. " . strtolower($user['nombre']);

      if($user['perfil_id'] == 1 ) {// Si el usuario es root
        $tituloVista = "TODOS los Incentivos de TODAS LAS PLANTAS ";
        $val = 6;
      }
    }

    

    View::set('tipoPeriodo',$tituloVista); // Identificacion del periodo
    View::set('tituloIncentivos',"Quincenales");
    View::set('option',$this->getPeriodosHistoricos("QUINCENAL")); // Optiene todos los periodos procesados(historicos) semanales
    View::set('tabla',$this->getTabla("Quincenal",$idPeriodo,"historicosQuincenales", $user['perfil_id'], $user['catalogo_departamento_id'], $user['catalogo_planta_id'], $val, $user['nombre_planta'])); // Obtener la tabla
    View::set('header',$this->_contenedor->header($this->getHeader()));
    View::set('footer',$this->_contenedor->footer($this->getFooter()));
    View::render("incentivos_historicos");
  }

  public function propiosSemanales(){

    $user = GeneralDao::getDatosUsuario($this->__usuario);

    if($user['perfil_id'] == 6){ // Si el usuario es de RH
      $tituloVista = "TODOS los Incentivos de Recursos Humanos - Planta " . strtolower($user['nombre_planta']) . " - Depto. " . $user['nombre'];
      $val = ($user['catalogo_planta_id'] == 1) ? 1 : 4;
    }

    if($user['perfil_id'] == 1){ // Si el usuario es de RH
      $tituloVista = "TODOS los Incentivos de ROOT - Planta " . strtolower($user['nombre_planta']) . " - Depto. " . $user['nombre'];
      $estatusRH = ($user['perfil_id'] == 1) ? 2 : 0;
      $val = 2; // TIENE INCENTIVOS PROPIOS
    }

    $getStatusPeriodo = GeneralDao::getLastPeriodo("SEMANAL");

    
    
    $idPeriodo = $this->getIdPeriodo("SEMANAL", $getStatusPeriodo['status']);
    View::set('msjPeriodo',$this->getPeriodo("SEMANAL", $getStatusPeriodo['status']));
    View::set('tituloIncentivos',"Semanales");
    View::set('tipoPeriodo',$tituloVista); // Identificacion del periodo
    View::set('tabla',$this->getTabla("Semanal",$idPeriodo,"propiosSemanales",$user['perfil_id'], $user['catalogo_departamento_id'], $user['catalogo_planta_id'], $val, $user['nombre_planta'] )); // Obtener la tabla
    View::set('header',$this->_contenedor->header($this->getHeader()));
    View::set('footer',$this->_contenedor->footer($this->getFooter()));
    View::render("incentivos_abiertos");
  }

  public function propiosQuincenales(){

    $user = GeneralDao::getDatosUsuario($this->__usuario);

    if($user['perfil_id'] == 6){ // Si el usuario es de RH
      $tituloVista = "TODOS los Incentivos de Recursos Humanos - Planta " . strtolower($user['nombre_planta']) . " - Depto. " . $user['nombre'];
      $val = ($user['catalogo_planta_id'] == 1) ? 1 : 4;
    }

    if($user['perfil_id'] == 1){ // Si el usuario es de RH
      $tituloVista = "TODOS los Incentivos de ROOT - Planta " . strtolower($user['nombre_planta']) . " - Depto. " . $user['nombre'];
      $estatusRH = ($user['perfil_id'] == 1) ? 2 : 0;
      $val = 2; // TIENE INCENTIVOS PROPIOS
    }

    $idPeriodo = $this->getIdPeriodo("QUINCENAL", 0);
    View::set('msjPeriodo',$this->getPeriodo("QUINCENAL", 0));
    View::set('tituloIncentivos',"Quincenales");
    View::set('tipoPeriodo',$tituloVista); // Identificacion del periodo
    View::set('tabla',$this->getTabla("Quincenal",$idPeriodo,"propiosQuincenales",$user['perfil_id'], $user['catalogo_departamento_id'], $user['catalogo_planta_id'], $val, $user['nombre_planta'] )); // Obtener la tabla
    View::set('header',$this->_contenedor->header($this->getHeader()));
    View::set('footer',$this->_contenedor->footer($this->getFooter()));
    View::render("incentivos_abiertos");
  }
  
  public function propiosSemanalesHistoricos(){
    $user = GeneralDao::getDatosUsuario($this->__usuario);

    if(empty($_POST)){
      $ultimoPeriodoHistorico = IncentivoDao::getUltimoPeriodoHistorico("SEMANAL");
      $idPeriodo = $ultimoPeriodoHistorico['prorrateo_periodo_id'];
      $msjPeriodofechas = $this->getPeriodoHistorico("SEMANAL", 1);

    }else{
      $idPeriodo = MasterDom::getData('tipo_periodo');
      $msjPeriodofechas = $this->getIdPeriodohistorico($idPeriodo);

    }

    if($user['perfil_id'] == 6){ // Si el usuario es de RH
      $tituloVista = "TODOS los Incentivos de Recursos Humanos - Planta " . strtolower($user['nombre_planta']) . " - Depto. " . $user['nombre'];
      $val = ($user['catalogo_planta_id'] == 1) ? 1 : 4;
    }

    if($user['perfil_id'] == 1){ // Si el usuario es de RH
      $tituloVista = "TODOS los Incentivos de ROOT - Planta " . strtolower($user['nombre_planta']) . " - Depto. " . $user['nombre'];
      $estatusRH = ($user['perfil_id'] == 1) ? 2 : 0;
      $val = 2; // TIENE INCENTIVOS PROPIOS
    }

    // INCLUCION DE FILTROS PARA LA TABLA
    View::set('nomina',$this->getNominas());
    View::set('idPuesto',$this->getPuestos());
    View::set('idEmpresa',$this->getEmpresas());
    View::set('idUbicacion',$this->getUbicacion());
    View::set('idDepartamento',$this->getDepartamentos());
    // TERMINO DE FILTROS DE LA TABLA

    View::set('form',"/Incentivo/propiosSemanalesHistoricos/");
    View::set('msjPeriodo',$msjPeriodofechas);
    View::set('tipoPeriodo',$tituloVista); // Identificacion del periodo
    View::set('tituloIncentivos',"Semanales");
    View::set('option',$this->getAllperiodosSemanalesHistoricos($idPeriodo)); // Optiene todos los periodos procesados(historicos) semanales
    View::set('tabla',$this->getTabla("Semanal",$idPeriodo,"propiosSemanalesHistoricos", $user['perfil_id'], $user['catalogo_departamento_id'], $user['catalogo_planta_id'], $val, $user['nombre_planta'] )); // Obtener la tabla
    View::set('busqueda',"/Incentivo/propiosSemanalesHistoricos/");
    View::set('header',$this->_contenedor->header($this->getHeader()));
    View::set('footer',$this->_contenedor->footer($this->getFooter()));
    View::render("incentivos_historicos");
  }

  public function propiosQuincenalesHistoricos(){
    $user = GeneralDao::getDatosUsuario($this->__usuario);

    if(empty($_POST)){
      $ultimoPeriodoHistorico = IncentivoDao::getUltimoPeriodoHistorico("QUINCENAL");
      $idPeriodo = $ultimoPeriodoHistorico['prorrateo_periodo_id'];
      $msjPeriodofechas = $this->getPeriodo("QUINCENAL", 1);
    }else{
      $idPeriodo = MasterDom::getData('tipo_periodo');
      $msjPeriodofechas = $this->getIdPeriodohistorico($idPeriodo);
    }

    if($user['perfil_id'] == 6){ // Si el usuario es de RH
      $tituloVista = "TODOS los Incentivos de Recursos Humanos - Planta " . strtolower($user['nombre_planta']) . " - Depto. " . $user['nombre'];
      $val = ($user['catalogo_planta_id'] == 1) ? 1 : 4;
    }

    if($user['perfil_id'] == 1){ // Si el usuario es de RH
      $tituloVista = "TODOS los Incentivos de ROOT - Planta " . strtolower($user['nombre_planta']) . " - Depto. " . $user['nombre'];
      $val = 2; // TIENE INCENTIVOS PROPIOS
    }
        
    View::set('tipoPeriodo',$tituloVista); // Identificacion del periodo
    View::set('tituloIncentivos',"Semanales");
    View::set('msjPeriodo',$msjPeriodofechas);
    View::set('option',$this->getPeriodosHistoricos("QUINCENAL",$idPeriodo)); // Optiene todos los periodos procesados(historicos) semanales
    View::set('tabla',$this->getTabla("Quincenal",$idPeriodo,"propiosQuincenalesHistoricos", $user['perfil_id'], $user['catalogo_departamento_id'], $user['catalogo_planta_id'], $val, $user['nombre_planta'] )); // Obtener la tabla
    View::set('busqueda',"/Incentivo/propiosQuincenalesHistoricos/");
    View::set('header',$this->_contenedor->header($this->getHeader()));
    View::set('footer',$this->_contenedor->footer($this->getFooter()));
    View::render("incentivos_historicos");
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
      
      <script>
        $(document).ready(function(){

          $("#myform-1").validate({
            rules:{
              status:{
                digits: true
              }
            },
            messages:{
              status:{
                digits: "Este campo que requiere solo campos de tipo texto"
              }
            }
          });//fin del jquery validate
        });
      </script>
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
      foreach (GeneralDao::getColaboradores($tipo, $perfilUsuario, $catalogDepartamentoId, $catalogoPlantaId, $estatusRH, $nombrePlanta, $filtros) as $key => $value) {  
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
          <td style="text-align:center; vertical-align:middle;">
html;
          $tabla.= $this->getIncentivos($value['catalogo_colaboradores_id']);
      $tabla.=<<<html
          </td>
          <td style="text-align:center; vertical-align:middle;">
            <a href="/Incentivo/getIncentivosColaborador/{$value['catalogo_colaboradores_id']}/{$idPeriodo}/{$vista}" type="submit" name="id_empresa" class="btn btn-primary"><i class="fa fa-plus"></i> </a>
          </td>
        </tr>
html;
      }
      return $tabla;
    }

    /*
    Obteniene los incentivos de cada colaborador
    @idColaborador-> Id del colaborador, para busqueda de incentivos
    */
    public function getIncentivos($idColaborador){
      $htmlIncentivos = "";
      foreach (IncentivoDao::getIncentivosPorColabordor($idColaborador) as $key => $value) {
        $htmlIncentivos .=<<<html
        <p> 
          <span class="fa fa-circle" style="color:{$value['color']}">
          </span> </a> {$value['nombre']}: <i>$ {$value['cantidad']}</i>
        </p>
html;
      }
      return $htmlIncentivos;
    }

    /*
      @$tipo -> SEMANAL o QUINCENAL
      @status -> 1 Abierto y 0 Cerrado
    */
    public function getPeriodo($tipo, $status){
      $periodo = IncentivoDao::searchPeriodos($tipo, $status);

      if($status == 1){
        View::set('error',"Error Periodo");
        View::set('tituloError',"Al parecer no hay periodo Abierto");
        $display = "style=\"display:none;\" ";
        View::set('visualizar', $display);
        View::set('mensajeError',"Debe existir un periodo Abierto, para checar los incentivos");
        View::render("error");
        exit;
      }elseif($status == 2){
        View::set('error',"Periodo");
        View::set('tituloError',"El periodo esta en proceso de termino");
        $display = "style=\"display:none;\" ";
        View::set('visualizar', $display);
        View::set('mensajeError',"Debes esperar a que el periodo termine completamente para iniciar uno nuevo.");
        View::render("error");
        exit;
      }else{
        $texto = ($status == 0) ? "Abierto" : "Cerrado";
        $statusColor = ($status == 0) ? "label label-succes" : "label label-danger";
        $fechaIni = MasterDom::getFecha($periodo[0]['fecha_inicio']);
        $fechaFin = MasterDom::getFecha($periodo[0]['fecha_fin']);
      $htmlPeriodo = <<<html
      <b>( {$fechaIni} al {$fechaFin} )</b> <label class="{$statusColor}">periodo {$texto}</label>
html;
      } 
      return $htmlPeriodo;
    }

    public function getPeriodoHistorico($tipo, $status){
      $periodo = IncentivoDao::searchLastPeriodos($tipo, $status);

        $texto = ($status == 0) ? "Abierto" : "Cerrado";
        $statusColor = ($status == 0) ? "label label-succes" : "label label-danger";
        $fechaIni = MasterDom::getFecha($periodo[0]['fecha_inicio']);
        $fechaFin = MasterDom::getFecha($periodo[0]['fecha_fin']);
      $htmlPeriodo = <<<html
      <b>( {$fechaIni} al {$fechaFin} )</b> <label class="{$statusColor}">periodo {$texto}</label>
html;
      return $htmlPeriodo;
    }

    /*
      @$tipo -> SEMANAL o QUINCENAL
      @status -> 1 Abierto y 0 Cerrado
    */
    public function getIdPeriodo($tipo, $status){
      $periodo = IncentivoDao::searchPeriodos($tipo, $status);
      return $periodo[0]['prorrateo_periodo_id'];
    }

    public function getIdPeriodohistorico($idPeriodo){
      $periodo = IncentivoDao::getPeriodo($idPeriodo);
      $fechaIni =  MasterDom::getFecha($periodo['fecha_inicio']);
      $fechaFin =  MasterDom::getFecha($periodo['fecha_fin']);
      if($periodo['status'] == 0){
        $html = <<<html
        <span><b>( {$fechaIni} al {$fechaFin} )</b><span class="label label-success">periodo Abierto</span></span>
html;
      }else{
        $html = <<<html
        <span><b>( {$fechaIni} al {$fechaFin} )</b><span class="label label-danger">periodo Cerrado</span></span>
html;
      }
      return $html;
    }

    /*
    Obtiene los incentivos SEMANALES O QUINCENALES, que ya han sido procesados
    */
    public function getAllPeriodoSemanales($periodo, $tipo){
      $option = "";
      foreach (IncentivoDao::getAllperiodosSemanales() as $key => $value) {
        $selected = ($value['prorrateo_periodo_id'] == $periodo) ? "selected" : "";
        $abierto = ($value['status'] == 0) ? "Abierto" : "Cerrado";
        $fechaIni = MasterDom::getFecha($value['fecha_inicio']);
        $fechaFin = MasterDom::getFecha($value['fecha_fin']);
        if($tipo == "edit"){
          if($selected == "selected"){
            $option .=<<<html
          <option {$selected} value="{$value['prorrateo_periodo_id']}">SEMANAL {$abierto} ({$fechaIni}) al ({$fechaFin}) </option>
html;
          }
        }

        if($tipo == "add"){
          $option .=<<<html
          <option {$selected} value="{$value['prorrateo_periodo_id']}">SEMANAL {$abierto} ({$fechaIni}) al ({$fechaFin}) </option>
html;
        }
      }
      return $option;
    }

    public function getAllPeriodos($periodo){
      $periodos = IncentivoDao::getTipoPeriodo($tipo);
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

    public function getAllperiodosSemanalesHistoricos($id){
      $option = "";
      foreach (IncentivoDao::getAllperiodosSemanales($id) as $key => $value) {
        $selected = ($value['prorrateo_periodo_id'] == $id) ? "selected" : "";
        $fechaIni = MasterDom::getFecha($value['fecha_inicio']);
        $fechaFin = MasterDom::getFecha($value['fecha_fin']);
        $status = ($value['status'] == 1) ? "<label class='btn btn-danger'> Cerrado </label>" : "<label class='btn btn-success'> Abierto </label>";
        if($value['status'] == 1){
          $option .=<<<html
            <option {$selected} value="{$value['prorrateo_periodo_id']}">({$fechaIni}) al ({$fechaFin}) {$status}</option>
html;
        }
      }
      return $option;
    }

    /*
    Muestra el estatus del periodo y las fechas 
    */
    public function getTextoPeriodo($periodo, $regreso, $perfilUser){
      $fechaIni = MasterDom::getFecha($periodo['fecha_inicio']);
      $fechaFin = MasterDom::getFecha($periodo['fecha_fin']);

      $statusPeriodo = ($periodo['status'] == 0) ? "abierto":"cerrado";
      $statusPanel = ($periodo['status'] == 0) ? "panel-success":"panel-danger";
      $btnBody = ($periodo['status'] == 0) ? "btn-success":"btn-danger";

      $html = <<<html
      <div class="row">
        <div class="col-md-1 col-sm-1 col-xs-12">
          <div class="pane">
            <a href="/Incentivo/{$regreso}/" style="color:white; text-align:center; "> 
              <div class="panel-body btn-primary" style="padding-top:25px; padding-bottom: 25px;" >
                <span style="font-size: 30px;" class="glyphicon glyphicon-chevron-left"></span>
              </div>
            </a> 
          </div>
        </div>

        <div class="col-md-10 col-sm-10 col-xs-12" >
          <div class="panel-footer" style="background:#ffffff;" style="padding-top:25px; padding-bottom: 25px;">
            <b><h3> ({$fechaIni}) al ({$fechaFin})</h3></b><br>
          </div>
        </div>

        <div class="col-md-1 col-sm-1 col-xs-12">
          <div class="panel {$panelSuccess}">
            <div class="panel-body {$btnBody}" style="padding-top:25px; padding-bottom: 25px;" ><b>Periodo </b><br>{$statusPeriodo}</div>
          </div>
        </div>
      </div>
html;
      return $html;
    }

    public function getHorasExtra($idColaborador, $idPeriodo){
    $horasExtra = IncentivoDao::getHorasExtraPeriodo($idColaborador, $idPeriodo);

    if($horasExtra['horas_extra']==0)
      $horasExtra = $horasExtra['horas_extra']."0 horas";
    else
      $horasExtra = $horasExtra['horas_extra'] . " horas";

    return $horasExtra;
    }

    /*
    Coloca la vista para poner horas extra y eliminar las
    */
    public function setHorasExtra($idColaborador,$idPeriodo,$status,$regreso,$value){
      if($value==1){
        $html = <<<html
      <div>
            <form name="form-add" id="add-horas-extra-m" action="/Incentivo/updateHorasExtra" method="POST">
              <input type="hidden" value="{$idColaborador}" name="colaborador_id">
              <input type="hidden" value="{$idPeriodo}" name="prorrateo_periodo_id">
              <input type="hidden" value="{$status}" name="status">
              <input type="hidden" value="{$regreso}" name="regreso">
                <div class="row">
                  <div class="col-md-12 col-sm-12 col-xs-12">
                    <input type="text" class="form-control" name="horas_extra" id="horas_extra">
                    <!--select class="form-control" name="horas_extra" id="horas_extra">
                      <option value="" disabled selected>Horas extra</option-->
html;
        $html .=<<<html
                    <!--/select-->
                  </div>
                  <div class="col-md-12 col-sm-12 col-xs-12"> <br>
                    <input type="hidden" class="btn btn-success" value="Modificar Horas Extra">
                    <button type="button" class="btn btn-info"  id="add-h-e-p"> Agregar Horas Extra </button>
                  </div>
                </div>

            </form>
          </div>
html;
      }else{
        $html = <<<html
      <div>
            <form name="form-add" id="add-horas-extra-m" action="/Incentivo/updateHorasExtra" method="POST">
              <input type="hidden" value="{$idColaborador}" name="colaborador_id">
              <input type="hidden" value="{$idPeriodo}" name="prorrateo_periodo_id">
              <input type="hidden" value="{$status}" name="status">
              <input type="hidden" value="{$regreso}" name="regreso">
                <div class="row">
                  <div class="col-md-12 col-sm-12 col-xs-12">
                    <input type="text" class="form-control" name="horas_extra" id="horas_extra">
                    <!--select class="form-control" name="horas_extra" id="horas_extra">
                      <option value="" disabled selected>Horas extra</option-->
html;
        $html .=<<<html
                    <!--/select-->
                  </div>
                  <div class="col-md-12 col-sm-12 col-xs-12"> <br>
                    <input type="hidden" class="btn btn-info" value="Agregar Horas Extra">
                    <button type="button" class="btn btn-info" id="add-h-e-p" > Agregar Horas Extra </button>
                  </div>
                </div>

            </form>
          </div>
html;
      }
      return $html;
    }

  public function getValor(){
    $horas = MasterDom::getData("horas_extra");
    $data = new \stdClass();

    $data->status = is_numeric($horas);
    $data->cantidad = $horas;

    echo json_encode($data);
  }

  public function getIncentivosColaborador($idColaborador, $idPeriodo, $tipo){

    $extraFooter=<<<html
      <script>
        $(document).ready(function(){

            // $('#horas_extra').on('input', function () { 
            //   var pattern = /[^0-9\.]/g; // cualquier cosa que no sea numero y punto;
            //   this.value = this.value.replace(pattern, '');
            // });

            $('#add-h-e-p').on('click', function() {
              $.ajax({
                data: { horas_extra : $("#horas_extra").val() },
                url: "/Incentivo/getValor/",
                type: "post",
                success: function(data){
                  
                  var obj = jQuery.parseJSON(data);
                  if(obj.status == true){
                    alertify.confirm('Se guardara la siguiente cantidad de: ' + obj.cantidad, function(response){
                    if(response){
                        $('#add-horas-extra-m').attr('target', '');
                        $('#add-horas-extra-m').attr('action', '/Incentivo/updateHorasExtra');
                        $("#add-horas-extra-m").submit();
                        alertify.success("Se ha guardado las horas extra");
                      }
                    });
                  }else{
                    alertify.confirm('Alerta: El valor debe ser numerico!!!');
                    $("#horas_extra").val("");
                  }
                }
              });
            });
            

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

            $("#delete").click(function(){
              var seleccionados = $("input[name='borrar[]']:checked").length;
              if(seleccionados>0){
                alertify.confirm('¿Segúro que desea eliminar lo seleccionado?', function(response){
                  if(response){
                    $('#all').attr('target', '');
                    $('#all').attr('action', '/Incentivo/deleteIncentivosColaborador');
                    $("#all").submit();
                    alertify.success("Se ha eliminado correctamente");
                  }
                });
              }else{
                alertify.confirm('Selecciona al menos uno para eliminar');
              }
            });

            $("#domingo_de_procesos").bootstrapSwitch();
            $("#domingo_laborado").bootstrapSwitch();
            $("#incentivo_de_noche").bootstrapSwitch();
            $("#eliminar-incentivos").bootstrapSwitch();
            $("#agregar-horas-extra").bootstrapSwitch();
            $("#update-horas-extra").bootstrapSwitch();
            $("#update-btn-horas-extra").hide();

            $("#domingoProcesos").bootstrapSwitch();
            $("#domingoLaborado").bootstrapSwitch();
			$("#domingoEspecial").bootstrapSwitch();

            $('input[name="domingoProcesos"]').on('switchChange.bootstrapSwitch', function(event, state) {
              if(state){
                $('input[name="domingoLaborado"]').bootstrapSwitch('state', false, false);
				$('input[name="domingoEspecial"]').bootstrapSwitch('state', false, false);
              }else{
              }
            });

            $('input[name="domingoLaborado"]').on('switchChange.bootstrapSwitch', function(event, state) {
              if(state){
                $('input[name="domingoProcesos"]').bootstrapSwitch('state', false, false);
				$('input[name="domingoEspecial"]').bootstrapSwitch('state', false, false);
              }else{
              }
            });
			
			 $('input[name="domingoEspecial"]').on('switchChange.bootstrapSwitch', function(event, state) {
            if(state){
              $('input[name="domingoProcesos"]').bootstrapSwitch('state', false, false);
			  $('input[name="domingoLaborado"]').bootstrapSwitch('state', false, false);
            }else{
            }
          });

            $('.noRepetitivo').click(function(){
              alert(1);
            });
html;
        for($i = 1; $i <= count(IncentivoDao::getIncentivoColaborador($idColaborador)); $i++){
          $extraFooter .=<<<html
          var total{$i} = + $(".tp{$i}").val() || 0; $(".ta{$i}").keyup(function() { 
            var vat{$i} = + $(this).val() || 0; 
            $(".tp{$i}").val(vat{$i} * total{$i});
          });
          
          $("#total{$i}").change(function() { 
            $("#cantidadTotalIncentivo{$i}").val($("#total{$i}").val()); 
          }); 
          
          $("#klient_open_4_end").change(function(){
            if($(this).prop("checked")){
              $("#nightlife_open_7_end").prop("disabled",true);
            }else{
              $("#nightlife_open_7_end").prop("disabled",false);
            }
          });
          $(".switch").bootstrapSwitch();

          $('input[name="uno{$i}"]').on('switchChange.bootstrapSwitch', function(event, state) {
            if(state){
              $(".add{$i}").val("ok"); 
              var cantidad{$i} = $(".ta{$i}").val();
              if(cantidad{$i}<1){ 
                $(".ta{$i}").val(1); 
              }
            }else{
              $(".add{$i}").val("none"); 
            }
          });
html;
        }
$extraFooter .=<<<html
        });
      </script>
html;

    $regreso = <<<html
      <a href="/Incentivo/{$tipo}/" style="color:white; background:#235262;"> <span class="glyphicon glyphicon-chevron-left"></span>  REGRESAR</a>
html;

    View::set('regreso',$regreso);
    
    $user = GeneralDao::getDatosUsuario($this->__usuario);
    $colaborador = IncentivoDao::getColaborador($idColaborador); // Obtiene la informacion del colaborador a a buscar

    $periodo = IncentivoDao::getPeriodo($idPeriodo); // Busca los datos del periodo
    $incentivoAsignado = IncentivoDao::getIncentivoColaborador($idColaborador); 
    
    $tablaIncentivosParaAsignar = $this->getTablaIncentivosParaAsignar($incentivoAsignado, $idPeriodo);
    $tablaIncentivosResumen = $this->getTablaIncentivosResumen($idColaborador, $idPeriodo, $colaborador['catalogo_lector_id']);
    $tablaEliminarIncentivos = $this->getTablaIncentivosEliminar($idColaborador, $idPeriodo, $colaborador['catalogo_lector_id']);

    $cantidad = IncentivoDao::getIncentivosColaboradorResumen($idColaborador, $idPeriodo);

    /*
      MUESTRA SI EL COLABORADOR TIENE FALTAS Y PUEDE IR A AGREGAR INCIDENCIAS PARA NO TERNER FALTAS 
    */
    View::set('resumenes',$this->setTablaAsistencia($idColaborador,$idPeriodo));

    $faltasPorPeriodoColaborador = $this->getFaltasColaborador($idColaborador,$idPeriodo);
    View::set('faltasPeriodo', "" . $faltasPorPeriodoColaborador);
    
    /*
      Creacion de seccion para los de PAM
    */
    if($colaborador['catalogo_lector_id'] == 3){
      $incentivoBotes = IncentivoDao::getIncentivoBotes($idColaborador);
      $incentivoMeta = IncentivoDao::getIncentivoBotesMeta($idColaborador);
      $cantidad = (!empty($incentivoBotes['cantidad'])>0) ? "{$incentivoBotes['nombre']}, {$incentivoBotes['descripcion']}:  $ {$incentivoBotes['cantidad']}" : "$ 0";
      $meta = (!empty($incentivoMeta['cantidad'])>0) ? " {$incentivoMeta['cantidad']} botes" : "0 botes";
      View::set('cantidad_inicial',$incentivoBotes['cantidad']);
      View::set('botes', $cantidad);
      View::set('incentivoBotes',$incentivoBotes);
      $extraFooter .=<<<html
        <script>
          $(document).ready(function(){
            
            // INICIO DE CONDICIONES
            // cantidad_botes_yema
            // cantidad_botes_precio_yema
            // contidad_total_yema

            $('#select_cantidad_botes_1').on('input', function () { 
              var pattern = /[^0-9\.]/g; // cualquier cosa que no sea numero y punto;
              this.value = this.value.replace(pattern, '');
            });

            $('#select_cantidad_botes_2').on('input', function () { 
              var pattern = /[^0-9\.]/g; // cualquier cosa que no sea numero y punto;
              this.value = this.value.replace(pattern, '');
            });

            $('#select_cantidad_botes_3').on('input', function () { 
              var pattern = /[^0-9\.]/g; // cualquier cosa que no sea numero y punto;
              this.value = this.value.replace(pattern, '');
            });

            
            // ESTE ES LA OPERACION PARA LA PRIMERA LINEA DE CLARA
              $('#input_cantidad_botes_precio_1').on('keyup keydown keypress change', function() {
                if ($(this).val() == '') {
                  //alert("vacio");
                } else {
                  var data1 = $("#select_cantidad_botes_1").val();
                  var valor1 = $("#input_cantidad_botes_precio_1").val();
                  var data2 = $("#select_cantidad_botes_2").val();
                  var valor2 = $("#input_cantidad_botes_precio_2").val();
                  var data3 = $("select#select_cantidad_botes_3").val();
                  var valor3 = $("#input_cantidad_botes_precio_3").val();
                  var ope1 = data1 * valor1;
                  var ope2 = data2 * valor2;
                  var ope3 = data3 * valor3;

                  var resultado = ope1 + ope2 + ope3;
                  $('.input_contidad_total_1').val(ope1);
                  $('.cantidad_asignar_botes').val(resultado);
                }
              });
              $('#select_cantidad_botes_1').on('keyup keydown keypress change', function() {
                if ($(this).val() >= 0) {
                  var data1 = $("#select_cantidad_botes_1").val();
                  var valor1 = $("#input_cantidad_botes_precio_1").val();
                  var data2 = $("#select_cantidad_botes_2").val();
                  var valor2 = $("#input_cantidad_botes_precio_2").val();
                  var data3 = $("select#select_cantidad_botes_3").val();
                  var valor3 = $("#input_cantidad_botes_precio_3").val();
                  var ope1 = data1 * valor1;
                  var ope2 = data2 * valor2;
                  var ope3 = data3 * valor3;

                  var resultado = ope1 + ope2 + ope3;
                  $('.input_contidad_total_1').val(ope1);
                  $('.cantidad_asignar_botes').val(resultado);
                }
              });

            // ESTE ES LA OPERACION PARA LA PRIMERA LINEA DE YEMA
              $('#input_cantidad_botes_precio_2').on('keyup keydown keypress change', function() {
                if ($(this).val() == '') {
                  //alert("vacio");
                } else {
                  var data1 = $("#select_cantidad_botes_1").val();
                  var valor1 = $("#input_cantidad_botes_precio_1").val();
                  var data2 = $("#select_cantidad_botes_2").val();
                  var valor2 = $("#input_cantidad_botes_precio_2").val();
                  var data3 = $("select#select_cantidad_botes_3 option").filter(":selected").val();
                  var valor3 = $("#input_cantidad_botes_precio_3").val();
                  var ope1 = data1 * valor1;
                  var ope2 = data2 * valor2;
                  var ope3 = data3 * valor3;

                  var resultado = ope1 + ope2 + ope3;
                  $('.input_contidad_total_2').val(ope2);
                  $('.cantidad_asignar_botes').val(resultado);
                }
              });
              $('#select_cantidad_botes_2').on('keyup keydown keypress change', function() {
                if ($(this).val() >= 0) {
                  var data1 = $("#select_cantidad_botes_1").val();
                  var valor1 = $("#input_cantidad_botes_precio_1").val();
                  var data2 = $("#select_cantidad_botes_2").val();
                  var valor2 = $("#input_cantidad_botes_precio_2").val();
                  var data3 = $("select#select_cantidad_botes_3").val();
                  var valor3 = $("#input_cantidad_botes_precio_3").val();
                  var ope1 = data1 * valor1;
                  var ope2 = data2 * valor2;
                  var ope3 = data3 * valor3;

                  var resultado = ope1 + ope2 + ope3;
                  $('.input_contidad_total_2').val(ope2);
                  $('.cantidad_asignar_botes').val(resultado);
                }
              });

            // ESTE ES LA OPERACION PARA LA PRIMERA LINEA DE HUEVO LIQUIDO
              $('#input_cantidad_botes_precio_3').on('keyup keydown keypress change', function() {
                if ($(this).val() == '') {
                  //alert("vacio");
                } else {
                  var data1 = $("#select_cantidad_botes_1").val();
                  var valor1 = $("#input_cantidad_botes_precio_1").val();
                  var data2 = $("#select_cantidad_botes_2").val();
                  var valor2 = $("#input_cantidad_botes_precio_2").val();
                  var data3 = $("#select_cantidad_botes_3").val();
                  var valor3 = $("#input_cantidad_botes_precio_3").val();
                  var ope1 = data1 * valor1;
                  var ope2 = data2 * valor2;
                  var ope3 = data3 * valor3;

                  var resultado = ope1 + ope2 + ope3;
                  $('.input_contidad_total_3').val(ope3);
                  $('.cantidad_asignar_botes').val(resultado);
                }
              });
              $('#select_cantidad_botes_3').on('keyup keydown keypress change', function() {
                if ($(this).val() >= 0) {
                  var data1 = $("#select_cantidad_botes_1").val();
                  var valor1 = $("#input_cantidad_botes_precio_1").val();
                  var data2 = $("#select_cantidad_botes_2").val();
                  var valor2 = $("#input_cantidad_botes_precio_2").val();
                  var data3 = $("#select_cantidad_botes_3").val();
                  var valor3 = $("#input_cantidad_botes_precio_3").val();
                  var ope1 = data1 * valor1;
                  var ope2 = data2 * valor2;
                  var ope3 = data3 * valor3;

                  var resultado = ope1 + ope2 + ope3;
                  $('.input_contidad_total_3').val(ope3);
                  $('.cantidad_asignar_botes').val(resultado);
                }
              });

          });
      </script>
html;
      $extraFooter1 .=<<<html
        <script>
          $(document).ready(function(){
            $('#cantidad_botes_1').on('keyup keydown keypress change', function() {
              $('#input_contidad_total_1').val($('#cantidad_botes_1').val() * $('#input_cantidad_botes_precio_1').val());
              $('#input_contidad_total_1_1').val($('#cantidad_botes_1').val() * $('#input_cantidad_botes_precio_1').val());
            });
            $('#input_cantidad_botes_precio_1').on('keyup keydown keypress change', function() {
              $('#input_contidad_total_1').val($('#cantidad_botes_1').val() * $('#input_cantidad_botes_precio_1').val());
              $('#input_contidad_total_1_1').val($('#cantidad_botes_1').val() * $('#input_cantidad_botes_precio_1').val());
            });
            $('#input_contidad_total_1_1').on('keyup keydown keypress change', function() {

              alert(0);
            });

          });
        </script>
html;

      $botesMetasObtener = IncentivoDao::getMetaBotes($idPeriodo);
      //if(count($botesMetasObtener)>0 && $faltasPorPeriodoColaborador == 0){
        $botesAsignar = "";
        foreach ($botesMetasObtener as $key => $value) {
          $bClara = ($value['clara'] == 1) ? "bote":"botes";
          $bYema = ($value['yema'] == 1) ? "bote":"botes";
          $bHliquido = ($value['huevo_liquido'] == 1) ? "bote":"botes";
          $botesAsignar .=<<<html
            <tr>
              <td>{$value['clara']} {$bClara} de clara como meta</td>
              <td>{$value['yema']} {$bYema} de yema como meta</td>
              <td>{$value['huevo_liquido']} {$bHliquido} de huevo liquido como meta</td>
            </tr>
html;
        }
        View::set('botesAsignar',$botesAsignar);
      //}else{
        View::set('tituloFaltas',"");
        //View::set('textoBotesNoAsignados',"<h2><b style=\"color:red;\">Lo sentimos pero no hay metas de botes agregada a este periodo</b></h2>");
        View::set('displayBotesMetas','display:none;');
      //}

    }else{
      View::set('displayBotes', 'display:none;');
    }

    /*
      finalizacion de la seccion de PAM
    */


    $hExtra = $this->getHorasExtra($idColaborador, $idPeriodo);
    $calculoHorasExtra = 0;
    if($hExtra > 0 ){
      $salarioDiario = IncentivoDao::getSalarioDiario($colaborador['clave_noi']);
      //echo "<pre>";print_r($colaborador['numero_identificador']);echo "</pre>";
      $calculoHorasExtra = $this->getCalculoHorasExtra($salarioDiario['sal_diario'], $hExtra);
      View::set('salMinimo',$salarioDiario['sal_diario']);
      View::set('calculoHorasExtra',"$" . $calculoHorasExtra);      
      View::set('seleccionHorasExtra',$this->setHorasExtra($idColaborador, $idPeriodo,"MODIFICAR", $tipo, 1));
    }else{
      $salarioDiario = IncentivoDao::getSalarioDiario($colaborador['clave_noi']);
      //echo "<pre>";print_r($colaborador['numero_identificador']);echo "</pre>";
      View::set('salMinimo',$salarioDiario['sal_diario']);
      View::set('calculoHorasExtra',"$ 0.00");      
      View::set('seleccionHorasExtra',$this->setHorasExtra($idColaborador, $idPeriodo,"INSERTAR", $tipo, 0));
    }

    if($calculoHorasExtra != 0){
      View::set('setcalculoHorasExtraPrecio', '$'. $calculoHorasExtra);
    }else{
      View::set('setcalculoHorasExtraPrecio', '$ 0.00');
    }



    

    $domingoProcesos = IncentivoDao::getDomingoProcesos($idColaborador, $idPeriodo);
    $domingoLaborado = IncentivoDao::getDomingoLaborado($idColaborador, $idPeriodo);
	//$domingoEspecial = IncentivoDao::getDomingoEspecial($idColaborador, $idPeriodo);

    if($tipo == 'historicosSemanales'){
      View::set('displayBotesHistoricos', 'display:none;' );
    }

    $domingo = "";
    if($domingoProcesos['domigo_procesos']>0){
      $domingo = $domingoProcesos['domigo_procesos'];
      View::set('domingo',"$ " . $domingoProcesos['domigo_procesos']);
    }elseif($domingoLaborado['domingo_laborado']>0){
      $domingo = $domingoLaborado['domingo_laborado'];
      View::set('domingo',"$ " . $domingoLaborado['domingo_laborado']);
    }elseif($domingoEspecial['domingo_especial']>0){
		$domingo = $domingoEspecial['domingo_especial'];
      View::set('domingo',"$ " . $domingoEspecial['domingo_especial']);
	}
	else{
      $extraFooter .=<<<html
<script> $(document).ready(function(){ $(".collapse-link-domingos").click(); }); </script>
html;
      View::set('domingo',"$ 0.00");
    }



    $valorTotal = 0;
    $cantidad = IncentivoDao::getSumaIncentivosAsginados($idColaborador, $idPeriodo);
    $nuevoResultado = number_format($cantidad['cantidad_incentivos_asignados'], 2, '.', '');
    $valorTotal = $domingo + $calculoHorasExtra;
    View::set('valorTotal',"$ " . $valorTotal);

    if($cantidad['cantidad_incentivos_asignados'] > 0){
      $extraFooter .=<<<html
<script> $(document).ready(function(){ $(".collapse-link-1-incentivos").click(); }); </script>
html;
    }

    if(empty($cantidad['cantidad_incentivos_asignados'])){
      $extraFooter .=<<<html
<script> $(document).ready(function(){ $(".collapse-link-incentivos-asignados").click(); }); </script>
html;
    }

    $extraFooter.=<<<html
<script> 
  $(document).ready(function(){ 
    $("#add").click(function() {
      $(".collapse-link-incentivos-asignados").click();
      $(".collapse-link-1-incentivos").click();
    });
  }); 
</script>
html;


    

    $showTextBtnActualizar = ( count($domingoProcesos)>0 || count($domingoLaborado)>0 || count($domingoEspecial)>0) ? "Actualizar datos" : "Agrear Valores";
    $accionesComplementarias = ( count($domingoProcesos)>0 || count($domingoLaborado)>0 || count($domingoEspecial)>0) ? "updateDomingos" : "addDomingos";
    $btnAccionesComplementarias = ( count($domingoProcesos)>0 || count($domingoLaborado)>0 || count($domingoEspecial)>0) ? "btn-info" : "btn-success";
    // Validar que estos datos ya existen en la base de datos
    $checkeddomingoProcesos = (count($domingoProcesos)>0) ? "checked" : "";    
    $checkeddomingoLaborado = (count($domingoLaborado)>0) ? "checked" : "";
	$checkeddomingoEspecial = (count($domingoEspecial)>0) ? "checked" : "";
    View::set('showTextBtnActualizar',$showTextBtnActualizar); // Texto del buton de agregar datos
    // Validar que estos datos ya existen en la base de datos
    $checkeddomingoProcesos = (count($domingoProcesos)>0) ? "checked" : "";    
    $checkeddomingoLaborado = (count($domingoLaborado)>0) ? "checked" : "";
	$checkeddomingoEspecial = (count($domingoEspecial)>0) ? "checked" : "";
    View::set('showTextBtnActualizar',$showTextBtnActualizar); // Texto del buton de agregar datos
    View::set('checkdomingoProcesos', $checkeddomingoProcesos); // value CHECKED del input checked
    View::set('checkdomingoLaborado', $checkeddomingoLaborado); // value CHECKED del input checked
	View::set('checkdomingoEspecial', $checkeddomingoEspecial); // value CHECKED del input checked
    // Valores del complementarios 
    View::set('domingoProcesos', $this->calculoDomingoProcesos(GeneralDao::getDatosColaborador($idColaborador))); // Muestra la cantidad que puede tener por domingos procesados
    View::set('domingoLaborado', $this->calculoDomingoLaborado(GeneralDao::getDatosColaborador($idColaborador))); // Muestra la cantidad que puede tener por domingo laborales
	View::set('domingoEspecial', $this->calculoDomingoEspecial(GeneralDao::getDatosColaborador($idColaborador))); // Muestra la cantidad que puede tener por domingo especial
    View::set('accionesComplementarias', $accionesComplementarias); // Envia el formulario si hay acciones complementarias o no 
    View::set('btnAccionesComplementarias',$btnAccionesComplementarias); // Muestra el color de la accion que se realizara

    View::set('btnAddIncentivos',($tipo=="historicosSemanales") ? "display:none;":"");
    View::set('btnIncentivos', (count($cantidad) > 0) ? "INSERTAR INCENTIVOS":"INSERTAR INCENTIVOS");
    View::set('display', (count($cantidad) > 0) ? "":"display:none;" );
    View::set('btn', (count($cantidad) > 0) ? "success":"info" );
    View::set('msjPeriodo',$this->getIdPeriodohistorico($periodo['prorrateo_periodo_id']));
    View::set('horasExtra',$this->getHorasExtra($idColaborador, $idPeriodo)); // Obteniene la cantidad de horas extra
    View::set('cantidad',count($incentivoAsignado));
    View::set('colaboradorId',$idColaborador);
    View::set('periodoId',$idPeriodo);
    View::set('tipo',$tipo);
    $cantidad = IncentivoDao::getSumaIncentivosAsginados($idColaborador, $idPeriodo);
    $nuevoResultado = number_format($cantidad['cantidad_incentivos_asignados'], 2, '.', '');
    $he = $this->getHorasExtra($idColaborador, $idPeriodo); // Horas extra
    $incentivosR = IncentivoDao::getIncentivosColaboradorResumen($idColaborador, $idPeriodo);
    $this->muestraBotesPamLiquidos($cantidad,$idColaborador, $idPeriodo, $incentivoAsignado);
    $this->alertasPlataforma($he, $domingoProcesos['domigo_procesos'], $domingoLaborado['domingo_laborado'], $domingoEspecial['domingo_especial'], count($incentivoAsignado), count($incentivosR), $cantidad, $colaborador['catalogo_lector_id']);

    // SUMA DE PERCEPCIONES 
    $opePercepciones = $calculoHorasExtra + $domingo + $cantidad['cantidad_incentivos_asignados'];
    $opeP = number_format($opePercepciones, 2, '.', '');

    View::set('sumaTotalPercepciones',"$ " .$opePercepciones); // HOLA123

    // CANTIDAD DE HORAS EXTRA EN PESOS 


    View::set('cantidadIncentivos',"$ " . $nuevoResultado);
    View::set('colaborador',$colaborador);
    View::set('tabla',$tablaIncentivosParaAsignar);
    View::set('tablaResumenIncentivos',$tablaIncentivosResumen);
    View::set('tablaEliminarIncentivos',$tablaEliminarIncentivos);
    View::set('footer',$this->_contenedor->footer($extraFooter));
    View::render('incentivos_colaborador_roy');
  }

  public function alertasPlataforma($horasExtra, $domingoProcesos, $domingoLaborado, $domingoEspecial, $listaIncentivos, $listaIncentivosResumen, $cantidadIncentivos, $ubicacion){

    $htmlHorasExtra = "";
    $colorhtmlHorasExtra = "";
    if($horasExtra != "0 horas"){
      $htmlHorasExtra .= "<span class=\"glyphicon glyphicon-ok\"></span>";
      $colorhtmlHorasExtra .= "color:green";
    }else{
      $htmlHorasExtra .= "<span class=\"glyphicon glyphicon-remove\"></span>";
      $colorhtmlHorasExtra .= "color:red";
    }
    

    $htmlDomingo = "";
    $colorhtmlDomingo = "";
    if(!empty($domingoProcesos)){
      $htmlDomingo .= "<span class=\"glyphicon glyphicon-ok\"></span>";
      $colorhtmlDomingo .= "color:green";
    }elseif(!empty($domingoLaborado)){
      $htmlDomingo .= "<span class=\"glyphicon glyphicon-ok\"></span>";
      $colorhtmlDomingo .= "color:green";
    }elseif(!empty($domingoEspecial)){
      $htmlDomingo .= "<span class=\"glyphicon glyphicon-ok\"></span>";
      $colorhtmlDomingo .= "color:green";
    }
	else{
      $htmlDomingo .= "<span class=\"glyphicon glyphicon-remove\"></span>";
      $colorhtmlDomingo .= "color:red";
    }
    

    $htmlListaIncentivos = "";
    $colorHtmlListaIncentivos = "";
    if($listaIncentivos > 0){
      $htmlListaIncentivos .= "<span class=\"glyphicon glyphicon-ok\"></span>";
      $colorHtmlListaIncentivos .= "color:green";
    }else{
      $htmlListaIncentivos .= "<span class=\"glyphicon glyphicon-remove\"></span>";
      $colorHtmlListaIncentivos .= "color:red";
    }
    

    $htmlResumenIncentivosAsignados = "";
    $colorHtmlResumenIncentivosAsignados = "";
    if(!empty($cantidadIncentivos['cantidad_incentivos_asignados'])){
      $htmlResumenIncentivosAsignados .= "<span class=\"glyphicon glyphicon-ok\"></span>";
      $colorHtmlResumenIncentivosAsignados .= "color:green";
    }else{
      $htmlResumenIncentivosAsignados .= "<span class=\"glyphicon glyphicon-remove\"></span>";
      $colorHtmlResumenIncentivosAsignados .= "color:red";
    }
    

    View::set('alertaHorasExtra',$htmlHorasExtra);
    View::set('colorhtmlHorasExtra',$colorhtmlHorasExtra);
    View::set('alertaDomingo',$htmlDomingo);
    View::set('colorhtmlDomingo',$colorhtmlDomingo);
    View::set('alertaIncentivos',$htmlListaIncentivos);
    View::set('colorHtmlListaIncentivos',$colorHtmlListaIncentivos);
    View::set('alertaIncentivosAsignados',$htmlResumenIncentivosAsignados);
    View::set('colorHtmlResumenIncentivosAsignados',$colorHtmlResumenIncentivosAsignados);
  }

  public function muestraBotesPamLiquidos($cantidad, $idColaborador, $idPeriodo, $incentivoAsignado){
    
    // MUESTRA DE INCENTIVOS PARA LOS BOTES
    $resumenIncentivo = IncentivoDao::getIncentivosColaboradorResumen($idColaborador, $idPeriodo);
    $sum = "";
    $sumMsj = "";
    $display = "";

    $incentivoParaValidar = array();
    $incentivoDinero = array();
    $incentivoInfo = array();
    foreach ($resumenIncentivo as $key => $value){
      if($value['catalogo_incentivo_id'] == 27){
          array_push($incentivoParaValidar, 1);
          array_push($incentivoInfo, array('nombre'=>$value['nombre'], 'descripcion'=>$value['descripcion']));
      }

      if($value['catalogo_incentivo_id'] == 47)
        array_push($incentivoDinero, $value['cantidad']);
      
    }

    if(count($incentivoParaValidar) > 0){
      $botesPrecio = IncentivoDao::getPrecioCompletoBotes();
      View::set('clara_valor',$botesPrecio['clara']);
      View::set('yema_valor',$botesPrecio['yema']);
      View::set('huevo_liquido_valor',$botesPrecio['huevo_liquido']);
      View::set('display_clara',($botesPrecio['clara_activo'] == 'si') ? "" : "display:none;" );
      View::set('display_yema',($botesPrecio['yema_activo'] == 'si') ? "" : "display:none;" );
      View::set('display_huevo_liquido',($botesPrecio['huevo_liquido_activo'] == 'si') ? "" : "display:none;" );
      if(!empty($incentivoDinero[0])){
        $sumMsj = "<b>Se tiene asignado en botes, la cantidad de: <span class=\"label label-success\" style=\"color:white;\"> $ {$incentivoDinero[0]} </span></b> ";
      }else{
        $sumMsj = "<b>No tiene asignado valor monetario para botes, la cantidad es de: <span class=\"label label-danger\" style=\"color:white;\"> $ 0.00 </span></b> ";
      }
      $mensajeDeAsignacionPrecioBotes .=<<<html
          <ul>
            <li>Se pagar&aacute; completo el incentivo de botes:
              <ul>
                <li>El colaborador tiene el incentivo asignado de: <b>{$incentivoInfo[0]['nombre']}</b>, {$incentivoInfo[0]['descripcion']}</li>
              </ul>
            </li>
          </ul>
html;
    }else{
      $botesPrecio = IncentivoDao::getPrecioNoCompletoBotes();
      View::set('clara_valor',$botesPrecio['clara']);
      View::set('yema_valor',$botesPrecio['yema']);
      View::set('huevo_liquido_valor',$botesPrecio['huevo_liquido']);
      View::set('display_clara',($botesPrecio['clara_activo'] == 'si') ? "" : "display:none;" );
      View::set('display_yema',($botesPrecio['yema_activo'] == 'si') ? "" : "display:none;" );
      View::set('display_huevo_liquido',($botesPrecio['huevo_liquido_activo'] == 'si') ? "" : "display:none;" );
      if(!empty($incentivoDinero[0])){
        $sumMsj = "<b>Se tiene asignado en botes, la cantidad de: <span class=\"label label-success\" style=\"color:white;\"> $ {$incentivoDinero[0]} </span></b> ";
      }else{
        $sumMsj = "<b>No tiene asignado valor monetario para botes, la cantidad es de: <span class=\"label label-danger\" style=\"color:white;\"> $ 0.00 </span></b> ";
      }

      $mensajeDeAsignacionPrecioBotes .=<<<html
          <ul>
            <li> No se pagar&aacute; completo los botes por lo siguiente
              <ul>
                <li>No tiene el colaborador asignado el incentivos de BPM PAM </li>
              </ul>
            </li>
          </ul>
html;
    }

    //if(count($incentivoAsignado) == count($resumenIncentivo) || count($incentivoAsignado) < count($resumenIncentivo)){
      
    /*  $arrSuma = array();
      $arrData = array();
      $arrBotesExtra = array();
      foreach ($resumenIncentivo as $key => $value) {
        if($value['catalogo_incentivo_id'] == "47"){
        }elseif($value['catalogo_incentivo_id'] == "48"){
        }else{
          array_push($arrSuma, $value['cantidad']);
        }

        if($value['catalogo_incentivo_id'] == "27") array_push($arrData, $value['catalogo_incentivo_id']);
        if($value['catalogo_incentivo_id'] == "29") array_push($arrData, $value['catalogo_incentivo_id']);
        if($value['catalogo_incentivo_id'] == "47") array_push($arrBotesExtra, $value['cantidad']);
      }
      $sum = number_format(array_sum($arrSuma), 2, '.', '');
      $display = "";
      $arrBotesExtra = (!empty($arrBotesExtra[0])) ? "$" . number_format($arrBotesExtra[0], 2, '.', '') : "0";
      $botesExtraIncentivo = " - Cantidad Extra botes <span class=\"label label-info\" style=\"color:white;\"> {$arrBotesExtra}</span>";
      if(count($arrData) == 1){ // A 10 pesos 
        // LO SIGUIENTE ES PARA MODIFICAR LOS BOTES JUNTO CON EL PRECIO QUE TIENEN CADA UNO 
        $botesPrecio = IncentivoDao::getPrecioNoCompletoBotes();
        View::set('clara_valor',$botesPrecio['clara']);
        View::set('yema_valor',$botesPrecio['yema']);
        View::set('huevo_liquido_valor',$botesPrecio['huevo_liquido']);
        View::set('display_clara',($botesPrecio['clara_activo'] == 'si') ? "" : "display:none;" );
        View::set('display_yema',($botesPrecio['yema_activo'] == 'si') ? "" : "display:none;" );
        View::set('display_huevo_liquido',($botesPrecio['huevo_liquido_activo'] == 'si') ? "" : "display:none;" );
        $sumMsj = "<b> No se va agregar la cantidad completa, ya que falto un incentivo - Cantidad:<span class=\"label label-warning\" style=\"color:white;\"> $ {$sum} </span></b> {$botesExtraIncentivo}";
      }elseif(count($arrData) == 2){ // PAGO A 15
        // LO SIGUIENTE ES PARA MODIFICAR LOS BOTES JUNTO CON EL PRECIO QUE TIENEN CADA UNO 
        $botesPrecio = IncentivoDao::getPrecioCompletoBotes();
        View::set('clara_valor',$botesPrecio['clara']);
        View::set('yema_valor',$botesPrecio['yema']);
        View::set('huevo_liquido_valor',$botesPrecio['huevo_liquido']);
        View::set('display_clara',($botesPrecio['clara_activo'] == 'si') ? "" : "display:none;" );
        View::set('display_yema',($botesPrecio['yema_activo'] == 'si') ? "" : "display:none;" );
        View::set('display_huevo_liquido',($botesPrecio['huevo_liquido_activo'] == 'si') ? "" : "display:none;" );
        $sumMsj = "<b> Se va agregar la cantidad completa de: <span class=\"label label-success\" style=\"color:white;\"> $ {$sum} </span></b> {$botesExtraIncentivo}";
      }else{
        $botesPrecio = IncentivoDao::getPrecioNoCompletoBotes();
        View::set('clara_valor',$botesPrecio['clara']);
        View::set('yema_valor',$botesPrecio['yema']);
        View::set('huevo_liquido_valor',$botesPrecio['huevo_liquido']);
        $display = "display:none;";
        View::set('hiddenBotes2', $display);
        $sumMsj = "<b>No tiene incentivos asignados <span class=\"label label-warning\" style=\"color:white;\"> $ 0 </span> </b> {$botesExtraIncentivo}";
      }*/

    View::set('mensajeDeAsignacionPrecioBotes',$mensajeDeAsignacionPrecioBotes);
    View::set('hiddenBotes', $display);
    View::set('msj_botes_suma', $sumMsj);
    View::set('cantidad_nuevo_incentivo_por_cumplir_todos_incentivos',$sum);
  }

  public function addBotesAsignadosMas(){
    $colaboradorId = MasterDom::getData('colaborador_id');
    $periodoId = MasterDom::getData('prorrateo_periodo_id');
    $tipo = MasterDom::getData('regreso');
    $cantidad = MasterDom::getData('cantidad_asignar_botes');
    $cantidad1 = MasterDom::getData('cantidad_nuevo_incentivo_por_cumplir_todos_incentivos');
    $incentivo  = MasterDom::getData('incentivo');

    $clara  = (!empty(MasterDom::getData('clara_valor'))) ? MasterDom::getData('clara_valor') : 0;
    $selecionClara  = (!empty(MasterDom::getData('select_cantidad_botes_1'))) ? trim(MasterDom::getData('select_cantidad_botes_1')) :0;
    $valorTotalClara  = (!empty(MasterDom::getData('input_contidad_total_1'))) ? MasterDom::getData('input_contidad_total_1') : 0;

    $yema  = (!empty(MasterDom::getData('yema_valor'))) ? MasterDom::getData('yema_valor') : 0;
    $selecionYema  = (!empty(MasterDom::getData('select_cantidad_botes_2'))) ? trim(MasterDom::getData('select_cantidad_botes_2')) : 0;
    $valorTotalYema  = (!empty(MasterDom::getData('input_contidad_total_2'))) ? MasterDom::getData('input_contidad_total_2') : 0;

    $huevo  = (!empty(MasterDom::getData('huevo_liquido_valor'))) ? MasterDom::getData('huevo_liquido_valor') : 0;
    $selecionHuevo  = (!empty(MasterDom::getData('select_cantidad_botes_3'))) ? trim(MasterDom::getData('select_cantidad_botes_3')) : 0;
    $valorTotalHuevo  = (!empty(MasterDom::getData('input_contidad_total_3'))) ? MasterDom::getData('input_contidad_total_3') : 0;
    
    $class = "";
    $texto = "";
    if($cantidad > 0){

      $data = new \stdClass();
      $data->_colaborador_id  = MasterDom::getData('colaborador_id');
      $data->_prorrateo_periodo_id  = MasterDom::getData('prorrateo_periodo_id');
      $data->_cantidad  = MasterDom::getData('cantidad_asignar_botes');
      $data->_catalogo_incentivo_id  = MasterDom::getData('incentivo');
      $data->_precio_bote  = $clara."-".$selecionClara."-".$valorTotalClara.",".$yema."-".$selecionYema."-".$valorTotalYema.",".$huevo."-".$selecionHuevo."-".$valorTotalHuevo;//MasterDom::getData('precio_por_bote');
      $busqueda = IncentivoDao::busquedaBotesNuevos($data);
      if($busqueda < 0){
        $class = "info";
        $texto .= "la cantidad de botes es de $ {$cantidad}, como un incentivo de botes extra - ";
      }else{
        $incentivo = IncentivoDao::busquedaBotesNuevos1($data);
        $delete= IncentivoDao::deleteIncentivoBotes47($incentivo['incentivos_asignados_id']);
        $id = IncentivoDao::insertBotesNuevos($data);
        if($id > 0){
          $class = "success";
          $texto .= "Se ha agregado la cantidad de $ {$cantidad}, como un incentivo de botes extra - ";
        }else{
          $class = "danger";
          $texto = "Ups! ha ocurrido un error.";  
        }
      }
    }else{
      $class = "warning";
      $texto = "No se ha agregado ninguna cantidad de botes extra. - ";
    }

    View::set('class',$class);
    View::set('regreso',"/Incentivo/getIncentivosColaborador/{$colaboradorId}/{$periodoId}/{$tipo}/");
    View::set('mensaje',$texto);
    View::set('header',$this->_contenedor->header($extraHeader));
    View::set('footer',$this->_contenedor->footer($extraFooter));
    View::render("alerta");
  }

  // Obtiene la cantidad de horas extra que tiene en pesos el colaborador
  public function getCalculoHorasExtra($salarioDiario, $horasExtra){
    if($horasExtra > 9){
      $primeraParte = (($salarioDiario * 2) / 8) * 9;
      $segundaParte = ($horasExtra-9) * (($salarioDiario*3)/8);

      $resultado = $primeraParte + $segundaParte;
    }else{
      $resultado = (($salarioDiario * 2) / 8) * $horasExtra;
    }

    $nuevoResultado = number_format($resultado, 2, '.', '');
    return $nuevoResultado;
  }

  public function getTablaIncentivosParaAsignar($incentivoAsignado, $idPeriodo){
    $tabla = "";
    foreach ($incentivoAsignado as $key => $value) {
      $key = $key + 1;
      $dato = $this->getCantidad($value['fijo']);
      $duplicador = $this->getRepetir($value['repetitivo'], $key);
      $tabla .=<<<html
        <tr>
          <td><input type="hidden" class="add{$key}" name="data-{$key}[]" value=""  />{$key}</td> <!-- NUMERO DE INCREMENTO -->
          <td>{$value['nombre']}</td> <!-- NOMBRE DEL INCENTIVO -->
          <td>{$value['fijo']}</td> <!-- SI ES FIJO -->
          <td>{$value['repetitivo']}</td> <!-- SI EL INCENTIVO SE REPETIRA -->
          <td>$ {$value['cantidad']}</td> <!-- CANTIDAD DEL INCENTIVO -->
          <td>
            <input type="hidden" name="data-{$key}[]" value="{$value['catalogo_colaboradores_id']}">
            <input type="hidden" name="data-{$key}[]" value="{$idPeriodo}">
            <input type="hidden" name="data-{$key}[]" value="{$value['catalogo_incentivo_id']}">
            {$duplicador}
          </td>
          <td>
            <input class="tp{$key}" type="hidden" name="data-{$key}[]" value="{$value['cantidad']}"  />
            <input class="tp{$key}" type="text" value="{$value['cantidad']}" disabled />
          </td>
          <td>
            <input type="checkbox" class="switch" data-on-text="SI" data-off-text="NO" name="uno{$key}">
            <input type="hidden" name="data-{$key}[]" value="{$value['repetitivo']}"  />
          </td>
        </tr>
html;
    }
    return $tabla;
  }

  public function pagoBotes(){
    $completo = IncentivoDao::getPrecioCompletoBotes();
    $noCompleto = IncentivoDao::getPrecioNoCompletoBotes();
    View::set('completo',$completo);
    View::set('noCompleto',$noCompleto);
    View::set('header',$this->_contenedor->header());
    View::set('footer',$this->_contenedor->footer());
    View::render('pago_botes');
  }

  public function modificarBotesPrecio($id){
    $extraFooter =<<<html
      <script>
        $(document).ready(function(){
          $("#edit").validate({
            rules:{
              clara:{
                required: true
              },
              yema:{
                required: true
              },
              huevo_liquido:{
                required: true
              }
            },
            messages:{
              clara:{
                required: "Este campo es requerido"
              },
              yema:{
                required: "Este campo es requerido"
              },
              huevo_liquido:{
                required: "Este campo es requerido"
              }
            }
          });

        });
      </script>
html;

    if($id==1)
      $botes = IncentivoDao::getPrecioCompletoBotes();
    if($id==2)
      $botes = IncentivoDao::getPrecioNoCompletoBotes();

    View::set('botes',$botes);
    View::set('titulo',($id==1) ? "Editar valores para botes completos":"Editar valores para botes no completos");
    View::set('header',$this->_contenedor->header());
    View::set('footer',$this->_contenedor->footer($extraFooter));
    View::render('pago_botes_edit');
  }

  public function editPagoBotes(){
    $data = new \stdClass();
    $data->_clara = MasterDom::getData('clara');
    $data->_yema = MasterDom::getData('yema');
    $data->_huevo_liquido = MasterDom::getData('huevo_liquido');
    $data->_pago_botes_id = MasterDom::getData('pago_botes_id');
    $id = IncentivoDao::updatePrecioBotes($data);

    View::set('class','success');
    View::set('regreso',"/Incentivo/pagoBotes/");
    View::set('mensaje',"Se ha actualizado la informacion");
    View::set('header',$this->_contenedor->header());
    View::set('footer',$this->_contenedor->footer());
    View::render("alerta");

  }

  public function getTablaIncentivosResumen($idColaborador, $idPeriodo, $ubicacion){
    $incentivos = IncentivoDao::getIncentivosColaboradorResumen($idColaborador, $idPeriodo);
    if($ubicacion == 3){
      foreach ($variable as $key => $value) {
        echo "<pre>";
        print_r($value);
        echo "</pre> **** ";
      }
    }

    $tabla = "";
    foreach ($incentivos as $key => $value) {
      $key = $key + 1;
      $tabla .= "<tr>";
      if($value['catalogo_incentivo_id'] == 47){
        $tabla .=<<<html
          <td>{$key}</td> <!-- NUMERO DE INCREMENTO -->
          <td>{$value['nombre']}</td>
          <td>{$value['descripcion']}</td>
          <td>{$value['tipo']}</td>
          <td>{$value['fijo']}</td>
          <td>{$value['repetitivo']}</td>
          <td>{$value['cantidad']}</td>
html;
      }else{
        $tabla .=<<<html
          <td>{$key}</td> <!-- NUMERO DE INCREMENTO -->
          <td>{$value['nombre']}</td>
          <td>{$value['descripcion']}</td>
          <td>{$value['tipo']}</td>
          <td>{$value['fijo']}</td>
          <td>{$value['repetitivo']}</td>
          <td>{$value['cantidad']}</td>
html;
      }

      $tabla .= "</tr>";
      
    }
    return $tabla;
  }

  public function getTablaIncentivosEliminar($idColaborador, $idPeriodo, $ubicacion){

    $incentivoAsignado = IncentivoDao::getIncentivoColaborador($idColaborador);
    $incentivos = IncentivoDao::getIncentivosColaboradorResumen($idColaborador, $idPeriodo);

    if($ubicacion == 3){
      $arrDataBPM = array();
      foreach ($incentivoAsignado as $key => $value) {
        if($value['catalogo_incentivo_id'] == 27) // INCENTIVO BPM
          array_push($arrDataBPM, 1);
      }

      $arrBotesAsisnados = array(); // SE GUARDARA EL VALOR DE BPM PARA CHECAR EL CALCULO DEL INCENTIVO - 
      $arrChecarBotesAsignados = array();
      $banderaSiTieneBotes = array();
      foreach ($incentivos as $key => $value) {
        if($value['catalogo_incentivo_id'] == 47) array_push($arrBotesAsisnados, array("incentivos_asignados_id"=>$value["incentivos_asignados_id"], "asignado"=>$value["asignado"]));
        if($value['catalogo_incentivo_id'] == 47) array_push($banderaSiTieneBotes, 1); // CHECA SI SE TIENE EL INCENTIVO DE BOTES 
        if($value['catalogo_incentivo_id'] == 27) array_push($arrChecarBotesAsignados, 1); // CHECA SI EL INCENTIVO DE BPM ESTA AGREGADO YA EN LOS INCENTIVOS
        
      }


      if(count($arrChecarBotesAsignados) > 0){
        $botesPrecio = IncentivoDao::getPrecioCompletoBotes();
        $idIncentivoAsignadoColaborador =  $arrBotesAsisnados['0']['incentivos_asignados_id'];
        $cantidadValorIncentivoActual =  $arrBotesAsisnados['0']['incentivos_asignados_id'];
        $explodeArray = explode(',', $arrBotesAsisnados['0']['asignado']);
        $explodeClara = explode('-', $explodeArray[0]);
        $explodeYema = explode('-', $explodeArray[1]);
        $explodeHuevo = explode('-', $explodeArray[2]);
        $calculoClara = $botesPrecio['clara'] * $explodeClara['1'];
        $calculoYema = $botesPrecio['yema'] * $explodeYema['1'];
        $calculohuevo = $botesPrecio['huevo_liquido'] * $explodeHuevo['1'];
        $nuevosValores = "{$botesPrecio['clara']}-{$explodeClara['1']}-{$calculoClara},{$botesPrecio['yema']}-{$explodeYema['1']}-{$calculoYema},{$botesPrecio['huevo_liquido']}-{$explodeHuevo['1']}-{$calculohuevo} ";
        $nuevoValorMonetario = $calculoClara + $calculoYema + $calculohuevo;
          IncentivoDao::updateIdValorBotes($arrBotesAsisnados['0']['incentivos_asignados_id'], $nuevoValorMonetario, $nuevosValores);
      }else{
        $botesPrecio = IncentivoDao::getPrecioNoCompletoBotes();
        if(count($banderaSiTieneBotes)>0){
          $idIncentivoAsignadoColaborador =  $arrBotesAsisnados['0']['incentivos_asignados_id'];
          $cantidadValorIncentivoActual =  $arrBotesAsisnados['0']['incentivos_asignados_id'];
          $explodeArray = explode(',', $arrBotesAsisnados['0']['asignado']);
          $explodeClara = explode('-', $explodeArray[0]);
          $explodeYema = explode('-', $explodeArray[1]);
          $explodeHuevo = explode('-', $explodeArray[2]);
          $calculoClara = $botesPrecio['clara'] * $explodeClara['1'];
          $calculoYema = $botesPrecio['yema'] * $explodeYema['1'];
          $calculohuevo = $botesPrecio['huevo_liquido'] * $explodeHuevo['1'];
          $nuevosValores = "{$botesPrecio['clara']}-{$explodeClara['1']}-{$calculoClara},{$botesPrecio['yema']}-{$explodeYema['1']}-{$calculoYema},{$botesPrecio['huevo_liquido']}-{$explodeHuevo['1']}-{$calculohuevo} ";
          $nuevoValorMonetario = $calculoClara + $calculoYema + $calculohuevo;
          IncentivoDao::updateIdValorBotes($arrBotesAsisnados['0']['incentivos_asignados_id'], $nuevoValorMonetario, $nuevosValores);
        }else{
          //echo "No Tienes incentivos de botes";
        }
      }

    }
    
    $tabla = "";
    foreach ($incentivos as $key => $value) {
      $key = $key + 1;
      if($value['catalogo_incentivo_id'] == 27){
        $tabla .=<<<html
        <tr>
          <td style="background:#1dae88; color:white;"><input type="checkbox" name="borrar[]" value="{$value['incentivos_asignados_id']}"/></td>
          <td style="background:#1dae88; color:white;">{$key}</td> <!-- NUMERO DE INCREMENTO -->
          <td style="background:#1dae88; color:white;">{$value['nombre']}</td>
          <td style="background:#1dae88; color:white;">{$value['descripcion']}</td>
          <td style="background:#1dae88; color:white;">{$value['tipo']}</td>
          <td style="background:#1dae88; color:white;">{$value['fijo']}</td>
          <td style="background:#1dae88; color:white;">{$value['repetitivo']}</td>
          <td style="background:#1dae88; color:white;">$ {$value['cantidad']}</td>
        </tr>
html;
      }elseif ($value['catalogo_incentivo_id'] == 29){
        $tabla .=<<<html
        <tr>
          <td style="background:#1dae88; color:white;"><input type="checkbox" name="borrar[]" value="{$value['incentivos_asignados_id']}"/></td>
          <td style="background:#1dae88; color:white;">{$key}</td> <!-- NUMERO DE INCREMENTO -->
          <td style="background:#1dae88; color:white;">{$value['nombre']}</td>
          <td style="background:#1dae88; color:white;">{$value['descripcion']}</td>
          <td style="background:#1dae88; color:white;">{$value['tipo']}</td>
          <td style="background:#1dae88; color:white;">{$value['fijo']}</td>
          <td style="background:#1dae88; color:white;">{$value['repetitivo']}</td>
          <td style="background:#1dae88; color:white;">$ {$value['cantidad']}</td>
        </tr>
html;
      }elseif ($value['catalogo_incentivo_id'] == 47){
        $tabla .=<<<html
        <tr>
          <td style="background:#48b3d9; color:white;"><input type="checkbox" name="borrar[]" value="{$value['incentivos_asignados_id']}"/></td>
          <td style="background:#48b3d9; color:white;">{$key}</td> <!-- NUMERO DE INCREMENTO -->
          <td style="background:#48b3d9; color:white;">{$value['nombre']}</td>
          <td style="background:#48b3d9; color:white;">{$value['descripcion']}</td>
          <td style="background:#48b3d9; color:white;">{$value['tipo']}</td>
          <td style="background:#48b3d9; color:white;">{$value['fijo']}</td>
          <td style="background:#48b3d9; color:white;">{$value['repetitivo']}</td>
          <td style="background:#48b3d9; color:white;">$ {$value['cantidad']}</td>
        </tr>
html;
      }elseif ($value['catalogo_incentivo_id'] == 48){
        $tabla .=<<<html
        <tr>
          <td style="background:#48b3d9; color:white;"><input type="checkbox" name="borrar[]" value="{$value['incentivos_asignados_id']}"/></td>
          <td style="background:#48b3d9; color:white;">{$key}</td> <!-- NUMERO DE INCREMENTO -->
          <td style="background:#48b3d9; color:white;">{$value['nombre']}</td>
          <td style="background:#48b3d9; color:white;">{$value['descripcion']}</td>
          <td style="background:#48b3d9; color:white;">{$value['tipo']}</td>
          <td style="background:#48b3d9; color:white;">{$value['fijo']}</td>
          <td style="background:#48b3d9; color:white;">{$value['repetitivo']}</td>
          <td style="background:#48b3d9; color:white;">$ {$value['cantidad']}</td>
        </tr>
html;
      }else{
        $tabla .=<<<html
        <tr>
          <td><input type="checkbox" name="borrar[]" value="{$value['incentivos_asignados_id']}"/></td>
          <td>{$key}</td> <!-- NUMERO DE INCREMENTO -->
          <td>{$value['nombre']}</td>
          <td>{$value['descripcion']}</td>
          <td>{$value['tipo']}</td>
          <td>{$value['fijo']}</td>
          <td>{$value['repetitivo']}</td>
          <td>$ {$value['cantidad']}</td>
        </tr>
html;
      }
    }

    if(count($incentivos)>0){
      $cantidad = IncentivoDao::getSumaIncentivosAsginados($idColaborador, $idPeriodo);
      $nuevoResultado = number_format($cantidad['cantidad_incentivos_asignados'], 2, '.', '');
      $tabla .=<<<html
        <tr>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td><b>TOTAL</b></td>
          <td><b>$ {$nuevoResultado}</b></td>
        </tr>
html;
    }
    return $tabla;
  }

  public function getRepetir($dato, $key){
    $html = "";
    if($dato == "si"){
      $html .=<<<html
        <input type="text" class="ta{$key}" maxlength="2" id="nightlife_open_7_end" >
html;
    }else{
      $html .=<<<html
        <input type="text" class="ta{$key}" class="noRepetitivo" maxlength="2" id="nightlife_open_7_end" value="1" readonly disabled/>
html;
    }
    return $html;
  }

  public function getCantidad($fijo){
    $html = "";
    if($fijo == "si"){
      $html .=<<<html
      <input type="text" name="data-{$key}[]" value="1">
html;
    }else{
      $html .=<<<html
      <input type="text" name="data-{$key}[]" value="{$value['cantidad']}">
html;
    }
    return $html;
  }

  public function addBotes(){
    $data = new \stdClass();
    $data->_colaborador_id = MasterDom::getData('colaborador_id');
    $data->_prorrateo_periodo_id = MasterDom::getData('prorrateo_periodo_id');
    $data->_botes_incentivo = MasterDom::getData('botes_incentivo');
    $data->_botes_max = MasterDom::getData('botes_max');

    if(!empty($data->_botes_incentivo) || !empty($data->_botes_max)){
      
      if(!empty($data->_botes_incentivo) AND empty($data->_botes_max)){
        $data->_colaborador_id = MasterDom::getData('colaborador_id');
        $data->_prorrateo_periodo_id = MasterDom::getData('prorrateo_periodo_id');
        $data->_catalogo_incentivo_id = MasterDom::getData('incentivo');
        $data->_cantidad = MasterDom::getData('cantidad_inicial');

        $busqueda = IncentivoDao::getBusquedaIncentivoBotes($data);
        if($busqueda>0){
          if($busqueda['cantidad'] == $data->_cantidad){
            $this->mensaje("Se ha insertado la cantidad de {$data->_cantidad}", "success", $data->_colaborador_id, $data->_prorrateo_periodo_id, MasterDom::getData('regreso'));
          }else{
            $update = IncentivoDao::updateIncentivoBotes($busqueda['incentivos_asignados_id'], $data->_cantidad);
            if($update>0){
              $this->mensaje("Se ha actualizado la cantidad de botes a la cantidad de $ {$data->_cantidad}", "info", $data->_colaborador_id, $data->_prorrateo_periodo_id, MasterDom::getData('regreso'));
            }else{
              $this->mensaje("ha ocurrido un error", "danger", $data->_colaborador_id, $data->_prorrateo_periodo_id, MasterDom::getData('regreso'));
            }
          }

        }else{
          $id = IncentivoDao::insertIncentivoBotes($data);
          if($id>0){
            $this->mensaje("Se ha insertado la cantidad de {$data->_cantidad}", "success", $data->_colaborador_id, $data->_prorrateo_periodo_id, MasterDom::getData('regreso'));
          }else{
            $this->mensaje("ha ocurrido un error", "danger", $data->_colaborador_id, $data->_prorrateo_periodo_id, MasterDom::getData('regreso'));
          }
        }
      }elseif(!empty($data->_botes_incentivo) AND !empty($data->_botes_max)){
        $data->_colaborador_id = MasterDom::getData('colaborador_id');
        $data->_prorrateo_periodo_id = MasterDom::getData('prorrateo_periodo_id');
        $data->_catalogo_incentivo_id = MasterDom::getData('incentivo');
        $data->_cantidad = MasterDom::getData('cantidad_inicial');
        $busqueda = IncentivoDao::getBusquedaIncentivoBotes($data);
        if($busqueda > 0){
          $data->_cantidad = 0;
          if(MasterDom::getData('valor_botes_total') == 0){
            $data->_cantidad = MasterDom::getData('cantidad_inicial') + MasterDom::getData('valor_botes_total');
          }else{
            $data->_cantidad = MasterDom::getData('valor_botes_total');
          }

          if($data->_cantidad == $busqueda['cantidad']){
            $this->mensaje("la cantidad de $ {$data->_cantidad}, es la asignada para los botes.", "success", $data->_colaborador_id, $data->_prorrateo_periodo_id, MasterDom::getData('regreso'));
          }else{
            $this->mensaje("Se actualizara la cantidad de botes a: $ {$data->_cantidad}", "info", $data->_colaborador_id, $data->_prorrateo_periodo_id, MasterDom::getData('regreso'));
            $update = IncentivoDao::updateIncentivoBotes($busqueda['incentivos_asignados_id'], $data->_cantidad);
          }
        }else{
          $data->_colaborador_id = MasterDom::getData('colaborador_id');
          $data->_prorrateo_periodo_id = MasterDom::getData('prorrateo_periodo_id');
          $data->_catalogo_incentivo_id = MasterDom::getData('incentivo');
          if(MasterDom::getData('valor_botes_total') == 0){
            $data->_cantidad = MasterDom::getData('cantidad_inicial') + MasterDom::getData('valor_botes_total');
          }else{
            $data->_cantidad = MasterDom::getData('valor_botes_total');
          }

          $id = IncentivoDao::insertIncentivoBotes($data);
          if($id>0){
            $this->mensaje("Se ha insertado la cantidad de {$data->_cantidad}", "success", $data->_colaborador_id, $data->_prorrateo_periodo_id, MasterDom::getData('regreso'));
          }else{
            $this->mensaje("ha ocurrido un error", "danger", $data->_colaborador_id, $data->_prorrateo_periodo_id, MasterDom::getData('regreso'));
          }
        }
      }

    }else{
      $this->mensaje("Al parecer no ingresaste alg&uacute;n valor, vuelve a intentar.", "warning", $data->_colaborador_id, $data->_prorrateo_periodo_id, MasterDom::getData('regreso'));
    }
  }

  public function mensaje($texto, $class, $colaboradorId, $periodoId, $tipo){
    View::set('class',$class);
    View::set('regreso',"/Incentivo/getIncentivosColaborador/{$colaboradorId}/{$periodoId}/{$tipo}/");
    View::set('mensaje',$texto);
    View::set('header',$this->_contenedor->header($extraHeader));
    View::set('footer',$this->_contenedor->footer($extraFooter));
    View::render("alerta");
  }

  public function botes($tipo=''){
    $extraFooter =<<<html
      <script>
        $(document).ready(function(){

          $("#muestra-cupones").tablesorter();
          var oTable = $('#muestra-cupones').DataTable({
                "columnDefs": [{
                    "orderable": false,
                    "targets": 0
                }],
                 "order": false
            });

            // Remove accented character from search input as well
            $('#muestra-cupones input[type=search]').keyup( function () {
                var table = $('#example').DataTable();
                table.search(
                    jQuery.fn.DataTable.ext.type.search.html(this.value)
                ).draw();
            });

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



            $("#delete").click(function(){
              var seleccionados = $("input[name='borrar[]']:checked").length;
              if(seleccionados>0){
                alertify.confirm('¿Segúro que desea eliminar lo seleccionado?', function(response){
                  if(response){
                    $('#all').attr('target', '');
                    $('#all').attr('action', '/Incentivo/deleteBotesMeta');
                    $("#all").submit();
                    alertify.success("Se ha eliminado correctamente");
                  }
                });
              }else{
                alertify.confirm('Selecciona al menos uno para eliminar');
              }
            });

        });
      </script>
html;
    $user = GeneralDao::getDatosUsuario($this->__usuario);

    $i = IncentivoDao::getPeriodoLast();
    $botes = IncentivoDao::getAllBotesPeriodo();
    $tabla = "";
    foreach ($botes as $key => $value) {
      $periodo = $this->getIdPeriodohistorico($value['prorrateo_periodo_id']);
      $tabla .=<<<html
        <tr>
          <td><input type="checkbox" name="borrar[]" value="{$value['botes_id']}"/></td>
          <td>{$periodo}</td>
          <td>{$value['clara']}</td>
          <td>{$value['yema']}</td>
          <td>{$value['huevo_liquido']}</td>
          <td><a href="/Incentivo/botesEdit/{$value['prorrateo_periodo_id']}/" class="btn btn-success"> <span class="glyphicon glyphicon-edit"></span></a></td>
        </tr>
html;
    }
   


    View::set('tabla',$tabla);
    View::set('tipo',$tipo);
    View::set('header',$this->_contenedor->header($extraHeader));
    View::set('footer',$this->_contenedor->footer($extraFooter));
    View::render("botes_muestra");
  }

  public function botesAdd(){
    $extraFooter =<<<html
      <script>
        $(document).ready(function(){
          $("#add").validate({
            rules:{
              periodo:{
                required: true
              },
              clara:{
                required: true
              },
              yema:{
                required: true
              },
              huevoliquido:{
                required: true
              }
            },
            messages:{
              periodo:{
                required: "Este campo es requerido"
              },
              clara:{
                required: "Este campo es requerido"
              },
              yema:{
                required: "Este campo es requerido"
              },
              huevoliquido:{
                required: "Este campo es requerido"
              }
            }
          });//fin del jquery validate

        });//fin del document.ready
      </script>
html;
    
    View::set('option',$this->getAllPeriodoSemanales(0, "add")); 
      
    View::set('btn',"Agregar");
    View::set('form',"/incentivo/addBotesCantidad/");
    View::set('class',"success");
    View::set('clara',$this->getBotes("501","clara"));
    View::set('yema',$this->getBotes("501","yema"));
    View::set('huevoliquido',$this->getBotes("501","huevo liquido"));
    
    View::set('header',$this->_contenedor->header($extraHeader));
    View::set('footer',$this->_contenedor->footer($extraFooter));
    View::render("botes_add"); 
  }

  public function botesEdit($periodo){
    $extraFooter =<<<html
      <script>
        $(document).ready(function(){
          $("#add").validate({
            rules:{
              periodo:{
                required: true
              },
              clara:{
                required: true
              },
              yema:{
                required: true
              },
              huevoliquido:{
                required: true
              }
            },
            messages:{
              periodo:{
                required: "Este campo es requerido"
              },
              clara:{
                required: "Este campo es requerido"
              },
              yema:{
                required: "Este campo es requerido"
              },
              huevoliquido:{
                required: "Este campo es requerido"
              }
            }
          });//fin del jquery validate

        });//fin del document.ready
      </script>
html;
    $id = IncentivoDao::getBotesByIdPerido($periodo);
    View::set('btn',"Actualizar");
    View::set('form',"/incentivo/editBotesCantidad/");
    View::set('class',"info");
    View::set('option',$this->getAllPeriodoSemanales($id['prorrateo_periodo_id'],"edit")); 
    View::set('clara',$this->getBotes($id['clara'],"clara"));
    View::set('yema',$this->getBotes($id['yema'],"yema"));
    View::set('huevoliquido',$this->getBotes($id['huevo_liquido'],"huevo liquido"));
    View::set('header',$this->_contenedor->header($extraHeader));
    View::set('footer',$this->_contenedor->footer($extraFooter));
    View::render("botes_add"); 
  }

  public function getBotes($cantidad, $tipo){
    $html ="";
    for ($i=0; $i <= 500 ; $i++) { 
      $selected = ($cantidad == $i) ? "selected":"";
      $html.=<<<html
        <option {$selected} value="{$i}">{$i} bote de {$tipo}</option>
html;
    }
    return $html;
  }

  public function addBotesCantidad(){

    $data = new \stdClass();
    $data->_prorrateo_periodo_id = MasterDom::getData('periodo');
    $data->_clara = MasterDom::getData('clara');
    $data->_yema = MasterDom::getData('yema');
    $data->_huevo_liquido = MasterDom::getData('huevoliquido');

    $busqueda = IncentivoDao::searchPeriodoBotes($data);

    if(empty($busqueda)){
      $id = IncentivoDao::insertNewBotesMetas($data);

      if($id>0){
        View::set('class',"success");
        View::set('mensaje',"Se ha creado una nueva meta de botes");
      }else{
        View::set('class',"danger");
        View::set('mensaje',"Al parecer ha ocurrido un error");
      }
    }else{
      View::set('class',"info");
      $a =<<<html
        <a href="/Incentivo/botesEdit/{$data->_prorrateo_periodo_id}/"> <span class="glyphicon glyphicon-eye-open"></span> Visualizar</a>
html;
      View::set('mensaje',"Al parecer ya hay datos con este periodo {$a} - ");
    }
    View::set('regreso',"/Incentivo/botes/");
    View::set('header',$this->_contenedor->header());
    View::set('footer',$this->_contenedor->footer());
    View::render("alerta");
  }

  public function editBotesCantidad(){

    $data = new \stdClass();
    $data->_prorrateo_periodo_id = MasterDom::getData('periodo');
    $data->_clara = MasterDom::getData('clara');
    $data->_yema = MasterDom::getData('yema');
    $data->_huevo_liquido = MasterDom::getData('huevoliquido');
    $update = IncentivoDao::updateMetaBotes($data);
    if($update>0){
      View::set('class',"success");
      View::set('mensaje',"Se ha actulizo los datos");
    }else{
      View::set('class',"warning");
      View::set('mensaje',"Al parecer ha ocurrido un error, ya que los datos son los mismos");
    }

    View::set('regreso',"/Incentivo/botes/");
    View::set('header',$this->_contenedor->header());
    View::set('footer',$this->_contenedor->footer());
    View::render("alerta");
  }

  public function deleteBotesMeta(){

    $arr = array();
    foreach ($_POST['borrar'] as $key => $value) {
      $id = IncentivoDao::deleteMetaBotes($value);
      array_push($arr, $id);
    }

    if(count($arr)>0){
      View::set('class',"success");
      View::set('mensaje',"Se ha eliminado de forma corracta!");
    }else{
      View::set('class',"danger");
      View::set('mensaje',"Ups! al parecer a ocurrido un error");
    }

    View::set('regreso',"/Incentivo/botes/");
    View::set('header',$this->_contenedor->header());
    View::set('footer',$this->_contenedor->footer());
    View::render("alerta");
  }

  public function addIncentivosColaborador(){
    $total = MasterDom::getData('cantidad');
    $colaboradorId = MasterDom::getData('colaboradorId');
    $periodoId = MasterDom::getData('periodoId');
    $tipo = MasterDom::getData('tipo');

    $arr = array();
    $arr1 = array();
    for ($i=1; $i <= $total ; $i++) { 
      array_push($arr, "data-".$i);
      array_push($arr1, "uno".$i);
    }

    $arrSuma = array();
    foreach ($arr1 as $key => $value) {
      if(count($_POST[$value]) > 0)
        array_push($arrSuma, 1);
    }

    if(empty($arrSuma)){
      View::set('class',"warning");
      View::set('regreso',"/Incentivo/getIncentivosColaborador/{$colaboradorId}/{$periodoId}/{$tipo}/");
      View::set('mensaje',"Al parecer no haz agregado ningun incentivo, favor de indicar que incentivos son los que se asignaran.");
    }else{
      // ELIMINAR INCENTIVOS
      if(count(IncentivoDao::getIncentivosColaboradorResumen($colaboradorId, $periodoId))>0){
        //$data = new \stdClass();
        //$data->_colaborador_id = $colaboradorId;
        //$data->_prorrateo_periodo_id = $periodoId;
        //$delete = IncentivoDao::eliminarIncentivosColaborador($data);
        // echo "Se eliminaran los incentivos, y se actualizaran despues";
      }else{
        // echo "no existian valores agregados en incentivos";
      }

      $id = 0;
      $data = new \stdClass();
      foreach ($arr as $key => $value) {
        if($_POST[$value][0] == "ok"){

          $data->_colaborador_id = $_POST[$value][1];
          $data->_prorrateo_periodo_id = $_POST[$value][2];
          $data->_catalogo_incentivo_id = $_POST[$value][3];
          $data->_cantidad = $_POST[$value][4];
          $data->_repetitivo = $_POST[$value][5];

          $data->_asignado = 0;
          $data->_valido = 0;
          $id = $this->getIncentivoPorId($data);
        }
      }
      if($id>0){
        View::set('class',"success");
        View::set('regreso',"/Incentivo/getIncentivosColaborador/{$colaboradorId}/{$periodoId}/{$tipo}/");
        View::set('mensaje',"Se ha incertado correctamente los incentivos");
      }
    }
    View::set('header',$this->_contenedor->header($extraHeader));
    View::set('footer',$this->_contenedor->footer($extraFooter));
    View::render("alerta");
  }

  public function getIncentivoPorId($data){
    $id = IncentivoDao::getIncentivoById($data->_colaborador_id, $data->_prorrateo_periodo_id, $data->_catalogo_incentivo_id);
    if($id['incentivos_asignados_id'] > 0){
      if($data->_repetitivo == "si"){
        IncentivoDao::eliminarIncentivosSeleccionado($id['incentivos_asignados_id']);
        $suma = $data->_cantidad + $id['cantidad'];
        $data->_cantidad = $suma;
        $id = IncentivoDao::insertIncentivos($data);
      }
    }elseif(empty($id['incentivos_asignados_id']) ){
      $id = IncentivoDao::insertIncentivos($data);
    }
    return $id;
  }



  /*
    Vista de incentivos asignados por colaborador
  */
  public function incentivos($idColaborador, $idPeriodo, $tipo){
    $extraFooter=<<<html
      <script>
        $(document).ready(function(){

          /*$("#muestra-cupones1").tablesorter();
          var oTable = $('#muestra-cupones1').DataTable({
            "columnDefs": [{"orderable": false,"targets": 0}],
            "order": false
          });

          $("#muestra-cupones2").tablesorter();
          var oTable = $('#muestra-cupones2').DataTable({
            "columnDefs": [{"orderable": false,"targets": 0}],
            "order": false
          });

          $("#tabla-muestra-borrar").tablesorter();
          var oTable = $('#tabla-muestra-borrar').DataTable({
            "columnDefs": [{"orderable": false,"targets": 0}],
            "order": false
          });

          $('#tabla-muestra-borrar input[type=search]').keyup( function () {
            var table = $('#tabla-muestra-borrar').DataTable();
            table.search(jQuery.fn.DataTable.ext.type.search.html(this.value)).draw();
          });*/

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

          $("#delete").click(function(){
              var seleccionados = $("input[name='borrar[]']:checked").length;
              if(seleccionados>0){
                alertify.confirm('¿Segúro que desea eliminar lo seleccionado?', function(response){
                  if(response){
                    $('#tabla_incentivos_borrar').attr('target', '');
                    $('#tabla_incentivos_borrar').attr('action', '/Incentivo/deleteIncentivos');
                    $("#tabla_incentivos_borrar").submit();
                    alertify.success("Se ha eliminado correctamente");
                  }
                });
              }else{
                alertify.confirm('Selecciona al menos uno para eliminar');
              }
            });


          $("#domingo_de_procesos").bootstrapSwitch();
          $("#domingo_laborado").bootstrapSwitch();
          $("#incentivo_de_noche").bootstrapSwitch();
          $("#eliminar-incentivos").bootstrapSwitch();
          $("#agregar-horas-extra").bootstrapSwitch();
          $("#update-horas-extra").bootstrapSwitch();
          $("#update-btn-horas-extra").hide();

          $("#domingoProcesos").bootstrapSwitch();
          $("#domingoLaborado").bootstrapSwitch();
		  $("#domingoEspecial").bootstrapSwitch();


          $(".switch").bootstrapSwitch();


          $('input[name="domingoProcesos"]').on('switchChange.bootstrapSwitch', function(event, state) {
            if(state){
              $('input[name="domingoLaborado"]').bootstrapSwitch('state', false, false);
			  $('input[name="domingoEspecial"]').bootstrapSwitch('state', false, false);
            }else{
            }
          });

          $('input[name="domingoLaborado"]').on('switchChange.bootstrapSwitch', function(event, state) {
            if(state){
              $('input[name="domingoProcesos"]').bootstrapSwitch('state', false, false);
			  $('input[name="domingoEspecial"]').bootstrapSwitch('state', false, false);
            }else{
            }
          });
		  
		  $('input[name="domingoEspecial"]').on('switchChange.bootstrapSwitch', function(event, state) {
            if(state){
              $('input[name="domingoProcesos"]').bootstrapSwitch('state', false, false);
			  $('input[name="domingoLaborado"]').bootstrapSwitch('state', false, false);
            }else{
            }
          });


          $(document).on('click', 'tr .tr_clone_add_1', function(){
            var fila = $(this).attr("fila");
            var codigoHTML = $("tr.fila_"+fila).html();
            $("tr.fila_"+fila).after("<tr class='copia_"+fila+"'>"+codigoHTML+"</tr>");
            $("tr.copia_"+fila+" .bootstrap-switch-container .bootstrap-switch-handle-on").remove();
            $("tr.copia_"+fila+" .bootstrap-switch-container .bootstrap-switch-handle-off").remove();
            $("tr.copia_"+fila+" .bootstrap-switch-container .bootstrap-switch-label").remove();
            var checkbox_input = $("tr.copia_"+fila+" .bootstrap-switch-container").html();
            $("tr.copia_"+fila+" .bootstrap-switch-container").remove();
            $("tr.copia_"+fila+" td.td_checkbox_"+fila).html(checkbox_input);
            $("tr.copia_"+fila+" .switch").bootstrapSwitch();
          });

          $(document).on('click', 'tr .tr_clone_add_2', function(){
            var fila = $(this).attr("fila");
            var codigoHTML = $("tr.fila_row_"+fila).html();
            $("tr.fila_row_"+fila).after("<tr class='copia_"+fila+"'>"+codigoHTML+"</tr>");
            $("tr.copia_"+fila+" .bootstrap-switch-container .bootstrap-switch-handle-on").remove();
            $("tr.copia_"+fila+" .bootstrap-switch-container .bootstrap-switch-handle-off").remove();
            $("tr.copia_"+fila+" .bootstrap-switch-container .bootstrap-switch-label").remove();
            var checkbox_input = $("tr.copia_"+fila+" .bootstrap-switch-container").html();
            $("tr.copia_"+fila+" .bootstrap-switch-container").remove();
            $("tr.copia_"+fila+" td.td_row_checkbox_"+fila).html(checkbox_input);
            $("tr.copia_"+fila+" .switch").bootstrapSwitch();
          });

          $('#horas_extra_1').on('change',function(){ 
            var value = $(this).val();
            if(value==1){
              $("#tabla_incentivos_para_asignar").show();
              $("#tabla_incentivos_asignados").hide();
              $("#tabla_incentivos_borrar").hide();
            }

            if(value==2){
              $("#tabla_incentivos_para_asignar").hide();
              $("#tabla_incentivos_asignados").show();
              $("#tabla_incentivos_borrar").hide();
                  }

            if(value==3){
              $("#tabla_incentivos_para_asignar").hide();
              $("#tabla_incentivos_asignados").hide();
              $("#tabla_incentivos_borrar").show();
            }
          });

          $("#chkAll").click(function() {
             $(".chkGroup").attr("checked", this.checked);
          });

        });

      </script>
html;
    
    $user = GeneralDao::getDatosUsuario($this->__usuario);

    $colaborador = IncentivoDao::getColaborador($idColaborador); // Obtiene la informacion del colaborador a a buscar
    $periodo = IncentivoDao::getPeriodo($idPeriodo); // Busca los datos del periodo

    if($this->getHorasExtra($idColaborador, $idPeriodo) > 0 )
      View::set('seleccionHorasExtra',$this->setHorasExtra($idColaborador, $idPeriodo,"MODIFICAR", $tipo, 1));
    else
      View::set('seleccionHorasExtra',$this->setHorasExtra($idColaborador, $idPeriodo,"INSERTAR", $tipo, 0));

    echo $this->getIncentivosIniciales($idColaborador, $idPeriodo);
    echo $this->getIncentivosAsignadosPeriodo($idColaborador, $idPeriodo);
    echo $this->getIncentivosBorrar($idColaborador, $idPeriodo);
    
    $countIncentivos = IncentivoDao::getIncentivosColaboradorAsignados($idColaborador,$idPeriodo);
    $accionSinIncentivos = (count($countIncentivos) == 0 ) ? "selected":"";
    $accionConIncentivos = (count($countIncentivos) > 0 ) ? "selected":"";

    $display = ($periodo['status'] == 0 ) ? "":"display: none;";

    $domingoProcesos = IncentivoDao::getDomingoProcesos($idColaborador, $idPeriodo);
    $domingoLaborado = IncentivoDao::getDomingoLaborado($idColaborador, $idPeriodo);
	$domingoEspecial = IncentivoDao::getDomingoEspecial($idColaborador, $idPeriodo);
    $incentivosNoche = IncentivoDao::getIncentivosNoche($idColaborador, $idPeriodo);


    $showTextBtnActualizar = ( count($domingoProcesos)>0 || count($domingoLaborado)>0 || count($domingoEspecial)>0) ? "Actualizar datos" : "Agrear Valores";
    $accionesComplementarias = ( count($domingoProcesos)>0 || count($domingoLaborado)>0 || count($domingoEspecial)>0) ? "updateDomingos" : "addDomingos";
    $btnAccionesComplementarias = ( count($domingoProcesos)>0 || count($domingoLaborado)>0 || count($domingoEspecial)>0) ? "btn-success" : "btn-info";


    // Validar que estos datos ya existen en la base de datos
    $checkeddomingoProcesos = (count($domingoProcesos)>0) ? "checked" : "";    
    $checkeddomingoLaborado = (count($domingoLaborado)>0) ? "checked" : "";
	$checkeddomingoEspecial = (count($domingoEspecial)>0) ? "checked" : "";
    View::set('showTextBtnActualizar',$showTextBtnActualizar); // Texto del buton de agregar datos
    View::set('checkdomingoProcesos', $checkeddomingoProcesos); // value CHECKED del input checked
    View::set('checkdomingoLaborado', $checkeddomingoLaborado); // value CHECKED del input checked
	View::set('checkdomingoEspecial', $checkeddomingoEspecial); // value CHECKED del input checked
    // Valores del complementarios 
    View::set('domingoProcesos', $this->calculoDomingoProcesos(GeneralDao::getDatosColaborador($idColaborador))); // Muestra la cantidad que puede tener por domingos procesados
    View::set('domingoLaborado', $this->calculoDomingoLaborado(GeneralDao::getDatosColaborador($idColaborador))); // Muestra la cantidad que puede tener por domingo laborales
	View::set('domingoEspecial', $this->calculoDomingoEspecial(GeneralDao::getDatosColaborador($idColaborador))); // Muestra la cantidad que puede tener por domingo especial
    View::set('accionesComplementarias', $accionesComplementarias); // Envia el formulario si hay acciones complementarias o no 
    View::set('btnAccionesComplementarias',$btnAccionesComplementarias); // Muestra el color de la accion que se realizara
    View::set('accionSinIncentivos',$accionSinIncentivos);
    View::set('accionConIncentivos',$accionConIncentivos);
    View::set('procesoPeriodo', ($periodo['status']==0)?"Abierto":"Cerrado"); // Estatus del periodo
    View::set('cantidadIncentivos', count(IncentivoDao::getIncentivosColaboradorAsignados($idColaborador,$idPeriodo))); // Cantidad de incentivos
    View::set('horasExtra',$this->getHorasExtra($idColaborador, $idPeriodo)); // Obteniene la cantidad de horas extra
    View::set('infoPeriodo',$this->getTextoPeriodo($periodo, $tipo, $user['perfil_id'])); // Texto superior del periodo
    View::set('colaborador_id',$idColaborador); // Set el id del colaborador
    View::set('prorrateo_periodo_id',$idPeriodo); // Set el id del periodo
    View::set('display',$display);
    View::set('regreso',$tipo);
    View::set('colaborador',$colaborador);
    View::set('regreso',$tipo); // Redireccion para los seccion donde se localiza, si es que cambia de vista y quiere regresar
    $extraFooter .= $this->showTablaInicio(count($countIncentivos));
    View::set('header',$this->_contenedor->header());
    View::set('footer',$this->_contenedor->footer($extraFooter));
    View::render('asignar_incentivos');
  }

  public function calculoDomingoEspecial($datosColaborador){
    $sum = 250;
    return number_format($sum,2,'.','');
  }
  
  public function calculoDomingoProcesos($datosColaborador){
    $ope1 = $datosColaborador['sal_diario'] * 0.25;
    $ope2 = $datosColaborador['sal_diario'] * 3;
    $sum = $ope1 + $ope2;
    return number_format($sum,2,'.','');
  }

  public function calculoDomingoLaborado($datosColaborador){
    $ope1 = $datosColaborador['sal_diario'] * 0.25;
    $ope2 = $datosColaborador['sal_diario'] * 2;
    $sum = $ope1 + $ope2;
    return number_format($sum,2,'.','');
  }

  public function updateDomingos(){
    $idPeriodo = MasterDom::getData('prorrateo_periodo_id');
    $idColaborador = MasterDom::getData('colaborador_id');
    $regreso = MasterDom::getData('regreso');

    $domingoProcesos = MasterDom::getData('domingoProcesos');
    $domingoLaborado = MasterDom::getData('domingoLaborado');
	$domingoEspecial = MasterDom::getData('domingoEspecial');

    $sdtClass = new \stdClass();
    $sdtClass->_catalogo_colaboradores_id = $idColaborador;
    $sdtClass->_prorrateo_periodo_id = $idPeriodo;
    
    $html = "<div>";
    $dp = IncentivoDao::getDomingoProcesos($idColaborador, $idPeriodo);
    $dl = IncentivoDao::getDomingoLaborado($idColaborador, $idPeriodo);
	$dl = IncentivoDao::getDomingoEspecial($idColaborador, $idPeriodo);
    if(!empty($domingoProcesos)){
      if(count($dp)>0){
        $html .= $this->mensajeComplementos("El <b>DOMINGO DE PROCESOS</b> ya est&aacute; asignado con un valor de <b> {$domingoProcesos} </b>","alert-info", $idColaborador, $idPeriodo, $regreso);
      }else{
        if(count($dl)>0)
          IncentivoDao::redelete("prorrateo_domigo_laborado", $idColaborador, $idPeriodo);
        
        $sdtClass->_domigo_procesos = $domingoProcesos;
        $insert = IncentivoDao::insertDomingoProcesos($sdtClass);
        $html .= $this->mensajeComplementos("El <b>DOMINGO DE PROCESOS</b> se ha agregado correctamente con un valor de <b> {$domingoProcesos} </b>","alert-success", $idColaborador, $idPeriodo, $regreso);
      }

    }elseif(!empty($domingoLaborado)){
      if(count($dl)>0){
        $html .= $this->mensajeComplementos("El <b>DOMINGO LABORADO</b> ya est&aacute; asignado con un valor de <b> {$domingoLaborado} </b>","alert-info", $idColaborador, $idPeriodo, $regreso);
      }else{
        if(count($dp)>0)
          IncentivoDao::redelete("prorrateo_domigo_procesos", $idColaborador, $idPeriodo);

        $sdtClass->_domingo_laborado = $domingoLaborado;
        $insert = IncentivoDao::insertDomingoLaborado($sdtClass);
        $html .= $this->mensajeComplementos("El <b>DOMINGO DE PROCESOS</b> se ha agregado correctamente con un valor de <b> {$domingoLaborado} </b>","alert-success", $idColaborador, $idPeriodo, $regreso);
      }
    }elseif(!empty($domingoEspecial)){
      if(count($dl)>0){
        $html .= $this->mensajeComplementos("El <b>DOMINGO ESPECIAL</b> ya est&aacute; asignado con un valor de <b> {$domingoEspecial} </b>","alert-info", $idColaborador, $idPeriodo, $regreso);
      }else{
        if(count($dp)>0)
          //IncentivoDao::redelete("prorrateo_domigo_procesos", $idColaborador, $idPeriodo);

        $sdtClass->_domingo_especial = $domingoEspecial;
        $insert = IncentivoDao::insertDomingoEspecial($sdtClass);
        $html .= $this->mensajeComplementos("El <b>DOMINGO ESPECIAL</b> se ha agregado correctamente con un valor de <b> {$domingoEspecial} </b>","alert-success", $idColaborador, $idPeriodo, $regreso);
	}}	else{
      if(count($dp)>0)
        IncentivoDao::redelete("prorrateo_domigo_procesos", $idColaborador, $idPeriodo);

      if(count($dl)>0)
        IncentivoDao::redelete("prorrateo_domigo_laborado", $idColaborador, $idPeriodo);
	
	  if(count($dl)>0)
        IncentivoDao::redelete("prorrateo_domigo_especial", $idColaborador, $idPeriodo);
      
      $html .= $this->mensajeComplementos("Ahora ya no tienes ningun valor monetario para domingo de procesos o domingo laborado o domingo especial", "alert-success", $idColaborador, $idPeriodo, $regreso);
    }
    
    $html .= "</div>";

    //View::set('regreso',"/Incentivo/getIncentivosColaborador/{$idColaborador}/{$idPeriodo}/{$regreso}");
    View::set('secciones',$html);
    View::set('header',$this->_contenedor->header());
    View::set('footer',$this->_contenedor->footer());
    View::render("alerta_multi");
  }

  public function addDomingos(){
    $regreso = MasterDom::getData('regreso');
    $periodoId = MasterDom::getData('prorrateo_periodo_id');
    $colaboradorId = MasterDom::getData('colaborador_id');
    $domingoProcesos = MasterDom::getData('domingoProcesos');
    $domingoLaborado = MasterDom::getData('domingoLaborado');
	$domingoEspecial = MasterDom::getData('domingoEspecial');

    $std = new \stdClass();
    $std->_catalogo_colaboradores_id = $colaboradorId;
    $std->_prorrateo_periodo_id = $periodoId;

    $html = "<div>";
    if(!empty($domingoProcesos)){
      $std->_domigo_procesos = $domingoProcesos;
      $insert = IncentivoDao::insertDomingoProcesos($std);
      $html .= $this->mensajeComplementos("El <b>DOMINGO DE PROCESOS</b> fue agregado satisfactoriamente con un valor de <b> {$domingoProcesos} </b>","alert-success", $colaboradorId, $periodoId, $regreso);
    }elseif(!empty($domingoLaborado)){
      $std->_domingo_laborado = $domingoLaborado;
      $insert = IncentivoDao::insertDomingoLaborado($std);
      $html .= $this->mensajeComplementos("El <b>DOMINGO LABORADO</b> fue agregado satisfactoriamente con un valor de <b> {$domingoLaborado} </b>","alert-success", $colaboradorId, $periodoId, $regreso);
    }elseif(!empty($domingoEspecial)){
      $std->_domingo_especial = $domingoEspecial;
      $insert = IncentivoDao::insertDomingoEspecial($std);
      $html .= $this->mensajeComplementos("El <b>DOMINGO ESPECIAL</b> fue agregado satisfactoriamente con un valor de <b> {$domingoEspecial} </b>","alert-success", $colaboradorId, $periodoId, $regreso);
    }
	else{
      $html .= $this->mensajeComplementos("No se inserto ning&uacute; valor, ya que no se selecciono alguno, favor de indicar cual se asignara", "alert-warning", $colaboradorId, $periodoId, $regreso);
    }
    
    $html .= "</div>";

    //View::set('regreso',"/Incentivo/getIncentivosColaborador/{$colaboradorId}/{$periodoId}/{$regreso}");
    View::set('secciones',$html);
    View::set('header',$this->_contenedor->header());
    View::set('footer',$this->_contenedor->footer());
    View::render("alerta_multi");

  }

  public function addComplementos(){
    
    $periodoId = MasterDom::getData('prorrateo_periodo_id');
    $colaboradorId = MasterDom::getData('colaborador_id');
    $regreso = MasterDom::getData('regreso');
    // PARAMETROS DEL CHECADOR
    $inputDomingoDeProcesos = MasterDom::getData('domingo_de_procesos');
    $inputDomingoLaborado = MasterDom::getData('domingo_laborado');
	$inputDomingoEspecial = MasterDom::getData('domingo_especial');
    $inputIncentivoDeNoche = MasterDom::getData('incentivo_de_noche');
    // VALORES DE INPUT 
    $valueDomingoDeProcesos = MasterDom::getData('value_domingo_de_procesos');
    $valueDomingoLaborado = MasterDom::getData('value_domigo_laborado');
	$valueDomingoEspecial = MasterDom::getData('value_domigo_especial');
    $valueIncentivoDeNoche = MasterDom::getData('value_incentivo_de_noche');

    $html = "<div class=\"x_content\">";

      if(!empty($inputDomingoDeProcesos) AND !empty(MasterDom::getData('value_domingo_de_procesos') )){
        $sdtClass = new \stdClass();
        $sdtClass->_catalogo_colaboradores_id = $colaboradorId;
        $sdtClass->_prorrateo_periodo_id = $periodoId;
        $sdtClass->_domigo_procesos = MasterDom::getData('value_domingo_de_procesos');

        $insert = IncentivoDao::insertDomingoProcesos($sdtClass);
        if($insert==0)
          $html .= $this->mensajeComplementos("El <b>DOMINGO DE PROCESOS</b> fue agregado satisfactoriamente con un valor de <b> {$valueDomingoDeProcesos} </b>","alert-success", $colaboradorId, $periodoId, $regreso);
        else
          $html .= $this->mensajeComplementos("Ha ocurrido en <b>DOMINGO DE PROCESOS</b> con un valor de <b> {$valueDomingoDeProcesos} </b>", "alert-danger", $colaboradorId, $periodoId, $regreso);
      }else{
          $html .= $this->mensajeComplementos("No se inserto el valor de <b>DOMINGO DE PROCESOS</b>", "alert-warning", $colaboradorId, $periodoId, $regreso);
      }

      if(!empty($inputDomingoLaborado) AND !empty($valueDomingoLaborado)){
        $sdtClass = new \stdClass();
        $sdtClass->_catalogo_colaboradores_id = $colaboradorId;
        $sdtClass->_prorrateo_periodo_id = $periodoId;
        $sdtClass->_domingo_laborado = $valueDomingoLaborado;
        $insert = IncentivoDao::insertDomingoLaborado($sdtClass);
        if($insert==0)
          $html .= $this->mensajeComplementos("El <b>DOMINGO LABORADO</b> fue agregado satisfactoriamente con un valor de <b> {$valueDomingoLaborado} </b>","alert-success", $colaboradorId, $periodoId, $regreso);
        else
          $html .= $this->mensajeComplementos("Ha ocurrido en <b>DOMINGO LABORADO</b> con un valor de <b> {$valueDomingoLaborado} </b>", "alert-danger", $colaboradorId, $periodoId, $regreso);
      }else{
          $html .= $this->mensajeComplementos("No se inserto el valor de <b>DOMINGO LABORADO</b>", "alert-warning", $colaboradorId, $periodoId, $regreso);
      }
	  
	  if(!empty($inputDomingoEspecial) AND !empty($valueDomingoEspecial)){
      $sdtClass = new \stdClass();
      $sdtClass->_catalogo_colaboradores_id = $colaboradorId;
      $sdtClass->_prorrateo_periodo_id = $periodoId;
      $sdtClass->_domingo_especial = $valueDomingoEspecial;
      $insert = IncentivoDao::insertDomingoEspecial($sdtClass);
      if($insert==0)
         $html .= $this->mensajeComplementos("El <b>DOMINGO ESPECIAL</b> fue agregado satisfactoriamente con un valor de <b> {$valueDomingoEspecial} </b>","alert-success", $colaboradorId, $periodoId, $regreso);
      else
         $html .= $this->mensajeComplementos("Ha ocurrido en <b>DOMINGO ESPECIAL</b> con un valor de <b> {$valueDomingoEspecial} </b>", "alert-danger", $colaboradorId, $periodoId, $regreso);
      }else{
          $html .= $this->mensajeComplementos("No se inserto el valor de <b>DOMINGO ESPECIAL</b>", "alert-warning", $colaboradorId, $periodoId, $regreso);
      }

      if(!empty($inputIncentivoDeNoche) AND !empty($valueIncentivoDeNoche) AND $valueIncentivoDeNoche !="" AND $valueIncentivoDeNoche != 0){
        $sdt = new \stdClass();
        $sdt->_catalogo_colaboradores_id = $colaboradorId;
        $sdt->_prorrateo_periodo_id = $periodoId;
        $sdt->_incentivo_noche = $valueIncentivoDeNoche;
        $insert = IncentivoDao::insertIncentivoNoche($sdt);
        if($insert==0)
          $html .= $this->mensajeComplementos("El <b>INCENTIVO DE NOCHE</b> fue agregado satisfactoriamente con un valor de <b> {$valueIncentivoDeNoche} </b>","alert-success", $colaboradorId, $periodoId, $regreso);
        else
          $html .= $this->mensajeComplementos("Ha ocurrido en <b>INCENTIVO DE NOCHE</b> con un valor de <b> {$valueIncentivoDeNoche} </b>", "alert-danger", $colaboradorId, $periodoId, $regreso);
      }else{
          $html .= $this->mensajeComplementos("No se inserto el valor de <b>INCENTIVO DE NOCHE</b>", "alert-warning", $colaboradorId, $periodoId, $regreso);
      }

    $html .= "</div>";

    View::set('regreso',"/Incentivo/incentivos/{$colaboradorId}/{$periodoId}/{$regreso}");
    View::set('secciones',$html);
    View::set('header',$this->_contenedor->header());
    View::set('footer',$this->_contenedor->footer());
    View::render("alerta_multi");
  }

  public function updateComplementos(){

    $idPeriodo = MasterDom::getData('prorrateo_periodo_id');
    $idColaborador = MasterDom::getData('colaborador_id');
    $regreso = MasterDom::getData('regreso');

    $domingoProcesos = IncentivoDao::getDomingoProcesos($idColaborador, $idPeriodo);
    $domingoLaborado = IncentivoDao::getDomingoLaborado($idColaborador, $idPeriodo);
	$domingoEspecial = IncentivoDao::getDomingoEspecial($idColaborador, $idPeriodo);
    $incentivosNoche = IncentivoDao::getIncentivosNoche($idColaborador, $idPeriodo);
    

    // PARAMETROS DEL CHECADOR
    $inputDomingoDeProcesos = MasterDom::getData('domingo_de_procesos');
    $inputDomingoLaborado = MasterDom::getData('domingo_laborado');
	$inputDomingoEspecial = MasterDom::getData('domingo_especial');
    $inputIncentivoDeNoche = MasterDom::getData('incentivo_de_noche');
    // VALORES DE INPUT 
    $valueDomingoDeProcesos = MasterDom::getData('value_domingo_de_procesos');
    $valueDomingoLaborado = MasterDom::getData('value_domigo_laborado');
	$valueDomingoEspecial = MasterDom::getData('value_domigo_especial');
    $valueIncentivoDeNoche = MasterDom::getData('value_incentivo_de_noche');


    if(empty($inputDomingoDeProcesos)){
      if(count($domingoProcesos)>0){
        IncentivoDao::redelete("prorrateo_domigo_procesos", $idColaborador, $idPeriodo);
      }
    }else{
      if($domingoProcesos['domigo_procesos'] != $valueDomingoDeProcesos){
        $sdtClass = new \stdClass();
        $sdtClass->_catalogo_colaboradores_id = $idColaborador;
        $sdtClass->_prorrateo_periodo_id = $idPeriodo;
        $sdtClass->_domigo_procesos = MasterDom::getData('value_domingo_de_procesos');
        $insert = IncentivoDao::insertDomingoProcesos($sdtClass);
      }
    }

    if(empty($inputDomingoLaborado)){
      if(count($domingoLaborado)>0){
        IncentivoDao::redelete("prorrateo_domigo_laborado", $idColaborador, $idPeriodo);
      }
    }else{
      if($domingoLaborado['domingo_laborado'] != $valueDomingoLaborado){
        $sdtClass = new \stdClass();
        $sdtClass->_catalogo_colaboradores_id = $idColaborador;
        $sdtClass->_prorrateo_periodo_id = $idPeriodo;
        $sdtClass->_domingo_laborado = MasterDom::getData('value_domigo_laborado');
        $insert = IncentivoDao::insertDomingoLaborado($sdtClass);
      }
    }
	
	if(empty($inputDomingoEspecial)){
      if(count($domingoEspecial)>0){
        IncentivoDao::redelete("prorrateo_domigo_especial", $idColaborador, $idPeriodo);
      }
    }else{
      if($domingoEspecial['domingo_especial'] != $valueDomingoEspecial){
        $sdtClass = new \stdClass();
        $sdtClass->_catalogo_colaboradores_id = $idColaborador;
        $sdtClass->_prorrateo_periodo_id = $idPeriodo;
        $sdtClass->_domingo_especial = MasterDom::getData('value_domigo_especial');
        $insert = IncentivoDao::insertDomingoEspecial($sdtClass);
      }
    }

    if(empty($inputIncentivoDeNoche)){
      if(count($incentivosNoche)>0 || $incentivosNoche != "" || empty($incentivosNoche)){
        IncentivoDao::redelete("prorrateo_incentivo_noche", $idColaborador, $idPeriodo);
      }
    }else{
      if($incentivosNoche['incentivo_noche'] != $valueIncentivoDeNoche){
        if($valueIncentivoDeNoche != 0){
          $sdtClass = new \stdClass();
          $sdtClass->_catalogo_colaboradores_id = $idColaborador;
          $sdtClass->_prorrateo_periodo_id = $idPeriodo;
          $sdtClass->_incentivo_noche = $valueIncentivoDeNoche;
          $insert = IncentivoDao::insertIncentivoNoche($sdtClass);
        } 
      }
    }

    

    $html = "<div class=\"x_content\">";
    $html .= $this->mensajeComplementos("Se actualizaron todos los datos <b> correctamente</b>", "alert-success", $idColaborador, $idPeriodo, $regreso);
    $html .= "</div>";

    View::set('regreso',"/Incentivo/getIncentivosColaborador/{$colaboradorId}/{$periodoId}/{$regreso}");
    View::set('secciones',$html);
    View::set('header',$this->_contenedor->header());
    View::set('footer',$this->_contenedor->footer());
    View::render("alerta_multi");

  }

  public function mensajeComplementos($mensaje, $alert, $colaboradorId, $periodoId, $regreso){
    $html =<<<html
      <div class="alert {$alert} alert-dismissable">
        {$mensaje} <a href="/Incentivo/getIncentivosColaborador/{$colaboradorId}/{$periodoId}/{$regreso}" class="alert-link">Regreso</a>.
      </div>
html;
    return $html;
  }

  /*
    Muestra cual es la tabla que debe iniciar al ingresar en los incentivos
  */
  public function showTablaInicio($countIncentivos){
    $extraFooter;
    if($countIncentivos==0){
      $extraFooter.=<<<html
        <script>
          $(document).ready(function(){
            $("#tabla_incentivos_para_asignar").show();
            $("#tabla_incentivos_asignados").hide();
            $("#tabla_incentivos_borrar").hide();
          });
        </script>
html;
    }

    if($countIncentivos>0){
      $extraFooter.=<<<html
        <script>
          $(document).ready(function(){
            $("#tabla_incentivos_para_asignar").hide();
            $("#tabla_incentivos_asignados").show();
            $("#tabla_incentivos_borrar").hide();
          });
        </script>
html;
    }

    return $extraFooter;
  }

  public function getFaltas($colaborador_id, $periodo_id){
    $periodo = (Object) ResumenesDao::getPeriodoById($periodo_id);
    $faltas = ResumenesDao::getFaltasByPeriodoColaborador($colaborador_id,$periodo)['faltas'];
    //echo "Faltas de $colaborador_id son: $faltas<br>";
    return $faltas;
  }

  /*
    Obtener todos los incentivos que se podran asginar 
  */
  public function getIncentivosIniciales($idColaborador, $idPeriodo){
    $tabla = "";
    $incentivoAsignador = IncentivoDao::getIncentivoColaborador($idColaborador);
    $contador = 0;
    foreach ($incentivoAsignador as $key => $value) {
      $fijo = ($value['fijo'] == "si") ? "Incentivo asignado por el sistema" : "";
      $repetitivo = ($value['repetitivo'] == "si") ? "display:none;" : "";
      $tabla .=<<<html
        <tr class="tr_clone_1 fila_{$contador}">
          <td style="text-align:center; vertical-align:middle;">{$value['nombre']}</td>
          <td style="text-align:left; vertical-align:middle;">$ {$value['cantidad']}</td>
          <td style="text-align:center; vertical-align:middle;">{$value['descripcion']}</td>
          <td style="text-align:center; vertical-align:middle;">{$value['tipo']}</td>
          <!--td style="text-align:center; vertical-align:middle;">{$fijo}</td-->
          <td style="text-align:center; vertical-align:middle;" class="td_checkbox_{$contador}" >
html;
      $faltas = $this->getFaltas($idColaborador, $idPeriodo);

      //echo $faltas.'::::::::::<br>';

      if($faltas == 0){
        $asignado = 1;
        $tipo = ($value['tipo'] == "MENSUAL") ? 0:1;
      }else{
        $asignado = 0;
        $tipo = 0;
      }

      /*if($value['fijo'] == "si"){
        echo "------- sin faltas";
        $aplicaIncentivoSistema = $this->getFaltas($idColaborador, $idPeriodo);
        $checked = ($aplicaIncentivoSistema > 0) ? "":"checked";
        $disabled = ($aplicaIncentivoSistema > 0) ? "":"disabled";

        if($aplicaIncentivoSistema == 0){
          $tabla.=<<<html
            <input type="hidden" value="{$value['catalogo_colaboradores_id']}|{$idPeriodo}|{$value['catalogo_incentivo_id']}|{$value['cantidad']}|{$asignado}|{$tipo}" name="agregar[]" >
            
            INFOINFO

            <input type="checkbox" class="switch" data-on-text="SI" data-off-text="NO" {$checked} value="{$value['catalogo_colaboradores_id']}|{$idPeriodo}|{$value['catalogo_incentivo_id']}|{$value['cantidad']}|{$asignado}|{$tipo}" >
html;
              }

        /*if($aplicaIncentivoSistema > 0){
          $tabla.=<<<html
          <input type="checkbox" class="switch" data-on-text="SI" data-off-text="NO" disabled value="{$value['catalogo_colaboradores_id']}|{$idPeriodo}|{$value['catalogo_incentivo_id']}|{$value['cantidad']}|{$asignado}|{$tipo}" name="agregar[]">
html;
              }
        }else{*/

    if($value['fijo'] == "si"){
      $tabla .= <<<html
      <!--input type="hidden" checked value="{$value['catalogo_colaboradores_id']}|{$idPeriodo}|{$value['catalogo_incentivo_id']}|{$value['cantidad']}|{$asignado}|{$tipo}" name="agregar[]" -->
      <input type="checkbox" class="switch" data-on-text="SI" data-off-text="NO" checked value="{$value['catalogo_colaboradores_id']}|{$idPeriodo}|{$value['catalogo_incentivo_id']}|{$value['cantidad']}|{$asignado}|{$tipo}" name="agregar[]">
html;
    }else{
      $tabla .= <<<html
      <input type="checkbox" class="switch" data-on-text="SI" data-off-text="NO" value="{$value['catalogo_colaboradores_id']}|{$idPeriodo}|{$value['catalogo_incentivo_id']}|{$value['cantidad']}|{$asignado}|{$tipo}" name="agregar[]">
html;
    }

      
    //}
    $tabla .=<<<html
          </td>
          <td style="text-align:center; vertical-align:middle;"> {$faltas}</td>
          <td style="text-align:center; vertical-align:middle;">
html;
    if($value['repetitivo'] == "si"){
      $tabla .= '<input type="button" name="add" value="+" class="tr_clone_add_1 btn btn-success" style="{$repetitivo}" fila="'.$contador.'"> ';
    }
    $tabla .=<<<html
        </td>
      </tr>
html;
      $contador++;
    }
    View::set('tabla', $tabla);
    }

    /*
    Regresa la tabla todos los incentivos que se le han asignado al colaborador
    */

  public function getIncentivosAsignadosPeriodo($idColaborador,$idPeriodo){
      $tablaAsignados = "";
      $incentivosAsignados = IncentivoDao::getIncentivosColaboradorAsignados($idColaborador,$idPeriodo);
      $contador = 0;
      foreach ($incentivosAsignados as $key => $value) {
        $fijo = ($value['fijo'] == "si") ? "Incentivo asignado por el sistema" : "";
        $repetitivo = ($value['repetitivo'] == "si") ? "display:none;" : "";
        $tablaAsignados .=<<<html
          <tr class="tr_clone_1 fila_row_{$contador}">
            <td style="text-align:center; vertical-align:middle;">{$value['nombre']}</td>
            <td style="text-align:left; vertical-align:middle;">$ {$value['cantidad']}</td>
            <td style="text-align:center; vertical-align:middle;">{$value['descripcion']}</td>
            <td style="text-align:center; vertical-align:middle;">{$value['tipo']}</td>
            <!--td style="text-align:center; vertical-align:middle;">{$fijo}</td-->

            <td style="text-align:center; vertical-align:middle;"  class="td_row_checkbox_{$contador}" >
html;
          $faltas = $this->getFaltas($idColaborador, $idPeriodo);
          if($faltas == 0){
            $asignado = 1;
            if($value['tipo'] == "MENSUAL")
              $tipo = 0;
            else
              $tipo = 1;
          }else{
            $asignado = 0;
            $tipo = 0;
          }

          /*if($value['fijo'] == "si"){
            $aplicaIncentivoSistema = $this->getFaltas($idColaborador, $idPeriodo);
            $checked = ($aplicaIncentivoSistema > 0) ? "":"checked";
            $disabled = ($aplicaIncentivoSistema > 0) ? "":"disabled";

            if($aplicaIncentivoSistema == 0){
              $tablaAsignados.=<<<html
                <input type="hidden" value="{$value['catalogo_colaboradores_id']}|{$idPeriodo}|{$value['catalogo_incentivo_id']}|{$value['cantidad']}|{$asignado}|{$tipo}" name="agregar[]" >
                <input type="checkbox" class="switch" data-on-text="SI" data-off-text="NO" checked value="{$value['catalogo_colaboradores_id']}|{$idPeriodo}|{$value['catalogo_incentivo_id']}|{$value['cantidad']}|{$asignado}|{$tipo}" >
html;
            }

            if($aplicaIncentivoSistema > 0){
              $tablaAsignados.=<<<html
                <input type="checkbox" class="switch" data-on-text="SI" data-off-text="NO" value="{$value['catalogo_colaboradores_id']}|{$idPeriodo}|{$value['catalogo_incentivo_id']}|{$value['cantidad']}|{$asignado}|{$tipo}" name="agregar[]" >
html;
            }
          }else{*/
            $tablaAsignados .= <<<html
            <input type="checkbox" class="switch" data-on-text="SI" data-off-text="NO" checked value="{$value['catalogo_colaboradores_id']}|{$idPeriodo}|{$value['catalogo_incentivo_id']}|{$value['cantidad']}|{$asignado}|{$tipo}" name="agregar[]" >
html;
          //}
        $tablaAsignados .=<<<html
                </td>
                <td style="text-align:center; vertical-align:middle;">{$faltas}</td>
                <td style="text-align:center; vertical-align:middle;">
html;
        if($value['repetitivo'] == "si"){
          $tablaAsignados .= '<input type="button" name="add" value="+" class="tr_clone_add_2 btn btn-success" style="{$repetitivo}" fila="'.$contador.'"> ';
        }
        $tablaAsignados .=<<<html
                </td>
              </tr>
html;
        $contador++;
      }

      View::set('tablaAsignados', $tablaAsignados);
    }


    public function getIncentivosBorrar($idColaborador,$idPeriodo){
      $tablaEliminar = "";
      $incentivosAsignados = IncentivoDao::getIncentivosColaboradorAsignados($idColaborador,$idPeriodo);
      foreach ($incentivosAsignados as $key => $value) {
        $fijo = ($value['fijo'] == "si") ? "Incentivo asignado por el sistema" : "";
        $repetitivo = ($value['repetitivo'] == "si") ? "display:none;" : "";
        $asignando = ($value['asignando'] == 1) ? "Si" : "No";

        if($value['valido'] == 0)
          $aplicaPeriodo = "no";
        if($value['valido'] == 1)
          $aplicaPeriodo = "Aplica este periodo";
        if($value['valido'] == 2)
          $aplicaPeriodo = "Aplica para fin de mes";

        $tablaEliminar .=<<<html
          <tr class="tr_clone_2">
            <td style="text-align:center; vertical-align:middle;"><input type="checkbox" name="borrar[]" value="{$value['incentivos_asignados_id']}"/></td>
            <td style="text-align:center; vertical-align:middle;">{$value['nombre']}</td>
            <td style="text-align:center; vertical-align:middle;">{$value['cantidad']}</td>
            <td style="text-align:center; vertical-align:middle;">{$value['descripcion']}</td>
            <td style="text-align:center; vertical-align:middle;">{$value['tipo']}</td>
            <td style="text-align:center; vertical-align:middle;">{$asignando}</td>
            <td style="text-align:center; vertical-align:middle;">{$aplicaPeriodo}</td>
            <!--td style="text-align:center; vertical-align:middle;">{$fijo}</td-->
          </tr>
html;
      }

      View::set('tablaEliminar', $tablaEliminar);
    }


  /*
    Regresa la cantidad de faltas que tiene el colaborador
    */
  public function getStatusAsistenciasColaborador1($idColaborador, $periodoSolicitadoId){
    $dias_traductor = array('Monday' => 'Lunes','Tuesday' => 'Martes','Wednesday' => 'Miercoles','Thursday' => 'Jueves','Friday' => 'Viernes','Saturday' => 'Sabado','Sunday' => 'Domingo');
    $meses_traductor = array(1 => 'ENE',2 => 'FEB',3 => 'MAR',4 => 'ABR',5 => 'MAY',6 => 'JUN',7 => 'JUL',8 => 'AGO',9 => 'SEP', 10 => 'OCT', 11 => 'NOV', 12 => 'DIC');
    $periodo = ResumenSemanalDao::getPeriodoById($periodoSolicitadoId);

    $fecha_fin = new \DateTime($periodo['fecha_fin']);
    $datos = new \stdClass();
    $datos->tipo = ucwords(strtolower($periodo['tipo']));
    $encabezado =<<<html
    <th>No. Empleado</th>
    <th>Nombre</th>
html;
    $j = 0;
    $administrador = ResumenSemanalDao::getDatosUsuarioLogeado(MasterDom::getSesion('usuario'));
    $datosColaborador = ResumenSemanalDao::getColaboradoresPago(ucwords(strtolower($periodo['tipo'])), $idColaborador);

    $faltasAcomuladas = array();
    foreach($datosColaborador as $key => $value){
      $tabla .=<<<html
      <tr>
        <td>{$value['numero_empleado']}</td>
html;
      $datos->numero_empleado = $value['numero_empleado'];
      $fecha_inicio = new \DateTime($periodo['fecha_inicio']);
      $i=1;
      $horario_laboral = ResumenSemanalDao::getHorarioLaboral($value['catalogo_colaboradores_id']);
      $contadorRetardos = array();
      while($fecha_inicio <= $fecha_fin){
        $dia_aux = '';
        $llegada = '';
        
        foreach ($horario_laboral as $llave1 => $valor1) {
          if($dia_aux != $valor1['dia_semana']){
            $dia_aux = $valor1['dia_semana'];
            if($dia_aux  == $dias_traductor[date('l', strtotime($fecha_inicio->format('Y-m-d')))]){
              $datos->_catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];
              $datos->_fecha = $fecha_inicio->format('Y-m-d');
              $incidencia = ResumenSemanalDao::getIncidencia($datos);
              if(count($incidencia)>0){
                $llegada = $incidencia[0]['identificador_incidencia'];
                $color = $incidencia[0]['color'];
                if($incidencia[0]['genera_falta'] == 1){
                  $llegada = 'F';
                }
              }else{
                $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' '.ResumenSemanal::restarMinutos($valor1['hora_entrada'],30);
                $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' '.ResumenSemanal::sumarMinutos($valor1['hora_entrada'], intval($valor1['tolerancia_entrada']));
                $registro_entrada = ResumenSemanalDao::getAsistencia($datos);
                if(count($registro_entrada) > 0){
                  $llegada = 'A';
                }else{
                  $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' 00:00:00';
                  $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' 23:59:59';
                  $registro_entrada = ResumenSemanalDao::getAsistencia($datos);
                  if(count($registro_entrada) > 0){
                    $llegada = 'R';
                    $contadorRetardos[$valor1['catalogo_horario_id']] += 1;
                  }else{
                    $llegada = 'F';
                  }

                  if( $contadorRetardos[$valor1['catalogo_horario_id']] == $valor1['numero_retardos'] ){
                    $llegada = 'F';
                    $contadorRetardos[$valor1['catalogo_horario_id']] = 0;
                  }
                  if($llegada != ''){break;}
                }//fin del else del  if(count($registro_entrada) > 0)
              }// fin del el del chequeo de incidencia
            }
          }
        }//fin del foreach fechas

        if($llegada == ''){
          $datos->_catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];
          $datos->_fecha = $fecha_inicio->format('Y-m-d');
          $incidencia = ResumenSemanalDao::getIncidencia($datos);
          if(count($incidencia)>0){
            $llegada = $incidencia[0]['identificador_incidencia'];
            $color = $incidencia[0]['color'];
          }else{
            $llegada = 'D';
          }
        }

        if ($llegada == 'A'){
          $color = 'green';
        }elseif($llegada == 'D'){
          $color = 'gray';
        }elseif($llegada == 'R'){
          $color = 'yellow';
        }elseif ($llegada == 'F'){
          $color = 'red';
          $bandera = 1; // SUMADOR de faltas
          array_push($faltasAcomuladas, $bandera);
        }else{
          $color = 'green';
        }

        $tabla .=<<<html
        <td><span class="btn btn-success"><label style="color: {$color};"> {$llegada} </label></span></td>
html;
        $fecha_inicio->add(new \DateInterval('P1D'));
        $i++;
      }
      $tabla .=<<<html
        </tr>
html;
      $j++;
    }
    //echo $tabla;
    $contadorFaltas = array_sum($faltasAcomuladas);
    return $contadorFaltas;
    }

  public function getStatusAsistenciasColaborador($idColaborador, $periodoSolicitadoId){
      $asistencia = new \stdClass();
      $asistencia->_prorrateo_periodo_id = $periodoSolicitadoId;
      $periodo = ResumenSemanalDao::getPeriodoById($periodoSolicitadoId);
      $fecha_fin = new \DateTime($periodo['fecha_fin']);
      $dias_traductor = array(
        'Monday'=>'Lunes',
        'Tuesday'=>'Martes',
        'Wednesday'=>'Miercoles',
        'Thursday'=>'Jueves',
        'Friday'=>'Viernes',
        'Saturday'=>'Sabado',
        'Sunday'=>'Domingo');

      $datos = new \stdClass();
      $datos->tipo = ucwords(strtolower($periodo['tipo']));

      foreach(ResumenSemanalDao::getAllColaboradoresPagoById($idColaborador) as $key => $value){
        $nombre_planta = strtolower($value['nombre_planta']);
        $asistencia->_catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];
        $datos->numero_empleado = $value['numero_empleado'];
        $fecha_inicio = new \DateTime($periodo['fecha_inicio']);
        $horario_laboral = ResumenSemanalDao::getHorarioLaboral($value['catalogo_colaboradores_id']);
        $contadorRetardos = array();
        $contadorFaltas = 0;
        while($fecha_inicio <= $fecha_fin){
          $asistencia->_fecha = '';
          $asistencia->_estatus = '';
          $dia_aux = '';
          $llegada = '';

          $datos->_catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];
          $datos->_fecha = $fecha_inicio->format('Y-m-d');
          $incidencia = ResumenSemanalDao::getIncidencia($datos);
          
          if(count($incidencia) > 0){
            $llegada = $incidencia[0]['catalogo_incidencia_id']; //incidencia
            if($incidencia[0]['genera_falta'] == 1){
              $llegada = -1; //falta
              $contadorFaltas ++;
            }
          }else{

          foreach ($horario_laboral as $llave1 => $valor1) {

            if($dia_aux != $valor1['dia_semana']){
              $dia_aux = $valor1['dia_semana'];


              if($dia_aux  == $dias_traductor[date('l', strtotime($fecha_inicio->format('Y-m-d')))]){
                  $datos->_catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];
                  $datos->_fecha = $fecha_inicio->format('Y-m-d');
                  $incidencia = ResumenSemanalDao::getIncidencia($datos);
                  if(count($incidencia)>0){
                    $llegada = $incidencia[0]['catalogo_incidencia_id']; //incidencia
                     if($incidencia[0]['genera_falta'] == 1){
                        $llegada = -1; //falta
                        $contadorFaltas ++;
                    }
                  }else{
                    $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' '.ResumenSemanal::restarMinutos($valor1['hora_entrada'],30);
                    $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' '.ResumenSemanal::sumarMinutos($valor1['hora_entrada'], intval($valor1['tolerancia_entrada']));
                    $registro_entrada = ResumenSemanalDao::getAsistencia($datos, $nombre_planta);
                    if(count($registro_entrada) > 0){
                      $llegada = 0; //asistencia
                    }else{
                      $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' 00:00:00';
                      $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' 23:59:59';
                      $registro_entrada = ResumenSemanalDao::getAsistencia($datos, $nombre_planta);
                      if(count($registro_entrada) > 0){
                          $llegada = -2;//retardo
                          $contadorRetardos[$valor1['catalogo_horario_id']] += 1;
                      }else{
                        $llegada = -1; //falta
                        $contadorFaltas ++;
                      }

                      if( $contadorRetardos[$valor1['catalogo_horario_id']] == $valor1['numero_retardos'] ){
                        $llegada = -22; //falta
                        $contadorFaltas ++;
                        $contadorRetardos[$valor1['catalogo_horario_id']] = 0;
                      }

                      if($llegada != ''){break;}
                  }//fin del else del  if(count($registro_entrada) > 0)
                }// fin del el del chequeo de incidencia
              }
            }
          }//fin del foreach fechas
        }


          if($llegada != ''){
            $asistencia->_fecha = $fecha_inicio->format('Y-m-d');
            $asistencia->_estatus = $llegada;
            //$id = ResumenSemanalDao::insertPeriodoAsistencia($asistencia);
            //echo "Se ha insertado el id $id<br>";
          }
            $fecha_inicio->add(new \DateInterval('P1D'));
        }

        return $contadorFaltas;
      }
      
    }

  public function updateHorasExtra(){


    $colaboradorId = MasterDom::getData('colaborador_id');
    $periodoId = MasterDom::getData('prorrateo_periodo_id');
    $regreso = MasterDom::getData('regreso');
    $regreso = "/Incentivo/getIncentivosColaborador/{$colaboradorId}/{$periodoId}/{$regreso}";

    if(MasterDom::getData('status') == "INSERTAR"){
      $data = new \stdClass();
      $data->_catalogo_colaboradores_id = MasterDom::getData('colaborador_id');
      $data->_horas_extra = MasterDom::getData('horas_extra');
      $data->_prorrateo_periodo_id = MasterDom::getData('prorrateo_periodo_id');
      IncentivoDao::insertHorasExtraColaborador($data);
      $this->alerta(MasterDom::getData('colaborador_id'),"insertar-horas-extra",MasterDom::getData('prorrateo_periodo_id'),$regreso);

    }


    if(MasterDom::getData('status') == "MODIFICAR"){

      if(MasterDom::getData('horas_extra') == 0){
        $delete = IncentivoDao::deleteHorasExtra(MasterDom::getData('colaborador_id'), MasterDom::getData('prorrateo_periodo_id'));

        if($delete>0)
          $this->alerta(MasterDom::getData('colaborador_id'),"delete-horas-extra",MasterDom::getData('prorrateo_periodo_id'),$regreso);
        else
          $this->alerta(MasterDom::getData('colaborador_id'), "error", MasterDom::getData('prorrateo_periodo_id'), $regreso);
        
      }else{
          
          $id = IncentivoDao::updateHorasExtra(MasterDom::getData('horas_extra'), MasterDom::getData('colaborador_id'), MasterDom::getData('prorrateo_periodo_id'));

          if($id>0)
            $this->alerta(MasterDom::getData('colaborador_id'),"update-horas-extra",MasterDom::getData('prorrateo_periodo_id'),$regreso);
          else
            $this->alerta(MasterDom::getData('colaborador_id'), "error", MasterDom::getData('prorrateo_periodo_id'), $regreso);
      }
    }

    }

    /*
    Agregar Los incentivos que puede tener el colaborador 
    */
  public function agregarIncentivos(){
    $colaboradorId = MasterDom::getData('colaborador_id');
    $periodoId = MasterDom::getData('prorrateo_periodo_id');
    $tipo = MasterDom::getData('regreso');
    $regreso = "/Incentivo/incentivos/{$colaboradorId}/{$periodoId}/{$tipo}";

    if(count($_POST)<=3){
      $this->alerta(MasterDom::getData('colaborador_id'), "vacio", MasterDom::getData('prorrateo_periodo_id'), $regreso);
    }else{
      $incentivo = new \stdClass();
      foreach (MasterDom::getDataAll('agregar') as $key => $value) {
        $explode = explode("|", $value);
        $incentivo->_colaborador_id = $explode['0'];
        $incentivo->_prorrateo_periodo_id = $explode['1'];
        $incentivo->_catalogo_incentivo_id = $explode['2'];
        $incentivo->_cantidad = $explode['3'];
        $incentivo->_asignado = $explode['4'];
        $incentivo->_valido = $explode['5'];
        if($explode['5'] == 1){

        }else{
          //echo "1";
        }

        $insertarIncentivo = IncentivoDao::insertIncentivos($incentivo);
        }

    if($insertarIncentivo>0)
      $this->alerta(MasterDom::getData('colaborador_id'), "add", MasterDom::getData('prorrateo_periodo_id'), $regreso);
        else
      $this->alerta(MasterDom::getData('colaborador_id'), "error", MasterDom::getData('prorrateo_periodo_id'), $regreso);
  
    }
    }

    /*
      Actualiza los incentivos que ya han sido asignados al colaborador
    */
    public function updateIncentivos(){
    // ELIMINAR INCENTIVOS QUE PREVIAMENTE HAN SIDO ASIGNADOS
    $colaboradorId = MasterDom::getData('colaborador_id');
    $periodoId = MasterDom::getData('prorrateo_periodo_id');
    $tipo = MasterDom::getData('regreso');
    $regreso = "/Incentivo/incentivos/{$colaboradorId}/{$periodoId}/{$tipo}";
    
    $eliminarIncentivos = new \stdClass();
    $eliminarIncentivos->_colaborador_id = MasterDom::getData('colaborador_id');
    $eliminarIncentivos->_prorrateo_periodo_id = MasterDom::getData('prorrateo_periodo_id');
    $eliminar = IncentivoDao::eliminarIncentivos($eliminarIncentivos);

    // ELIMINAR TODOS LOS INCENTIVOS
    if(empty($_POST))
      $this->alerta(MasterDom::getData('colaborador_id'), "vacio", MasterDom::getData('prorrateo_periodo_id'), $regreso);
      $incentivo = new \stdClass();
      foreach (MasterDom::getDataAll('agregar') as $key => $value) {
        $explode = explode("|", $value);
        $incentivo->_colaborador_id = MasterDom::getData('colaborador_id');
        $incentivo->_prorrateo_periodo_id = $explode['1'];
        $incentivo->_catalogo_incentivo_id = $explode['2'];
        $incentivo->_cantidad = $explode['3'];
        $incentivo->_asignado = $explode['4'];
        $incentivo->_valido = $explode['5'];
        $insertarIncentivo = IncentivoDao::insertIncentivos($incentivo);
    }

    if($insertarIncentivo>0)
      $this->alerta(MasterDom::getData('colaborador_id'), "update-incentivos", MasterDom::getData('prorrateo_periodo_id'), $regreso);
    else
      $this->alerta(MasterDom::getData('colaborador_id'), "error", MasterDom::getData('prorrateo_periodo_id'), $regreso);
  }

  public function deleteIncentivosColaborador(){

    $colaboradorId = MasterDom::getData('colaboradorId');
    $periodoId = MasterDom::getData('periodoId');
    $tipo = MasterDom::getData('tipo');
    $regreso = "/Incentivo/getIncentivosColaborador/{$colaboradorId}/{$periodoId}/{$tipo}";

    $id = MasterDom::getDataAll('borrar');
    $arrayDelete = array();
    foreach ($id as $key => $value) {
      $delete = IncentivoDao::delete($value);
      array_push($arrayDelete, $delete);
    }

    $sumaArr = array_sum($arrayDelete);

    if($sumaArr>0)
      $this->alerta(MasterDom::getData('colaborador_id'), "delete", MasterDom::getData('prorrateo_periodo_id'), $regreso);
    else
      $this->alerta(MasterDom::getData('colaborador_id'), "error", MasterDom::getData('prorrateo_periodo_id'), $regreso);
  
  }

  /*
    Elimina los incentivos del colaborador que ya han sido asignados
  */
  public function deleteIncentivos(){

    $colaboradorId = MasterDom::getData('colaborador_id');
    $periodoId = MasterDom::getData('prorrateo_periodo_id');
    $tipo = MasterDom::getData('regreso');
    $regreso = "/Incentivo/incentivos/{$colaboradorId}/{$periodoId}/{$tipo}";

    $id = MasterDom::getDataAll('borrar');
    $arrayDelete = array();
    foreach ($id as $key => $value) {
      $delete = IncentivoDao::delete($value);
      array_push($arrayDelete, $delete);
    }

    $sumaArr = array_sum($arrayDelete);

    if($sumaArr>0)
      $this->alerta(MasterDom::getData('colaborador_id'), "delete", MasterDom::getData('prorrateo_periodo_id'), $regreso);
    else
      $this->alerta(MasterDom::getData('colaborador_id'), "error", MasterDom::getData('prorrateo_periodo_id'), $regreso);
  
  }

  /*
    Obtiene el ultimo periodo ya sea semanal o quincenal
  */
  public function getUltimoPeriodo($tipoPeriodo){

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

  public function alerta($id, $parametro, $colaborador_id, $seccion){
    $regreso = $seccion;

    if($parametro == 'add'){
      $mensaje = "Se ha agregado correctamente";
      $class = "success";
    }

    if($parametro == 'edit'){
      $mensaje = "Se ha modificado correctamente";
      $class = "success";
    }

    if($parametro == 'add-horas-extra'){
      $mensaje = "Se ha agregado las horas extra, correctamente";
      $class = "success";
    }

    if($parametro == 'update-horas-extra'){
      $mensaje = "Se ha cambiado las horas extra, correctamente";
      $class = "success";
    }

    if($parametro == 'insertar-horas-extra'){
      $mensaje = "Se ha insertado las horas extra, correctamente";
      $class = "success";
    }

    if($parametro == 'delete'){
      $mensaje = "Se ha eliminado, satisfactoriamente";
      $class = "success";
    }

    if($parametro == 'nothing'){
      $mensaje = "Posibles errores: <li>No intentaste actualizar ningún campo</li> <li>Este dato ya esta registrado, comunicate con soporte técnico</li> ";
      $class = "warning";
    }

    if($parametro == 'update-incentivos'){
      $mensaje = "Se han ha modificado la tabla de incentivos asignados";
      $class = "success";
    }

    if($parametro == 'no_cambios'){
      $mensaje = "No intentaste actualizar ningún campo";
      $class = "warning";
    }

    if($parametro == "vacio"){
      $mensaje = "No se ha recibido ningo incentivo para asignar.";
      $class = "warning";
    }

    if($parametro == 'union'){
      $mensaje = "Al parecer este campo de está ha sido enlazada con un campo de Catálogo de Colaboradores, ya que esta usuando esta información";
      $class = "info";
    }

    if($parametro == "error"){
      $mensaje = "Al parecer ha ocurrido un problema";
      $class = "danger";
    }

    if($parametro == "delete-horas-extra"){
      $mensaje = "Se ha eliminado las horas extra.";
      $class = "success";
    }

    View::set('class',$class);
    View::set('regreso',$regreso);
    View::set('mensaje',$mensaje);
    View::set('header',$this->_contenedor->header($extraHeader));
    View::set('footer',$this->_contenedor->footer($extraFooter));
    View::render("alerta");
    }

  public function setTablaAsistencia($idColaborador, $idPeriodo){
    

    $dias_traductor = array('Monday' => 'Lunes','Tuesday' => 'Martes','Wednesday' => 'Miercoles','Thursday' => 'Jueves','Friday' => 'Viernes','Saturday' => 'Sabado','Sunday' => 'Domingo');
    $meses_traductor = array(1 => 'ENE',2 => 'FEB',3 => 'MAR',4 => 'ABR',5 => 'MAY',6 => 'JUN',7 => 'JUL',8 => 'AGO',9 => 'SEP', 10 => 'OCT', 11 => 'NOV', 12 => 'DIC');
    $periodo = ResumenesDao::getPeriodoById($idPeriodo);
    $hidden = ($periodo['status'] == 0)? '': ' hidden ';
    $fecha_fin = new \DateTime($periodo['fecha_fin']);
    $datos = new \stdClass();
    $datos->tipo = ucwords(strtolower($periodo['tipo']));
    $encabezado =<<<html
        <th>No. Empleado</th>
        <th>Nombre</th>
        <th>Informacion</th>
html;
    $j = 0;
    $colaboradores = ResumenesDao::getAllColaboradorById($idColaborador);
    foreach($colaboradores as $key => $value){


      $nombre_planta = strtolower($value['identificador']);
      $nombreEmpleado = utf8_encode($value['nombre'])."<br />".utf8_encode($value['apellido_paterno'])."<br />".utf8_encode($value['apellido_materno']);
      $tabla .=<<<html
        <tr>
          <td>{$value['numero_empleado']}</td>
    <td>$nombreEmpleado</td>
          <td>
            <b>Departamento</b> {$value['nombre_departamento']} <br>
            <b>Identificador</b> {$value['nombre_planta']} <br>
          </td>
html;
        $datos->numero_empleado = $value['numero_empleado'];
        $fecha_inicio = new \DateTime($periodo['fecha_inicio']);
        $datos->catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];

        $ultimo_horario = IncidenciaDao::getUltimoHorario($datos);
        $horarios = IncidenciaDao::getHorariosById($datos);

        /**************
        print_r($horarios);
        echo '<br><br>';
        /**************/

        $contadorRetardos = 0;
        $catalogo_horario_id = 0;
        $num_semana = 0;

        $existe = $this->buscarHorario($horarios, $ultimo_horario);
          
        if((!$existe) && $catalogo_horario_id == 0){
          $datosBusqueda = new \stdClass();
          $datosBusqueda->catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];
          $catalogo_horario_id = $this->obtenerHorarioByDay(IncidenciaDao::getHorarioLaboral($datosBusqueda), $nombre_dia_semana);
        }

        while($fecha_inicio <= $fecha_fin){          
          $nombre_dia_semana = $dias_traductor[$fecha_inicio->format('l')];
          $dia_aux = '';
          $llegada = '';
          /******************************************************************************************************************************************************/
          
          $valor1 = '';
          if($value['horario_tipo'] == 'semanal'){
            $nombre_dia_semana = $dias_traductor[$fecha_inicio->format('l')];
            $num_semana = $fecha_inicio->format('N');
            if( $num_semana == 2 || $catalogo_horario_id == 0){
                  if($catalogo_horario_id == 0){
                    if(empty($ultimo_horario)){
                      $catalogo_horario_id = $horarios[0]['catalogo_horario_id'];
                    }else{
                      if($num_semana != 2){//en caso que no sea martes y sea el primer dia del periodo
                        $catalogo_horario_id = $ultimo_horario['catalogo_horario_id'];
                      }else{
                        for($llave=0; $llave<count($horarios); $llave++) {
                          if($horarios[$llave]['catalogo_horario_id'] == $ultimo_horario['catalogo_horario_id']){
                            if( ($llave+1) >= count($horarios) ){
                              $catalogo_horario_id = $horarios[0]['catalogo_horario_id'];
                            }else{
                              $catalogo_horario_id = $horarios[$llave+1]['catalogo_horario_id'];
                            }
                          }
                        }//fin del for busca siguiente horario
                      }
                    }
                  }else{//en caso que $catalogo_horario_id no sea cero
                    for($llave=0; $llave<count($horarios); $llave++) {
                      if($horarios[$llave]['catalogo_horario_id'] == $catalogo_horario_id){
                          if( ($llave+1) >= count($horarios) ){
                            $catalogo_horario_id = $horarios[0]['catalogo_horario_id'];
                          }else{
                            $catalogo_horario_id = $horarios[$llave+1]['catalogo_horario_id'];
                          }
                      }
                      if($catalogo_horario_id != $catalogo_horario_id_anterior){break;}
                    }//fin del for de los horarios

                  }//fin del for de los horarios
            }
        }else{
          if($value['horario_tipo'] == 'diario'){
            $catalogo_horario_id = '';
          }
        }

        $datos->catalogo_horario_id = $catalogo_horario_id;
        
        /******************************************************************************************************************************************************/
        $horario_laboral = IncidenciaDao::getHorarioLaboralById($datos);
          foreach ($horario_laboral as $llave1 => $valor1) {
            $nombre_horario = $valor1['horario'];
            $color = '';
            if($dia_aux != $valor1['dia_semana']){
              $dia_aux = $valor1['dia_semana'];
              if($dia_aux  == $dias_traductor[$fecha_inicio->format('l')]){
                $datos->_catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];
                $datos->_fecha = $fecha_inicio->format('Y-m-d');
                $incidencia = ResumenesDao::getIncidencia($datos);
                if(count($incidencia)>0){
                  $llegada = $incidencia[0]['identificador_incidencia'];
                  $color = $incidencia[0]['color'];
                  if($incidencia[0]['genera_falta'] == 1){
                    $llegada = 'FF'; // falta (-1)
                    if(count(ResumenSemanalDao::getDiaFestivo($fecha_inicio->format('Y-m-d'))) >0){
                      $llegada = 'FDF'; //falta en dia festivo (-26)
                    }
                  }
                }else{
                  if (strtolower($nombre_horario) == 'nocturno'){
                    $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' 12:00:00';  
                  }else{
                    $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' 00:00:00';
                  }

                  $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' '.Resumenes::sumarMinutos($valor1['hora_entrada'], intval($valor1['tolerancia_entrada']));
                  $registro_entrada = ResumenesDao::getAsistencia($datos, $nombre_planta);

                  if(count($registro_entrada) > 0){
                    $llegada = 'A'; // asistencia (0)
                    if(count(ResumenSemanalDao::getDiaFestivo($fecha_inicio->format('Y-m-d'))) >0){
                      $llegada = 'AA'; // asistencia en dia festivo (-24)
                    }

                  }else{

                    if (strtolower($nombre_horario) == 'nocturno'){
                      $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' 12:00:00';  
                    }else{
                      $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' 00:00:00';
                    }

                    $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' 23:59:59';
                    $datos->catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];
                    $registro_entrada = ResumenesDao::getAsistencia($datos, $nombre_planta);

                if(count($registro_entrada) > 0){
                      if($value['privilegiado'] == 1){
                        $llegada = 'A';
                      }else{
                        $llegada = 'R'; //retardo (-2)
                      }
                      if(count(ResumenSemanalDao::getDiaFestivo($fecha_inicio->format('Y-m-d'))) >0){
                        if($value['privilegiado'] == 1){
                          $llegada = 'A';
                        }else{
                          $llegada .= 'RDF'; //retardo en dia festivo (-25)
                        }
                      }
                      $contadorRetardos[$valor1['catalogo_horario_id']] += 1;
                }else{
                      if($value['privilegiado'] == 1){
                        $llegada = 'A';
                      }else{
                        $llegada = 'FF'; // falta (-1)
                      }
                      if(count(ResumenSemanalDao::getDiaFestivo($fecha_inicio->format('Y-m-d'))) >0){
                        $llegada = 'FDF'; //falta en dia festivo (-26)
                      }
                    }

                    if( $contadorRetardos[$valor1['catalogo_horario_id']] == $valor1['numero_retardos'] ){
                      if($value['privilegiado'] == 1){
                        $llegada = 'A';
                      }else{
                        $llegada = 'FR'; //falta por retardo (-22)
                      }
                      if(count(ResumenSemanalDao::getDiaFestivo($fecha_inicio->format('Y-m-d'))) >0){
                        $llegada .= 'FDF';
                      }
                      $contadorRetardos[$valor1['catalogo_horario_id']] = 0;
                    }

                    if($llegada != ''){break;}
                  }//fin del else del  if(count($registro_entrada) > 0)
                }// fin del el del chequeo de incidencia
              }
            }
          }//fin del for del recorrido de los horarios


          if($llegada == ''){
            $datos->_catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];
            $datos->_fecha = $fecha_inicio->format('Y-m-d');
            $incidencia = ResumenesDao::getIncidencia($datos);
            if(count($incidencia)>0){
              $llegada = $incidencia[0]['identificador_incidencia'];
              $color = $incidencia[0]['color'];
            }else{
              $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' 00:00:00';
              if (strtolower($nombre_horario) == 'nocturno'){
                  $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' 12:00:00';  
              }else{
                  $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' 00:00:00';
              }
              $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' 23:59:59';
              $registro_entrada = ResumenesDao::getAsistencia($datos, $nombre_planta);
              if(count(ResumenSemanalDao::getDiaFestivo($fecha_inicio->format('Y-m-d'))) >0){
                $llegada = 'DF'; // dia festivo (-23)
                if(count($registro_entrada)>0){
                  $llegada = 'AA'; // asistencia en dia festivo (-24)
                }
              }else{
                if(count($registro_entrada)>0){
                  $llegada = 'AA'; // asistencia (0) en dia  no laboral
                }
              }

              if($llegada == ''){
                $llegada = 'D'; //descanso 
              }
            }
          }else{
              $datos->_catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];
              $datos->_fecha = $fecha_inicio->format('Y-m-d');
              $incidencia = ResumenesDao::getIncidencia($datos);
              if(count($incidencia)>0){
                $llegada = $incidencia[0]['identificador_incidencia'];
                $color = $incidencia[0]['color'];
              }
          }

        if ($llegada == 'A' || $llegada =='AA'){$color = 'green';}
        elseif($llegada == 'D' || $llegada == 'DF'){$color = 'gray';}
        elseif($llegada == 'R' || $llegada == 'RDF'){$color = 'yellow';}
        elseif ($llegada == 'FF' || $llegada == 'F'){$color = 'red';}
        elseif ($llegada == 'FR'){$color = 'orange';}
        $fecha_temp = new \DateTime(date('Y-m-d'));

        if($fecha_inicio > $fecha_temp){
          $tabla .=<<<html
          <td style="text-align: center; vertical-align: middle; font-size: 18px;" bgcolor="#f1f1f1"><span><label style="color: {$color};"> --- </label></span></td>
html;
        }else{
          $tabla .=<<<html
          <td style="text-align: center; vertical-align: middle; font-size: 18px;" bgcolor="#f1f1f1"><span><label style="color: {$color};"> {$llegada}</label></span></td>
html;
        }
        
        if($j==0){
          $encabezado .=<<<html
            <td>{$fecha_inicio->format('d')}-{$meses_traductor[intval($fecha_inicio->format('m'))]} </td>
html;
            }
            $fecha_inicio->add(new \DateInterval('P1D'));
      
    }//fin del while del recorrido de fechas
        //Incidencia/checadorFechas/195/22/semanales/
        $tipoPagoColaborador = "";
        if($value['pago'] == 'Semanal'){
          $tipoPagoColaborador = "semanales";
        }else{
          $tipoPagoColaborador = "quincenales";
        }
        // $idColaborador, $idPeriodo
         $tabla .=<<<html
          <td style="vertical-align: middle;"> 
            <a href="/Incidencia/checadorFechas/{$idColaborador}/{$idPeriodo}/{$tipoPagoColaborador}/" target="_blank" class="btn btn-primary"> Incidencias </a>
          </td>
        </tr>
html;
    $j++;
  }
  $encabezado .=<<<html
    <td>Ver Incidencias</td>
html;

    $html = <<<html
      <div class="dataTable_wrapper">
            <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
              {$encabezado}
              <tbody>
                {$tabla}
              </tbody>
            </table>
          </div>
html;
    return $html;
  }

  public function getFaltasColaborador($idColaborador, $idPeriodo){

    $dias_traductor = array('Monday' => 'Lunes','Tuesday' => 'Martes','Wednesday' => 'Miercoles','Thursday' => 'Jueves','Friday' => 'Viernes','Saturday' => 'Sabado','Sunday' => 'Domingo');
    $meses_traductor = array(1 => 'ENE',2 => 'FEB',3 => 'MAR',4 => 'ABR',5 => 'MAY',6 => 'JUN',7 => 'JUL',8 => 'AGO',9 => 'SEP', 10 => 'OCT', 11 => 'NOV', 12 => 'DIC');
    $periodo = ResumenesDao::getPeriodoById($idPeriodo);
    $hidden = ($periodo['status'] == 0)? '': ' hidden ';
    $fecha_fin = new \DateTime($periodo['fecha_fin']);
    $datos = new \stdClass();
    $datos->tipo = ucwords(strtolower($periodo['tipo']));
    $encabezado =<<<html
        <th>No. Empleado</th>
        <th>Nombre</th>
        <th>Informacion</th>
html;
    $j = 0;
    $colaboradores = ResumenesDao::getAllColaboradorById($idColaborador);
    foreach($colaboradores as $key => $value){  


      $nombre_planta = strtolower($value['identificador']);
      $nombreEmpleado = utf8_encode($value['nombre'])."<br />".utf8_encode($value['apellido_paterno'])."<br />".utf8_encode($value['apellido_materno']);
      $tabla .=<<<html
        <tr>
          <td>{$value['numero_empleado']}</td>
    <td>$nombreEmpleado</td>
          <td>
            <b>Departamento</b> {$value['nombre_departamento']} <br>
            <b>Identificador</b> {$value['nombre_planta']} <br>
          </td>
html;
        $datos->numero_empleado = $value['numero_empleado'];
        $fecha_inicio = new \DateTime($periodo['fecha_inicio']);
        $datos->catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];

        $ultimo_horario = IncidenciaDao::getUltimoHorario($datos);
        $horarios = IncidenciaDao::getHorariosById($datos);

        /**************
        print_r($horarios);
        echo '<br><br>';
        /**************/

        $contadorRetardos = 0;
        $catalogo_horario_id = 0;
        $num_semana = 0;

        $existe = $this->buscarHorario($horarios, $ultimo_horario);
          
        if((!$existe) && $catalogo_horario_id == 0){
          $datosBusqueda = new \stdClass();
          $datosBusqueda->catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];
          $catalogo_horario_id = $this->obtenerHorarioByDay(IncidenciaDao::getHorarioLaboral($datosBusqueda), $nombre_dia_semana);
        }

        $arrayFaltas = array();

        while($fecha_inicio <= $fecha_fin){          
          $nombre_dia_semana = $dias_traductor[$fecha_inicio->format('l')];
          $dia_aux = '';
          $llegada = '';
          /******************************************************************************************************************************************************/
          
          $valor1 = '';
          if($value['horario_tipo'] == 'semanal'){
            $nombre_dia_semana = $dias_traductor[$fecha_inicio->format('l')];
            $num_semana = $fecha_inicio->format('N');
            if( $num_semana == 2 || $catalogo_horario_id == 0){
                  if($catalogo_horario_id == 0){
                    if(empty($ultimo_horario)){
                      $catalogo_horario_id = $horarios[0]['catalogo_horario_id'];
                    }else{
                      if($num_semana != 2){//en caso que no sea martes y sea el primer dia del periodo
                        $catalogo_horario_id = $ultimo_horario['catalogo_horario_id'];
                      }else{
                        for($llave=0; $llave<count($horarios); $llave++) {
                          if($horarios[$llave]['catalogo_horario_id'] == $ultimo_horario['catalogo_horario_id']){
                            if( ($llave+1) >= count($horarios) ){
                              $catalogo_horario_id = $horarios[0]['catalogo_horario_id'];
                            }else{
                              $catalogo_horario_id = $horarios[$llave+1]['catalogo_horario_id'];
                            }
                          }
                        }//fin del for busca siguiente horario
                      }
                    }
                  }else{//en caso que $catalogo_horario_id no sea cero
                    for($llave=0; $llave<count($horarios); $llave++) {
                      if($horarios[$llave]['catalogo_horario_id'] == $catalogo_horario_id){
                          if( ($llave+1) >= count($horarios) ){
                            $catalogo_horario_id = $horarios[0]['catalogo_horario_id'];
                          }else{
                            $catalogo_horario_id = $horarios[$llave+1]['catalogo_horario_id'];
                          }
                      }
                      if($catalogo_horario_id != $catalogo_horario_id_anterior){break;}
                    }//fin del for de los horarios

                  }//fin del for de los horarios
            }
        }else{
          if($value['horario_tipo'] == 'diario'){
            $catalogo_horario_id = '';
          }
        }

        $datos->catalogo_horario_id = $catalogo_horario_id;
        
        /******************************************************************************************************************************************************/
        $horario_laboral = IncidenciaDao::getHorarioLaboralById($datos);
          foreach ($horario_laboral as $llave1 => $valor1) {
            $nombre_horario = $valor1['horario'];
            $color = '';
            if($dia_aux != $valor1['dia_semana']){
              $dia_aux = $valor1['dia_semana'];
              if($dia_aux  == $dias_traductor[$fecha_inicio->format('l')]){
                $datos->_catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];
                $datos->_fecha = $fecha_inicio->format('Y-m-d');
                $incidencia = ResumenesDao::getIncidencia($datos);
                if(count($incidencia)>0){
                  $llegada = $incidencia[0]['identificador_incidencia'];
                  $color = $incidencia[0]['color'];
                  if($incidencia[0]['genera_falta'] == 1){
                    $llegada = 'FF'; // falta (-1)
                    if(count(ResumenSemanalDao::getDiaFestivo($fecha_inicio->format('Y-m-d'))) >0){
                      $llegada = 'FDF'; //falta en dia festivo (-26)
                    }
                  }
                }else{
                  if (strtolower($nombre_horario) == 'nocturno'){
                    $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' 12:00:00';  
                  }else{
                    $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' 00:00:00';
                  }

                  $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' '.Resumenes::sumarMinutos($valor1['hora_entrada'], intval($valor1['tolerancia_entrada']));
                  $registro_entrada = ResumenesDao::getAsistencia($datos, $nombre_planta);

                  if(count($registro_entrada) > 0){
                    $llegada = 'A'; // asistencia (0)
                    if(count(ResumenSemanalDao::getDiaFestivo($fecha_inicio->format('Y-m-d'))) >0){
                      $llegada = 'AA'; // asistencia en dia festivo (-24)
                    }

                  }else{

                    if (strtolower($nombre_horario) == 'nocturno'){
                      $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' 12:00:00';  
                    }else{
                      $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' 00:00:00';
                    }

                    $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' 23:59:59';
                    $datos->catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];
                    $registro_entrada = ResumenesDao::getAsistencia($datos, $nombre_planta);

                if(count($registro_entrada) > 0){
                      if($value['privilegiado'] == 1){
                        $llegada = 'A';
                      }else{
                        $llegada = 'R'; //retardo (-2)
                      }
                      if(count(ResumenSemanalDao::getDiaFestivo($fecha_inicio->format('Y-m-d'))) >0){
                        if($value['privilegiado'] == 1){
                          $llegada = 'A';
                        }else{
                          $llegada .= 'RDF'; //retardo en dia festivo (-25)
                        }
                      }
                      $contadorRetardos[$valor1['catalogo_horario_id']] += 1;
                }else{
                      if($value['privilegiado'] == 1){
                        $llegada = 'A';
                      }else{
                        $llegada = 'FF'; // falta (-1)
                      }
                      if(count(ResumenSemanalDao::getDiaFestivo($fecha_inicio->format('Y-m-d'))) >0){
                        $llegada = 'FDF'; //falta en dia festivo (-26)
                      }
                    }

                    if( $contadorRetardos[$valor1['catalogo_horario_id']] == $valor1['numero_retardos'] ){
                      if($value['privilegiado'] == 1){
                        $llegada = 'A';
                      }else{
                        $llegada = 'FR'; //falta por retardo (-22)
                      }
                      if(count(ResumenSemanalDao::getDiaFestivo($fecha_inicio->format('Y-m-d'))) >0){
                        $llegada .= 'FDF';
                      }
                      $contadorRetardos[$valor1['catalogo_horario_id']] = 0;
                    }

                    if($llegada != ''){break;}
                  }//fin del else del  if(count($registro_entrada) > 0)
                }// fin del el del chequeo de incidencia
              }
            }
          }//fin del for del recorrido de los horarios


          if($llegada == ''){
            $datos->_catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];
            $datos->_fecha = $fecha_inicio->format('Y-m-d');
            $incidencia = ResumenesDao::getIncidencia($datos);
            if(count($incidencia)>0){
              $llegada = $incidencia[0]['identificador_incidencia'];
              $color = $incidencia[0]['color'];
            }else{
              $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' 00:00:00';
              if (strtolower($nombre_horario) == 'nocturno'){
                  $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' 12:00:00';  
              }else{
                  $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' 00:00:00';
              }
              $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' 23:59:59';
              $registro_entrada = ResumenesDao::getAsistencia($datos, $nombre_planta);
              if(count(ResumenSemanalDao::getDiaFestivo($fecha_inicio->format('Y-m-d'))) >0){
                $llegada = 'DF'; // dia festivo (-23)
                if(count($registro_entrada)>0){
                  $llegada = 'AA'; // asistencia en dia festivo (-24)
                }
              }else{
                if(count($registro_entrada)>0){
                  $llegada = 'AA'; // asistencia (0) en dia  no laboral
                }
              }

              if($llegada == ''){
                $llegada = 'D'; //descanso 
              }
            }
          }else{
              $datos->_catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];
              $datos->_fecha = $fecha_inicio->format('Y-m-d');
              $incidencia = ResumenesDao::getIncidencia($datos);
              if(count($incidencia)>0){
                $llegada = $incidencia[0]['identificador_incidencia'];
                $color = $incidencia[0]['color'];
              }
          }

        if ($llegada == 'A' || $llegada =='AA'){$color = 'green';}
        elseif($llegada == 'D' || $llegada == 'DF'){$color = 'gray';}
        elseif($llegada == 'R' || $llegada == 'RDF'){$color = 'yellow';}
        elseif ($llegada == 'FF' || $llegada == 'F'){$color = 'red';}
        elseif ($llegada == 'FR'){$color = 'orange';}
        $fecha_temp = new \DateTime(date('Y-m-d'));

        if($fecha_inicio > $fecha_temp){
          $tabla .=<<<html
          <td style="text-align: center; vertical-align: middle; font-size: 18px;" bgcolor="#f1f1f1"><span><label style="color: {$color};"> --- </label></span></td>
html;
        }else{
          $tabla .=<<<html
          <td style="text-align: center; vertical-align: middle; font-size: 18px;" bgcolor="#f1f1f1"><span><label style="color: {$color};"> {$llegada}</label></span></td>
html;
        }
        
        if($j==0){
          $encabezado .=<<<html
            <td>{$fecha_inicio->format('d')}-{$meses_traductor[intval($fecha_inicio->format('m'))]} </td>
html;
            }
            $fecha_inicio->add(new \DateInterval('P1D'));
    
    if($llegada == 'FF')
          array_push($arrayFaltas, 1);
        
    }
      $j++;
    }

    $cantidadFaltas = count($arrayFaltas);
    return $cantidadFaltas;//count($arrayFaltas));
  }

  public function buscarHorario($horarios, $horario){
    foreach ($horarios as $key => $value) {
      if($value['catalogo_horario_id'] == $horario['catalogo_horario_id']){
        return true;
      }
    }
    return false;
  }

  public function obtenerHorarioByDay($horario_laboral, $nombre_dia){
    foreach ($horario_laboral as $key => $value) {
      if($value['dia_semana'] == $nombre_dia){
        return $value['catalogo_horario_id'];
      }
    }

    return 'NULL';
  }
    
}
