<?php
namespace App\controllers;
//defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \App\controllers\Contenedor;
use \Core\Controller;
use \App\models\Colaboradores AS ColaboradoresDao;
use \App\models\General AS GeneralDao;

class Colaboradores extends Controller{

  private $_contenedor;

  function __construct(){
    parent::__construct();
    $this->_contenedor = new Contenedor;
    View::set('header',$this->_contenedor->header());
    View::set('footer',$this->_contenedor->footer());
  }

  public function index() {
    $user = GeneralDao::getDatosUsuarioLogeado($this->__usuario);
    //$user = GeneralDao::getDatosUsuarioLogeado("muriza");
    //echo "<pre>"; print_r($user); echo "</pre>";

      /////////////////////////////////////////////////

      $plantilla = ColaboradoresDao::getXochimilco();
      $tabla_1= '';
      foreach ($plantilla as $key => $value) {
          $tabla_1.=<<<html
                <tr>
                    <td>{$value['plantilla_autorizada']}</td>
                    <td>{$value['activos']}</td>
                    <td>{$value['faltantes']}</td>
                    <td><b>{$value['color']}</b></td>
                </tr>
html;
      }

      $plantilla1 = ColaboradoresDao::getVallejo();
      $tabla_2= '';
      foreach ($plantilla1 as $key => $value) {
          $tabla_2.=<<<html
                <tr>
                    <td>{$value['plantilla_autorizada']}</td>
                    <td>{$value['activos']}</td>
                    <td>{$value['faltantes']}</td>
                    <td><b>{$value['color']}</b></td>
                </tr>
html;
      }

      $plantilla2 = ColaboradoresDao::getGATSA();
      $tabla_3= '';
      foreach ($plantilla2 as $key => $value) {
          $tabla_3.=<<<html
                <tr>
                   <td>{$value['plantilla_autorizada']}</td>
                    <td>{$value['activos']}</td>
                    <td>{$value['faltantes']}</td>
                    <td><b>{$value['color']}</b></td>
                </tr>
html;
      }

      $plantilla3= ColaboradoresDao::getUNIDESH();
      $tabla_4= '';
      foreach ($plantilla3 as $key => $value) {
          $tabla_4.=<<<html
                <tr>
                    <td>{$value['plantilla_autorizada']}</td>
                    <td>{$value['activos']}</td>
                    <td>{$value['faltantes']}</td>
                    <td><b>{$value['color']}</b></td>
                </tr>
html;
      }

      $plantilla4 = ColaboradoresDao::getProduccionXochimilco();
      $tabla_5= '';
      foreach ($plantilla4 as $key => $value) {
          $tabla_5.=<<<html
                <tr>
                   <td>{$value['plantilla_autorizada']}</td>
                    <td>{$value['activos']}</td>
                    <td>{$value['faltantes']}</td>
                    <td><b>{$value['color']}</b></td>
                </tr>
html;
      }

      ///////////////////////////////////////////////////////

    $filtros = "";
    if($_POST != "")
      $filtros = $this->getFiltro($post);

    View::set('nomina',$this->getNominas());
    View::set('idPuesto',$this->getPuestos());
    View::set('idEmpresa',$this->getEmpresas());
    View::set('idUbicacion',$this->getUbicacion());
    View::set('idDepartamento',$this->getDepartamentos());
    View::set('tituloColaboradores',$this->getTituloColaboradores($user['perfil_id'], $user['identificador'], $user['catalogo_planta_id'], $user['nombre_departamento']));
    View::set('tabla',$this->getAllColaboradoresAsignados($user['perfil_id'], $user['identificador'], $user['catalogo_planta_id'], $user['catalogo_departamento_id'], $filtros));
    View::set('header',$this->_contenedor->header($this->getHeader()));
    View::set('footer',$this->_contenedor->footer($this->getFooter()));
    View::set('tabla_1',$tabla_1);
    View::set('tabla_2',$tabla_2);
    View::set('tabla_3',$tabla_3);
    View::set('tabla_4',$tabla_4);
    View::set('tabla_5',$tabla_5);
    View::render("colaboradores_all");
  }

  public function getAllColaboradoresAsignados($perfil, $identificador, $planta, $departamento, $filtros){
    $html = "";
    foreach (GeneralDao::getAllColaboradores($perfil, $identificador, $planta, $departamento, $filtros) as $key => $value) {
      $value['apellido_paterno'] = utf8_encode($value['apellido_paterno']);
      $value['apellido_materno'] = utf8_encode($value['apellido_materno']);
      $value['nombre'] = utf8_encode($value['nombre']);

      $value['identificador_noi'] = (!empty($value['identificador_noi'])) ? $value['identificador_noi'] : "SIN<br>IDENTIFICADOR";
      $html .=<<<html
        <tr>
          <td style="text-align:center; vertical-align:middle;"><input type="checkbox" name="borrar[]" value="{$value['catalogo_colaboradores_id']}"/> {$value['catalogo_colaboradores_id']}</td>
          <td style="text-align:center; vertical-align:middle;"><img class="foto" src="/img/colaboradores/{$value['foto']}"/></td>
          <td style="text-align:left; vertical-align:middle;">
            <b># EMPLEADO</b> {$value['numero_empleado']} <br>
            <b># PUESTO</b> {$value['nombre_puesto']}
          </td>
          <td style="text-align:center; vertical-align:middle;"> {$value['apellido_paterno']} <br> {$value['apellido_materno']} <br> {$value['nombre']} </td>
          <td style="text-align:center; vertical-align:middle;"> {$value['nombre_empresa']} </td>
          <td style="text-align:center; vertical-align:middle;"> {$value['nombre_departamento']} </td>
          <td style="text-align:center; vertical-align:middle;"> {$value['pago']} </td>
          <td style="text-align:center; vertical-align:middle;"> {$value['identificador_noi']} </td>
          <td style="text-align:center; vertical-align:middle;">
            <a href="/Colaboradores/edit/{$value['catalogo_colaboradores_id']}" {$editarHidden} type="submit" name="id" class="btn btn-primary"><span class="fa fa-pencil-square-o" style="color:white"></span> </a> <br>
            <a href="/Colaboradores/show/{$value['catalogo_colaboradores_id']}" type="submit" name="id_empresa" class="btn btn-success"><span class="glyphicon glyphicon-eye-open" style="color:white"></span> </a>
          </td>
        </tr>
html;
    }
    return $html;
  }

  public function getTituloColaboradores($perfil, $identificador, $planta, $nombreDepartamento){
    $identificador = explode("_", $identificador);
    $identificador = strtoupper($identificador[0]);
    $titulo = "";
    if($perfil == 1){ // PERFIL ROOT
      $titulo .= "Administra a todos los usuarios";
    }elseif($perfil == 4){ // PERFIL ADMINISTRADOR
      $titulo .= "Administra a todos los usuarios";
    }elseif($perfil == 5){ // PERFIL PERSONALIZADO
      $titulo .= "Administra unicamente a los usuario del departamento de {$nombreDepartamento}";
    }elseif($perfil == 6){ // PERFIL RECURSOS HUMANOS
      if($planta == 1){
        $titulo .= "Administra a todos los usuarios";
      }else{
        $titulo .= "Recursos humanos {$identificador}, Administra a usuarios de {$identificador}";
      }
    }else{ // NO HAY PERFIL
      $titulo .= " -> lo sentimos, no hay ningun perfil asignado para este usuario.";
    }
    return " " . $titulo;
  }

  public function getFiltro($post){
    $datos = array();
    $datos['c.catalogo_empresa_id'] = MasterDom::getData('catalogo_empresa_id');
    $datos['c.catalogo_ubicacion_id'] = MasterDom::getData('catalogo_ubicacion_id');
    $datos['c.catalogo_departamento_id'] = MasterDom::getData('catalogo_departamento_id');
    $datos['c.catalogo_puesto_id'] = MasterDom::getData('catalogo_puesto_id');
    $datos['c.identificador_noi'] = (!empty(MasterDom::getData('status'))) ? MasterDom::getData('status') : "";

    $filtro = '';
    foreach ($datos as $key => $value) {
      if($value!=''){
        if($key == 'c.pago') $filtro .= "AND {$key} = '$value' ";
        else $filtro .= "AND {$key} = '$value' ";
      }
    }
    return $datos;
  }

  public function getEmpresas(){
    $empresas = '';
    foreach (ColaboradoresDao::getIdEmpresa() as $key => $value) {
      $empresas .=<<<html
      <option value="{$value['catalogo_empresa_id']}">{$value['nombre']}</option>
html;
    }
    return $empresas;
  }

  public function getUbicacion(){
    $ubicaciones = '';
      foreach (ColaboradoresDao::getIdUbicacion() as $key => $value) {
        $ubicaciones .=<<<html
        <option value="{$value['catalogo_ubicacion_id']}">{$value['nombre']}</option>
html;
    }
    return $ubicaciones;
  }

  public function getDepartamentos(){
    $departamentos = "";
    foreach (ColaboradoresDao::getIdDepartamento() as $key => $value) {
      $departamentos .=<<<html
      <option value="{$value['catalogo_departamento_id']}">{$value['nombre']}</option>
html;
    }
    return $departamentos;
  }

  public function getPuestos(){
    $puestos = '';
    foreach (ColaboradoresDao::getIdPuesto() as $key => $value) {
      $puestos .=<<<html
      <option value="{$value['catalogo_puesto_id']}">{$value['nombre']}</option>
html;
    }
    return $puestos;
  }

  public function getNominas(){
    $nomina = "";
    foreach (ColaboradoresDao::getNominaIdentificador() as $key => $value) {
      if(!empty($value['identificador_noi'])){
        $nomina .=<<<html
        <option value="{$value['identificador_noi']}">NOMINA NOI {$value['identificador_noi']}</option>
html;
        }else{
        $nomina .=<<<html
        <option value="vacio">SIN NOMINA NOI</option>
html;
      }
    }
    return $nomina;
  }

  /*  =========================================================== */

    public function index1() {

      $extraHeader=<<<html
      <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css" />
      <style>
        .incentivo{margin: 2px;font: message-box;height:100%;}
        .foto{width:100px;height:100px;border-radius: 50px;}
      </style>
html;

      $extraFooter =<<<html
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
            } );

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


           $("#btnExcel").click(function(){
              $('#all').attr('action', '/Colaboradores/generarExcel/');
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });

            $("#btnPDF").click(function(){
              $('#all').attr('action', '/Colaboradores/generarPDF/');
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });

            $("#delete").click(function(){
              var seleccionados = $("input[name='borrar[]']:checked").length;
              if(seleccionados>0){
                alertify.confirm('¿Segúro que desea eliminar lo seleccionado?', function(response){
                  if(response){
                    $('#all').attr('action', '/Colaboradores/delete');
                    $('#all').attr('target', '');
                    $("#all").submit();
                    alertify.success("Se ha eliminado correctamente");
                   }
                });
              }else{
                alertify.confirm('Selecciona al menos uno para eliminar');
              }
            });

            /*$("select").change(function(){
              $.ajax({
                url: "/Colaboradores/getTabla",
                type: "POST",
                data: $("#all").serialize(),
                success: function(data){
                  $("#registros").html(data);
                }
              });
            });*/



        });
      </script>
