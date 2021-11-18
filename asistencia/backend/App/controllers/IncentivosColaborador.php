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

class IncentivosColaborador extends Controller{

	function __construct(){	
		parent::__construct();
		$this->_contenedor = new Contenedor;
		View::set('header',$this->_contenedor->header());
		View::set('footer',$this->_contenedor->footer());
	}

	public function muestra($idColaborador, $idPeriodo, $seccion){

		View::set('addTableIncentivos',$this->addTableIncentivos($idColaborador, $idPeriodo));
		View::set('header',$this->_contenedor->header());
		View::set('footer',$this->_contenedor->footer($extraFooter));
		View::render('incentivos_colaborador_muestra');    	
	}

	public function addTableIncentivos($idColaborador, $idPeriodo){
		$html = "";
		foreach (IncentivoDao::getIncentivoColaborador($idColaborador) as $key => $value) {
			$key = $key + 1;
			$dato = $this->getCantidad($value['fijo']);
			$duplicador = $this->getRepetir($value['repetitivo'], $key);
			$html .=<<<html
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

}
