<?php
namespace App\controllers;
defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \Core\Controller;
use \App\models\Incentivo as IncentivoDao;
use \App\models\ResumenSemanal AS ResumenSemanalDao;
use \App\models\General AS GeneralDao;

class Incentivo extends Controller{

	function __construct(){
		parent::__construct();
		$this->_contenedor = new Contenedor;
		View::set('header',$this->_contenedor->header());
		View::set('footer',$this->_contenedor->footer());
  } 

  public function index(){}

	public function semanales(){
    $user = GeneralDao::getDatosUsuario($this->__usuario);

    if($user['perfil_id'] == 6){
      $val = ($user['catalogo_planta_id'] == 1) ? 6 : 5;
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
    }

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

      if($user['perfil_id'] == 1 ){ // Si el usuario es root
        $tituloVista = "TODOS los Incentivos de TODAS LAS PLANTAS ";
        $val = 6;
      }
    }
      	
    View::set('tipoPeriodo',$tituloVista); // Identificacion del periodo
    View::set('tituloIncentivos',"Semanales");
    View::set('option',$this->getPeriodosHistoricos("SEMANAL",$idPeriodo)); // Optiene todos los periodos procesados(historicos) semanales
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


    View::set('msjPeriodo',$msjPeriodofechas);
    View::set('tipoPeriodo',$tituloVista); // Identificacion del periodo
    View::set('tituloIncentivos',"Semanales");
    View::set('option',$this->getPeriodosHistoricos("SEMANAL",$idPeriodo)); // Optiene todos los periodos procesados(historicos) semanales
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

