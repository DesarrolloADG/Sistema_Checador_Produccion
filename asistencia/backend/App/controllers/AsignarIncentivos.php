<?php
namespace App\controllers;
//defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \App\controllers\Contenedor;
use \Core\Controller;
use \App\models\AsignarIncentivos AS AsignarIncentivosDao;
use \App\models\ResumenSemanal AS ResumenSemanalDao;
class AsignarIncentivos extends Controller{

    private $_contenedor;

    function __construct(){
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header',$this->_contenedor->header());
        View::set('footer',$this->_contenedor->footer());
        $usuario = $this->__usuario;

        if(Controller::getPermisosUsuario($this->__usuario, "Asignar_incentivos",1) == 0)
          header('Location: /Principal/');
    }

    public function index() {
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
              "order": false,
              dom: 'Bfrtip',
                      buttons: [
                          'excelHtml5',
                          'csvHtml5',
                          'pdfHtml5'
                      ]
            });

            $(".buttons-copy").addClass('btn btn-default');
            $(".buttons-excel").addClass('btn btn-success');
            $(".buttons-csv").addClass('btn btn-warning');
            $(".buttons-pdf").addClass('btn btn-primary');

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

          $("#export_pdf").click(function(){
              $('#all').attr('action', '/AsignarIncentivos/generarPDF/');
              $('#all').attr('target', '_blank');
              $("#all").submit();
          });
        </script>
html;

      $datosUsuario = AsignarIncentivosDao::getDatosUsuarioLogeado($this->__usuario);
      // USUARIO ROOT ID 1 
      //print_r($datosUsuario);
      $periodo;
      if($_POST['tipo_periodo'] != ""){
        $periodo = AsignarIncentivosDao::getStatusPeriodo(MasterDom::getData('tipo_periodo'));
      }else{
        $periodo = AsignarIncentivosDao::getTipoPeriodoProceso(strtoupper($datosUsuario['tipo']));
        if($periodo == ""){
          if($datosUsuario['tipo'] == "semanal"){
            $periodoSemanal = AsignarIncentivosDao::getPeriodosSemanales();
            $periodo = $periodoSemanal['prorrateo_periodo_id'];
          }

          if($datosUsuario['tipo'] == "quincenal"){
            $periodoQuincenal = AsignarIncentivosDao::getPeriodosQuincenales();
            $periodo = $periodoQuincenal['prorrateo_periodo_id'];
          }

        }
      }


      if($_POST['tipo_periodo'] != ""){
        foreach (AsignarIncentivosDao::getTipoPeriodo(strtoupper($datosUsuario['tipo'])) as $key => $value) {
          $mensaje  = ($value['status'] == 0) ? " de proceso":" terminado";
          $selected  = ($value['prorrateo_periodo_id'] == MasterDom::getData('tipo_periodo')) ? "selected":"";
          $semanal .=<<<html
            <option {$selected} value="{$value['prorrateo_periodo_id']}">Periodo: {$value['fecha_inicio']} - {$value['fecha_fin']} : Este periodo esta en estatus {$mensaje}</option>
html;
        }
      }else{
        foreach (AsignarIncentivosDao::getTipoPeriodo(strtoupper($datosUsuario['tipo'])) as $key => $value) {
          $mensaje  = ($value['status'] == 0) ? " de proceso":" terminado";
          $selected  = ($value['status'] == 0) ? "selected":"";
          $semanal .=<<<html
            <option value="{$value['prorrateo_periodo_id']}">Periodo: {$value['fecha_inicio']} - {$value['fecha_fin']} : Este periodo esta en estatus {$mensaje}</option>
html;
        }
      }

      if($datosUsuario['usuario'] == "root")
        $secciones = AsignarIncentivosDao::getAllDepartamentos();
      else
        $secciones = AsignarIncentivosDao::getDepartamentos($datosUsuario['administrador_id']);
        

      $tabla = '';
      foreach ($secciones as $key1 => $value1) {
        foreach (AsignarIncentivosDao::getAllColaboradores($value1['catalogo_departamento_id'], $datosUsuario['nombre_planta'], $datosUsuario['perfil_id']) as $key => $value) {
          $tabla .=<<<html
            <tr>
              <td style="text-align:center; vertical-align:middle;"><img class="foto" src="/img/colaboradores/{$value['foto']}"/></td>
              <td style="text-align:center; vertical-align:middle;">{$value['nombre']} {$value['apellido_paterno']} {$value['apellido_materno']}</td>
              <td style="text-align:center; vertical-align:middle;">{$value['nombre_departamento']}</td>
              <td style="text-align:center; vertical-align:middle;">{$value['pago']}</td>
              <td style="text-align:center; vertical-align:middle;">
html;
          foreach (AsignarIncentivosDao::getIncentivosColaboradores($value['catalogo_colaboradores_id']) as $k => $val) {

            $tabla .=<<<html
              <p>{$val['nombre']}: $ {$val['cantidad']}</p>
html;
          }
          $tabla .=<<<html
              </td>
              <td style="text-align:center; vertical-align:middle;">
html;
            if($_POST['tipo_periodo'] != ""){
              $prorrateo_periodo_id = MasterDom::getData('tipo_periodo');
            }else{
              $prorrateo_periodo_id = $periodo['prorrateo_periodo_id'];
            }

            $tabla .=<<<html
                <a href="/AsignarIncentivos/incentivos/{$value['catalogo_colaboradores_id']}/{$prorrateo_periodo_id}/" type="submit" name="id_empresa" class="btn btn-primary"><i class="fa fa-plus"></i> </a>
html;
          $tabla .=<<<html
              </td>
            </tr>
html;
        }
      }

      $datosPeriodo1 = AsignarIncentivosDao::getPeriodoBuscar($prorrateo_periodo_id);
      $msjbackground = ($datosPeriodo1['status']==0)?"#73c4a6":"#e46360";
      $msj = ($datosPeriodo1['status']==0)?" Abierto":" Cerrado";

      $textoPeriodo .=<<<html
        <p style="color:white; font-weight:bolder;">El periodo es de {$datosPeriodo1['fecha_inicio']} al {$datosPeriodo1['fecha_fin']}, periodo {$msj} </p>
