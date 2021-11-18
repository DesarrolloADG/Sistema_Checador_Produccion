<?php
namespace App\controllers;
//defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \App\controllers\Contenedor;
use \Core\Controller;
use \App\models\AsignarIncentivos AS AsignarIncentivosDao;
use \App\models\ResumenSemanal AS ResumenSemanalDao;

class TemplateNoi extends Controller{

    private $_contenedor;

    function __construct(){
    }

    public function index() {

	$this->_contenedor = new Contenedor;
        View::set('header',$this->_contenedor->header());
        View::set('footer',$this->_contenedor->footer());
        $usuario = $this->__usuario;
	//View::set('header',$this->_contenedor->header($extraHeader));
      	//View::set('footer',$this->_contenedor->footer($extraFooter));
      	View::render("template_noi_add");
    }

    public function add() {

	if($_FILES['incentivos']['name'] == '' OR $_FILES['faltas']['name'] == ''){
	    return MasterDom::alertas('personal', '/TemplateNoi', 'Error', 'Lo sentimos algun archivo no se subio correctamente');
	}

	$incentivosFile = $_FILES['incentivos'];
	$archivo = MasterDom::moverDirectorio($incentivosFile, 'granja', 'csub');
        if($archivo === false)
            return MasterDom::alertas('error_general');

	$excelIncentivos = json_decode(MasterDom::procesoExcel('completeArray', $archivo['nombre'], true),1);


	/*FALTAS*/
	$faltasFile = $_FILES['faltas'];
        $archivoFaltas = MasterDom::moverDirectorio($faltasFile, 'granja', 'csub');
        if($archivoFaltas === false)
            return MasterDom::alertas('error_general');

	$excelFaltas = json_decode(MasterDom::procesoExcel('completeArray', $archivoFaltas['nombre'], true),1);

	$arrayFaltas = array();     
        foreach($excelFaltas AS $k=>$value){

            if($k == 0)
                continue;
           
            if($value['A'] == '' OR $value['A'] == 0)
                continue;

	    $fechas = array();
	    $clave = $value['A'];
	    if($value['B'] != '')
	    	array_push($fechas, array($excelFaltas[1]['B'] => $value['B']));

	    if($value['C'] != '')
                array_push($fechas, array($excelFaltas[1]['C'] => $value['C']));

	    if($value['D'] != '')
                array_push($fechas, array($excelFaltas[1]['D'] => $value['D']));

	    if($value['E'] != '')
                array_push($fechas, array($excelFaltas[1]['E'] => $value['E']));

	    if($value['F'] != '')
                array_push($fechas, array($excelFaltas[1]['F'] => $value['F']));

	    if($value['G'] != '')
                array_push($fechas, array($excelFaltas[1]['G'] => $value['G']));

	    if($value['H'] != '')
                array_push($fechas, array($excelFaltas[1]['H'] => $value['H']));

	    if($value['I'] != '')
                array_push($fechas, array($excelFaltas[1]['I'] => $value['I']));

            $datoFaltas = array('clave'=>$clave, 
                                        'fechas' => $fechas
                                );
            array_push($arrayFaltas, $datoFaltas);
        }

	/*INCENTIVOS*/

	$arrayIncentivos = array();	
	foreach($excelIncentivos AS $key=>$valueIncentivos){

	    if($key == 0)
		continue;
	   
	    if($valueIncentivos['A'] == '' OR $valueIncentivos['A'] == 0)
	  	continue;

	    $clave = $valueIncentivos['A'];
	    $asistencia = ($valueIncentivos['B']  == '' OR $valueIncentivos['B'] == 0) ? '0' : $valueIncentivos['B'];
	    $puntualidad = ($valueIncentivos['C']  == '' OR $valueIncentivos['C'] == 0) ? '0' : $valueIncentivos['C'];
	    $horas = ($valueIncentivos['D']  == '' OR $valueIncentivos['D'] == 0) ? '0' : $valueIncentivos['D'];
	    $despensa = ($valueIncentivos['E']  == '' OR $valueIncentivos['E'] == 0) ? '0' : $valueIncentivos['E'];
	    $incentivo = ($valueIncentivos['F']  == '' OR $valueIncentivos['F'] == 0) ? '0' : $valueIncentivos['F'];
	    $prima = ($valueIncentivos['G']  == '' OR $valueIncentivos['G'] == 0) ? '0' : $valueIncentivos['G'];
	    $domingo = ($valueIncentivos['H']  == '' OR $valueIncentivos['H'] == 0) ? '0' : $valueIncentivos['H'];

	    $datoIncentivos = array('clave'=>$clave, 
					'datos' => array(
						'asistencia'=>$asistencia, 
						'puntualidad'=>$puntualidad,
						'horas'=>$horas,
						'despensa'=>$despensa,
						'incentivo'=>$incentivo,
						'prima'=>$prima,
						'domingo'=>$domingo
					)
				);	
	    array_push($arrayIncentivos, $datoIncentivos);
	}

	$nomina = ($_POST['nomina'] == '') ? 'XOCHIMILCO' : $_POST['nomina'];

	$general = array('nomina'=>$nomina,
			'faltas'=>$arrayFaltas,
			'incentivos'=>$arrayIncentivos);

	//$general = serialize($general);
	print_r($general);
exit;
	$curl = $this->curlPost($general);
	return MasterDom::alertas('personal', '/TemplateNoi', 'Success', 'Se realizo exitosamente la carga');

        //View::set('header',$this->_contenedor->header($extraHeader));
        //View::set('footer',$this->_contenedor->footer($extraFooter));
        //View::render("template_noi_add");
    }

    public function getPost(){

	$nomina = $_POST['nomina'];
	if($nomina == 'XOCHIMILCO'){
	    $asistenciaNoi = 34;
	    $puntualidadNoi = 89;
	    $horasNoi = 98;
	    $despensa = 89;
	    $incentivo = 77;
	    $prima = 40;
	    $domingo = 89;
	}

	$faltas = $_POST['faltas'];
	$incentivos = $_POST['incentivos'];

	echo "$nomina";
	print_r($faltas);
	print_r($incentivos);

	foreach($incentivos AS $value){

	    if($value['clave'] == '' OR $value['clave'] == 0)
                continue;	    
	    $clave = $value['clave'];
	    echo "+++CLAVE: $clave+++\n";

	    if(($asistencia = $value['datos']['asistencia']) != 0){
		echo "+++SOY ASISTENCIA: $asistencia\n";
	    }

	    if(($puntualidad = $value['datos']['puntualidad']) != 0){
                echo "+++SOY PUNTUALIDAD: $puntualidad\n";
            }

	    if(($horas = $value['datos']['horas']) != 0){
                echo "+++SOY HORAS: $horas\n";
            }

	    if(($despensa = $value['datos']['despensa']) != 0){
                echo "+++SOY DESPENSA: $despensa\n";
            }

	    if(($incentivo = $value['datos']['incentivo']) != 0){
                echo "+++SOY INCENTIVO: $incentivo\n";
            }

	    if(($prima = $value['datos']['prima']) != 0){
                echo "+++SOY PRIMA: $prima\n";
            }

	    if(($domingo = $value['datos']['domingo']) != 0){
                echo "+++SOY DOMINGO: $domingo\n";
            }
	}
exit;
    }

    public function curlPost($data){

        $url = 'localhost:8080/testing_01112021.php';

	$dato = http_build_query($data);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $dato);
	//execute
	$output = curl_exec($ch);
	if ($output === FALSE) {
  	    echo "cURL Error: " . curl_error($ch);
	    return -1;
	}
	curl_close($ch);
	return $output;
    }
}
