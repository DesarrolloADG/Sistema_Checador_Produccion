<?php
namespace App\controllers;
defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \App\controllers\Contenedor;
use \Core\Controller;
use \App\models\Incidencias AS IncidenciasDao;
use \App\models\General AS GeneralDao;

class Incidencias extends Controller{

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


            $("#export_pdf").click(function(){
              $('#all').attr('action', '/Incidencias/generarPDF/');
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });

            $("#export_excel").click(function(){
              $('#all').attr('action', '/Incidencias/generarExcel/');
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });

            $("#delete").click(function(){
              var seleccionados = $("input[name='borrar[]']:checked").length;
              if(seleccionados>0){
                alertify.confirm('¿Segúro que desea eliminar lo seleccionado?', function(response){
                  if(response){
                    $('#all').attr('action', '/Incidencias/delete');
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
      $editarHidden = (Controller::getPermisosUsuario($usuario, "seccion_incidencias", 5)==1)?  "" : "style=\"display:none;\"";
      $eliminarHidden = (Controller::getPermisosUsuario($usuario, "seccion_incidencias", 6)==1)? "" : "style=\"display:none;\"";
      $incidencias = IncidenciasDao::getAll();
      $tabla= '';
      foreach ($incidencias as $key => $value) {
        $tabla.=<<<html
                <tr>
                    <td><input type="checkbox" name="borrar[]" value="{$value['catalogo_incidencia_id']}"/></td>
                    <th>{$value['identificador_incidencia']}</th>
                    <th>{$value['nombre']}</th>
                    <th>{$value['descripcion']}</th>
                    <th><span style="color:{$value['color']}" class="fa fa-circle"></span></th>
                    <th>{$value['status']}</th>
                    <td class="center">
                        <a href="/Incidencias/edit/{$value['catalogo_incidencia_id']}" {$editarHidden} type="submit" name="id" class="btn btn-primary"><span class="fa fa-pencil-square-o" style="color:white"></span> </a>
                        <a href="/Incidencias/show/{$value['catalogo_incidencia_id']}" type="submit" name="id_empresa" class="btn btn-success"><span class="glyphicon glyphicon-eye-open" style="color:white"></span> </a>
                    </td>
                </tr>
html;
      }
      $pdfHidden = (Controller::getPermisosUsuario($usuario, "seccion_incidencias", 2)==1)?  "" : "style=\"display:none;\"";
      $excelHidden = (Controller::getPermisosUsuario($usuario, "seccion_incidencias", 3)==1)? "" : "style=\"display:none;\"";
      $agregarHidden = (Controller::getPermisosUsuario($usuario, "seccion_incidencias", 4)==1)? "" : "style=\"display:none;\"";

      View::set('pdfHidden',$pdfHidden);
      View::set('excelHidden',$excelHidden);
      View::set('agregarHidden',$agregarHidden);
      View::set('editarHidden',$editarHidden);
      View::set('eliminarHidden',$eliminarHidden);
      View::set('tabla',$tabla);
      View::set('header',$this->_contenedor->header($extraHeader));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("incidencias_all");
    }

    public function add(){
      $extraFooter =<<<html
        <script src="/js/moment.min.js"></script>
        <script>
        $(document).ready(function(){
           $('#color').colorpicker();
           $('#color').change(function(){
             $("#color").css("background-color",$(this).val());
           });

          $.validator.addMethod("verificarIdentificadorIncidencia",
            function(value, element) {
              var result = false;
              $.ajax({
                type:"POST",
                async: false,
                url: "/Incidencias/validoIdentificadorIncidencia",
                data: {
                    identificador_incidencia: function() {
                      return $("#identificador_incidencia").val();
                    }},
                success: function(data) {
                    console.log("success::: " + data);
                    result = (data == "true") ? false : true;

                    if(result == true){
                      $('#availability1').html('<span class="text-success glyphicon glyphicon-ok"></span><span> Identificador disponible</span>');
                      $('#register').attr("disabled", true);
                    }else{
                      $('#availability1').html('<span class="text-danger glyphicon glyphicon-remove"></span>');
                      $('#register').attr("disabled", false);
                    }
                }
              });
              // return true if username is exist in database
              return result;
              },
              "<li>¡Este nombre ya está en uso. Intenta con otro!</li><li> Si no es visible en la tabla inicial, contacta a soporte técnico</li>"
          );

          $.validator.addMethod("verificarNombreIncidencia",
            function(value, element) {
              var result = false;
              $.ajax({
                type:"POST",
                async: false,
                url: "/Incidencias/validoNombreIncidencia",
                data: {
                    nombre: function() {
                      return $("#nombre").val();
                    }},
                success: function(data) {
                    console.log("success::: " + data);
                    result = (data == "true") ? false : true;

                    if(result == true){
                      $('#availability2').html('<span class="text-success glyphicon glyphicon-ok"></span><span> Nombre disponible</span>');
                      $('#register').attr("disabled", true);
                    }else{
                      $('#availability2').html('<span class="text-danger glyphicon glyphicon-remove"></span>');
                      $('#register').attr("disabled", false);
                    }
                }
              });
              // return true if username is exist in database
              return result;
              },
              "¡Este nombre del incidencia ya está en uso! Prueba otro."
          );


          $("#add").validate({
            rules:{
              identificador_incidencia:{
                required: true,
                verificarIdentificadorIncidencia: true
              },
              nombre: {
                required: true,
                verificarNombreIncidencia: true
              },
              descripcion: {
                required: true
              },
              color:{
                required: true
              },
              status:{
                required: true
              }

            },
            messages:{
              identificador_incidencia:{
                required: "Este campo es requerido"
              },
              nombre: {
                required: "Este campo es requerido"
              },
              descripcion: {
                required: "Este campo es requerido"
              },
              color:{
                required: "Este campo es requerido"
              },
              status: {
                required: "Este campo es requerido"
              },
              status: {
                required: "Este campo es requerido"
              }
            }
          });//fin del jquery validate

          $("#btnCancel").click(function(){
            window.location.href = "/Incidencias/";
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

      View::set('status', $optionStatus);
      View::set('header',$this->_contenedor->header(''));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("incidencias_add");
    }

    public function edit($id){
      $extraFooter =<<<html
      <script>
        $(document).ready(function(){

          $('#color').colorpicker();
          $('#color').change(function(){
            $("#color").css("background-color",$(this).val());
          });

          $("#add").validate({
            rules:{
              identificador_incidencia:{
                required: true
              },
              nombre: {
                required: true
              },
              descripcion: {
                required: true
              },
              color:{
                required: true
              },
              status: {required: true}

            },
            messages:{
              identificador_incidencia:{
                required: "Este campo es requerido"
              },
              nombre: {
                required: "Este campo es requerido"
              },
              color: {
                required: "Este campo es requerido"
              },
              descripcion: {
                required: "Este campo es requerido"
              },
              status: {required: "Este campo es requerido"}
            }
          });//fin del jquery validate

          $("#btnCancel").click(function(){
            window.location.href = "/Incidencias/";
          });//fin del btnAdd

        });//fin del document.ready
      </script>
html;
      $incidencia = IncidenciasDao::getById($id);
      $status = GeneralDao::getStatus();
      $optionStatus = '';
      foreach ($status as $key => $val2) {
        $optionStatus .= <<<html
        <option value="{$val2['catalogo_status_id']}"
html;
        $optionStatus .= ($val2['catalogo_status_id'] == $incidencia['status'] ) ? "selected" : "";
        $optionStatus .= <<<html
        >{$val2['nombre']}</option>
html;
      }

      $generaFalta = '';
      $arrayFalta = array('No', 'Si');
      foreach ($arrayFalta as $key => $value) {
	$selected = ($incidencia['genera_falta'] == $key) ? 'selected="selected"' : '';
        $generaFalta .= <<<html
        <option value="$key" $selected >$value</option>
html;
      }

      View::set('status', $optionStatus);
      View::set('incidencia',$incidencia);
      View::set('generaFalta', $generaFalta);
      View::set('header',$this->_contenedor->header(''));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("incidencias_edit");
    }

    public function show($id){
      $extraFooter =<<<html
      <script>
        $(document).ready(function(){

          $('#color').colorpicker();
          $('#color').change(function(){
            $("#color").css("background-color",$(this).val());
          });

          $("#btnCancel").click(function(){
            window.location.href = "/Incidencias/";
          });//fin del btnAdd

        });//fin del document.ready
      </script>
html;
      $incidencia = IncidenciasDao::getById($id);

      View::set('incidencia',$incidencia);
      View::set('header',$this->_contenedor->header(''));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("incidencias_view");
    }

    public function delete(){
      $id = MasterDom::getDataAll('borrar');
      $array = array();
      foreach ($id as $key => $value) {
        $id = IncidenciasDao::delete($value);
        if($id['seccion'] == 2){
          array_push($array, array('seccion' => 2, 'id' => $id['id'] ));
        }else if($id['seccion'] == 1){
          array_push($array, array('seccion' => 1, 'id' => $id['id'] ));
        }
      }
      $this->alertas("Eliminacion de incidencias", $array, "/Incidencias/");
    }

    public function incidenciasAdd(){
      $incidencias = new \stdClass();
      $identificador_incidencia = MasterDom::getDataAll('identificador_incidencia');
      $identificador_incidencia = MasterDom::procesoAcentosNormal($identificador_incidencia);
      $incidencias->_identificador_incidencia = $identificador_incidencia;
      $nombre = MasterDom::getDataAll('nombre');
      $nombre = MasterDom::procesoAcentosNormal($nombre);
      $incidencias->_nombre = $nombre;
      $descripcion = MasterDom::getDataAll('descripcion');
      $descripcion = MasterDom::procesoAcentosNormal($descripcion);
      $incidencias->_descripcion = $descripcion;
      $incidencias->_color = MasterDom::getData('color');
      $incidencias->_status = MasterDom::getData('status');
      $incidencias->_genera_falta = MasterDom::getData('genera_falta');

      $id = IncidenciasDao::insert($incidencias);
      if($id >= 1)
        $this->alerta($id,'add');
      else
        $this->alerta($id,'error');
    }

    public function incidenciasEdit(){
      $incidencias = new \stdClass();
      $id = incidenciasDao::verificarRelacion(MasterDom::getData('catalogo_incidencia_id'));
      $incidencias->_catalogo_incidencia_id = MasterDom::getData('catalogo_incidencia_id');
      $identificador_incidencia = MasterDom::getDataAll('identificador_incidencia');
      $identificador_incidencia = MasterDom::procesoAcentosNormal($identificador_incidencia);
      $incidencias->_identificador_incidencia = $identificador_incidencia;
      $nombre = MasterDom::getDataAll('nombre');
      $nombre = MasterDom::procesoAcentosNormal($nombre);
      $incidencias->_nombre = $nombre;
      $descripcion = MasterDom::getDataAll('descripcion');
      $descripcion = MasterDom::procesoAcentosNormal($descripcion);
      $incidencias->_descripcion = $descripcion;
      $incidencias->_color = MasterDom::getData('color');
      $incidencias->_status = MasterDom::getData('status');
      $incidencias->_genera_falta = MasterDom::getData('genera_falta');


      $array = array();
      if($id['seccion'] == 2){
        array_push($array, array('seccion' => 2, 'id' => $id['id'] ));
        //
        $idStatus = (MasterDom::getData('status')!=2) ? true : false;
        if($idStatus){
          if(incidenciasDao::update($incidencias) > 0)
            $this->alerta($id,'edit');
          else
            $this->alerta($id,'nothing');
        }else{
          $this->alertas("Eliminacion de incidencias", $array, "/Incidencias/");
        }
      }

      if($id['seccion'] == 1){
        array_push($array, array('seccion' => 1, 'id' => $id['id'] ));

        //$id = incidenciasDao::update($incidencias);

        if(MasterDom::getData('status') == 2){
          incidenciasDao::update($incidencias);
          $this->alerta(MasterDom::getData('catalogo_incidencia_id'),'delete');
        }else{
          if(incidenciasDao::update($incidencias) >= 1) $this->alerta($id,'edit');
          else $this->alerta("",'no_cambios');
        }

      }
    }

    public function validoIdentificadorIncidencia(){
      $dato = IncidenciasDao::getIdentificadorIncidencia($_POST['identificador_incidencia']);
      if($dato == 1){
        echo "true";
      }else{
        echo "false";
      }
    }

    public function validoNombreIncidencia(){
      $dato = IncidenciasDao::getNombreIncidencia($_POST['nombre']);
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
<H1 class="titulo">Incidencias</H1>
<table border="0" style="width:100%;text-align: center">
    <tr style="background-color:#B8B8B8;">
    <th><strong>Id</strong></th>
    <th><strong>Identificador Incidencia</strong></th>
    <th><strong>Nombre</strong></th>
    <th><strong>Descripción</strong></th>
    <th><strong>Status</strong></th>
    </tr>
html;

      if($ids!=''){
        foreach ($ids as $key => $value) {
          $incidencia = IncidenciasDao::getByIdReporte($value);
          print_r($value);
            $tabla.=<<<html
              <tr style="background-color:#B8B8B8;">
              <td style="height:auto; width: 200px;background-color:{$incidencia['color']}">{$incidencia['catalogo_incidencia_id']}</td>
              <td style="height:auto; width: 200px;background-color:{$incidencia['color']}">{$incidencia['identificador_incidencia']}</td>
              <td style="height:auto; width: 200px;background-color:{$incidencia['color']}">{$incidencia['nombre']}</td>
              <td style="height:auto; width: 200px;background-color:{$incidencia['color']}">{$incidencia['descripcion']}</td>
              <td style="height:auto; width: 200px;background-color:{$incidencia['color']}">{$incidencia['status']}</td>
              </tr>
html;
        }
      }else{
        foreach (IncidenciasDao::getAll() as $key => $incidencia) {
          $tabla.=<<<html
          <tr style="background-color:#B8B8B8;">
          <td style="height:auto; width: 200px;background-color:{$incidencia['color']}">{$incidencia['catalogo_incidencia_id']}</td>
          <td style="height:auto; width: 200px;background-color:{$incidencia['color']}">{$incidencia['identificador_incidencia']}</td>
          <td style="height:auto; width: 200px;background-color:{$incidencia['color']}">{$incidencia['nombre']}</td>
          <td style="height:auto; width: 200px;background-color:{$incidencia['color']}">{$incidencia['descripcion']}</td>
          <td style="height:auto; width: 200px;background-color:{$incidencia['color']}">{$incidencia['status']}</td>
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

      $controlador = "Incidencias";
      $columna = array('A','B','C','D','E','F');
      $nombreColumna = array('Id','Identificador Incidencia','Nombre','Descripción','Status');
      $nombreCampo = array('catalogo_incidencia_id','identificador_incidencia','nombre','descripcion','status');

      $objPHPExcel->getActiveSheet()->SetCellValue('A'.$fila, 'Reporte de Incidencias');
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
          $incidencia = IncidenciasDao::getByIdReporte($value);
          foreach ($nombreCampo as $key => $campo) {
            $objPHPExcel->getActiveSheet()->SetCellValue($columna[$key].$fila, html_entity_decode($incidencia[$campo], ENT_QUOTES, "UTF-8"));
            $objPHPExcel->getActiveSheet()->getStyle($columna[$key].$fila)->applyFromArray($estilo_celda);
            $objPHPExcel->getActiveSheet()->getStyle($columna[$key].$fila)->getAlignment()->setWrapText($adaptarTexto);
          }
          $fila +=1;
        }
      }else{
        foreach (IncidenciasDao::getAll() as $key => $value) {
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
      $regreso = "/Incidencias/";

      if($parametro == 'add'){
        $mensaje = "Se ha agregado correctamente";
        $class = "success";
      }

      if($parametro == 'edit'){
        $mensaje = "Se ha modificado correctamente";
        $class = "success";
      }

      if($parametro == 'delete'){
        $mensaje = "Se ha eliminado correctamente";
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

      if($parametro == 'no_cambios'){
        $mensaje = "No intentaste actualizar ningún campo";
        $class = "warning";
      }

      if($parametro == "error"){
        $mensaje = "Al parecer ha ocurrido un problema";
        $class = "danger";
      }+
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
              <h4>El ID <b>{$value['id']}</b>, no se puede eliminar, ya que esta siendo utilizado por el Catálogo de Colaboradores</h4>
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
