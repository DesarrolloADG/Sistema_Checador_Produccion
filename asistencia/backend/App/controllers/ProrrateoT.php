<?php
namespace App\controllers;
defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \Core\Controller;
use \App\models\Prorrateo as ProrrateoDao;
use \App\models\General AS GeneralDao;

class ProrrateoT extends Controller{

	function __construct(){
		parent::__construct();
		$this->_contenedor = new Contenedor;
		View::set('header',$this->_contenedor->header());
		View::set('footer',$this->_contenedor->footer());
	}


	public function SalarioMinino(){


		$sm = GeneralDao::getSalarioMinimo();

		if(empty($sm)){
			View::set('accion','Agregar');
			View::set('mensaje',"No cuentas con un salario minimo, agrega para no tener problemas con el prorrateo");
		}else{
			View::set('accion','Agregar nuevo valor');
			View::set('mensaje',"El salario minimo es <b>{$sm['cantidad']}</b>");
		}

		View::render("salario");
	}

	public function SalarioMinimoAdd(){
		$cantidad  = MasterDom::getData('salario');
		$id = GeneralDao::insertSalarioMinimo($cantidad);
		if($id>0){
			$mensaje .= <<<html
            <div class="alert alert-success" role="alert">
              <p>Se ha agregado la cantidad de $ {$cantidad}, como el valor de salario minimo.</p>
            </div>
html;
			View::set('regreso', "/ProrrateoT/SalarioMinino/");
			View::set('mensaje', $mensaje);
			View::set('titulo', "insertado");
			View::set('class', "success");
			View::render("alertas");
		}else{
			$mensaje .= <<<html
            <div class="alert alert-danger" role="alert">
              <p>Ha ocurrido un error</p>
            </div>
html;
			View::set('regreso', "/ProrrateoT/SalarioMinino/");
			View::set('mensaje', $mensaje);
			View::set('titulo', "Error");
			View::set('class', "danger");
			View::render("alertas");
		}
	}

	public function calculo($tipo, $identificador){
		$user = GeneralDao::getDatosUsuario($this->__usuario);

		$getIdPeriodo = ProrrateoDao::getLastPeriodoProcesado($identificador);
		if(!empty($getIdPeriodo)){
			$count = ProrrateoDao::getRegistro($getIdPeriodo['prorrateo_periodo_id'], $identificador);
		}else{
			$count = 0;
		}

		$existeFechaDiaFestivo = $this->getRango($getIdPeriodo['fecha_inicio'], $getIdPeriodo['fecha_fin']);

		if($count['contador'] > 0){
			$display = "display: none;";
			View::set('visualizar', $display);
			View::set('display',$display);
			$msj = <<<html
<div class="alert alert-success alert-dismissible" role="alert">
	{$identificador} ya esta guardado en NOI
</div>
html;
			View::set('mensaje',$msj);
		}

		// VALIDARA SI HAY DIA FESTIVO

		if(empty($existeFechaDiaFestivo)){
			View::set('displayDiaFestivo', "display:none;");
		}
		$extraFooter=$this->getFooter();
		$extraFooter .=<<<html
			<script>
				$(document).ready(function(){
					$(document).on('click','#btn-action-accept',function(e) {
						$('#btn-action-accept').hide();
						$('#contenido-mensaje').html("<div class='alert alert-success' role='alert'><h2>Procesando</h2><p>Se esta procesando la petición, no cierre o actualice el navegador. </p></div>");
					});
				});
			</script>
html;


		View::set('txtAccion',"Exportar informaci&oacute;n a NOI de {$identificador}");
		View::set('identificador', $identificador);
		View::set('msjPeriodo',$this->getPeriodo(strtoupper($tipo), $getIdPeriodo['status'], $getIdPeriodo['identificador_noi'])); // Obtiene el periodo de la incidencia
		$idPeriodo = $this->getIdPeriodo(strtoupper($tipo), $getIdPeriodo['status']); // Obtiene el ultimo periodo Abierto
		View::set('prorrateo_periodo_id', $idPeriodo);
		View::set('tabla',$this->getColaboradores($tipo , $identificador, $idPeriodo, $existeFechaDiaFestivo)); // Coloca la informacion de la tabla de todos los colaboradores
		View::set('tipoPeriodo',$tipo); // Identificacion del periodo
		View::set('header',$this->_contenedor->header($this->getHeader()));
		View::set('footer',$this->_contenedor->footer($extraFooter));
		View::render('prorrateo_abierto');
	}

	public function getRango($fechaInicio, $fechaFinal){
		$rango =  $this->fechas($fechaInicio, $fechaFinal);

		$arr = array();
		foreach ($rango as $key => $value) {
			$diaFestivo = ProrrateoDao::getSiEsDiaFestivo($value);
			if(!empty(($diaFestivo))){
				array_push($arr, $diaFestivo);
			}
		}
		return $arr['0'];
	}

	public function fechas($start, $end) {
		$range = array();

		if (is_string($start) === true) $start = strtotime($start);
		if (is_string($end) === true ) $end = strtotime($end);

		if ($start > $end) return createDateRangeArray($end, $start);

		do {
			$range[] = date('Y-m-d', $start);
			$start = strtotime("+ 1 day", $start);
		} while($start <= $end);

		return $range;
	}

