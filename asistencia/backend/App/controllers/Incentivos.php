<?php
namespace App\controllers;
defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \App\controllers\Contenedor;
use \Core\Controller;
use \App\models\Incentivos AS IncentivosDao;
use \App\models\General AS GeneralDao;

class Incentivos extends Controller{

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
              $('#all').attr('action', '/Incentivos/generarPDF/');
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });

            $("#export_excel").click(function(){
              $('#all').attr('action', '/Incentivos/generarExcel/');
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });

            $("#delete").click(function(){
              var seleccionados = $("input[name='borrar[]']:checked").length;
              if(seleccionados>0){
                alertify.confirm('¿Segúro que desea eliminar lo seleccionado?', function(response){
                  if(response){
                    $('#all').attr('action', '/Incentivos/delete');
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
      $editarHidden = (Controller::getPermisosUsuario($usuario, "seccion_incentivos", 5)==1)?  "" : "style=\"display:none;\"";
      $eliminarHidden = (Controller::getPermisosUsuario($usuario, "seccion_incentivos", 6)==1)? "" : "style=\"display:none;\"";
      $Incentivos = IncentivosDao::getAll();
      $tabla= '';
      foreach ($Incentivos as $key => $value) {
        $tabla.=<<<html
                <tr>
                    <td style="text-align:center; vertical-align:middle;"><input type="checkbox" name="borrar[]" value="{$value['catalogo_incentivo_id']}"/></td>
                    <td style="text-align:center; vertical-align:middle;">{$value['nombre']}</td>
                    <td style="text-align:center; vertical-align:middle;">{$value['descripcion']}</td>
                    <td style="text-align:center; vertical-align:middle;"><span style="color:{$value['color']}" class="fa fa-circle"></span></td>
                    <td style="text-align:center; vertical-align:middle;">{$value['fijo']}</td>
                    <td style="text-align:center; vertical-align:middle;">{$value['repetitivo']}</td>
                    <td style="text-align:center; vertical-align:middle;">{$value['tipo']}</td>
                    <td style="text-align:center; vertical-align:middle;">{$value['status']}</td>
                    <td style="text-align:center; vertical-align:middle;" class="center">
                        <a href="/Incentivos/edit/{$value['catalogo_incentivo_id']}" {$editarHidden} type="submit" name="id" class="btn btn-primary"><span class="fa fa-pencil-square-o" style="color:white"></span> </a>
                        <a href="/Incentivos/show/{$value['catalogo_incentivo_id']}" type="submit" name="id_incentivos" class="btn btn-success"><span class="glyphicon glyphicon-eye-open" style="color:white"></span> </a>
                    </td>
                </tr>
html;
      }
      $pdfHidden = (Controller::getPermisosUsuario($usuario, "seccion_incentivos", 2)==1)?  "" : "style=\"display:none;\"";
      $excelHidden = (Controller::getPermisosUsuario($usuario, "seccion_incentivos", 3)==1)? "" : "style=\"display:none;\"";
      $agregarHidden = (Controller::getPermisosUsuario($usuario, "seccion_incentivos", 4)==1)? "" : "style=\"display:none;\"";

      View::set('pdfHidden',$pdfHidden);
      View::set('excelHidden',$excelHidden);
      View::set('agregarHidden',$agregarHidden);
      View::set('editarHidden',$editarHidden);
      View::set('eliminarHidden',$eliminarHidden);
      View::set('tabla',$tabla);
      View::set('header',$this->_contenedor->header($extraHeader));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("incentivos_all");
    }

    public function add(){
      $extraFooter =<<<html
      <script>
        $(document).ready(function(){

          $("#fijo").bootstrapSwitch();
          $("#repetitivo").bootstrapSwitch();

          $('#color').colorpicker();
          $('#color').change(function(){
            $("#color").css("background-color",$(this).val());
          });

           $.validator.addMethod("verificarNombreIncentivo",
            function(value, element) {
              var result = false;
              $.ajax({
                type:"POST",
                async: false,
                url: "/Incentivos/validarNombreIncentivo", // script to validate in server side
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
              nombre: {
                required: true,
                verificarNombreIncentivo: true
              },
              descripcion: {
                required: true
              },
              color: {
                required: true
              },
              status: {
                required: true
              },
              tipo:{
                required: true
              }
            },
            messages:{
              nombre: {
                required: "Este campo es requerido"
              },
              descripcion: {
                required: "Este campo es requerido"
              },
              color: {
                required: "Este campo es requerido"
              },
              status: {
                required: "Este campo es requerido"
              },
              tipo:{
                required: "Este campo es requerido"
              }
            }
          });//fin del jquery validate

          $("#btnCancel").click(function(){
            window.location.href = "/Incentivos/";
          });//fin del btnAdd

        });//fin del document.ready
      </script>
html;

      $status = GeneralDao::getStatus();
      $optionStatus = '';
      foreach ($status as $key => $val2) {
        $optionStatus .= <<<html
        <option value="{$val2['catalogo_status_id']}">{$val2['nombre']}</option>
html;
      }

      $tipo = '';
      $tipo_periodo = array('ninguno' => "ninguno", 'semanal' => "semanal", 'quincenal' => "quincenal", 'mensual' => 'mensual' );
      foreach ($tipo_periodo as $key => $value) {
        $tipo .= <<<html
        <option value="{$value}">{$value}</option>
html;
      }

      View::set('status', $optionStatus);
      View::set('tipo',$tipo);
      View::set('header',$this->_contenedor->header(''));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("incentivos_add");
    }

    public function edit($id){
      $extraFooter =<<<html
      <script>
        $(document).ready(function(){

          $("#fijo").bootstrapSwitch();
          $("#repetitivo").bootstrapSwitch();

          $('#color').colorpicker();
          $('#color').change(function(){
            $("#color").css("background-color",$(this).val());
          });

          $("#add").validate({
            rules:{
              nombre: {
                required: true
              },
              descripcion: {
                required: true
              },
              color: {
                required: true
              },
              status: {
                required: true
              },
              tipo:{
                required: true
              }
            },
            messages:{
              nombre: {
                required: "Este campo es requerido"
              },
              descripcion: {
                required: "Este campo es requerido"
              },
              color: {
                required: "Este campo es requerido"
              },
              status: {
                required: "Este campo es requerido"
              },
              tipo:{
                required: "Este campo es requerido"
              }
            }
          });//fin del jquery validate

          $("#btnCancel").click(function(){
            window.location.href = "/Incentivos/";
          });//fin del btnAdd

        });//fin del document.ready
      </script>
html;
      $incentivo = IncentivosDao::getById($id);

      $checked = ($incentivo['fijo']=="si")? "checked":"";
      $status = GeneralDao::getStatus();
      $optionStatus = '';
      foreach ($status as $key => $val2) {
        $optionStatus .= <<<html
        <option value="{$val2['catalogo_status_id']}"
html;
        $optionStatus .= ($val2['catalogo_status_id'] == $incentivo['status'] ) ? "selected" : "";
        $optionStatus .= <<<html
        >{$val2['nombre']}</option>
html;
      }

      $tipo_incentivo_periodo = $incentivo['tipo'];
      $tipo = '';
      $tipo_periodo = array('ninguno' => "ninguno", 'semanal' => "semanal", 'quincenal' => "quincenal", 'mensual' => 'mensual' );
      foreach ($tipo_periodo as $key => $value) {
        $selected = ($value == $tipo_incentivo_periodo) ? "selected":"";
        $tipo .= <<<html
        <option {$selected} value="{$value}">{$value}</option>
html;
      }

      $repetido = ($incentivo['repetitivo'] == "si") ? "checked":"";
      
      View::set('repetido',$repetido);
      View::set('tipo',$tipo);
      View::set('status', $optionStatus);
      View::set('incidencia',$incentivo);
      View::set('checked',$checked);
      View::set('header',$this->_contenedor->header(''));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("incentivos_edit");
    }

    public function show($id){
      $extraFooter =<<<html
      <script>
        $(document).ready(function(){

          $("#btnCancel").click(function(){
            window.location.href = "/Incentivos/";
          });//fin del btnAdd

        });//fin del document.ready
      </script>
html;
      $incentivo = IncentivosDao::getById($id);
      View::set('incentivo',$incentivo);
      View::set('header',$this->_contenedor->header(''));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("incentivos_view");
    }

    public function delete(){
      $id = MasterDom::getDataAll('borrar');
      $array = array();
      foreach ($id as $key => $value) {
        $id = IncentivosDao::delete($value);
        if($id['seccion'] == 2){
          array_push($array, array('seccion' => 2, 'id' => $id['id'] ));
        }else if($id['seccion'] == 1){
          array_push($array, array('seccion' => 1, 'id' => $id['id'] ));
        }
      }
      $this->alertas("Eliminacion de Incentivo", $array, "/Incentivos/");
    }

    public function incentivosAdd(){
      $Incentivos = new \stdClass();
      $nombre = MasterDom::getDataAll('nombre');
      $nombre = MasterDom::procesoAcentosNormal($nombre);
      $Incentivos->_nombre = $nombre;
      $descripcion = MasterDom::getDataAll('descripcion');
      $descripcion = MasterDom::procesoAcentosNormal($descripcion);
      $repetido = (MasterDom::getData('repetitivo') == "on") ? "si" : "no";
      $Incentivos->_repetitivo = $repetido;
      $Incentivos->_tipo = MasterDom::getData('tipo');
      $Incentivos->_descripcion = $descripcion;
      $Incentivos->_color = MasterDom::getData('color');
      $Incentivos->_fijo = (!empty(MasterDom::getData('fijo'))) ? "si" : "no";
      $Incentivos->_status = MasterDom::getData('status');
      $id = IncentivosDao::insert($Incentivos);

      if($id >= 1)
        $this->alerta($id,'add');
      else
        $this->alerta($id,'error');
    }

    public function incentivoEdit(){
        $Incentivos = new \stdClass();
        $id = IncentivosDao::verificarRelacion(MasterDom::getData('catalogo_incentivo_id'));
        $Incentivos->_catalogo_incentivo_id = MasterDom::getData('catalogo_incentivo_id');
        $nombre = MasterDom::getDataAll('nombre');
        $nombre = MasterDom::procesoAcentosNormal($nombre);
        $Incentivos->_nombre = $nombre;
        $repetido = (MasterDom::getData('repetitivo') == "on") ? "si" : "no";
        $Incentivos->_repetitivo = $repetido;
        $descripcion = MasterDom::getDataAll('descripcion');
        $descripcion = MasterDom::procesoAcentosNormal($descripcion);
        $Incentivos->_tipo = MasterDom::getData('tipo');
        $Incentivos->_fijo = (!empty(MasterDom::getData('fijo'))) ? "si" : "no";
        $Incentivos->_descripcion = $descripcion;
        $Incentivos->_color = MasterDom::getData('color');
        $Incentivos->_status = MasterDom::getData('status');



        $array = array();
        if($id['seccion'] == 2){
          array_push($array, array('seccion' => 2, 'id' => $id['id'] ));
          $idStatus = (MasterDom::getData('status')!=2) ? true : false;
          if($idStatus){
            if(IncentivosDao::update($Incentivos) > 0)
              $this->alerta($id,'edit');
            else
              $this->alerta($id,'nothing');
          }else{
            $this->alertas("Eliminación de incentivo", $array, "/Incentivos/");
          }
        }

        if($id['seccion'] == 1){
          array_push($array, array('seccion' => 1, 'id' => $id['id'] ));
          if(MasterDom::getData('status') == 2){
            IncentivosDao::update($Incentivos);
            $this->alerta(MasterDom::getData('catalogo_incentivo_id'),'delete');
          }else{
            if(IncentivosDao::update($Incentivos) >= 1){
             $this->alerta($id,'edit');
            }else{
              $this->alerta("",'nothing');
            }
          }

        }

    }

    public function validarNombreIncentivo(){
      $dato = IncentivosDao::getNombreIncentivo($_POST['nombre']);
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
<H1 class="titulo">Incentivos</H1>
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
          $incentivo = IncentivosDao::getByIdReporte($value);
            $tabla.=<<<html
              <tr style="background-color:#B8B8B8;">
              <td style="height:auto; width: 200px;background-color:{$incentivo['color']};">{$incentivo['catalogo_incentivo_id']}</td>
              <td style="height:auto; width: 200px;background-color:{$incentivo['color']};">{$incentivo['nombre']}</td>
              <td style="height:auto; width: 200px;background-color:{$incentivo['color']};">{$incentivo['descripcion']}</td>
              <td style="height:auto; width: 200px;background-color:{$incentivo['color']};">{$incentivo['status']}</td>
              </tr>
html;
        }
      }else{
        foreach (IncentivosDao::getAll() as $key => $incentivo) {
          $tabla.=<<<html
          <tr style="background-color:#B8B8B8;">
          <td style="height:auto; width: 200px;background-color:{$incentivo['color']};">{$incentivo['catalogo_incentivo_id']}</td>
          <td style="height:auto; width: 200px;background-color:{$incentivo['color']};">{$incentivo['nombre']}</td>
          <td style="height:auto; width: 200px;background-color:{$incentivo['color']};">{$incentivo['descripcion']}</td>
          <td style="height:auto; width: 200px;background-color:{$incentivo['color']};">{$incentivo['status']}</td>
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
      //echo shell_exec('php -f /home/granja/backend/public/librerias/mpdf_apis/Api.php Empresa '.json_encode(MasterDom::getDataAll('borrar')));
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

      $controlador = "Incentivo";
      $columna = array('A','B','C','D','E','F','G');
      $nombreColumna = array('Id','Nombre','Descripción','Status');
      $nombreCampo = array('catalogo_incentivo_id','nombre','descripcion','status');

      $objPHPExcel->getActiveSheet()->SetCellValue('A'.$fila, 'Reporte de Incentivos');
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
          $empresa = IncentivosDao::getByIdReporte($value);
          foreach ($nombreCampo as $key => $campo) {
            $objPHPExcel->getActiveSheet()->SetCellValue($columna[$key].$fila, html_entity_decode($empresa[$campo], ENT_QUOTES, "UTF-8"));
            $objPHPExcel->getActiveSheet()->getStyle($columna[$key].$fila)->applyFromArray($estilo_celda);
            $objPHPExcel->getActiveSheet()->getStyle($columna[$key].$fila)->getAlignment()->setWrapText($adaptarTexto);
          }
          $fila +=1;
        }
      }else{
        foreach (IncentivosDao::getAll() as $key => $value) {
          foreach ($nombreCampo as $key => $campo) {
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
    }

    public function alerta($id, $parametro){
      $regreso = "/Incentivos/";

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

      if($parametro == 'delete'){
        $mensaje = "Se ha eliminado el incentivo con id {$id}, ya que cambiaste el estatus a eliminado";
        $class = "success";
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