    public function getTabla($tipo, $idPeriodo, $vista, $perfilUsuario, $catalogDepartamentoId, $catalogoPlantaId, $estatusRH, $nombrePlanta){
    	$tabla = "";
    	foreach (GeneralDao::getColaboradores($tipo, $perfilUsuario, $catalogDepartamentoId, $catalogoPlantaId, $estatusRH, $nombrePlanta) as $key => $value) {  

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
						<a href="/Incentivo/incentivos/{$value['catalogo_colaboradores_id']}/{$idPeriodo}/{$vista}" type="submit" name="id_empresa" class="btn btn-primary"><i class="fa fa-plus"></i> </a>
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
      $html = <<<html
        <b>( {$fechaIni} al {$fechaFin} )</b><small>periodo Abierto</small>
html;
      return $html;
    }

    /*
		Obtiene los incentivos SEMANALES O QUINCENALES, que ya han sido procesados
    */
    public function getPeriodosHistoricos($tipo, $periodoObtenido){
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
			$horasExtra = $horasExtra['horas_extra']."0";
		else
			$horasExtra = $horasExtra['horas_extra'];

		return $horasExtra;
    }

    /*
		Coloca la vista para poner horas extra y eliminar las
    */
    public function setHorasExtra($idColaborador,$idPeriodo,$status,$regreso,$value){
    	if($value==1){
        $html = <<<html
      <div class="col-md-12 col-sm-12 col-xs-12">
            <form name="form-add" id="" action="/Incentivo/updateHorasExtra" method="POST">
              <input type="hidden" value="{$idColaborador}" name="colaborador_id">
              <input type="hidden" value="{$idPeriodo}" name="prorrateo_periodo_id">
              <input type="hidden" value="{$status}" name="status">
              <input type="hidden" value="{$regreso}" name="regreso">
                <div class="row">
                  <div class="col-md-12 col-sm-12 col-xs-12">
                    <select class="form-control" name="horas_extra" id="horas_extra">
                      <option value="" disabled selected>Horas extra</option>
html;
        for ($i=0; $i <= 15; $i++) { 
          if($i == 1)
            $html .= "<option value=". $i .">" . $i. " hora </option>";
          else{
            if($i == 0)
              $html .= "<option value=". $i .">" . $i. " hora - Elimina las horas asignadas</option>";
            else
              $html .= "<option value=". $i .">" . $i. " horas </option>";
          }
        }
        $html .=<<<html
                    </select>
                  </div>
                  <div class="col-md-2 col-sm-2 col-xs-2">
                    <br><input type="submit" class="btn btn-info" value="Modificar Horas Extra">
                  </div>
                </div>

            </form>
          </div>
html;
      }else{
        $html = <<<html
      <div class="col-md-12 col-sm-12 col-xs-12">
            <form name="form-add" id="" action="/Incentivo/updateHorasExtra" method="POST">
              <input type="hidden" value="{$idColaborador}" name="colaborador_id">
              <input type="hidden" value="{$idPeriodo}" name="prorrateo_periodo_id">
              <input type="hidden" value="{$status}" name="status">
              <input type="hidden" value="{$regreso}" name="regreso">
                <div class="row">
                  <div class="col-md-12 col-sm-12 col-xs-12">
                    <select class="form-control" name="horas_extra" id="horas_extra">
                      <option value="" disabled selected>Horas extra</option>
html;
        for ($i=1; $i <= 15; $i++) { 
          if($i == 1)
            $html .= "<option value=". $i .">" . $i. " hora </option>";
          else
            $html .= "<option value=". $i .">" . $i. " horas </option>";
        }
        $html .=<<<html
                    </select>
                  </div>
                  <div class="col-md-2 col-sm-2 col-xs-2">
                    <br><input type="submit" class="btn btn-success" value="Agregar Horas Extra">
                  </div>
                </div>

            </form>
          </div>
html;
      }
		  return $html;
    }


    /*
		Vista de incentivos asignados por colaborador
    */
	public function incentivos($idColaborador, $idPeriodo, $tipo){
		$extraFooter=<<<html
			<script>
        $(document).ready(function(){

          $("#muestra-cupones1").tablesorter();
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


          $(".switch").bootstrapSwitch();


          $('input[name="domingoProcesos"]').on('switchChange.bootstrapSwitch', function(event, state) {
            if(state){
              $('input[name="domingoLaborado"]').bootstrapSwitch('state', false, false);
            }else{
            }
          });

          $('input[name="domingoLaborado"]').on('switchChange.bootstrapSwitch', function(event, state) {
            if(state){
              $('input[name="domingoProcesos"]').bootstrapSwitch('state', false, false);
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
    $incentivosNoche = IncentivoDao::getIncentivosNoche($idColaborador, $idPeriodo);


    $showTextBtnActualizar = ( count($domingoProcesos)>0 || count($domingoLaborado)>0) ? "Actualizar datos" : "Agrear Valores";
    $accionesComplementarias = ( count($domingoProcesos)>0 || count($domingoLaborado)>0) ? "updateDomingos" : "addDomingos";
    $btnAccionesComplementarias = ( count($domingoProcesos)>0 || count($domingoLaborado)>0) ? "btn-info" : "btn-success";


    // Validar que estos datos ya existen en la base de datos
    $checkeddomingoProcesos = (count($domingoProcesos)>0) ? "checked" : "";    
    $checkeddomingoLaborado = (count($domingoLaborado)>0) ? "checked" : "";
    View::set('showTextBtnActualizar',$showTextBtnActualizar); // Texto del buton de agregar datos
    View::set('checkdomingoProcesos', $checkeddomingoProcesos); // value CHECKED del input checked
    View::set('checkdomingoLaborado', $checkeddomingoLaborado); // value CHECKED del input checked
    // Valores del complementarios 
    View::set('domingoProcesos', $this->calculoDomingoProcesos(GeneralDao::getDatosColaborador($idColaborador))); // Muestra la cantidad que puede tener por domingos procesados
    View::set('domingoLaborado', $this->calculoDomingoLaborado(GeneralDao::getDatosColaborador($idColaborador))); // Muestra la cantidad que puede tener por domingo laborales
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

  public function calculoDomingoProcesos($datosColaborador){
    $operacion1 = ($datosColaborador['sal_diario'] * 0.25) + ($datosColaborador['sal_diario'] * 3);
    $masPrimaDominical = $operacion1 * .25;
    $resultado = $masPrimaDominical + $operacion1;
    return number_format($resultado,2,'.','');
  }

  public function calculoDomingoLaborado($datosColaborador){
    $operacion1 = ($datosColaborador['sal_diario'] * 0.25) + ($datosColaborador['sal_diario'] * 2);
    $masPrimaDominical = $operacion1 * .25;
    $resultado = $masPrimaDominical + $operacion1;
    return number_format($resultado,2,'.','');
  }

  public function updateDomingos(){
    $idPeriodo = MasterDom::getData('prorrateo_periodo_id');
    $idColaborador = MasterDom::getData('colaborador_id');
    $regreso = MasterDom::getData('regreso');

    $domingoProcesos = MasterDom::getData('domingoProcesos');
    $domingoLaborado = MasterDom::getData('domingoLaborado');

    $sdtClass = new \stdClass();
    $sdtClass->_catalogo_colaboradores_id = $idColaborador;
    $sdtClass->_prorrateo_periodo_id = $idPeriodo;
    
    $html = "<div>";
    $dp = IncentivoDao::getDomingoProcesos($idColaborador, $idPeriodo);
    $dl = IncentivoDao::getDomingoLaborado($idColaborador, $idPeriodo);
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
    }else{
      if(count($dp)>0)
        IncentivoDao::redelete("prorrateo_domigo_procesos", $idColaborador, $idPeriodo);

      if(count($dl)>0)
        IncentivoDao::redelete("prorrateo_domigo_laborado", $idColaborador, $idPeriodo);
      
      $html .= $this->mensajeComplementos("Ahora ya no tienes ningun valor monetario para domingo de procesos o domingo laborado", "alert-success", $idColaborador, $idPeriodo, $regreso);
    }
    
    $html .= "</div>";

    View::set('regreso',"/Incentivo/incentivos/{$idColaborador}/{$idPeriodo}/{$regreso}");
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
    }else{
      $html .= $this->mensajeComplementos("No se inserto ning&uacute; valor, ya que no se selecciono alguno, favor de indicar cual se asignara", "alert-warning", $colaboradorId, $periodoId, $regreso);
    }
    
    $html .= "</div>";

    View::set('regreso',"/Incentivo/incentivos/{$colaboradorId}/{$periodoId}/{$regreso}");
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
    $inputIncentivoDeNoche = MasterDom::getData('incentivo_de_noche');
    // VALORES DE INPUT 
    $valueDomingoDeProcesos = MasterDom::getData('value_domingo_de_procesos');
    $valueDomingoLaborado = MasterDom::getData('value_domigo_laborado');
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
    $incentivosNoche = IncentivoDao::getIncentivosNoche($idColaborador, $idPeriodo);
    

    // PARAMETROS DEL CHECADOR
    $inputDomingoDeProcesos = MasterDom::getData('domingo_de_procesos');
    $inputDomingoLaborado = MasterDom::getData('domingo_laborado');
    $inputIncentivoDeNoche = MasterDom::getData('incentivo_de_noche');
    // VALORES DE INPUT 
    $valueDomingoDeProcesos = MasterDom::getData('value_domingo_de_procesos');
    $valueDomingoLaborado = MasterDom::getData('value_domigo_laborado');
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

    View::set('regreso',"/Incentivo/incentivos/{$colaboradorId}/{$periodoId}/{$regreso}");
    View::set('secciones',$html);
    View::set('header',$this->_contenedor->header());
    View::set('footer',$this->_contenedor->footer());
    View::render("alerta_multi");

  }

  public function mensajeComplementos($mensaje, $alert, $colaboradorId, $periodoId, $regreso){
    $html =<<<html
      <div class="alert {$alert} alert-dismissable">
        {$mensaje} <a href="/Incentivo/incentivos/{$colaboradorId}/{$periodoId}/{$regreso}" class="alert-link">Regreso</a>.
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
					<td style="text-align:center; vertical-align:middle;">{$fijo}</td>
					<td style="text-align:center; vertical-align:middle;" class="td_checkbox_{$contador}" >
html;
			$faltas = $this->getStatusAsistenciasColaborador($idColaborador, $idPeriodo);

      //echo $faltas.'::::::::::<br>';

			if($faltas == 0){
				$asignado = 1;
				$tipo = ($value['tipo'] == "MENSUAL") ? 0:1;
			}else{
				$asignado = 0;
				$tipo = 0;
			}

			if($value['fijo'] == "si"){
				$aplicaIncentivoSistema = $this->getStatusAsistenciasColaborador($idColaborador, $idPeriodo);
				$checked = ($aplicaIncentivoSistema > 0) ? "":"checked";
				$disabled = ($aplicaIncentivoSistema > 0) ? "":"disabled";

				if($aplicaIncentivoSistema == 0){
					$tabla.=<<<html
						<input type="hidden" value="{$value['catalogo_colaboradores_id']}|{$idPeriodo}|{$value['catalogo_incentivo_id']}|{$value['cantidad']}|{$asignado}|{$tipo}" name="agregar[]" >
						<input type="checkbox" class="switch" data-on-text="SI" data-off-text="NO" {$checked} disabled value="{$value['catalogo_colaboradores_id']}|{$idPeriodo}|{$value['catalogo_incentivo_id']}|{$value['cantidad']}|{$asignado}|{$tipo}" name="agregar[]" >
html;
            	}

				if($aplicaIncentivoSistema > 0){
					$tabla.=<<<html
					<input type="checkbox" class="switch" data-on-text="SI" data-off-text="NO" disabled value="{$value['catalogo_colaboradores_id']}|{$idPeriodo}|{$value['catalogo_incentivo_id']}|{$value['cantidad']}|{$asignado}|{$tipo}" name="agregar[]">
html;
            	}
        }else{
			$tabla .= <<<html
			<input type="checkbox" class="switch" data-on-text="SI" data-off-text="NO" {$checked} value="{$value['catalogo_colaboradores_id']}|{$idPeriodo}|{$value['catalogo_incentivo_id']}|{$value['cantidad']}|{$asignado}|{$tipo}" name="agregar[]">
html;
		}
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
            <td style="text-align:center; vertical-align:middle;">{$fijo}</td>

            <td style="text-align:center; vertical-align:middle;"  class="td_row_checkbox_{$contador}" >
html;
          $faltas = $this->getStatusAsistenciasColaborador($idColaborador, $idPeriodo);
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

          if($value['fijo'] == "si"){
            $aplicaIncentivoSistema = $this->getStatusAsistenciasColaborador($idColaborador, $idPeriodo);
            $checked = ($aplicaIncentivoSistema > 0) ? "":"checked";
            $disabled = ($aplicaIncentivoSistema > 0) ? "":"disabled";

            if($aplicaIncentivoSistema == 0){
              $tablaAsignados.=<<<html
                <input type="hidden" value="{$value['catalogo_colaboradores_id']}|{$idPeriodo}|{$value['catalogo_incentivo_id']}|{$value['cantidad']}|{$asignado}|{$tipo}" name="agregar[]" >
                <input type="checkbox" class="switch" data-on-text="SI" data-off-text="NO" checked disabled value="{$value['catalogo_colaboradores_id']}|{$idPeriodo}|{$value['catalogo_incentivo_id']}|{$value['cantidad']}|{$asignado}|{$tipo}" name="agregar[]" >
html;
            }

            if($aplicaIncentivoSistema > 0){
              $tablaAsignados.=<<<html
                <input type="checkbox" class="switch" data-on-text="SI" data-off-text="NO" disabled value="{$value['catalogo_colaboradores_id']}|{$idPeriodo}|{$value['catalogo_incentivo_id']}|{$value['cantidad']}|{$asignado}|{$tipo}" name="agregar[]" >
html;
            }
          }else{
            $tablaAsignados .= <<<html
            <input type="checkbox" class="switch" data-on-text="SI" data-off-text="NO" checked value="{$value['catalogo_colaboradores_id']}|{$idPeriodo}|{$value['catalogo_incentivo_id']}|{$value['cantidad']}|{$asignado}|{$tipo}" name="agregar[]" >
html;
          }
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
            <td style="text-align:center; vertical-align:middle;">{$fijo}</td>
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
		$regreso = "/Incentivo/incentivos/{$colaboradorId}/{$periodoId}/{$regreso}";

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
    
}
