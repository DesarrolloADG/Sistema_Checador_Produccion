<?php
namespace App\controllers;
defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \Core\Controller;
use \App\models\Test as TestDao;

//NIOMBRE DE LA CLASE EN MAYUSCULA INICIAL
class Test extends Controller{

//DECLARACION DE ARIBUTOS
private $__atributo = 10; 

    function __construct(){
	//SE EJECUTO PARA VALIDA LA SESSION SE ESTA HEREDANDO DE LA CLASE CONTROLLER
	//ARRIBA SE TIENE LA RUTA DEL PADRE EN EL USE
	parent::__construct();
	$this->_contenedor = new Contenedor;
        View::set('header',$this->_contenedor->header());
        View::set('footer',$this->_contenedor->footer());
    } 

    //DECLARACION DE METODO
    public function getTest(){

	//DECLARCION DE VARIABLE
	$_variable = 10;
	$array = TestDao::getAll();
	$html = print_r($array, 1);
	View::set('contenido',$html);
	//NOMBRE DEL CONTROLLADOR_ACCION
        View::render("test_show");
    }
}
