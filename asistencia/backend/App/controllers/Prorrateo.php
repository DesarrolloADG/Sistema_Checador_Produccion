<?php
namespace App\controllers;
defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \App\controllers\Contenedor;
use \Core\Controller;
use \App\models\Prorrateo AS ProrrateoDao;

class Prorrateo extends Controller{

    private $_contenedor;

    function __construct(){
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header',$this->_contenedor->header());
        View::set('footer',$this->_contenedor->footer());

        if(Controller::getPermisosUsuario($this->__usuario, "Prorrateo", 1) ==0)
          header('Location: /Principal/');

    }

    public function getUsuario(){
      return $this->__usuario;
    }

    public function index() {
      $extraHeader=<<<html
      <style>
      </style>
html;
      $extraFooter =<<<html
      <script>
        $(document).ready(function(){

          $("#periodo_id").change(function(){
              $.ajax({
                url: '/ResumenSemanal/verificarPeriodo',
                type: 'POST',
                data:{periodo_id: $("#periodo_id").val()},
                success: function(response){
                  if(response == 0){
                    $("#alerta").addClass('alert-success');
                    $("#alerta").removeClass('alert-danger');
                    $("#alerta").html('<strong>Atensión</strong> El periodo seleccionado esta abierto.');
                  }else{
                    $("#alerta").addClass('alert-danger');
                    $("#alerta").removeClass('alert-success');
                    $("#alerta").html('<strong>Atensión</strong> El periodo seleccionado esta cerrado.');
                  }
                }//funcion successsfull ajax
              });//cierre del ajax
            }); //cierre del evento

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
              $('#all').attr('action', '/Empresa/generarPDF/');
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });

            $("#export_excel").click(function(){
              $('#all').attr('action', '/Empresa/generarExcel/');
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });

            $("#btnCalcular").click(function(){
              $.ajax({
                url: '/Prorrateo/generarTablaProrrateo',
                type: 'POST',
                data: { periodo_id: $("#periodo_id").val()},
                success: function(response){
                  $("#contenedor_tabla").html(response);
                  $("#muestra-cupones").tablesorter();
                  
                  var table = $('#muestra-cupones').DataTable({
                        "columnDefs": [{
                            "orderable": false,
                            "targets": 0
                        }],
                        "ordering": true,
                        "stripeClasses": ['evenColor', 'oddColor']
                    });

                    var buttons = new $.fn.dataTable.Buttons(table, {
                         buttons: [
                           {
                               extend: 'excelHtml5',
                               text: 'Excel',
                               filename: function() {
                                   var date_edition = moment().format("YYYY-MM-DD HH[h]mm")
                                   var selected_machine_name = $("#output_select_machine select option:selected").text()
                                   return 'Prorrateo_'+date_edition + '_' + selected_machine_name
                               },
                               sheetName: 'Nombre de la hoja excel',
                               title : 'Prorrateo AG Alimentos de Granja'
                            },
                            {
                                extend: 'csvHtml5',
                                className: 'btn-datatable',
                                filename: function() {
                                   var date_edition = moment().format("YYYY-MM-DD HH[h]mm")
                                   var selected_machine_name = $("#output_select_machine select option:selected").text()
                                   return 'Prorrateo_'+date_edition + '_' + selected_machine_name
                               },
                               title : 'Prorrateo AG Alimentos de Granja'
                            },
                            {
                                extend: 'pdfHtml5',
                                className: 'btn-datatable',
                                orientation: 'landscape',
                                pageSize: 'LEGAL',
                                filename: function() {
                                   var date_edition = moment().format("YYYY-MM-DD HH[h]mm")
                                   var selected_machine_name = $("#output_select_machine select option:selected").text()
                                   return 'Prorrateo_'+date_edition + '_' + selected_machine_name
                               },
                               title : 'Prorrateo AG Alimentos de Granja'
                            }
                        ]
                    }).container().appendTo($('#buttons'));

                    $(".dt-buttons").show();
                    $(".buttons-excel").addClass('btn btn-success');
                    $(".buttons-csv").addClass('btn btn-warning');
                    $(".buttons-pdf").addClass('btn btn-primary');

                    // Remove accented character from search input as well
                    $('#muestra-cupones input[type=search]').keyup( function () {
                        var table = $('#example').DataTable();
                        table.search(
                            jQuery.fn.DataTable.ext.type.search.html(this.value)
                        ).draw();
                    });
                }
              });//fin del ajax

            });//fin evento btnCalcular

            $("#btnCalcular").click();

        });
      </script>
