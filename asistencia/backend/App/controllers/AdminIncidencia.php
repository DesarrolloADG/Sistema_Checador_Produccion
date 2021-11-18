<?php
namespace App\controllers;
//defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \App\controllers\Contenedor;
use \Core\Controller;
use \App\models\AdminIncidencia AS AdminIncidenciaDao;
class AdminIncidencia extends Controller{

    private $_contenedor;

    function __construct(){
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header',$this->_contenedor->header());
        View::set('footer',$this->_contenedor->footer());

        if(Controller::getPermisosUsuario($this->__usuario, "Registro_incidencias", 1) ==0)
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
              "order": false
            });

            // Remove accented character from search input as well
            $('#muestra-cupones input[type=search]').keyup( function () {
              var table = $('#example').DataTable();
              table.search(jQuery.fn.DataTable.ext.type.search.html(this.value)).draw();
            } );

            $("#btnReiniciar").click(function(){
              $.ajax({
                url: "/AdminIncidencia/getTabla",
                type: "POST",
                data: "",
                success: function(data){
                  $("#registros").html(data);
                }
              });
            });

            $("select").change(function(){
              $.ajax({
                url: "/AdminIncidencia/getTabla",
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
    $datosUsuario = AdminIncidenciaDao::getDatosUsuarioLogeado($this->__usuario);

    // BANDERA PARA PERIODO
    $bandera = 0;
    if(empty($_POST)){
      // INICIO DE LA VISTA 
      $periodos = "";
      foreach (AdminIncidenciaDao::getPeriodoFechas(strtoupper($datosUsuario['tipo'])) as $key => $value) {
        $mensaje = ($value['status'] == 0 ) ? "Periodo Abierto" : "Periodo Cerrado" ;
        $selected = ($value['status'] == 0 ) ? "selected":""; 
        $periodos .=<<<html
          <option {$selected} value="{$value['prorrateo_periodo_id']}">Periodo: {$value['fecha_inicio']} al {$value['fecha_fin']}, {$mensaje}</option>
html;
      }

      $periodoUltimo = AdminIncidenciaDao::getLastPeriodo(strtoupper($datosUsuario['tipo']));

      if($periodoUltimo['status'] == 0){
        $color = "#40C1A6";
        $msj = " Abierto";
      }else{
        $color = "#EA6153";
        $msj = " Cerrado";
      }

      $texto = <<<html
        <p style="color:white; font-weight:bolder;">El periodo es de {$periodoUltimo['fecha_inicio']} al {$periodoUltimo['fecha_fin']}, periodo {$msj} </p>
html;
    
    }else{
      // CUANDO SE MANDA UNA PETICION CON UN PERIODO
      $periodos = "";
      foreach (AdminIncidenciaDao::getPeriodoFechas(strtoupper($datosUsuario['tipo'])) as $key => $value) {
        $mensaje = ($value['status'] == 0 ) ? "Periodo Abierto." : "Periodo Cerrado" ;
        $selected = ($value['prorrateo_periodo_id'] == MasterDom::getData('tipo_periodo') ) ? "selected":""; 
        $periodos .=<<<html
          <option {$selected} value="{$value['prorrateo_periodo_id']}">Periodo: {$value['fecha_inicio']} al {$value['fecha_fin']}, {$mensaje}</option>
html;
      }

      $periodoProcesado = AdminIncidenciaDao::getPeriodoById(MasterDom::getData('tipo_periodo'));

      $bandera = $periodoProcesado['status'];

      if($bandera == 0){
        $color = "#40C1A6";
        $msj = " Abierto";
      }else{
        $color = "#EA6153";
        $msj = " Cerrado";
      }

      $texto = <<<html
        <p style="color:white; font-weight:bolder;">El periodo es de {$periodoProcesado['fecha_inicio']} al {$periodoProcesado['fecha_fin']}, periodo {$msj} </p>
html;

    }

    $secciones = AdminIncidenciaDao::getDepartamentos($datosUsuario['administrador_id']);
    if($datosUsuario['perfil_id'] == 6){
        //$secciones = AdminIncidenciaDao::getDepartamentosRh();
        $secciones = AdminIncidenciaDao::getDepartamentos($datosUsuario['administrador_id']);
      }else{
        $secciones = AdminIncidenciaDao::getDepartamentos($datosUsuario['administrador_id']);
      }

    $tabla = '';
    foreach ($secciones as $key => $value) {
      foreach (AdminIncidenciaDao::getAllColaboradores($value['catalogo_departamento_id'], $datosUsuario['usuario']) as $key => $value) {
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
              <a href="/AdminIncidencia/editFechas/{$value['catalogo_colaboradores_id']}" type="submit" name="id_empresa" class="btn btn-primary"><i class="fa fa-plus"></i> </a>
            </td>
          </tr>
html;
      }
    }
      View::set('periodos',$periodos);
      View::set('tabla',$tabla);
      View::set('color',$color);
      View::set('texto',$texto);
      View::set('header',$this->_contenedor->header($extraHeader));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("admin_incidencia_all");
    }

    public function semanal() {
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
                url: "/AdminIncidencia/getTabla",
                type: "POST",
                data: "",
                success: function(data){
                  $("#registros").html(data);
                }
              });
            });

            $("select").change(function(){
              $.ajax({
                url: "/AdminIncidencia/getTabla",
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
    $datosUsuario = AdminIncidenciaDao::getDatosUsuarioLogeado($this->__usuario);

    // BANDERA PARA PERIODO
    $bandera = 0;
    if(empty($_POST)){
      // INICIO DE LA VISTA 
      $periodos = "";
      foreach (AdminIncidenciaDao::getPeriodoFechas(strtoupper($datosUsuario['tipo'])) as $key => $value) {
        $mensaje = ($value['status'] == 0 ) ? "Periodo Abierto" : "Periodo Cerrado" ;
        $selected = ($value['status'] == 0 ) ? "selected":""; 
        $periodos .=<<<html
          <option {$selected} value="{$value['prorrateo_periodo_id']}">Periodo: {$value['fecha_inicio']} al {$value['fecha_fin']}, {$mensaje}</option>
html;
      }

      $periodoUltimo = AdminIncidenciaDao::getLastPeriodo(strtoupper($datosUsuario['tipo']));

      if($periodoUltimo['status'] == 0){
        $color = "#40C1A6";
        $msj = " Abierto";
      }else{
        $color = "#EA6153";
        $msj = " Cerrado";
      }

      $texto = <<<html
        <p style="color:white; font-weight:bolder;">El periodo es de {$periodoUltimo['fecha_inicio']} al {$periodoUltimo['fecha_fin']}, periodo {$msj} </p>
html;
    
    }else{
      // CUANDO SE MANDA UNA PETICION CON UN PERIODO
      $periodos = "";
      foreach (AdminIncidenciaDao::getPeriodoFechas(strtoupper($datosUsuario['tipo'])) as $key => $value) {
        $mensaje = ($value['status'] == 0 ) ? "Periodo Abierto." : "Periodo Cerrado" ;
        $selected = ($value['prorrateo_periodo_id'] == MasterDom::getData('tipo_periodo') ) ? "selected":""; 
        $periodos .=<<<html
          <option {$selected} value="{$value['prorrateo_periodo_id']}">Periodo: {$value['fecha_inicio']} al {$value['fecha_fin']}, {$mensaje}</option>
html;
      }

      $periodoProcesado = AdminIncidenciaDao::getPeriodoById(MasterDom::getData('tipo_periodo'));

      $bandera = $periodoProcesado['status'];

      if($bandera == 0){
        $color = "#40C1A6";
        $msj = " Abierto";
      }else{
        $color = "#EA6153";
        $msj = " Cerrado";
      }

      $texto = <<<html
        <p style="color:white; font-weight:bolder;">El periodo es de {$periodoProcesado['fecha_inicio']} al {$periodoProcesado['fecha_fin']}, periodo {$msj} </p>
html;

    }

    $secciones = AdminIncidenciaDao::getDepartamentos($datosUsuario['administrador_id']);
    if($datosUsuario['perfil_id'] == 6){
        $secciones = AdminIncidenciaDao::getDepartamentosRh('');
      }else{
        $secciones = AdminIncidenciaDao::getDepartamentos($datosUsuario['administrador_id']);
      }

    $tabla = '';
    foreach ($secciones as $key => $value) {
      foreach (AdminIncidenciaDao::getAllColaboradoresSemanal($value['catalogo_departamento_id'], $datosUsuario['usuario']) as $key => $value) {
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
              <a href="/AdminIncidencia/editFechas/{$value['catalogo_colaboradores_id']}" type="submit" name="id_empresa" class="btn btn-primary"><i class="fa fa-plus"></i> </a>
            </td>
          </tr>
html;
      }
    }
      View::set('periodos',$periodos);
      View::set('tabla',$tabla);
      View::set('color',$color);
      View::set('texto',$texto);
      View::set('header',$this->_contenedor->header($extraHeader));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("admin_incidencia_all");
    }

    public function quincenal() {
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
                url: "/AdminIncidencia/getTabla",
                type: "POST",
                data: "",
                success: function(data){
                  $("#registros").html(data);
                }
              });
            });

            $("select").change(function(){
              $.ajax({
                url: "/AdminIncidencia/getTabla",
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
    $datosUsuario = AdminIncidenciaDao::getDatosUsuarioLogeado($this->__usuario);

    // BANDERA PARA PERIODO
    $bandera = 0;
    if(empty($_POST)){
      // INICIO DE LA VISTA 
      $periodos = "";
      foreach (AdminIncidenciaDao::getPeriodoFechas(strtoupper($datosUsuario['tipo'])) as $key => $value) {
        $mensaje = ($value['status'] == 0 ) ? "Periodo Abierto" : "Periodo Cerrado" ;
        $selected = ($value['status'] == 0 ) ? "selected":""; 
        $periodos .=<<<html
          <option {$selected} value="{$value['prorrateo_periodo_id']}">Periodo: {$value['fecha_inicio']} al {$value['fecha_fin']}, {$mensaje}</option>
html;
      }

      $periodoUltimo = AdminIncidenciaDao::getLastPeriodo(strtoupper($datosUsuario['tipo']));

      if($periodoUltimo['status'] == 0){
        $color = "#40C1A6";
        $msj = " Abierto";
      }else{
        $color = "#EA6153";
        $msj = " Cerrado";
      }

      $texto = <<<html
        <p style="color:white; font-weight:bolder;">El periodo es de {$periodoUltimo['fecha_inicio']} al {$periodoUltimo['fecha_fin']}, periodo {$msj} </p>
html;
    
    }else{
      // CUANDO SE MANDA UNA PETICION CON UN PERIODO
      $periodos = "";
      foreach (AdminIncidenciaDao::getPeriodoFechas(strtoupper($datosUsuario['tipo'])) as $key => $value) {
        $mensaje = ($value['status'] == 0 ) ? "Periodo Abierto." : "Periodo Cerrado" ;
        $selected = ($value['prorrateo_periodo_id'] == MasterDom::getData('tipo_periodo') ) ? "selected":""; 
        $periodos .=<<<html
          <option {$selected} value="{$value['prorrateo_periodo_id']}">Periodo: {$value['fecha_inicio']} al {$value['fecha_fin']}, {$mensaje}</option>
html;
      }

      $periodoProcesado = AdminIncidenciaDao::getPeriodoById(MasterDom::getData('tipo_periodo'));

      $bandera = $periodoProcesado['status'];

      if($bandera == 0){
        $color = "#40C1A6";
        $msj = " Abierto";
      }else{
        $color = "#EA6153";
        $msj = " Cerrado";
      }

      $texto = <<<html
        <p style="color:white; font-weight:bolder;">El periodo es de {$periodoProcesado['fecha_inicio']} al {$periodoProcesado['fecha_fin']}, periodo {$msj} </p>
html;

    }

    $secciones = AdminIncidenciaDao::getDepartamentos($datosUsuario['administrador_id']);
    if($datosUsuario['perfil_id'] == 6){
        $secciones = AdminIncidenciaDao::getDepartamentosRh('');
      }else{
        $secciones = AdminIncidenciaDao::getDepartamentos($datosUsuario['administrador_id']);
      }

    $tabla = '';
    foreach ($secciones as $key => $value) {
      foreach (AdminIncidenciaDao::getAllColaboradoresQuincenal($value['catalogo_departamento_id'], $datosUsuario['usuario']) as $key => $value) {
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
              <a href="/AdminIncidencia/editFechas/{$value['catalogo_colaboradores_id']}" type="submit" name="id_empresa" class="btn btn-primary"><i class="fa fa-plus"></i> </a>
            </td>
          </tr>
html;
      }
    }
      View::set('periodos',$periodos);
      View::set('tabla',$tabla);
      View::set('color',$color);
      View::set('texto',$texto);
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
      foreach (AdminIncidenciaDao::getAllReporte($filtro) as $key => $value) {
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
                        <a href="/AdminIncidencia/mostrarDiasLaborales/{$value['catalogo_colaboradores_id']}" type="submit" name="id_empresa" class="btn btn-success"><span class="glyphicon glyphicon-eye-open" style="color:white"></span> </a>
                  </td>
                </tr>
html;
      }
      echo $tabla;
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
            window.location.replace('/AdminIncidencia/editFechas/{$colaborador_id}')
          });
        });
      </script>
