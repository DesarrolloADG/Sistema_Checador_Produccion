<?php
namespace App\controllers;
//defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \App\controllers\Contenedor;
use \Core\Controller;
use \App\models\AsignarIncentivos AS AsignarIncentivosDao;
class AsignarIncetivosTest extends Controller{

    private $_contenedor;

    function __construct(){
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header',$this->_contenedor->header());
        View::set('footer',$this->_contenedor->footer());
    }

    public function index() {
      echo "1";
      exit;
      $extraHeader=<<<html
        <style>.foto{ width:100px; height:100px; border-radius: 50px;}</style>
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
              table.search(jQuery.fn.DataTable.ext.type.search.html(this.value)).draw();
            } );
           
          });

          var hideAll = function() {
            $('.optiondiv').hide();
          }

          $('#select').on('change', function() {
            hideAll();
            var category = $(this).val();
            $('.' + category).show();
          });
        </script>
html;

      $semanal = "";
      foreach (AsignarIncentivosDao::getPeriodoSemanal() as $key => $value) {
        $mensaje  = ($value['status'] == 0) ? " de proceso":" terminado";
        $selected  = ($value['status'] == 0) ? "selected":"";
        $id_periodo_semanal = ($value['status'] == 0) ? $value['prorrateo_periodo_id'] : "";
        $semanal .=<<<html
          <option {$selected} value="{$value['prorrateo_periodo_id']}">Periodo: {$value['fecha_inicio']} - {$value['fecha_fin']} : Este periodo esta en estatus {$mensaje}</option>
html;
      }

      $quincenal = "";
      foreach (AsignarIncentivosDao::getPeriodoQuincenal() as $key => $value) {
        $mensaje  = ($value['status'] == 0) ? " de proceso":" terminado";
        $selected  = ($value['status'] == 0) ? "selected":"";
        $id_periodo_quincenal = ($value['status'] == 0) ? $value['prorrateo_periodo_id'] : "";
        $quincenal .=<<<html
          <option {$selected} value="{$value['prorrateo_periodo_id']}">Periodo: {$value['fecha_inicio']} - {$value['fecha_fin']} : Este periodo esta en estatus {$mensaje}</option>
html;
      }


      $datosUsuario = AsignarIncentivosDao::getDatosUsuarioLogeado($this->__usuario);
      $secciones = AsignarIncentivosDao::getDepartamentos($datosUsuario['administrador_id']);
      $tabla = '';
      foreach ($secciones as $key1 => $value1) {
        foreach (AsignarIncentivosDao::getAllColaboradores($value1['catalogo_departamento_id']) as $key => $value) {
          
          if(strtoupper($value['pago']) == "SEMANAL")
            $periodo_consultar = $id_periodo_semanal;
          

          if(strtoupper($value['pago']) == "QUINCENAL")
            $periodo_consultar = $id_periodo_quincenal;
          
          $tabla .=<<<html
            <tr>
              <td style="text-align:center; vertical-align:middle;"><img class="foto" src="/img/colaboradores/{$value['foto']}"/></td>
              <td style="text-align:center; vertical-align:middle;">{$value['nombre']} {$value['apellido_paterno']} {$value['apellido_materno']}</td>
              <td style="text-align:center; vertical-align:middle;">{$value['nombre_departamento']}</td>
              <td style="text-align:center; vertical-align:middle;">{$value['pago']}</td>
              <td style="text-align:center; vertical-align:middle;">
html;
          foreach (AsignarIncentivosDao::getIncentivosColaborador($value['catalogo_colaboradores_id']) as $k => $val) {
            $tabla .=<<<html
              <p>{$val['nombre']}: $ {$val['cantidad']}</p>
html;
          }
          $tabla .=<<<html
              </td>
              <td style="text-align:center; vertical-align:middle;">
                <a href="/AsignarIncentivos/incentivos/{$value['catalogo_colaboradores_id']}/{$periodo_consultar}" type="submit" name="id_empresa" class="btn btn-primary"><i class="fa fa-plus"></i> </a>
              </td>
            </tr>
html;
        }
      }
        


      View::set('tabla',$tabla);
      View::set('quincenal',$quincenal);
      View::set('semanal',$semanal);
      View::set('header',$this->_contenedor->header($extraHeader));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("asignar_incentivos_all");
    }

    public function filtros(){
      $extraHeader=<<<html
        <style>.foto{ width:100px; height:100px; border-radius: 50px;}</style>
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
              table.search(jQuery.fn.DataTable.ext.type.search.html(this.value)).draw();
            } );
           
          });

          var hideAll = function() {
            $('.optiondiv').hide();
          }

          $('#select').on('change', function() {
            hideAll();
            var category = $(this).val();
            $('.' + category).show();
          });
        </script>