	public function calculoHistorico($tipo, $identificador){
		$user = GeneralDao::getDatosUsuario($this->__usuario);

		if(empty($_POST)){
			$ultimoPeriodoHistorico = ProrrateoDao::getUltimoPeriodoHistorico(strtoupper($tipo));
			$idPeriodo = $ultimoPeriodoHistorico['prorrateo_periodo_id'];
			$msjPeriodofechas = $this->getPeriodoT(strtoupper($tipo), 1);
			View::set('btnbackground','info');
			View::set('btnText','Buscar');
			//View::set('tabla',$this->getTabla('Semanal', $idPeriodo));
		}else{
			$idPeriodo = MasterDom::getData('tipo_periodo');
			$msjPeriodofechas = $this->getPeriodoHistoricosTipo($idPeriodo);
			View::set('btnbackground','success');
			View::set('btnText','Volver a Buscar');
		}

		$existeFechaDiaFestivo = $this->getRango($getIdPeriodo['fecha_inicio'], $getIdPeriodo['fecha_fin']);
		// VALIDARA SI HAY DIA FESTIVO

		if(empty($existeFechaDiaFestivo)){
			View::set('displayDiaFestivo', "display:none;");
		}

		View::set('prorrateo_periodo_id', $idPeriodo);
		View::set('identificador', $identificador);
		View::set('display','display:none;');
		View::set('tabla',$this->getColaboradores($tipo , $identificador, $idPeriodo, $existeFechaDiaFestivo)); // Coloca la informacion de la tabla de todos los colaboradores
		$strtoupper = strtoupper($tipo);
		View::set('tipoPeriodo',"<h1>PRORRATEO HISTORICO {$strtoupper}</h1>");
		View::set('msjPeriodo',$msjPeriodofechas);
		View::set('titulo','Quincenales');
		View::set('option',$this->getPeriodosHistoricos(strtoupper($tipo))); // Todos los periodos
		View::set('header',$this->_contenedor->header($this->getHeader()));
		View::set('footer',$this->_contenedor->footer($this->getFooter()));
		View::render("prorrateo_historico");
	}

	public function getPremiodePuntualidadColaborador($SDI, $INCENTIVOS, $TOTALINCENTIVOSHORAS, $PREMIOASISTENCIA){
		/*$SDI = '115.88'; //E12
		$INCENTIVOS = '0.00'; // G12
		$TOTALINCENTIVOSHORAS = '128.69'; // J12
		$PREMIOASISTENCIA = '81.12'; //L12 */

		//echo ":::::::::function getPremiodePuntualidadColaborador($SDI, $INCENTIVOS, $TOTALINCENTIVOSHORAS, $PREMIOASISTENCIA):::::::\n";
		// CONDICION 1
		$resultado = 0;
		if( ($TOTALINCENTIVOSHORAS-$PREMIOASISTENCIA) > (($SDI*7)*0.1) ){
			$resultado = (($SDI*7)*0.1);
			$bandera1 = true;
			$resultado1 = (($SDI*7)*0.1);
		}

		// CONDICION 2
		if ( ($INCENTIVOS-$PREMIOASISTENCIA) < (($SDI*7)*0.1) ){
			$resultado =($INCENTIVOS-$PREMIOASISTENCIA);
			//echo ":::$resultado::::\n";
			/*
			if($INCENTIVOS <= 0)
				$resultado =$PREMIOASISTENCIA;
			else
				$resultado =($INCENTIVOS-$PREMIOASISTENCIA);
			*/
			if($resultado < 0){
				$resultado = 0;
			}

			$bandera2 = true;
		}

		if($bandera1 && $bandera2){
			$resultado = $resultado1;
		}

		$operacionNueva = round($resultado, 2);
		$final = number_format($operacionNueva, 2, '.', '');

		if($final < 0)
			$final = 0;

		//echo "::::FUNCTION getPremiodePuntualidadColaborador() $final:::::";

		return /*"SDI ->" . $SDI . "<br>INCENTIVOS ->" . $INCENTIVOS . "<br>TOTALINCENTIVOSHORAS ->" . $TOTALINCENTIVOSHORAS . "<br>PREMIOASISTENCIA ->" . $PREMIOASISTENCIA . "<br>" .*/$final;
	}

	public function getPremioAsistenciaColaborador($SDI, $INCENTIVOS, $TOTALINCENTIVOSHORAS){
		$ope = (($SDI*7)*0.1);

		$resultado = 0;
		//echo "{$TOTALINCENTIVOSHORAS} > {$ope}: {$ope}<br>";
		//echo "{$INCENTIVOS} < {$ope}: {$ope}, {$INCENTIVOS} > 0, {$INCENTIVOS}, 0<br>";

		/*
		**
		**:::JUAN CAMBIO
		**
		/*
		if($TOTALINCENTIVOSHORAS > $ope){
			$final = $ope;
		}else{
			if($INCENTIVOS < $ope){
				$final = $INCENTIVOS;
			}elseif($INCENTIVOS > 0){
				$final = 0;
			}
		}
		*/
		if($TOTALINCENTIVOSHORAS > 0 AND $TOTALINCENTIVOSHORAS < $ope){
			$final = $TOTALINCENTIVOSHORAS;
		}else if($INCENTIVO > 0 AND $INCENTIVO < $ope){
			$final = $INCENTIVO;
		}else if($TOTALINCENTIVOSHORAS > $ope){
			$final = $ope;
		}else if($INCENTIVO > $ope){
			$final = $ope;
		}

		/*
		if($TOTALINCENTIVOSHORAS > $ope){
			$final = $ope;
		}else{
			if($INCENTIVOS < $ope){
				$final = $INCENTIVOS;
			}elseif($INCENTIVOS > 0){
				$final = $ope;
			}
		}
		*/

		//echo "::::function getPremioAsistenciaColaborador $final::::\n";

		$resultado = number_format($final, 2, '.', '');
		return $resultado;

	}
	public function hExtraPrevio($sumaIncentivos, $limiteHoExtra, $setPremioAsistenciaColaborador, $setPremiodePuntualidadColaborador){
		$op = 0;
		if( ($sumaIncentivos - $setPremioAsistenciaColaborador - $setPremiodePuntualidadColaborador) >0){
			$op = ($sumaIncentivos - $setPremioAsistenciaColaborador - $setPremiodePuntualidadColaborador);
		}elseif( ($sumaIncentivos - $setPremioAsistenciaColaborador - $setPremiodePuntualidadColaborador ) > $limiteHoExtra){
			$op = ($sumaIncentivos - $setPremioAsistenciaColaborador - $setPremiodePuntualidadColaborador);
		}else{
			$op = 0;
		}
		//echo "<br>{$limiteHoExtra} = {$sumaIncentivos} - {$setPremioAsistenciaColaborador} - {$setPremiodePuntualidadColaborador}";
		return $op;
	}

