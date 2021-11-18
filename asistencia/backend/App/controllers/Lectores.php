<?php
namespace App\controllers;
defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \App\controllers\Contenedor;
use \Core\Controller;
use \App\models\Lectores AS LectoresDao;

class Lectores extends Controller{

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
              $('#all').attr('action', '/Lectores/generarPDF/');
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });

            $("#export_excel").click(function(){
              $('#all').attr('action', '/Lectores/generarExcel/');
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });

            $("#delete").click(function(){
              var seleccionados = $("input[name='borrar[]']:checked").length;
              if(seleccionados>0){
                alertify.confirm('¿Segúro que desea eliminar lo seleccionado?', function(response){
                  if(response){
                    $('#all').attr('action', '/Lectores/delete');
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

      $lectores = LectoresDao::getAll();
      $usuario = $this->__usuario;
      $editarHidden = (Controller::getPermisosUsuario($usuario, "seccion_lectores", 5)==1)?  "" : "style=\"display:none;\"";
      $eliminarHidden = (Controller::getPermisosUsuario($usuario, "seccion_lectores", 6)==1)? "" : "style=\"display:none;\"";
      $tabla= '';
      foreach ($lectores as $key => $value) {
        $explode = explode('_', $value['identificador']);
        $identificador = strtoupper($explode['0']);
        $tabla.=<<<html
                <tr>
                    <td><input type="checkbox" name="borrar[]" value="{$value['catalogo_lector_id']}"/></td>
                    <th>{$value['ubicacion']}</th>
                    <th>{$value['tipo_comunicacion']}</th>
                    <th>{$value['ip_lector']}</th>
                    <th>{$value['puerto']}</th>
                    <th>{$value['descripcion']}</th>
                    <th>{$identificador} </th>
                    <td class="center">
                        <a href="/Lectores/edit/{$value['catalogo_lector_id']}" {$editarHidden} type="submit" name="id" class="btn btn-primary"><span class="fa fa-pencil-square-o" style="color:white"></span> </a>
                        <a href="/Lectores/show/{$value['catalogo_lector_id']}" type="submit" name="id_empresa" class="btn btn-success"><span class="glyphicon glyphicon-eye-open" style="color:white"></span> </a>
                    </td>
                </tr>
html;
      }
      $pdfHidden = (Controller::getPermisosUsuario($usuario, "seccion_lectores", 2)==1)?  "" : "style=\"display:none;\"";
      $excelHidden = (Controller::getPermisosUsuario($usuario, "seccion_lectores", 3)==1)? "" : "style=\"display:none;\"";
      $agregarHidden = (Controller::getPermisosUsuario($usuario, "seccion_lectores", 4)==1)? "" : "style=\"display:none;\"";

      View::set('pdfHidden',$pdfHidden);
      View::set('excelHidden',$excelHidden);
      View::set('agregarHidden',$agregarHidden);
      View::set('editarHidden',$editarHidden);
      View::set('eliminarHidden',$eliminarHidden);
      View::set('tabla',$tabla);
      View::set('header',$this->_contenedor->header($extraHeader));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("lectores_all");
    }

    public function add(){
      $extraFooter =<<<html
      <script src="/js/jquery.inputmask.bundle.min.js"></script>
      <script>
        $(document).ready(function(){
          
          $.validator.addMethod("verificarNombreLector",
            function(value, element) {
              var result = false;
              $.ajax({
                type:"POST",
                async: false,
                url: "/Lectores/validarNombreLector", // script to validate in server side
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

          $.validator.addMethod('IP4Checker', function(value) {
            var ip = "^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$";
            return value.match(ip);
          }, 'No es una IP valida');

          $("#add").validate({
            rules:{
              nombre:{
                required: true,
                verificarNombreLector: true
              },
              tipo_comunicacion: {
                required: true
              },
              ip_comunicacion:{
                required: true,
                IP4Checker: true
              },
              puerto: {
                required: true,
                digits: true
              },
              descripcion:{
                required: true
              },
              status: {
                required: true
              }

            },
            messages:{
              nombre:{
                required: "Este campo es requerido"
              },
              tipo_comunicacion: {
                required: "Este campo es requerido"
              },
              ip_comunicacion: {
                required: "Este campo es requerido"
              },
              puerto: {
                required: "Este campo es requerido",
                digits: "No se aceptan letras, solo numeros"
              },
              descripcion: {
                required: "Este campo es requerido"
              },
              status: {
                required: "Este campo es requerido"
              }
            }
          });//fin del jquery validate

          $("#btnCancel").click(function(){
            window.location.href = "/Lectores/";
          });//fin del btnAdd

        });//fin del document.ready
      </script>
html;

      $optionStatus = '';
      foreach (lectoresDao::getStatus() as $key => $val2) {
        $optionStatus .= <<<html
        <option value="{$val2['catalogo_status_id']}">{$val2['nombre']}</option>
html;
      }

      View::set('optionStatus', $optionStatus);
      View::set('header',$this->_contenedor->header(''));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("lectores_add");
    }

    public function edit($id){
      $extraFooter =<<<html
      <script>
        $(document).ready(function(){

          $.validator.addMethod('IP4Checker', function(value) {
            var ip = "^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$";
            return value.match(ip);
          }, 'No es una IP valida');

          $("#add").validate({
            rules:{
              nombre:{
                required: true
              },
              tipo_comunicacion: {
                required: true
              },
              ip_comunicacion:{
                required: true,
                IP4Checker: true
              },
              puerto: {
                required: true,
                digits: true
              },
              descripcion:{
                required: true
              },
              status: {
                required: true
              }

            },
            messages:{
              nombre:{
                required: "Este campo es requerido"
              },
              tipo_comunicacion: {
                required: "Este campo es requerido"
              },
              ip_comunicacion: {
                required: "Este campo es requerido"
              },
              puerto: {
                required: "Este campo es requerido",
                digits: "No se aceptan letras, solo numeros"
              },
              descripcion: {
                required: "Este campo es requerido"
              },
              status: {
                required: "Este campo es requerido"
              }
            }
          });//fin del jquery validate

          $("#btnCancel").click(function(){
            window.location.href = "/Lectores/";
          });//fin del btnAdd

        });//fin del document.ready
      </script>
html;
      $getDataLector = lectoresDao::getById($id);

      $optionStatus = "";
      foreach (lectoresDao::getStatus() as $key => $value) {
        $selected = ($getDataLector['status']==$value['catalogo_status_id'])? 'selected' : '';
        $optionStatus .=<<<html
        <option {$selected} value="{$value['catalogo_status_id']}">{$value['nombre']}</option>
html;
      }
      $lectores = lectoresDao::getById($id);

      View::set('optionUbicacionId', $optionUbicacionId);
      View::set('optionStatus', $optionStatus);
      View::set('setDataLector', $getDataLector);
      View::set('lectores',$lectores);
      View::set('header',$this->_contenedor->header(''));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("lectores_edit");
    }

    public function show($id){
      $extraFooter =<<<html
      <script>
        $(document).ready(function(){

          $("#btnCancel").click(function(){
            window.location.href = "/Lectores/";
          });//fin del btnAdd

        });//fin del document.ready
      </script>
html;
      $lectores = lectoresDao::getDataLector($id);
      View::set('lector',$lectores);
      View::set('header',$this->_contenedor->header(''));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("lectores_view");
    }

    public function delete(){
      $id = MasterDom::getDataAll('borrar');
      $array = array();
      foreach ($id as $key => $value) {
        $id = lectoresDao::delete($value);
        if($id['seccion'] == 2){
          array_push($array, array('seccion' => 2, 'id' => $id['id'] ));
        }else if($id['seccion'] == 1){
          array_push($array, array('seccion' => 1, 'id' => $id['id'] ));
        }      }
      $this->alertas("Eliminacion de lectores", $array, "/Lectores/");
    }

    public function lectoresAdd(){
      $lectores = new \stdClass();
      $lectores->_ubicacion_id = MasterDom::getData('ubicacion_id');
      $lectores->_tipo_comunicacion = MasterDom::getData('tipo_comunicacion');
      $lectores->_ip_comunicacion = MasterDom::getData('ip_comunicacion');
      $lectores->_puerto = MasterDom::getData('puerto');
      $descripcion = MasterDom::getDataAll('descripcion');
      $descripcion = MasterDom::procesoAcentosNormal($descripcion);
      $lectores->_identificador = MasterDom::getDataAll('identificador');
      $lectores->_descripcion = $descripcion;
      $lectores->_status = MasterDom::getData('status');
      $nombre = MasterDom::getDataAll('nombre');
      $lectores->_nombre = MasterDom::procesoAcentosNormal($nombre);
      $id = lectoresDao::insert($lectores);
      if($id >= 1)
        $this->alerta($id,'add');
      else
        $this->alerta($id,'error');
    }

    public function validarNombreLector(){
      $dato = lectoresDao::getNombreLector($_POST['nombre']);
      if($dato == 1){
        echo "true";
      }else{
        echo "false";
      }
    }

    public function lectoresUpdate(){
      $lectores = new \stdClass();
      $lectores->_catalogo_lector_id = MasterDom::getData('catalogo_lector_id');
      $id = lectoresDao::verificarRelacion(MasterDom::getData('catalogo_lector_id'));
      $lectores->_ubicacion_id = MasterDom::getData('ubicacion_id');
      $lectores->_tipo_comunicacion = MasterDom::getData('tipo_comunicacion');
      $lectores->_ip_comunicacion = MasterDom::getData('ip_comunicacion');
      $lectores->_puerto = MasterDom::getData('puerto');
      $descripcion = MasterDom::getDataAll('descripcion');
      $descripcion = MasterDom::procesoAcentosNormal($descripcion);
      $lectores->_descripcion = $descripcion;
      $lectores->_status = MasterDom::getData('status');
      $nombre = MasterDom::getDataAll('nombre');
      $lectores->_nombre = MasterDom::procesoAcentosNormal($nombre);

      $array = array();
      if($id['seccion'] == 2){
        array_push($array, array('seccion' => 2, 'id' => $id['id'] ));
        $idStatus = (MasterDom::getData('status')!=2) ? true : false;
        if($idStatus){
          if(lectoresDao::update($lectores) > 0)
            $this->alerta($id,'edit');
          else
            $this->alerta($id,'nothing');
        }else{
          $this->alertas("Eliminacion de lector", $array, "/Lectores/");
        }
      }


      if($id['seccion'] == 1){
        array_push($array, array('seccion' => 1, 'id' => $id['id'] ));
        
        if(MasterDom::getData('status') == 2){
          lectoresDao::update($lectores);
          $this->alerta(MasterDom::getData('catalogo_lector_id'),'delete');
        }else{
          if(lectoresDao::update($lectores) >= 1) $this->alerta($id,'edit');
          else $this->alerta("",'no_cambios');
        }

      }
    }

    public function lectoresEdit(){
      $lectores = new \stdClass();
      $lectores->_nombre = MasterDom::getData('nombre');
      $lectores->_catalogo_lectores_id = MasterDom::getData('catalogo_lectores_id');
      $id = lectoresDao::update($lectores);
      if($id >= 1)
        $this->alerta($id,'edit');
      else
        $this->alerta($id,'nothing');
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
<H1 class="titulo">Lectores</H1>
<table border="0" style="width:100%;text-align: center">
    <tr style="background-color:#B8B8B8;">
    <th><strong>Id</strong></th>
    <th><strong>Ubicación</strong></th>
    <th><strong>Tipo Comunicación</strong></th>
    <th><strong>Dirección IP</strong></th>
    <th><strong>Puerto</strong></th>
    <th><strong>Descripción</strong></th>
    <th><strong>Status</strong></th>
    </tr>
html;

      if($ids!=''){
        foreach ($ids as $key => $value) {
          $lector = LectoresDao::getByIdReporte($value);
            $tabla.=<<<html
              <tr style="background-color:#B8B8B8;">
              <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$lector['catalogo_lector_id']}</td>
              <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$lector['ubicacion']}</td>
              <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$lector['tipo_comunicacion']}</td>
              <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$lector['ip_lector']}</td>
              <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$lector['puerto']}</td>
              <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$lector['descripcion']}</td>
              <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$lector['status']}</td>
              </tr>
html;
        }
      }else{
        foreach (LectoresDao::getAll() as $key => $lector) {
          $tabla.=<<<html
            <tr style="background-color:#B8B8B8;">
            <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$lector['catalogo_lector_id']}</td>
            <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$lector['ubicacion']}</td>
            <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$lector['tipo_comunicacion']}</td>
            <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$lector['ip_lector']}</td>
            <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$lector['puerto']}</td>
            <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$lector['descripcion']}</td>
            <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$lector['status']}</td>
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

      $controlador = "Lectores";
      $columna = array('A','B','C','D','E','F','G');
      $nombreColumna = array('Id','Ubicación','Tipo Comunicación','Dirección IP','Puerto','Descripción','Status');
      $nombreCampo = array('catalogo_lector_id','ubicacion','tipo_comunicacion','ip_lector','puerto','descripcion','status');

      $objPHPExcel->getActiveSheet()->SetCellValue('A'.$fila, 'Reporte de Lectores');
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
          $lector = LectoresDao::getByIdReporte($value);
          foreach ($nombreCampo as $key => $campo) {
            $objPHPExcel->getActiveSheet()->SetCellValue($columna[$key].$fila, html_entity_decode($lector[$campo], ENT_QUOTES, "UTF-8"));
            $objPHPExcel->getActiveSheet()->getStyle($columna[$key].$fila)->applyFromArray($estilo_celda);
            $objPHPExcel->getActiveSheet()->getStyle($columna[$key].$fila)->getAlignment()->setWrapText($adaptarTexto);
          }
          $fila +=1;
        }
      }else{
        foreach (LectoresDao::getAll() as $key => $value) {
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
      $regreso = "/Lectores/";

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
        $mensaje = "Al parecer este campo de está ha sido enlazada con un campo de ****, ya que esta usuando esta información";
        $class = "info";
      }

      if($parametro == 'no_cambios'){
        $mensaje = "No intentaste actualizar ningún campo";
        $class = "warning";
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
