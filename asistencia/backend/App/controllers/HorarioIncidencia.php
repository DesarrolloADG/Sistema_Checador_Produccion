<?php
namespace App\controllers;
defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \App\controllers\Contenedor;
use \Core\Controller;
use \App\models\HorarioIncidencia as HorarioIncidenciaDao;

class HorarioIncidencia extends Controller{ 
	private $_contenedor;
	function __construct(){
		parent::__construct();
		$this->_contenedor = new Contenedor;
		View::set('header',$this->_contenedor->header());
		View::set('footer',$this->_contenedor->footer());
	}

	public function index() {
		$extraHeader=<<<html
        <style>
			.foto{ width:100px; height:100px; border-radius: 50px;}
        </style>
html;
		$extraFooter =<<<html
			<script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
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
		              } );

		              $("#btnReiniciar").click(function(){
		                $.ajax({
		                  url: "/AdminJustificacion/getTabla",
		                  type: "POST",
		                  data: "",
		                  success: function(data){
		                    $("#registros").html(data);
		                  }
		                });
		              });

		              $("select").change(function(){
		                $.ajax({
		                  url: "/AdminJustificacion/getTabla",
		                  type: "POST",
		                  data: $("#all").serialize(),
		                  success: function(data){
		                    $("#registros").html(data);
		                  }
		                });
		              });

	          	});
        	</script>
html;
      
		$datosUsuario = HorarioIncidenciaDao::getDatosUsuarioLogeado($this->__usuario);
		$secciones = HorarioIncidenciaDao::getDepartamentos($datosUsuario['administrador_id']); 
		$tabla = '';
		foreach ($secciones as $key => $value) {
			foreach (HorarioIncidenciaDao::getAllColaboradores($value['catalogo_departamento_id']) as $key => $value) {
				$tabla .=<<<html
					<tr>
						<td {$editarHidden}><input type="checkbox" name="borrar[]" value="{$value['catalogo_colaboradores_id']}"/></td>
						<td><img class="foto" src="/img/colaboradores/{$value['foto']}"/></td>
						<td>{$value['numero_empleado']}</td>
						<td>{$value['nombre']} <br>{$value['apellido_paterno']}<br> {$value['apellido_materno']}</td>
						<td>{$value['nombre_empresa']}</td>
						<td>{$value['nombre_departamento']}</td>
						<td>{$value['status']}</td>
						<td class="center">
							<a href="/HorarioIncidencia/getChecadorDias/{$value['catalogo_colaboradores_id']}" type="submit" name="id_empresa" class="btn btn-primary"><i class="fa fa-plus"></i> </a>
						</td>
					</tr>
html;
			}
		}

		View::set('tabla',$tabla);
		View::set('header',$this->_contenedor->header($extraHeader));
		View::set('footer',$this->_contenedor->footer($extraFooter));
		View::render("horario_incidencia");
    }

    public function getChecadorDias($id){
    	/*$extraFooter =<<<html
        <script src="/js/moment/moment.min.js"></script>
        <script src="/js/datepicker/scriptdatepicker.js"></script>
        <script src="/js/datepicker/datepicker2.js"></script>
html;
		$dias = array('Monday'=>'Lunes','Tuesday'=>'Martes','Wednesday'=>'Miercoles','Thursday'=>'Jueves','Friday'=>'Viernes','Saturday'=>'Sabado','Sunday'=>'Domingo');
    	$datosColaborador = HorarioIncidenciaDao::getDatosColaborador($id);
		$tabla = "";
		foreach (HorarioIncidenciaDao::getDatosChecador($datosColaborador['numero_empleado']) as $key => $value) {
			$date = date('Y-m-d', strtotime($value['date_check']));
			$tabla .= <<<html
				<tr>
					<td><input type="checkbox" name="borrar[]" value="{$value['catalogo_colaboradores_id']}"/></td>
					<td>{$dias[date('l', strtotime($date))]}</td>
					<td>{$value['date_check']}</td>
					<td>{$value['hora_entrada']}</td>
					<td>{$value['hora_salida']}</td>
					<td>{$value['tolerancia_entrada']}</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
html;
		}

    	View::set('datosColaborador', $datosColaborador);
    	View::set('tabla',$tabla);
    	View::set('header',$this->_contenedor->header());
		View::set('footer',$this->_contenedor->footer($extraFooter));
		View::render("horario_incidencia_checador");*/

		$extraHeader=<<<html
        <style>
			.foto{ width:100px; height:100px; border-radius: 50px;}
        </style>
html;
		$extraFooter =<<<html
			<script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
			<script src="/js/moment/moment.min.js"></script>
			<script src="/js/datepicker/scriptdatepicker.js"></script>
			<script src="/js/datepicker/datepicker2.js"></script>
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
		              } );

		              $("#btnReiniciar").click(function(){
		                $.ajax({
		                  url: "/AdminJustificacion/getTabla",
		                  type: "POST",
		                  data: "",
		                  success: function(data){
		                    $("#registros").html(data);
		                  }
		                });
		              });

		              $("select").change(function(){
		                $.ajax({
		                  url: "/AdminJustificacion/getTabla",
		                  type: "POST",
		                  data: $("#all").serialize(),
		                  success: function(data){
		                    $("#registros").html(data);
		                  }
		                });
		              });

	          	});
			</script>