	// CONTROL DE LA TABLA PARA TODOS LOS PERIODOS
	public function getColaboradores($tipo, $identificador, $idPeriodo, $existeFechaDiaFestivo){
		$html = "";
		$fila = 0;
		$count = count(ProrrateoDao::getColaboradoresProrrateo($tipo,$identificador));
		foreach (ProrrateoDao::getColaboradoresProrrateo($tipo,$identificador) as $key => $value) {
		/*
		//JUAN
		if($value['catalogo_colaboradores_id'] != 369)
			continue;
		//JUAN
		*/
/******************************QUITAR PARA EL FUNCIONAMIENTO DE LA FUNCION*************************************
if($value['catalogo_colaboradores_id'] != 43 ){
        continue;
}

/******************************QUITAR PARA EL FUNCIONAMIENTO DE LA FUNCION**************************************/
			$primaDominical = $this->getPrimaDominical($value['catalogo_colaboradores_id'], $idPeriodo);
			$domingoLaborado = $this->getDomingoLaborados($value['catalogo_colaboradores_id'], $idPeriodo);
			$nuervosValores = ($primaDominical == 0) ? $domingoLaborado : $primaDominical;

			$horasExtra = $this->getHorasExtra($value['catalogo_colaboradores_id'], $idPeriodo);
			//echo ":::horasExtra:::$horasExtra:::\n";
			$sumaIncentivos = $this->getIncentivos($value['catalogo_colaboradores_id'], $idPeriodo);
			//echo ":::sumaIncentivos:::$sumaIncentivos:::\n";


			$cantidadPesosHorasExtra = $this->getCalculoHorasExtra($value['sal_diario'], $horasExtra);
			//echo ":::cantidadPesosHorasExtra:::$cantidadPesosHorasExtra:::\n";
			$totalHorasExtraIncentivos = $this->getSumaHorasExtra_IncentivosPeriodos($cantidadPesosHorasExtra, $sumaIncentivos, $nuervosValores); // -
			//echo ":::totalHorasExtraIncentivos:::$totalHorasExtraIncentivos:::\n";
			$TOTAL = $this->getSumaHorasExtra_IncentivosPeriodos($cantidadPesosHorasExtra, $sumaIncentivos); // 9
			//echo ":::TOTAL:::$TOTAL:::\n";
			$LimiteHorasExtra = $this->getLimiteHorasExtra($value['sal_diario']);
			//echo ":::LimiteHorasExtra:::$LimiteHorasExtra:::\n";
			$premioAsistencia = $this->getPremioAsistencia($sumaIncentivos, $totalHorasExtraIncentivos, $value['sdi']);
			//echo ":::premioAsistencia:::$premioAsistencia:::\n";

			$incentivoNoche = $this->getIncentivoNoche($value['catalogo_colaboradores_id'], $idPeriodo);

			//$festivo = $this->getFestivo($value['sal_diario']);
			$fila ++;

			$value['nombre'] = utf8_encode($value['nombre']);
			$value['apellido_paterno'] = utf8_encode($value['apellido_paterno']);
			$value['apellido_materno'] = utf8_encode($value['apellido_materno']);

			$setPremioAsistenciaColaborador = $this->getPremioAsistenciaColaborador($value['sdi'], $sumaIncentivos, $totalHorasExtraIncentivos);
			//echo ":::setPremioAsistenciaColaborador:::$setPremioAsistenciaColaborador:::\n";
			$setPremiodePuntualidadColaborador = $this->getPremiodePuntualidadColaborador($value['sdi'], $sumaIncentivos, $TOTAL, $setPremioAsistenciaColaborador);
			//echo ":::setPremiodePuntualidadColaborador:::$setPremiodePuntualidadColaborador:::\n";
			$horasExtraPrevio = $this->horasExtraPrevio($TOTAL, $LimiteHorasExtra, $setPremioAsistenciaColaborador, $setPremiodePuntualidadColaborador);
			//echo ":::horasExtraPrevio:::$horasExtraPrevio:::\n";

			$importeHorasExtra = $this->getImporteHorasExtras($value['sal_diario'], $totalHorasExtraIncentivos, $LimiteHorasExtra, $setPremioAsistenciaColaborador, $setPremiodePuntualidadColaborador, $horasExtraPrevio);
			//echo ":::importeHorasExtra:::$importeHorasExtra:::\n";
			$numeroHorasExtra = $this->getNumeroHorasExtra($value['sal_diario'], $importeHorasExtra);
			//echo ":::numeroHorasExtra::$numeroHorasExtra:::\n";
			$despensaEnEfectivo =  $this->getDespensaEnEfectivo($TOTAL, $setPremioAsistenciaColaborador, $setPremiodePuntualidadColaborador, $importeHorasExtra);

			$incentivo =  $this->getIncentivo($TOTAL, $setPremioAsistenciaColaborador, $setPremiodePuntualidadColaborador, $importeHorasExtra, $despensaEnEfectivo);
			$totalProrrateo =  $this->getTotalProrrateo($setPremioAsistenciaColaborador, $setPremiodePuntualidadColaborador, $importeHorasExtra, $despensaEnEfectivo, $incentivo);

			$premioPuntualidad = $this->getPremioPuntualidad($sumaIncentivos, $totalHorasExtraIncentivos, $value['sdi'], $setPremioAsistenciaColaborador);


			// VALIDA SI HAY FECHA EN DIA FESTIVO
			$displayDiaFestivo = "";

			if(empty($existeFechaDiaFestivo)){
				$displayDiaFestivo = 'display:none;';
			}else{
				// CHECAR LA FECHA
				//$asistenciaFestiva = ProrrateoDao::verificarSiLaASistenciasFestivaExiste($value['clave'], $existeFechaDiaFestivo['fecha'], $value['identificador']);
				$asistenciaFestiva = ProrrateoDao::verificaAsistenciaFestiva($idPeriodo, $value['catalogo_colaboradores_id'], $existeFechaDiaFestivo['fecha']);

				if($asistenciaFestiva['asistencia']>0){
                    $festivo = 0;
					$festivo = $this->getFestivo($value['sal_diario']);
				}
                else
                {
                    $festivo = 0;
                }

				if($key == 0){
					//echo "<pre>"; print_r($idPeriodo); echo "</pre>";
				}
			}

			/*
			* DOMIGO DE TRABAJO
			* Formula de excel =SI(AL15>0,(SALARIODIARIO*0.25+(SALARIODIARIO*2)),(0)) => si hay asignacion de dia de trabajo, poner la siguiente operacion
			*/
			if ($totalProrrateo == $TOTAL) {
				$color = 'green';
				$textov = 'Correcto';
			}
			else {
				$color = 'red';
				$textov = 'Incorrecto';
			}

			$dmgTrabajo = 0;
			if(!empty($primaDominical)) {
				$dmgTrabajo = $primaDominical;
			}elseif(!empty($domingoLaborado)){
				$dmgTrabajo = $domingoLaborado;
			}
			//MRR
			$totalP = $dmgTrabajo + $TOTAL;
			//? $this->getDomingoTrabajo($value['sal_diario']) : 0;

			$limHrsExtra = $this->getLimHrsExtra($value['sal_diario'], $cantidadPesosHorasExtra, $totalHorasExtraIncentivos);

			$hExtraPrevio = $this->hExtraPrevio($sumaIncentivos, $cantidadPesosHorasExtra, $setPremioAsistenciaColaborador, $setPremiodePuntualidadColaborador);

			$htmlGuarda .= <<<html
					<input type="hidden" name="count" value="{$count}">
						<input type="hidden" name="datos_colaborador_{$fila}" value="{$value['clave']}|{$value['nombre']} {$value['apellido_paterno']} {$value['apellido_materno']}|{$setPremioAsistenciaColaborador}|{$setPremiodePuntualidadColaborador}|{$numeroHorasExtra}|{$despensaEnEfectivo}|{$incentivo}|{$dmgTrabajo}|{$domingoLaborado}|{$totalHorasExtraIncentivos}|{$value['catalogo_colaboradores_id']}|{$idPeriodo}|{$identificador}|{$totalHorasExtraIncentivos}|{$dmgTrabajo}|{$festivo}|">
html;
			$html .= <<<html
				<tr>

					<!-- 1 --><td style="text-align:center; vertical-align:middle;">{$value['clave']}</td>
					<!-- 2 --><td style="text-align:center; vertical-align:middle;">{$value['apellido_paterno']} {$value['apellido_materno']} {$value['nombre']}</td>
					<!-- 3 --><td style="text-align:center; vertical-align:middle;">$ {$value['sal_diario']}</td>
					<!-- 4 --><td style="text-align:center; vertical-align:middle;">$ {$value['sdi']}</td>
					<!-- 5 --><td style="text-align:center; vertical-align:middle;">  {$horasExtra}</td>
					<!-- 6 --><td style="text-align:center; vertical-align:middle;">$ {$cantidadPesosHorasExtra}</td>
					<!-- 7 --><td style="text-align:center; vertical-align:middle;">$ {$sumaIncentivos}</td>
					<!-- 8 --><td style="text-align:center; vertical-align:middle;">$ {$TOTAL}</td>
					<!-- 9 <td style="text-align:center; vertical-align:middle;"></td>-->
					<!-- 10 --><td style="text-align:center; vertical-align:middle;">$ {$limHrsExtra}</td>
					<!-- 11 --><td style="background: #fbf6b6; text-align:center; vertical-align:middle;">$ {$setPremioAsistenciaColaborador}</td>
					<!-- 12 --><td style="background: #fbf6b6; text-align:center; vertical-align:middle;">$ {$setPremiodePuntualidadColaborador}</td>
					<!-- 13 --><td style="background: #fbf6b6; text-align:center; vertical-align:middle;">{$numeroHorasExtra}</td>
					<!-- 14 --><td style="text-align:center; vertical-align:middle;">{$importeHorasExtra}</td>
					<!-- 15 --><td style="text-align:center; vertical-align:middle;">{$horasExtraPrevio}</td>
					<!-- 16 --><td style="background: #fbf6b6; text-align:center; vertical-align:middle;">$ {$despensaEnEfectivo}</td>
					<!-- 17 --><td style="background: #fbf6b6; text-align:center; vertical-align:middle;">$ {$incentivo}</td>
					<!-- 18 --><td style="text-align:center; vertical-align:middle;">$ {$totalProrrateo}</td>
					<!-- 19 --><td style="background: {$color}; color: white; text-align:center; vertical-align:middle;">{$textov}</td>
					<!-- 20 --><td style="text-align:center; vertical-align:middle;"> $ 0</td>
					<!-- 21 --><td style="background: #fbf6b6; text-align:center; vertical-align:middle;">$ {$dmgTrabajo} </td>
					<!-- 22 --><td style="background: #fbf6b6; text-align:center; vertical-align:middle; {$displayDiaFestivo}"> {$festivo}</td>
					<!-- 23 --><td style="text-align:center; vertical-align:middle;">$
					{$totalP/*$totalHorasExtraIncentivos*/}
						<!--
							Clave noi
							Nombre
							Premio Asistencia
							Premio Puntualidad
							Numero Horas Extra
							Despensa en efectivo
							Incentivo
						-->
					</td>
					<!--td style="">{$incentivoNoche}</td-->
				</tr>
html;
		}

		return array("htmlGuarda"=>$htmlGuarda,"html"=>$html);
	}