html;
      

      $editarHidden = (Controller::getPermisosUsuario($usuario, "seccion_empresas", 5)==1)? "" : "style=\"display:none;\"";
      $eliminarHidden = (Controller::getPermisosUsuario($usuario, "seccion_empresas", 6)==1)? "" : "style=\"display:none;\"";
      $pdfHidden = (Controller::getPermisosUsuario($usuario, "seccion_empresas", 2)==1)?  "" : "style=\"display:none;\"";
      $excelHidden = (Controller::getPermisosUsuario($usuario, "seccion_empresas", 3)==1)? "" : "style=\"display:none;\"";
      $agregarHidden = (Controller::getPermisosUsuario($usuario, "seccion_empresas", 4)==1)? "" : "style=\"display:none;\"";

      $sPeriodo = '';
      foreach (ProrrateoDao::getPeriodos() as $key => $value) {
        $sPeriodo .=<<<html
        <option value="{$value['prorrateo_periodo_id']}">{$value['fecha_inicio']} /<::::>/ {$value['fecha_fin']} /<::>/ {$value['status_name']}</option>
html;
      }

      View::set('pdfHidden',$pdfHidden);
      View::set('excelHidden',$excelHidden);
      View::set('agregarHidden',$agregarHidden);
      View::set('editarHidden',$editarHidden);
      View::set('eliminarHidden',$eliminarHidden);

      View::set('sPeriodo',$sPeriodo);
      View::set('header',$this->_contenedor->header($extraHeader));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("prorrateo_all");
    }

    public function verificarPeriodo(){
        $periodo_id = MasterDom::getData('periodo_id');
        $periodo = ResumenSemanalDao::getPeriodoById($periodo_id);

        echo $periodo['status'];
    }

    public function generarTablaProrrateo(){
      $periodo = ProrrateoDao::getPeriodoById(MasterDom::getData('periodo_id'));
      $usuario = $this->__usuario;
      $administrador = ProrrateoDao::getAdministradorId($usuario);
      if($administrador['perfil_id'] == 6){
        $colaboradores = ProrrateoDao::getAllColaboradoresPago('');
      }else{
        $colaboradores = ProrrateoDao::getAllColaboradoresPago($administrador['administrador_id']);
      }
      

      $datos = new \stdClass();
      $datos->_fecha_inicio = $periodo['fecha_inicio'];
      $datos->_fecha_fin = $periodo['fecha_fin'];      

      $tabla =<<<html
      <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
      <thead>
                <tr>
                  <th><input type="checkbox" name="checkAll" id="checkAll" value=""/></th>
                  <th>Id</th>
                  <th>Nombre</th>
                  <th>Numero Empleado</th>
                  <th>Salario Mínimo</th>
                  <th>Salario Diario</th>
                  <th>Salario Diario Integrado</th>
                  <th>Premio Asistencia</th>
                  <th>Premio Puntualidad</th>
                  <th>Horas Extra</th>
                  <th>Importe Horas Extra</th>
                  <th>Despensa</th>
                  <th>Incentivo</th>
                  <th>Domingo</th>
                  <th>Faltas</th>
                  <th>Validación</th>
                  <th>Total</th>
                </tr>
              </thead>
              <tbody>
html;

      foreach ($colaboradores as $llave => $colaborador) {
        $salario_diario = 100.58;
        $salario_diario_integrado = 124.88;
        $salario_minimo = 80.02;
        $salario = 814.20;
        $precio_hora_extra = 25.145;


        $premio_asistencia = 0;
        $premio_puntualidad = 0;
        $importe_horas_extra = 0;
        $despensa_efectivo = 0;
        $incentivo = 0;
        $domingo = 0;
        $faltas = 0;
        $validacion = ' Correcto ';
        $total_percepciones = 0;

        $datos->_catalogo_colaboradores_id = $colaborador['catalogo_colaboradores_id'];
        $datos->_periodo_id = MasterDom::getData('periodo_id');
        $incentivos = ProrrateoDao::getIncentivosColaborador($datos);
        $horario_laboral = ProrrateoDao::getHorarioLaboral($colaborador['catalogo_colaboradores_id']);

        $salario_aux = $salario;
  /******************************OBNTENCION DE LOS PREMIOS DE PUNTUALIDAD Y ASISTENCIA***********************************************/
        if(count($incentivos) > 0){
          $premio_asistencia = (($salario_diario_integrado*7)/100)*10;

          if($premio_asistencia >= $salario_aux){
            $premio_asistencia = $salario_aux;
            $salario_aux = 0;
          }else{
            $salario_aux -= $premio_asistencia;
          }

          $premio_puntualidad = (($salario_diario_integrado*7)/100)*10;
          if($salario_aux > 0){
            if($premio_puntualidad >= $salario_aux){
              $premio_puntualidad = $salario_aux;
              $salario_aux = 0;
            }else{
              $salario_aux -= $premio_puntualidad;
            }
          }     
        }
  /****************************************HORAS EXTRA Y DESPENSA EN EFECTIVO*******************************************************/
        $num_horas = 0;
        $importe_horas_extra = 0;
        if($salario_aux >= $precio_hora_extra){
          $num_horas = intval($salario_aux/$precio_hora_extra);
          if($num_horas >9){
            $num_horas = 9;
            $importe_horas_extra = $num_horas * $precio_hora_extra;
            $salario_aux -= $importe_horas_extra;
          }else{
            $importe_horas_extra = $num_horas * $precio_hora_extra;
            $salario_aux -= $importe_horas_extra;
          }
        }

        if($salario_aux > 0){
          $despensa_efectivo = (($salario_minimo*7)/100)*40;
          if($despensa_efectivo >= $salario_aux){
            $despensa_efectivo = $salario_aux;
            $salario_aux = 0;
          }else{
            $salario_aux -= $despensa_efectivo;
          }
        }

        if($salario_aux > 0){
          $incentivo = $salario_aux;
          $salario_aux = 0;
        }

        /********OBTENIENDO LOS DOMINGOS TRABAJADOS*/
        $datos_where = ' AND catalogo_incidencia_id IN (1,2) ';
        $domingos_incentivo = ProrrateoDao::getIncidenciaColaborador($datos);
        $prima_dominical = ( ($salario_diario/100)*25 );
        foreach ($domingos_incentivo as $key => $value) {
          //1.- Domingo pagado doble
          //2.- Domingo pagado triple
          switch($value['catalogo_incidencia_id']){
            case 1:
              $domingo += ($salario_diario * 2)+$prima_dominical; 
              break;
            case 2:
              $domingo += ($salario_diario * 3)+$prima_dominical;
              break;
            
          }
        }

        /*TOTAL DE PERCEPCIONES*/
        $total_percepciones = $premio_asistencia+$premio_puntualidad+$importe_horas_extra+$despensa_efectivo+$incentivo+$domingo;

        $faltas = count(ProrrateoDao::getFaltasColaborador($colaborador['catalogo_colaboradores_id'], $periodo['prorrateo_periodo_id']));

        $tabla.=<<<html
          <tr>
            <td><input type="checkbox" name="borrar[]" value="{$colaborador['catalogo_colaboradores_id']}"/></td>
            <td>{$colaborador['catalogo_colaboradores_id']}</td>
            <td>{$colaborador['nombre']} {$colaborador['apellido_paterno']} {$colaborador['apellido_materno']}</td>
            <td>{$colaborador['numero_empleado']}</td>
            <td>{$salario_minimo}</td>
            <td>{$salario_diario}</td>
            <td>{$salario_diario_integrado}</td>
            <td>{$premio_asistencia}</td>
            <td>{$premio_puntualidad}</td>
            <td>{$num_horas}</td>
            <td>{$importe_horas_extra}</td>
            <td>{$despensa_efectivo}</td>
            <td>{$incentivo}</td>
            <td>{$domingo}</td>
            <td>{$faltas}</td>
            <td>{$validacion}</td>
            <td>{$total_percepciones}</td>
          </tr>
html;
      }

      $tabla .=<<<html
      </tbody>
      </table>
html;

      echo $tabla;
    }

    public static function getHorasTrabajadasById($numero_empleado){
      $registros = array();
      $fecha_anterior = '';
      $horasCheck = ProrrateoDao::getHorasCheckById($numero_empleado);

        for($i = 0; $i<count($horasCheck); $i++) {
          $date = strtotime($horasCheck[$i]['date_check']);
          $fecha = date('Y-m-d', $date);
          $hora = date('H:i', $date);

          if($fecha_anterior=='' && $fecha!=$fecha_anterior){
            $fecha_anterior = $fecha;
            array_push($registros, strtotime($horasCheck[$i]['date_check']));
          }elseif($fecha!=$fecha_anterior){
            array_push($registros, strtotime($horasCheck[$i-1]['date_check']));
            array_push($registros, strtotime($horasCheck[$i]['date_check']));
            $fecha_anterior = $fecha;
          }

        }

        $horas = array();

        for($i = 1; $i<=count($registros); $i+=2) {

          if(date('Y-m-d', $registros[$i-1]) == date('Y-m-d', $registros[$i])){
            array_push($horas, array('hora_entrada'=>date('H:i', $registros[$i-1]), 'hora_salida'=>date('H:i', $registros[$i]) ));
          }
        }
        $horasTrabajadas = 0;
        foreach ($horas as $key => $value) {
          $horasTrabajadas += Prorrateo::getHoras($value['hora_entrada'], $value['hora_salida']);
        }

        return $horasTrabajadas;
    }

    public static function getHorasTotales($id){
      $horarios = ProrrateoDao::getHorasHorarioById($id);
      $horas = 0;
      foreach ($horarios as $key => $value) {
        $horas += Prorrateo::getHoras($value['hora_entrada'],$value['hora_salida']);
      }
      return intval($horas/60).":".(floatval($horas/60) - intval($horas/60) )*60;
    }

    public static function getHoras($hora_entrada,$hora_salida){
      $entrada = explode(":",$hora_entrada);
      $salida = explode(":",$hora_salida);
      $fecha1 = mktime($entrada[0],$entrada[1],0,0,0,0000);
      $fecha2 = mktime($salida[0],$salida[1],0,0,0,0000);
      $diferencia = $fecha2-$fecha1;
      //$diff['dias'] = (int)($diferencia/(60*60*24));
      $diff['horas'] = (int)($diferencia/(60*60));
      $diff['minutos'] = ( (float)($diferencia/(60*60)) - (int)($diferencia/(60*60)) )*60;
      return (intval($diff['horas'])*60) + $diff['minutos'];
    }

    /**
    ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::    :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
    **/

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
      <H1 class="titulo">Prorrateo </H1>
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
          $empresa = ProrrateoDao::getByIdReporte($value);
            $tabla.=<<<html
              <tr style="background-color:#B8B8B8;">
              <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$empresa['catalogo_empresa_id']}</td>
              <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$empresa['nombre']}</td>
              <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$empresa['descripcion']}</td>
              <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$empresa['status']}</td>
              </tr>
