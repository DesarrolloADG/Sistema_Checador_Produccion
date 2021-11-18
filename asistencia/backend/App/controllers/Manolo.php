<?php
namespace App\controllers;
defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \App\controllers\Contenedor;
use \Core\Controller;
use \App\models\Manolo AS ManoloDao;

class Manolo extends Controller{

    private $_contenedor;

    function __construct(){
      parent::__construct();
      $this->_contenedor = new Contenedor;
      View::set('header',$this->_contenedor->header());
      View::set('footer',$this->_contenedor->footer());
	    //echo "es el usuario : ---{$this->_contenedor->getUsuario()}----+++++";
    }

    public function index() {
      $extraFooter =<<<html
      <script>
        $(document).ready(function(){
          $("#muestra-cupones").tablesorter();

            var oTable = $('#muestra-manolo').DataTable();
           
		   
		   $("#delete").on( "click", function() {
				alert("clcik");
			});
		   
        });
      </script>
	 
html;
      $usuario = $this->__usuario;
	  
	  /*
      $departamentos = ManoloDao::getAll();
     
      $tabla= '';
      foreach ($departamentos as $key => $value) {
        $tabla.=<<<html
                <tr>
                    <td><input type="checkbox" name="borrar[]" value="{$value['catalogo_departamento_id']}"/></td>
                    <td>{$value['nombre']}</td>
                    <td>{$value['status']}</td>
                    <td class="center" >
                        <a href="/Departamento/edit/{$value['catalogo_departamento_id']}" {$editarHidden} type="submit" name="id" class="btn btn-primary"><span class="fa fa-pencil-square-o" style="color:red"></span> </a>
                        <a href="/Departamento/show/{$value['catalogo_departamento_id']}" type="submit" name="id_empresa" class="btn btn-success"><span class="glyphicon glyphicon-eye-open" style="color:white"></span> </a>
                    </td>
                </tr>
				
html;
      }
	  */
	  
	  $tablaTr = '';
	  
	  $jugadoresArray = ManoloDao::obtienJugadoresAll(2);
	  foreach($jugadoresArray AS $departamento){
		  $tablaTr .=<<<html
			<tr>
				<td>{$departamento['catalogo_departamento_id']}</td>
				<td>{$departamento['nombre']}</td>
				<td><button id="export_excel_manolo" type="button" class="btn btn-warning btn-circle" ><i class="fa fa-file-excel-o"> <b>{$departamento['status']}</b></i></button></td>
			</tr>
html;
	  }
	  
	  echo $this->_contenedor->header($extraHeader);
	  
	  $variable =<<<html
	div class="right_col">
  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    <div class="panel panel-default">
      <div class="x_title">
        <br><br>
        <h1> Catálogo de Gestión de Departamentos</h1>
        <div class="clearfix"></div>
      </div>
      <form name="delete-form" name="all" id="all" action="/Departamento/delete" method="POST">
    
		
		<div class="panel-body" >
          
		  <table class="table" id="muestra-manolo">
              <thead>
                <tr>
                  <th>catalogo_departamento_id</th>
                  <th>nombre</th>
                  <th>status</th>
                  
                </tr>
              </thead>
              <tbody>
                $tablaJugadores
              </tbody>
            </table>
		  
        </div>
		
		
      </form>
    </div>
  </div>
</div>
html;
	  echo $variable;
	  
	  echo $this->_contenedor->footer($extraFooter);
	  
	  exit();
	  
	  /*
	  $texto='boton manolo';
      View::set('tabla',$tabla);
      View::set('header',$this->_contenedor->header($extraHeader));
      View::set('footer',$this->_contenedor->footer($extraFooter));
	  View::set('btn',$texto);
	  View::set('tablaJugadores', $tablaTr);
      View::render("manolo_all");
	  */
    } 

}