	public function getDomingoTrabajo($sdi){
		$val = round(($sdi*0.25+($sdi*2)), 2);
		$operacion = number_format($val, 2, '.', '');
		return $operacion;
	}

	public function getLimHrsExtra($salarioDiario, $cantidadPesosHorasExtra, $totalHorasExtraIncentivos){
		$o1 = $salarioDiario * 2;
		$o2 = $o1/8;
		$o3 = $o2*9;

		$resultado = 0;

		if($cantidadPesosHorasExtra == 0 && $salarioDiario > 0 && $cantidadPesosHorasExtra < $o3 ){
			$resultado = $o3;
		}else{
			$resultado = $o3;
		}


		$num = number_format($resultado, 2, '.', '');

		return $num;
	}

	public function saveProrrateo(){

		//exit;
		// CREACION DEL ARRAY
		$stdClassIncentivos = new \stdClass();
		$stdClassIncentivos->nomina = MasterDom::getData('identificador');
		for ($i = 0; $i <= MasterDom::getData('count'); $i++) {
			$arr = explode("|", MasterDom::getData("datos_colaborador_" . $i));
			$datos = ProrrateoDao::getIdColaborador($arr['0'], MasterDom::getData('identificador'));



			if(!empty($datos)){
				$stdClassIncentivos->incentivos[$i] =
					array(
						"clave"=>$arr['0'],
						"datos"=> array(
							"asistencia"=>$arr['2'],
							"puntualidad"=>$arr['3'],
							"horas"=>$arr['4'],
							"despensa"=>$arr['5'],
							"incentivo"=>$arr['6'],
							"prima"=>0,
							"domingo"=>$arr['7'],
                            "festivo"=>$arr['15']
					)
				);
				$datos = new \stdClass();

				$datos->_catalogo_colaboradores_id = $arr['10']; // COLABORADDOR ID
				$datos->_clave_noi = $arr['0']; // // CLAVE NOI
				$datos->_prorrateo_periodo_id = $arr['11']; // PRORRATEO
				$datos->_premio_asistencia = $arr['2']; // ASISTENCIA
				$datos->_premio_puntualidad = $arr['3']; // PUNTUALIDAD
				$datos->_horas_extra = $arr['4']; // H EXTRA
				$despensa = ($arr['5'] == "0.00") ? $arr['5'] : 0;
				$datos->_despensa_efectivo = $despensa; // DESPENSA
				$datos->_incentivo = $arr['6']; // INCENTIVO
				$datos->_prima_dominical = 0; // PRIMA
				$datos->_domingo_trabajo = $arr['7']; // DOMINGO
				$datos->_total_percepciones = $arr['13']; // TOTAL
				$datos->_identificador = $arr['12']; // TOTAL,
                $datos->_festivo = $arr['15']; // TOTAL

				/*if($arr['0'] == 43){
					echo "<pre>";print_r($_POST);echo "</pre>";
					echo "<br> consulta numero final<br>";
					echo "<br> 0 .- ";print_r($arr[0]);
					echo "<br> 1 .- ";print_r($arr[1]);
					echo "<br> 2 .- ";print_r($arr[2]);
					echo "<br> 4 .- ";print_r($arr[4]);
					echo "<br> 5 .- ";print_r($arr[5]);
					echo "<br> 6 .- ";print_r($arr[6]);
					echo "<br> 7 .- ";print_r($arr[7]);
					echo "<br> 8 .- ";print_r($arr[8]);
					echo "<br>11 .- ";print_r($arr[11]);
					echo "<br>11 .- ";print_r($arr[10]);
					echo "<br>11 .- ";print_r($arr[11]);
					echo "<br>12 .- ";print_r($arr[12]);
					echo "<br>13 .- ";print_r($arr[13]);
					echo "<br>14 .- ";print_r($arr[14]);
					echo "<br>15 .- ";print_r($arr[15]);
					echo "<br> STDCLASS<br>";
					print_r($arr[8]);
					echo "<br> datos<br><pre>";
					print_r($datos);
					echo "</pre><br>";

					print_r($datos);

				}*/
				$insertarDatos = ProrrateoDao::insertProrrateoPeriodoResumen($datos);

			}
		}

				$arrayClaveNoiColaborador = array();
                for ($i = 1; $i <= MasterDom::getData('count'); $i++) {
                        $arr = explode("|", MasterDom::getData("datos_colaborador_" . $i));
                        array_push($arrayClaveNoiColaborador, $arr['0']);
                }

                $stdClassFaltas = new \stdClass();
                $prorrateoPeriodo = MasterDom::getData('prorrateo_periodo_id');
                $stdClassFaltas->nomina = MasterDom::getData('identificador');
                foreach ($arrayClaveNoiColaborador as $key => $value) {

                        $busquedaFaltas = ProrrateoDao::busquedaFaltas($value, $prorrateoPeriodo, MasterDom::getData('identificador'));
                        if(count($busquedaFaltas) > 0){
                                $arrayFechas  = array();
                                $add['clave'] = $value;
                                foreach ($busquedaFaltas as $k => $ff) {
                                    $valor = $this->getStatusFalta(trim($ff['estatus']));
                                    array_push($arrayFechas, array($ff['fecha'] => $valor));
                                }
                                $add['fechas'] = $arrayFechas;
                                $stdClassFaltas->faltas[$key] = $add;
                        }
                }

		// CREACION DEL ARRAY DE FALTAS
		/*
		$arrayClaveNoiColaborador = array();
		for ($i = 1; $i <= MasterDom::getData('count'); $i++) {
			$arr = explode("|", MasterDom::getData("datos_colaborador_" . $i));
			array_push($arrayClaveNoiColaborador, $arr['0']);
		}

		foreach ($arrayClaveNoiColaborador as $key => $value) {

			$busquedaFaltas = ProrrateoDao::busquedaFaltas($value, MasterDom::getData('prorrateo_periodo_id'));

			$stdClassFaltas->nomina = MasterDom::getData('identificador');

			if(!empty($busquedaFaltas)){
				$arrayFechas  = array();
				$add = array();
				foreach ($busquedaFaltas as $k => $ff) {
					$valor = $this->getStatusFalta(trim($ff['estatus']));
					//echo "<pre>";print_r("¡".$ff['estatus']."¡"); echo "</pre>";
					array_push($arrayFechas, array($ff['fecha'] => $valor));
					$add = array(
						"clave"=>$value,
						"fechas"=> $arrayFechas
					);
				}
				$stdClassFaltas->faltas[$key] = $add;
			}
		}
		*/
		$array_merge = array_merge(get_object_vars($stdClassFaltas), get_object_vars($stdClassIncentivos));
		$statusNoi = MasterDom::curlPost($array_merge);
		$html =<<< html
			<pre>{$statusNoi}</pre>
html;

$identificador = MasterDom::getData('identificador');

$msj = <<<html
<div class="alert alert-success alert-dismissible" role="alert">
		Se han ingresado datos correctamente a NOI de
	<a href="/ProrrateoT/calculo/Semanal/{$identificador}/" class="btn btn-success">
		Regresar a prorrateo {$identificador}
	</a>
</div>
html;
		View::set('msjPeriodoCerrado',$msj);
		//MRR
		$num = 0;
    $nominas = ProrrateoDao::consultanomnoi(MasterDom::getData('prorrateo_periodo_id'));
    foreach($nominas as $key => $value){
      $num ++;
    }

		if($num == 4){
			ProrrateoDao::updatePeriodoProrrateo(MasterDom::getData('prorrateo_periodo_id'));
			$msj = <<<html
<div class="alert alert-success alert-dismissible" role="alert">
	<strong>Cierre de periodo</strong> Este periodo ya esta cerrado
	<a href="/ProrrateoT/calculo/Semanal/{$identificador}/" class="btn btn-success">
		Regresar a prorrateo {$identificador}
	</a>
</div>
html;
			View::set('msjPeriodoCerrado',$msj);
		}

		$msjPeriodo = $this->getPeriodoMensaje(MasterDom::getData('prorrateo_periodo_id'));
		View::set('statusNoi',$html);
		View::set('msjPeriodo',$msjPeriodo);
		View::set('header',$this->_contenedor->header($this->getHeader()));
		View::set('footer',$this->_contenedor->footer($this->getFooter()));
		View::render('prorrateo_resumen');

	}

