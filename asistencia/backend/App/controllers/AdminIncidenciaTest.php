<?php
namespace App\controllers;
//defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \App\controllers\Contenedor;
use \Core\Controller;
use \App\models\AdminIncidencia AS AdminIncidenciaTestDao;
class AdminIncidenciaTest extends Controller{

    private $_contenedor;

    function __construct(){
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header',$this->_contenedor->header());
        View::set('footer',$this->_contenedor->footer());
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
              "order": false
            });

            // Remove accented character from search input as well
            $('#muestra-cupones input[type=search]').keyup( function () {
              var table = $('#example').DataTable();
              table.search(jQuery.fn.DataTable.ext.type.search.html(this.value)).draw();
            } );

            $("#btnReiniciar").click(function(){
              $.ajax({
                url: "/AdminIncidenciaTest/getTabla",
                type: "POST",
                data: "",
                success: function(data){
                  $("#registros").html(data);
                }
              });
            });

            $("select").change(function(){
              $.ajax({
                url: "/AdminIncidenciaTest/getTabla",
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
    $datosUsuario = AdminIncidenciaTestDao::getDatosUsuarioLogeado($this->__usuario);

    if(empty($_POST)){
      $periodos = "";
      foreach (AdminIncidenciaTestDao::getPeriodoFechas(strtoupper($datosUsuario['tipo'])) as $key => $value) {
        $mensaje = ($value['status'] == 0 ) ? "Periodo en proceso." : "Periodo terminado" ;
        $selected = ($value['status'] == 0 ) ? "selected":""; 
        $periodos .=<<<html
          <option {$selected} value="{$value['prorrateo_periodo_id']}">Inicio: {$value['fecha_inicio']} - Fin:{$value['fecha_fin']} {$mensaje}</option>
html;
      }
    }else{
      $periodos = "";
      foreach (AdminIncidenciaTestDao::getPeriodoFechas(strtoupper($datosUsuario['tipo'])) as $key => $value) {
        $mensaje = ($value['status'] == 0 ) ? "Periodo en proceso." : "Periodo terminado" ;
        $selected = ($value['prorrateo_periodo_id'] == MasterDom::getData('tipo_periodo') ) ? "selected":""; 
        $periodos .=<<<html
          <option {$selected} value="{$value['prorrateo_periodo_id']}">Inicio: {$value['fecha_inicio']} - Fin:{$value['fecha_fin']} {$mensaje}</option>
html;
      }
    }



    $secciones = AdminIncidenciaTestDao::getDepartamentos($datosUsuario['administrador_id']);
    $tabla = '';
    foreach ($secciones as $key => $value) {
      foreach (AdminIncidenciaTestDao::getAllColaboradores($value['catalogo_departamento_id']) as $key => $value) {
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
              <a href="/AdminIncidenciaTest/editFechas/{$value['catalogo_colaboradores_id']}" type="submit" name="id_empresa" class="btn btn-primary"><i class="fa fa-plus"></i> </a>
            </td>
          </tr>
html;
      }
    }

      View::set('periodos',$periodos);
      View::set('tabla',$tabla);
      View::set('header',$this->_contenedor->header($extraHeader));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("admin_incidencia_all");
    }

    public function getTabla(){
      $datos = array();
      $datos['e.catalogo_empresa_id'] = MasterDom::getData('catalogo_empresa_id');
      $datos['u.catalogo_ubicacion_id'] = MasterDom::getData('catalogo_ubicacion_id');
      $datos['d.catalogo_departamento_id'] = MasterDom::getData('catalogo_departamento_id');
      $datos['p.catalogo_puesto_id'] = MasterDom::getData('catalogo_puesto_id');
      $datos['s.catalogo_status_id'] = MasterDom::getData('status');

      $filtro = '';
      foreach ($datos as $key => $value) {
        if($value!=''){
          $filtro .= 'AND '.$key.' = '.$value.' ';
        }
      }

      $tabla= '';
      foreach (AdminIncidenciaTestDao::getAllReporte($filtro) as $key => $value) {
        $value['numero_empleado'] = utf8_encode($value['numero_empleado']);
        $value['nombre'] = utf8_encode($value['nombre']);
        $value['apellido_paterno'] = utf8_encode($value['apellido_paterno']);
        $value['apellido_materno'] = utf8_encode($value['apellido_materno']);
        $value['catalogo_empresa_id'] = utf8_encode($value['catalogo_empresa_id']);
        $value['catalogo_departamento_id'] = utf8_encode($value['catalogo_departamento_id']);
        $value['status'] = utf8_encode($value['status']);
        $tabla.=<<<html
                <tr>
                  <td {$editarHidden}><input type="checkbox" name="borrar[]" value="{$value['catalogo_colaboradores_id']}"/></td>
                  <td><img class="foto" src="/img/colaboradores/{$value['foto']}"/></td>
                  <td>{$value['numero_empleado']}</td>
                  <td>{$value['nombre']} {$value['apellido_paterno']} {$value['apellido_materno']}</td>
                  <td>{$value['catalogo_empresa_id']}</td>
                  <td>{$value['catalogo_departamento_id']}</td>
                  <td class="center" >
                        <a href="/AdminIncidenciaTest/mostrarDiasLaborales/{$value['catalogo_colaboradores_id']}" type="submit" name="id_empresa" class="btn btn-success"><span class="glyphicon glyphicon-eye-open" style="color:white"></span> </a>
                  </td>
                </tr>
html;
      }
      echo $tabla;
    }

    public function edit($id){
      $extraFooter =<<<html
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
              table.search(jQuery.fn.DataTable.ext.type.search.html(this.value)).draw();
            } );


          $('#fecha_inicio').daterangepicker({
              singleDatePicker: true,
              calender_style: "picker_1"
            }, function(start, end, label) {
              console.log(start.toISOString(), end.toISOString(), label);
          });

          $('#fecha_fin').daterangepicker({
              singleDatePicker: true,
              calender_style: "picker_1"
            }, function(start, end, label) {
              console.log(start.toISOString(), end.toISOString(), label);
          });

          $("#btnAplicar").click(function(){
            $.ajax({
              url:"/AdminIncidenciaTest/getTablaFechaRango/",
              data:{
                fecha_inicio: $("#fecha_inicio").val(),
                fecha_fin: $("#fecha_fin").val(),
                catalogo_horario_id: $("#catalogo_horario_id").val(),
                catalogo_colaboradores_id: $("#catalogo_colaboradores_id").val(),
                numero_empleado: $("#numero_empleado").val()
              },
              type:"post",
              success:function(data){
                //alert(data);
                $("#registros").html(data);
              }
            });
          });

         $(document).on('click', '.span_delete', function () {
            var colaborador = $(this).attr('colaborador_id');
            var fecha1 = $(this).attr('fecha');

            $.ajax({
                url: "/AdminIncidenciaTest/delete",
                type: "POST",
                data:{colaborador_id: colaborador, fecha: fecha1},
                success: function(){
                    $("#btnAplicar").click();
                }
            });
            return false;
         });

          $("#btnAplicar").click();
        });//fin del document ready

     </script>