html;
		/*$dias = array('Monday'=>'Lunes','Tuesday'=>'Martes','Wednesday'=>'Miercoles','Thursday'=>'Jueves','Friday'=>'Viernes','Saturday'=>'Sabado','Sunday'=>'Domingo');
		$datosColaborador = HorarioIncidenciaDao::getDatosColaborador($id);
		print_r($datosColaborador);
		$tabla = "";
		foreach (HorarioIncidenciaDao::getDatosChecador($datosColaborador['numero_empleado']) as $key => $value) {
			$date = date('Y-m-d', strtotime($value['date_check']));
			$tabla .=<<<html
				<tr>
					<td><input type="checkbox" name="borrar[]" value="{$value['catalogo_colaboradores_id']}"/></td>
					<td>{$value['date_check']}</td>
					<td>{$dias[date('l', strtotime($date))]}</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
html;
		}*/
		$dias = array('Monday'=>'Lunes','Tuesday'=>'Martes','Wednesday'=>'Miercoles','Thursday'=>'Jueves','Friday'=>'Viernes','Saturday'=>'Sabado','Sunday'=>'Domingo');
		$busqueda = new \stdClass();
		$fecha_ini = (empty(MasterDom::getData('fecha_inicial'))) ? date("Y-m-d")." 00:00:00" : MasterDom::getData('fecha_inicial') . " 00:00:00";
		$fecha_fin = (empty(MasterDom::getData('fecha_final'))) ? date("Y-m-d")." 23:59:59" : MasterDom::getData('fecha_final') . " 23:59:59";
		$datosColaborador = HorarioIncidenciaDao::getDatosColaborador($id);
		$numero_empleado = $datosColaborador['numero_empleado'];

		$fechaIni = (!empty(MasterDom::getData('fecha_inicial'))) ? MasterDom::getData('fecha_inicial') : date('Y-m-d');
		$fechaFin = (!empty(MasterDom::getData('fecha_final'))) ? MasterDom::getData('fecha_final') : date('Y-m-d');
		View::set('fechaIni', $fechaIni);
		View::set('fechaFin', $fechaFin);
		View::set('id',$id);
		View::set('consultaTabla',$consultaTabla);
		View::set('datosColaborador', $datosColaborador);
		View::set('tabla',$tabla);
		View::set('header',$this->_contenedor->header($extraHeader));
		View::set('footer',$this->_contenedor->footer($extraFooter));
		View::render("horario_incidencia_checador");
    }

    public function busqueda(){
    	$extraFooter =<<<html
			<script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
			<script src="/js/moment/moment.min.js"></script>
			<script src="/js/datepicker/scriptdatepicker.js"></script>
			<script src="/js/datepicker/datepicker2.js"></script>
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
		              } );

		              $("#btnReiniciar").click(function(){
		                $.ajax({
		                  url: "/AdminJustificacion/getTabla",
		                  type: "POST",
		                  data: "",
		                  success: function(data){
		                    $("#registros").html(data);
		                  }
		                });
		              });

		              $("select").change(function(){
		                $.ajax({
		                  url: "/AdminJustificacion/getTabla",
		                  type: "POST",
		                  data: $("#all").serialize(),
		                  success: function(data){
		                    $("#registros").html(data);
		                  }
		                });
		              });

	          	});
			</script>