html;

      $semanal = "";
      foreach (AsignarIncentivosDao::getPeriodoSemanal() as $key => $value) {
        $semanal .=<<<html
          <option value="{$value['prorrateo_periodo_id']}">Periodo: {$value['fecha_inicio']} - {$value['fecha_fin']}</option>
html;
      }


      $quincenal = "";
      foreach (AsignarIncentivosDao::getPeriodoQuincenal() as $key => $value) {
        $quincenal .=<<<html
          <option value="{$value['prorrateo_periodo_id']}">Periodo: {$value['fecha_inicio']} - {$value['fecha_fin']}</option>
html;
      }

      $periodo = MasterDom::getData('select');


      $filtro;
      if($periodo == "ambos"){
        $filtro = "AMBOS";
        $id_periodo_semanal = MasterDom::getData('semanal1');
        $id_periodo_quincenal = MasterDom::getData('quincenal1');
      }

      if($periodo == "semanal"){
        $filtro = "SEMANAL";
        $id_periodo_semanal = MasterDom::getData('semanal1');
      }

      if($periodo == "quincenal"){
        $filtro = "QUINCENAL";
        $id_periodo_quincenal = MasterDom::getData('quincenal1');
      }
      

      $datosUsuario = AsignarIncentivosDao::getDatosUsuarioLogeado($this->__usuario);
      $secciones = AsignarIncentivosDao::getDepartamentos($datosUsuario['administrador_id']);
      $tabla = '';
      foreach ($secciones as $key1 => $value1) {
        foreach (AsignarIncentivosDao::getAllColaboradoresPeriodo($value1['catalogo_departamento_id'], $filtro) as $key => $value) {
          $periodo_consultar;

          if(strtoupper($value['pago']) == "SEMANAL"){
            $periodo_consultar = $id_periodo_semanal;
          }

          if(strtoupper($value['pago']) == "QUINCENAL"){
            $periodo_consultar = $id_periodo_quincenal;
          }

          $tabla .=<<<html
            <tr>
              <td style="text-align:center; vertical-align:middle;"><img class="foto" src="/img/colaboradores/{$value['foto']}"/></td>
              <td style="text-align:center; vertical-align:middle;">{$value['nombre']} {$value['apellido_paterno']} {$value['apellido_materno']}</td>
              <td style="text-align:center; vertical-align:middle;">{$value['nombre_departamento']}</td>
              <td style="text-align:center; vertical-align:middle;">{$value['pago']}</td>
              <td style="text-align:center; vertical-align:middle;">
html;
          foreach (AsignarIncentivosDao::getIncentivosColaborador($value['catalogo_colaboradores_id']) as $k => $val) {
            $tabla .=<<<html
              <p>{$val['nombre']}: $ {$val['cantidad']}</p>
html;
          }
          $tabla .=<<<html
              </td>
              <td style="text-align:center; vertical-align:middle;">
                <a href="/AsignarIncentivos/incentivos/{$value['catalogo_colaboradores_id']}/{$periodo_consultar}" type="submit" name="id_empresa" class="btn btn-primary"><i class="fa fa-plus"></i> </a>
              </td>
            </tr>
html;
        }
      }
        


      View::set('tabla',$tabla);
      View::set('quincenal',$quincenal);
      View::set('semanal',$semanal);
      View::set('header',$this->_contenedor->header($extraHeader));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("asignar_incentivos_all");
    }

    public function incentivos($id, $idPeriodo){

      $extraFooter = <<<html
        <script>
          $(document).ready(function(){
          
            for (i = 1; i <= 30; i++) { 
              $("#aplica"+i).bootstrapSwitch();
            }
            
          });
        </script>
html;

      $colaborador = AsignarIncentivosDao::getColaboradorById($id);
      // Busca el estatus del periodo con el que se esta haciendo la busqueda
      $getStatusPeriodo = AsignarIncentivosDao::getStatusPeriodo($idPeriodo);

      foreach ($colaborador as $key => $value) {
        $nombre = $value['nombre'] . " " . $value['apellido_paterno'] . " " . $value['apellido_materno'];
        $pago = $value['pago'];
        $catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];
      }

      $busqueda = "";
      foreach ($getStatusPeriodo as $key => $value) {
        $statusPeriodo = $value['status'];
        $mensaje  = ($value['status'] == 0) ? " de proceso":" terminado";
        $class  = ($value['status'] == 0) ? "success":"default";
        $display  = ($value['status'] == 0) ? "":"display:none;";
        $busqueda = "<h2>{$value['fecha_inicio']} - {$value['fecha_fin']} </h2>
                      <p> <b>Este periodo esta en estatus {$mensaje}</b></p>";
      }

      
      $periodo1 = strtoupper($pago);
      foreach (AsignarIncentivosDao::getPeriodos($periodo) as $key => $value) {
        $select = ($value['prorrateo_periodo_id']== $idPeriodo) ? "selected" : "";
        $mensaje  = ($value['status'] == 0) ? " de proceso":" terminado";
        
        $periodo1 .=<<<html
          <option {$select} value="{$value['prorrateo_periodo_id']}">{$value['fecha_inicio']} - {$value['fecha_fin']} : Este periodo esta en estatus {$mensaje}</option>
html;
      }


      $tabla = "";
      $contador = 0;
      foreach (AsignarIncentivosDao::getIncentivosColaborador($id) as $key => $value) {
        $contador++;
        $asignadoSistema = ($value['fijo']=="no")?"display: none;":"";
        $checked = ($value['fijo']=="si")?"checked":"";
        $disable = ($value['fijo']=="si")?"disabled":"";
        $input =<<<html
          <input type="hidden" name="aplica{$contador}" checked value="si">
html;
        $input = ($value['fijo']=="si")?$input:"";

        $tabla .=<<<html
          <tr>
            <th style="text-align:left; vertical-align:middle;">{$value['nombre']}:</th>  
            <td style="text-align:center; vertical-align:middle;">$ {$value['cantidad']}</td>
            <td style="text-align:center; vertical-align:middle;">
              <div class="form-group">
                <label class="control-label" for="nombre">Aplica<span class="required"></span></label ><br>
                <input type="hidden" name="incentivo{$contador}" checked value="{$value['nombre']}">
html;
        if($statusPeriodo == 0){

          $a = <<<html
            <input type="hidden" name="aplica{$contador}" checked value="si">
html;
          $b = <<<html
            <input type="hidden" name="aplica{$contador}" checked value="{$value['nombre']}">
html;

          $arrayName = array('aplica' => $a, 'incentivo' => $b);
          $tabla .=<<<html
                <input name="aplica{$contador}" id="aplica{$contador}" type="checkbox" name="my-checkbox" data-on-text="SI" data-off-text="NO" {$checked} {$disable} ><br>
                {$array}
html;
        }

        if($statusPeriodo == 1){
          $tabla .=<<<html
                <input name="aplica{$contador}" id="aplica{$contador}" type="checkbox" name="my-checkbox" data-on-text="SI" data-off-text="NO" {$checked} {$disable} disabled><br>
                {$input}
html;
        }

        $tabla .=<<<html
              </div>
            </td>
            <td style="text-align:center; vertical-align:middle; width:30%;">
              <p style="{$asignadoSistema}"> Este incentivo es asignado por el sistema</p>
            </td>
            
          </tr>
html;
      }

      View::set('periodo',$busqueda);
      View::set('tabla',$tabla);
      View::set('colaborador',$nombre);
      View::set('pago',$pago);
      View::set('class',$class);
      View::set('display',$display);
      View::set('colaborador_id',$id);
      View::set('periodo_id',$idPeriodo);
      View::set('header',$this->_contenedor->header($extraHeader));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("asignar_incentivos_show");
    }

    public function incentivo(){

      $extraFooter = <<<html
        <script>
          $(document).ready(function(){
          
            for (i = 1; i <= 30; i++) { 
              $("#existente"+i).bootstrapSwitch();
            }
            
          });
        </script>
html;
      $id = MasterDom::getData('catalogo_colaboradores_id');
      $colaborador = AsignarIncentivosDao::getColaboradorById($id);
      foreach ($colaborador as $key => $value) {
        $nombre = $value['nombre'] . " " . $value['apellido_paterno'] . " " . $value['apellido_materno'];
        $pago = $value['pago'];
        $catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];
      }

      
      $periodo = strtoupper($pago);
      foreach (AsignarIncentivosDao::getPeriodos($periodo) as $key => $value) {
        $select = ($value['status']==0) ? "selected" : "";
        $periodo .=<<<html
          <option {$select} value="{$value['prorrateo_periodo_id']}">{$value['fecha_inicio']} - {$value['fecha_fin']}</option>
html;
      }

      $tabla = "";
      $contador = 0;
      foreach (AsignarIncentivosDao::getIncentivosColaborador($id) as $key => $value) {
        $contador++;
        $asignadoSistema = ($value['fijo']=="no")?"display: none;":"";
        $checked = ($value['fijo']=="si")?"checked":"";
        $disable = ($value['fijo']=="si")?"disabled":"";
        $input =<<<html
          <input type="hidden" name="existente{$contador}" checked value="si">
html;
        $input = ($value['fijo']=="si")?$input:"";

        $tabla .=<<<html
          <tr>
            <th style="text-align:left; vertical-align:middle;">{$value['nombre']}:</th>  
            <td style="text-align:center; vertical-align:middle;">$ {$value['cantidad']}</td>
            <td style="text-align:center; vertical-align:middle;">
              <div class="form-group">
                <label class="control-label" for="nombre">Aplica {$contador}<span class="required"></span></label ><br>
                <input type="hidden" name="" >
                <input name="existente{$contador}" id="existente{$contador}" type="checkbox" name="my-checkbox" data-on-text="SI" data-off-text="NO" {$checked} {$disable} ><br>
                {$input}
              </div>
            </td>
            <td style="text-align:center; vertical-align:middle; width:30%;">
              <p style="{$asignadoSistema}"> Este incentivo es asignado por el sistema</p>
            </td>
            
          </tr>
html;
      }

      View::set('periodo',$periodo);
      View::set('tabla',$tabla);
      View::set('colaborador',$nombre);
      View::set('pago',$pago);
      View::set('catalogo_colaboradores_id',$catalogo_colaboradores_id);
      View::set('header',$this->_contenedor->header($extraHeader));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("asignar_incentivos_show");
    }


    public function activos(){

      foreach ($_POST as $key => $value) {
        //print_r($value);
      }

      $this->alerta(MasterDom::getData('colaborador_id'), "add", MasterDom::getData('periodo_id'));
    }


    public function alerta($id, $parametro, $colaborador_id){
      $regreso = "/AsignarIncentivos/incentivos/{$id}/{$colaborador_id}";

      if($parametro == 'add'){
        $mensaje = "Se ha agregado correctamente";
        $class = "success";
      }

      if($parametro == 'edit'){
        $mensaje = "Se ha modificado correctamente";
        $class = "success";
      }

      if($parametro == 'delete'){
        $mensaje = "Se ha eliminado la empresa {$id}, ya que cambiaste el estatus a eliminado";
        $class = "success";
      }

      if($parametro == 'nothing'){
        $mensaje = "Posibles errores: <li>No intentaste actualizar ningún campo</li> <li>Este dato ya esta registrado, comunicate con soporte técnico</li> ";
        $class = "warning";
      }

      if($parametro == 'no_cambios'){
        $mensaje = "No intentaste actualizar ningún campo";
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

      View::set('class',$class);
      View::set('regreso',$regreso);
      View::set('mensaje',$mensaje);
      View::set('header',$this->_contenedor->header($extraHeader));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("alerta");
    }

}
