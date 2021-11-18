<?php
namespace App\controllers;
defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \App\controllers\Contenedor;
use \Core\Controller;
use \App\models\Administradores AS AdministradoresDao;

class Administradores extends Controller{

    private $_contenedor;

    function __construct(){
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header',$this->_contenedor->header());
        View::set('footer',$this->_contenedor->footer());
        if(Controller::getPermisosUsuario($this->__usuario, "permisos_globales",7) == 0)
          header('Location: /Principal/');
    }

    public function index() {
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


            $("#export_pdf").click(function(){
              $('#all').attr('action', '/Administradores/generarPDF/');
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });

            $("#export_excel").click(function(){
              $('#all').attr('action', '/Administradores/generarExcel/');
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });

            $("#delete").click(function(){
              var seleccionados = $("input[name='borrar[]']:checked").length;
              if(seleccionados>0){
                alertify.confirm('¿Segúro que desea eliminar lo seleccionado?', function(response){
                  if(response){
                    $('#all').attr('target', '');
                    $('#all').attr('action', '/Administradores/delete');
                    $("#all").submit();
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
      $Administradoress = AdministradoresDao::getAll();
      $tabla= '';
      foreach ($Administradoress as $key => $value) {
        $explode =  explode('_', $value['identificador']);
        $identificador = strtoupper($explode['0']);
        $administrador_id = $value['administrador_id'];
        $tabla.=<<<html
                <tr>
                <td style="vertical-align:middle;"><input type="checkbox" name="borrar[]" value="{$value['administrador_id']}"/></td>
                <td style="vertical-align:middle;">
                    <b>Nombre</b><br>
                    {$value['nombre']}<br><br>
                    <b>Usuario</b><br>
                    {$value['usuario']} <br><br>
                    <b>Perfil Usuario</b> <br>
                    {$value['nombre_perfil']} <br><br>
                    <b>Planta</b> <br>
                    {$value['nombre_planta']} <br><br>
                    <b>Identificador</b> <br>
                    {$identificador} <br><br>
                </td>
                <td style="vertical-align:middle;">
html;

        $s1 = explode("-",$value['seccion_empresas']);
        $s2 = explode("-",$value['seccion_plantas']);
        $s3 = explode("-",$value['seccion_horarios']);
        $s4 = explode("-",$value['seccion_departamentos']);
        $s5 = explode("-",$value['seccion_ubicaciones']);
        $s6 = explode("-",$value['seccion_lectores']);
        $s7 = explode("-",$value['seccion_dias_festivos']);
        $s8 = explode("-",$value['seccion_motivo_bajas']);
        $s9 = explode("-",$value['seccion_incidencias']);
        $s10 = explode("-",$value['seccion_puestos']);
        $s11 = explode("-",$value['seccion_incentivos']);
        $s12 = explode("-",$value['seccion_colaboradores']);
        $s13 = explode("-",$value['seccion_incentivosadd']);
        $s14 = explode("-",$value['seccion_periodo']);
        $s15 = explode("-",$value['seccion_registro_incidencias']);
        $s16 = explode("-",$value['seccion_resumen']);
        $s17 = explode("-",$value['seccion_prorrateo']);

        $ver1 = ($s1['0'] == 1) ? "Empresas":"";
        $pdf1 = ($s1['1'] == 2) ? "<span class=\"fa fa-file-pdf-o\" style=\"font-size:10px;\"></span>":"";
        $excel1 = ($s1['2'] == 3) ? "<span class=\"fa fa-file-excel-o\" style=\"font-size:10px;\"></span>":"";
        $agregar1 = ($s1['3'] == 4) ? "<span class=\"fa fa-check\" style=\"font-size:10px;\"></span>":"";
        $editar1 = ($s1['4'] == 5) ? "<span class=\"fa fa-edit\" style=\"font-size:10px;\"></span>":"";
        $eliminar1 = ($s1['5'] == 6) ? "<span class=\"fa fa-close\" style=\"font-size:10px;\"></span>":"";

        $ver2 = ($s2['0'] == 1) ? "Plantas":"";
        $pdf2 = ($s2['1'] == 2) ? "<span class=\"fa fa-file-pdf-o\" style=\"font-size:10px;\"></span>":"";
        $excel2 = ($s2['2'] == 3) ? "<span class=\"fa fa-file-excel-o\" style=\"font-size:10px;\"></span>":"";
        $agregar2 = ($s2['3'] == 4) ? "<span class=\"fa fa-check\" style=\"font-size:10px;\"></span>":"";
        $editar2 = ($s2['4'] == 5) ? "<span class=\"fa fa-edit\" style=\"font-size:10px;\"></span>":"";
        $eliminar2 = ($s2['5'] == 6) ? "<span class=\"fa fa-close\" style=\"font-size:10px;\"></span>":"";

        $ver3 = ($s3['0'] == 1) ? "Horarios":"";
        $pdf3 = ($s3['1'] == 2) ? "<span class=\"fa fa-file-pdf-o\" style=\"font-size:10px;\"></span>":"";
        $excel3 = ($s3['2'] == 3) ? "<span class=\"fa fa-file-excel-o\" style=\"font-size:10px;\"></span>":"";
        $agregar3 = ($s3['3'] == 4) ? "<span class=\"fa fa-check\" style=\"font-size:10px;\"></span>":"";
        $editar3 = ($s3['4'] == 5) ? "<span class=\"fa fa-edit\" style=\"font-size:10px;\"></span>":"";
        $eliminar3 = ($s3['5'] == 6) ? "<span class=\"fa fa-close\" style=\"font-size:10px;\"></span>":"";

        $ver4 = ($s4['0'] == 1) ? "Departament":"";
        $pdf4 = ($s4['1'] == 2) ? "<span class=\"fa fa-file-pdf-o\" style=\"font-size:10px;\"></span>":"";
        $excel4 = ($s4['2'] == 3) ? "<span class=\"fa fa-file-excel-o\" style=\"font-size:10px;\"></span>":"";
        $agregar4 = ($s4['3'] == 4) ? "<span class=\"fa fa-check\" style=\"font-size:10px;\"></span>":"";
        $editar4 = ($s4['4'] == 5) ? "<span class=\"fa fa-edit\" style=\"font-size:10px;\"></span>":"";
        $eliminar4 = ($s4['5'] == 6) ? "<span class=\"fa fa-close\" style=\"font-size:10px;\"></span>":"";

        $ver5 = ($s5['0'] == 1) ? "Ubicaciones":"";
        $pdf5 = ($s5['1'] == 2) ? "<span class=\"fa fa-file-pdf-o\" style=\"font-size:10px;\"></span>":"";
        $excel5 = ($s5['2'] == 3) ? "<span class=\"fa fa-file-excel-o\" style=\"font-size:10px;\"></span>":"";
        $agregar5 = ($s5['3'] == 4) ? "<span class=\"fa fa-check\" style=\"font-size:10px;\"></span>":"";
        $editar5 = ($s5['4'] == 5) ? "<span class=\"fa fa-edit\" style=\"font-size:10px;\"></span>":"";
        $eliminar5 = ($s5['5'] == 6) ? "<span class=\"fa fa-close\" style=\"font-size:10px;\"></span>":"";

        $ver6 = ($s6['0'] == 1) ? "Lectores":"";
        $pdf6 = ($s6['1'] == 2) ? "<span class=\"fa fa-file-pdf-o\" style=\"font-size:10px;\"></span>":"";
        $excel6 = ($s6['2'] == 3) ? "<span class=\"fa fa-file-excel-o\" style=\"font-size:10px;\"></span>":"";
        $agregar6 = ($s6['3'] == 4) ? "<span class=\"fa fa-check\" style=\"font-size:10px;\"></span>":"";
        $editar6 = ($s6['4'] == 5) ? "<span class=\"fa fa-edit\" style=\"font-size:10px;\"></span>":"";
        $eliminar6 = ($s6['5'] == 6) ? "<span class=\"fa fa-close\" style=\"font-size:10px;\"></span>":"";

        $ver7 = ($s7['0'] == 1) ? "Horarios":"";
        $pdf7 = ($s7['1'] == 2) ? "<span class=\"fa fa-file-pdf-o\" style=\"font-size:10px;\"></span>":"";
        $excel7 = ($s7['2'] == 3) ? "<span class=\"fa fa-file-excel-o\" style=\"font-size:10px;\"></span>":"";
        $agregar7 = ($s7['3'] == 4) ? "<span class=\"fa fa-check\" style=\"font-size:10px;\"></span>":"";
        $editar7 = ($s7['4'] == 5) ? "<span class=\"fa fa-edit\" style=\"font-size:10px;\"></span>":"";
        $eliminar7 = ($s7['5'] == 6) ? "<span class=\"fa fa-close\" style=\"font-size:10px;\"></span>":"";

        $ver8 = ($s8['0'] == 1) ? "Motivo Bajas":"";
        $pdf8 = ($s8['1'] == 2) ? "<span class=\"fa fa-file-pdf-o\" style=\"font-size:10px;\"></span>":"";
        $excel8 = ($s8['2'] == 3) ? "<span class=\"fa fa-file-excel-o\" style=\"font-size:10px;\"></span>":"";
        $agregar8 = ($s8['3'] == 4) ? "<span class=\"fa fa-check\" style=\"font-size:10px;\"></span>":"";
        $editar8 = ($s8['4'] == 5) ? "<span class=\"fa fa-edit\" style=\"font-size:10px;\"></span>":"";
        $eliminar8 = ($s8['5'] == 6) ? "<span class=\"fa fa-close\" style=\"font-size:10px;\"></span>":"";

        $ver9 = ($s9['0'] == 1) ? "Incidencias":"";
        $pdf9 = ($s9['1'] == 2) ? "<span class=\"fa fa-file-pdf-o\" style=\"font-size:10px;\"></span>":"";
        $excel9 = ($s9['2'] == 3) ? "<span class=\"fa fa-file-excel-o\" style=\"font-size:10px;\"></span>":"";
        $agregar9 = ($s9['3'] == 4) ? "<span class=\"fa fa-check\" style=\"font-size:10px;\"></span>":"";
        $editar9 = ($s9['4'] == 5) ? "<span class=\"fa fa-edit\" style=\"font-size:10px;\"></span>":"";
        $eliminar9 = ($s9['5'] == 6) ? "<span class=\"fa fa-close\" style=\"font-size:10px;\"></span>":"";

        $ver10 = ($s10['0'] == 1) ? "Puestos":"";
        $pdf10 = ($s10['1'] == 2) ? "<span class=\"fa fa-file-pdf-o\" style=\"font-size:10px;\"></span>":"";
        $excel10 = ($s10['2'] == 3) ? "<span class=\"fa fa-file-excel-o\" style=\"font-size:10px;\"></span>":"";
        $agregar10 = ($s10['3'] == 4) ? "<span class=\"fa fa-check\" style=\"font-size:10px;\"></span>":"";
        $editar10 = ($s10['4'] == 5) ? "<span class=\"fa fa-edit\" style=\"font-size:10px;\"></span>":"";
        $eliminar10 = ($s10['5'] == 6) ? "<span class=\"fa fa-close\" style=\"font-size:10px;\"></span>":"";

        $ver11 = ($s11['0'] == 1) ? "Incentivos":"";
        $pdf11 = ($s11['1'] == 2) ? "<span class=\"fa fa-file-pdf-o\" style=\"font-size:10px;\"></span>":"";
        $excel11 = ($s11['2'] == 3) ? "<span class=\"fa fa-file-excel-o\" style=\"font-size:10px;\"></span>":"";
        $agregar11 = ($s11['3'] == 4) ? "<span class=\"fa fa-check\" style=\"font-size:10px;\"></span>":"";
        $editar11 = ($s11['4'] == 5) ? "<span class=\"fa fa-edit\" style=\"font-size:10px;\"></span>":"";
        $eliminar11 = ($s11['5'] == 6) ? "<span class=\"fa fa-close\" style=\"font-size:10px;\"></span>":"";

        $ver12 = ($s12['0'] == 1) ? "Colaboradores":"";
        $pdf12 = ($s12['1'] == 2) ? "<span class=\"fa fa-file-pdf-o\" style=\"font-size:10px;\"></span>":"";
        $excel12 = ($s12['2'] == 3) ? "<span class=\"fa fa-file-excel-o\" style=\"font-size:10px;\"></span>":"";
        $agregar12 = ($s12['3'] == 4) ? "<span class=\"fa fa-check\" style=\"font-size:10px;\"></span>":"";
        $editar12 = ($s12['4'] == 5) ? "<span class=\"fa fa-edit\" style=\"font-size:10px;\"></span>":"";
        $eliminar12 = ($s12['5'] == 6) ? "<span class=\"fa fa-close\" style=\"font-size:10px;\"></span>":"";

        $ver13 = ($s13['0'] == 1) ? "Asignar Incentivos":"";
        $pdf13 = ($s13['1'] == 2) ? "<span class=\"fa fa-file-pdf-o\" style=\"font-size:10px;\"></span>":"";
        $excel13 = ($s13['2'] == 3) ? "<span class=\"fa fa-file-excel-o\" style=\"font-size:10px;\"></span>":"";
        $agregar13 = ($s13['3'] == 4) ? "<span class=\"fa fa-check\" style=\"font-size:10px;\"></span>":"";
        $editar13 = ($s13['4'] == 5) ? "<span class=\"fa fa-edit\" style=\"font-size:10px;\"></span>":"";
        $eliminar13 = ($s13['5'] == 6) ? "<span class=\"fa fa-close\" style=\"font-size:10px;\"></span>":"";

        $ver14 = ($s14['0'] == 1) ? "Periodo":"";
        $pdf14 = ($s14['1'] == 2) ? "<span class=\"fa fa-file-pdf-o\" style=\"font-size:10px;\"></span>":"";
        $excel14 = ($s14['2'] == 3) ? "<span class=\"fa fa-file-excel-o\" style=\"font-size:10px;\"></span>":"";
        $agregar14 = ($s14['3'] == 4) ? "<span class=\"fa fa-check\" style=\"font-size:10px;\"></span>":"";
        $editar14 = ($s14['4'] == 5) ? "<span class=\"fa fa-edit\" style=\"font-size:10px;\"></span>":"";
        $eliminar14 = ($s14['5'] == 6) ? "<span class=\"fa fa-close\" style=\"font-size:10px;\"></span>":"";

        $ver15 = ($s15['0'] == 1) ? "Reg. Incidencia":"";
        $pdf15 = ($s15['1'] == 2) ? "<span class=\"fa fa-file-pdf-o\" style=\"font-size:10px;\"></span>":"";
        $excel15 = ($s15['2'] == 3) ? "<span class=\"fa fa-file-excel-o\" style=\"font-size:10px;\"></span>":"";
        $agregar15 = ($s15['3'] == 4) ? "<span class=\"fa fa-check\" style=\"font-size:10px;\"></span>":"";
        $editar15 = ($s15['4'] == 5) ? "<span class=\"fa fa-edit\" style=\"font-size:10px;\"></span>":"";
        $eliminar15 = ($s15['5'] == 6) ? "<span class=\"fa fa-close\" style=\"font-size:10px;\"></span>":"";

        $ver16 = ($s16['0'] == 1) ? "Resumen":"";
        $pdf16 = ($s16['1'] == 2) ? "<span class=\"fa fa-file-pdf-o\" style=\"font-size:10px;\"></span>":"";
        $excel16 = ($s16['2'] == 3) ? "<span class=\"fa fa-file-excel-o\" style=\"font-size:10px;\"></span>":"";
        $agregar16 = ($s16['3'] == 4) ? "<span class=\"fa fa-check\" style=\"font-size:10px;\"></span>":"";
        $editar16 = ($s16['4'] == 5) ? "<span class=\"fa fa-edit\" style=\"font-size:10px;\"></span>":"";
        $eliminar16 = ($s16['5'] == 6) ? "<span class=\"fa fa-close\" style=\"font-size:10px;\"></span>":"";

        $ver17 = ($s17['0'] == 1) ? "Prorrateo":"";
        $pdf17 = ($s17['1'] == 2) ? "<span class=\"fa fa-file-pdf-o\" style=\"font-size:10px;\"></span>":"";
        $excel17 = ($s17['2'] == 3) ? "<span class=\"fa fa-file-excel-o\" style=\"font-size:10px;\"></span>":"";
        $agregar17 = ($s17['3'] == 4) ? "<span class=\"fa fa-check\" style=\"font-size:10px;\"></span>":"";
        $editar17 = ($s17['4'] == 5) ? "<span class=\"fa fa-edit\" style=\"font-size:10px;\"></span>":"";
        $eliminar17 = ($s17['5'] == 6) ? "<span class=\"fa fa-close\" style=\"font-size:10px;\"></span>":"";

        $varSeccion1 = ($ver1!="")?"<li class=\"list-group-item col-md-3 col-sm-6 col-xs-6\"><span style=\"font-size:10px;\">{$ver1}</span> {$pdf1} {$excel1} {$agregar1} {$editar1} {$eliminar2}</li>": "";
        $varSeccion2 = ($ver2!="")?"<li class=\"list-group-item col-md-3 col-sm-6 col-xs-6\"><span style=\"font-size:10px;\">{$ver2}</span> {$pdf2} {$excel2} {$agregar2} {$editar2} {$eliminar2}</li>": "";
        $varSeccion3 = ($ver3!="")?"<li class=\"list-group-item col-md-3 col-sm-6 col-xs-6\"><span style=\"font-size:10px;\">{$ver3}</span> {$pdf3} {$excel3} {$agregar3} {$editar3} {$eliminar3}</li>": "";
        $varSeccion4 = ($ver4!="")?"<li class=\"list-group-item col-md-3 col-sm-6 col-xs-6\"><span style=\"font-size:10px;\">{$ver4}</span> {$pdf4} {$excel4} {$agregar4} {$editar4} {$eliminar4}</li>": "";
        $varSeccion5 = ($ver5!="")?"<li class=\"list-group-item col-md-3 col-sm-6 col-xs-6\"><span style=\"font-size:10px;\">{$ver5}</span> {$pdf5} {$excel5} {$agregar5} {$editar5} {$eliminar5}</li>": "";
        $varSeccion6 = ($ver6!="")?"<li class=\"list-group-item col-md-3 col-sm-6 col-xs-6\"><span style=\"font-size:10px;\">{$ver6}</span> {$pdf6} {$excel6} {$agregar6} {$editar6} {$eliminar6}</li>": "";
        $varSeccion7 = ($ver7!="")?"<li class=\"list-group-item col-md-3 col-sm-6 col-xs-6\"><span style=\"font-size:10px;\">{$ver7}</span> {$pdf7} {$excel7} {$agregar7} {$editar7} {$eliminar7}</li>": "";
        $varSeccion8 = ($ver8!="")?"<li class=\"list-group-item col-md-3 col-sm-6 col-xs-6\"><span style=\"font-size:10px;\">{$ver8}</span> {$pdf8} {$excel8} {$agregar8} {$editar8} {$eliminar8}</li>": "";
        $varSeccion9 = ($ver9!="")?"<li class=\"list-group-item col-md-3 col-sm-6 col-xs-6\"><span style=\"font-size:10px;\">{$ver9}</span> {$pdf9} {$excel9} {$agregar9} {$editar9} {$eliminar9}</li>": "";
        $varSeccion10 = ($ver10!="")?"<li class=\"list-group-item col-md-3 col-sm-6 col-xs-6\"><span style=\"font-size:10px;\">{$ver10}</span> {$pdf10} {$excel10} {$agregar10} {$editar10} {$eliminar10}</li>": "";
        $varSeccion11 = ($ver11!="")?"<li class=\"list-group-item col-md-3 col-sm-6 col-xs-6\"><span style=\"font-size:10px;\">{$ver11}</span> {$pdf11} {$excel11} {$agregar11} {$editar11} {$eliminar11}</li>": "";
        $varSeccion12 = ($ver12!="")?"<li class=\"list-group-item col-md-3 col-sm-6 col-xs-6\"><span style=\"font-size:10px;\">{$ver12}</span> {$pdf12} {$excel12} {$agregar12} {$editar12} {$eliminar12}</li>": "";
        $varSeccion13 = ($ver13!="")?"<li class=\"list-group-item col-md-3 col-sm-6 col-xs-6\"><span style=\"font-size:10px;\">{$ver13}</span> {$pdf13} {$excel13} {$agregar13} {$editar13} {$eliminar13}</li>": "";
        $varSeccion14 = ($ver14!="")?"<li class=\"list-group-item col-md-3 col-sm-6 col-xs-6\"><span style=\"font-size:10px;\">{$ver14}</span> {$pdf14} {$excel14} {$agregar14} {$editar14} {$eliminar14}</li>": "";
        $varSeccion15 = ($ver15!="")?"<li class=\"list-group-item col-md-3 col-sm-6 col-xs-6\"><span style=\"font-size:10px;\">{$ver15}</span> {$pdf15} {$excel15} {$agregar15} {$editar15} {$eliminar15}</li>": "";
        $varSeccion16 = ($ver16!="")?"<li class=\"list-group-item col-md-3 col-sm-6 col-xs-6\"><span style=\"font-size:10px;\">{$ver16}</span> {$pdf16} {$excel16} {$agregar16} {$editar16} {$eliminar16}</li>": "";
        $varSeccion17 = ($ver17!="")?"<li class=\"list-group-item col-md-3 col-sm-6 col-xs-6\"><span style=\"font-size:10px;\">{$ver17}</span> {$pdf17} {$excel17} {$agregar17} {$editar17} {$eliminar17}</li>": "";

        $tabla .= "<div class=\"list-group\">" .$varSeccion1 . $varSeccion2 . $varSeccion3 . $varSeccion4 . $varSeccion5 . $varSeccion6 . $varSeccion7 . $varSeccion8 . $varSeccion9 . $varSeccion10 . $varSeccion11 . $varSeccion12 . $varSeccion13 . $varSeccion14 . $varSeccion15 . $varSeccion16 . $varSeccion17 ."</div></td>";
        $HaySeccion = AdministradoresDao::getDepartamentosAdministrador($value['administrador_id']);
        $tabla.=<<<html
                <td style="vertical-align:middle;" class="center">
html;
        foreach ($HaySeccion as $llave => $valor) {
          $tabla .=<<<html
            <p>{$valor['nombre']}</p>
html;
        }

          $tabla.=<<<html
                </td>
                <td style="vertical-align:middle;" class="center">
                    <a href="/Administradores/edit/{$value['administrador_id']}" class="btn btn-primary"><span class="fa fa-pencil-square-o" style="color:white"></span> </a>
                </td>
              </tr>
html;
      }

      View::set('tabla',$tabla);
      View::set('header',$this->_contenedor->header($extraHeader));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("administradores_all");
    }

    public function add(){
      $extraFooter =<<<html
      <script>
        $(document).ready(function(){

          $.validator.addMethod("checkUserName",
            function(value, element) {
              var result = false;
              $.ajax({
                type:"POST",
                async: false,
                url: "/Administradores/isValidateUser", // script to validate in server side
                data: {
                    usuario: function() {
                      return $("#usuario").val();
                    }},
                success: function(data) {
                    console.log("success::: " + data);
                    result = (data == "true") ? false : true;

                    if(result == true){
                      $('#availability').html('<span class="text-success glyphicon glyphicon-ok"></span><span> Usuario valido</span>');
                      $('#register').attr("disabled", true);
                    }else{
                      $('#availability').html('<span class="text-danger glyphicon glyphicon-remove"></span>');
                      $('#register').attr("disabled", false);
                    }
                }
              });
              // return true if username is exist in database
              return result;
              },
              "¡Este nombre de usuario ya está en uso! Prueba otro."
          );

          $.validator.addMethod("noSpace",
            function(value, element) {
              return value.indexOf(" ") < 0 && value != "";
              },
              "No se permite que tenga espacios este campo de usuario"
          );

          $("#add").validate({
            rules:{
              nombre:{
                required: true
              },
              usuario:{
                required: true,
                checkUserName: true,
                noSpace: true
              },
              contrasena_1:{
                required: true
              },
              contrasena_2:{
                required: true,
                equalTo: "#contrasena_1"
              },
              descripcion:{
                required: true
              },
              perfil_id:{
                required: true
              },
              status:{
                required: true
              },
              planta:{
                required: true
              },
              tipo:{
                required: true
              },
              departamento:{
                required: true
              },
              identificador:{
                required: true
              }
              
            },
            messages:{
              nombre:{
                required: "Este campo es requerido"
              },
              usuario:{
                required: "Este campo es requerido"
              },
              contrasena_1:{
                required: "Este campo es requerido"
              },
              contrasena_2:{
                required: "Este campo es requerido",
                equalTo: "La contraseña no es igual"
              },
              descripcion:{
                required: "Este campo es requerido"
              },
              perfil_id:{
                required: "Este campo es requerido"
              },
              status:{
                required: "Este campo es requerido"
              },
              planta:{
                required: "Este campo es requerido"
              },
              tipo:{
                required: "Este campo es requerido"
              },
              departamento:{
                required: "Este campo es requerido"
              },
              identificador:{
                required: "Este campo es requerido"
              }
              
            }
          });//fin del jquery validate

          $("#btnCancel").click(function(){
            window.location.href = "/Administradores/";
          });//fin del btnAdd

          
          $('#myCheck1').change(function(){
            if(this.checked){
              document.getElementById("pdf1").disabled = false;
              document.getElementById("excel1").disabled = false;
              document.getElementById("agregar1").disabled = false;
              document.getElementById("editar1").disabled = false;
              document.getElementById("eliminar1").disabled = false;
            }else{
              document.getElementById("pdf1").disabled = true;
              document.getElementById("excel1").disabled = true;
              document.getElementById("agregar1").disabled = true;
              document.getElementById("editar1").disabled = true ;
              document.getElementById("eliminar1").disabled = true ;
            }
          });
          $('#myCheck2').change(function(){
            if(this.checked){
              document.getElementById("pdf2").disabled = false;
              document.getElementById("excel2").disabled = false;
              document.getElementById("agregar2").disabled = false;
              document.getElementById("editar2").disabled = false;
              document.getElementById("eliminar2").disabled = false;
            }else{
              document.getElementById("pdf2").disabled = true;
              document.getElementById("excel2").disabled = true;
              document.getElementById("agregar2").disabled = true;
              document.getElementById("editar2").disabled = true ;
              document.getElementById("eliminar2").disabled = true ;
            }
          });
          $('#myCheck3').change(function(){
            if(this.checked){
              document.getElementById("pdf3").disabled = false;
              document.getElementById("excel3").disabled = false;
              document.getElementById("agregar3").disabled = false;
              document.getElementById("editar3").disabled = false;
              document.getElementById("eliminar3").disabled = false;
            }else{
              document.getElementById("pdf3").disabled = true;
              document.getElementById("excel3").disabled = true;
              document.getElementById("agregar3").disabled = true;
              document.getElementById("editar3").disabled = true ;
              document.getElementById("eliminar3").disabled = true ;
            }
          });
          $('#myCheck4').change(function(){
            if(this.checked){
              document.getElementById("pdf4").disabled = false;
              document.getElementById("excel4").disabled = false;
              document.getElementById("agregar4").disabled = false;
              document.getElementById("editar4").disabled = false;
              document.getElementById("eliminar4").disabled = false;
            }else{
              document.getElementById("pdf4").disabled = true;
              document.getElementById("excel4").disabled = true;
              document.getElementById("agregar4").disabled = true;
              document.getElementById("editar4").disabled = true ;
              document.getElementById("eliminar4").disabled = true ;
            }
          });
          $('#myCheck5').change(function(){
            if(this.checked){
              document.getElementById("pdf5").disabled = false;
              document.getElementById("excel5").disabled = false;
              document.getElementById("agregar5").disabled = false;
              document.getElementById("editar5").disabled = false;
              document.getElementById("eliminar5").disabled = false;
            }else{
              document.getElementById("pdf5").disabled = true;
              document.getElementById("excel5").disabled = true;
              document.getElementById("agregar5").disabled = true;
              document.getElementById("editar5").disabled = true ;
              document.getElementById("eliminar5").disabled = true ;
            }
          });
          $('#myCheck6').change(function(){
            if(this.checked){
              document.getElementById("pdf6").disabled = false;
              document.getElementById("excel6").disabled = false;
              document.getElementById("agregar6").disabled = false;
              document.getElementById("editar6").disabled = false;
              document.getElementById("eliminar6").disabled = false;
            }else{
              document.getElementById("pdf6").disabled = true;
              document.getElementById("excel6").disabled = true;
              document.getElementById("agregar6").disabled = true;
              document.getElementById("editar6").disabled = true ;
              document.getElementById("eliminar6").disabled = true ;
            }
          });
          $('#myCheck7').change(function(){
            if(this.checked){
              document.getElementById("pdf7").disabled = false;
              document.getElementById("excel7").disabled = false;
              document.getElementById("agregar7").disabled = false;
              document.getElementById("editar7").disabled = false;
              document.getElementById("eliminar7").disabled = false;
            }else{
              document.getElementById("pdf7").disabled = true;
              document.getElementById("excel7").disabled = true;
              document.getElementById("agregar7").disabled = true;
              document.getElementById("editar7").disabled = true ;
              document.getElementById("eliminar7").disabled = true ;
            }
          });
          $('#myCheck8').change(function(){
            if(this.checked){
              document.getElementById("pdf8").disabled = false;
              document.getElementById("excel8").disabled = false;
              document.getElementById("agregar8").disabled = false;
              document.getElementById("editar8").disabled = false;
              document.getElementById("eliminar8").disabled = false;
            }else{
              document.getElementById("pdf8").disabled = true;
              document.getElementById("excel8").disabled = true;
              document.getElementById("agregar8").disabled = true;
              document.getElementById("editar8").disabled = true ;
              document.getElementById("eliminar8").disabled = true ;
            }
          });
          $('#myCheck9').change(function(){
            if(this.checked){
              document.getElementById("pdf9").disabled = false;
              document.getElementById("excel9").disabled = false;
              document.getElementById("agregar9").disabled = false;
              document.getElementById("editar9").disabled = false;
              document.getElementById("eliminar9").disabled = false;
            }else{
              document.getElementById("pdf9").disabled = true;
              document.getElementById("excel9").disabled = true;
              document.getElementById("agregar9").disabled = true;
              document.getElementById("editar9").disabled = true ;
              document.getElementById("eliminar9").disabled = true ;
            }
          });
          $('#myCheck10').change(function(){
            if(this.checked){
              document.getElementById("pdf10").disabled = false;
              document.getElementById("excel10").disabled = false;
              document.getElementById("agregar10").disabled = false;
              document.getElementById("editar10").disabled = false;
              document.getElementById("eliminar10").disabled = false;
            }else{
              document.getElementById("pdf10").disabled = true;
              document.getElementById("excel10").disabled = true;
              document.getElementById("agregar10").disabled = true;
              document.getElementById("editar10").disabled = true ;
              document.getElementById("eliminar10").disabled = true ;
            }
          });
          $('#myCheck11').change(function(){
            if(this.checked){
              document.getElementById("pdf11").disabled = false;
              document.getElementById("excel11").disabled = false;
              document.getElementById("agregar11").disabled = false;
              document.getElementById("editar11").disabled = false;
              document.getElementById("eliminar11").disabled = false;
            }else{
              document.getElementById("pdf11").disabled = true;
              document.getElementById("excel11").disabled = true;
              document.getElementById("agregar11").disabled = true;
              document.getElementById("editar11").disabled = true ;
              document.getElementById("eliminar11").disabled = true ;
            }
          });
          $('#myCheck12').change(function(){
            if(this.checked){
              document.getElementById("pdf12").disabled = false;
              document.getElementById("excel12").disabled = false;
              document.getElementById("agregar12").disabled = false;
              document.getElementById("editar12").disabled = false;
              document.getElementById("eliminar12").disabled = false;
            }else{
              document.getElementById("pdf12").disabled = true;
              document.getElementById("excel12").disabled = true;
              document.getElementById("agregar12").disabled = true;
              document.getElementById("editar12").disabled = true ;
              document.getElementById("eliminar12").disabled = true ;
            }
          });
          $('#myCheck13').change(function(){
            if(this.checked){
              document.getElementById("pdf13").disabled = false;
              document.getElementById("excel13").disabled = false;
              document.getElementById("agregar13").disabled = false;
              document.getElementById("editar13").disabled = false;
              document.getElementById("eliminar13").disabled = false;
            }else{
              document.getElementById("pdf13").disabled = true;
              document.getElementById("excel13").disabled = true;
              document.getElementById("agregar13").disabled = true;
              document.getElementById("editar13").disabled = true ;
              document.getElementById("eliminar13").disabled = true ;
            }
          });

          $('#myCheck14').change(function(){
            if(this.checked){
              document.getElementById("pdf14").disabled = false;
              document.getElementById("excel14").disabled = false;
              document.getElementById("agregar14").disabled = false;
              document.getElementById("editar14").disabled = false;
              document.getElementById("eliminar14").disabled = false;
            }else{
              document.getElementById("pdf14").disabled = true;
              document.getElementById("excel14").disabled = true;
              document.getElementById("agregar14").disabled = true;
              document.getElementById("editar14").disabled = true ;
              document.getElementById("eliminar14").disabled = true ;
            }
          });

          $('#myCheck15').change(function(){
            if(this.checked){
              document.getElementById("pdf15").disabled = false;
              document.getElementById("excel15").disabled = false;
              document.getElementById("agregar15").disabled = false;
              document.getElementById("editar15").disabled = false;
              document.getElementById("eliminar15").disabled = false;
            }else{
              document.getElementById("pdf15").disabled = true;
              document.getElementById("excel15").disabled = true;
              document.getElementById("agregar15").disabled = true;
              document.getElementById("editar15").disabled = true ;
              document.getElementById("eliminar15").disabled = true ;
            }
          });

          $('#myCheck16').change(function(){
            if(this.checked){
              document.getElementById("pdf16").disabled = false;
              document.getElementById("excel16").disabled = false;
              document.getElementById("agregar16").disabled = false;
              document.getElementById("editar16").disabled = false;
              document.getElementById("eliminar16").disabled = false;
            }else{
              document.getElementById("pdf16").disabled = true;
              document.getElementById("excel16").disabled = true;
              document.getElementById("agregar16").disabled = true;
              document.getElementById("editar16").disabled = true ;
              document.getElementById("eliminar16").disabled = true ;
            }
          });

          $('#myCheck17').change(function(){
            if(this.checked){
              document.getElementById("pdf17").disabled = false;
              document.getElementById("excel17").disabled = false;
              document.getElementById("agregar17").disabled = false;
              document.getElementById("editar17").disabled = false;
              document.getElementById("eliminar17").disabled = false;
            }else{
              document.getElementById("pdf17").disabled = true;
              document.getElementById("excel17").disabled = true;
              document.getElementById("agregar17").disabled = true;
              document.getElementById("editar17").disabled = true ;
              document.getElementById("eliminar17").disabled = true ;
            }
          });

        });//fin del document.ready

        function showDiv(elem){

          if(elem.value == '1'){
            document.getElementById('permiosos-root').style.display = "block";
            document.getElementById('permiosos-personalizados').style.display = "none";
            document.getElementById('permiosos-recursos-humanos').style.display = "none";
            document.getElementById('permiosos-personales').style.display = "none";
            document.getElementById('permiosos-prorrateo').style.display = "none";
            document.getElementById('departamentos').style.display = "none";
            document.getElementById('add-departamentos').style.display = "none";
            document.getElementById('permiosos-administrador').style.display = "none";
          }

          if(elem.value == '2'){
            document.getElementById('permiosos-root').style.display = "none";
            document.getElementById('permiosos-administrador').style.display = "none";
            document.getElementById('permiosos-recursos-humanos').style.display = "none";
            document.getElementById('permiosos-personales').style.display = "block";
            document.getElementById('permiosos-prorrateo').style.display = "none";
            document.getElementById('departamentos').style.display = "block";
            document.getElementById('add-departamentos').style.display = "block";
            document.getElementById('permiosos-personalizados').style.display = "block";
          }

          if(elem.value == '3'){
            document.getElementById('permiosos-root').style.display = "none";
            document.getElementById('permiosos-personalizados').style.display = "none";
            document.getElementById('permiosos-recursos-humanos').style.display = "none";
            document.getElementById('permiosos-personales').style.display = "none";
            document.getElementById('permiosos-prorrateo').style.display = "block";
            document.getElementById('departamentos').style.display = "none";
            document.getElementById('add-departamentos').style.display = "none";
            document.getElementById('permiosos-administrador').style.display = "none";
          }


          if(elem.value == '4'){
            document.getElementById('permiosos-root').style.display = "none";
            document.getElementById('permiosos-personalizados').style.display = "none";
            document.getElementById('permiosos-recursos-humanos').style.display = "none";
            document.getElementById('permiosos-prorrateo').style.display = "none";
            document.getElementById('permiosos-personales').style.display = "none";
            document.getElementById('departamentos').style.display = "block";
            document.getElementById('add-departamentos').style.display = "block";
            document.getElementById('permiosos-administrador').style.display = "block";
          }

          if(elem.value == '5'){
            document.getElementById('permiosos-root').style.display = "none";
            document.getElementById('permiosos-administrador').style.display = "none";
            document.getElementById('permiosos-recursos-humanos').style.display = "none";
            document.getElementById('permiosos-personales').style.display = "none";
            document.getElementById('permiosos-prorrateo').style.display = "none";
            document.getElementById('departamentos').style.display = "block";
            document.getElementById('add-departamentos').style.display = "block";
            document.getElementById('permiosos-personalizados').style.display = "block";
          }

          if(elem.value == '6'){
            document.getElementById('permiosos-root').style.display = "none";
            document.getElementById('permiosos-administrador').style.display = "none";
            document.getElementById('permiosos-personalizados').style.display = "none";
            document.getElementById('permiosos-personales').style.display = "none";
            document.getElementById('permiosos-prorrateo').style.display = "none";
            //document.getElementById('departamentos').style.display = "none";
            //document.getElementById('add-departamentos').style.display = "none";
            document.getElementById('permiosos-recursos-humanos').style.display = "block";

          }

        }

        $("#btnDepartamentoAdd").click(function(){
            var id = $("#departamento").val();
            if(id!=""){
              var nombre = $("#departamento option:selected").text();
              var color = $("#departamento option:selected").attr("color");

              var html = '<div class="col-md-4 col-sm-4 col-xs-4" id="contenedor_'+id+'" style="background-color: #f7f7f7; border-radius:5px; margin:5px; justify-content: center;">';
              html += '<i class="fa fa-times-circle-o cerrar col-md-1 col-sm-1 col-xs-1" id="'+id+'" nombre="'+nombre+'" color="'+color+'" style="font-size: 18px; margin-top:3px; margin-left:5px; color:#C9302C;"></i>';
              html += '<div class="col-md-11 col-sm-11 col-xs-11" style="justify-content: center;margin-top:10px;">';
              html += '<input class="checkbox checkbox-circle col-md-2 col-sm-2 col-xs-2" type="checkbox" name="departamento[]" value="'+id+'" checked="checked" />';
              html += '<label class="control-label col-md-8 col-sm-8 col-xs-8" style="text-align: center;color:#000000;" >'+nombre+'</label>';
              html += '</div>';
              html += '</div>';

              $("#departamento_asignado").append(html);
              $("#departamento option:selected").remove();

              if($("#departamento option").length<=1){
                $("#btnDepartamentoAdd").hide();
              }
            }
          });

          $(document).on("click","i.cerrar",function(event){

            var id = $(this).attr("id");
            $("#contenedor_"+id).remove();
            var nombre = $(this).attr("nombre");

            $("#departamento").append('<option value="'+id+'" >'+nombre+'</option>');

            if($("#departamento option").length>1){
              $("#btnDepartamentoAdd").show();
            }

          });

      </script>

html;
        $plantas = "";
        foreach (AdministradoresDao::getPlantas() as $key => $value) {
        $plantas .=<<<html
            <option value="{$value['catalogo_planta_id']}">{$value['nombre']}</option>
html;
        }

        $perfiles = "";
        foreach (AdministradoresDao::getPerfiles() as $key => $value) {
        $perfiles .=<<<html
            <option value="{$value['perfil_id']}">{$value['nombre']}</option>
html;
        }

        $status = "";
        foreach (AdministradoresDao::getStatus() as $key => $value) {
        $status .=<<<html
            <option value="{$value['catalogo_status_id']}">{$value['nombre']}</option>
html;
      }

        $tabla1= '';
        foreach (AdministradoresDao::getSeccionesMenu() as $key => $value) {
            $tabla1.=<<<html
          <tr>
            <td>
              <input type="checkbox" id="myCheck{$value['utilerias_seccion_id']}" name="seccion{$value['utilerias_seccion_id']}" > {$value['nombre_seccion']}
            </td>
            <td>
              <input class="toggle botonEstado" name="pdf{$value['utilerias_seccion_id']}" id="pdf{$value['utilerias_seccion_id']}" type="checkbox" data-toggle="toggle" disabled >
            </td>
            <td>
              <input class="toggle botonEstado" name="excel{$value['utilerias_seccion_id']}" id="excel{$value['utilerias_seccion_id']}" type="checkbox" data-toggle="toggle" disabled >
            </td>
            <td>
              <input class="toggle botonEstado" name="agregar{$value['utilerias_seccion_id']}" id="agregar{$value['utilerias_seccion_id']}" type="checkbox" data-toggle="toggle" disabled >
            </td>
            <td>
              <!--No <input type="checkbox" class="js-switch" id="b{$value['utilerias_seccion_id']}" name="editar{$value['utilerias_seccion_id']}"/> Si-->
              <input class="toggle botonEstado" name="editar{$value['utilerias_seccion_id']}" id="editar{$value['utilerias_seccion_id']}" type="checkbox" data-toggle="toggle" disabled >
            </td>
            <td>
              <!--No <input type="checkbox" class="js-switch" name="eliminar{$value['utilerias_seccion_id']}"/> Si -->
              <input class="toggle botonEstado" name="eliminar{$value['utilerias_seccion_id']}" id="eliminar{$value['utilerias_seccion_id']}" type="checkbox" data-toggle="toggle" disabled >
            </td>
        </tr>
html;
      }

      $departamentos = '';
      foreach (AdministradoresDao::getDepartamentos() as $key => $value) {
        $departamentos .=<<<html
        <option value="{$value['catalogo_departamento_id']}">{$value['nombre']}</option>
html;
      }

      $tipoSeleccion = array('semanal' => 'semanal', 'quincenal' => 'quincenal');
      
      $tipo = "";
      foreach ($tipoSeleccion as $key => $value) {
        $selected = ($value == $administrador['tipo']) ? "selected" : "";
        $tipo .=<<<html
              <option {$selected} value="{$value}">{$value}</option>
html;
      }
      

      View::set('tipo', $tipo);
      View::set('plantas',$plantas);
      View::set('permisos', $tabla1);
      View::set('perfiles', $perfiles);
      View::set('departamentos',$departamentos);
      View::set('status',$status);
      View::set('header',$this->_contenedor->header(''));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("administrador_add");
    }

    public function isValidateUser(){
      $dato = AdministradoresDao::getUser($_POST['usuario']);
      if($dato == 1){
        echo "true";
      }else{
        echo "false";
      }
    }

    public function administradorAdd(){

        $administrador = new \stdClass();

        $administrador->_nombre = MasterDom::getData('nombre');
        $administrador->_usuario = MasterDom::getData('usuario');
        $administrador->_planta = MasterDom::getData('planta');
        $administrador->_identificador = MasterDom::getData('identificador');
        $permisos = new \stdClass();

        if(MasterDom::getData('contrasena_1') == MasterDom::getData('contrasena_2')){
            $contrasenaMD5 = MD5(MasterDom::getData('contrasena_1'));
            $administrador->_contrasena = $contrasenaMD5;
        }

        $administrador->_perfil_id = MasterDom::getData('perfil_id');
        $administrador->_descripcion = MasterDom::getData('descripcion');
        $administrador->_status = MasterDom::getData('status');
        $administrador->_tipo = 0;

      $departamento = MasterDom::getDataAll('departamento');

      if(MasterDom::getData('perfil_id') == 4 || MasterDom::getData('perfil_id') == 6 || MasterDom::getData('perfil_id') == 1){
        $permisos->_usuario = MasterDom::getData('usuario');

        if(MasterDom::getData('perfil_id') == 4)
           $permisos->_permisos_globales = 1; 

        if(MasterDom::getData('perfil_id') == 6)
            $permisos->_permisos_globales = 2; 

        if(MasterDom::getData('perfil_id') == 1)
            $permisos->_permisos_globales = 1;
        
        
        $arrSecciones = array(1=>"empresas",2=>"plantas",3=>"horarios",4=>"departamentos",5=>"ubicaciones",6=>"lectores",7=>"dias_festivos",8=>"motivo_bajas",9=>"incidencias",10=>"puestos",11=>"incentivos",12=>"colaboradores",13=>"Asignar_incentivos",14=>"Periodo",15=>"Registro_incidencias",16=>"Resumen",17=>"Prorrateo");
        $permisos->_seccion_empresas = "1-2-3-4-5-6";
        for ($i=1; $i <= 17; $i++) {
          $sec = "_seccion_" . $arrSecciones[$i];
          $permisos->$sec = "1-2-3-4-5-6";
        }
      }

        if(MasterDom::getData('perfil_id') == 5 || MasterDom::getData('perfil_id') == 2){
            $permisos->_usuario = MasterDom::getData('usuario');
            $permisos->_permisos_globales = 0;
            $arrSecciones = array(1=>"empresas",2=>"plantas",3=>"horarios",4=>"departamentos",5=>"ubicaciones",6=>"lectores",7=>"dias_festivos",8=>"motivo_bajas",9=>"incidencias",10=>"puestos",11=>"incentivos",12=>"colaboradores",13=>"Asignar_incentivos",14=>"Periodo",15=>"Registro_incidencias",16=>"Resumen",17=>"Prorrateo");
            for ($i=1; $i <= 17; $i++) {
                $seccion = "seccion" . $i; $pdf = "pdf" . $i; $excel = "excel" . $i; $agregar = "agregar" . $i; $editar = "editar" . $i; $eliminar = "eliminar" . $i;
                $varSeccion = "_" . $seccion;
                $varPdf = "_" . $pdf; $varExcel = "_" . $excel; $varAgregar = "_" . $agregar; $varEditar = "_" . $editar; $varEliminar = "_" . $eliminar;
                $resultSeccion = (MasterDom::getData($seccion) == "on") ? "1" : "0";
                $resultPdf = (MasterDom::getData($pdf) == "on") ? "2" : "0";
                $resultExcel = (MasterDom::getData($excel) == "on") ? "3" : "0";
                $resultAgregar = (MasterDom::getData($agregar) == "on") ? "4" : "0";
                $resultEditar = (MasterDom::getData($editar) == "on") ? "5" : "0";
                $resultEliminar = (MasterDom::getData($eliminar) == "on") ? "6" : "0";
                $sec = "_seccion_" . $arrSecciones[$i];
                $permisos->$sec = $resultSeccion . "-" .$resultPdf . "-" . $resultExcel . "-" . $resultAgregar . "-" . $resultEditar . "-" . $resultEliminar;
        }
      }

      if(MasterDom::getData('perfil_id') == 3){
            $permisos->_usuario = MasterDom::getData('usuario');
            $permisos->_permisos_globales = 0;
            $arrSecciones = array(1=>"empresas",2=>"plantas",3=>"horarios",4=>"departamentos",5=>"ubicaciones",6=>"lectores",7=>"dias_festivos",8=>"motivo_bajas",9=>"incidencias",10=>"puestos",11=>"incentivos",12=>"colaboradores",13=>"Asignar_incentivos",14=>"Periodo",15=>"Registro_incidencias",16=>"Resumen",17=>"Prorrateo");
            for ($i=1; $i <= 17; $i++) {
                $seccion = "seccion" . $i; $pdf = "pdf" . $i; $excel = "excel" . $i; $agregar = "agregar" . $i; $editar = "editar" . $i; $eliminar = "eliminar" . $i;
                $varSeccion = "_" . $seccion;
                $varPdf = "_" . $pdf; $varExcel = "_" . $excel; $varAgregar = "_" . $agregar; $varEditar = "_" . $editar; $varEliminar = "_" . $eliminar;
                $resultSeccion = (MasterDom::getData($seccion) == "on") ? "0" : "0";
                $resultPdf = (MasterDom::getData($pdf) == "on") ? "0" : "0";
                $resultExcel = (MasterDom::getData($excel) == "on") ? "0" : "0";
                $resultAgregar = (MasterDom::getData($agregar) == "on") ? "0" : "0";
                $resultEditar = (MasterDom::getData($editar) == "on") ? "0" : "0";
                $resultEliminar = (MasterDom::getData($eliminar) == "on") ? "0" : "0";
                $sec = "_seccion_" . $arrSecciones[$i];
                $permisos->$sec = $resultSeccion . "-" .$resultPdf . "-" . $resultExcel . "-" . $resultAgregar . "-" . $resultEditar . "-" . $resultEliminar;
        }
        $permisos->_seccion_Prorrateo = '1-2-3-4-5-6';
      }

      $idAdministrador = AdministradoresDao::insert($administrador);
      $idPermisos = AdministradoresDao::insertPermisos($permisos);

        //if(MasterDom::getData('perfil_id') != 6){
        $secciones = AdministradoresDao::insertarDepartamentos($idAdministrador, MasterDom::getDataAll('departamento'));
        //$departamento = MasterDom::getDataAll('departamento');
        //if($departamento >= 1){
        //    foreach ($departamento as $key => $value) {
                /*if($secciones >= 0){  echo "ok";
                }else{echo "no";}*/
        //    }
        //}else{
        //    $this->alerta($id,'sin_departamento');
        //}
      //}

      if($idAdministrador >= 1 && $idPermisos >= 1){
        $this->alerta($id,'add');
      }else{
        $this->alerta($id,'error');
      }

    }

    public function edit($id){
      $extraFooter =<<<html
      <script>
        $(document).ready(function(){

          $("#editt").validate({
            rules:{
              descripcion:{
                required: true
              },
              perfil_id:{
                required: true
              },
              status:{
                required: true
              }
            },
            messages:{
              descripcion:{
                required: "Este campo es requerido"
              },
              perfil_id:{
                required: "Este campo es requerido"
              },
              status:{
                required: "Este campo es requerido"
              }
            }
          });//fin del jquery validate

          $("#btnCancel").click(function(){
            window.location.href = "/Administradores/";
          });//fin del btnAdd

          $('#myCheck1').change(function(){
            if(this.checked){
              document.getElementById("pdf1").disabled = false;
              document.getElementById("excel1").disabled = false;
              document.getElementById("agregar1").disabled = false;
              document.getElementById("editar1").disabled = false;
              document.getElementById("eliminar1").disabled = false;
            }else{
              document.getElementById("pdf1").disabled = true;
              document.getElementById("excel1").disabled = true;
              document.getElementById("agregar1").disabled = true;
              document.getElementById("editar1").disabled = true ;
              document.getElementById("eliminar1").disabled = true ;
            }
          });
          $('#myCheck2').change(function(){
            if(this.checked){
              document.getElementById("pdf2").disabled = false;
              document.getElementById("excel2").disabled = false;
              document.getElementById("agregar2").disabled = false;
              document.getElementById("editar2").disabled = false;
              document.getElementById("eliminar2").disabled = false;
            }else{
              document.getElementById("pdf2").disabled = true;
              document.getElementById("excel2").disabled = true;
              document.getElementById("agregar2").disabled = true;
              document.getElementById("editar2").disabled = true ;
              document.getElementById("eliminar2").disabled = true ;
            }
          });
          $('#myCheck3').change(function(){
            if(this.checked){
              document.getElementById("pdf3").disabled = false;
              document.getElementById("excel3").disabled = false;
              document.getElementById("agregar3").disabled = false;
              document.getElementById("editar3").disabled = false;
              document.getElementById("eliminar3").disabled = false;
            }else{
              document.getElementById("pdf3").disabled = true;
              document.getElementById("excel3").disabled = true;
              document.getElementById("agregar3").disabled = true;
              document.getElementById("editar3").disabled = true ;
              document.getElementById("eliminar3").disabled = true ;
            }
          });
          $('#myCheck4').change(function(){
            if(this.checked){
              document.getElementById("pdf4").disabled = false;
              document.getElementById("excel4").disabled = false;
              document.getElementById("agregar4").disabled = false;
              document.getElementById("editar4").disabled = false;
              document.getElementById("eliminar4").disabled = false;
            }else{
              document.getElementById("pdf4").disabled = true;
              document.getElementById("excel4").disabled = true;
              document.getElementById("agregar4").disabled = true;
              document.getElementById("editar4").disabled = true ;
              document.getElementById("eliminar4").disabled = true ;
            }
          });
          $('#myCheck5').change(function(){
            if(this.checked){
              document.getElementById("pdf5").disabled = false;
              document.getElementById("excel5").disabled = false;
              document.getElementById("agregar5").disabled = false;
              document.getElementById("editar5").disabled = false;
              document.getElementById("eliminar5").disabled = false;
            }else{
              document.getElementById("pdf5").disabled = true;
              document.getElementById("excel5").disabled = true;
              document.getElementById("agregar5").disabled = true;
              document.getElementById("editar5").disabled = true ;
              document.getElementById("eliminar5").disabled = true ;
            }
          });
          $('#myCheck6').change(function(){
            if(this.checked){
              document.getElementById("pdf6").disabled = false;
              document.getElementById("excel6").disabled = false;
              document.getElementById("agregar6").disabled = false;
              document.getElementById("editar6").disabled = false;
              document.getElementById("eliminar6").disabled = false;
            }else{
              document.getElementById("pdf6").disabled = true;
              document.getElementById("excel6").disabled = true;
              document.getElementById("agregar6").disabled = true;
              document.getElementById("editar6").disabled = true ;
              document.getElementById("eliminar6").disabled = true ;
            }
          });
          $('#myCheck7').change(function(){
            if(this.checked){
              document.getElementById("pdf7").disabled = false;
              document.getElementById("excel7").disabled = false;
              document.getElementById("agregar7").disabled = false;
              document.getElementById("editar7").disabled = false;
              document.getElementById("eliminar7").disabled = false;
            }else{
              document.getElementById("pdf7").disabled = true;
              document.getElementById("excel7").disabled = true;
              document.getElementById("agregar7").disabled = true;
              document.getElementById("editar7").disabled = true ;
              document.getElementById("eliminar7").disabled = true ;
            }
          });
          $('#myCheck8').change(function(){
            if(this.checked){
              document.getElementById("pdf8").disabled = false;
              document.getElementById("excel8").disabled = false;
              document.getElementById("agregar8").disabled = false;
              document.getElementById("editar8").disabled = false;
              document.getElementById("eliminar8").disabled = false;
            }else{
              document.getElementById("pdf8").disabled = true;
              document.getElementById("excel8").disabled = true;
              document.getElementById("agregar8").disabled = true;
              document.getElementById("editar8").disabled = true ;
              document.getElementById("eliminar8").disabled = true ;
            }
          });
          $('#myCheck9').change(function(){
            if(this.checked){
              document.getElementById("pdf9").disabled = false;
              document.getElementById("excel9").disabled = false;
              document.getElementById("agregar9").disabled = false;
              document.getElementById("editar9").disabled = false;
              document.getElementById("eliminar9").disabled = false;
            }else{
              document.getElementById("pdf9").disabled = true;
              document.getElementById("excel9").disabled = true;
              document.getElementById("agregar9").disabled = true;
              document.getElementById("editar9").disabled = true ;
              document.getElementById("eliminar9").disabled = true ;
            }
          });
          $('#myCheck10').change(function(){
            if(this.checked){
              document.getElementById("pdf10").disabled = false;
              document.getElementById("excel10").disabled = false;
              document.getElementById("agregar10").disabled = false;
              document.getElementById("editar10").disabled = false;
              document.getElementById("eliminar10").disabled = false;
            }else{
              document.getElementById("pdf10").disabled = true;
              document.getElementById("excel10").disabled = true;
              document.getElementById("agregar10").disabled = true;
              document.getElementById("editar10").disabled = true ;
              document.getElementById("eliminar10").disabled = true ;
            }
          });
          $('#myCheck11').change(function(){
            if(this.checked){
              document.getElementById("pdf11").disabled = false;
              document.getElementById("excel11").disabled = false;
              document.getElementById("agregar11").disabled = false;
              document.getElementById("editar11").disabled = false;
              document.getElementById("eliminar11").disabled = false;
            }else{
              document.getElementById("pdf11").disabled = true;
              document.getElementById("excel11").disabled = true;
              document.getElementById("agregar11").disabled = true;
              document.getElementById("editar11").disabled = true ;
              document.getElementById("eliminar11").disabled = true ;
            }
          });
          $('#myCheck12').change(function(){
            if(this.checked){
              document.getElementById("pdf12").disabled = false;
              document.getElementById("excel12").disabled = false;
              document.getElementById("agregar12").disabled = false;
              document.getElementById("editar12").disabled = false;
              document.getElementById("eliminar12").disabled = false;
            }else{
              document.getElementById("pdf12").disabled = true;
              document.getElementById("excel12").disabled = true;
              document.getElementById("agregar12").disabled = true;
              document.getElementById("editar12").disabled = true ;
              document.getElementById("eliminar12").disabled = true ;
            }
          });
          $('#myCheck13').change(function(){
            if(this.checked){
              document.getElementById("pdf13").disabled = false;
              document.getElementById("excel13").disabled = false;
              document.getElementById("agregar13").disabled = false;
              document.getElementById("editar13").disabled = false;
              document.getElementById("eliminar13").disabled = false;
            }else{
              document.getElementById("pdf13").disabled = true;
              document.getElementById("excel13").disabled = true;
              document.getElementById("agregar13").disabled = true;
              document.getElementById("editar13").disabled = true ;
              document.getElementById("eliminar13").disabled = true ;
            }
          });
          $('#myCheck14').change(function(){
            if(this.checked){
              document.getElementById("pdf14").disabled = false;
              document.getElementById("excel14").disabled = false;
              document.getElementById("agregar14").disabled = false;
              document.getElementById("editar14").disabled = false;
              document.getElementById("eliminar14").disabled = false;
            }else{
              document.getElementById("pdf14").disabled = true;
              document.getElementById("excel14").disabled = true;
              document.getElementById("agregar14").disabled = true;
              document.getElementById("editar14").disabled = true ;
              document.getElementById("eliminar14").disabled = true ;
            }
          });
          $('#myCheck15').change(function(){
            if(this.checked){
              document.getElementById("pdf15").disabled = false;
              document.getElementById("excel15").disabled = false;
              document.getElementById("agregar15").disabled = false;
              document.getElementById("editar15").disabled = false;
              document.getElementById("eliminar15").disabled = false;
            }else{
              document.getElementById("pdf15").disabled = true;
              document.getElementById("excel15").disabled = true;
              document.getElementById("agregar15").disabled = true;
              document.getElementById("editar15").disabled = true ;
              document.getElementById("eliminar15").disabled = true ;
            }
          });
          $('#myCheck16').change(function(){
            if(this.checked){
              document.getElementById("pdf16").disabled = false;
              document.getElementById("excel16").disabled = false;
              document.getElementById("agregar16").disabled = false;
              document.getElementById("editar16").disabled = false;
              document.getElementById("eliminar16").disabled = false;
            }else{
              document.getElementById("pdf16").disabled = true;
              document.getElementById("excel16").disabled = true;
              document.getElementById("agregar16").disabled = true;
              document.getElementById("editar16").disabled = true ;
              document.getElementById("eliminar16").disabled = true ;
            }
          });
          $('#myCheck17').change(function(){
            if(this.checked){
              document.getElementById("pdf17").disabled = false;
              document.getElementById("excel17").disabled = false;
              document.getElementById("agregar17").disabled = false;
              document.getElementById("editar17").disabled = false;
              document.getElementById("eliminar17").disabled = false;
            }else{
              document.getElementById("pdf17").disabled = true;
              document.getElementById("excel17").disabled = true;
              document.getElementById("agregar17").disabled = true;
              document.getElementById("editar17").disabled = true ;
              document.getElementById("eliminar17").disabled = true ;
            }
          });


          var selects = document.getElementById("perfil_id");
          var selectedValue = selects.options[selects.selectedIndex].value;

          $("#perfil_id").change(function(){

            //alert($("#perfil_id").val());

            if($("#perfil_id").val() == '1'){
                document.getElementById('permiosos-root').style.display = "block";
                document.getElementById('permiosos-personalizados').style.display = "none";
                document.getElementById('permiosos-recursos-humanos').style.display = "none";
                document.getElementById('permiosos-prorrateo').style.display = "none";
                document.getElementById('permiosos-personales').style.display = "none";
                document.getElementById('departamentos').style.display = "none";
                document.getElementById('add-departamentos').style.display = "none";
                document.getElementById('permiosos-administrador').style.display = "none";
            }

            if($("#perfil_id").val() == '2'){
            document.getElementById('permiosos-root').style.display = "none";
            document.getElementById('permiosos-administrador').style.display = "none";
            document.getElementById('permiosos-recursos-humanos').style.display = "none";
            document.getElementById('permiosos-prorrateo').style.display = "none";
            document.getElementById('permiosos-personales').style.display = "block";
            document.getElementById('departamentos').style.display = "block";
            document.getElementById('add-departamentos').style.display = "block";
            document.getElementById('permiosos-personalizados').style.display = "block";
          }

          if($("#perfil_id").val() == '3'){
            document.getElementById('permiosos-root').style.display = "none";
            document.getElementById('permiosos-personalizados').style.display = "none";
            document.getElementById('permiosos-recursos-humanos').style.display = "none";
            document.getElementById('permiosos-prorrateo').style.display = "block";
            document.getElementById('permiosos-personales').style.display = "none";
            document.getElementById('departamentos').style.display = "none";
            document.getElementById('add-departamentos').style.display = "none";
            document.getElementById('permiosos-administrador').style.display = "none";
          }

            if($("#perfil_id").val() == '4'){
              document.getElementById('permiosos-personalizados').style.display = "none";
              document.getElementById('permiosos-administrador').style.display = "block";
              document.getElementById('permiosos-prorrateo').style.display = "none";
              document.getElementById('permiosos-personales').style.display = "none";
            }

            if($("#perfil_id").val() == '5'){
              document.getElementById('permiosos-administrador').style.display = "none";
              document.getElementById('permiosos-personalizados').style.display = "block";
              document.getElementById('permiosos-prorrateo').style.display = "none";
              document.getElementById('permiosos-personales').style.display = "none";
            }

            if($("#perfil_id").val() == '6'){
              document.getElementById('permiosos-administrador').style.display = "none";
              document.getElementById('permiosos-personalizados').style.display = "none";
              document.getElementById('permiosos-recursos-humanos').style.display = "block";
              document.getElementById('permiosos-prorrateo').style.display = "none";
              document.getElementById('permiosos-personales').style.display = "none";
              //document.getElementById('departamentos').style.display = "none";
              //document.getElementById('add-departamentos').style.display = "none";
            }
          });

          $("#btnDepartamentoAdd").click(function(){
            var id = $("#departamento").val();
            if(id!=""){
              var nombre = $("#departamento option:selected").text();
              var color = $("#departamento option:selected").attr("color");

              var html = '<div class="col-md-4 col-sm-4 col-xs-4" id="contenedor_'+id+'" style="background-color: #f7f7f7; border-radius:5px; margin:5px; justify-content: center;">';
              html += '<i class="fa fa-times-circle-o cerrar col-md-1 col-sm-1 col-xs-1" id="'+id+'" nombre="'+nombre+'" color="'+color+'" style="font-size: 18px; margin-top:3px; margin-left:5px; color:#C9302C;"></i>';
              html += '<input type="hidden" name="departamento_id[]" value="'+id+'" checked="checked" />';
              html += '<label class="control-label col-md-8 col-sm-8 col-xs-8" style="text-align: center;color:#000000;" >'+nombre+'</label>';
              html += '</div>';

              $("#departamento_asignado").append(html);
              $("#departamento option:selected").remove();

              if($("#departamento option").length<=1){
                $("#btnDepartamentoAdd").hide();
              }
            }
          });

          $(document).on("click","i.cerrar",function(event){

            var id = $(this).attr("id");
            $("#contenedor_"+id).remove();
            var nombre = $(this).attr("nombre");

            $("#departamento").append('<option value="'+id+'" >'+nombre+'</option>');

            if($("#departamento option").length>1){
              $("#btnDepartamentoAdd").show();
            }

          });

          $("#perfil_id").change();

        });//fin del document.ready

      </script>
html;
      $administrador = AdministradoresDao::getById($id);

      $identificaciones = array();
      array_push($identificaciones, array("nombre"=>"xochimilco_entrada","titulo"=>"XOCHIMILCO"));
      array_push($identificaciones, array("nombre"=>"vallejo_entrada","titulo"=>"VALLEJO"));
      array_push($identificaciones, array("nombre"=>"gatsa_entrada","titulo"=>"GATSA"));
      array_push($identificaciones, array("nombre"=>"unidesh_entrada","titulo"=>"UNIDESH"));
      array_push($identificaciones, array("nombre"=>"produccion_entrada","titulo"=>"PRODUCCION"));
      $identificador = "";
      foreach ($identificaciones as $key => $value) {
        $selected = ($administrador['identificador'] == $value['nombre']) ? "selected" : "";
        $identificador .=<<<html
            <option {$selected} value="{$value['nombre']}">{$value['titulo']}</option>
html;
      }
    View::set('identificador',$identificador);

      $status = "";
      foreach (AdministradoresDao::getStatus() as $key => $value) {
        $selected = ($administrador['status'] == $value['catalogo_status_id'])? 'selected' : '';
        $status .=<<<html
        <option {$selected} value="{$value['catalogo_status_id']}">{$value['nombre']}</option>
html;
      }

      $perfiles = "";
      foreach (AdministradoresDao::getPerfil() as $key => $value) {
        $selected = ($administrador['perfil_id'] == $value['perfil_id'])? 'selected' : '';
        $perfiles .=<<<html
        <option {$selected} id="perfil{{$value['perfil_id']}}" value="{$value['perfil_id']}">{$value['nombre']}</option>
html;
      }

      $permisos_usuario = AdministradoresDao::getPermisosByUser($administrador['usuario']);
      $tabla1= '';
      foreach (AdministradoresDao::getSeccionesMenu() as $key => $value) {

        if($permisos_usuario['permisos_globales']==0){
          $seccion = 'seccion_'.strtolower($value['nombre_seccion']);
          $seccion = str_replace(' de ','_',$seccion);
          $seccion = str_replace(' ','_',$seccion);

          $permisos = $permisos_usuario[$seccion];

          $permisos = preg_replace('/1/','checked',$permisos,1);
          $permisos = preg_replace('/2/','checked',$permisos,1);
          $permisos = preg_replace('/3/','checked',$permisos,1);
          $permisos = preg_replace('/4/','checked',$permisos,1);
          $permisos = preg_replace('/5/','checked',$permisos,1);
          $permisos = preg_replace('/6/','checked',$permisos,1);
          $permisos = preg_replace('/0/','',$permisos);
          $permisos = explode('-',$permisos);

          $permisos = ($permisos[0]=='')? array('','','','','','') : $permisos; /*si no tiene el permiso de "ver" se quitan todos los demas permisos */
          $check_habilitado = ($permisos[0]=='')? array('','disabled','disabled','disabled','disabled') : array('','','','','',''); /*si no tiene el permiso de "ver" se quitan todos los demas permisos */
        }else{
          $permisos = array('checked','checked','checked','checked','checked','checked');
        }

        $tabla1.=<<<html
          <tr>
            <td>
              <input type="checkbox" id="myCheck{$value['utilerias_seccion_id']}" name="seccion{$value['utilerias_seccion_id']}" {$permisos[0]} {$check_habilitado[0]}> {$value['nombre_seccion']}
            </td>
            <td>
              <input class="toggle botonEstado" name="pdf{$value['utilerias_seccion_id']}" id="pdf{$value['utilerias_seccion_id']}" type="checkbox" data-toggle="toggle" {$permisos[1]} {$check_habilitado[1]} >
            </td>
            <td>
              <input class="toggle botonEstado" name="excel{$value['utilerias_seccion_id']}" id="excel{$value['utilerias_seccion_id']}" type="checkbox" data-toggle="toggle" {$permisos[2]} {$check_habilitado[2]} >
            </td>
            <td>
              <input class="toggle botonEstado" name="agregar{$value['utilerias_seccion_id']}" id="agregar{$value['utilerias_seccion_id']}" type="checkbox" data-toggle="toggle" {$permisos[3]} {$check_habilitado[3]} >
            </td>
            <td>
              <!--No <input type="checkbox" class="js-switch" id="b{$value['utilerias_seccion_id']}" name="editar{$value['utilerias_seccion_id']}"/> Si-->
              <input class="toggle botonEstado" name="editar{$value['utilerias_seccion_id']}" id="editar{$value['utilerias_seccion_id']}" type="checkbox" data-toggle="toggle" {$permisos[4]} {$check_habilitado[4]} >
            </td>
            <td>
              <!--No <input type="checkbox" class="js-switch" name="eliminar{$value['utilerias_seccion_id']}"/> Si -->
              <input class="toggle botonEstado" name="eliminar{$value['utilerias_seccion_id']}" id="eliminar{$value['utilerias_seccion_id']}" type="checkbox" data-toggle="toggle" {$permisos[5]} {$check_habilitado[5]} >
            </td>
        </tr>
html;
      }

      $departamentos = '';
      $departamento_asignado = '';
      
      foreach (AdministradoresDao::getDepartamentos() as $key => $value) {
        /*$existe = false;
        foreach (AdministradoresDao::getDepartamentosById($id) as $llave => $valor) {
          $existe = ($value['catalogo_departamento_id'] == $valor['catalogo_departamento_id']);
            if($existe==true){
              break;
            }
        }

        if($existe==true){
          $departamento_asignado .=<<<html
          <div class="col-md-4 col-sm-4 col-xs-4" id="contenedor_{$value['catalogo_departamento_id']}" style="background-color: #f7f7f7; border-radius:5px; margin:5px; justify-content: center;">
            <i class="fa fa-times-circle-o cerrar col-md-1 col-sm-1 col-xs-1" id="{$value['catalogo_departamento_id']}" nombre="{$value['nombre']}" color="#000000" style="font-size: 18px; margin-top:3px; margin-left:5px; color:#C9302C;"></i>
            <input type="hidden" name="departamento_id[]" id="departamento_id[]" value="{$value['catalogo_departamento_id']}"/>
              <label class="control-label col-md-8 col-sm-8 col-xs-8" style="text-align: center;color:#000000;" >{$value['nombre']}</label>
          </div>
html;
        }else{*/
            $selected = ($administrador['catalogo_departamento_id'] == $value['catalogo_departamento_id']) ? "selected" : "";
          $departamentos .=<<<html
          <option {$selected} value="{$value['catalogo_departamento_id']}">{$value['nombre']} </option>
html;
        //}
      }

      $tipoSeleccion = array('semanal' => 'semanal', 'quincenal' => 'quincenal');
      
      $tipo = "";
      foreach ($tipoSeleccion as $key => $value) {
        $selected = ($value == $administrador['tipo']) ? "selected" : "";
        $tipo .=<<<html
              <option {$selected} value="{$value}">{$value}</option>
html;
      }

        $plantas = "";
        foreach (AdministradoresDao::getPlantas() as $key => $value) {
            $selected = ($value['catalogo_planta_id'] == $administrador['catalogo_planta_id'] ) ? "selected" : "";
            $plantas .=<<<html
            <option {$selected} value="{$value['catalogo_planta_id']}">{$value['nombre']}</option>
html;
        }
      

        View::set('tipo', $tipo);
        View::set('plantas', $plantas);
        View::set('perfiles', $perfiles);
        View::set('status',$status);
        View::set('permisos',$tabla1);
        View::set('departamentos', $departamentos);
        View::set('administrador',$administrador);
        View::set('departamento_asignado',$departamento_asignado);
        View::set('header',$this->_contenedor->header(''));
        View::set('footer',$this->_contenedor->footer($extraFooter));
        View::render("administrador_edit");
    }

    public function delete(){
      $id = MasterDom::getDataAll('borrar');
      $array = array();
      foreach ($id as $key => $value) {
        $id = AdministradoresDao::delete($value);
        if($id['seccion'] == 2){
          array_push($array, array('seccion' => 2, 'id' => $id['id'] ));
        }else if($id['seccion'] == 1){
          array_push($array, array('seccion' => 1, 'id' => $id['id'] ));
        }
      }
      $this->alertas("Eliminacion de Administradores", $array, "/Administradores/");
    }


    public function administradoresEdit(){

        $administrador = new \stdClass();
        $permisos = new \stdClass();
        $administrador->_usuario = MasterDom::getData('usuario');
        $administrador->_planta = MasterDom::getData('planta');
        $administrador->_identificador = MasterDom::getData('identificador');

        $administrador->_perfil_id = MasterDom::getData('perfil_id');
        $administrador->_descripcion = MasterDom::getData('descripcion');
        $administrador->_tipo = 0;
        $administrador->_status = MasterDom::getData('status');

      // Eliminar todos los incentivos que se han agregado anterior mente
      $departamentos = $_POST['departamento_id'];
      $estatusDelete = array();
      if(empty($departamentos)){
        $deleteDepartamentoVacios = AdministradoresDao::deleteDepartamento(MasterDom::getData('administrador_id'));
      }else{
        foreach ($departamentos as $key => $value) {
          $deleteDepartamento = AdministradoresDao::deleteDepartamento(MasterDom::getData('administrador_id'));
        }
      }

      //foreach ($departamentos as $key => $value) {
        $addDepartamento = AdministradoresDao::insertarDepartamentos(MasterDom::getData('administrador_id'), MasterDom::getData('departamento'));
      //}

      // ACTUALIZAR DATOS DEL COLABORADOR
      $idAdministrador = AdministradoresDao::updateDataAdministrador($administrador);
      if(MasterDom::getData('perfil_id') == 1){
        $permisos->_usuario = MasterDom::getData('usuario');
        $permisos->_permisos_globales = 1;
        $arrSecciones = array(1=>"empresas",2=>"plantas",3=>"horarios",4=>"departamentos",5=>"ubicaciones",6=>"lectores",7=>"dias_festivos",8=>"motivo_bajas",9=>"incidencias",10=>"puestos",11=>"incentivos",12=>"colaboradores",13=>"Asignar_incentivos",14=>"Periodo",15=>"Registro_incidencias",16=>"Resumen",17=>"Prorrateo");
        $permisos->_seccion_empresas = "1-2-3-4-5-6";
        for ($i=1; $i <= 17; $i++) {
          $sec = "_seccion_" . $arrSecciones[$i];
          $permisos->$sec = "1-2-3-4-5-6";
        }
      }

      if(MasterDom::getData('perfil_id') == 4){
        $permisos->_usuario = MasterDom::getData('usuario');
        $permisos->_permisos_globales = 0;
        $arrSecciones = array(1=>"empresas",2=>"plantas",3=>"horarios",4=>"departamentos",5=>"ubicaciones",6=>"lectores",7=>"dias_festivos",8=>"motivo_bajas",9=>"incidencias",10=>"puestos",11=>"incentivos",12=>"colaboradores",13=>"Asignar_incentivos",14=>"Periodo",15=>"Registro_incidencias",16=>"Resumen",17=>"Prorrateo");
        $permisos->_seccion_empresas = "1-2-3-4-5-6";
        for ($i=1; $i <= 17; $i++) {
          $sec = "_seccion_" . $arrSecciones[$i];
          $permisos->$sec = "1-2-3-4-5-6";
        }
      }

      if(MasterDom::getData('perfil_id') == 6){
        $permisos->_usuario = MasterDom::getData('usuario');
        $permisos->_permisos_globales = 2;
        $arrSecciones = array(1=>"empresas",2=>"plantas",3=>"horarios",4=>"departamentos",5=>"ubicaciones",6=>"lectores",7=>"dias_festivos",8=>"motivo_bajas",9=>"incidencias",10=>"puestos",11=>"incentivos",12=>"colaboradores",13=>"Asignar_incentivos",14=>"Periodo",15=>"Registro_incidencias",16=>"Resumen",17=>"Prorrateo");
        $permisos->_seccion_empresas = "1-2-3-4-5-6";
        for ($i=1; $i <= 17; $i++) {
          $sec = "_seccion_" . $arrSecciones[$i];
          $permisos->$sec = "1-2-3-4-5-6";
        }
      }

      if(MasterDom::getData('perfil_id') == 5){
        $permisos->_usuario = MasterDom::getData('usuario');
        $permisos->_permisos_globales = 0;
        $arrSecciones = array(1=>"empresas",2=>"plantas",3=>"horarios",4=>"departamentos",5=>"ubicaciones",6=>"lectores",7=>"dias_festivos",8=>"motivo_bajas",9=>"incidencias",10=>"puestos",11=>"incentivos",12=>"colaboradores",13=>"Asignar_incentivos",14=>"Periodo",15=>"Registro_incidencias",16=>"Resumen",17=>"Prorrateo");
        for ($i=1; $i <= 17; $i++) {
          $seccion = "seccion" . $i; $pdf = "pdf" . $i; $excel = "excel" . $i; $agregar = "agregar" . $i; $editar = "editar" . $i; $eliminar = "eliminar" . $i;
          $varSeccion = "_" . $seccion;
          $varPdf = "_" . $pdf; $varExcel = "_" . $excel; $varAgregar = "_" . $agregar; $varEditar = "_" . $editar; $varEliminar = "_" . $eliminar;
          $resultSeccion = (MasterDom::getData($seccion) == "on") ? "1" : "0";
          $resultPdf = (MasterDom::getData($pdf) == "on") ? "2" : "0";
          $resultExcel = (MasterDom::getData($excel) == "on") ? "3" : "0";
          $resultAgregar = (MasterDom::getData($agregar) == "on") ? "4" : "0";
          $resultEditar = (MasterDom::getData($editar) == "on") ? "5" : "0";
          $resultEliminar = (MasterDom::getData($eliminar) == "on") ? "6" : "0";
          $sec = "_seccion_" . $arrSecciones[$i];
          $permisos->$sec = $resultSeccion . "-" .$resultPdf . "-" . $resultExcel . "-" . $resultAgregar . "-" . $resultEditar . "-" . $resultEliminar;
        }
      }

      $idPermisos = AdministradoresDao::updatePermisosUsuario($permisos);
      if( ($idAdministrador >= 1 || $idPermisos >= 1) || $$idPermisos >0){
        $this->alerta($id,'edit');
      }else{
        $this->alerta($id,'nothing');
      }

    }

    public function generarPDF(){
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
      </style>
html;
$tabla =<<<html
<img class="imagen" src="/img/ag_logo.png"/>
<br>
<div style="page-break-inside: avoid;" align='center'>
<H1 class="titulo">Administradoress</H1>
<table border="0" style="width:100%;text-align: center">
    <tr style="background-color:#B8B8B8;">
    <th><strong>Id</strong></th>
    <th><strong>Nombre</strong></th>
    <th><strong>Descripción</strong></th>
    <th><strong>Status</strong></th>
    </tr>
html;

      if($ids!=''){
        foreach ($ids as $key => $value) {
          $Administradores = AdministradoresDao::getByIdReporte($value);
            $tabla.=<<<html
              <tr style="background-color:#B8B8B8;">
              <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$Administradores['administrador_id']}</td>
              <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$Administradores['nombre']}</td>
              <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$Administradores['descripcion']}</td>
              <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$Administradores['nombre_status']}</td>
              </tr>
html;
        }
      }else{
        foreach (AdministradoresDao::getAll() as $key => $Administradores) {
          $tabla.=<<<html
            <tr style="background-color:#B8B8B8;">
            <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$Administradores['administrador_id']}</td>
            <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$Administradores['nombre']}</td>
            <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$Administradores['descripcion']}</td>
            <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$Administradores['nombre_status']}</td>
            </tr>
html;
          }
      }
      $tabla .=<<<html
      </table>
      </div>