html;
        }
      }else{
        foreach (ProrrateoDao::getAll() as $key => $empresa) {
          $tabla.=<<<html
            <tr style="background-color:#B8B8B8;">
            <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$empresa['catalogo_empresa_id']}</td>
            <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$empresa['nombre']}</td>
            <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$empresa['descripcion']}</td>
            <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$empresa['status']}</td>
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

      $controlador = "Empresa";
      $columna = array('A','B','C','D');
      $nombreColumna = array('Id','Nombre','Descripción','Status');
      $nombreCampo = array('catalogo_empresa_id','nombre','descripcion','status');

      $objPHPExcel->getActiveSheet()->SetCellValue('A'.$fila, 'Reporte de Empresas');
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
          $empresa = ProrrateoDao::getByIdReporte($value);
          foreach ($nombreCampo as $key => $campo) {
            $objPHPExcel->getActiveSheet()->SetCellValue($columna[$key].$fila, html_entity_decode($empresa[$campo], ENT_QUOTES, "UTF-8"));
            $objPHPExcel->getActiveSheet()->getStyle($columna[$key].$fila)->applyFromArray($estilo_celda);
            $objPHPExcel->getActiveSheet()->getStyle($columna[$key].$fila)->getAlignment()->setWrapText($adaptarTexto);
          }
          $fila +=1;
        }
      }else{
        foreach (ProrrateoDao::getAll() as $key => $value) {
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
      $regreso = "/Empresa/";

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