html;

    	$dias = array('Monday'=>'Lunes','Tuesday'=>'Martes','Wednesday'=>'Miercoles','Thursday'=>'Jueves','Friday'=>'Viernes','Saturday'=>'Sabado','Sunday'=>'Domingo');
		$busqueda = new \stdClass();
		$fecha_ini = (empty(MasterDom::getData('fecha_inicial'))) ? date("Y-m-d")." 09:20:00" : MasterDom::getData('fecha_inicial') . " 00:00:00";
		$fecha_fin = (empty(MasterDom::getData('fecha_final'))) ? date("Y-m-d")." 23:59:59" : MasterDom::getData('fecha_final') . " 23:59:59";
		$datosColaborador = HorarioIncidenciaDao::getDatosColaborador(MasterDom::getData('id_colaborador'));

		$tabla = "";

		$tabla .=<<<html
			<table class="table table-striped table-bordered table-hover" id="muestra-cupones">
				<thead>
					<tr>
						<th style="vertical-align:middle;"><input type="checkbox" name="checkAll" id="checkAll" value=""/></th>
						<th style="vertical-align:middle;">Checador</th>
						<th style="vertical-align:middle;">Dia</th>
						<th style="vertical-align:middle;">Hora <br>Entrada</th>
						<th style="vertical-align:middle;">Hora <br>Salida</th>
						<th style="vertical-align:middle;">Tolerancia</th>
						<th style="vertical-align:middle;">Entrada</th>
						<th style="vertical-align:middle;">Salida</th>
						<th style="vertical-align:middle;">Comentario</th>
						<th style="vertical-align:middle;">Incidencia</th>
					</tr>
				</thead>
			<tbody id="registros">
html;
		foreach (HorarioIncidenciaDao::getDatosChecador($fecha_ini, $fecha_fin, $datosColaborador['numero_empleado']) as $key => $value) {
			$date = date('Y-m-d', strtotime($value['date_check']));
			$tabla .=<<<html
				<tr>
					<th><input type="checkbox" name="checkAll" id="checkAll" value=""/></th>
					<th> {$value['date_check']} </th>
					<th> {$dias[date('l', strtotime($date))]} </th>
					<th> {$value['hora_entrada']} </th>
					<th> {$value['hora_salida']} </th>
					<th> {$value['tolerancia_entrada']} </th>
					<th>  </th>
					<th>  </th>
					<th>  </th>
					<th>  </th>
				</tr>
html;
		}
		$tabla .=<<<html
			</tbody>
		</table>
html;


		$fechaIni = (!empty(MasterDom::getData('fecha_inicial'))) ? MasterDom::getData('fecha_inicial') : date('Y-m-d');
		$fechaFin = (!empty(MasterDom::getData('fecha_final'))) ? MasterDom::getData('fecha_final') : date('Y-m-d');
		View::set('fechaIni', $fechaIni);
		View::set('fechaFin', $fechaFin);
		View::set('id',MasterDom::getData('id_colaborador'));
		View::set('datosColaborador',$datosColaborador);
		View::set('tabla',$tabla);
		View::set('header',$this->_contenedor->header($extraHeader));
		View::set('footer',$this->_contenedor->footer($extraFooter));
		View::render("horario_incidencia_checador");

    }

    
}