html;

      View::set('msjbackground',$msjbackground);
      View::set('tabla',$tabla);
      View::set('textoPeriodo',$textoPeriodo);
      View::set('textoSinPeriodo',$textoSinPeriodo);
      View::set('quincenal',$quincenal);
      View::set('semanal',$semanal);
      View::set('periodo',$periodo);
      View::set('fechaPeriodo',$fechaPeriodo);
      View::set('datosUsuario',$datosUsuario);
      View::set('header',$this->_contenedor->header($extraHeader));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("asignar_incentivos_all");
    }

    public function incentivosSemanales() {
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
              "order": false,
              dom: 'Bfrtip',
                      buttons: [
                          'excelHtml5',
                          'csvHtml5',
                          'pdfHtml5'
                      ]
            });

            $(".buttons-copy").addClass('btn btn-default');
            $(".buttons-excel").addClass('btn btn-success');
            $(".buttons-csv").addClass('btn btn-warning');
            $(".buttons-pdf").addClass('btn btn-primary');

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

          $("#export_pdf").click(function(){
              $('#all').attr('action', '/AsignarIncentivos/generarPDF/');
              $('#all').attr('target', '_blank');
              $("#all").submit();
          });
        </script>
html;
      $periodoAbierto = " el periodo esta abierto";
      $periodoCerrado = " el periodo esta cerrado";
      $periodoID;
      if(empty($_POST)){
        $periodo = AsignarIncentivosDao::getLastPeriodoSemanal();
        $periodoID = $periodo['prorrateo_periodo_id'];
        $statusPeriodo = ($periodo['status'] == 0) ? $periodoAbierto : $periodoCerrado;
        $msjbackground = ($periodo['status'] == 0) ? "#26B99A;" :"#EA6153;";
        $textoPeriodo =<<<html
        <h3 style="color: #FFFFFF;">Periodo: {$periodo['fecha_inicio']} al {$periodo['fecha_fin']}, {$statusPeriodo}</h3>
html;
        // MUESTRA LA OPCION DEL SELECT de periodo  
        $semanales = "";
        foreach (AsignarIncentivosDao::getPeriodos('SEMANAL') as $key => $value) {
          $msj = ($value['status'] == 0) ? $periodoAbierto:$periodoCerrado;
          $semanales .= <<<html
          <option value="{$value['prorrateo_periodo_id']}">Periodo: {$value['fecha_inicio']} al {$value['fecha_fin']}, {$msj}</option>
html;
        }
      }else{
        $semanales = "";
        foreach (AsignarIncentivosDao::getPeriodos('SEMANAL') as $key => $value) {
          $msj = ($value['status'] == 0) ? $periodoAbierto:$periodoCerrado;
          $selected = ($value['prorrateo_periodo_id'] == MasterDom::getData('tipo_periodo')) ? "selected":"";
          $semanales .= <<<html
          <option {$selected} value="{$value['prorrateo_periodo_id']}">Periodo: {$value['fecha_inicio']} al {$value['fecha_fin']}, {$msj}</option>
html;
        }
        // Se obtiene la el periodo que se manda a buscar en el select
        $periodo = AsignarIncentivosDao::getPeriodo("SEMANAL",MasterDom::getData('tipo_periodo'));
        $periodoID = $periodo['prorrateo_periodo_id'];
        $statusPeriodo = ($periodo['status'] == 0) ? $periodoAbierto : $periodoCerrado;
        $msjbackground = ($periodo['status'] == 0) ? "#26B99A;" :"#EA6153;";
        $textoPeriodo =<<<html
        <h3 style="color: #FFFFFF;">Periodo: {$periodo['fecha_inicio']} al {$periodo['fecha_fin']}, {$statusPeriodo}</h3>
html;
      }

      echo $this->getTablaIncentivos("Semanal", $periodoID, 2);
      View::set('msjbackground',$msjbackground);
      View::set('textoPeriodo',$textoPeriodo);
      View::set('semanal',$semanales);
      View::set('semanales',"Semanales");
      View::set('header',$this->_contenedor->header($extraHeader));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::set('periodoSemanal',2);
      View::render("asignar_incentivos_all");
    }

        public function incentivosQuincenales() {
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
              "order": false,
              dom: 'Bfrtip',
                      buttons: [
                          'excelHtml5',
                          'csvHtml5',
                          'pdfHtml5'
                      ]
            });

            $(".buttons-copy").addClass('btn btn-default');
            $(".buttons-excel").addClass('btn btn-success');
            $(".buttons-csv").addClass('btn btn-warning');
            $(".buttons-pdf").addClass('btn btn-primary');

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

          $("#export_pdf").click(function(){
              $('#all').attr('action', '/AsignarIncentivos/generarPDF/');
              $('#all').attr('target', '_blank');
              $("#all").submit();
          });
        </script>
