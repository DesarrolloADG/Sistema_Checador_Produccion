<?php
namespace App\controllers;
defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \App\controllers\Contenedor;
use \Core\Controller;
use \App\models\Horarios AS HorariosDao;

class Horarios extends Controller{

    private $_contenedor;

    function __construct(){
      parent::__construct();
      $this->_contenedor = new Contenedor;
      View::set('header',$this->_contenedor->header());
      View::set('footer',$this->_contenedor->footer());
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
              $('#all').attr('action', '/Horarios/generarPDF/');
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });

            $("#export_excel").click(function(){
              $('#all').attr('action', '/Horarios/generarExcel/');
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });

            $("#delete").click(function(){
              var seleccionados = $("input[name='borrar[]']:checked").length;
              if(seleccionados>0){
                alertify.confirm('¿Segúro que desea eliminar lo seleccionado?', function(response){
                  if(response){
                    $('#all').attr('action', '/Horarios/delete');
                    $('#all').attr('target', '');
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
      $usuario = $this->__usuario;
      $eliminarHidden = (Controller::getPermisosUsuario($usuario, "seccion_horarios", 6)==1)?"":"style=\"display:none;\"";
      $editarHidden = (Controller::getPermisosUsuario($usuario, "seccion_horarios", 5)==1)?"":"style=\"display:none;\"";
      $horarios = HorariosDao::getAll();
      $tabla= '';
      foreach ($horarios as $key => $value) {
        $tabla.=<<<html
                <tr>
                    <td><input type="checkbox" name="borrar[]" value="{$value['catalogo_horario_id']}"/></td>
                    <td>{$value['nombre']}</td>
                    <td>{$value['hora_entrada']}</td>
                    <td>{$value['hora_salida']}</td>
                    <td>{$value['tolerancia_entrada']}</td>
                    <td>
html;
      foreach (HorariosDao::getDiasLaboralesHorarioById($value['catalogo_horario_id']) as $llave => $valor) {
        $tabla .=<<<html
        <span class="badge incentivo">{$valor['nombre']}</span>
html;
      }

      $tabla .=<<<html
                    </td>
                    <td>{$value['numero_retardos']}</td>
                    <td>{$value['status']}</td>
                    <td class="center">
                        <a href="/Horarios/edit/{$value['catalogo_horario_id']}" {$editarHidden} type="submit" name="id" class="btn btn-primary"><span class="fa fa-pencil-square-o" style="color:white"></span> </a>
                        <a href="/Horarios/show/{$value['catalogo_horario_id']}" type="submit" name="id_empresa" class="btn btn-success"><span class="glyphicon glyphicon-eye-open" style="color:white"></span> </a>
                    </td>
                </tr>
html;
      }


      $pdfHidden = (Controller::getPermisosUsuario($usuario, "seccion_horarios", 2)==1)?"":"style=\"display:none;\"";
      $excelHidden = (Controller::getPermisosUsuario($usuario, "seccion_horarios", 3)==1)?"":"style=\"display:none;\"";
      $agregarHidden = (Controller::getPermisosUsuario($usuario, "seccion_horarios", 4)==1)?"":"style=\"display:none;\"";
      View::set('pdfHidden',$pdfHidden);
      View::set('excelHidden',$excelHidden);
      View::set('agregarHidden',$agregarHidden);
      View::set('editarHidden',$editarHidden);
      View::set('eliminarHidden',$eliminarHidden);
      View::set('tabla',$tabla);
      View::set('header',$this->_contenedor->header($extraHeader));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("horario_all");
    }

    public function add(){
      $extraHeader =<<<html
      <style>
      .incentivo{
        margin: 2px;
        font: message-box;
      }

      </style>
html;
      $extraFooter =<<<html
      <script>
        $(document).ready(function(){
          $.validator.addMethod("verificarNombreHorario",
            function(value, element) {
              var result = false;
              $.ajax({
                type:"POST",
                async: false,
                url: "/Horarios/validarNombreHorario", // script to validate in server side
                data: {
                    nombre: function() {
                      return $("#nombre").val();
                    }},
                success: function(data) {
                    console.log("success::: " + data);
                    result = (data == "true") ? false : true;

                    if(result == true){
                      $('#availability').html('<span class="text-success glyphicon glyphicon-ok"></span><span> Nombre disponible</span>');
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
              "<li>¡Este nombre ya está en uso. Intenta con otro!</li><li> Si no es visible en la tabla inicial, contacta a soporte técnico</li>"
          );
          $("#add").validate({
            rules:{
              nombre:{
                required: true,
                verificarNombreHorario: true
              },
              hora_entrada:{
                required: true
              },
              hora_salida:{
                required: true
              },
              tolerancia_entrada:{
                required: true
              },
              dias_laborales:{
                required: true
              },
              numero_retardos:{
                required: true
              },
              status:{
                required: true
              }
            },
            messages:{
              nombre:{
                required: "Este campo es requerido"
              },
              hora_entrada:{
                required: "Este campo es requerido"
              },
              hora_salida:{
                required: "Este campo es requerido"
              },
              tolerancia_entrada:{
                required: "Este campo es requerido"
              },
              dias_laborales:{
                required: "Este campo es requerido"
              },
              numero_retardos:{
                required: "Este campo es requerido"
              },
              status:{
                required: "Este campo es requerido"
              }
            }
          });//fin del jquery validate

          $("#btnCancel").click(function(){
            window.location.href = "/Horarios/";
          });//fin del btnAdd

        });//fin del document.ready
      </script>
html;

      $hora = '';
      for ($i=0; $i <=23 ; $i++) {
        if($i<10){
          $hora .= '<option value="0'.$i.':00">0'.$i.':00 Hrs</option>';
          $hora .= '<option value="0'.$i.':30">0'.$i.':30 Hrs</option>';
        }else{
          $hora .= '<option value="'.$i.':00">'.$i.':00 Hrs</option>';
          $hora .= '<option value="'.$i.':30  ">'.$i.':30 Hrs</option>';
        }
      }

      $tolerancia = '';
      for ($i=0; $i <=20 ; $i+=5) {
        if($i<10){
          $tolerancia .= '<option value="'.$i.'">0'.$i.':00 Minutos</option>';
        }else{
          $tolerancia .= '<option value="'.$i.'">'.$i.':00 Minutos</option>';
        }
      }

      $numero_retardos = '';
      for ($i=0; $i <=10 ; $i++) {
        if($i==1){
          $numero_retardos .= '<option value="'.$i.'">'.$i.' Retardo</option>';
        }else{
          $numero_retardos .= '<option value="'.$i.'">'.$i.' Retardos</option>';
        }
      }
      $sStatus = "";
      foreach (HorariosDao::getStatus() as $key => $value) {
        $sStatus .=<<<html
        <option value="{$value['catalogo_status_id']}">{$value['nombre']}</option>
html;
      }

      $dias_laborales = '';
      foreach (HorariosDao::getDiasLaborales() as $key => $value) {
        $dias_laborales .=<<<html
        <span class="badge incentivo"><input type="checkbox" class="check" id="dias_laborales[]" name="dias_laborales[]" value="{$value['dias_laborales_id']}" >  {$value['nombre']}</span>
html;
      }

      View::set('sStatus',$sStatus);
      View::set('hora',$hora);
      View::set('tolerancia',$tolerancia);
      View::set('dias_laborales',$dias_laborales);
      View::set('numero_retardos',$numero_retardos);
      View::set('header',$this->_contenedor->header($extraHeader));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("horario_add");
    }

    public function edit($id){
      $extraFooter =<<<html
      <script>
        $(document).ready(function(){

          $("#edit").validate({
            rules:{
              nombre:{
                required: true
              },
              hora_entrada:{
                required: true
              },
              hora_salida:{
                required: true
              },
              tolerancia_entrada:{
                required: true
              },
              dias_laborales:{
                required: true
              },
              numero_retardos:{
                required: true
              },
              status:{
                required: true
              }
            },
            messages:{
              nombre:{
                required: "Este campo es requerido"
              },
              hora_entrada:{
                required: "Este campo es requerido"
              },
              hora_salida:{
                required: "Este campo es requerido"
              },
              tolerancia_entrada:{
                required: "Este campo es requerido"
              },
              dias_laborales:{
                required: "Este campo es requerido"
              },
              numero_retardos:{
                required: "Este campo es requerido"
              },
              status:{
                required: "Este campo es requerido"
              }
            }
          });//fin del jquery validate

          $("#btnCancel").click(function(){
            window.location.href = "/Horarios/";
          });//fin del btnAdd

        });//fin del document.ready
      </script>
html;
      $horario = HorariosDao::getById($id);
      $hora_entrada = '';
      for ($i=0; $i <=23 ; $i++) {
        if($i<10){
          $select1 = ($horario['hora_entrada']=='0'.$i.':00')? 'selected':'';
          $select2 = ($horario['hora_entrada']=='0'.$i.':30')? 'selected':'';
          $hora_entrada .= '<option '.$select1.' value="0'.$i.':00">0'.$i.':00 Hrs</option>';
          $hora_entrada .= '<option '.$select2.' value="0'.$i.':30">0'.$i.':30 Hrs</option>';
        }else{
          $select1 = ($horario['hora_entrada']==''.$i.':00')? 'selected':'';
          $select2 = ($horario['hora_entrada']==''.$i.':30')? 'selected':'';
          $hora_entrada .= '<option '.$select1.' value="'.$i.':00">'.$i.':00 Hrs</option>';
          $hora_entrada .= '<option '.$select2.' value="'.$i.':30  ">'.$i.':30 Hrs</option>';
        }
      }

      $hora_salida = '';
      for ($i=0; $i <=23 ; $i++) {
        if($i<10){
          $select1 = ($horario['hora_salida']=='0'.$i.':00')? 'selected':'';
          $select2 = ($horario['hora_salida']=='0'.$i.':30')? 'selected':'';
          $hora_salida .= '<option '.$select1.' value="0'.$i.':00">0'.$i.':00 Hrs</option>';
          $hora_salida .= '<option '.$select2.' value="0'.$i.':30">0'.$i.':30 Hrs</option>';
        }else{
          $select1 = ($horario['hora_salida']==''.$i.':00')? 'selected':'';
          $select2 = ($horario['hora_salida']==''.$i.':30')? 'selected':'';
          $hora_salida .= '<option '.$select1.' value="'.$i.':00">'.$i.':00 Hrs</option>';
          $hora_salida .= '<option '.$select2.' value="'.$i.':30  ">'.$i.':30 Hrs</option>';
        }
      }

      $sDiasLaborales = '';
      foreach (HorariosDao::getDiasLaborales() as $key => $value) {
        $checked = '';
        foreach (HorariosDao::getDiasLaboralesById($horario['catalogo_horario_id']) as $llave => $valor) {
          if($valor['dias_laborales_id'] == $value['dias_laborales_id']){
            $checked = 'checked';
            break;
          }
        }
        $sDiasLaborales .=<<<html
        <span class="badge incentivo"><input type="checkbox" class="check" id="dias_laborales[]" name="dias_laborales[]" value="{$value['dias_laborales_id']}" {$checked}/>  {$value['nombre']}</span>
html;
      }

      $sStatus = "";
      foreach (HorariosDao::getStatus() as $key => $value) {
        $selected = ($horario['catalogo_status_id']==$value['catalogo_status_id'])? 'selected' : '';
        $sStatus .=<<<html
        <option {$selected} value="{$value['catalogo_status_id']}">{$value['nombre']}</option>
html;
      }


      View::set('sStatus',$sStatus);
      View::set('hora_entrada',$hora_entrada);
      View::set('hora_salida',$hora_salida);
      View::set('tolerancia',$tolerancia);
      View::set('sDiasLaborales',$sDiasLaborales);
      View::set('horario',$horario);
      View::set('header',$this->_contenedor->header(''));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("horario_edit");
    }

    public function show($id){
      $extraFooter =<<<html
      <script>
        $(document).ready(function(){

          $("#btnCancel").click(function(){
            window.location.href = "/Horarios/";
          });//fin del btnAdd

        });//fin del document.ready
      </script>
html;
      $horario = HorariosDao::getById($id);
      $sDiasLaborales = '';
      foreach (HorariosDao::getDiasLaborales() as $key => $value) {
        $checked = '';
        foreach (HorariosDao::getDiasLaboralesById($horario['catalogo_horario_id']) as $llave => $valor) {
          if($valor['dias_laborales_id'] == $value['dias_laborales_id']){
            $checked = 'checked';
            break;
          }
        }
        $sDiasLaborales .=<<<html
        <span><input disabled readonly type="checkbox" class="check" id="dias_laborales[]" name="dias_laborales[]" value="{$value['dias_laborales_id']}" {$checked}/>  {$value['nombre']}</span> <br>
html;
      }

      View::set('diasLaborales',$sDiasLaborales);
      View::set('horario',$horario);
      View::set('header',$this->_contenedor->header(''));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("horario_view");
    }

    public function delete(){
      $id = MasterDom::getDataAll('borrar');
      $array = array();
      foreach ($id as $key => $value) {
        $id = HorariosDao::delete($value);
        if($id['seccion'] == 2){
          array_push($array, array('seccion' => 2, 'id' => $id['id'] ));
        }else if($id['seccion'] == 1){
          array_push($array, array('seccion' => 1, 'id' => $id['id'] ));
        }
      }
      $this->alertas("Eliminacion de Horario", $array, "/Horarios/");
    }

    public function horarioAdd(){
      $horario = new \stdClass();
      $nombre = MasterDom::getDataAll('nombre');
      $nombre = MasterDom::procesoAcentosNormal($nombre);
      $horario->_nombre = $nombre;
      $horario->_hora_entrada = MasterDom::getData('hora_entrada');
      $horario->_hora_salida = MasterDom::getData('hora_salida');
      $horario->_tolerancia_entrada = MasterDom::getData('tolerancia_entrada');
      $horario->_numero_retardos = MasterDom::getData('numero_retardos');
      $horario->_status = MasterDom::getData('status');
      $id = HorariosDao::insert($horario);
      foreach (MasterDom::getDataAll('dias_laborales') as $key => $value) {
        $dias_laborales = new \stdClass();
        $dias_laborales->_catalogo_horario_id = $id;
        $dias_laborales->_dias_laborales_id = $value;
        HorariosDao::insertDiasLaborales($dias_laborales);
      }
      if($id >= 0)
        $this->alerta($id,'add');
      else
        $this->alerta($id,'error');
    }

    public function horarioEdit(){
      $horario = new \stdClass();
      $horario_relacion = HorariosDao::verificarRelacion(MasterDom::getData('catalogo_horario_id'));
      $horario->_catalogo_horario_id = MasterDom::getData('catalogo_horario_id');
      $nombre = MasterDom::getDataAll('nombre');
      $nombre = MasterDom::procesoAcentosNormal($nombre);
      $horario->_nombre = $nombre;
      $horario->_hora_entrada = MasterDom::getData('hora_entrada');
      $horario->_hora_salida = MasterDom::getData('hora_salida');
      $horario->_tolerancia_entrada = MasterDom::getData('tolerancia_entrada');
      $horario->_dias_laborales = MasterDom::getData('dias_laborales');
      $horario->_numero_retardos = MasterDom::getData('numero_retardos');
      $horario->_status = MasterDom::getData('status');
      $dias_horario_anterior = HorariosDao::getDiasLaboralesHorarioById(MasterDom::getData('catalogo_horario_id'));
      $dias_horario_nuevo = MasterDom::getDataAll('dias_laborales');

      $response = array();
      array_push($response,MasterDom::getData('catalogo_horario_id'));
        if(MasterDom::getData('status')!=2 || count($horario_relacion) == 0){
        $id_modificacion = HorariosDao::update($horario);
        array_push($response,$id_modificacion);
      }elseif (count($horario_relacion) > 0) {
        $this->alertas("Eliminacion de horario", $response, "/Horarios/");
      }

      $modificable = true;
      if(count($dias_horario_anterior) == count($dias_horario_nuevo)){
        foreach ($dias_horario_anterior as $key => $value) {
          $modificable = ($value['dias_laborales_id'] == $dias_horario_nuevo[$key])? false: true;
          if($modificable){
            break;
          }
        }
      }

      $response_dias = array();
      if($modificable){
        $idHorario = HorariosDao::deleteDiasLaborales(MasterDom::getData('catalogo_horario_id'));
        foreach (MasterDom::getDataAll('dias_laborales') as $key => $value) {
          $dias_laborales = new \stdClass();
          $dias_laborales->_catalogo_horario_id = MasterDom::getData('catalogo_horario_id');
          $dias_laborales->_dias_laborales_id = $value;
          array_push($response_dias, HorariosDao::insertDiasLaborales($dias_laborales));
        }

        array_push($response,$response_dias);
      }

      Horarios::alertaEdit($response,"edit");

    }

    public function validarNombreHOrario(){
      $dato = HorariosDao::getNombreHorario($_POST['nombre']);
      if($dato == 1){
        echo "true";
      }else{
        echo "false";
      }
    }

    public function generarPDF(){
      $ids = MasterDom::getDataAll('borrar');
      $mpdf=new \mPDF('c');
      $mpdf->defaultPageNumStyle = 'I';
      $mpdf->h2toc = array('H5'=>0,'H6'=>1);
      $style =<<<html
      <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css" />
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
          margin-top:30px;
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

      </style>
html;

      $tabla =<<<html
      <link href="/css/styles_pdf.css" rel="stylesheet"/>
      <img class="imagen" src="/img/ag_logo.png"/>
      <br>
      <div style="page-break-inside: avoid;" align='center'>
      <H1 class="titulo">Horarios</H1>
      <table border="0" style="width:100%;text-align: center">
          <tr style="background-color:#B8B8B8;">
            <th><strong>Id</strong></th>
            <th><strong>Nombre</strong></th>
            <th><strong>Hora <br>Entrada</strong></th>
            <th><strong>Hora <br> Salida</strong></th>
            <th><strong>Tolerancia</strong></th>
            <th><strong>Dias <br> labolares</strong></th>
            <th><strong>Total de <br> Retardos</strong></th>
            <th><strong>Status</strong></th>
          </tr>
html;
      if($ids!=''){
        foreach ($ids as $key => $value) {
          $horario = HorariosDao::getByIdReporte($value);
            $tabla.=<<<html
              <tr class="tr">
                <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$horario['catalogo_horario_id']}</td>
                <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$horario['nombre']}</td>
                <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$horario['hora_entrada']}</td>
                <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$horario['hora_salida']}</td>
                <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$horario['tolerancia_entrada']} min</td>
                <td style="height:auto; width: 200px;background-color:#E4E4E4;">
html;
        foreach (HorariosDao::getDiasLaboralesHorarioById($horario['catalogo_horario_id']) as $llave => $valor) {
            $tabla .=<<<html
                <span class="badge incentivo">{$valor['nombre']}</span>
html;
        }
            $tabla .=<<<html
                </td>
                <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$horario['numero_retardos']}</td>
                <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$horario['status']}</td>
              </tr>
html;
        }
      }else{
        foreach (HorariosDao::getAll() as $key => $horario) {
          $tabla.=<<<html
            <tr style="background-color:#B8B8B8;">
              <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$horario['catalogo_horario_id']}</td>
              <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$horario['nombre']}</td>
              <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$horario['hora_entrada']}</td>
              <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$horario['hora_salida']}</td>
              <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$horario['tolerancia_entrada']} min</td>
              <td style="height:auto; width: 200px;background-color:#E4E4E4;">
html;
        foreach (HorariosDao::getDiasLaboralesHorarioById($horario['catalogo_horario_id']) as $llave => $valor) {
            $tabla .=<<<html
              <span class="badge incentivo">{$valor['nombre']}</span>
html;
        }
            $tabla .=<<<html
              </td>
              <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$horario['numero_retardos']} </td>
              <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$horario['status']} </td>
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

      //$ids = MasterDom::getDataAll('borrar');
      //echo shell_exec('php -f /home/granja/backend/public/librerias/mpdf_apis/Api.php Horario '.json_encode(MasterDom::getDataAll('borrar')));
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

      $controlador = "Horario";
      $columna = array('A','B','C','D','E','F','G','H');
      $nombreColumna = array('Id','Nombre','Entrada','Salida','Tolerancia','Dias Laborales','Retardos','Status');
      $nombreCampo = array('catalogo_horario_id','nombre','hora_entrada','hora_salida','tolerancia_entrada','dias_laborales','numero_retardos','status');

      $objPHPExcel->getActiveSheet()->SetCellValue('A'.$fila, 'Reporte de Horarios');
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

          $horario = HorariosDao::getByIdReporte($value);
          foreach ($nombreCampo as $llave => $campo) {

            if($campo=='dias_laborales'){
              $horario[$campo] = '';
              foreach (HorariosDao::getDiasLaboralesHorarioById($value['catalogo_horario_id']) as $llave1 => $valor) {
                  $horario[$campo].= $valor['nombre'].',';
              }
            }

            $objPHPExcel->getActiveSheet()->SetCellValue($columna[$llave].$fila, html_entity_decode($horario[$campo], ENT_QUOTES, "UTF-8"));
            $objPHPExcel->getActiveSheet()->getStyle($columna[$llave].$fila)->applyFromArray($estilo_celda);
            $objPHPExcel->getActiveSheet()->getStyle($columna[$llave].$fila)->getAlignment()->setWrapText($adaptarTexto);
          }
          $fila +=1;

        }
      }else{
        foreach (HorariosDao::getAll() as $key => $value) {
          foreach ($nombreCampo as $key => $campo) {

            if($campo=='dias_laborales'){
              $value[$campo] = '';
              foreach (HorariosDao::getDiasLaboralesHorarioById($value['catalogo_horario_id']) as $llave => $valor) {
                  $value[$campo].= $valor['nombre'].',';
              }
            }

            $objPHPExcel->getActiveSheet()->SetCellValue($columna[$key].$fila, html_entity_decode($value[$campo], ENT_QUOTES, "UTF-8"));
            $objPHPExcel->getActiveSheet()->getStyle($columna[$key].$fila)->applyFromArray($estilo_celda);
            $objPHPExcel->getActiveSheet()->getStyle($columna[$key].$fila)->getAlignment()->setWrapText($adaptarTexto);
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
      exit;
    }

    public function alerta($id, $parametro){
      $regreso = "/Horarios/";

      if($parametro == 'add'){
        $mensaje = "Se ha agregado correctamente";
        $class = "success";
      }

      if($parametro == 'edit'){
        $mensaje = "Se ha modificado correctamente";
        $class = "success";
      }

      if($parametro == 'nothing'){
        $mensaje = "Posibles errores: <li>No intentaste actualizar ningún campo</li> <li>Este dato ya esta registrado, comunicate con soporte técnico</li> ";
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

    public static function alertaEdit($reporte=array(4,0,array(0,0,0,0)), $parametro='edit'){
      $mensaje = '';
      if($parametro=='edit'){
        $accion = "modificado";
      }

      if($reporte[1] > 0){
        $mensaje .=<<<html
         <div class="alert alert-success">
          <strong>Success!</strong> Se ha $accion correctamente el horario con el Id $reporte[0]
        </div>
html;
      }else{
        $mensaje .=<<<html
         <div class="alert alert-warning">
          <strong>Warning!</strong> No se modifico ningun dato principal del horario con Id $reporte[0]
        </div>
html;
      }

      if(count($reporte[2])>0){
        foreach ($reporte[2] as $key => $value) {
          if($value >= 0){
            $mensaje .=<<<html
            <div class="alert alert-success">
              <strong>Success!</strong> Se ha agregado correctamente el dia laboral al horario con el Id $reporte[0]
            </div>
html;
          }else{
            $mensaje .=<<<html
            <div class="alert alert-warning">
              <strong>Warning!</strong> No se agrego correctamente el dia laboral al horario con el Id $reporte[0]
            </div>
html;
          }
        }
      }


      View::set('regreso',"/Horarios/");
      View::set('titulo',"Modificacion de Horario");
      View::set('mensaje',$mensaje);
      View::render("alertas");
    }

    public function alertas($title, $array, $regreso){
      $mensaje = "";
      foreach ($array as $key => $value) {
        if($value['seccion'] == 2){
          $mensaje .= <<<html
            <div class="alert alert-danger" role="alert">
              <h4>El ID <b>{$value['id']}</b>, no se puede eliminar, ya que esta siendo utilizado por el Catálogo de Gestión Colaboradores</h4>
            </div>
html;
        }

        if($value['seccion'] == 1){
          $mensaje .= <<<html
            <div class="alert alert-success" role="alert">
              <h4>El ID <b>{$value['id']}</b>, se ha eliminado</h4>
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