html;
      $sIncidencia = '';  
      foreach(AdminIncidenciaDao::getIncidencias() as $key => $value){
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
            $existe = count(AdminIncidenciaDao::getFechaIncidenciaById($datos));
            if($existe>0){
                AdminIncidenciaDao::deleteFechaIncidenciaById($datos);
                $id = AdminIncidenciaDao::insertProrrateoColaboradorIncidencia($datos);
            }else{
                $id = AdminIncidenciaDao::insertProrrateoColaboradorIncidencia($datos);
            }
            if($id < 0){
                break;
            }
            $fecha_inicio->add(new \DateInterval('P1D'));
        }
      }else{
        $id = AdminIncidenciaDao::insertProrrateoColaboradorIncidencia($datos);
      }

      if($id > 0){
        $this->alerta($id, 'add', MasterDom::getData('colaborador_id'), MasterDom::getData('vista'));
      }else{
        $this->alerta($id, 'error', MasterDom::getData('colaborador_id'), MasterDom::getData('vista'));
      }
    }

    public function delete(){
        $datos = new \stdClass();
        $datos->fecha = MasterDom::getData('fecha');
        $datos->catalogo_colaboradores_id = MasterDom::getData('colaborador_id');
        AdminIncidenciaDao::deleteFechaIncidenciaById($datos);
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

            

            //$("#btnAdd").click(
            function accionar(){
              $.ajax({
                url: '/AdminIncidencia/getTablaRangoFechas',
                'type': 'POST',
                'data': {
                  'colaborador_id': $('#catalogo_colaboradores_id').val(),
                  'periodo': $('#periodo').val()
                },
                success: function(response){
                  $("#contendor_tabla").html(response);

                  $("#muestra-cupones").tablesorter();
                    var oTable = $('#muestra-cupones').DataTable({
                    "columnDefs": [{
                      "orderable": false,
                      "targets": 0
                    }],
                    "order": false,
                    "language": {
                      "emptyTable": "No hay datos disponibles",
                      "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                      "info": "Mostrar _START_ a _END_ de _TOTAL_ registros",
                      "infoFiltered":   "(Filtrado de _MAX_ total de registros)",
                      "lengthMenu": "Mostrar _MENU_ registros",
                      "zeroRecords":  "No se encontraron resultados",
                      "search": "Buscar:",
                      "processing": "Procesando...",
                      "paginate" : {
                          "next": "Siguiente",
                          "previous" : "Anterior"
                      }
                    }
                  });

                  // Remove accented character from search input as well
                  $('#muestra-cupones input[type=search]').keyup( function () {
                    var table = $('#example').DataTable();
                    table.search(jQuery.fn.DataTable.ext.type.search.html(this.value)).draw();
                  } );
                }
              });
            }
            //);

            $("#periodo").change(function(){
              $.ajax({
                url: '/AdminIncidencia/verificarPeriodo',
                'type': 'POST',
                'data': {
                  'periodo': $('#periodo').val()
                },
                success: function(response){
                  if(response == 0){
                    $("#alerta").removeClass('alert-danger');
                    $("#alerta").addClass('alert-success');
                    $("#alerta").html('<strong>Atención</strong> El periodo seleccionado está abierto.');

                    //$("a.btn-primary").attr("disabled",false);
                    //$("a.eliminar").attr("disabled",false);

                  }

                  if(response == 1){
                    $("#alerta").removeClass('alert-success');
                    $("#alerta").addClass('alert-danger');
                    $("#alerta").html('<strong>Atención</strong> El periodo seleccionado está cerrado.');

                    //$("a.btn-primary").attr("disabled",true);
                    //$("a.eliminar").attr("disabled",true);
                  }

                  accionar();
                }
              });
            });

            $("#periodo").change();
            $("#btnAdd").click();


          });
        </script>