html;
      $periodoAbierto = " el periodo esta abierto";
      $periodoCerrado = " el periodo esta cerrado";

      $periodoID;
      if(empty($_POST)){
        $periodo = AsignarIncentivosDao::getLastPeriodoQuincenal();
        $periodoID = $periodo['prorrateo_periodo_id'];
        $statusPeriodo = ($periodo['status'] == 0) ? $periodoAbierto : $periodoCerrado;
        $msjbackground = ($periodo['status'] == 0) ? "#26B99A;" :"#EA6153;";
        $textoPeriodo =<<<html
        <h3 style="color: #FFFFFF;">Periodo: {$periodo['fecha_inicio']} al {$periodo['fecha_fin']}, {$statusPeriodo}</h3>
html;
        // MUESTRA LA OPCION DEL SELECT de periodo  
        $semanales = "";
        foreach (AsignarIncentivosDao::getPeriodos('QUINCENAL') as $key => $value) {
          $msj = ($value['status'] == 0) ? $periodoAbierto:$periodoCerrado;
          $semanales .= <<<html
          <option value="{$value['prorrateo_periodo_id']}">Periodo: {$value['fecha_inicio']} al {$value['fecha_fin']}, {$msj}</option>
html;
        }
      }else{
        $semanales = "";
        foreach (AsignarIncentivosDao::getPeriodos('QUINCENAL') as $key => $value) {
          $msj = ($value['status'] == 0) ? $periodoAbierto:$periodoCerrado;
          $selected = ($value['prorrateo_periodo_id'] == MasterDom::getData('tipo_periodo')) ? "selected":"";
          $semanales .= <<<html
          <option {$selected} value="{$value['prorrateo_periodo_id']}">Periodo: {$value['fecha_inicio']} al {$value['fecha_fin']}, {$msj}</option>
html;
        }
        // Se obtiene la el periodo que se manda a buscar en el select
        $periodo = AsignarIncentivosDao::getPeriodo("QUINCENAL",MasterDom::getData('tipo_periodo'));
        $periodoID = $periodo['prorrateo_periodo_id'];
        $statusPeriodo = ($periodo['status'] == 0) ? $periodoAbierto : $periodoCerrado;
        $msjbackground = ($periodo['status'] == 0) ? "#26B99A;" :"#EA6153;";
        $textoPeriodo =<<<html
        <h3 style="color: #FFFFFF;">Periodo: {$periodo['fecha_inicio']} al {$periodo['fecha_fin']}, {$statusPeriodo}</h3>
html;
      }

      echo $this->getTablaIncentivos("Quincenal", $periodoID, 3);
      View::set('msjbackground',$msjbackground);
      View::set('textoPeriodo',$textoPeriodo);
      View::set('semanal',$semanales);
      View::set('semanales',"Semanales");
      View::set('header',$this->_contenedor->header($extraHeader));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::set('periodoSemanal',3);
      View::render("asignar_incentivos_all");
    }

    // $periodo = "ES SEMANAL o QUINCENAL"
    // $periodoID = "El ID del periodo"
    // $seccion = redireccionamiento
    public function getTablaIncentivos($periodo, $periodoID, $seccion){
      $datosUsuario = AsignarIncentivosDao::getDatosUsuarioLogeado($this->__usuario);
        

      $tabla="";
      foreach (AsignarIncentivosDao::getAllColaboradoresRH($periodo, $datosUsuario['nombre_planta']) as $key => $value) {
        $tabla.=<<<html
        <tr>
          <td style="text-align:center; vertical-align:middle;"><img class="foto" src="/img/colaboradores/{$value['foto']}"/></td>
          <td style="text-align:center; vertical-align:middle;">{$value['nombre']} {$value['apellido_paterno']} {$value['apellido_materno']}</td>
          <td style="text-align:center; vertical-align:middle;">{$value['nombre_departamento']}</td>
          <td style="text-align:center; vertical-align:middle;">{$value['pago']}</td>
          <td style="text-align:center; vertical-align:middle;">
html;
        foreach (AsignarIncentivosDao::getIncentivosColaboradores($value['catalogo_colaboradores_id']) as $k => $val) {
          $tabla.=<<<html
          <p>{$val['nombre']}: {$val['cantidad']}</p>
html;
        }
        $tabla.=<<<html
          </td>
          <td style="text-align:center; vertical-align:middle;">
          <a href="/AsignarIncentivos/incentivos/{$value['catalogo_colaboradores_id']}/{$periodoID}/{$seccion}" type="submit" name="id_empresa" class="btn btn-primary"><i class="fa fa-plus"></i> </a>
          </td>
        </tr>
html;
      }
      View::set('tabla',$tabla);
    }

    // Agregar un nuevo incentivo
    public function add($id, $idPeriodo,$seccion){
      $extraHeader=<<<html
html;
      $extraFooter=<<<html
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
                    $('#tabla_incentivos_borrar').attr('target', '');
                    $('#tabla_incentivos_borrar').attr('action', '/AsignarIncentivos/delete');
                    $("#tabla_incentivos_borrar").submit();
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
      echo $this->getIncentivosAsigandos($id,$idPeriodo);
      echo $this->getInformacionColaborador($id);
      echo $this->getIncentivosAsignadosColaborador($id,$idPeriodo, $seccion);
      echo $this->getPeriodo($idPeriodo);
      echo $this->getRedireccion($id, $idPeriodo, $seccion);

      View::set('header',$this->_contenedor->header($extraHeader));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render('asignar_incentivo_add');
    }

    public function getIncentivosAsigandos($idColaborador, $idPeriodo){
      $tabla = "";
      $incentivoAsignador = AsignarIncentivosDao::getIncentivosColaborador($idColaborador, $idPeriodo);
      foreach ($incentivoAsignador as $key => $value) {
        $aplica = ($value['valido'] >= 1) ? "Aplica para <br>este periodo" : "Aplica despues";
        $asignado = ($value['asignado'] == 1) ? "asignado" : "no asignado";
        $msjSistema = ($value['fijo'] == "si") ? "Este Incentivo es <br> generado por el sistema " : "";
        $tabla .=<<<html
          <tr>
            <td style="text-align:center; vertical-align:middle;"><input type="checkbox" name="borrar[]" value="{$value['incentivos_asignados_id']}"/> </td>
            <td style="text-align:center; vertical-align:middle;">{$value['nombre']}</td>
            <td style="text-align:center; vertical-align:middle;">{$value['cantidad']}</td>
            <td style="text-align:center; vertical-align:middle;">{$value['tipo']}</td>
            <td style="text-align:center; vertical-align:middle;">{$aplica}</td>
            <td style="text-align:center; vertical-align:middle;">{$asignado}</td>
            <td style="text-align:center; vertical-align:middle;">{$msjSistema}</td>
          </tr>
html;
      }

      $btnAddIncentivos = (count($incentivoAsignador)>0) ? "display:none" : "";
      View::set('btnAddIncentivos',$btnAddIncentivos);
      View::set('cantidadIncentivos',count($incentivoAsignador));
      View::set('tabla',$tabla);
    }

    public function getInformacionColaborador($id){
      $colaborador = AsignarIncentivosDao::getColaborador($id);
      View::set('colaborador',$colaborador);
    }

    public function getRedireccion($idColaborador, $idPeriodo, $seccion){
      $redireccionamiento = <<<html
        <form action="/{$seccion}/" method="POST">
          <input type="hidden" name="colaborador_id" value="<?php echo $colabordor_id ?>">
          <input type="hidden" name="prorrateo_periodo_id" value="<?php echo $prorrateo_periodo_id ?>">
          <span class="btn btn-info"><span class="glyphicon glyphicon-chevron-left" style="color:white;" style="color:white;"></span><input class="" style="background:none; border: none; color:white;" type="submit" value="Regresar {$seccion}" ></span>
        </form>
html;
      View::set('redireccionamiento',$redireccionamiento);
    }

    public function getIncentivosAsignadosColaborador($id,$idPeriodo,$seccion){
      $getIncentivos = AsignarIncentivosDao::getIncentivosAsignadosColaborador($id);
      $repite = strtoupper($value['repetitivo']);
      $listaIncentivos ="";
      $datosPeriodoStatus = AsignarIncentivosDao::getPeriodoBuscar($idPeriodo);

      $datosPeriodo = AsignarIncentivosDao::getPeriodoBuscar($idPeriodo);
      $displayLista = ($datosPeriodo['status']==0)?"":"display:none";

      // Buscara la cantidad de elementos en la tabla general para saber si mostrar la obcion del menu lateral(buton agregar)
      $displayItemMenuLeft = (count(AsignarIncentivosDao::getIncentivosColaborador($id, $idPeriodo)) > 0) ? "":"display:none;";

      foreach ($getIncentivos as $key => $value) {
        $repite = ($value['repetitivo'] == "si") ? "":"display:none";
        $listaIncentivos .=<<<html
          <li id="row">
            <div class="block">
              <div style="{$displayItemMenuLeft}">
                <div class="tags" style="{$displayLista}">
                  <a style="{$repite}" href="/AsignarIncentivos/asigarnarIncentivo/{$id}/{$idPeriodo}/{$value['catalogo_incentivo_id']}/{$value['cantidad']}/{$value['tipo']}/{$seccion}" class="tag">
                    Agregar
                  </a>
                </div>
              </div>
              <div class="block_content">
                <h2 class="title"><a>Nombre: <b>{$value['nombre']}</small></a></h2>
                <div class="byline">
                  <span>Tipo:</span> <b>{$value['tipo']} </b> -
                  <span>Se duplica:</span> <b>"{$value['repetitivo']}"</b>
                </div>
                <span class="excerpt"><span class="title"><a>Cantidad $ {$value['cantidad']}</small></a></span></span>
                <p class="excerpt">Descripcion: {$value['descripcion']} </p>
                <input type="hidden" name="colaborador_id" value="{$id}">
                <input type="hidden" name="prorrateo_periodo_id" value="{$idPeriodo}">
                <input type="hidden" name="catalogo_incentivo_id" value="{$value['catalogo_incentivo_id']}">
                <input type="hidden" name="cantidad" value="{$value['cantidad']}">
              </div>

            </div>
          </li>
html;
      }
      View::set('colabordor_id',$id);
      View::set('prorrateo_periodo_id',$idPeriodo);
      View::set('listaIncentivos',$listaIncentivos);
    }

    public function checarSiAplicaIncentivo(){
      $idColaborador = MasterDom::getData('colaborador_id');
      $periodoSolicitadoId = MasterDom::getData('prorrateo_periodo_id');

      $periodo = ResumenSemanalDao::getPeriodoById($periodoSolicitadoId);
      $administrador = ResumenSemanalDao::getDatosUsuarioLogeado(MasterDom::getSesion('usuario'));
      $datosColaborador = ResumenSemanalDao::getColaboradoresPago(ucwords(strtolower($periodo['tipo'])), $idColaborador);

      $contadorFaltas = $this->getStatusAsistenciasColaborador($idColaborador, $periodoSolicitadoId);


      foreach ($datosColaborador as $key => $value) {
        //print_r($value);
        //print_r($value);
        $nombreCompleto = ucwords(strtolower($value['nombre']))." ".ucwords(strtolower($value['apellido_paterno']))." ".ucwords(strtolower($value['apellido_materno']));
        //if($contadorFaltas>0){
        //  $class="warning";
        //  $mensaje="No se le agregaran incentivos ya que {$nombreCompleto}, cuenta con {$contadorFaltas} faltas";
        //}else{
          // Agregar los incentivos para el colaborador cuando cumple con el horario sin generar alguna falta.

          $data = new \stdClass();
          $data->_catalogo_colaboradores_id = $idColaborador;
          $data->_prorrateo_periodo_id = $periodoSolicitadoId;

          // Obtener todos los incentivos para el colaborador
          $incentivos = AsignarIncentivosDao::getIncentivosAsignadosColaborador($idColaborador);


          foreach ($incentivos as $key => $value) {


            $data->_colaborador_id = $idColaborador;
            $data->_prorrateo_periodo_id = $periodoSolicitadoId;
            $data->_catalogo_incentivo_id = $value['catalogo_incentivo_id'];
            $data->_cantidad = $value['cantidad'];

            if($value['tipo'] == "semanal"){
              $data->_asignado = 1;
              $data->_valido = 1;
            }

            if($value['tipo'] == "quincenal"){
              $data->_asignado = 1;
              $data->_valido = 1;
            }

            if($value['tipo'] == "mensual"){
              $data->_asignado = 1;
              $data->_valido = 2;
            }

            if($value['fijo'] == "si"){

              if($contadorFaltas>0){
                $data->_asignado = 0;
                $data->_valido = 3;
                $agregarIncentivosColaborador = AsignarIncentivosDao::addIncentivoColaborador($data);
              }

            }else{
              $agregarIncentivosColaborador = AsignarIncentivosDao::addIncentivoColaborador($data);
            }

            //$agregarIncentivosColaborador = AsignarIncentivosDao::addIncentivoColaborador($data);

          }
          $class="success";
          $mensaje="Los incentivos han sido agregados para el colaborador {$nombreCompleto}. Cantidad de faltas {$contadorFaltas}";
        //}
      }

        View::set('class',$class);
        View::set('mensaje',$mensaje);
        View::set('regreso',"/AsignarIncentivos/add/{$idColaborador}/{$periodoSolicitadoId}");
        View::set('header',$this->_contenedor->header($extraHeader));
        View::set('footer',$this->_contenedor->footer($extraFooter));
        View::render("alerta");
    }


    /////////////////////////////////
    ///////////////////////////////
    ////////////////////////////

    public function incentivos($idColaborador, $idPeriodo, $back){
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
                    $('#tabla_incentivos_borrar').attr('action', '/AsignarIncentivos/delete');
                    $("#tabla_incentivos_borrar").submit();
                    alertify.success("Se ha eliminado correctamente");
                  }
                });
              }else{
                alertify.confirm('Selecciona al menos uno para eliminar');
              }
            });


          $("#existente").bootstrapSwitch();
          $("#lista").bootstrapSwitch();
          $("#asignados").bootstrapSwitch();
          $("#eliminar-incentivos").bootstrapSwitch();
          $("#agregar-horas-extra").bootstrapSwitch();
          $("#update-horas-extra").bootstrapSwitch();
          $("#update-btn-horas-extra").hide();

          $(".switch").bootstrapSwitch();

          $('input[name="lista"]').on('switchChange.bootstrapSwitch', function(event, state) {
            if(state){
              //$("#asignados").click();
              $("#tabla_incentivos_asignados").hide();
              $("#eliminar-btn-incentivos").hide();
              $("#tabla_incentivos_para_asignar").show();
              $("#tabla_incentivos_borrar").hide();
              $("input[type=radio]").attr('checked', false);
              $("#asignados-btn-incentivos").hide();
            }else{
              $("#asignados-btn-incentivos").show();
              $("#tabla_incentivos_para_asignar").hide();
              $("#tabla_incentivos_asignados").show();
              //$("#asignados").click();
              $("#asignados").removeClass('bootstrap-switch-on');
              $("#asignados").addClass('bootstrap-switch-off');
            }
          });

          $('input[name="asignados"]').on('switchChange.bootstrapSwitch', function(event, state) {
            if(state){
              $("#lista").removeClass('bootstrap-switch-on');
              $("#lista").addClass('bootstrap-switch-off');
              $("#tabla_incentivos_borrar").hide();
              $("#tabla_incentivos_asignados").show();
              $("#tabla_incentivos_para_asignar").hide();
              $("#eliminar-btn-incentivos").show();
              $("input[type=radio]").attr('checked', false);
            }else{
              $("#eliminar-btn-incentivos").hide();
              $("#tabla_incentivos_para_asignar").show();
              $("#lista").click();
              $("#lista").removeClass('bootstrap-switch-on');
              $("#lista").addClass('bootstrap-switch-off');
            }
          });

          $('input[name="eliminar-incentivos"]').on('switchChange.bootstrapSwitch', function(event, state) {
            if(state){
              $("#tabla_incentivos_borrar").show();
              $("#tabla_incentivos_asignados").hide();
              $("#tabla_incentivos_para_asignar").hide();
              //$("#asignados-btn-incentivos").hide();
              //$("#asignados").click();
              $("input[type=radio]").attr('checked', false);
            }else{
              $("#asignados-btn-incentivos").show();
              //$("#eliminar-incentivos").click();
              $("#tabla_incentivos_asignados").show();
              $("#tabla_incentivos_para_asignar").hide();
              $("#tabla_incentivos_borrar").hide();
            }
          });

          $('input[name="agregar-horas-extra"]').on('switchChange.bootstrapSwitch', function(event, state) {
            if(state){
              $("#form-eliminar-btn-incentivos").show();
            }else{
              $("#form-eliminar-btn-incentivos").hide();
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

          $('#horas_extra').on('change',function(){ 
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

        });

      </script>
html;

      $horasExtra = "";
      for ($i=1; $i < 15; $i++) { 
        if($i == 0){
          $horasExtra .=<<<html
          <option value="{$i}">{$i} hora</option>
html;
        }else{
          $horasExtra .=<<<html
          <option value="{$i}">{$i} horas</option>
html;
        }
      }

      $horas = AsignarIncentivosDao::getHorasExtraPeriodo($idColaborador, $idPeriodo);
      

      $datosAdmin = AsignarIncentivosDao::getDatosUsuarioLogeado($this->__usuario);
      $colaborador = AsignarIncentivosDao::getColaborador($idColaborador);
      $countIncentivos = AsignarIncentivosDao::getIncentivosColaboradorAsignados($idColaborador,$idPeriodo);

      $SiHayIncentivos = (count($countIncentivos) > 0 ) ? "display:none":"";
      $NoHayIncentivos = (count($countIncentivos) == 0 ) ? "display:none":"";
      $accionSinIncentivos = (count($countIncentivos) == 0 ) ? "selected":"";
      $accionConIncentivos = (count($countIncentivos) > 0 ) ? "selected":"";



      echo $this->getPeriodo($idPeriodo);
      echo $this->getIncentivosIniciales($idColaborador, $idPeriodo);
      echo $this->getIncentivosAsignadosPeriodo($idColaborador, $idPeriodo);
      echo $this->getIncentivosBorrar($idColaborador, $idPeriodo);
      View::set('prorrateo_periodo_id',$idPeriodo);
      View::set('colaborador_id',$idColaborador);
      View::set('hayIncentivosAsignados',$SiHayIncentivos);
      View::set('NohayIncentivosAsignados',$NoHayIncentivos);
      View::set('countIncentivos',count($countIncentivos));
      View::set('horasExtra',$horasExtra);

      if(count($countIncentivos)>0){
        $extraFooter.=<<<html
        <script>
        $(document).ready(function(){
            $("#tabla_incentivos_para_asignar").hide();
            $("#tabla_incentivos_asignados").show();
            $("#tabla_incentivos_borrar").hide();
            $("#form-eliminar-btn-incentivos").hide();
        });
        </script>
html;
      }

      if(count($countIncentivos) == 0){
        $extraFooter.=<<<html
        <script>
        $(document).ready(function(){
          $("#tabla_incentivos_para_asignar").show();
          $("#tabla_incentivos_asignados").hide();
          $("#tabla_incentivos_borrar").hide();
          $("#form-eliminar-btn-incentivos").hide();
        });
        </script>
html;
      }

      if($horas['horas_extra']>0){
        $extraFooter.=<<<html
        <script>
        $(document).ready(function(){
          $("#horas-extra").hide();
        });
        </script>
html;
      }

      if($horas['horas_extra']==0){
        $extraFooter.=<<<html
        <script>
        $(document).ready(function(){
          $("#horas-extra").show();
        });
        </script>
html;
      }


      if($horas['horas_extra']>0){
        $h = $horas['horas_extra'] . " Horas extra";
        $displaySelectHE = "";

        $horasExtraUpdate = "";
        for ($i=0; $i < 15; $i++) { 
          $selected = ($horas['horas_extra'] == $i) ? "selected":"";
          if($i == 1){
            $horasExtraUpdate .=<<<html
            <option {$selected} value="{$i}">{$i} hora</option>
html;
          }else{
            $horasExtraUpdate .=<<<html
            <option {$selected} value="{$i}">{$i} horas</option>
html;
          }
        }
      }else{
        $h = "0 Horas extra";
        $displaySelectHE = "display:none";
      }

      

      $permisosGlobales = AsignarIncentivosDao::getPermisosGlobales($this->__usuario);
      $he = ($banderaHorasExtra > 0) ? "<br>" : "display:none;";
      View::set('existenHorasExtra', $he);
      View::set('permisosGlobales',$permisosGlobales);
      View::set('colaborador',$colaborador);
      View::set('datosAdmin',$datosAdmin);
      View::set('accionSinIncentivos',$accionSinIncentivos);
      View::set('accionConIncentivos',$accionConIncentivos);
      View::set('horas',$h);
      View::set('displaySelectHE',$displaySelectHE);
      View::set('horasExtraUpdate',$horasExtraUpdate);


      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::set('back',$back);
      View::render('asignar_incentivos');
    }



    public function agregarHorasExtraColaborador(){
      $horasExtra = new \stdClass();
      $horasExtra->_catalogo_colaboradores_id = MasterDom::getData('colaborador_id');
      $horasExtra->_prorrateo_periodo_id = MasterDom::getData('prorrateo_periodo_id');
      $horasExtra->_horas_extra = MasterDom::getData('horas_extra');
      $insertHorasExtra = AsignarIncentivosDao::insertHorasExtras($horasExtra);
    }

    public function agregarHorasExtra(){
      $horasExtra = new \stdClass();
      $horasExtra->_catalogo_colaboradores_id = MasterDom::getData('colaborador_id');
      $horasExtra->_horas_extra = MasterDom::getData('horas_extra');
      $horasExtra->_prorrateo_periodo_id = MasterDom::getData('prorrateo_periodo_id');
      $insertHorasExtra = AsignarIncentivosDao::insertarHorasExtras($horasExtra);

      if($horasExtra>0)
          $this->alerta(MasterDom::getData('colaborador_id'), "add-horas-extra", MasterDom::getData('prorrateo_periodo_id'), MasterDom::getData('seccion'));
        else
          $this->alerta(MasterDom::getData('colaborador_id'), "error", MasterDom::getData('prorrateo_periodo_id'), MasterDom::getData('seccion'));

    }

     public function updateHorasExtra(){
      
      if(MasterDom::getData('horas_extra') == 0){
        $delete = AsignarIncentivosDao::deleteHorasExtra(MasterDom::getData('colaborador_id'), MasterDom::getData('prorrateo_periodo_id'));

        if($delete>0)
          $this->alerta(MasterDom::getData('colaborador_id'),"delete-horas-extra",MasterDom::getData('prorrateo_periodo_id'),MasterDom::getData('seccion'));
        else
          $this->alerta(MasterDom::getData('colaborador_id'), "error", MasterDom::getData('prorrateo_periodo_id'), MasterDom::getData('seccion'));
      }else{
        $id = AsignarIncentivosDao::updateHorasExtra(MasterDom::getData('horas_extra'), MasterDom::getData('colaborador_id'), MasterDom::getData('prorrateo_periodo_id'));

        if($id>0)
          $this->alerta(MasterDom::getData('colaborador_id'),"update-horas-extra",MasterDom::getData('prorrateo_periodo_id'),MasterDom::getData('seccion'));
        else
          $this->alerta(MasterDom::getData('colaborador_id'), "error", MasterDom::getData('prorrateo_periodo_id'), MasterDom::getData('seccion'));
  
      }

      exit;
      


    }


    public function getIncentivosIniciales($idColaborador, $idPeriodo){

      $tabla = "";
      $incentivoAsignador = AsignarIncentivosDao::getIncentivoColaborador($idColaborador);

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
                <td style="text-align:center; vertical-align:middle;">{$aplicaIncentivoSistema}</td>
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

    public function getIncentivosAsignadosPeriodo($idColaborador,$idPeriodo){
      $tablaAsignados = "";
      $incentivosAsignados = AsignarIncentivosDao::getIncentivosColaboradorAsignados($idColaborador,$idPeriodo);
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
                <td style="text-align:center; vertical-align:middle;">{$aplicaIncentivoSistema}</td>
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
      $incentivosAsignados = AsignarIncentivosDao::getIncentivosColaboradorAsignados($idColaborador,$idPeriodo);
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

    public function getStatusAsistenciasColaborador($idColaborador, $periodoSolicitadoId){
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
            <td>
              <span class="btn btn-success"><label style="color: {$color};"> {$llegada} </label></span>
            </td>
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

    public function agregarIncentivos(){
      if(count($_POST)<=3){
        $this->alerta(MasterDom::getData('colaborador_id'), "vacio", MasterDom::getData('prorrateo_periodo_id'), MasterDom::getData('seccion'));
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
            $insertarIncentivo = AsignarIncentivosDao::insertIncentivos($incentivo);

        }

        if($insertarIncentivo>0)
          $this->alerta(MasterDom::getData('colaborador_id'), "delete", MasterDom::getData('prorrateo_periodo_id'), MasterDom::getData('seccion'));
        else
          $this->alerta(MasterDom::getData('colaborador_id'), "error", MasterDom::getData('prorrateo_periodo_id'), MasterDom::getData('seccion'));


      }

    }

    public function updateIncentivos(){
      // ELIMINAR INCENTIVOS QUE PREVIAMENTE HAN SIDO ASIGNADOS
      $eliminarIncentivos = new \stdClass();
      $eliminarIncentivos->_colaborador_id = MasterDom::getData('colaborador_id');
      $eliminarIncentivos->_prorrateo_periodo_id = MasterDom::getData('prorrateo_periodo_id');
      $eliminar = AsignarIncentivosDao::eliminarIncentivos($eliminarIncentivos);

      // ELIMINAR TODOS LOS INCENTIVOS
      if(empty($_POST))
        $this->alerta(MasterDom::getData('colaborador_id'), "vacio", MasterDom::getData('prorrateo_periodo_id'), MasterDom::getData('seccion'));
        $incentivo = new \stdClass();
        foreach (MasterDom::getDataAll('agregar') as $key => $value) {
          $explode = explode("|", $value);
          $incentivo->_colaborador_id = MasterDom::getData('colaborador_id');
          $incentivo->_prorrateo_periodo_id = $explode['1'];
          $incentivo->_catalogo_incentivo_id = $explode['2'];
          $incentivo->_cantidad = $explode['3'];
          $incentivo->_asignado = $explode['4'];
          $incentivo->_valido = $explode['5'];
          $insertarIncentivo = AsignarIncentivosDao::insertIncentivos($incentivo);
        }

        if($insertarIncentivo>0)
          $this->alerta(MasterDom::getData('colaborador_id'), "delete", MasterDom::getData('prorrateo_periodo_id'), MasterDom::getData('seccion'));
        else
          $this->alerta(MasterDom::getData('colaborador_id'), "error", MasterDom::getData('prorrateo_periodo_id'), MasterDom::getData('seccion'));
    }


    public function getTablaIncentivosValidacion($idColaborador, $idPeriodo){
      $incentivoAsignador = AsignarIncentivosDao::getIncentivoColaborador($idColaborador);
      $faltas = $this->getStatusAsistenciasColaborador($idColaborador, $idPeriodo);
      foreach ($incentivoAsignador as $key => $value) {
        $asignadoSistema = ($value['fijo'] == "si") ? "Asignado por el sistema." : "";
        $tabla .=<<<html
          <tr>
            <td style="text-align:center; vertical-align:middle;"><input type="checkbox" name="borrar[]" value="{$value['incentivos_asignados_id']}"/> </td>
            <td style="text-align:center; vertical-align:middle;">{$value['nombre']}</td>
            <td style="text-align:center; vertical-align:middle;">{$value['cantidad']}</td>
            <td style="text-align:center; vertical-align:middle;">{$value['tipo']}</td>
            <td style="text-align:center; vertical-align:middle;">Cantidad de faltas {$faltas}</td>
            <td style="text-align:center; vertical-align:middle;">{$asignado}</td>
            <td style="text-align:center; vertical-align:middle;">{$asignadoSistema}</td>
            <td style="text-align:center; vertical-align:middle;"><input id="agregartablauno" type="button" value="AGREGAR"> <br></td>
          </tr>
html;
      }
        $tabla.=<<<html
          <tr id="destino">

          </tr>
html;
      View::set('tabla',$tabla);
    }

    public function getPeriodo($idPeriodo){
      $datosPeriodo = AsignarIncentivosDao::getPeriodoBuscar($idPeriodo);
      $procesoPeriodo = ($datosPeriodo['status']==0)?"Periodo Abierto":"Periodo Cerrado";
      $display = ($datosPeriodo['status']==0)?"":"display:none";
      $msjbackground = ($datosPeriodo['status']==0)?"":"display:none";
      $mensajeIncentivo = ($datosPeriodo['status']==0)?"<h5>Antes de comenzar, puedes validar que incentivos pueden ser agregados al colaborador, <b>para este periodo</b>.</h5>":"<h5>El periodo esta cerrado, y no se puede ingresar informacion, solo es para consultar.<h5>";


      View::set('display',$display);
      View::set('mensajeIncentivo',$mensajeIncentivo);
      View::set('procesoPeriodo',$procesoPeriodo);
      View::set('datosPeriodo',$datosPeriodo);
    }

    public function delete(){
      $id = MasterDom::getDataAll('borrar');

      $arrayDelete = array();
      foreach ($id as $key => $value) {
        $delete = AsignarIncentivosDao::delete($value);
        array_push($arrayDelete, $delete);
      }

      $sumaArr = array_sum($arrayDelete);

      if($sumaArr>0)
        $this->alerta(MasterDom::getData('colaborador_id'), "delete", MasterDom::getData('prorrateo_periodo_id'), MasterDom::getData('seccion'));
      else
        $this->alerta(MasterDom::getData('colaborador_id'), "error", MasterDom::getData('prorrateo_periodo_id'), MasterDom::getData('seccion'));
    }

    public function deleteIncentivo($id, $idPeriodo, $catalogo_incentivo_id, $cantidad, $asignado){

      $incentivo = new \stdClass();
      $incentivo->_colaborador_id = $id;
      $incentivo->_prorrateo_periodo_id = $idPeriodo;
      $incentivo->_catalogo_incentivo_id = $catalogo_incentivo_id;
      $incentivo->_cantidad = $cantidad;
      $incentivo->_asignado = $asignado;
      $delete = AsignarIncentivosDao::deleteIncentivo($incentivo);
      $this->alerta($id, "add", $idPeriodo);
    }

    public function alerta($id, $parametro, $colaborador_id,$seccion){
      $regreso = "/AsignarIncentivos/incentivos/{$id}/{$colaborador_id}/{$seccion}";

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

      if($parametro == 'delete'){
        $mensaje = "Se ha eliminado el incentivo, satisfactoriamente";
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