html;

      $extraFooter1 =<<<html
      <script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
      <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
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


          //$("#muestra-cupones").tablesorter();

          /*var oTable = $('#muestra-cupones').DataTable({
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

            $('#muestra-cupones input[type=search]').keyup( function () {
                var table = $('#example').DataTable();
                table.search(
                    jQuery.fn.DataTable.ext.type.search.html(this.value)
                ).draw();
            });*/



          /*$("#muestra-cupones").tablesorter();
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
            } );*/

            $("#delete").click(function(){
              var seleccionados = $("input[name='borrar[]']:checked").length;
              if(seleccionados>0){
                alertify.confirm('¿Segúro que desea eliminar lo seleccionado?', function(response){
                  if(response){
                    $('#all').attr('action', '/Colaboradores/delete');
                    $('#all').attr('target', '');
                    $("#all").submit();
                    alertify.success("Se ha eliminado correctamente");
                   }
                });
              }else{
                alertify.confirm('Selecciona al menos uno para eliminar');
              }
            });

            $("#btnExcel").click(function(){
              $('#all').attr('action', '/Colaboradores/generarExcel/');
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });

            $("#btnPDF").click(function(){
              $('#all').attr('action', '/Colaboradores/generarPDF/');
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });

            $("#btnReiniciar").click(function(){
              $.ajax({
                url: "/Colaboradores/getTabla",
                type: "POST",
                data: "",
                success: function(data){
                  $("#registros").html(data);
                }
              });
            });

            $("select").change(function(){
              $.ajax({
                url: "/Colaboradores/getTabla",
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

      $usuario = $this->__usuario;
      $admin = ColaboradoresDao::getDatosUsuarioLogeado($usuario);

      $editarHidden = (Controller::getPermisosUsuario($usuario, "seccion_colaboradores", 5)==1)?  "" : "style=\"display:none;\"";
      $eliminarHidden = (Controller::getPermisosUsuario($usuario, "seccion_colaboradores", 6)==1)? "" : "style=\"display:none;\"";
      $pdfHidden = (Controller::getPermisosUsuario($usuario, "seccion_colaboradores", 2)==1)?  "" : "style=\"display:none;\"";
      $excelHidden = (Controller::getPermisosUsuario($usuario, "seccion_colaboradores", 3)==1)? "" : "style=\"display:none;\"";
      $agregarHidden = (Controller::getPermisosUsuario($usuario, "seccion_colaboradores", 4)==1)? "" : "style=\"display:none;\"";
      View::set('pdfHidden',$pdfHidden);
      View::set('excelHidden',$excelHidden);
      View::set('agregarHidden',$agregarHidden);
      View::set('editarHidden',$editarHidden);
      View::set('eliminarHidden',$eliminarHidden);

      //$datosUsuario = ColaboradoresDao::GeneralDao($this->__usuario);
    $datosUsuario = GeneralDao::getDatosUsuario($this->__usuario);

    if($datosUsuario['perfil_id'] == 1 || $datosUsuario['perfil_id'] == 4){
      $accion = 2;
    }

    if($datosUsuario['perfil_id'] == 6){
      if($datosUsuario['catalogo_planta_id]'] != 1){
        $accion = 6; // RH es diferente a RH xochimilco
      }else{
        $accion = 2;
      }
    }

    $datos = array();
      $datos['e.catalogo_empresa_id'] = MasterDom::getData('catalogo_empresa_id');
      $datos['u.catalogo_ubicacion_id'] = MasterDom::getData('catalogo_ubicacion_id');
      $datos['d.catalogo_departamento_id'] = MasterDom::getData('catalogo_departamento_id');
      $datos['p.catalogo_puesto_id'] = MasterDom::getData('catalogo_puesto_id');
      $datos['c.identificador_noi'] = (!empty(MasterDom::getData('status'))) ? MasterDom::getData('status') : "";

      $filtro = '';
      foreach ($datos as $key => $value) {
        if($value!=''){
          if($key == 'c.pago'){
            //$filtro .= 'AND '.$key." = '{$value}' ";
            $filtro .= "AND {$key} = '$value' ";
          }else{
            //$filtro .= 'AND '.$key." = $value ";
            $filtro .= "AND {$key} = '$value' ";
          }
        }
      }
    $tabla = '';
      foreach (ColaboradoresDao::getAllColaboradores($datosUsuario['perfil_id'], $datosUsuario['catalogo_planta_id'], $datosUsuario['catalogo_departamento_id'], $accion, $value['catalogo_departamento_id'], $admin['nombre_planta'], $admin['usuario'], $admin['perfil_id'], 1, $filtro) as $key => $value) {

        $value['nombre'] = utf8_encode($value['nombre']);
        $value['apellido_paterno'] = utf8_encode($value['apellido_paterno']);
        $value['apellido_materno'] = utf8_encode($value['apellido_materno']);
        $value['identificador_noi'] = ($value['identificador_noi'] != '') ? $value['identificador_noi'] : "SIN IDENTIFICADOR";

        $tabla .=<<<html
          <tr>
            <td {$editarHidden} style="text-align:center; vertical-align:middle;"><input type="checkbox" name="borrar[]" value="{$value['catalogo_colaboradores_id']}"/></td>
            <td style="text-align:center; vertical-align:middle;"><img class="foto" src="/img/colaboradores/{$value['foto']}"/></td>
            <td style="text-align:center; vertical-align:middle;">
              <b># EMPLEADO</b> {$value['numero_empleado']} <br>
              <b># PUESTO</b> {$value['nombre_puesto']}
            </td>
            <td style="text-align:center; vertical-align:middle;">{$value['nombre']} {$value['apellido_paterno']} {$value['apellido_materno']}</td>
            <td style="text-align:center; vertical-align:middle;">{$value['nombre_empresa']}</td>
            <td style="text-align:center; vertical-align:middle;">{$value['nombre_departamento']}</td>
            <td style="text-align:center; vertical-align:middle;"> {$value['pago']} </td>
            <td style="text-align:center; vertical-align:middle;"> {$value['identificador_noi']} </td>
            <td style="text-align:center; vertical-align:middle;">
                        <a href="/Colaboradores/edit/{$value['catalogo_colaboradores_id']}" {$editarHidden} type="submit" name="id" class="btn btn-primary"><span class="fa fa-pencil-square-o" style="color:white"></span> </a> <br>
                        <a href="/Colaboradores/show/{$value['catalogo_colaboradores_id']}" type="submit" name="id_empresa" class="btn btn-success"><span class="glyphicon glyphicon-eye-open" style="color:white"></span> </a>
                    </td>
          </tr>
html;
      }

      $sStatus = "";
      foreach (ColaboradoresDao::getStatus() as $key => $value) {
        $sStatus .=<<<html
        <option value="{$value['catalogo_status_id']}">{$value['nombre']}</option>
html;
      }

      $idDepartamento = "";
      foreach (ColaboradoresDao::getIdDepartamento() as $key => $value) {
        $idDepartamento .=<<<html
        <option value="{$value['catalogo_departamento_id']}">{$value['nombre']}</option>
html;
      }

      $idEmpresa = '';
      foreach (ColaboradoresDao::getIdEmpresa() as $key => $value) {
        $idEmpresa .=<<<html
        <option value="{$value['catalogo_empresa_id']}">{$value['nombre']}</option>
html;
      }

      $idUbicacion = '';
      foreach (ColaboradoresDao::getIdUbicacion() as $key => $value) {
        $idUbicacion .=<<<html
        <option value="{$value['catalogo_ubicacion_id']}">{$value['nombre']}</option>
html;
      }

      $idPuesto = '';
      foreach (ColaboradoresDao::getIdPuesto() as $key => $value) {
        $idPuesto .=<<<html
        <option value="{$value['catalogo_puesto_id']}">{$value['nombre']}</option>
html;
      }

      $nomina = "";
      foreach (ColaboradoresDao::getNominaIdentificador() as $key => $value) {
        if(!empty($value['identificador_noi'])){
          $nomina .=<<<html
            <option value="{$value['identificador_noi']}">NOMINA NOI {$value['identificador_noi']}</option>
html;
        }else{
          $nomina .=<<<html
            <option value="vacio">SIN NOMINA NOI</option>
html;
        }

      }

      View::set('nomina', $nomina);
      View::set('sStatus',$sStatus);
      View::set('idEmpresa', $idEmpresa);
      View::set('idUbicacion', $idUbicacion);
      View::set('idDepartamento', $idDepartamento);
      View::set('idPuesto', $idPuesto);
      View::set('idHorario', $idHorario);
      View::set('idIncentivo', $idIncentivo);
      View::set('sPago', $sPago);
      View::set('tabla',$tabla);
      View::set('header',$this->_contenedor->header($extraHeader));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("colaboradores_all");
    }

    public function colaboradoresPropios() {

      $extraHeader=<<<html
      <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css" />
      <style>
        .incentivo{margin: 2px;font: message-box;height:100%;}
        .foto{width:100px;height:100px;border-radius: 50px;}
      </style>
html;
      $extraFooter =<<<html
      <script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
      <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
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
                table.search(
                    jQuery.fn.DataTable.ext.type.search.html(this.value)
                ).draw();
            } );

            $("#delete").click(function(){
              var seleccionados = $("input[name='borrar[]']:checked").length;
              if(seleccionados>0){
                alertify.confirm('¿Segúro que desea eliminar lo seleccionado?', function(response){
                  if(response){
                    $('#all').attr('action', '/Colaboradores/delete');
                    $('#all').attr('target', '');
                    $("#all").submit();
                    alertify.success("Se ha eliminado correctamente");
                   }
                });
              }else{
                alertify.confirm('Selecciona al menos uno para eliminar');
              }
            });

            $("#btnExcel").click(function(){
              $('#all').attr('action', '/Colaboradores/generarExcel/');
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });

            $("#btnPDF").click(function(){
              $('#all').attr('action', '/Colaboradores/generarPDF/');
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });

            $("#btnReiniciar").click(function(){
              $.ajax({
                url: "/Colaboradores/getTabla",
                type: "POST",
                data: "",
                success: function(data){
                  $("#registros").html(data);
                }
              });
            });

            $("select").change(function(){
              $.ajax({
                url: "/Colaboradores/getTabla",
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

      $usuario = $this->__usuario;
      $admin = ColaboradoresDao::getDatosUsuarioLogeado($usuario);

      $editarHidden = (Controller::getPermisosUsuario($usuario, "seccion_colaboradores", 5)==1)?  "" : "style=\"display:none;\"";
      $eliminarHidden = (Controller::getPermisosUsuario($usuario, "seccion_colaboradores", 6)==1)? "" : "style=\"display:none;\"";
      $pdfHidden = (Controller::getPermisosUsuario($usuario, "seccion_colaboradores", 2)==1)?  "" : "style=\"display:none;\"";
      $excelHidden = (Controller::getPermisosUsuario($usuario, "seccion_colaboradores", 3)==1)? "" : "style=\"display:none;\"";
      $agregarHidden = (Controller::getPermisosUsuario($usuario, "seccion_colaboradores", 4)==1)? "" : "style=\"display:none;\"";
      View::set('pdfHidden',$pdfHidden);
      View::set('excelHidden',$excelHidden);
      View::set('agregarHidden',$agregarHidden);
      View::set('editarHidden',$editarHidden);
      View::set('eliminarHidden',$eliminarHidden);

      /*$datosUsuario = ColaboradoresDao::getDatosUsuarioLogeado($this->__usuario);
      $secciones = ColaboradoresDao::getDepartamentos($datosUsuario['administrador_id']);
      $datosUsuario = ColaboradoresDao::getDatosUsuarioLogeado($this->__usuario);*/

      $datosUsuario = GeneralDao::getDatosUsuario($this->__usuario);

    $accion = 4; // ES PARA PROPIOS DE RH O ROOT
    $tabla = '';
      foreach (ColaboradoresDao::getAllColaboradores($datosUsuario['perfil_id'], $datosUsuario['catalogo_planta_id'], $datosUsuario['catalogo_departamento_id'], $accion, $value['catalogo_departamento_id'], $admin['nombre_planta'], $admin['usuario'], $admin['perfil_id'], 2) as $key => $value) {
        $tabla .=<<<html
          <tr>
            <td {$editarHidden} style="text-align:center; vertical-align:middle;"><input type="checkbox" name="borrar[]" value="{$value['catalogo_colaboradores_id']}"/></td>
            <td style="text-align:center; vertical-align:middle;"><img class="foto" src="/img/colaboradores/{$value['foto']}"/></td>
            <td style="text-align:center; vertical-align:middle;">{$value['numero_empleado']}</td>
            <td style="text-align:center; vertical-align:middle;">{$value['nombre']} {$value['apellido_paterno']} {$value['apellido_materno']}</td>
            <td style="text-align:center; vertical-align:middle;">{$value['nombre_empresa']}</td>
            <td style="text-align:center; vertical-align:middle;">{$value['nombre_departamento']}</td>
            <td style="text-align:center; vertical-align:middle;"> {$value['status']} </td>
            <td class="center">
                        <a href="/Colaboradores/edit/{$value['catalogo_colaboradores_id']}" {$editarHidden} type="submit" name="id" class="btn btn-primary"><span class="fa fa-pencil-square-o" style="color:white"></span> </a>
                        <a href="/Colaboradores/show/{$value['catalogo_colaboradores_id']}" type="submit" name="id_empresa" class="btn btn-success"><span class="glyphicon glyphicon-eye-open" style="color:white"></span> </a>
                    </td>
          </tr>
html;
    }

      $sStatus = "";
      foreach (ColaboradoresDao::getStatus() as $key => $value) {
        $sStatus .=<<<html
        <option value="{$value['catalogo_status_id']}">{$value['nombre']}</option>
html;
      }

      $idDepartamento = "";
      foreach (ColaboradoresDao::getIdDepartamento() as $key => $value) {
        $idDepartamento .=<<<html
        <option value="{$value['catalogo_departamento_id']}">{$value['nombre']}</option>
html;
      }

      $idEmpresa = '';
      foreach (ColaboradoresDao::getIdEmpresa() as $key => $value) {
        $idEmpresa .=<<<html
        <option value="{$value['catalogo_empresa_id']}">{$value['nombre']}</option>
html;
      }

      $idUbicacion = '';
      foreach (ColaboradoresDao::getIdUbicacion() as $key => $value) {
        $idUbicacion .=<<<html
        <option value="{$value['catalogo_ubicacion_id']}">{$value['nombre']}</option>
html;
      }

      $idPuesto = '';
      foreach (ColaboradoresDao::getIdPuesto() as $key => $value) {
        $idPuesto .=<<<html
        <option value="{$value['catalogo_puesto_id']}">{$value['nombre']}</option>
html;
      }
      View::set('sStatus',$sStatus);
      View::set('idEmpresa', $idEmpresa);
      View::set('idUbicacion', $idUbicacion);
      View::set('idDepartamento', $idDepartamento);
      View::set('idPuesto', $idPuesto);
      View::set('idHorario', $idHorario);
      View::set('idIncentivo', $idIncentivo);
      View::set('sPago', $sPago);
      View::set('tabla',$tabla);
      View::set('header',$this->_contenedor->header($extraHeader));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("colaboradores_all");
    }

    public function getTabla(){
      $datos = array();
      $datos['e.catalogo_empresa_id'] = MasterDom::getData('catalogo_empresa_id');
      $datos['u.catalogo_ubicacion_id'] = MasterDom::getData('catalogo_ubicacion_id');
      $datos['d.catalogo_departamento_id'] = MasterDom::getData('catalogo_departamento_id');
      $datos['p.catalogo_puesto_id'] = MasterDom::getData('catalogo_puesto_id');
      $datos['c.pago'] = MasterDom::getData('status');

      $filtro = '';
      foreach ($datos as $key => $value) {
        if($value!=''){
          if($key == 'c.pago'){
            $filtro .= 'AND '.$key." = '{$value}' ";
          }else{
            $filtro .= 'AND '.$key." = $value ";
          }
        }
      }

      $tabla= '';
      foreach (ColaboradoresDao::getAllReporte($filtro) as $key => $value) {
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
                  <td>{$value['pago']}</td>
                  <td class="center" {$editarHidden}>
                        <a href="/Colaboradores/edit/{$value['catalogo_colaboradores_id']}" type="submit" name="id" class="btn btn-primary"><span class="fa fa-pencil-square-o" style="color:white"></span> </a>
                        <a href="/Colaboradores/show/{$value['catalogo_colaboradores_id']}" type="submit" name="id_empresa" class="btn btn-success"><span class="glyphicon glyphicon-eye-open" style="color:white"></span> </a>
                    </td>
                </tr>
html;
      }
      echo $tabla;
    }

    public function add(){
      $extraHeader =<<<html
      <link href="/css/bootstrap-datetimepicker.css" rel="stylesheet">
      <style>
        .incentivo{ margin: 2px; background-color: #18bf7f; font: message-box; height:25px; -webkit-box-shadow: 9px 13px 23px -9px #18bf7f; -moz-box-shadow: 9px 13px 23px -9px #18bf7f; box-shadow: 9px 13px 23px -9px #18bf7f;}
        .cerrar{padding: 3px;}
        .incentivo:hover{background-color: #c9069b;-webkit-box-shadow: 9px 13px 23px -9px #c9069b;-moz-box-shadow: 9px 13px 23px -9px #c9069b;box-shadow: 9px 13px 23px -9px #c9069b;}
        .foto{ width:150px; height:150px; border-radius: 50px; margin:10px; float:left;}
        .btn span.glyphicon { opacity: 0;}
        .btn.active span.glyphicon { opacity: 1;}
      </style>

      <link href="/css/datetime.css" rel="stylesheet">
      <link href='http://fonts.googleapis.com/css?family=Roboto:400,500' rel='stylesheet' type='text/css'>
      <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
html;
      $extraFooter =<<<html
      <script src="/js/moment/moment.min.js"></script>
      <script src="/js/datepicker/scriptdatepicker.js"></script>
      <script src="/js/datepicker/datepicker2.js"></script>
      <script type="text/javascript" src="/js/bootstrap-material-datetimepicker.js"></script>

      <script>
        $(document).ready(function(){

            $("#id_catalogo_lector").change("Act", function(value, element) {
                if ($(this).val() === "0") {

                    $("#numero_identificacion").prop("disabled", true);
                    document.getElementById("numero_identificacion").value = "";
                     $('#availability').html('<span class="text-success"></span></span>');


                } else {
                    $("#numero_identificacion").prop("disabled", false);
                    document.getElementById("numero_identificacion").value = "";
                    $('#availability').html('<span class="text-success"></span></span>');
                     $('#btnAdd').attr("disabled", false);
                }
            });

            $.validator.addMethod("checkPlantilla", function(value, element) {
              var result = false;
              $.ajax({
                type:"POST",
                async: false,
                url: "/Colaboradores/isValidatePlantilla", // script to validate in server side
                data:  {
                    id_catalogo_lector: function()
                    {
                      return $("#id_catalogo_lector").val();
                    }
                    },
                success: function(data) {
                    console.log("success::: " + data);
                    result = (data == "true") ? false : true;

                    if(result == true){
                      $('#availability1').html('<span class="text-success glyphicon glyphicon-ok"></span><span> Plantilla con Espacios Disponibles</span>');
                      $('#btnAdd').attr("disabled", false);
                    }else{
                      $('#availability1').html('<span class="text-danger glyphicon glyphicon-remove"></span>');
                      $('#btnAdd').attr("disabled", true);
                       $("#numero_identificacion").prop("disabled", true);
                    document.getElementById("numero_identificacion").value = "";
                     $('#availability').html('<span class="text-success"></span></span>');

                    }
                }
              });
              // return true if username is exist in database
              return result;
              },
              "¡La Plantilla esta Completa para la Unidad, Contacte a Sistemas!"
          );

          $.validator.addMethod("checkUserName", function(value, element) {
              var result = false;
              $.ajax({
                type:"POST",
                async: false,
                url: "/Colaboradores/isValidateUser", // script to validate in server side
                data:  {
                    numero_identificacion: function() {
                      return $("#numero_identificacion").val();
                    }, id_catalogo_lector: function() {
                      return $("#id_catalogo_lector").val();
                    }
                    },
                success: function(data) {
                    console.log("success::: " + data);
                    result = (data == "true") ? false : true;

                    if(result == true){
                      $('#availability').html('<span class="text-success glyphicon glyphicon-ok"></span><span> Número de identificación Disponible</span>');
                      $('#btnAdd').attr("disabled", false);
                    }else{
                      $('#availability').html('<span class="text-danger glyphicon glyphicon-remove"></span>');
                      $('#btnAdd').attr("disabled", true);
                    }
                }
              });
              // return true if username is exist in database
              return result;
              },
              "¡Este número de identificación ya pertenece a otro Colaborador! Prueba otro."
          );

          $(".check").change(function(){
            var incentivo = $(this);

             if (!incentivo.is(':checked')) {
              incentivo.attr("cantidad",0);
              $("#texto_"+incentivo.val()).html($("#texto_"+incentivo.val()).attr("texto_default"));
              $("#cantidad_"+incentivo.val()).attr("value",0);
             }else{
                var mensaje = alertify.prompt('Introduce la cantidad del incentivo:',
                function(evt,value){
                  incentivo.attr("cantidad",value);
                  $("#cantidad_"+incentivo.val()).attr("value",value);
                  $("#texto_"+incentivo.val()).html($("#texto_"+incentivo.val()).attr("texto_default")+" : $"+value);
                }
              );
              mensaje.set('type', 'text');
             }
          });

          $('#fecha_alta').daterangepicker({
              singleDatePicker: true,
              calender_style: "picker_1"
            }, function(start, end, label) {
              console.log(start.toISOString(), end.toISOString(), label);
          });

          $('#fecha_baja').daterangepicker({
              singleDatePicker: true,
              calender_style: "picker_1"
            }, function(start, end, label) {
              console.log(start.toISOString(), end.toISOString(), label);
          });

          $("#add").validate({
            rules:{
              nombre:{
                required: true
              },
              numero_identificacion:{
                 checkUserName: true,
                  required: true
              },
              genero:{
                required: true
              },
              id_catalogo_empresa:{
                required: true
              },
              id_catalogo_ubicacion:{
                required: true
              },
	            id_catalogo_lector:{
                checkPlantilla: true,
                required: true
              },
              id_catalogo_departamento:{
                required: true
              },
              id_catalogo_puesto:{
                required: true
              },
              rfc:{
                required: true
              },
              status:{
                required: true
              },
              fecha_alta:{
                required: true
              },
              pago:{
                required: true
              },
              incentivo:{
                required: true
              },
              tipo_horario:{
                required: true
              }
            },
            messages:{
              nombre:{
                required: "Este campo es requerido"
              },
              apellido_paterno:{
                required: "Este campo es requerido"
              },
              apellido_materno:{
                required: "Este campo es requerido"
              },
              numero_identificacion:{
                required: "Este campo es requerido"
              },
              genero:{
                required: "Este campo es requerido"
              },
              id_catalogo_empresa:{
                required: "Este campo es requerido"
              },
              id_catalogo_ubicacion:{
                required: "Este campo es requerido"
              },
              id_catalogo_departamento:{
                required: "Este campo es requerido"
              },
              id_catalogo_puesto:{
                required: "Este campo es requerido"
              },
	            id_catalogo_lector:{
                required: "Este campo es requerido"
              },
              rfc:{
                required: "Este campo es requerido"
              },
              status:{
                required: "Este campo es requerido"
              },
              fecha_alta:{
                required: "Este campo es requerido"
              },
              pago:{
                required: "Este campo es requerido"
              },
              incentivo:{
                required: "Este campo es requerido"
              },
              tipo_horario:{
                required: "Este campo es requerido"
              }
            }
          });//fin del jquery validate

          jQuery.validator.addMethod("foto", function(value, element) {
              $.extend(jQuery.validator.messages, {
                foto: "Formato de la foto invalida solo se acepta (jpg/jpeg/png)"
              });
              if(value!="" && value.match(/^.*.(jpg|JPG|jpeg|JPEG|png|PNG)$/)){
                if(element.files[0].size<1000000){
                  return true;
                }else{
                   $.extend(jQuery.validator.messages, {
                     foto: "El tamaño de la foto debe ser menor a 1 MB"
                   });
                  return false;
                }
              }else{
                  return false;
              }
          }, jQuery.validator.format("Formato de la foto invalida solo se acepta (jpg/jpeg/png)"));

          jQuery.validator.addMethod("rfc", function(value, element) {
              if(value=="" || value.match(/^([A-Za-z&]{3,4})([0-9]{2})(0[1-9]|1[0-2])(0[1-9]|1[0-9]|2[0-9]|3[0-1])([A-Za-z\d]{3})?$/)){
                return true;
              }else{
                return false;
              }
          }, jQuery.validator.format("Fotmato RFC invalido"));

      	  //horario default
       	  jQuery.validator.addMethod("horario_metodo", function(value, element) {
              if(value=="" || value=="-1"){
                return false;
              }else{
                return true;
              }
          }, jQuery.validator.format("Se requiere por lo menos el horario default"));

          //MRR
          $("#id_catalogo_lector").change(function(){
            let clp = parseInt($(this).val());
            if (clp == 3 || clp == 4) {
              $("#inc").hide();
            }
            else {
              $("#inc").show();
            }
          });

	        $("#horario_1").rules("add", {
            required: true,
            horario_metodo: true
          });

          $("#btnCancel").click(function(){
            window.location.href = "/Colaboradores/";
          });//fin del btnAdd

          $("#id_catalogo_ubicacion").change(function(){
            $("#letra_ubicacion").val(($("#id_catalogo_ubicacion option:selected").text()[0]).toUpperCase());
            //$("#numero_identificacion").val( ($("#id_catalogo_ubicacion option:selected").text()[0]).toUpperCase() );
          });

          $("#foto").change(function(e){
            Archivos = jQuery('#foto')[0].files;
            Lector = new FileReader();
        		Lector.onloadend = function(e){
        			var origen,tipo;
        			//Envía la imagen a la pantalla
        			origen = e.target; //objeto FileReader
        			jQuery('#vista_previa').attr('src', origen.result);
        		};
        		Lector.onerror = function(e){
        			console.log(e)
        		}
        		Lector.readAsDataURL(Archivos[0]);

            $("#vista_previa").attr("src","");
          });

          $("#catalogos").change(function(){
            if($("#catalogos option").length>1){
              $("#btnHorarioAdd").show();
            }
          });

          $("#btnIncentivoAdd").click(function(){
            var id = $("#incentivos").val();
            if(id!=""){
              var nombre = $("#incentivos option:selected").text();
              var color = $("#incentivos option:selected").attr("color");

              var html = '<div class="col-md-4 col-sm-4 col-xs-4" id="incentivo_asignado_'+id+'" style="background-color: '+color+'; border-radius:10px; margin:5px; justify-content: center;">';
              html += '<input type="text" name="cantidad_'+id+'" id="cantidad_'+id+'" class="col-md-9 col-sm-9 col-xs-9" style="margin-right:10px;margin-top:10px; border-radius:10px; border-color: white;" placeholder="Cantidad">';
              html += '<i class="fa fa-times-circle-o cerrar col-md-1 col-sm-1 col-xs-1" id="'+id+'" nombre="'+nombre+'" color="'+color+'" style="font-size: 18px; margin-top:3px; margin-left:5px; color:#C9302C;"></i>';
              html += '<div class="col-md-11 col-sm-11 col-xs-11" style="justify-content: center;margin-top:10px;">';
              html += '<input class="checkbox checkbox-circle col-md-2 col-sm-2 col-xs-2" type="checkbox" name="incentivo[]" value="'+id+'" checked="checked" />';
              html += '<label class="control-label col-md-8 col-sm-8 col-xs-8" style="text-align: center;color:#000000;" >'+nombre+'</label>';
              html += '</div>';
              html += '</div>';

              //$("#incentivos_asignados").html($("#incentivos_asignados").html()+html);
	            $("#incentivos_asignados").append(html);
              $("#incentivos option:selected").remove();

              if($("#incentivos option").length<=1){
                $("#btnIncentivoAdd").hide();
              }
            }
          });

          $(document).on("click","i.cerrar",function(event){
            var id = $(this).attr("id");
            var nombre = $(this).attr("nombre");
            var color = $(this).attr("color");
            $("#incentivo_asignado_"+id).remove();
            $("#incentivos").append('<option value="'+id+'" color="'+color+'">'+nombre+'</option>');
            if($("#incentivos option").length>1){
              $("#btnIncentivoAdd").show();
            }
          });

          var llave_1 = -1 ;
          var llave_2 = -1 ;
          var llave_3 = -1 ;
          var llave_4 = -1 ;

/*HORARIO 1 DEFAULT*/
          $("#horario_1").change(function(){
            var llave = $("#horario_1 option:selected").attr("llave");
            $("#horario_2 [llave="+llave+"]").remove();
            $("#horario_3 [llave="+llave+"]").remove();
            $("#horario_4 [llave="+llave+"]").remove();

            if(llave_1>=0){
              var id = $("#horario_1 [llave="+llave_1+"]").val();
              var nombre = $("#horario_1 [llave="+llave_1+"]").text();

              $("#horario_2").append('<option value="'+id+'" llave="'+llave_1+'">'+nombre+'</option>');
              $("#horario_3").append('<option value="'+id+'" llave="'+llave_1+'">'+nombre+'</option>');
              $("#horario_4").append('<option value="'+id+'" llave="'+llave_1+'">'+nombre+'</option>');

              //alert(id+"-"+nombre+"-"+llave_select);
            }

            llave_1 = llave;
          });

/*HORARIO 2*/
          $("#horario_2").change(function(){
            var llave = $("#horario_2 option:selected").attr("llave");
            $("#horario_1 [llave="+llave+"]").remove();
            $("#horario_3 [llave="+llave+"]").remove();
            $("#horario_4 [llave="+llave+"]").remove();

            if(llave_2>=0){
              var id = $("#horario_2 [llave="+llave_2+"]").val();
              var nombre = $("#horario_2 [llave="+llave_2+"]").text();

              $("#horario_1").append('<option value="'+id+'" llave="'+llave_2+'">'+nombre+'</option>');
              $("#horario_3").append('<option value="'+id+'" llave="'+llave_2+'">'+nombre+'</option>');
              $("#horario_4").append('<option value="'+id+'" llave="'+llave_2+'">'+nombre+'</option>');

              //alert(id+"-"+nombre+"-"+llave_select);
            }

            llave_2 = llave;
          });

/*HORARIO 3*/
          $("#horario_3").change(function(){
            var llave = $("#horario_3 option:selected").attr("llave");
            $("#horario_1 [llave="+llave+"]").remove();
            $("#horario_2 [llave="+llave+"]").remove();
            $("#horario_4 [llave="+llave+"]").remove();

            if(llave_3>=0){
              var id = $("#horario_3 [llave="+llave_3+"]").val();
              var nombre = $("#horario_3 [llave="+llave_3+"]").text();

              $("#horario_1").append('<option value="'+id+'" llave="'+llave_3+'">'+nombre+'</option>');
              $("#horario_2").append('<option value="'+id+'" llave="'+llave_3+'">'+nombre+'</option>');
              $("#horario_4").append('<option value="'+id+'" llave="'+llave_3+'">'+nombre+'</option>');

              //alert(id+"-"+nombre+"-"+llave_select);
            }

            llave_3 = llave;
          });
/*HORARIO 4*/
          $("#horario_4").change(function(){
            var llave = $("#horario_4 option:selected").attr("llave");
            $("#horario_1 [llave="+llave+"]").remove();
            $("#horario_2 [llave="+llave+"]").remove();
            $("#horario_3 [llave="+llave+"]").remove();

            if(llave_4>=0){
              var id = $("#horario_4 [llave="+llave_4+"]").val();
              var nombre = $("#horario_4 [llave="+llave_4+"]").text();

              $("#horario_1").append('<option value="'+id+'" llave="'+llave_4+'">'+nombre+'</option>');
              $("#horario_2").append('<option value="'+id+'" llave="'+llave_4+'">'+nombre+'</option>');
              $("#horario_3").append('<option value="'+id+'" llave="'+llave_4+'">'+nombre+'</option>');

              //alert(id+"-"+nombre+"-"+llave_select);
            }

            llave_4 = llave;
          });


        });//fin del document.ready
      </script>

    <script type="text/javascript" src="/js/moment-with-locales.min.js"></script>
    <script type="text/javascript">
      $(document).ready(function(){
        $('#date-new1').bootstrapMaterialDatePicker
        ({
          time: false,
          clearButton: true
        });

        $('#date-new2').bootstrapMaterialDatePicker
        ({
          time: false,
          clearButton: true
        });

        $('#time').bootstrapMaterialDatePicker
        ({
          date: false,
          shortTime: false,
          format: 'HH:mm'
        });

        $('#date-format').bootstrapMaterialDatePicker
        ({
          format: 'dddd DD MMMM YYYY - HH:mm'
        });
        $('#date-fr').bootstrapMaterialDatePicker
        ({
          format: 'DD/MM/YYYY HH:mm',
          lang: 'es',
          weekStart: 1,
          cancelText : 'ANNULER',
          nowButton : true,
          switchOnClick : true
        });

        $('#date-end').bootstrapMaterialDatePicker
        ({
          weekStart: 0, format: 'DD/MM/YYYY HH:mm'
        });
        $('#date-start').bootstrapMaterialDatePicker
        ({
          weekStart: 0, format: 'DD/MM/YYYY HH:mm', shortTime : true
        }).on('change', function(e, date)
        {
          $('#date-end').bootstrapMaterialDatePicker('setMinDate', date);
        });

        $('#min-date').bootstrapMaterialDatePicker({ format : 'DD/MM/YYYY HH:mm', minDate : new Date() });

        $.material.init()
      });
    </script>

html;
      $colaborador_existente = MasterDom::getDataAll('colaborador_id');
      if($colaborador_existente!=''){
        $colaborador_existente = ColaboradoresDao::getOperacionNoiId($colaborador_existente);
        $hidden = 'readonly';
      }

      $sStatus = "";
      foreach (ColaboradoresDao::getStatus() as $key => $value) {
        $sStatus .=<<<html
        <option value="{$value['catalogo_status_id']}">{$value['nombre']}</option>
html;
      }

      $idEmpresa = "";
      foreach (ColaboradoresDao::getIdEmpresa() as $key => $value) {
        $idEmpresa .=<<<html
        <option value="{$value['catalogo_empresa_id']}">{$value['nombre']}</option>
html;
      }

      $idLector = "";
      foreach (ColaboradoresDao::getIdLector() as $key => $value) {
        $idLector .=<<<html
        <option value="{$value['catalogo_lector_id']}">{$value['nombre']}</option>
html;
      }

      $idLectorSecundario = "";
      foreach (ColaboradoresDao::getIdLector() as $key => $value) {
        $idLectorSecundario .=<<<html
        <option value="{$value['catalogo_lector_id']}">{$value['nombre']}</option>
html;
      }

      $idUbicacion = "";
      foreach (ColaboradoresDao::getIdUbicacion() as $key => $value) {
        $idUbicacion .=<<<html
        <option value="{$value['catalogo_ubicacion_id']}">{$value['nombre']}</option>
html;
      }

      $idDepartamento = "";
      foreach (ColaboradoresDao::getIdDepartamento() as $key => $value) {
        $idDepartamento .=<<<html
        <option value="{$value['catalogo_departamento_id']}">{$value['nombre']}</option>
html;
      }

      $idPuesto = "";
      foreach (ColaboradoresDao::getIdPuesto() as $key => $value) {
        $idPuesto .=<<<html
        <option value="{$value['catalogo_puesto_id']}">{$value['nombre']}</option>
html;
      }

      $horario = "";
      foreach (ColaboradoresDao::getIdHorario() as $key => $value) {
        $horario .=<<<html
        <option value="{$value['catalogo_horario_id']}" llave="{$key}">{$value['nombre']}</option>
html;
      }

      $idMotivo = "";
      foreach (ColaboradoresDao::getIdMotivoBaja() as $key => $value) {
        $selected = ($value['catalogo_motivo_baja_id'] == $colaborador['motivo'])? 'selected' : '';
        $idMotivo .=<<<html
        <option {$selected} value="{$value['catalogo_motivo_baja_id']}">{$value['nombre']}</option>
html;
      }

      $idIncentivo = "";
      foreach (ColaboradoresDao::getIdIncentivo() as $key => $value) {
        $idIncentivo .=<<<html
        <option value="{$value['catalogo_incentivo_id']}" color="{$value['color']}">{$value['nombre']}</option>
html;

      }

      $sPago = "";
      foreach (array('Semanal','Quincenal') as $key => $value) {
        $sPago .=<<<html
        <option value="{$value}">{$value}</option>
html;
      }

      View::set('colaborador_existente',$colaborador_existente);
      View::set('sStatus',$sStatus);
      View::set('idEmpresa', $idEmpresa);
      View::set('idLector', $idLector);
      View::set('idLectorSecundario', $idLectorSecundario);
      View::set('idUbicacion', $idUbicacion);
      View::set('idDepartamento', $idDepartamento);
      View::set('idPuesto', $idPuesto);
      View::set('horario', $horario);
      View::set('idIncentivo', $idIncentivo);
      View::set('idMotivo', $idMotivo);
      View::set('sPago', $sPago);
      View::set('hidden', $hidden);
      View::set('header',$this->_contenedor->header($extraHeader));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("colaboradores_add");
    }

    public function isValidateUser(){
    $dato = ColaboradoresDao::getNumero_Identificacion($_POST['numero_identificacion'],$_POST['id_catalogo_lector']);
    if($dato == 1){
        echo "true";
    }else{
        echo "false";
    }
}

    public function isValidatePlantilla()
{

    if ($_POST['id_catalogo_lector'] == 1) {
        foreach (ColaboradoresDao::getXochimilco() as $key => $value) {
            $faltante = $value['faltantes'];

            if ($faltante >= 1) {
                echo "false";
            } else {
                echo "true";
            }
        }

    }
    if ($_POST['id_catalogo_lector'] == 2) {
        foreach (ColaboradoresDao::getVallejo() as $key => $value) {
            $faltante = $value['faltantes'];

            if ($faltante >= 1) {
                echo "false";
            } else {
                echo "true";
            }
        }

    }

    if ($_POST['id_catalogo_lector'] == 3) {
        foreach (ColaboradoresDao::getGATSA() as $key => $value) {
            $faltante = $value['faltantes'];

            if ($faltante >= 1) {
                echo "false";
            } else {
                echo "true";
            }
        }

    }

    if ($_POST['id_catalogo_lector'] == 4) {
        foreach (ColaboradoresDao::getUNIDESH() as $key => $value) {
            $faltante = $value['faltantes'];

            if ($faltante >= 1) {
                echo "false";
            } else {
                echo "true";
            }
        }

    }

    if ($_POST['id_catalogo_lector'] == 1) {
        foreach (ColaboradoresDao::getProduccionXochimilco() as $key => $value) {
            $faltante = $value['faltantes'];

            if ($faltante >= 1) {
                echo "false";
            } else {
                echo "true";
            }
        }

    }
}

    public function edit($id){
      $extraHeader =<<<html
      <link href="/css/bootstrap-datetimepicker.css" rel="stylesheet">
      <style>
        .incentivo{
          margin: 2px;
          background-color: #18bf7f;
          font: message-box;
          height:25px;
          -webkit-box-shadow: 9px 13px 23px -9px #18bf7f;
          -moz-box-shadow: 9px 13px 23px -9px #18bf7f;
          box-shadow: 9px 13px 23px -9px #18bf7f;
        }

        .cerrar{
          padding: 3px;
        }

        .incentivo:hover{
          background-color: #c9069b;
          -webkit-box-shadow: 9px 13px 23px -9px #c9069b;
          -moz-box-shadow: 9px 13px 23px -9px #c9069b;
          box-shadow: 9px 13px 23px -9px #c9069b;
        }
        .foto{
          width:150px;
          height:150px;
          border-radius: 50px;
          margin:10px;
          float:left;
        }

        .btn span.glyphicon {
        	opacity: 0;
        }
        .btn.active span.glyphicon {
        	opacity: 1;
        }
      </style>

      <link href="/css/datetime.css" rel="stylesheet">
      <link href='http://fonts.googleapis.com/css?family=Roboto:400,500' rel='stylesheet' type='text/css'>
      <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
html;
      $extraFooter =<<<html
      <script src="/js/moment/moment.min.js"></script>
      <script src="/js/datepicker/scriptdatepicker.js"></script>
      <script src="/js/datepicker/datepicker2.js"></script>
      <script type="text/javascript" src="/js/bootstrap-material-datetimepicker.js"></script>

      <script>

        $(document).ready(function(){

          $(".check").change(function(){
            var incentivo = $(this);

             if (!incentivo.is(':checked')) {
              incentivo.attr("cantidad",0);
              $("#texto_"+incentivo.val()).html($("#texto_"+incentivo.val()).attr("texto_default"));
              $("#cantidad_"+incentivo.val()).attr("value",0);
             }else{
                var mensaje = alertify.prompt('Introduce la cantidad del incentivo:',
                function(evt,value){
                  if(value!=""){
                    alertify.success(value);
                    incentivo.attr("cantidad",value);
                    $("#cantidad_"+incentivo.val()).attr("value",value);
                    $("#texto_"+incentivo.val()).html($("#texto_"+incentivo.val()).attr("texto_default")+" : $"+value);
                  }
                }
              );
              mensaje.set('type', 'text');
             }
          });

          $('#fecha_alta').daterangepicker({
              singleDatePicker: true,
              calender_style: "picker_1"
            }, function(start, end, label) {
              console.log(start.toISOString(), end.toISOString(), label);
          });

          $('#fecha_baja').daterangepicker({
              singleDatePicker: true,
              calender_style: "picker_1"
            }, function(start, end, label) {
              console.log(start.toISOString(), end.toISOString(), label);
          });

          $("#foto").change(function(e){
            Archivos = jQuery('#foto')[0].files;
            Lector = new FileReader();
        		Lector.onloadend = function(e){
        			var origen,tipo;
        			//Envía la imagen a la pantalla
        			origen = e.target; //objeto FileReader
        			jQuery('#vista_previa').attr('src', origen.result);
        		};
        		Lector.onerror = function(e){
        			console.log(e)
        		}
        		Lector.readAsDataURL(Archivos[0]);

            $("#vista_previa").attr("src","");
          });

          $("#edit").validate({
            rules:{
              nombre:{
                required: true
              },
              numero_identificacion:{
                required: true
              },
              genero:{
                required: true
              },
              id_catalogo_empresa:{
                required: true
              },
              id_catalogo_ubicacion:{
                required: true
              },
              id_catalogo_departamento:{
                required: true
              },
	            id_catalogo_lector:{
                required: true
              },
              id_catalogo_puesto:{
                required: true
              },
              rfc:{
                required: true,
              },
              status:{
                required: true
              },
              fecha_alta:{
                required: true
              },
              pago:{
                required: true
              },
              incentivo:{
                required: true
              },
              numero_empleado:{
                required: true
              }
            },
            messages:{
              nombre:{
                required: "Este campo es requerido"
              },
              apellido_paterno:{
                required: "Este campo es requerido"
              },
              apellido_materno:{
                required: "Este campo es requerido"
              },
              numero_identificacion:{
                required: "Este campo es requerido"
              },
              genero:{
                required: "Este campo es requerido"
              },
              id_catalogo_empresa:{
                required: "Este campo es requerido"
              },
              id_catalogo_ubicacion:{
                required: "Este campo es requerido"
              },
              id_catalogo_departamento:{
                required: "Este campo es requerido"
              },
              id_catalogo_puesto:{
                required: "Este campo es requerido"
              },
	            id_catalogo_lector:{
                required: "Este campo es requerido"
              },
              horario:{
                required: "Este campo es requerido"
              },
              rfc:{
                required: "Este campo es requerido",
              },
              status:{
                required: "Este campo es requerido"
              },
              fecha_alta:{
                required: "Este campo es requerido"
              },
              pago:{
                required: "Este campo es requerido"
              },
              incentivo:{
                required: "Este campo es requerido"
              },
              numero_empleado:{
                required: "Este campo es requerido"
              }
            }
          });//fin del jquery validate

          jQuery.validator.addMethod("foto", function(value, element) {
              $.extend(jQuery.validator.messages, {
                foto: "Formato de la foto invalida solo se acepta (jpg/jpeg/png)"
              });
              if(value!="" && value.match(/^.*.(jpg|JPG|jpeg|JPEG|png|PNG)$/)){
                if(element.files[0].size<1000000){
                  return true;
                }else{
                   $.extend(jQuery.validator.messages, {
                     foto: "El tamaño de la foto debe ser menor a 1 MB"
                   });
                  return false;
                }
              }else{
                  return false;
              }
          }, jQuery.validator.format("Formato de la foto invalida solo se acepta (jpg/jpeg/png)"));

          jQuery.validator.addMethod("rfc", function(value, element) {
              if(value=="" || value.match(/^([A-Za-z&]{3,4})([0-9]{2})(0[1-9]|1[0-2])(0[1-9]|1[0-9]|2[0-9]|3[0-1])([A-Za-z\d]{3})?$/) ){
                return true;
              }else{
                return false;
              }
          }, jQuery.validator.format("Fotmato RFC invalido"));

 	  //horario default
          jQuery.validator.addMethod("horario_metodo", function(value, element) {
              if(value=="" || value=="-1"){
                return false;
              }else{
                return true;
              }
          }, jQuery.validator.format("Se requiere por lo menos el horario default"));

          $("#horario_1").rules("add", {
            required: true,
            horario_metodo: true
          });

          $("#id_catalogo_ubicacion").change(function(){
            $("#numero_empleado").val(($("#id_catalogo_ubicacion option:selected").text()[0]).toUpperCase()+$("#catalogo_colaboradores_id").val());
          });

          $("#horarios").change(function(){
            if($("#horarios option").length>1){
              $("#btnHorarioAdd").show();
            }
          });

          $("#btnIncentivoAdd").click(function(){
            var id = $("#incentivos").val();
            if(id!=""){
              var nombre = $("#incentivos option:selected").text();
              var color = $("#incentivos option:selected").attr("color");

              var html = '<div class="col-md-4 col-sm-4 col-xs-4" id="incentivo_asignado_'+id+'" style="background-color: '+color+'; border-radius:10px; margin:5px; justify-content: center;">';
              html += '<input type="text" name="cantidad_'+id+'" id="cantidad_'+id+'" class="col-md-9 col-sm-9 col-xs-9" style="margin-right:10px;margin-top:10px; border-radius:10px; border-color: white;" placeholder="Cantidad">';
              html += '<i class="fa fa-times-circle-o cerrar col-md-1 col-sm-1 col-xs-1" id="'+id+'" nombre="'+nombre+'" color="'+color+'" style="font-size: 18px; margin-top:3px; margin-left:5px; color:#C9302C;"></i>';
              html += '<div class="col-md-11 col-sm-11 col-xs-11" style="justify-content: center;">';
              html += '<input class="checkbox checkbox-circle col-md-2 col-sm-2 col-xs-2" type="checkbox" name="incentivo[]" value="'+id+'" checked="checked"/>';
              html += '<label class="col-md-9 col-sm-9 col-xs-9" style="text-align:center;color:#000000;border-radius:10px; padding:5px;margin-left:5px;word-wrap: break-word;" >'+nombre+'</label>';
              html += '</div>';
              html += '</div>';

              //$("#incentivos_asignados").html($("#incentivos_asignados").html()+html);
	      $("#incentivos_asignados").append(html);
              $("#incentivos option:selected").remove();

              if($("#incentivos option").length<=1){
                $("#btnIncentivoAdd").hide();
              }
            }
          });

          $(document).on("click","i.cerrar",function(event){
            var id = $(this).attr("id");
            var nombre = $(this).attr("nombre");
            var color = $(this).attr("color");
            $("#incentivo_asignado_"+id).remove();
            $("#incentivos").append('<option value="'+id+'" color="'+color+'">'+nombre+'</option>');

            if($("#incentivos option").length>1){
              $("#btnIncentivoAdd").show();
            }
          });

          $("#btnCancel").click(function(){
            window.location.href = "/Colaboradores/";
          });//fin del btnAdd

          var llave_1 = -1 ;
          var llave_2 = -1 ;
          var llave_3 = -1 ;
          var llave_4 = -1 ;

/*HORARIO 1 DEFAULT*/
          $("#horario_1").change(function(){
            var llave = $("#horario_1 option:selected").attr("llave");
            $("#horario_2 [llave="+llave+"]").remove();
            $("#horario_3 [llave="+llave+"]").remove();
            $("#horario_4 [llave="+llave+"]").remove();

            if(llave_1>=0){
              var id = $("#horario_1 [llave="+llave_1+"]").val();
              var nombre = $("#horario_1 [llave="+llave_1+"]").text();

              $("#horario_2").append('<option value="'+id+'" llave="'+llave_1+'">'+nombre+'</option>');
              $("#horario_3").append('<option value="'+id+'" llave="'+llave_1+'">'+nombre+'</option>');
              $("#horario_4").append('<option value="'+id+'" llave="'+llave_1+'">'+nombre+'</option>');

              //alert(id+"-"+nombre+"-"+llave_select);
            }

            llave_1 = llave;
          });

/*HORARIO 2*/
          $("#horario_2").change(function(){
            var llave = $("#horario_2 option:selected").attr("llave");
            $("#horario_1 [llave="+llave+"]").remove();
            $("#horario_3 [llave="+llave+"]").remove();
            $("#horario_4 [llave="+llave+"]").remove();

            if(llave_2>=0){
              var id = $("#horario_2 [llave="+llave_2+"]").val();
              var nombre = $("#horario_2 [llave="+llave_2+"]").text();

              $("#horario_1").append('<option value="'+id+'" llave="'+llave_2+'">'+nombre+'</option>');
              $("#horario_3").append('<option value="'+id+'" llave="'+llave_2+'">'+nombre+'</option>');
              $("#horario_4").append('<option value="'+id+'" llave="'+llave_2+'">'+nombre+'</option>');

              //alert(id+"-"+nombre+"-"+llave_select);
            }

            llave_2 = llave;
          });

/*HORARIO 3*/
          $("#horario_3").change(function(){
            var llave = $("#horario_3 option:selected").attr("llave");
            $("#horario_1 [llave="+llave+"]").remove();
            $("#horario_2 [llave="+llave+"]").remove();
            $("#horario_4 [llave="+llave+"]").remove();

            if(llave_3>=0){
              var id = $("#horario_3 [llave="+llave_3+"]").val();
              var nombre = $("#horario_3 [llave="+llave_3+"]").text();

              $("#horario_1").append('<option value="'+id+'" llave="'+llave_3+'">'+nombre+'</option>');
              $("#horario_2").append('<option value="'+id+'" llave="'+llave_3+'">'+nombre+'</option>');
              $("#horario_4").append('<option value="'+id+'" llave="'+llave_3+'">'+nombre+'</option>');

              //alert(id+"-"+nombre+"-"+llave_select);
            }

            llave_3 = llave;
          });
/*HORARIO 4*/
          $("#horario_4").change(function(){
            var llave = $("#horario_4 option:selected").attr("llave");
            $("#horario_1 [llave="+llave+"]").remove();
            $("#horario_2 [llave="+llave+"]").remove();
            $("#horario_3 [llave="+llave+"]").remove();

            if(llave_4>=0){
              var id = $("#horario_4 [llave="+llave_4+"]").val();
              var nombre = $("#horario_4 [llave="+llave_4+"]").text();

              $("#horario_1").append('<option value="'+id+'" llave="'+llave_4+'">'+nombre+'</option>');
              $("#horario_2").append('<option value="'+id+'" llave="'+llave_4+'">'+nombre+'</option>');
              $("#horario_3").append('<option value="'+id+'" llave="'+llave_4+'">'+nombre+'</option>');

              //alert(id+"-"+nombre+"-"+llave_select);
            }

            llave_4 = llave;
          });

          $("#numero_identificacion").keyup(function(){
            var letra = ($("#id_catalogo_ubicacion option:selected").text()[0]).toUpperCase();
            var numero = $(this).val();
            $("#numero_empleado").val(letra+numero);
          });

          $("#horario_1").change();
          $("#horario_2").change();
          $("#horario_3").change();
          $("#horario_4").change();

          //MRR
          /*let clp = parseInt($("#catalogo_lector_id").val());
          if (clp == 3 || clp == 4) {
            $("#inc").hide();
          }
          else {
            $("#inc").show();
          }*/
        });//fin del document.ready
      </script>

          <script type="text/javascript" src="/js/moment-with-locales.min.js"></script>
    <script type="text/javascript">
      $(document).ready(function(){
        $('#date-new1').bootstrapMaterialDatePicker
        ({
          time: false,
          clearButton: true
        });

        $('#date-new2').bootstrapMaterialDatePicker
        ({
          time: false,
          clearButton: true
        });

        $('#time').bootstrapMaterialDatePicker
        ({
          date: false,
          shortTime: false,
          format: 'HH:mm'
        });

        $('#date-format').bootstrapMaterialDatePicker
        ({
          format: 'dddd DD MMMM YYYY - HH:mm'
        });
        $('#date-fr').bootstrapMaterialDatePicker
        ({
          format: 'DD/MM/YYYY HH:mm',
          lang: 'es',
          weekStart: 1,
          cancelText : 'ANNULER',
          nowButton : true,
          switchOnClick : true
        });

        $('#date-end').bootstrapMaterialDatePicker
        ({
          weekStart: 0, format: 'DD/MM/YYYY HH:mm'
        });
        $('#date-start').bootstrapMaterialDatePicker
        ({
          weekStart: 0, format: 'DD/MM/YYYY HH:mm', shortTime : true
        }).on('change', function(e, date)
        {
          $('#date-end').bootstrapMaterialDatePicker('setMinDate', date);
        });

        $('#min-date').bootstrapMaterialDatePicker({ format : 'DD/MM/YYYY HH:mm', minDate : new Date() });

        $.material.init()
      });
    </script>
html;
      $colaborador = ColaboradoresDao::getById($id);
      if(intval($colaborador['clave_noi'])>0){
        $hidden = "readonly";
      }

      $sStatus = "";
      foreach (ColaboradoresDao::getStatus() as $key => $value) {
        $selected = ($value['catalogo_status_id'] == $colaborador['status'])? 'selected' : '';
        $sStatus .=<<<html
        <option {$selected} value="{$value['catalogo_status_id']}">{$value['nombre']}</option>
html;
      }

      $idEmpresa = "";
      foreach (ColaboradoresDao::getIdEmpresa() as $key => $value) {
        $selected = ($value['catalogo_empresa_id'] == $colaborador['catalogo_empresa_id'])? 'selected' : '';
        $idEmpresa .=<<<html
        <option {$selected} value="{$value['catalogo_empresa_id']}">{$value['nombre']}</option>
html;

      }

      $idLector = "";
      foreach (ColaboradoresDao::getIdLector() as $key => $value) {
        $selected = ($value['catalogo_lector_id'] == $colaborador['catalogo_lector_id'])? 'selected' : '';
        $idLector .=<<<html
        <option {$selected} value="{$value['catalogo_lector_id']}">{$value['nombre']}</option>
html;

      }

      $idLectorSecundarioSelected =  false;
      $idLectorSecundario = "";
      foreach (ColaboradoresDao::getIdLector() as $key => $value) {
        $selected = ($value['catalogo_lector_id'] == $colaborador['catalogo_lector_secundario_id'])? 'selected' : '';

        $idLectorSecundario .=<<<html
        <option {$selected} value="{$value['catalogo_lector_id']}">{$value['nombre']}</option>
html;

      }

      if($colaborador['catalogo_lector_secundario_id'] == 0)
          $idLectorSecundario = '<option selected value="0">Catalogo Lector Secundario Nulo o vacio</option>'.$idLectorSecundario;
      else
	  $idLectorSecundario = '<option value="0">Catalogo Lector Secundario Nulo o vacio</option>'.$idLectorSecundario;

      $idUbicacion = "";
      foreach (ColaboradoresDao::getIdUbicacion() as $key => $value) {
        $selected = ($value['catalogo_ubicacion_id'] == $colaborador['catalogo_ubicacion_id'])? 'selected' : '';
        $idUbicacion .=<<<html
        <option {$selected} value="{$value['catalogo_ubicacion_id']}">{$value['nombre']}</option>
html;
      }

      $idDepartamento = "";
      foreach (ColaboradoresDao::getIdDepartamento() as $key => $value) {
        $selected = ($value['catalogo_departamento_id'] == $colaborador['catalogo_departamento_id'])? 'selected' : '';
        $idDepartamento .=<<<html
        <option {$selected} value="{$value['catalogo_departamento_id']}">{$value['nombre']}</option>
html;
      }

      $idPuesto = "";
      foreach (ColaboradoresDao::getIdPuesto() as $key => $value) {
        $selected = ($value['catalogo_puesto_id'] == $colaborador['catalogo_puesto_id'])? 'selected' : '';
        $idPuesto .=<<<html
        <option {$selected} value="{$value['catalogo_puesto_id']}">{$value['nombre']}</option>
html;
      }

      $idMotivo = "";
      foreach (ColaboradoresDao::getIdMotivoBaja() as $key => $value) {
        $selected = ($value['catalogo_motivo_baja_id'] == $colaborador['motivo'])? 'selected' : '';
        $idMotivo .=<<<html
        <option {$selected} value="{$value['catalogo_motivo_baja_id']}">{$value['nombre']}</option>
html;
      }

      $idPrivilegiado = "";
      $arrayPrivilegiado = array('No', 'Si');
      foreach ($arrayPrivilegiado as $key => $value) {
        $selected = ($colaborador['privilegiado'] == $key) ? 'selected' : '';
        $idPrivilegiado .=<<<html
        <option {$selected} value="{$key}">{$value}</option>
html;
      }

      $idIncentivo = "";
      $idIncentivos_asignados = "";
      $incentivo_colaborador = ColaboradoresDao::getIncentivoById($id);
      if(count($incentivo_colaborador)>0){

        foreach (ColaboradoresDao::getIdIncentivo() as $key => $value) {
          $existe = false;
          $cantidad = '';

          foreach ($incentivo_colaborador as $llave => $incentivo) {
            $existe = ($value['catalogo_incentivo_id'] == $incentivo['catalogo_incentivo_id'])? true : false;
            if($existe){
              $cantidad = $incentivo['cantidad'];
              break;
            }else{
               $cantidad ='';
            }
          }

          if($existe){
            if (condition) {
              // code...
            }
            $signo = ($cantidad!='')? '$':'';
            $idIncentivos_asignados .=<<<html
              <div class="col-md-4 col-sm-4 col-xs-4" id="incentivo_asignado_{$value['catalogo_incentivo_id']}" style="background-color:{$value['color']}; border-radius:10px; margin:5px; justify-content: center;">
                <!--MRR-->
                <!--<input type="text" name="cantidad_{$value['catalogo_incentivo_id']}" id="cantidad_{$value['catalogo_incentivo_id']}" class="col-md-9 col-sm-9 col-xs-9" style="margin-right:10px;margin-top:10px; border-radius:10px; border-color: white;" value="{$cantidad}" placeholder="Cantidad" readonly>-->
                <input type="text" name="cantidad_{$value['catalogo_incentivo_id']}" id="cantidad_{$value['catalogo_incentivo_id']}" class="col-md-9 col-sm-9 col-xs-9" style="margin-right:10px;margin-top:10px; border-radius:10px; border-color: white;" value="{$cantidad}" placeholder="Cantidad">
                <!--<i class="fa fa-times-circle-o col-md-1 col-sm-1 col-xs-1" id="{$value['catalogo_incentivo_id']}" nombre="{$value['nombre']}" color="{$value['color']}" style="font-size: 18px; margin-top:3px; margin-left:5px; color:#C9302C;"></i>-->
                <i class="fa fa-times-circle-o cerrar col-md-1 col-sm-1 col-xs-1" id="{$value['catalogo_incentivo_id']}" nombre="{$value['nombre']}" color="{$value['color']}" style="font-size: 18px; margin-top:3px; margin-left:5px; color:#C9302C;"></i>
                <div class="col-md-11 col-sm-11 col-xs-11" style="justify-content: center; margin-top: 5px;">
                  <!--<input class="checkbox checkbox-circle col-md-2 col-sm-2 col-xs-2" type="checkbox" name="incentivo[]" value="{$value['catalogo_incentivo_id']}" checked="checked" onclick="this.checked=!this.checked"/>-->
                  <!--<label class="col-md-9 col-sm-9 col-xs-9" style="text-align:center;color:#000000;border-radius:10px; padding:5px;margin-left:5px;word-wrap: break-word;" > {$value['nombre']} </label>-->
                  <input class="checkbox checkbox-circle col-md-2 col-sm-2 col-xs-2" type="checkbox" name="incentivo[]" value="{$value['catalogo_incentivo_id']}" checked/>
                  <label class="col-md-9 col-sm-9 col-xs-9" style="text-align:center;color:#000000;border-radius:10px; padding:5px;margin-left:5px;word-wrap: break-word;" > {$value['nombre']} </label>
                </div>
              </div>
html;
          }else{
            $idIncentivo .=<<<html
          <option value="{$value['catalogo_incentivo_id']}" color="{$value['color']}">{$value['nombre']}</option>
html;
          }
        }
      }else{
        foreach (ColaboradoresDao::getIdIncentivo() as $key => $value) {
          $idIncentivo .=<<<html
          <option value="{$value['catalogo_incentivo_id']}" color="{$value['color']}">{$value['nombre']}</option>
html;
        }
      }

      $sPago = "";
      foreach (array('Semanal','Quincenal') as $key => $value) {
      $selected = ($colaborador['pago']==$value)? 'selected' : '';
        $sPago .=<<<html
        <option {$selected} value="{$value}">{$value}</option>
html;
      }

      $horario = '';
      $horarios_asignados = array();
      $contador = 0;
      foreach (ColaboradoresDao::getIdHorario() as $key => $value) {
        $existe = false;
        $default = false;
        foreach (ColaboradoresDao::getHorarioById($colaborador['catalogo_colaboradores_id']) as $llave => $valor) {
          if($valor['catalogo_horario_id']==$value['catalogo_horario_id']){
            $existe = true;
            $default = ($valor['horario_default'] == true)? true : false;
          }
        }
        if($existe){
          if($default){
            $horarios_asignados[0] .=<<<html
            <option value="{$value['catalogo_horario_id']}" llave="{$key}" selected>{$value['nombre']}</option>
html;
          }else{
            $horarios_asignados[$contador] .=<<<html
            <option value="{$value['catalogo_horario_id']}" llave="{$key}" selected>{$value['nombre']}</option>
html;
          }
          $contador +=1;
        }else{
          $horario .=<<<html
          <option value="{$value['catalogo_horario_id']}" llave="{$key}">{$value['nombre']}</option>
html;
        }
      }

      $sTipoHorario = '';
      foreach (array('diario'=>'Diario', 'semanal'=>'Semanal') as $key => $value) {
        $selected = ($colaborador['horario_tipo'] == $key)? 'selected' : '';
        $sTipoHorario .=<<<html
        <option value="{$key}" {$selected}>{$value}</option>
html;
      }

      View::set('sStatus',$sStatus);
      View::set('sTipoHorario',$sTipoHorario);
      View::set('idPuesto', $idPuesto);
      View::set('horario', $horario);
      View::set('horarios_asignados', $horarios_asignados);
      View::set('idEmpresa', $idEmpresa);
      View::set('idLector', $idLector);
      View::set('idLectorSecundario', $idLectorSecundario);
      View::set('idIncentivo', $idIncentivo);
      View::set('idIncentivos_asignados', $idIncentivos_asignados);
      View::set('idUbicacion', $idUbicacion);
      View::set('idDepartamento', $idDepartamento);
      View::set('idMotivo', $idMotivo);
      View::set('idPrivilegiado', $idPrivilegiado);
      View::set('colaborador', $colaborador);
      View::set('sPago', $sPago);
      View::set('hidden', $hidden);
      View::set('header',$this->_contenedor->header($extraHeader));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("colaboradores_edit");
    }

    public function show($id){
      $extraHeader =<<<html
      <link href="/css/bootstrap-datetimepicker.css" rel="stylesheet">
      <style>
        .incentivo{
          margin: 2px;
          background-color: #18bf7f;
          font: message-box;
          height:25px;
          -webkit-box-shadow: 9px 13px 23px -9px #18bf7f;
          -moz-box-shadow: 9px 13px 23px -9px #18bf7f;
          box-shadow: 9px 13px 23px -9px #18bf7f;
        }

        .incentivo:hover{
          background-color: #c9069b;
          -webkit-box-shadow: 9px 13px 23px -9px #c9069b;
          -moz-box-shadow: 9px 13px 23px -9px #c9069b;
          box-shadow: 9px 13px 23px -9px #c9069b;
        }
        .foto{
          width:150px;
          height:150px;
          border-radius: 50px;
          margin:10px;
          float:left;
        }

        .btn span.glyphicon {
          opacity: 0;
        }
        .btn.active span.glyphicon {
          opacity: 1;
        }

      </style>
html;
      $extraFooter =<<<html
      <script src="/js/moment/moment.min.js"></script>
      <script src="/js/datepicker/scriptdatepicker.js"></script>
      <script src="/js/datepicker/datepicker2.js"></script>


      <script>

        $(document).ready(function(){

          $(".check").change(function(){
            var incentivo = $(this);

             if (!incentivo.is(':checked')) {
              incentivo.attr("cantidad",0);
              $("#texto_"+incentivo.val()).html($("#texto_"+incentivo.val()).attr("texto_default"));
              $("#cantidad_"+incentivo.val()).attr("value",0);
             }else{
                var mensaje = alertify.prompt('Introduce la cantidad del incentivo:',
                function(evt,value){
                  incentivo.attr("cantidad",value);
                  $("#cantidad_"+incentivo.val()).attr("value",value);
                  $("#texto_"+incentivo.val()).html($("#texto_"+incentivo.val()).attr("texto_default")+" : $"+value);
                }
              );
              mensaje.set('type', 'text');
             }
          });

          $('#fecha_alta').daterangepicker({
              singleDatePicker: true,
              calender_style: "picker_1"
            }, function(start, end, label) {
              console.log(start.toISOString(), end.toISOString(), label);
          });

          $('#fecha_baja').daterangepicker({
              singleDatePicker: true,
              calender_style: "picker_1"
            }, function(start, end, label) {
              console.log(start.toISOString(), end.toISOString(), label);
          });

          $("#foto").change(function(e){
            Archivos = jQuery('#foto')[0].files;
            Lector = new FileReader();
            Lector.onloadend = function(e){
              var origen,tipo;
              //Envía la imagen a la pantalla
              origen = e.target; //objeto FileReader
              jQuery('#vista_previa').attr('src', origen.result);
            };
            Lector.onerror = function(e){
              console.log(e)
            }
            Lector.readAsDataURL(Archivos[0]);

            $("#vista_previa").attr("src","");
          });

          $("#edit").validate({
            rules:{
              nombre:{
                required: true,
                minlength:3,
                maxlength:20
              },
              apellido_paterno:{
                required: true,
                minlength: 3,
                maxlength:20
              },
              apellido_materno:{
                required: true,
                minlength: 3,
                maxlength:20
              },
              numero_identificacion:{
                required: true
              },
              genero:{
                required: true
              },
              id_catalogo_empresa:{
                required: true
              },
              id_catalogo_ubicacion:{
                required: true
              },
              id_catalogo_departamento:{
                required: true
              },
              id_catalogo_puesto:{
                required: true
              },
              horario:{
                required: true
              },
              rfc:{
                required: true,
                minlength: 10,
                maxlength:13,
                rfc:true
              },
              status:{
                required: true
              },
              fecha_alta:{
                required: true
              },
              pago:{
                required: true
              },
              incentivo:{
                required: true
              },
              numero_empleado:{
                required: true,
                minlength: 2,
                maxlength:20
              }
            },
            messages:{
              nombre:{
                required: "Este campo es requerido",
                minlength:"Debe ser mayor a 3 caracteres",
                maxlength:"Debe ser menor a 20 caracteres"
              },
              apellido_paterno:{
                required: "Este campo es requerido",
                minlength:"Debe ser mayor a 3 caracteres",
                maxlength:"Debe ser menor a 20 caracteres"
              },
              apellido_materno:{
                required: "Este campo es requerido",
                minlength:"Debe ser mayor a 3 caracteres",
                maxlength:"Debe ser menor a 20 caracteres"
              },
              numero_identificacion:{
                required: "Este campo es requerido"
              },
              genero:{
                required: "Este campo es requerido"
              },
              id_catalogo_empresa:{
                required: "Este campo es requerido"
              },
              id_catalogo_ubicacion:{
                required: "Este campo es requerido"
              },
              id_catalogo_departamento:{
                required: "Este campo es requerido"
              },
              id_catalogo_puesto:{
                required: "Este campo es requerido"
              },
              horario:{
                required: "Este campo es requerido"
              },
              rfc:{
                required: "Este campo es requerido",
                minlength:"Deben ser 10 caracteres",
                maxlength:"Deben ser 13 caracteres"
              },
              status:{
                required: "Este campo es requerido"
              },
              fecha_alta:{
                required: "Este campo es requerido"
              },
              pago:{
                required: "Este campo es requerido"
              },
              incentivo:{
                required: "Este campo es requerido"
              },
              numero_empleado:{
                required: "Este campo es requerido",
                minlength:"Debe ser mayor a 2 caracteres",
                maxlength:"Debe ser menor a 20 caracteres"
              }
            }
          });//fin del jquery validate

          jQuery.validator.addMethod("foto", function(value, element) {
              $.extend(jQuery.validator.messages, {
                foto: "Formato de la foto invalida solo se acepta (jpg/jpeg/png)"
              });
              if(value!="" && value.match(/^.*.(jpg|JPG|jpeg|JPEG|png|PNG)$/)){
                if(element.files[0].size<1000000){
                  return true;
                }else{
                   $.extend(jQuery.validator.messages, {
                     foto: "El tamaño de la foto debe ser menor a 1 MB"
                   });
                  return false;
                }
              }else{
                  return false;
              }
          }, jQuery.validator.format("Formato de la foto invalida solo se acepta (jpg/jpeg/png)"));

          jQuery.validator.addMethod("rfc", function(value, element) {
              if(value=="" || value.match(/^([A-Za-z&]{3,4})([0-9]{2})(0[1-9]|1[0-2])(0[1-9]|1[0-9]|2[0-9]|3[0-1])([A-Za-z\d]{3})?$/) ){
                return true;
              }else{
                return false;
              }
          }, jQuery.validator.format("Fotmato RFC invalido"));

          $("#id_catalogo_ubicacion").change(function(){
            $("#numero_empleado").val(($("#id_catalogo_ubicacion option:selected").text()[0]).toUpperCase()+$("#catalogo_colaboradores_id").val());
          });


          $("#btnCancel").click(function(){
            window.location.href = "/Colaboradores/";
          });//fin del btnAdd


        });//fin del document.ready
      </script>
html;
      $colaborador = ColaboradoresDao::getById($id);

      $idPrivilegiado = ($colaborador['privilegiado'] == 1) ? 'Si' : 'No';

      if(intval($colaborador['clave_noi'])>0){
        $hidden = "readonly";
      }

      $sStatus = "";
      foreach (ColaboradoresDao::getStatus() as $key => $value) {
        $selected = ($value['catalogo_status_id'] == $colaborador['status'])? 'selected' : '';
        $sStatus .=<<<html
        <option {$selected} value="{$value['catalogo_status_id']}">{$value['nombre']}</option>
html;
      }
      $idMotivo = ColaboradoresDao::getMotivoById($colaborador['motivo'])['nombre'];
      $idIncentivo = "";
      $incentivo_colaborador = ColaboradoresDao::getIncentivoById($id);
      if(count($incentivo_colaborador)>0){
        foreach (ColaboradoresDao::getIdIncentivo() as $key => $value) {

          foreach ($incentivo_colaborador as $key => $incentivo) {
            $selected = ($value['catalogo_incentivo_id']==$incentivo['catalogo_incentivo_id'])? 'checked':'';
            if($selected!=''){
              $cantidad = $incentivo['cantidad'];
              break;
            }else{
               $cantidad ='';
            }
          }

          $idIncentivo .=<<<html
          <span class="badge incentivo" style="background-color: {$value['color']};">
            <input type="checkbox" class="check" id="incentivo[]" name="incentivo[]" value="{$value['catalogo_incentivo_id']}" {$selected}>
            <label id="texto_{$value['catalogo_incentivo_id']}" texto_default="{$value['nombre']}">{$value['nombre']}: {$cantidad}</label>
          </span>
          <input type="hidden" id="cantidad_{$value['catalogo_incentivo_id']}" name="cantidad_{$value['catalogo_incentivo_id']}" value="{$cantidad}"/>
html;
        }
      }else{
        foreach (ColaboradoresDao::getIdIncentivo() as $key => $value) {
          $idIncentivo .=<<<html
          <span class="badge incentivo"><input type="checkbox" class="check" id="incentivo[]" name="incentivo[]" value="{$value['catalogo_incentivo_id']}">  {$value['nombre']}</span>
html;
        }
      }


      $sPago = "";
      foreach (array('Semanal','Quincenal') as $key => $value) {
      $selected = ($colaborador['pago']==$value)? 'selected' : '';
        $sPago .=<<<html
        <option {$selected} value="{$value}">{$value}</option>
html;
      }

      $horario = "";
      foreach (ColaboradoresDao::getHorarioById($colaborador['catalogo_colaboradores_id']) as $key => $value) {
        $dias = $this->getDiasLaboralesColaborador($value['catalogo_horario_id']);
        if($colaborador['catalogo_horario_id'] == $value['catalogo_horario_id']){
          $horario .=<<<html
          <p><span class="badge " id="span_{$value['catalogo_horario_id']}" style="background-color: ;">
          <label>{$value['nombre']}: <b>{$dias}</b> </label><br>
          </span></p>
html;
        }else{
          $horario .=<<<html
          <p><span class="badge " id="span_{$value['catalogo_horario_id']}" style="background-color: ;">
          <label>{$value['nombre']}: <b>{$dias}</b> </label>
          </span></p>
html;
        }


    }

      $nombreEmpresa = ColaboradoresDao::getCatalogoEmpresa($id);
      $nombreUbicacion = ColaboradoresDao::getCatalogoUbicacion($id);
      $nombreDepartamento = ColaboradoresDao::getCatalogoDepartamento($id);
      $nombrePuesto = ColaboradoresDao::getCatalogoPuesto($id);
      $nombreLector = ColaboradoresDao::getCatalogoLector($id);
      $nombresIncentivos = ColaboradoresDao::getIncentivosColaborador($id);
      $statusNombre = ColaboradoresDao::getStatusColaborador($id);

      $nomIncentivos = '';
      $signo = "$";
      foreach ($nombresIncentivos as $key => $value) {
        $nomIncentivos .= <<<html
              <span class="glyphicon glyphicon-minus"> {$value['nombre']}: {$signo}{$value['cantidad']} </span><br>
html;
      }


      View::set('sStatus',$sStatus);
      View::set('idPuesto', $idPuesto);
      View::set('horario', $horario);
      View::set('nombrePuesto',$nombrePuesto);
      View::set('idMotivo', $idMotivo);
      View::set('idPrivilegiado', $idPrivilegiado);
      View::set('nombreEmpresa', $nombreEmpresa);
      View::set('nombreLector', $nombreLector);
      View::set('nombreUbicacion', $nombreUbicacion);
      View::set('nombreDepartamento', $nombreDepartamento);
      View::set('nomIncentivos', $nomIncentivos);
      View::set('colaborador', $colaborador);
      View::set('status', $statusNombre);
      View::set('sPago', $sPago);
      View::set('hidden', $hidden);
      View::set('header',$this->_contenedor->header($extraHeader));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("colaboradores_view");
    }

    public function getDiasLaboralesColaborador($catalogo_horario_id){
      $html = "";
      foreach (ColaboradoresDao::getDiasLaboralesColaborador($catalogo_horario_id) as $key => $value) {
        $html .=<<<html
          <span> {$value['dia_laboral']} </span>
html;
      }
      return $html;
    }

    public function existente(){
      $extraFooter =<<<html
      <script>
        $(document).ready(function(){
          $("#existente").bootstrapSwitch();

          $('input[name="existente"]').on('switchChange.bootstrapSwitch', function(event, state) {
            if(state){
              $("#identificador").show();
              $("#tabla_muestra").show();
            }else{
              $("#identificador").hide();
              $("#tabla_muestra").hide();
              $("input[type=radio]").attr('checked', false);
            }
          });



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

            $('#muestra-cupones input[type=search]').keyup( function () {
                var table = $('#example').DataTable();
                table.search(
                    jQuery.fn.DataTable.ext.type.search.html(this.value)
                ).draw();
            });

            $(document).on("change","#identificador",function(e){
              e.stopPropagation();//evita que se ejecute 2 veces el mismo evento
              $.ajax({
                url:'/Colaboradores/getTablaOperaciones/',
                type:'POST',
                data:{"identificador": $(this).val()},
                success:function(response){
                  $("#tabla_muestra").html(response);
                  $('#muestra-cupones').DataTable({
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
                    });//fin del dataTable
                }
              });//fin del ajax
            });//fin del evento change de #identificador

            $("#btnAceptar").click(function(){
              $('#form-existente').attr('action', '/Colaboradores/add/');
              $('#form-existente').attr('method', 'POST');
              $('#form-existente').attr('target', '');
              $("#form-existente").submit();
            });
        });//fin del document ready
      </script>
html;
      $sIdentificador = "";
      foreach (ColaboradoresDao::getIdentificador() as $key => $value) {
        $sIdentificador .=<<<html
        <option value="{$value['identificador']}">{$value['identificador']}</option>
html;
      }

      $sColaboradorExistente = "";
      $colaboradores_existentes = ColaboradoresDao::getOperacionNoi();
      foreach ($colaboradores_existentes as $key => $value) {
        $value['nombre'] = utf8_encode($value['nombre']);
        $value['ap_pat'] = utf8_encode($value['ap_pat']);
        $value['ap_mat'] = utf8_encode($value['ap_mat']);
        $value['rfc'] = utf8_encode($value['rfc']);
        $value['fecha_alta'] = utf8_encode($value['fecha_alta']);
        $sColaboradorExistente .=<<<html
        <tr>
          <td><input type="radio" name="colaborador_id" value="{$value['identificador']}{$value['clave']}"/></td>
          <td>{$value['nombre']}</td>
          <td>{$value['ap_pat']}</td>
          <td>{$value['ap_mat']}</td>
          <td>{$value['rfc']}</td>
          <td>{$value['fecha_alta']}</td>
        </tr>
html;
      }

      View::set("sIdentificador",$sIdentificador);
      View::set("sColaboradorExistente",$sColaboradorExistente);
      View::set('header',$this->_contenedor->header($extraHeader));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("colaboradores_existente");
    }

    public function bajas(){
      $extraFooter =<<<html
      <script>
        $(document).ready(function(){
          $("#existente").bootstrapSwitch();

          $('input[name="existente"]').on('switchChange.bootstrapSwitch', function(event, state) {
            if(state){
              $("#identificador").show();
              $("#tabla_muestra").show();
            }
            else{
              $("#identificador").hide();
              $("#tabla_muestra").hide();
              $("input[type=radio]").attr('checked', false);
            }
          });

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

          $('#muestra-cupones input[type=search]').keyup( function () {
              var table = $('#example').DataTable();
              table.search(
                  jQuery.fn.DataTable.ext.type.search.html(this.value)
              ).draw();
          });

          $(document).on("change","#identificador",function(e){
            e.stopPropagation();//evita que se ejecute 2 veces el mismo evento
            $.ajax({
              url:'/Colaboradores/getTablaBajas/',
              type:'POST',
              data:{"identificador": $(this).val()},
              success:function(response){
                $("#tabla_muestra").html(response);
                $('#muestra-cupones').DataTable({
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
                  });//fin del dataTable
              }
            });//fin del ajax
          });//fin del evento change de #identificador
        });//fin del document ready
      </script>
html;
      $tabla = "";
      $colaboradores_existentes = ColaboradoresDao::getBajaEmpleados();
      foreach ($colaboradores_existentes as $key => $value) {
        $value['nc'] = utf8_encode($value['nc']);
        $value['fa'];
        $value['fb'];
        $value['d'] = utf8_encode($value['d']);
        $tabla .=<<<html
        <tr>
          <td>{$value['nc']}</td>
          <td>{$value['fa']}</td>
          <td>{$value['fb']}</td>
          <td>{$value['d']}</td>
        </tr>
html;
      }

      View::set("tabla",$tabla);
      View::set('header',$this->_contenedor->header($extraHeader));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("colaboradores_bajas");
    }

    public function getTablaOperaciones(){
      $identificador = MasterDom::getData('identificador');
      $filtro = ($identificador!='')? " AND identificador = '".$identificador."'" : '';

      echo <<<html
      <div class="dataTable_wrapper">
        <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
          <thead>
            <tr>
              <th ></th>
              <th>Nombre</th>
              <th>Apellido Paterno</th>
              <th>Apellido Materno</th>
              <th>Planta</th>
              <th>RFC</th>
              <th>Clave NOI</th>
              <th>Fecha Alta</th>
            </tr>
          </thead>
          <tbody id="registros">
html;
      foreach (ColaboradoresDao::getOperacionNoi($filtro) as $key => $value) {
        $value['nombre'] = utf8_encode($value['nombre']);
        $value['ap_pat'] = utf8_encode($value['ap_pat']);
        $value['ap_mat'] = utf8_encode($value['ap_mat']);
        $value['rfc'] = utf8_encode($value['rfc']);
        $value['fecha_alta'] = utf8_encode($value['fecha_alta']);
        echo <<<html
        <tr>
          <td><input type="radio" name="colaborador_id" value="{$value['identificador']}{$value['clave']}"/></td>
          <td>{$value['nombre']}</td>
          <td>{$value['ap_pat']}</td>
          <td>{$value['ap_mat']}</td>
          <td>{$value['identificador']}</td>
          <td>{$value['rfc']}</td>
          <td>{$value['clave']}</td>
          <td>{$value['fecha_alta']}</td>
        </tr>
html;
      }

      echo <<<html
      </tbody>
    </table>
  </div>
html;

    }

    public function getTablaBajas(){
      $identificador = MasterDom::getData('identificador');
      $filtro = ($identificador!='')? " AND identificador = '".$identificador."'" : '';

      echo <<<html
      <div class="dataTable_wrapper">
        <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
          <thead>
            <tr>
              <th ></th>
              <th>Nombre</th>
              <th>Apellido Paterno</th>
              <th>Apellido Materno</th>
              <th>Planta</th>
              <th>RFC</th>
              <th>Clave NOI</th>
              <th>Fecha Alta</th>
            </tr>
          </thead>
          <tbody id="registros">
html;
      foreach (ColaboradoresDao::getOperacionNoi($filtro) as $key => $value) {
        $value['nombre'] = utf8_encode($value['nombre']);
        $value['ap_pat'] = utf8_encode($value['ap_pat']);
        $value['ap_mat'] = utf8_encode($value['ap_mat']);
        $value['rfc'] = utf8_encode($value['rfc']);
        $value['fecha_alta'] = utf8_encode($value['fecha_alta']);
        echo <<<html
        <tr>
          <td><input type="radio" name="colaborador_id" value="{$value['identificador']}{$value['clave']}"/></td>
          <td>{$value['nombre']}</td>
          <td>{$value['ap_pat']}</td>
          <td>{$value['ap_mat']}</td>
          <td>{$value['identificador']}</td>
          <td>{$value['rfc']}</td>
          <td>{$value['clave']}</td>
          <td>{$value['fecha_alta']}</td>
        </tr>
html;
      }

      echo <<<html
      </tbody>
    </table>
  </div>
html;

    }

    public function delete(){
      $ids = MasterDom::getDataAll('borrar');
      foreach ($ids as $key => $value) {
        $id = ColaboradoresDao::delete($value);
        $reporte->_ids = $id;
      }
      $this->alerta($reporte,'delete');
    }

    public function colaboradorAdd(){
      $horario = MasterDom::getDataAll('horario');
      $default = (MasterDom::getDataAll('horario_default')!='')? MasterDom::getDataAll('horario_default') : $horario[0] ;
      $foto = MasterDom::getData('foto');
      $nombre = explode('.',$foto['name']);
      $nombre = uniqid().'.'.$nombre[1];

      $directorio = dirname(__DIR__).'/../public/img/colaboradores/';
      if ($foto['name']=='' || move_uploaded_file($foto['tmp_name'], $directorio/*"/home/granja/backend/public/img/colaboradores/"*/.$nombre)) {

        $colaborador = new \stdClass();
        $colaborador->_nombre = MasterDom::getData('nombre');
        $colaborador->_apellido_paterno = MasterDom::getData('apellido_paterno');
        $colaborador->_apellido_materno = MasterDom::getData('apellido_materno');
        $colaborador->_motivo = MasterDom::getData('motivo');
        $colaborador->_genero = MasterDom::getData('genero');
        $colaborador->_numero_identificacion = MasterDom::getData('numero_identificacion');
        $colaborador->_rfc = MasterDom::getData('rfc');
          $rfc = MasterDom::getData('rfc');

          $rfc = substr($rfc,4,6);
          $rfc = str_split($rfc,2);
          $fecha_nac =  '19'.$rfc[0].'-'.$rfc[1].'-'.$rfc[2];
          $colaborador->_fecha_nac = $fecha_nac;
        $colaborador->_id_catalogo_empresa = MasterDom::getData('id_catalogo_empresa');
        $colaborador->_id_catalogo_ubicacion = MasterDom::getData('id_catalogo_ubicacion');
        $colaborador->_id_catalogo_departamento = MasterDom::getData('id_catalogo_departamento');
        $colaborador->_id_catalogo_puesto = MasterDom::getData('id_catalogo_puesto');
        $colaborador->_id_catalogo_lector = MasterDom::getData('id_catalogo_lector');
        $colaborador->_id_catalogo_lector_secundario = MasterDom::getData('id_catalogo_lector_secundario');
        $colaborador->_horario = $default;
        $colaborador->_fecha_alta = MasterDom::getData('fecha_alta');
        $colaborador->_fecha_baja = MasterDom::getData('fecha_baja');
        $colaborador->_foto = ($foto['name']!='')? $nombre : 'user.png';
        $colaborador->_pago = MasterDom::getData('pago');
        $colaborador->_opcion = MasterDom::getData('opcion');
        $colaborador->_status = MasterDom::getData('status');
        $colaborador->_letra_ubicacion = MasterDom::getData('letra_ubicacion');
        $colaborador->_clave_noi = MasterDom::getData('clave_noi');
        $colaborador->_identificador = MasterDom::getData('identificador');
	    $colaborador->_privilegiado = MasterDom::getData('privilegiado');
	    $colaborador->_tipo_horario = MasterDom::getDataAll('tipo_horario');

        $reporte = new \stdClass();
        $reporte->_id_colaborador = ColaboradoresDao::insert($colaborador);
        $reporte->_numero_empleado = ColaboradoresDao::updateNumeroEmpleado($reporte->_id_colaborador, MasterDom::getData('numero_identificacion'));

          $puestos_ocupados = new \stdClass();
          $fechamovimiento1 =  date('Y-m-d H:i:s');
          $puestos_ocupados->_id_puesto = MasterDom::getData('id_catalogo_puesto');
          $puestos_ocupados->_fecha = $fechamovimiento1;
          $puestos_ocupados->_id_colaborador =  $reporte->_id_colaborador;

          $nombre = MasterDom::getData('nombre');
          $apellido_paterno = MasterDom::getData('apellido_paterno');
          $apellido_materno = MasterDom::getData('apellido_materno');
          $nombre_completo = $nombre." ".$apellido_paterno." ".$apellido_materno;

          $sueldo_inicial = new \stdClass();
          $sueldo_inicial->_clave = MasterDom::getData('clave_noi');
          $sueldo_inicial->_fecha_alta = $fechamovimiento1;

          $usuarios_encuestas = new \stdClass();
          $usuarios_encuestas->_nombre = $nombre_completo;
          $usuarios_encuestas->_usuario_clave = MasterDom::getData('numero_identificacion');
          $usuarios_encuestas->_contrasena = MasterDom::getData('apellido_paterno');
          $usuarios_encuestas->_id_colaborador =  $reporte->_id_colaborador;

          $id = ColaboradoresDao::insertIngresoProyecto($reporte);
          $id1 = ColaboradoresDao::insertPuestosOcupados($puestos_ocupados);
          $id2 = ColaboradoresDao::insertUsuariosEncuestas($usuarios_encuestas);
          $id3 = ColaboradoresDao::insertCuestionarioIngreso($reporte);
          $id3 = ColaboradoresDao::insertSueldoInicial($sueldo_inicial);

        $horarios = array();
        $contador = 0;
        foreach ($horario as $key => $value) {
          if($value!=''){
            $horario = new \stdClass();
            $horario->_catalogo_colaboradores_id = $reporte->_id_colaborador;
            $horario->_catalogo_horario_id = $value;
            $horario->_default = ($value == $default)? 1 : 0;
            $horarios[$contador] = ColaboradoresDao::insertHorario($horario);
            $contador +=1;
          }
        }

        /**add**/
        if (MasterDom::getData('id_catalogo_lector') == 3 || MasterDom::getData('id_catalogo_lector') == 4) {
          $reporte->_horarios = $horarios;
          $puesto = MasterDom::getData('id_catalogo_puesto');
          // $incentivos_colaborador = MasterDom::getDataAll('incentivo');
          //MRR
          $incentivos_colaborador = ColaboradoresDao::getIncentivos($puesto);
          $ti = $incentivos_colaborador['total_valor_incentivos'];
          // $reporte->_id_colaborador = 930;
          for ($i=1; $i <= $incentivos_colaborador['numero_incentivos']; $i++) {
            $incentivo = new \stdClass();
            $incentivo->_catalogo_colaboradores_id = $reporte->_id_colaborador;
            $incentivo->_catalogo_incentivo_id = $incentivos_colaborador['incentivo'.$i];
            $incentivo->_cantidad = $ti * ($incentivos_colaborador['porcentaje'.$i]/100);
            $insert = ColaboradoresDao::insertIncentivo($incentivo);

            // $getin = ColaboradoresDao::getIn($reporte->_id_colaborador,$incentivo->_catalogo_incentivo_id);
            // $getpid = ColaboradoresDao::getpid();
            // $data = new \stdClass();
            // $data->_colaborador_id = $reporte->_id_colaborador;
            // $data->_prorrateo_periodo_id = $getpid['prorrateo_periodo_id'];
            // $data->_catalogo_incentivo_id = $getin['catalogo_incentivo_id'];
            // $data->_cantidad = $getin['cantidad'];
            // $data->_asignado = 0;
            // $data->_valido = 0;
            // $id = ColaboradoresDao::insertIncentivos($data);
            // print_r($data);
            // echo '<br/>';
          }
        }
        else {
          $reporte->_horarios = $horarios;
          $incentivos_colaborador = MasterDom::getDataAll('incentivo');
          $incentivos = array();
          $contador = 0;

          if(count($incentivos_colaborador)>0){
            foreach ($incentivos_colaborador as $key => $value) {

              $incentivo = new \stdClass();
              $incentivo->_catalogo_colaboradores_id = $reporte->_id_colaborador;
              $incentivo->_catalogo_incentivo_id = $value;
              $incentivo->_cantidad = MasterDom::getData("cantidad_".$value);

        	    if($incentivo->_cantidad == 0 || $incentivo->_cantidad == "")
                continue;
              $incentivos[$contador] = ColaboradoresDao::insertIncentivo($incentivo);
              $contador += 1;
            }
          }
        }
        $reporte->_incentivos = $incentivos;
        $this->alerta($reporte,'add');
      } else {
        echo "¡Posible ataque de subida de ficheros!<br>";
      }
    }

    public function colaboradorEdit(){
      $horario = MasterDom::getDataAll('horario');
      $default = (MasterDom::getDataAll('horario_default')!='')? MasterDom::getDataAll('horario_default') : $horario[0] ;
      $foto = MasterDom::getData('foto');
      $nombre = '';
      if($foto['name']!=''){
        $nombre = explode('.',$foto['name']);
        $nombre = uniqid().'.'.$nombre[1];

        $directorio = dirname(__DIR__).'/../public/img/colaboradores/';
        move_uploaded_file($foto['tmp_name'], $directorio/*"/home/granja/backend/public/img/colaboradores/"*/.$nombre);

      }else{
        $colaborador = ColaboradoresDao::getById(MasterDom::getData('catalogo_colaboradores_id'));
        $nombre = $colaborador['foto'];
      }
        $colaborador = new \stdClass();
        $colaborador->_nombre = MasterDom::getData('nombre');
        $colaborador->_catalogo_colaboradores_id = MasterDom::getData('catalogo_colaboradores_id');
        $colaborador->_apellido_paterno = MasterDom::getData('apellido_paterno');
        $colaborador->_apellido_materno = MasterDom::getData('apellido_materno');
        $colaborador->_motivo = MasterDom::getData('motivo');
        $colaborador->_genero = MasterDom::getData('genero');
        $colaborador->_numero_identificacion = MasterDom::getData('numero_identificacion');
        $colaborador->_rfc = MasterDom::getData('rfc');
        $colaborador->_id_catalogo_empresa = MasterDom::getData('id_catalogo_empresa');
	      $colaborador->_id_catalogo_lector = MasterDom::getData('catalogo_lector_id');
        $colaborador->_id_catalogo_lector_secundario = MasterDom::getData('catalogo_lector_secundario_id');
        $colaborador->_id_catalogo_ubicacion = MasterDom::getData('id_catalogo_ubicacion');
        $colaborador->_id_catalogo_departamento = MasterDom::getData('id_catalogo_departamento');
        $colaborador->_id_catalogo_puesto = MasterDom::getData('id_catalogo_puesto');
        $colaborador->_horario = $default;
        $colaborador->_fecha_alta = MasterDom::getData('fecha_alta');
        $colaborador->_fecha_baja = MasterDom::getData('fecha_baja');
        $colaborador->_foto = $nombre;
        $colaborador->_pago = MasterDom::getData('pago');
        $colaborador->_opcion = MasterDom::getData('opcion');
        $colaborador->_status = MasterDom::getData('status');
        $colaborador->_numero_empleado = MasterDom::getData('numero_empleado');
        $colaborador->_clave_noi = MasterDom::getData('clave_noi');
        $colaborador->_privilegiado = MasterDom::getData('privilegiado');
        $colaborador->_tipo_horario = MasterDom::getDataAll('tipo_horario');

        $update_puestos = new \stdClass();

        $fechamovimiento1 =  date('Y-m-d H:i:s');
        $update_puestos->_fecha = $fechamovimiento1;
        $update_puestos->_id_colaborador = MasterDom::getData('catalogo_colaboradores_id');

        if($update_puestos->_id_catalogo_puesto == $colaborador->_id_catalogo_puesto) {
          echo "son iguales";
        }
        else {
          $update_puestos->_id_puesto = MasterDom::getData('id_catalogo_puesto');
          ColaboradoresDao::insertPuestosOcupados($update_puestos);
        }

        $reporte = new \stdClass();
        $reporte->_id_colaborador = MasterDom::getData('catalogo_colaboradores_id');
        $reporte->_actualizar = ColaboradoresDao::update($colaborador);

        ColaboradoresDao::deleteHorario($colaborador->_catalogo_colaboradores_id);
        $horarios = array();
        $contador = 0;
        foreach ($horario as $key => $value) {
          if($value!=''){
            $horario = new \stdClass();
            $horario->_catalogo_colaboradores_id = $colaborador->_catalogo_colaboradores_id;
            $horario->_catalogo_horario_id = $value;
            $horario->_default = ($value == $default)? 1 : 0;
            $horarios[$contador] = ColaboradoresDao::insertHorario($horario);
            $contador +=1;
          }
        }

        $reporte->_horarios = $horarios;

        ColaboradoresDao::deleteIncentivo($colaborador->_catalogo_colaboradores_id);
        //MRR

        if(MasterDom::getData('id_catalogo_puesto') == 31){
          $incentivos = MasterDom::getDataAll('incentivo');
          $incentivos_error = array();
          $contador = 0;
          if(count($incentivos)>0){
            foreach ($incentivos as $key => $value) {
              $incentivo = new \stdClass();
              $incentivo->_catalogo_colaboradores_id = $colaborador->_catalogo_colaboradores_id;
              $incentivo->_catalogo_incentivo_id = $value;
              $incentivo->_cantidad = MasterDom::getData("cantidad_".$value);

              if($incentivo->_cantidad == 0 || $incentivo->_cantidad == "")
              continue;
              $incentivos_error[$contador] = ColaboradoresDao::insertIncentivo($incentivo);
              $contador += 1;
            }
            $reporte->_incentivos = $incentivos_error;
          }
        }
        else if (MasterDom::getData('catalogo_lector_id') == 3 || MasterDom::getData('catalogo_lector_id') == 4) {
          $getpid = ColaboradoresDao::getpid();
          foreach (ColaboradoresDao::getIa(MasterDom::getData('catalogo_colaboradores_id'),$getpid['ppi']) as $val => $vol) {//,$incentivo->_catalogo_incentivo_id
            $dele = ColaboradoresDao::delIa($vol['iai']);
          }

          $reporte->_horarios = $horarios;
          $puesto = MasterDom::getData('id_catalogo_puesto');
          // $incentivos_colaborador = MasterDom::getDataAll('incentivo');
          //MRR
          $incentivos_colaborador = ColaboradoresDao::getIncentivos($puesto);
          $ti = $incentivos_colaborador['total_valor_incentivos'];
          // $reporte->_id_colaborador = 930;
          for ($i=1; $i <= $incentivos_colaborador['numero_incentivos']; $i++) {
            $incentivo = new \stdClass();
            $incentivo->_catalogo_colaboradores_id = $reporte->_id_colaborador;
            $incentivo->_catalogo_incentivo_id = $incentivos_colaborador['incentivo'.$i];
            $incentivo->_cantidad = $ti * ($incentivos_colaborador['porcentaje'.$i]/100);
            $insert = ColaboradoresDao::insertIncentivo($incentivo);

            // $getin = ColaboradoresDao::getIn($reporte->_id_colaborador,$incentivo->_catalogo_incentivo_id);
            // // $getpid = ColaboradoresDao::getpid();
            // $data = new \stdClass();
            // $data->_colaborador_id = $reporte->_id_colaborador;
            // $data->_prorrateo_periodo_id = $getpid['ppi'];
            // $data->_catalogo_incentivo_id = $getin['catalogo_incentivo_id'];
            // $data->_cantidad = $getin['cantidad'];
            // $data->_asignado = 0;
            // $data->_valido = 0;
            // $id = ColaboradoresDao::insertIncentivos($data);
            // print_r($data);
            // echo '<br/>';
          }
        }
        else {
          $incentivos = MasterDom::getDataAll('incentivo');
          $incentivos_error = array();
          $contador = 0;
          if(count($incentivos)>0){
            foreach ($incentivos as $key => $value) {
              $incentivo = new \stdClass();
              $incentivo->_catalogo_colaboradores_id = $colaborador->_catalogo_colaboradores_id;
              $incentivo->_catalogo_incentivo_id = $value;
              $incentivo->_cantidad = MasterDom::getData("cantidad_".$value);

              if($incentivo->_cantidad == 0 || $incentivo->_cantidad == "")
              continue;
              $incentivos_error[$contador] = ColaboradoresDao::insertIncentivo($incentivo);
              $contador += 1;
            }
            $reporte->_incentivos = $incentivos_error;
          }
        }
        $this->alerta($reporte,'edit');
    }


    public function generarPDF(){

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
      $ids = MasterDom::getDataAll('borrar');
      $mpdf=new \mPDF('c');
      $mpdf->defaultPageNumStyle = 'I';
      $mpdf->h2toc = array('H5'=>0,'H6'=>1);
      $style =<<<html
      <style>
        .imagen{
          width:100%;
          height: 150px;
          background: url(/img/ag_logo.png) no-repeat center center fixed;
          background-size: cover;
          -moz-background-size: cover;
          -webkit-background-size: cover
          -o-background-size: cover;
        }

        .titulo{
          width:100%;
          margin-top: 30px;
          color: #F5AA3C;
          margin-left:auto;
          margin-right:auto;
        }

        .incentivo{
          border-radius:10px;
          background-color: #a0985e;
          margin: 2px;
          font: message-box bold;
          height:100%;
        }

        .foto{
          width: 150px;
          height: 150px;
        }

      </style>
html;

$tabla =<<<html
<img class="imagen" src="/img/ag_logo.png"/>
<br>
<H1 class="titulo">Colaboradores</H1>

html;

      if($ids!=''){
        foreach ($ids as $key => $value) {
          $colaborador = ColaboradoresDao::getByIdReporte($value);

          $colaborador['catalogo_colaboradores_id'] = utf8_encode($colaborador['catalogo_colaboradores_id']);
          $colaborador['rfc'] = utf8_encode($colaborador['rfc']);
          $colaborador['nombre'] = utf8_encode($colaborador['nombre']);
          $colaborador['apellido_paterno'] = utf8_encode($colaborador['apellido_paterno']);
          $colaborador['apellido_materno'] = utf8_encode($colaborador['apellido_materno']);
          $colaborador['numero_identificador'] = utf8_encode($colaborador['numero_identificador']);
          $colaborador['sexo'] = utf8_encode($colaborador['sexo']);
          $colaborador['status'] = utf8_encode($colaborador['status']);
          $colaborador['numero_empleado'] = utf8_encode($colaborador['numero_empleado']);
          $colaborador['opcion'] = utf8_encode($colaborador['opcion']);
          $colaborador['catalogo_empresa_id'] = utf8_encode($colaborador['catalogo_empresa_id']);
          $colaborador['catalogo_puesto_id'] = utf8_encode($colaborador['catalogo_puesto_id']);
          $colaborador['catalogo_ubicacion_id'] = utf8_encode($colaborador['catalogo_ubicacion_id']);
          $colaborador['catalogo_departamento_id'] = utf8_encode($colaborador['catalogo_departamento_id']);
          $colaborador['pago'] = utf8_encode($colaborador['pago']);
          $colaborador['fecha_alta'] = utf8_encode($colaborador['fecha_alta']);
          $colaborador['fecha_baja'] = utf8_encode($colaborador['fecha_baja']);


          $tabla.=<<<html
          <div style="page-break-inside: avoid; margin-bottom: 30px;">
            <table border="0" style="width:100%;text-align: center; ">
              <tr>
                <td colspan="1" style="background-color:#B8B8B8;"><strong>Colaborador Id: </strong> {$colaborador['catalogo_colaboradores_id']}</td>
                <td colspan="2" style="background-color:#B8B8B8;"><strong>RFC: </strong> {$colaborador['rfc']}</td>
              </tr>
              <tr>
                <td colspan="3" style="background-color:#B8B8B8;"><strong>Nombre: </strong> {$colaborador['nombre']} {$colaborador['apellido_paterno']} {$colaborador['apellido_materno']}</td>
              </tr>
              <tr>
                <td colspan="3" style="background-color:#E4E4E4;"><strong>Número de Identificador: </strong> {$colaborador['numero_identificador']}</td>
              </tr>
              <tr>
                <td colspan="1" style="background-color:#E4E4E4;"><strong>Sexo: </strong> {$colaborador['sexo']}</td>
                <td colspan="1" style="background-color:#E4E4E4;"><strong>Status: </strong> {$colaborador['status']}</td>
                <td colspan="1" style="background-color:#E4E4E4;"><strong>Numero Empleado: </strong> {$colaborador['numero_empleado']}</td>
              </tr>
              <tr>
                <td colspan="1" style="background-color:#E4E4E4;"><strong>Opción: </strong> {$colaborador['opcion']}</td>
                <td colspan="2" style="background-color:#E4E4E4;"><strong>Competencias: </strong> {$colaborador['catalogo_empresa_id']}</td>
              </tr>
              <tr>
                <td colspan="1" style="background-color:#E4E4E4;"><strong>Puesto: </strong> {$colaborador['catalogo_puesto_id']}</td>
                <td colspan="2" style="background-color:#E4E4E4;"><strong>Ubicacion: </strong> {$colaborador['catalogo_ubicacion_id']}</td>
              </tr>
              <tr>
                <td colspan="1" style="background-color:#E4E4E4;"><strong>Departamento: </strong> {$colaborador['catalogo_departamento_id']}</td>
                <td colspan="2" style="background-color:#E4E4E4;"><strong>Horario</strong>
html;
                foreach (ColaboradoresDao::getHorarioById($colaborador['catalogo_colaboradores_id']) as $key => $horario) {
                  $tabla .=<<<html
                      <span class="badge incentivo">{$horario['nombre']}</span>
html;
              }
                $tabla .=<<<html
                </td>
              </tr>
              <tr>
                <td colspan="1" style="background-color:#E4E4E4;"><strong>Pago: </strong> {$colaborador['pago']}</td>
                <td colspan="2" style="background-color:#E4E4E4;"><strong>Incentivos: </strong>
html;

                              foreach (ColaboradoresDao::getIncentivoById($colaborador['catalogo_colaboradores_id']) as $key => $incentivo) {
                                $signo = "$";
                                $tabla .=<<<html
                                <br><span class="badge incentivo" style="background-color: {$incentivo['color']} ;">{$incentivo['nombre']}: {$signo}{$incentivo['cantidad']}</span>
html;
                              }


                              $tabla .=<<<html

                </td>
              </tr>
              <tr>
                <td colspan="1" style="background-color:#E4E4E4;"><strong>Fecha Alta: </strong> {$colaborador['fecha_alta']}</td>
                <td colspan="2" style="background-color:#E4E4E4;"><strong>Fecha Baja: </strong> {$colaborador['fecha_baja']}</td>
              </tr>
            </table>
          </div>
html;
        }
      }else{
        foreach (ColaboradoresDao::getAllReporte($filtro) as $key => $colaborador) {

          $colaborador['catalogo_colaboradores_id'] = utf8_encode($colaborador['catalogo_colaboradores_id']);
          $colaborador['rfc'] = utf8_encode($colaborador['rfc']);
          $colaborador['nombre'] = utf8_encode($colaborador['nombre']);
          $colaborador['apellido_paterno'] = utf8_encode($colaborador['apellido_paterno']);
          $colaborador['apellido_materno'] = utf8_encode($colaborador['apellido_materno']);
          $colaborador['numero_identificador'] = utf8_encode($colaborador['numero_identificador']);
          $colaborador['sexo'] = utf8_encode($colaborador['sexo']);
          $colaborador['status'] = utf8_encode($colaborador['status']);
          $colaborador['numero_empleado'] = utf8_encode($colaborador['numero_empleado']);
          $colaborador['opcion'] = utf8_encode($colaborador['opcion']);
          $colaborador['catalogo_empresa_id'] = utf8_encode($colaborador['catalogo_empresa_id']);
          $colaborador['catalogo_puesto_id'] = utf8_encode($colaborador['catalogo_puesto_id']);
          $colaborador['catalogo_ubicacion_id'] = utf8_encode($colaborador['catalogo_ubicacion_id']);
          $colaborador['catalogo_departamento_id'] = utf8_encode($colaborador['catalogo_departamento_id']);
          $colaborador['pago'] = utf8_encode($colaborador['pago']);
          $colaborador['fecha_alta'] = utf8_encode($colaborador['fecha_alta']);
          $colaborador['fecha_baja'] = utf8_encode($colaborador['fecha_baja']);


        $tabla.=<<<html
        <div style="page-break-inside: avoid;">
          <table border="0" style="width:100%;text-align: center; margin-bottom: 30px;">
            <tr>
              <td colspan="1" style="background-color:#B8B8B8;"><strong>Colaborador Id: </strong> {$colaborador['catalogo_colaboradores_id']}</td>
              <td colspan="2" style="background-color:#B8B8B8;"><strong>RFC: </strong> {$colaborador['rfc']}</td>
            </tr>
            <tr>
              <td colspan="1" rowspan="2" style="background-color:#E4E4E4;"><img class="foto" src="/img/colaboradores/{$colaborador['foto']}" /></td>
              <td colspan="2" style="background-color:#B8B8B8;"><strong>Nombre: </strong> {$colaborador['nombre']} {$colaborador['apellido_paterno']} {$colaborador['apellido_materno']}</td>
              </tr>
              <tr>
              <td colspan="2" style="background-color:#E4E4E4;"><strong>Número de Identificador: </strong> {$colaborador['numero_identificador']}</td>
            </tr>
            <tr>
              <td colspan="1" style="background-color:#E4E4E4;"><strong>Sexo: </strong> {$colaborador['sexo']}</td>
              <td colspan="1" style="background-color:#E4E4E4;"><strong>Status: </strong> {$colaborador['status']}</td>
              <td colspan="1" style="background-color:#E4E4E4;"><strong>Numero Empleado: </strong> {$colaborador['numero_empleado']}</td>
            </tr>
            <tr>
              <td colspan="1" style="background-color:#E4E4E4;"><strong>Opción: </strong> {$colaborador['opcion']}</td>
              <td colspan="2" style="background-color:#E4E4E4;"><strong>Competencias: </strong> {$colaborador['catalogo_empresa_id']}</td>
            </tr>
            <tr>
              <td colspan="1" style="background-color:#E4E4E4;"><strong>Puesto: </strong> {$colaborador['catalogo_puesto_id']}</td>
              <td colspan="2" style="background-color:#E4E4E4;"><strong>Ubicacion: </strong> {$colaborador['catalogo_ubicacion_id']}</td>
            </tr>
            <tr>
              <td colspan="1" style="background-color:#E4E4E4;"><strong>Departamento: </strong> {$colaborador['catalogo_departamento_id']}</td>
              <td colspan="2" style="background-color:#E4E4E4;"><strong>Horario</strong>
html;
              foreach (ColaboradoresDao::getHorarioById($colaborador['catalogo_colaboradores_id']) as $key => $horario) {
                  $tabla .=<<<html
                      <span class="badge incentivo" >{$horario['nombre']}</span>
html;
              }
              $tabla .=<<<html
              </td>
            </tr>
            <tr>
              <td colspan="1" style="background-color:#E4E4E4;"><strong>Pago: </strong> {$colaborador['pago']}</td>
              <td colspan="2" style="background-color:#E4E4E4;"><strong>Incentivos: </strong>
html;

              foreach (ColaboradoresDao::getIncentivoById($colaborador['catalogo_colaboradores_id']) as $key => $incentivo) {
                $signo = "$";
                $tabla .=<<<html
                <br><span class="badge incentivo" style="background-color: {$incentivo['color']} ;">{$incentivo['nombre']}: {$signo}{$incentivo['cantidad']}</span>
html;
              }


              $tabla .=<<<html
              </td>
            </tr>
            <tr>
              <td colspan="1" style="background-color:#E4E4E4;"><strong>Fecha Alta: </strong> {$colaborador['fecha_alta']}</td>
              <td colspan="2" style="background-color:#E4E4E4;"><strong>Fecha Baja: </strong> {$colaborador['fecha_baja']}</td>
            </tr>
          </table>
        </div>
html;
          }
      }
      $mpdf->WriteHTML($style,1);
      $mpdf->WriteHTML($tabla,2);
  	  print_r($mpdf->Output());
      exit;
    }

    public function generarExcel(){
      $ids = MasterDom::getDataAll("borrar");
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

      $objPHPExcel = new \PHPExcel();
      $objPHPExcel->getProperties()->setCreator("jma");
      $objPHPExcel->getProperties()->setLastModifiedBy("jma");
      $objPHPExcel->getProperties()->setTitle("Reporte");
      $objPHPExcel->getProperties()->setSubject("Reporte");
      $objPHPExcel->getProperties()->setDescription("Descripcion");
      $objPHPExcel->setActiveSheetIndex(0);

      /*AGREGAR IMAGEN AL EXCEL*/
      //$gdImage = imagecreatefromjpeg('http://52.32.114.10:8070/img/ag_logo.jpg');
      $gdImage = imagecreatefrompng('http://52.32.114.10:8070/img/ag_logo.png');
      // Add a drawing to the worksheetecho date('H:i:s') . " Add a drawing to the worksheet\n";
      $objDrawing = new \PHPExcel_Worksheet_MemoryDrawing();
      $objDrawing->setName('Sample image');$objDrawing->setDescription('Sample image');
      $objDrawing->setImageResource($gdImage);
      //$objDrawing->setRenderingFunction(\PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
      $objDrawing->setRenderingFunction(\PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG);
      $objDrawing->setMimeType(\PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
      $objDrawing->setWidth(50);
      $objDrawing->setHeight(125);
      $objDrawing->setCoordinates('A1');
      $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

      $estilo_titulo = array(
        'font' => array('bold' => true,'name'=>'Verdana','size'=>12, 'color' => array('rgb' => 'FEAE41')),
        'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
        'type' => \PHPExcel_Style_Fill::FILL_SOLID
      );

      $estilo_encabezado = array(
        'font' => array('bold' => true,'name'=>'Verdana','size'=>10, 'color' => array('rgb' => 'FEAE41')),
        'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
        'type' => \PHPExcel_Style_Fill::FILL_SOLID
      );

      $estilo_celda = array(
        'font' => array('bold' => false,'name'=>'Verdana','size'=>8,'color' => array('rgb' => 'B59B68')),
        'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
        'type' => \PHPExcel_Style_Fill::FILL_SOLID

      );


      $fila = 9;
      $adaptarTexto = true;
      $controlador = "Colaboradores";
      $columna = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T');
      $nombreColumna = array('Id','Nombre','Apellido Paterno','Apellido Materno','Status','Motivo','Sexo','Numero Identificador','RFC','Competencias','Ubicación','Departamento','Puesto','Horario','Fecha Alta','Fecha Baja','Pago','Incentivo','Opción','Número Empleado');
      $nombreCampo = array('catalogo_colaboradores_id','nombre','apellido_paterno','apellido_materno','status','motivo','sexo','numero_identificador','rfc','catalogo_empresa_id','catalogo_ubicacion_id','catalogo_departamento_id','catalogo_puesto_id','catalogo_horario_id','fecha_alta','fecha_baja','pago','catalogo_incentivo','opcion','numero_empleado');

      $objPHPExcel->getActiveSheet()->SetCellValue('A'.$fila, 'Reporte de Colaboradores');
      $objPHPExcel->getActiveSheet()->mergeCells('A'.$fila.':'.$columna[count($nombreColumna)-1].$fila);
      $objPHPExcel->getActiveSheet()->getStyle('A'.$fila)->applyFromArray($estilo_titulo);
      $objPHPExcel->getActiveSheet()->getStyle('A'.$fila)->getAlignment()->setWrapText($adaptarTexto);

      $fila +=1;

      /*COLUMNAS DE LOS DATOS DEL ARCHIVO EXCEL*/
      foreach ($nombreColumna as $key => $value) {
        $objPHPExcel->getActiveSheet()->SetCellValue($columna[$key].$fila, $value);
        $objPHPExcel->getActiveSheet()->getStyle($columna[$key].$fila)->applyFromArray($estilo_encabezado);
        $objPHPExcel->getActiveSheet()->getStyle($columna[$key].$fila)->getAlignment()->setWrapText($adaptarTexto);
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($key)->setAutoSize(true);
      }
      $fila +=1; //fila donde comenzaran a escribirse los datos

      if($ids!=''){
        foreach ($ids as $key => $value) {
          $colaborador = ColaboradoresDao::getByIdReporte($value);
          foreach ($nombreCampo as $llave => $campo) {

            if($campo == 'catalogo_incentivo'){
              $colaborador[$campo] = '';
              foreach (ColaboradoresDao::getIncentivoById($colaborador['catalogo_colaboradores_id']) as $k => $v) {
                $colaborador[$campo] .= '('.$v['nombre'].': $'.$v['cantidad'].'),';
              }
            }

            if($campo == 'catalogo_horario_id'){
              $colaborador[$campo] = '';
              foreach (ColaboradoresDao::getHorarioById($colaborador['catalogo_colaboradores_id']) as $k => $v) {
                $colaborador[$campo] .= $v['nombre'].',';
              }
            }
            $objPHPExcel->getActiveSheet()->SetCellValue($columna[$llave].$fila, html_entity_decode($colaborador[$campo], ENT_QUOTES, "UTF-8"));
            $objPHPExcel->getActiveSheet()->getStyle($columna[$llave].$fila)->applyFromArray($estilo_celda);
            $objPHPExcel->getActiveSheet()->getStyle($columna[$llave].$fila)->getAlignment()->setWrapText($adaptarTexto);
          }
          $fila +=1;
        }
      }else{
        foreach (ColaboradoresDao::getAllReporte($filtro) as $key => $value) {

          foreach ($nombreCampo as $llave => $campo) {

            if($campo == 'catalogo_incentivo'){
              $value[$campo] = '';
              foreach (ColaboradoresDao::getIncentivoById($value['catalogo_colaboradores_id']) as $k => $v) {
                $value[$campo] .= '('.$v['nombre'].': $'.$v['cantidad'].'),';
              }
            }

            if($campo == 'catalogo_horario_id'){
              $value[$campo] = 'Horario';
              foreach (ColaboradoresDao::getHorarioById($value['catalogo_colaboradores_id']) as $k => $v) {
                $value[$campo] .= $v['nombre'].',';
              }
            }

            $objPHPExcel->getActiveSheet()->SetCellValue($columna[$llave].$fila, html_entity_decode($value[$campo], ENT_QUOTES, "UTF-8"));
            $objPHPExcel->getActiveSheet()->getStyle($columna[$llave].$fila)->applyFromArray($estilo_celda);
            $objPHPExcel->getActiveSheet()->getStyle($columna[$llave].$fila)->getAlignment()->setWrapText($adaptarTexto);
          }
          $fila +=1;
        }
      }

      $objPHPExcel->getActiveSheet()->getStyle('A1:'.$columna[count($columna)-1].$fila)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      for ($i=0; $i <$fila ; $i++) {
        $objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(20);
      }

      $objPHPExcel->getActiveSheet()->setTitle('Reporte');

      header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
      header('Content-Disposition: attachment;filename="Reporte AG '.$controlador.'.xlsx"');
      header('Cache-Control: max-age=0');
      header('Cache-Control: max-age=1');
      header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
      header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
      header ('Cache-Control: cache, must-revalidate');
      header ('Pragma: public');

      \PHPExcel_Settings::setZipClass(\PHPExcel_Settings::PCLZIP);
      $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
      $objWriter->save('php://output');
    }

    public function getHeader(){
    $extraHeader =<<<html
      <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css" />
      <style>
        .incentivo{margin: 2px;font: message-box;height:100%;}
        .foto{width:100px;height:100px;border-radius: 50px;}
      </style>
html;
    return $extraHeader;
  }

  public function getFooter(){
    $extraFooter =<<<html
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
            } );

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


           $("#btnExcel").click(function(){
              $('#all').attr('action', '/Colaboradores/generarExcel/');
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });

            $("#btnBajas").click(function(){
               $('#all').attr('action', '/Colaboradores/bajas/');
               $('#all').attr('target', '_blank');
               $("#all").submit();
             });

            $("#btnPDF").click(function(){
              $('#all').attr('action', '/Colaboradores/generarPDF/');
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });

            $("#delete").click(function(){
              var seleccionados = $("input[name='borrar[]']:checked").length;
              if(seleccionados>0){
                alertify.confirm('¿Segúro que desea eliminar lo seleccionado?', function(response){
                  if(response){
                    $('#all').attr('target', '');
                    $('#all').attr('action', '/Colaboradores/delete');
                    $("#all").submit();
                    alertify.success("Se ha eliminado correctamente");
                  }
                });
              }else{
                alertify.confirm('Selecciona al menos uno para eliminar');
              }
            });

            /*$("select").change(function(){
              $.ajax({
                url: "/Colaboradores/getTabla",
                type: "POST",
                data: $("#all").serialize(),
                success: function(data){
                  $("#registros").html(data);
                }
              });
            });*/
        });
      </script>
html;
    return $extraFooter;
  }

    public function alerta($reporte='', $parametro='add'){
      $regreso = "/Colaboradores/";
      $accion = '';

      if($parametro == 'add')
        $accion = "agregado";

      if($parametro == 'edit')
        $accion = "modificado";

      if($parametro == 'delete')
            $accion = "eliminado";

       $mensaje = '';

       if($reporte->_id_colaborador!= '' && intval($reporte->_id_colaborador) > 0){
         $mensaje .=<<<html
         <div class="alert alert-success">
          <strong>Success!</strong> Se ha $accion correctamente el colaborador con el Id $reporte->_id_colaborador.
        </div>
html;
       }

       if($reporte->_numero_empleado!=''){
         if(intval($reporte->_numero_empleado) >= 0){
           $mensaje .=<<<html
           <div class="alert alert-success">
           <strong>Success!</strong> Se ha asigando correctamente el numero de empleado.
           </div>
html;
         }else{
           $mensaje .=<<<html
           <div class="alert alert-error">
           <strong>Error!</strong> No se asigno el numero de empleado debido a un error.
           </div>
html;
         }
       }

       if(count($reporte->_horarios)>0){
         foreach ($reporte->_horarios as $key => $value) {
           if(intval($value) >= 0){
             $mensaje .=<<<html
             <div class="alert alert-success">
             <strong>Success!</strong> Se ha asigando correctamente el horario al colaborador.
             </div>
html;
           }else{
             $mensaje .=<<<html
             <div class="alert alert-error">
             <strong>Error!</strong> No se asigno el horario debido a un error.
             </div>
html;
           }
         }
       }

        if(count($reporte->_incentivos)>0){

         foreach ($reporte->_incentivos as $key => $value) {
           if(intval($value) >= 0){
             $mensaje .=<<<html
             <div class="alert alert-success">
             <strong>Success!</strong> Se ha asigando correctamente el incentivo al colaborador.
             </div>
html;
           }else{
             $mensaje .=<<<html
             <div class="alert alert-error">
             <strong>Error!</strong> No se asigno el incentivo debido a un error.
             </div>
html;
           }
         }
        }

        if($reporte->_ids >= 1){
          $id = $reporte->_ids;
          $mensaje .=<<<html
             <div class="alert alert-success">
             <strong>Success!</strong> Se ha eliminado el colaborador.
             </div>
html;
        }


        View::set('class',$class);
        View::set('regreso',$regreso);
        View::set('mensaje',$mensaje);
        View::set('header',$this->_contenedor->header($extraHeader));
        View::set('footer',$this->_contenedor->footer($extraFooter));
        View::render("alertas");
    }

}