	public function getStatusFalta($status){
		$valor = "";

			if((int)$status > 0){
				$valor = $this->getStatusIncidencia($status);
			}else{
				switch ($status) {
					case '0':
                                                $valor = 'A';
                                                break;
					case '-1':
						$valor = 'F';
						break;
					case '-2':
						$valor = 'R';
						break;
					case '-11':
						$valor = 'FF';
						break;
					case '-2':
						$valor = 'R';
						break;
					case '-22':
						$valor = 'FR';
						break;
					case '-23':
                                                $valor = 'DF';
                                                break;
					case '-24':
						$valor = 'AA';
						break;
					case '-25':
                                                $valor = 'RDF';
                                                break;
					case '-26':
						$valor = 'FRDF';
						break;
					default:
						break;
				}
			}


		return $valor;
	}

	public function getStrinfInt($status){

	}

	public function getStatusIncidencia($id){
		$incidencia = ProrrateoDao::getIdentificadorIncidencia($id);
		return $incidencia['id_i'];
	}

	public function getFestivo($salarioDiario){
		return $salarioDiario * 2;
	}

	public function horasExtraPrevio($total, $LimiteHorasExtra, $premioAsistencia, $premioPuntualidad){
		$operacion1 = $total - $premioAsistencia - $premioPuntualidad;

		$resultado = 0;
		if($total == 0 && $premioAsistencia == 0 && $premioPuntualidad == 0){
			$resultado = 0;
		}else{

			if($operacion1 == 0 || $operacion1 == 1.4210854715202E-14){
				$resultado = 0;
			}else{
				if($operacion1 < $LimiteHorasExtra){
					$resultado = $operacion1;
				}

				if($operacion1 > $LimiteHorasExtra){
					$resultado = $LimiteHorasExtra;
				}
			}
		}

		$val = round($resultado, 2);


		if($val < 0){
			$res = 0;
		}else{
			$res = ($val == '-0') ? 0 : $val;
		}

		return $res;//$total."-".$premioAsistencia."-".$premioPuntualidad."=".$operacion1 ."<".$LimiteHorasExtra;
	}