html;
      $mpdf->WriteHTML($style,1);
      $mpdf->WriteHTML($tabla,2);

      //$nombre_archivo = "MPDF_".uniqid().".pdf";/* se genera un nombre unico para el archivo pdf*/
      print_r($mpdf->Output());/* se genera el pdf en la ruta especificada*/
      //echo $nombre_archivo;/* se imprime el nombre del archivo para poder retornarlo a CrmCatalogo/index */

      exit;
      //$ids = MasterDom::getDataAll('borrar');
      //echo shell_exec('php -f /home/granja/backend/public/librerias/mpdf_apis/Api.php Administradores '.json_encode(MasterDom::getDataAll('borrar')));
    }

    public function generarExcel(){
      $ids = MasterDom::getDataAll('borrar');
      $objPHPExcel = new \PHPExcel();
      $objPHPExcel->getProperties()->setCreator("jma");
      $objPHPExcel->getProperties()->setLastModifiedBy("jma");
      $objPHPExcel->getProperties()->setTitle("Reporte");
      $objPHPExcel->getProperties()->setSubject("Reorte");
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
        'font' => array('bold' => true,'name'=>'Verdana','size'=>16, 'color' => array('rgb' => 'FEAE41')),
        'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
        'type' => \PHPExcel_Style_Fill::FILL_SOLID
      );

      $estilo_encabezado = array(
        'font' => array('bold' => true,'name'=>'Verdana','size'=>14, 'color' => array('rgb' => 'FEAE41')),
        'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
        'type' => \PHPExcel_Style_Fill::FILL_SOLID
      );

      $estilo_celda = array(
        'font' => array('bold' => false,'name'=>'Verdana','size'=>12,'color' => array('rgb' => 'B59B68')),
        'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
        'type' => \PHPExcel_Style_Fill::FILL_SOLID

      );


      $fila = 9;
      $adaptarTexto = true;

      $controlador = "Administradores";
      $columna = array('A','B','C','D');
      $nombreColumna = array('Id','Nombre','Descripción','Status');
      $nombreCampo = array('administrador_id','nombre','descripcion','nombre_status');

      $objPHPExcel->getActiveSheet()->SetCellValue('A'.$fila, 'Reporte de Administradores');
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

      /* FILAS DEL ARCHIVO EXCEL */
      if($ids!=''){
        foreach ($ids as $key => $value) {
          $Administradores = AdministradoresDao::getByIdReporte($value);
          foreach ($nombreCampo as $llave => $campo) {
            $objPHPExcel->getActiveSheet()->SetCellValue($columna[$llave].$fila, html_entity_decode($Administradores[$campo], ENT_QUOTES, "UTF-8"));
            $objPHPExcel->getActiveSheet()->getStyle($columna[$llave].$fila)->applyFromArray($estilo_celda);
            $objPHPExcel->getActiveSheet()->getStyle($columna[$llave].$fila)->getAlignment()->setWrapText($adaptarTexto);
          }
          $fila +=1;
        }
      }else{
        foreach (AdministradoresDao::getAll() as $key => $value) {
          foreach ($nombreCampo as $llave => $campo) {
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

    public function alerta($id, $parametro){
      $regreso = "/Administradores/";

      if($parametro == 'add'){
        $mensaje = "Se ha agregado correctamente";
        $class = "success";
      }

      if($parametro == 'edit'){
        $mensaje = "Se ha modificado correctamente";
        $class = "success";
      }

      if($parametro == 'nothing'){
        $mensaje = "Al parecer no intentaste actualizar ningún campo";
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

      if($parametro == "sin_departamento"){
        $mensaje = "Por favor asigna un departamento";
        $class = "warning";
        $regreso = "/Administradores/add";
      }


      View::set('class',$class);
      View::set('regreso',$regreso);
      View::set('mensaje',$mensaje);
      View::set('header',$this->_contenedor->header($extraHeader));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("alerta");
    }

    public function alertas($title, $array, $regreso){
        $mensaje = "";
        foreach ($array as $key => $value) {
            if($value['seccion'] == 1){
                $mensaje .= <<<html
                <div class="alert alert-success" role="alert">
                    <h4>El ID <b>{$value['id']}</b>, se ha eliminado. <b><a href="{$regreso}">Regresar</a></b></h4>
                </div>
html;
            }
        }
        
      View::set('regreso', $regreso);
      View::set('mensaje', $mensaje);
      View::set('titulo', $title);
      View::render("alertas");
    }
}