html;

      $datosUsuario = AdminIncidenciaTestDao::getDatosUsuarioLogeado($this->__usuario);
      $colaborador = AdminIncidenciaTestDao::getById($id);
      $horarios = AdminIncidenciaTestDao::getHorariosColaborador($id);

      $fechaInicial = date("Y-m-d",strtotime("2017-11-08 -7 day"));
      $fecha = new \DateTime('');
      View::set('colaborador',$colaborador);
      View::set('fecha_ini', $fechaInicial);
      View::set('fecha_fin', $fecha->format('Y-m-d'));
      View::set('header',$this->_contenedor->header($extraHeader));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("admin_incidencia_edit");
    }

    public function add($colaborador_id, $fecha){
      $extraFooter =<<<html
      <script src="/js/moment/moment.min.js"></script>
      <script src="/js/datepicker/scriptdatepicker.js"></script>
      <script src="/js/datepicker/datepicker2.js"></script>
      <script>
        $(document).ready(function(){

          $('#fecha').daterangepicker({
              singleDatePicker: true,
              calender_style: "picker_1"
            }, function(start, end, label) {
              console.log(start.toISOString(), end.toISOString(), label);
          });

          $('#fecha_fin').daterangepicker({
              singleDatePicker: true,
              calender_style: "picker_1"
            }, function(start, end, label) {
              console.log(start.toISOString(), end.toISOString(), label);
          });

          $("#rango_fechas").bootstrapSwitch();

          $('input[name="rango_fechas"]').on('switchChange.bootstrapSwitch', function(event, state) {
            if(state){
              $("#contenedor_fecha_fin").show();
            }else{
              $("#contenedor_fecha_fin").hide();
            }
          });

          $("#add").validate({
            rules:{
              fecha:{required: true},
              incidencia_id:{required: true}
            },
            messages:{
              fecha:{required: "Este campo es requerido"},
              incidencia_id:{required: "Este campo es requerido"}
            }
          });
          $("#btnCancel").click(function(){
            window.location.replace('/AdminIncidenciaTest/editFechas/{$colaborador_id}')
          });
        });
      </script>
html;
      $sIncidencia = '';  
      foreach(AdminIncidenciaTestDao::getIncidencias() as $key => $value){
        $sIncidencia .=<<<html
        <option value="{$value['catalogo_incidencia_id']}">{$value['nombre']}</option>
html;
      }

      View::set('colaborador_id', $colaborador_id);
      View::set('fecha', $fecha);
      View::set('sIncidencia', $sIncidencia);
      View::set('header', $this->_contenedor->header());
      View::set('footer', $this->_contenedor->footer($extraFooter));
      View::render('admin_incidencia_add');
    }

    public function incidenciaAdd(){
      $datos = new \stdClass();
      $datos->catalogo_colaboradores_id = MasterDom::getData('colaborador_id');
      $datos->fecha = MasterDom::getData('fecha');
      $datos->catalogo_incidencia_id = MasterDom::getData('incidencia_id');
      $datos->comentario = MasterDom::getData('comentario');

      if(MasterDom::getData('rango_fechas') == 'on'){
        $fecha_inicio = new \DateTime(MasterDom::getData('fecha'));
        $fecha_fin = new \DateTime(MasterDom::getData('fecha_fin'));
        while($fecha_inicio <= $fecha_fin){
            $datos->fecha = $fecha_inicio->format('Y-m-d');
            $existe = count(AdminIncidenciaTestDao::getFechaIncidenciaById($datos));
            if($existe>0){
                AdminIncidenciaTestDao::deleteFechaIncidenciaById($datos);
                $id = AdminIncidenciaTestDao::insertProrrateoColaboradorIncidencia($datos);
            }else{
                $id = AdminIncidenciaTestDao::insertProrrateoColaboradorIncidencia($datos);
            }
            if($id < 0){
                break;
            }
            $fecha_inicio->add(new \DateInterval('P1D'));
        }
      }else{
        $id = AdminIncidenciaTestDao::insertProrrateoColaboradorIncidencia($datos);
      }

      if($id > 0){
        $this->alerta($id, 'add', MasterDom::getData('colaborador_id'));
      }else{
        $this->alerta($id, 'error', MasterDom::getData('colaborador_id'));
      }
    }

    public static function getTablaFechaRango(){
      $fecha_inicio = new \DateTime(MasterDom::getData('fecha_inicio'));
      $fecha_fin = new \DateTime(MasterDom::getData('fecha_fin'));
      $dias = array('Monday'=>'Lunes','Tuesday'=>'Martes','Wednesday'=>'Miercoles','Thursday'=>'Jueves','Friday'=>'Viernes','Saturday'=>'Sabado','Sunday'=>'Domingo');
      $incidencias_colaborador = AdminIncidenciaTestDao::getProrrateoColaboradorIncidenciaById(MasterDom::getData('catalogo_colaboradores_id'));
      $datos = new \stdClass();
      $datos->numero_empleado = MasterDom::getData('numero_empleado');
      $datos->catalogo_colaboradores_id = MasterDom::getData('catalogo_colaboradores_id');
      $datos->catalogo_horario_id = MasterDom::getData('catalogo_horario_id');
      $dia_aux = '';
      while($fecha_inicio <= $fecha_fin){
        foreach (AdminIncidenciaTestDao::getHorarioLaboral($datos) as $key => $value) {
          $diferente = false;
          if($dia_aux != strtolower($value['dia_semana'])){
              $dia_aux = strtolower($value['dia_semana']);
              $diferente = true;
          }
          if(strtolower($value['dia_semana']) == strtolower($dias[$fecha_inicio->format('l')]) && $diferente){

            $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' '.AdminIncidenciaTest::restarMinutos($value['hora_entrada'],60);
            $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' '.AdminIncidenciaTest::sumarMinutos($value['hora_entrada'],intval($value['tolerancia_entrada']));
            $registro_entrada = AdminIncidenciaTestDao::getAsistencia($datos);

            $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' '.AdminIncidenciaTest::restarMinutos($value['hora_salida'],240);
            $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' '.AdminIncidenciaTest::sumarMinutos($value['hora_salida'],240);
            $registro_salida = AdminIncidenciaTestDao::getAsistencia($datos);

            if($registro_entrada['date_check'] != ''){
              $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' 00:00:00';
              $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' 23:59:59';
              $registro_entrada = AdminIncidenciaTestDao::getAsistenciaOne($datos);
            }

            $registro_entrada['date_check'] = ($registro_entrada['date_check'] != '')? $registro_entrada['date_check'] : 'Sin Registro';
            $registro_salida['date_check'] = ($registro_salida['date_check'] != '')? $registro_salida['date_check'] : 'Sin Registro';
            $colaborador_id = MasterDom::getData('catalogo_colaboradores_id');
            $incidencia = '';
            foreach ($incidencias_colaborador as $llave => $valor) {
              if( $fecha_inicio->format('Y-m-d') === $valor['fecha_incidencia']){
                $incidencia = $valor['nombre'];
                break;
              }
            }

            $tabla .=<<<html
              <tr>
                <td>{$dias[$fecha_inicio->format('l')]}</td>
                <td>{$fecha_inicio->format('Y-m-d')}</td>
                <td>{$value['hora_entrada']}</td>
                <td>{$value['hora_salida']}</td>
                <td>{$registro_entrada['date_check']}</td>
                <td>{$registro_salida['date_check']}</td>
                <td></td>
                <td>
                  <label>{$incidencia}</label>
                </td>
                <td>
                    <div class="form-group">
                      <a class="btn btn-primary" href="/AdminIncidenciaTest/add/{$colaborador_id}/{$fecha_inicio->format('Y-m-d')}"><i class="fa fa-plus-square" aria-hidden="true"></i></a>
                    </div>
html;
              $datos->fecha = $fecha_inicio->format('Y-m-d');
              if(count(AdminIncidenciaTestDao::getFechaIncidenciaById($datos)) > 0) {
                  $tabla .= <<<html
                    <div class="form-group">
                      <span class="btn btn-danger span_delete" id="delete" colaborador_id="{$colaborador_id}" fecha="{$fecha_inicio->format('Y-m-d')}"><i class="fa fa-trash-o" aria-hidden="true"> </i><span/>
                    </div>
html;
              }
              $tabla .=<<<html
                </td>
              </tr>
html;
          }
        }
        $fecha_inicio->add(new \DateInterval('P1D'));
      }
      echo $tabla;
    }

    public function delete(){
        $datos = new \stdClass();
        $datos->fecha = MasterDom::getData('fecha');
        $datos->catalogo_colaboradores_id = MasterDom::getData('colaborador_id');
        AdminIncidenciaTestDao::deleteFechaIncidenciaById($datos);
    }

    public function editFechas($id) {
      $extraHeader =<<<html
        <link rel="stylesheet" type="text/css" media="all" href="/css/daterangepicker.css" />
html;
      $extraFooter =<<<html
        
        <script type="text/javascript" src="http://netdna.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
        <script src="/js/moment/moment.min.js"></script>
        <script type="text/javascript" src="/js/daterangepicker.js"></script>
        <script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>

        <script type="text/javascript">
          $(document).ready(function() {
            updateConfig();

            function updateConfig() {
              var options = {};
              $('#config-demo').daterangepicker(
                options,
                function(start, end, label) { 
                  console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')'
                );
              }
            );}
          });
        </script>

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
        </script>