	// Obtiene la cantidad de horas extra del colaborador con el periodo actual SEMANAL o QUINCENAL
	public function getHorasExtra($idColaborador,$idPeriodo){
		$cantidadHorasExtra = ProrrateoDao::getHorasExtraColaboradorPeriodo($idColaborador, $idPeriodo);
		return $cantidadHorasExtra[0]['horas_extra'];
	}

	// Obtener la cantidad de incentivos asignados para el colaborador, con el periodo actual, SEMANAL o ACTUAL
	public function getIncentivos($idColaborador,$idPeriodo){
		$sumaIncentivos = ProrrateoDao::getIncentivosColabordorPeriodo($idColaborador,$idPeriodo);
		$op = ($sumaIncentivos['suma_incentivos']>0) ? $sumaIncentivos['suma_incentivos'] : 0;
		$num = number_format($op, 2, '.', '');
		return $num;
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

	// Suma de cantidades de pesos de horas extra y la suma de incentivos
	//MRR
	public function getSumaHorasExtra_IncentivosPeriodos($horasExtra, $sumaIncentivos, $nuervosValores){
		if ($horasExtra != '0.00' AND $sumaIncentivos != '0.00' AND $nuervosValores != '0.00') {
			return $horasExtra + $sumaIncentivos + $nuervosValores;
		}
		else if ($horasExtra != '0.00' AND $sumaIncentivos == '0.00' AND $nuervosValores != '0.00') {
			return $horasExtra + $nuervosValores;
		}
		else if ($horasExtra == '0.00' AND $sumaIncentivos != '0.00' AND $nuervosValores != '0.00') {
			return $sumaIncentivos + $nuervosValores;
		}
		else if ($horasExtra != '0.00' AND $sumaIncentivos == '0.00' AND $nuervosValores == '0.00') {
			return $horasExtra;
		}
		else if ($sumaIncentivos != '0.00' AND $horasExtra == '0.00' AND $nuervosValores == '0.00') {
			return $sumaIncentivos;
		}
		else {
			return 0;
		}

		/*
		**:::JUAN::070319
		**:::REGRESAR BLOQUE DE ABAJO Y BORRAR ARRIBA
		*/

		/*
		if($sumaIncentivos != '0.00'){
			return $horasExtra + $sumaIncentivos + $nuervosValores;
		}else{
			return '0.00';
		}
		*/

	}

	// Limite de horas extra
	public function getLimiteHorasExtra($salarioDiario){
		return number_format((($salarioDiario * 2) / 8 ) * 9, 2, '.', '');
	}

	// Coloca la cantidad del incentivo de premio de asistencia
	public function getPremioAsistencia($sumaIncentivos, $totalHorasExtraIncentivos, $sdi){

		/*
		if($totalHorasExtraIncentivos>0){
			$operacion = (($sdi * 7)*0.1);
			if($sumaIncentivos > 0){
				$nuevoResultado = number_format($operacion, 2, '.', '');
				return $nuevoResultado;
			}else{
				return 0;
			}
		}else{
			return 0;
		}
		*/

		/*
		**::JUAN CAMBIO 07/03/2019
		**
		*/
		if($totalHorasExtraIncentivos>0 OR $sumaIncentivos > 0){
			$operacion = (($sdi * 7)*0.1);
			$nuevoResultado = number_format($operacion, 2, '.', '');
			return $nuevoResultado;
		}
		else{
			return 0;
		}
	}

	// Coloca la cantidad del incentivo de premio de asistencia
	public function getPremioPuntualidad($sumaIncentivos, $totalHorasExtraIncentivos, $sdi, $premioAsistencia){
		/*=REDONDEAR(SI((TOTALINCENTIVOS-PREMIOASISTENCIA)>((SDI*7)*0.1),((SDI*7)*0.1),SI(Y((INCENTIVOSOPECION-PREMIOASISTENCIA)<((SDI*7)*0.1),(INCENTIVOSOPECION-PREMIOASISTENCIA)>0),(INCENTIVOSOPECION-PREMIOASISTENCIA),0)),2)


		=REDONDEAR(

			SI((J25-L25)>((E25*7)*0.1),
				((E25*7)*0.1),

			SI(Y((G25-L25)<((E25*7)*0.1),
				(G25-L25)>0),
				(G25-L25),0)),2)

		(TOTALINCENTIVOS-PREMIOASISTENCIA) ? ((SDI*7)*0.1)
		((SDI*7)*0.1)**/

		if($totalHorasExtraIncentivos>0){
			if($sumaIncentivos - $premioAsistencia > ( ($sdi * 7) * 0.1) ){
				$operacion = (($sdi * 7)*0.1);
				$nuevoResultado = number_format($operacion, 2, '.', '');
				return $nuevoResultado;
			}elseif($sumaIncentivos - $premioAsistencia < ( ($sdi * 7) * 0.1) ){
				$operacion = $sumaIncentivos - $premioAsistencia;
				$nuevoResultado = number_format($operacion, 2, '.', '');
				return $nuevoResultado;
			}else{
				return 0;
			}
		}else{
			return 0;
		}
	}

	// Obtener el numero de horas extra
	public function getNumeroHorasExtra($salarioDiario, $importeHorasExtra){
		$operacionSalarioDiarioEntre8 = $salarioDiario / 8;
		$operacionImporteHExtra = $importeHorasExtra / $operacionSalarioDiarioEntre8;
		$operacion = $operacionImporteHExtra / 2;
		$round = round($operacion, 2);
		$op = $importeHorasExtra/($salarioDiario/8)/2;

		//return ($round>9) ? 9 : $round;
		$nuevoResultado = number_format($op, 2, '.', '');
		return $nuevoResultado;
	}

	// Obtiene el total de despensa en efectivo
	public function getDespensaEnEfectivo($totalHorasExtraIncentivos, $premioAsistencia, $premioPuntualidad, $importeHorasExtra){
		//$salarioMinimo = 88.36;// 73.04;
		$sm = GeneralDao::getSalarioMinimo();
		$salarioMinimo = $sm['cantidad'];

		$operacion = number_format($totalHorasExtraIncentivos - $premioAsistencia - $premioPuntualidad - $importeHorasExtra, 2, '.','');
		$operacionSalarioMinimo = number_format( (($salarioMinimo * 7) * 0.4), 2, '.','');

		$ope = 0;
		if($operacion > $operacionSalarioMinimo){
			$ope = $operacionSalarioMinimo;
		}elseif($operacion < $operacionSalarioMinimo){
			$ope = $operacion;
		}else{
			$ope = 0;
		}

		$val = round($ope, 2);

		if($val < 0){
			$res = 0;
		}else{
			$res = $val;
		}

		return $res; //$totalHorasExtraIncentivos . " - " . $premioAsistencia . " - " . $premioPuntualidad;

	}

	// Importe de horas extras
	public function getImporteHorasExtras($salarioDiario, $totalHorasExtraIncentivos, $LimiteHorasExtra, $permioAsistencia, $premioPuntualidad, $horasExtraPrevio){

		$operacion = ($salarioDiario / 8) * 2;

		if($horasExtraPrevio >= $operacion){
			return $horasExtraPrevio;
		}else{
			return 0;
		}

		exit;
		$operacion1 = $totalHorasExtraIncentivos - $permioAsistencia - $premioPuntualidad;
		$operacion2 = $totalHorasExtraIncentivos - $permioAsistencia - $premioPuntualidad > $LimiteHorasExtra;
		if($operacion1 > $LimiteHorasExtra){
			return 0; //$LimiteHorasExtra;
		}elseif($operacion1 > 0){
			if($LimiteHorasExtra > $totalHorasExtraIncentivos){
				return 0;
			}else{
				return $operacion1;
			}
		}
	}

	public function getIncentivo($totalHorasExtraIncentivos, $premioAsistencia, $premioPuntualidad, $importeHorasExtra, $despensaEnEfectivo){

		$operacion1 = number_format($totalHorasExtraIncentivos-$premioAsistencia-$premioPuntualidad-$importeHorasExtra,2,'.','')-$despensaEnEfectivo;

		if($operacion1 <= 0){
			return 0;
		}else{
			return $operacion1;
		}
	}

	// Obtiene la cantidad del prorrateo
	public function getTotalProrrateo($premioAsistencia, $premioPuntualidad, $importeHorasExtra, $despensaEnEfectivo, $incentivo){
		return number_format(($premioAsistencia+$premioPuntualidad+$importeHorasExtra+$despensaEnEfectivo+$incentivo),2,'.','');
	}

	// Obtener el incentivo de noche
	public function getIncentivoNoche($idColaborador,$idPeriodo){
		$sumaIncentivos = ProrrateoDao::getIncentivoNoche($idColaborador,$idPeriodo);
		return $sumaIncentivos['suma_incentivos_noche'];
	}

	public function getPrimaDominical($idColaborador, $idPeriodo){
		$resultado = ProrrateoDao::getPrimaDominical($idColaborador, $idPeriodo);
		return (!empty($resultado['domigo_procesos'])) ? $resultado['domigo_procesos'] : 0;
	}

	public function getDomingoLaborados($idColaborador, $idPeriodo){
		$resultado = ProrrateoDao::getDomingoLaborado($idColaborador, $idPeriodo);
		return (!empty($resultado['domingo_laborado'])) ? $resultado['domingo_laborado'] : 0;
	}

	/*
		@$tipo -> SEMANAL o QUINCENAL
		@status -> 1 Abierto y 0 Cerrado
	*/
		//MRR
	public function getPeriodo($tipo, $status, $identificador_noi){
		$periodo = ProrrateoDao::searchPeriodos($tipo, $status);
		if($status == 0 || empty($identificador_noi)){
			$h2 =<<<html
				<h1>Guardar resumenes</h1>
html;
			$p =<<<html
				<h4>Debes guardar los datos de resumenes, para poder generar el prorrateo</h4>
html;
			View::set('error',"Mensaje");
			View::set('tituloError',$h2);
			$display = "style=\"display:none;\" ";
			View::set('visualizar', $display);
			View::set('mensajeError',$p);
			View::render("error");
			exit;
		}elseif($status == 1){
			$h2 =<<<html
				<h1>Periodo Cerrado</h1>
html;
			$p =<<<html
				<h4>No hay periodo abierto, debe existir un periodo abierto</h4>
html;
			View::set('error',"Mensaje");
			View::set('tituloError',$h2);
			$display = "style=\"display:none;\" ";
			View::set('visualizar', $display);
			View::set('mensajeError',$p);
			View::render("error");
			exit;
		}else{
			$fechaIni = MasterDom::getFecha($periodo[0]['fecha_inicio']);
			$fechaFin = MasterDom::getFecha($periodo[0]['fecha_fin']);

			if ($periodo[0]['status'] == 0) {
				$label = "success";
				$status = "Abierto";
			}
			if ($periodo[0]['status'] == 1) {
				$label = "danger";
				$status = "Cerrado";
			}
			if ($periodo[0]['status'] == 3) {
				$label = "info";
				$status = "en proceso de cierre";
			}
			$htmlPeriodo = <<<html
			<b>( {$fechaIni} al {$fechaFin} )</b> <label class="label label-{$label}"> periodo {$status}</label>
html;
		}
		return $htmlPeriodo;
    }

    public function getPeriodoT($tipo, $status){
		$periodo = ProrrateoDao::searchPeriodos($tipo, $status);

			$fechaIni = MasterDom::getFecha($periodo[0]['fecha_inicio']);
			$fechaFin = MasterDom::getFecha($periodo[0]['fecha_fin']);

			if ($periodo[0]['status'] == 0) {
				$label = "success";
				$status = "Abierto";
			}
			if ($periodo[0]['status'] == 1) {
				$label = "danger";
				$status = "Cerrado";
			}
			if ($periodo[0]['status'] == 3) {
				$label = "info";
				$status = "en proceso de cierre";
			}
			$htmlPeriodo = <<<html
			<b>( {$fechaIni} al {$fechaFin} )</b> <label class="label label-{$label}"> periodo {$status}</label>
html;
		return $htmlPeriodo;
    }

    public function getPeriodoMensaje($id){
		$periodo = ProrrateoDao::getPeriodoId($id);
		$fechaIni = MasterDom::getFecha($periodo['fecha_inicio']);
		$fechaFin = MasterDom::getFecha($periodo['fecha_fin']);

		if ($periodo['status'] == 0) {
			$label = "success";
			$status = "Abierto";
		}
		if ($periodo['status'] == 1) {
			$label = "danger";
			$status = "Cerrado";
		}
		if ($periodo['status'] == 3) {
			$label = "info";
			$status = "en proceso de cierre";
		}
		$htmlPeriodo = <<<html
			<b>( {$fechaIni} al {$fechaFin} )</b> <label class="label label-{$label}"> periodo {$status}</label>
html;
		return $htmlPeriodo;
    }



    /*
		@$tipo -> SEMANAL o QUINCENAL
		@status -> 1 Abierto y 0 Cerrado
	*/
	public function getPeriodoHistoricosTipo($periodoId){
		$periodo = ProrrateoDao::getPeriodoById($periodoId);
		if(empty($periodo)){
			View::set('error',"No hay Periodos Cerrados");
			View::set('tituloError',"Al parecer no hay periodo Cerrados");
			$display = "style=\"display:none;\" ";
			View::set('visualizar', $display);
			View::set('mensajeError',"Debe existir periodos cerrados, para checar prorrateos historicos");
			View::render("error");
			exit;
		}else{
			$fechaIni = MasterDom::getFecha($periodo['fecha_inicio']);
			$fechaFin = MasterDom::getFecha($periodo['fecha_fin']);
			$status = ($periodo['status'] == 0) ? "Abierto": "Cerrado";
			$label = ($periodo['status'] == 0) ? "success": "danger";
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
    public function getIdPeriodo($tipo, $status){
		$periodo = ProrrateoDao::searchPeriodos($tipo, $status);
		return $periodo[0]['prorrateo_periodo_id'];
    }

	/*
		Obtiene los incentivos SEMANALES O QUINCENALES, que ya han sido procesados
	*/
	public function getPeriodosHistoricos($tipo, $periodoObtenido){
		$periodos = ProrrateoDao::getTipoPeriodo($tipo);
		$option = "";
		foreach ($periodos as $key => $value) {
    		$selected = ($value['prorrateo_periodo_id'] == $periodoObtenido) ? "selected" : "";
    		$selected = ($value['prorrateo_periodo_id'] == $periodoObtenido) ? "selected" : "";
			$fechaIni = MasterDom::getFecha($value['fecha_inicio']);
			$fechaFin = MasterDom::getFecha($value['fecha_fin']);
			$option .=<<<html
				<option {$selected} value="{$value['prorrateo_periodo_id']}">({$fechaIni}) al ({$fechaFin})</option>
html;
    	}
    	return $option;
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
						"order": false,
                       	dom: 'Bfrtip',
                      	buttons: [
                          'excelHtml5'
                      	]
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

}
