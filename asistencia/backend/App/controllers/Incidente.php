<?php
namespace App\controllers;
defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \Core\Controller;
use \App\models\Incidente as IncidenteDao;

class Incidente extends Controller{

	function __construct(){
		parent::__construct();
		$this->_contenedor = new Contenedor;
		View::set('header',$this->_contenedor->header());
		View::set('footer',$this->_contenedor->footer());
	}

	public function colaboradores($tipo){
		
		$data = new \stdClass();
		$data->_pago = $tipo;

		$tabla= '';
		foreach (IncidenteDao::getAllColaboradores($data) as $key => $value) {
			echo "<pre>"; print_r($value); echo "</pre>";
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
					<td style="text-align:center; vertical-align:middle;" $hidden>
						<a href="/Incidencia/checadorFechas/{$value['catalogo_colaboradores_id']}/{$idPeriodo}/{$vista}/{$accion}" type="submit" name="id_empresa" class="btn btn-primary"><i class="fa fa-plus"></i> </a>
					</td>
				</tr>
html;
    	}

		View::set('tabla',$tabla);
		View::render("incidencia_abierta");
	}

	public function getRangoPeriodo($idColaborador, $idPeriodo){
		$dataColaborador = IncidenteDao::getById($idColaborador);
		$periodo = IncidenteDao::getPeriodoById($idPeriodo);

		$fechaIni = new \DateTime($periodo['fecha_inicio']);
		$fechaFin = new \DateTime($periodo['fecha_fin']);

		$dias = array('Monday'=>'Lunes','Tuesday'=>'Martes','Wednesday'=>'Miercoles','Thursday'=>'Jueves','Friday'=>'Viernes','Saturday'=>'Sabado','Sunday'=>'Domingo');
		//$this->siElDiaEsLaboral($dataColaborador['catalogo_horario_id'], $dias[$i->format('l')]);

		foreach (IncidenteDao::getHorarioId($dataColaborador['catalogo_horario_id']) as $key => $value) {
			echo "<pre>";
			print_r($value);
			echo "</pre>";
		}


		for($i = $fechaIni; $i <= $fechaFin; $i->modify('+1 day')){
			echo $i->format("Y-m-d") . " " . $dias[$i->format('l')] .  $esTrabajo . "<br>";
		}
	}

	public function siElDiaEsLaboral($catalogoHorarioId, $dia){

		

	}

}