html;
      $colaborador = AdminIncidenciaTestDao::getById($id);

      foreach (AdminIncidenciaTestDao::getPeriodoFechasProceso(strtoupper($colaborador['pago'])) as $key => $value) {
        $f1 = $value['fecha_inicio'];
        $f2 = $value['fecha_fin'];
        $estatusPeriodo = $value['status'];
      }


      //$f1 = date('m/d/Y', strtotime('-7 day'));
      //$f2 = date('m/d/Y', strtotime('-1 day'));

      $fecha_inicio = new \DateTime($f1);
      $fecha_final = new \DateTime($f2);
      $dias = array('Monday'=>'Lunes','Tuesday'=>'Martes','Wednesday'=>'Miercoles','Thursday'=>'Jueves','Friday'=>'Viernes','Saturday'=>'Sabado','Sunday'=>'Domingo');

      $incidencias_colaborador = AdminIncidenciaTestDao::getProrrateoColaboradorIncidenciaById($colaborador['catalogo_colaboradores_id']);
      $datos = new \stdClass();
      $datos->numero_empleado = $colaborador['numero_empleado'];
      $datos->catalogo_colaboradores_id = $colaborador['catalogo_colaboradores_id'];

      $dia_aux = '';
      while($fecha_inicio <= $fecha_final){
        $bandera_dias_descanso = false;
        foreach (AdminIncidenciaTestDao::getHorarioLaboral($datos) as $key => $value) {
          if($dia_aux != strtolower($value['dia_semana'])){
              $dia_aux = strtolower($value['dia_semana']);
              $diferente = true;
              $diferente = false;
          }
          if(strtolower($value['dia_semana']) == strtolower($dias[$fecha_inicio->format('l')]) && $diferente){

            $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' '.AdminIncidenciaTest::restarMinutos($value['hora_entrada'],60);
            $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' '.AdminIncidenciaTest::sumarMinutos($value['hora_entrada'],intval($value['tolerancia_entrada']));
            $registro_entrada = AdminIncidenciaTestDao::getAsistencia($datos);

            $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' '.AdminIncidenciaTest::restarMinutos($value['hora_salida'],240);
            $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' '.AdminIncidenciaTest::sumarMinutos($value['hora_salida'],240);
            $registro_salida = AdminIncidenciaTestDao::getAsistencia($datos);

            if($registro_entrada['date_check'] != ''){
              $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' 00:00:00';
              $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' 23:59:59';
              $registro_entrada = AdminIncidenciaTestDao::getAsistenciaOne($datos);
            }

            $registro_entrada['date_check'] = ($registro_entrada['date_check'] != '')? $registro_entrada['date_check'] : 'Sin Registro';
            $registro_salida['date_check'] = ($registro_salida['date_check'] != '')? $registro_salida['date_check'] : 'Sin Registro';
            $colaborador_id = MasterDom::getData('catalogo_colaboradores_id');

            $incidencia = '';
            foreach ($incidencias_colaborador as $llave => $valor) {
              if( $fecha_inicio->format('Y-m-d') === $valor['fecha_incidencia']){
                $incidencia = $valor['nombre'];
                $comentario = $valor['comentario'];
                break;
              }
            }

            $tabla .=<<<html
              <tr>
                <td>{$dias[$fecha_inicio->format('l')]}</td>
                <td>{$fecha_inicio->format('Y-m-d')}</td>
                <td>{$value['hora_entrada']}</td>
                <td>{$value['hora_salida']}</td>
                <td>{$registro_entrada['date_check']}</td>
                <td>{$registro_salida['date_check']}</td>
                <td>
html;
            foreach ($incidencias_colaborador as $llave => $valor) {
              if( $fecha_inicio->format('Y-m-d') === $valor['fecha_incidencia']){
                //$incidencia = $valor['nombre'];
                $tabla .= $valor['comentario'];
                break;
              }
            }
            $tabla.=<<<html
                </td>


                <td>
                  <label>{$incidencia}</label>
                </td>
                <td>
                    <div class="form-group">
html;
                if($estatusPeriodo == 0){
                  $tabla.=<<<html
                      <a class="btn btn-primary" href="/AdminIncidenciaTest/add/{$id}/{$fecha_inicio->format('Y-m-d')}"><i class="fa fa-plus-square" aria-hidden="true"></i></a>
html;
                }else{
                  $tabla.=<<<html
                      <a class="btn btn-primary" ><i class="fa fa-plus-square" aria-hidden="true"></i></a>
html;
                }

              $datos->fecha = $fecha_inicio->format('Y-m-d');
              if(count(AdminIncidenciaTestDao::getFechaIncidenciaById($datos)) > 0) {
                  $tabla .= <<<html
                      <a  href="/AdminIncidenciaTest/deleteIncidecia?id={$id}&fecha={$fecha_inicio->format('Y-m-d')}" class="btn btn-danger">
                        <i class="fa fa-trash-o" aria-hidden="true"> </i>
                      </a>
html;
              }
              $tabla .=<<<html
                    </div>
                </td>
              </tr>
html;
          }else{
            if(!$bandera_dias_descanso){
              $bandera_dias_descanso = true;
              $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' '.AdminIncidenciaTest::restarMinutos($value['hora_entrada'],60);
              $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' '.AdminIncidenciaTest::sumarMinutos($value['hora_entrada'],intval($value['tolerancia_entrada']));
              $registro_entrada = AdminIncidenciaTestDao::getAsistencia($datos);

              $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' '.AdminIncidenciaTest::restarMinutos($value['hora_salida'],240);
              $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' '.AdminIncidenciaTest::sumarMinutos($value['hora_salida'],240);
              $registro_salida = AdminIncidenciaTestDao::getAsistencia($datos);

              if($registro_entrada['date_check'] != ''){
                $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' 00:00:00';
                $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' 23:59:59';
                $registro_entrada = AdminIncidenciaTestDao::getAsistenciaOne($datos);
              }

              $registro_entrada['date_check'] = ($registro_entrada['date_check'] != '')? $registro_entrada['date_check'] : 'Sin Registro';
              $registro_salida['date_check'] = ($registro_salida['date_check'] != '')? $registro_salida['date_check'] : 'Sin Registro';
              $colaborador_id = MasterDom::getData('catalogo_colaboradores_id');

              $incidencia = '';
              foreach ($incidencias_colaborador as $llave => $valor) {
                if( $fecha_inicio->format('Y-m-d') === $valor['fecha_incidencia']){
                  $incidencia = $valor['nombre'];
                  $comentario = $valor['comentario'];

                  break;
                }
              }

              $tabla .=<<<html
                <tr>
                  <td>{$dias[$fecha_inicio->format('l')]}</td>
                  <td>{$fecha_inicio->format('Y-m-d')}</td>
                  <td>{$value['hora_entrada']}</td>
                  <td>{$value['hora_salida']}</td>
                  <td>{$registro_entrada['date_check']}</td>
                  <td>{$registro_salida['date_check']}</td>
                  <td>
html;
              foreach ($incidencias_colaborador as $llave => $valor) {
                if( $fecha_inicio->format('Y-m-d') === $valor['fecha_incidencia']){
                  //$incidencia = $valor['nombre'];
                  $tabla .= $valor['comentario'];
                  break;
                }
              }
              $tabla.=<<<html
                  </td>


                  <td>
                    <label>{$incidencia}</label>
                  </td>
                  <td>
                      <div class="form-group">
html;
                  if($estatusPeriodo == 0){
                    $tabla.=<<<html
                        <a class="btn btn-primary" href="/AdminIncidenciaTest/add/{$id}/{$fecha_inicio->format('Y-m-d')}"><i class="fa fa-plus-square" aria-hidden="true"></i></a>
html;
                  }else{
                    $tabla.=<<<html
                        <a class="btn btn-primary" ><i class="fa fa-plus-square" aria-hidden="true"></i></a>
html;
                  }

                $datos->fecha = $fecha_inicio->format('Y-m-d');
                if(count(AdminIncidenciaTestDao::getFechaIncidenciaById($datos)) > 0) {
                    $tabla .= <<<html
                        <a  href="/AdminIncidenciaTest/deleteIncidecia?id={$id}&fecha={$fecha_inicio->format('Y-m-d')}" class="btn btn-danger">
                          <i class="fa fa-trash-o" aria-hidden="true"> </i>
                        </a>
html;
                }
                $tabla .=<<<html
                      </div>
                  </td>
                </tr>
html;
            }
          }
        }
        $fecha_inicio->add(new \DateInterval('P1D'));
      }

      $selectPeriodoFechas = "";
      foreach (AdminIncidenciaTestDao::getPeriodoFechas(strtoupper($colaborador['pago'])) as $key => $value) {
        
        $select = ($value['status'] == 0) ? "selected":"";
        $mensaje  = ($value['status'] == 0) ? " de proceso":" terminado";
        $selectPeriodoFechas .= <<<html
          <option {$select} value="{$value['prorrateo_periodo_id']}">{$value['fecha_inicio']} - {$value['fecha_fin']} : Este periodo esta en estatus {$mensaje}</option>
html;
      }

      View::set('colaborador',$colaborador);
      View::set('rango',$f1 . " - " . $f2);
      View::set('tabla',$tabla);
      View::set('selectPeriodoFechas', $selectPeriodoFechas);
      View::set('header', $this->_contenedor->header($extraHeader));
      View::set('footer', $this->_contenedor->footer($extraFooter));
      View::render("admin_incidencia_colaborador");
    }

    public function deleteIncidecia(){
      $delete = new \stdClass();
      $delete->_id = MasterDom::getData('id');
      $delete->_fecha = MasterDom::getData('fecha');
      $id = AdminIncidenciaTestDao::deleteIncidencia($delete);
      if($id>0)
        $this->alerta("", "delete", MasterDom::getData('id'));
      else
        $this->alerta("", "error", MasterDom::getData('id'));


    }

    public function rangoFechas() {
      $extraHeader =<<<html

        <link rel="stylesheet" type="text/css" media="all" href="/css/daterangepicker.css" />
html;
      $extraFooter =<<<html
        
        <script type="text/javascript" src="http://netdna.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
        <script src="/js/moment/moment.min.js"></script>
        <script type="text/javascript" src="/js/daterangepicker.js"></script>
        <script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>

        <script type="text/javascript">
          $(document).ready(function() {
            updateConfig();

            function updateConfig() {
              var options = {};
              $('#config-demo').daterangepicker(
                options,
                function(start, end, label) { 
                  console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')'
                );
              }
            );}
          });
        </script>

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
        </script>
