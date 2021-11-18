<?php
namespace App\controllers;
defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \App\controllers\Contenedor;
use \Core\Controller;
use \App\models\Diasfestivos AS DiasfestivosDao;
use \App\models\General AS GeneralDao;

class Diasfestivos extends Controller{

    private $_contenedor;

    function __construct(){
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header',$this->_contenedor->header());
        View::set('footer',$this->_contenedor->footer());
    }

    public function index() {
        $extraFooter =<<<html
            <script src="/js/bootbox.min.js"></script>
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
                      $('#all').attr('action', '/Diasfestivos/generarPDF/');
                      $('#all').attr('target', '_blank');
                      $("#all").submit();
                    });

                    $("#export_excel").click(function(){
                      $('#all').attr('action', '/Diasfestivos/generarExcel/');
                      $('#all').attr('target', '_blank');
                      $("#all").submit();
                    });

                    $("#delete").click(function(){
                      var seleccionados = $("input[name='borrar[]']:checked").length;
                      if(seleccionados>0){
                        alertify.confirm('¿Segúro que desea eliminar lo seleccionado?', function(response){
                          if(response){
                            $('#all').attr('action', '/Diasfestivos/delete');
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
      $editarHidden = (Controller::getPermisosUsuario($usuario, "seccion_dias_festivos", 5)==1)?  "" : "style=\"display:none;\"";
      $eliminarHidden = (Controller::getPermisosUsuario($usuario, "seccion_dias_festivos", 6)==1)? "" : "style=\"display:none;\"";
      $diasfestivos = DiasfestivosDao::getAll();
      $tabla= '';
      foreach ($diasfestivos as $key => $value) {
        $tabla.=<<<html
                <tr>
                    <td><input type="checkbox" name="borrar[]" value="{$value['catalogo_dia_festivo_id']}"/></td>
                    <td>{$value['nombre']}</td>
                    <td>{$value['descripcion']}</td>
                    <th>{$value['fecha']}</th>
                    <th>{$value['status']}</th>
                    <td class="center">
                        <a href="/Diasfestivos/edit/{$value['catalogo_dia_festivo_id']}" {$editarHidden} type="submit" name="id" class="btn btn-primary"><span class="fa fa-pencil-square-o" style="color:white"></span> </a>
                        <a href="/Diasfestivos/show/{$value['catalogo_dia_festivo_id']}" type="submit" name="id_empresa" class="btn btn-success"><span class="glyphicon glyphicon-eye-open" style="color:white"></span> </a>
                    </td>
                </tr>
html;
        $extraFooter1 .= <<<html
<script>
$(document).on("click", "#delete", function(e) {
            bootbox.confirm("&iquest;Eliminaras los dias festivos del sistema, seleccionados?", function(result) {
                if (result)
                    $( "#delete-form" ).submit();
            });
        });
</script>
html;
      }
      $pdfHidden = (Controller::getPermisosUsuario($usuario, "seccion_dias_festivos", 2)==1)?  "" : "style=\"display:none;\"";
      $excelHidden = (Controller::getPermisosUsuario($usuario, "seccion_dias_festivos", 3)==1)? "" : "style=\"display:none;\"";
      $agregarHidden = (Controller::getPermisosUsuario($usuario, "seccion_dias_festivos", 4)==1)? "" : "style=\"display:none;\"";
      View::set('pdfHidden',$pdfHidden);
      View::set('excelHidden',$excelHidden);
      View::set('agregarHidden',$agregarHidden);
      View::set('editarHidden',$editarHidden);
      View::set('eliminarHidden',$eliminarHidden);
      View::set('tabla',$tabla);
      View::set('header',$this->_contenedor->header($extraHeader));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("diasfestivos_all");
    }

    public function add(){
        $extraFooter =<<<html
        <script src="/js/moment/moment.min.js"></script>
        <script src="/js/datepicker/scriptdatepicker.js"></script>
        <script src="/js/datepicker/datepicker2.js"></script>

        <script>
            $(document).ready(function(){
                $.validator.addMethod("verificarNombreDiaFestivo",
                  function(value, element) {
                    var result = false;
                    $.ajax({
                      type:"POST",
                      async: false,
                      url: "/Diasfestivos/validarNombreDiafestivo", // script to validate in server side
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
                          verificarNombreDiaFestivo: true
                        },
                        descripcion:{
                          required: true
                        },
                        fecha:{
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
                        descripcion:{
                          required: "Este campo es requerido"
                        },
                        fecha:{
                          required: "Este campo es requerido"
                        },
                        status:{ required: "Este campo es requerido" }
                    }
                });//fin del jquery validate

                $("#btnCancel").click(function(){
                    window.location.href = "/Diasfestivos/";
                });//fin del btnAdd
            });//fin del document.ready
      </script>
html;
        $status = GeneralDao::getStatus();
        $getStatus = '';
        foreach ($status as $key => $val2) {
            $getStatus .= <<<html
                <option value="{$val2['catalogo_status_id']}">{$val2['nombre']}</option>
html;
        }

        View::set('status', $getStatus);
        View::set('header',$this->_contenedor->header(''));
        View::set('footer',$this->_contenedor->footer($extraFooter));
        View::render("diasfestivos_add");
    }

    public function edit($id){
        $extraFooter =<<<html
        
        <script src="/js/datepicker/scriptdatepicker.js"></script>
        <script src="/js/datepicker/datepicker2.js"></script>
        
        <script>
            $(document).ready(function(){
                $("#edit").validate({
                    rules:{
                        nombre:{
                          required: true
                        },
                        descripcion:{
                          required: true
                        },
                        single_cal2:{
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
                        descripcion:{
                          required: "Este campo es requerido"
                        },
                        single_cal2:{
                          required: "Este campo es requerido"
                        },
                        status:{ required: "Este campo es requerido" }
                    }
                });//fin del jquery validate

                $("#btnCancel").click(function(){
                    window.location.href = "/Diasfestivos/";
                });//fin del btnAdd
            });//fin del document.ready
      </script>
html;
        $idDiaFestivo = DiasfestivosDao::getById($id);

        $status = GeneralDao::getStatus();
        $optionStatus = '';
        foreach ($status as $key => $val2) {
            $optionStatus .= <<<html
                <option value="{$val2['catalogo_status_id']}"
html;
            $optionStatus .= ($val2['catalogo_status_id'] == $idDiaFestivo['status']) ? "selected" : "";
            $optionStatus .= <<<html
            >{$val2['nombre']}</option>
html;
        }


        View::set('status', $optionStatus);
        View::set('Diasfestivos',$idDiaFestivo);
        View::set('header',$this->_contenedor->header(''));
        View::set('footer',$this->_contenedor->footer($extraFooter));
        View::render("diasfestivos_edit");
    }

    public function show($id){
        $extraFooter =<<<html
        <script>
            $(document).ready(function(){

                $("#btnCancel").click(function(){
                    window.location.href = "/Diasfestivos/";
                });//fin del btnAdd
            });//fin del document.ready
      </script>
html;
        $idDiaFestivo = DiasfestivosDao::getById($id);
        View::set('Diafestivo',$idDiaFestivo);
        View::set('header',$this->_contenedor->header(''));
        View::set('footer',$this->_contenedor->footer($extraFooter));
        View::render("diasfestivos_view");
    }

    public function delete(){
        $ids = MasterDom::getDataAll('borrar');
        foreach ($ids as $key => $value) {
            $id = DiasfestivosDao::delete($value);
        }

        $this->alerta($id,'delete');
    }

    public function deleteOne($id){
        $extraFooter =<<<html
        alert("info");
html;
        View::set('header',$this->_contenedor->header($extraHeader));
    }



    public function diafestivoAdd(){
      $Diasfestivos = new \stdClass();
      $nombre = MasterDom::getDataAll('nombre');
      $nombre = MasterDom::procesoAcentosNormal($nombre);
      $Diasfestivos->_nombre = $nombre;
      $descripcion = MasterDom::getDataAll('descripcion');
      $descripcion = MasterDom::procesoAcentosNormal($descripcion);
      $Diasfestivos->_descripcion = $descripcion;
      $Diasfestivos->_fecha =  MasterDom::getData('fecha');
      $Diasfestivos->_status = MasterDom::getData('status');
      $id = DiasfestivosDao::insert($Diasfestivos);
      if($id >= 1)
        $this->alerta($id,'add');
      else
        $this->alerta($id,'error');

    }

    public function diafestivoEdit(){
        $Diasfestivos = new \stdClass();
        $Diasfestivos->_catalogo_dia_festivo_id = MasterDom::getData('catalogo_dia_festivo_id');
        $Diasfestivos->_nombre = MasterDom::getData('nombre');
        $nombre = MasterDom::getDataAll('nombre');
        $nombre = MasterDom::procesoAcentosNormal($nombre);
        $Diasfestivos->_nombre = $nombre;
        $Diasfestivos->_descripcion = MasterDom::getData('descripcion');
        $fecha =  MasterDom::getData('fecha');
        //$Diasfestivos->_fecha = MasterDom::reformatDate($fecha);
        $Diasfestivos->_fecha = $fecha;
        $Diasfestivos->_status = MasterDom::getData('status');
        $id = DiasfestivosDao::update($Diasfestivos);
        if($id >= 1)
          $this->alerta($id,'edit');
        else
          $this->alerta($id,'nothing');
    }

    public function validarNombreDiafestivo(){
      $dato = DiasfestivosDao::getNombreDiaFestivo($_POST['nombre']);
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
<H1 class="titulo">Dias Festivos</H1>
<table border="0" style="width:100%;text-align: center">
    <tr style="background-color:#B8B8B8;">
    <th><strong>Id</strong></th>
    <th><strong>Nombre</strong></th>
    <th><strong>Descripción</strong></th>
    <th><strong>Fecha</strong></th>
    <th><strong>Estatus</strong></th>
    </tr>
html;

      if($ids!=''){
        foreach ($ids as $key => $value) {
          $dia_festivo = DiasfestivosDao::getByIdReporte($value);
            $tabla.=<<<html
              <tr style="background-color:#B8B8B8;">
              <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$dia_festivo['catalogo_dia_festivo_id']}</td>
              <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$dia_festivo['nombre']}</td>
              <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$dia_festivo['descripcion']}</td>
              <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$dia_festivo['fecha']}</td>
              <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$dia_festivo['status']}</td>
              </tr>
html;
        }
      }else{
        foreach (DiasfestivosDao::getAll() as $key => $dia_festivo) {
          $tabla.=<<<html
            <tr style="background-color:#B8B8B8;">
            <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$dia_festivo['catalogo_dia_festivo_id']}</td>
            <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$dia_festivo['nombre']}</td>
            <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$dia_festivo['descripcion']}</td>
            <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$dia_festivo['fecha']}</td>
            <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$dia_festivo['status']}</td>
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

      $controlador = "Dias Festivos";
      $columna = array('A','B','C','D','E','F','G');
      $nombreColumna = array('Id','Nombre','Descripción','Fecha','Status');
      $nombreCampo = array('catalogo_dia_festivo_id','nombre','descripcion','fecha','status');

      $objPHPExcel->getActiveSheet()->SetCellValue('A'.$fila, 'Reporte de Dias Festivos');
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
          $empresa = DiasfestivosDao::getByIdReporte($value);
          foreach ($nombreCampo as $key => $campo) {
            $objPHPExcel->getActiveSheet()->SetCellValue($columna[$key].$fila, html_entity_decode($empresa[$campo], ENT_QUOTES, "UTF-8"));
            $objPHPExcel->getActiveSheet()->getStyle($columna[$key].$fila)->applyFromArray($estilo_celda);
            $objPHPExcel->getActiveSheet()->getStyle($columna[$key].$fila)->getAlignment()->setWrapText($adaptarTexto);
          }
          $fila +=1;
        }
      }else{
        foreach (DiasfestivosDao::getAll() as $key => $value) {
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
      $regreso = "/Diasfestivos/";

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