html;
      $colaborador = AdminIncidenciaDao::getById($id);
      $selectPeriodoFechas = "";
      foreach (AdminIncidenciaDao::getPeriodoFechas(strtoupper($colaborador['pago'])) as $key => $value) {
        
        $select = ($value['status'] == 0) ? "selected":"";
        $mensaje  = ($value['status'] == 0) ? " de proceso":" terminado";
        $selectPeriodoFechas .= <<<html
          <option {$select} value="{$value['prorrateo_periodo_id']}">{$value['fecha_inicio']} - {$value['fecha_fin']} : Este periodo esta en estatus {$mensaje}</option>
html;
      }

      foreach (AdminIncidenciaDao::getPeriodoFechasProceso(strtoupper($colaborador['pago'])) as $key => $value) {
        $f1 = $value['fecha_inicio'];
        $f2 = $value['fecha_fin'];
        $estatusPeriodo = $value['status'];
      }

      View::set('colaborador',$colaborador);
      View::set('rango',$f1 . " - " . $f2);
      View::set('selectPeriodoFechas', $selectPeriodoFechas);
      View::set('header', $this->_contenedor->header($extraHeader));
      View::set('footer', $this->_contenedor->footer($extraFooter));
      View::render("admin_incidencia_colaborador");
    }

    public static function getTablaRangoFechas(){
      $colaborador = AdminIncidenciaDao::getById(MasterDom::getData('colaborador_id'));
      $periodo = AdminIncidenciaDao::getPeriodoById(MasterDom::getData('periodo'));
      $colaborador_id = MasterDom::getData('colaborador_id');
/*
      foreach (AdminIncidenciaDao::getPeriodoFechasProceso(strtoupper($colaborador['pago'])) as $key => $value) {
        $f1 = $value['fecha_inicio'];
        $f2 = $value['fecha_fin'];
        $estatusPeriodo = $value['status'];
      }
*/
      $estatusPeriodo = $periodo['status'];
      $fecha_inicio = new \DateTime($periodo['fecha_inicio']);
      $fecha_final = new \DateTime($periodo['fecha_fin']);
      $dias = array('Monday'=>'Lunes','Tuesday'=>'Martes','Wednesday'=>'Miercoles','Thursday'=>'Jueves','Friday'=>'Viernes','Saturday'=>'Sabado','Sunday'=>'Domingo');

      $incidencias_colaborador = AdminIncidenciaDao::getProrrateoColaboradorIncidenciaById($colaborador['catalogo_colaboradores_id']);
      $datos = new \stdClass();
      $datos->numero_empleado = $colaborador['numero_identificador'];
      $datos->catalogo_colaboradores_id = $colaborador['catalogo_colaboradores_id'];
      $tabla =<<<html
        <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
                  <thead>
                    <tr>
                      <th>Dia</th>
                      <th>Fecha</th>
                      <th>Entrada</th>
                      <th>Salida</th>
                      <th>Entrada Registrada</th>
                      <th>Salida Registrada</th>
                      <th>Comentario</th>
                      <th>Incidencia</th>
                      <th>Acción</th>
                    </tr>
                  </thead>
                <tbody id="registros">
html;

      $dia_aux = '';
      while($fecha_inicio <= $fecha_final){
        $value = '';
        //$bandera_dias_descanso = false;
        $semanal = 0;
        foreach (AdminIncidenciaDao::getHorarioLaboral($datos) as $llave => $valor) {
          if($colaborador['horario_tipo'] == 'semanal'){
            if($semanal != date('W')){
              $semanal = date('W');
              $value = $valor;
              break;
            }
          }elseif ($colaborador['horario_tipo'] == 'diario') {
            if($valor['dia_semana'] == $dias[$fecha_inicio->format('l')]){
              $value = $valor;
              break;
            }
          }
        }

        //$bandera_dias_descanso = true;
        $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' '.AdminIncidencia::restarMinutos($value['hora_entrada'],60);
        $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' '.AdminIncidencia::sumarMinutos($value['hora_entrada'],intval($value['tolerancia_entrada']));
        //$registro_entrada = AdminIncidenciaDao::getAsistencia($datos);
	//SE AGREGA CAMBIOS DEL DIA SE REQUIEREN TODOS LOS DATOS
	$registro_entrada_array = AdminIncidenciaDao::getAsistenciaModificada($datos);

	/*
        $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' '.AdminIncidencia::restarMinutos($value['hora_salida'],240);
        $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' '.AdminIncidencia::sumarMinutos($value['hora_salida'],240);
        $registro_salida = AdminIncidenciaDao::getAsistencia($datos);

        if($registro_entrada['date_check'] != ''){
          $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' 00:00:00';
          $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' 23:59:59';
          $registro_entrada = AdminIncidenciaDao::getAsistenciaOne($datos);
        }

        $registro_entrada['date_check'] = ($registro_entrada['date_check'] != '')? $registro_entrada['date_check'] : 'Sin Registro';
        $registro_salida['date_check'] = ($registro_salida['date_check'] != '')? $registro_salida['date_check'] : 'Sin Registro';
	*/
	
	if(count($registro_entrada_array) >= 1){
	    $registro_entrada = array_shift($registro_entrada_array);
	    if($registro_entrada['date_check'] != ''){
		$registro_entrada = $registro_entrada['date_check'];
	    }else{
		$registro_entrada = 'Sin registro';
	    }

	    if(count($registro_entrada_array)>=1){
	        $registro_salida = array_pop($registro_entrada_array);
		if($registro_salida['date_check'] != '')
		    $registro_salida = $registro_salida['date_check'];
		else
		    $registro_salida = 'Sin registro';
	    }else
		$registro_salida = 'Sin registro';
	}else{
	    $registro_entrada = 'Sin registro';
	    $registro_salida = 'Sin registro';
	}
	
	//$registro_entrada = ($registro_entrada['date_check'] != '') ? $registro_entrada['date_check'] : 'Sin registro';
	//$registro_salida = ($registro_salida['date_check'] != '') ? $registro_salida['date_check'] : 'Sin registro';

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
            <td>$registro_entrada</td>
            <td>$registro_salida</td>
            <td>
html;
        foreach ($incidencias_colaborador as $llave => $valor) {
          if( $fecha_inicio->format('Y-m-d') === $valor['fecha_incidencia']){
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
          <!--div class="form-group"-->
html;

            if($estatusPeriodo == 0){
              $tabla.=<<<html
                <a class="btn btn-primary" href="/AdminIncidencia/add/{$colaborador['catalogo_colaboradores_id']}/{$fecha_inicio->format('Y-m-d')}">
                  <i class="fa fa-plus-square" aria-hidden="true"></i>
                </a>
html;
            }else{
              $tabla.=<<<html
                <a class="btn btn-default" ><i class="fa fa-plus-square" aria-hidden="true"></i></a>
html;
            }
            $datos->fecha = $fecha_inicio->format('Y-m-d');
            if(count(AdminIncidenciaDao::getFechaIncidenciaById($datos)) > 0) {
              if($estatusPeriodo == 0){
                $tabla .= <<<html
                  <a  href="/AdminIncidencia/deleteIncidecia?id={$colaborador['catalogo_colaboradores_id']}&fecha={$fecha_inicio->format('Y-m-d')}" class="btn btn-danger eliminar">
                    <i class="fa fa-trash-o" aria-hidden="true"> </i>
                  </a>
html;
              }else{
                $tabla .= <<<html
                  <a class="btn btn-default eliminar">
                    <i class="fa fa-trash-o" aria-hidden="true"> </i>
                  </a>
html;
              }
            }else{
              $tabla .= <<<html
                        
html;
            }
              $tabla .=<<<html
                <!--/div-->
          </td>
        </tr>
html;
            

        $fecha_inicio->add(new \DateInterval('P1D'));
      }//fin del while rango de fechas
      $tabla .=<<<html
      </tbody>
    </table>
html;

      echo $tabla;
    }

    public function verificarPeriodo(){
        $periodo_id = MasterDom::getData('periodo');
        $periodo = AdminIncidenciaDao::getPeriodoById($periodo_id);
        echo $periodo['status'];
    }

    public function deleteIncidecia(){
      $delete = new \stdClass();
      $delete->_id = MasterDom::getData('id');
      $delete->_fecha = MasterDom::getData('fecha');
      $id = AdminIncidenciaDao::deleteIncidencia($delete);
      if($id>0)
        $this->alerta("", "delete", MasterDom::getData('id'), MasterDom::getData('vista'));
      else
        $this->alerta("", "error", MasterDom::getData('id'), MasterDom::getData('vista'));
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
      $colaborador = AdminIncidenciaDao::getById($id);

      foreach (AdminIncidenciaDao::getPeriodoFechasProcesoBusqueda(strtoupper($colaborador['pago']), $periodo) as $key => $value) {
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
      $incidencias_colaborador = AdminIncidenciaDao::getProrrateoColaboradorIncidenciaById(MasterDom::getData('catalogo_colaboradores_id'));

      $datos = new \stdClass();
      $datos->numero_empleado = MasterDom::getData('numero_empleado');
      $datos->catalogo_colaboradores_id = MasterDom::getData('catalogo_colaboradores_id');
      $datos->catalogo_horario_id = MasterDom::getData('catalogo_horario_id');
      $dia_aux = '';
      while($fecha_inicio <= $fecha_final){
        foreach (AdminIncidenciaDao::getHorarioLaboral($datos) as $key => $value) {
          $diferente = false;
          if($dia_aux != strtolower($value['dia_semana'])){
              $dia_aux = strtolower($value['dia_semana']);
              $diferente = true;
          }
          if(strtolower($value['dia_semana']) == strtolower($dias[$fecha_inicio->format('l')]) && $diferente){

            $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' '.AdminIncidencia::restarMinutos($value['hora_entrada'],60);
            $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' '.AdminIncidencia::sumarMinutos($value['hora_entrada'],intval($value['tolerancia_entrada']));
            $registro_entrada = AdminIncidenciaDao::getAsistencia($datos);

            $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' '.AdminIncidencia::restarMinutos($value['hora_salida'],240);
            $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' '.AdminIncidencia::sumarMinutos($value['hora_salida'],240);
            $registro_salida = AdminIncidenciaDao::getAsistencia($datos);

            if($registro_entrada['date_check'] != ''){
              $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' 00:00:00';
              $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' 23:59:59';
              $registro_entrada = AdminIncidenciaDao::getAsistenciaOne($datos);
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
                <a class="btn btn-primary" href="/AdminIncidencia/add/{$colaborador_id}/{$fecha_inicio->format('Y-m-d')}"><i class="fa fa-plus-square" aria-hidden="true"></i></a>
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
            if(count(AdminIncidenciaDao::getFechaIncidenciaById($datos)) > 0) {
                $tabla .= <<<html
                  <div class="form-group">
html;
              if($estatusPeriodo == 0){
                $tabla .= <<<html
                    <a  href="/AdminIncidencia/deleteIncidecia?id={$id}&fecha={$fecha_inicio->format('Y-m-d')}" class="btn btn-danger">
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
      foreach (AdminIncidenciaDao::getPeriodoFechas(strtoupper($colaborador['pago'])) as $key => $value) {
        
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

    public function alerta($id, $parametro, $colaborador_id, $vista){
      $regreso = "/Incidencia/$vista/";

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