html;
      $id = MasterDom::getData('catalogo_colaboradores_id');
      $periodo = MasterDom::getData('periodo');
      $colaborador = AdminIncidenciaTestDao::getById($id);

      foreach (AdminIncidenciaTestDao::getPeriodoFechasProcesoBusqueda(strtoupper($colaborador['pago']), $periodo) as $key => $value) {
        $f1 = $value['fecha_inicio'];
        $f2 = $value['fecha_fin'];
        $estatusPeriodo = $value['status'];
      }

      /*$rangos = MasterDom::getData('rango');
      $fechas = explode("-", $rangos);
      $f1 = $fechas[0];
      $f2 = $fechas[1];*/
      $fecha_ini = date('Y-m-d', strtotime($f1));
      $fecha_fin = date('Y-m-d', strtotime($f2));
      $fecha_inicio = new \DateTime($fecha_ini);
      $fecha_final = new \DateTime($fecha_fin);
      $dias = array('Monday'=>'Lunes','Tuesday'=>'Martes','Wednesday'=>'Miercoles','Thursday'=>'Jueves','Friday'=>'Viernes','Saturday'=>'Sabado','Sunday'=>'Domingo');

      $incidencias_colaborador = AdminIncidenciaTestDao::getProrrateoColaboradorIncidenciaById(MasterDom::getData('catalogo_colaboradores_id'));

      $datos = new \stdClass();
      $datos->numero_empleado = MasterDom::getData('numero_empleado');
      $datos->catalogo_colaboradores_id = MasterDom::getData('catalogo_colaboradores_id');
      $datos->catalogo_horario_id = MasterDom::getData('catalogo_horario_id');
      $dia_aux = '';
      while($fecha_inicio <= $fecha_final){
        foreach (AdminIncidenciaTestDao::getHorarioLaboral($datos) as $key => $value) {
          $diferente = false;
          if($dia_aux != strtolower($value['dia_semana'])){
              $dia_aux = strtolower($value['dia_semana']);
              $diferente = true;
          }
          if(strtolower($value['dia_semana']) == strtolower($dias[$fecha_inicio->format('l')]) && $diferente){

            $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' '.AdminIncidenciaTest::restarMinutos($value['hora_entrada'],60);
            $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' '.AdminIncidenciaTest::sumarMinutos($value['hora_entrada'],intval($value['tolerancia_entrada']));
            $registro_entrada = AdminIncidenciaTestDao::getAsistencia($datos);

            $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' '.AdminIncidenciaTest::restarMinutos($value['hora_salida'],240);
            $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' '.AdminIncidenciaTest::sumarMinutos($value['hora_salida'],240);
            $registro_salida = AdminIncidenciaTestDao::getAsistencia($datos);

            if($registro_entrada['date_check'] != ''){
              $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' 00:00:00';
              $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' 23:59:59';
              $registro_entrada = AdminIncidenciaTestDao::getAsistenciaOne($datos);
            }

            $registro_entrada['date_check'] = ($registro_entrada['date_check'] != '')? $registro_entrada['date_check'] : 'Sin Registro';
            $registro_salida['date_check'] = ($registro_salida['date_check'] != '')? $registro_salida['date_check'] : 'Sin Registro';
            $colaborador_id = MasterDom::getData('catalogo_colaboradores_id');
            $incidencia = '';
            foreach ($incidencias_colaborador as $llave => $valor) {
              if( $fecha_inicio->format('Y-m-d') === $valor['fecha_incidencia']){
                $incidencia = $valor['nombre'];
                break;
              }
            }
 
            $tabla .=<<<html
              <tr>
                <td>{$dias[$fecha_inicio->format('l')]}</td>
                <td>{$fecha_inicio->format('Y-m-d')}</td>
                <td>{$value['hora_entrada']}</td>
                <td>{$value['hora_salida']}</td>
                <td>{$registro_entrada['date_check']}</td>
                <td>{$registro_salida['date_check']}</td>
                <td></td>
                <td>
                  <label>{$incidencia}</label>
                </td>
                <td>
html;
            if($estatusPeriodo == 0){
              $tabla.=<<<html
                <a class="btn btn-primary" href="/AdminIncidenciaTest/add/{$colaborador_id}/{$fecha_inicio->format('Y-m-d')}"><i class="fa fa-plus-square" aria-hidden="true"></i></a>
html;
            }

            if($estatusPeriodo == 1){
              $tabla.=<<<html
                <a class="btn btn-default"><i class="fa fa-plus-square" aria-hidden="true"></i></a>
html;
            }

            $tabla .=<<<html
                    </div>
html;
            $datos->fecha = $fecha_inicio->format('Y-m-d');
            if(count(AdminIncidenciaTestDao::getFechaIncidenciaById($datos)) > 0) {
                $tabla .= <<<html
                  <div class="form-group">
html;
              if($estatusPeriodo == 0){
                $tabla .= <<<html
                    <a  href="/AdminIncidenciaTest/deleteIncidecia?id={$id}&fecha={$fecha_inicio->format('Y-m-d')}" class="btn btn-danger">
                        <i class="fa fa-trash-o" aria-hidden="true"> </i>
                      </a>
html;
              }

              }
              $tabla .=<<<html
                </td>
              </tr>
html;
          }
        }
        $fecha_inicio->add(new \DateInterval('P1D'));
      }

      $selectPeriodoFechas = "";
      foreach (AdminIncidenciaTestDao::getPeriodoFechas(strtoupper($colaborador['pago'])) as $key => $value) {
        
        $select = ($value['prorrateo_periodo_id'] == $periodo) ? "selected":"";
        $mensaje  = ($value['status'] == 0) ? " de proceso":" terminado";
        $selectPeriodoFechas .= <<<html
          <option {$select} value="{$value['prorrateo_periodo_id']}">{$value['fecha_inicio']} - {$value['fecha_fin']} : Este periodo esta en estatus {$mensaje}</option>
html;
      }

      View::set('rango',$rangos);
      View::set('tabla',$tabla);
      View::set('selectPeriodoFechas',$selectPeriodoFechas);
      View::set('colaborador',$colaborador);
      View::set('catalogo_colaboradores_id',MasterDom::getData('catalogo_colaboradores_id'));
      View::set('numero_empleado',MasterDom::getData('numero_empleado'));
      View::set('header',$this->_contenedor->header($extraHeader));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("admin_incidencia_rangos");
    }

    public static function sumarMinutos($horaInicial,$minutoAnadir){
      $segundos_horaInicial=strtotime($horaInicial);
      $segundos_minutoAnadir=$minutoAnadir*60;
      $nuevaHora=date("H:i",$segundos_horaInicial+$segundos_minutoAnadir);
      return $nuevaHora;
    }

    public static function restarMinutos($horaInicial,$minutoAnadir){
      $segundos_horaInicial=strtotime($horaInicial);
      $segundos_minutoAnadir=$minutoAnadir*60;
      $nuevaHora=date("H:i",$segundos_horaInicial-$segundos_minutoAnadir);
      return $nuevaHora;
    }

    public function alerta($id, $parametro, $colaborador_id){
      $regreso = "/AdminIncidenciaTest/editFechas/".$colaborador_id;

      if($parametro == 'add'){
        $mensaje = "Se ha agregado correctamente";
        $class = "success";
      }

      if($parametro == 'edit'){
        $mensaje = "Se ha modificado correctamente";
        $class = "success";
      }

      if($parametro == 'delete'){
        $mensaje = "Se ha eliminado la incidencias.";
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
