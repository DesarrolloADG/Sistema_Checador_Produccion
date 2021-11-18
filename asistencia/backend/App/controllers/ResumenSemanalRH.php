<?php
namespace App\controllers;
defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \Core\Controller;
use \App\models\ResumenSemanalRH as ResumenSemanalRHDao;

class ResumenSemanalRH extends Controller{

    function __construct(){
    parent::__construct();
    $this->_contenedor = new Contenedor;
        View::set('header',$this->_contenedor->header());
        View::set('footer',$this->_contenedor->footer());
    }

    public function index(){
      $extraFooter =<<<html
      <script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
      <script>
          $(document).ready(function(){

            $("#btnAplicar").click(function(){
              $.ajax({
                url: '/ResumenSemanalRH/getTablaRangoFechas',
                type: 'POST',
                data:{periodo_id: $("#periodo_id").val()},
                success: function(response){
                  var obj = $.parseJSON(response);
                  var datos = eval (obj);
                  //$("#registros").html(datos['tabla']);
                  //$("#encabezado").html(datos['encabezado']);
                  //$(".table").html('<thead><tr>'+datos['encabezado']+'</tr></thead><tbody>'+datos['tabla']+'</tbody>');
                  $("#contenedor_tabla").html('<table class="table table-striped table-bordered table-hover" id="muestra-cupones" name="muestra-cupones"><thead><tr>'+datos['encabezado']+'</tr></thead><tbody>'+datos['tabla']+'</tbody></table>');

                  var oTable = $('#muestra-cupones').DataTable({
                      "columnDefs": [{
                          "orderable": true,
                          "targets": 0
                      }],
                       "order": false,
                       dom: 'Bfrtip',
                      buttons: [
                          'excelHtml5',
                          'csvHtml5',
                          'pdfHtml5'
                      ]
                  });

                  $(".dt-buttons").addClass('col-md-3 col-sm-3 col-xs-3 form-group');
                  $(".dt-buttons").after('<div class="col-md-4 col-sm-4 col-xs-4" align="center"><div class="col-md-3 col-sm-3 col-xs-3"><span style="color:#169D5F" class="fa fa-circle"></span><label class="col-md-12 col-sm-12 col-xs-12">Asistencia</label></div><div class="col-md-3 col-sm-3 col-xs-3"><span style="color:#E9FC00" class="fa fa-circle"></span><label class="col-md-12 col-sm-12 col-xs-12">Retardo</label></div><div class="col-md-3 col-sm-3 col-xs-3"><span style="color:#991D04" class="fa fa-circle"></span><label class="col-md-12 col-sm-12 col-xs-12">Falta</label></div><div class="col-md-3 col-sm-3 col-xs-3"><span style="color:#628881" class="fa fa-circle"></span><label class="col-md-12 col-sm-12 col-xs-12">Descanso</label></div></div>');

                  $(".buttons-copy").addClass('btn btn-default');
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

                  //$("#periodo_id").change();

                }
              });
            });

            $("#btnGuardar").click(function(){


              $.ajax({
                url: '/ResumenSemanalRH/guardarPeriodo',
                type: 'POST',
                data:{periodo_id: $("#periodo_id").val()},
                success: function(response){
                  //alert(response);
                  //$("#respuesta").html(response);
                  $("#periodo_id").change();
                }
              });

              $.ajax({
                url: '/ResumenSemanalRH/guardarPeriodoAsistencia',
                type: 'POST',
                data:{periodo_id: $("#periodo_id").val()},
                success: function(response){
                  //alert(response);
                  //$("#respuesta").html(response);
                }
              });
              alertify.alert("Se ha cerrado correctamente el periodo");
              $("#periodo_id").change();
            });

            $("#periodo_id").change(function(){
              $.ajax({
                url: '/ResumenSemanalRH/verificarPeriodo',
                type: 'POST',
                data:{periodo_id: $("#periodo_id").val()},
                success: function(response){
                  //alert(response);
                  //$("#respuesta").html(response);
                  if(response == 0){
                    $("#btnGuardar").show();
                    $("#btnRestaurarPeriodo").hide();
                    $("#btnCancelarPeriodo").hide();
                    $("#btnRespaldarPeriodo").hide();
                    //$(".dt-buttons").addClass('hidden');

                    $("#alerta").removeClass('alert-danger');
                    $("#alerta").addClass('alert-success');
                    $("#alerta").html('<h3><strong>Atencion</strong> El periodo seleccionado esta abierto.<h3>');

                  }else{
                    if(response == 1){
                      $("#btnGuardar").show();
                      $("#btnRestaurarPeriodo").show();
                      $("#btnCancelarPeriodo").hide();
                      $("#btnRespaldarPeriodo").hide();
                      //$(".dt-buttons").addClass('hidden');

                      $("#alerta").removeClass('alert-danger');
                    $("#alerta").addClass('alert-success');
                    $("#alerta").html('<h3><strong>Atencion</strong> El periodo seleccionado esta abierto.</h3>');
                    }else{
                      if(response == -1){
                        $("#btnGuardar").hide();
                        $("#btnRestaurarPeriodo").hide();
                        $("#btnCancelarPeriodo").show();
                        $("#btnRespaldarPeriodo").show();
                        //$(".dt-buttons").removeClass('hidden');

                        $("#alerta").addClass('alert-danger');
                        $("#alerta").removeClass('alert-success');
                        $("#alerta").html('<h3><strong>Atención</strong> El periodo seleccionado esta cerrado.</h3>');
                      }else{
                        if(response == -2){
                          $("#btnGuardar").hide();
                          $("#btnRestaurarPeriodo").hide();
                          $("#btnCancelarPeriodo").show();
                          $("#btnRespaldarPeriodo").hide();
                          //$(".dt-buttons").removeClass('hidden');

                          $("#alerta").addClass('alert-danger');
                          $("#alerta").removeClass('alert-success');
                          $("#alerta").html('<h3><strong>Atensión</strong> El periodo seleccionado esta cerrado.</h3>');
                        }else{
                          $("#btnGuardar").hide();
                          $("#btnRestaurarPeriodo").hide();
                          $("#btnCancelarPeriodo").hide();
                          $("#btnRespaldarPeriodo").hide();
                          //$(".dt-buttons").removeClass('hidden');

                          $("#alerta").addClass('alert-danger');
                          $("#alerta").removeClass('alert-success');
                          $("#alerta").html('<h3><strong>Atensión</strong> El periodo seleccionado esta cerrado.</h3>');
                        }
                      }//fin del tercer if
                    }//fin del segundo if
                  }//fin del primer if

                  $("#btnAplicar").click();

                }//funcion successsfull ajax
              });//cierre del ajax
            }); //cierre del evento

            $("#btnCancelarPeriodo").click(function(){
              $.ajax({
                url: "/ResumenSemanalRH/cancelarPeriodo",
                type: "POST",
                success: function(data){
                  $("#periodo_id").change();
                  alertify.alert("Se ha cancelado el cerrado del periodo");
                }
              });
            });

            $("#btnRespaldarPeriodo").click(function(){
              $.ajax({
                url: '/ResumenSemanalRH/respaldarPeriodo',
                type: 'POST',
                data:{periodo_id: $("#periodo_id").val()},
                success: function(response){
                  $("#periodo_id").change();
                  alertify.alert("Se ha realizado el respaldo correctamente");
                }
              });
            });

            $("#btnRestaurarPeriodo").click(function(){
              $.ajax({
                url: '/ResumenSemanalRH/restaurarPeriodo',
                type: 'POST',
                data:{periodo_id: $("#periodo_id").val()},
                success: function(response){
                  $("#periodo_id").change();
                  alertify.alert("Se ha restaurado el respaldo correctamente");
                }
              });
            });
            $("#btnAplicar").click();
            $("#periodo_id").change();


          });//fin del document ready
        </script>
html;
    $sPeriodo = '';
    $periodos = ResumenSemanalRHDao::getPeriodos('SEMANAL');
    if(count($periodos) == 0){
      $periodos = array('fecha_inicio' => ' No hay ', 'fecha_fin' => ' periodo ', 'tipo' => ' abierto');
    }

    foreach ($periodos as $key => $value) {
      $sPeriodo .=<<<html
      <option value="{$value['prorrateo_periodo_id']}">{$value['fecha_inicio']} - {$value['fecha_fin']}</option>
html;
    }

        //echo date('W', strtotime('1993-04-23'));
        //echo date("W");
      View::set('sPeriodo', $sPeriodo);
      View::set('header',$this->_contenedor->header($extraHeader));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("resumen_semanal");
    }

    public function respaldarPeriodo(){
      foreach (ResumenSemanalRHDao::getPeriodoColaboradoresById(MasterDom::getData('periodo_id')) as $key => $value) {
        $id = ResumenSemanalRHDao::insertPeriodoBackup( (Object) $value);
        echo $id.'-';
      }
    }

    public function restaurarPeriodo(){
      foreach (ResumenSemanalRHDao::getPeriodoBackup(MasterDom::getData('periodo_id')) as $key => $value) {
        $id = ResumenSemanalRHDao::restaurarPeriodo( (Object) $value);

        echo $id.'-';
      }
      $id = ResumenSemanalRHDao::deletePeriodoBackup(MasterDom::getData('periodo_id'));
      $id = ResumenSemanalRHDao::updatePeriodo(MasterDom::getData('periodo_id'));
    }

    public function cancelarPeriodo(){
      $id = ResumenSemanalRHDao::cancelarPeriodo();
      
    }

    public function verificarPeriodo(){
        $periodo_id = MasterDom::getData('periodo_id');
        $periodo = ResumenSemanalRHDao::getPeriodo($periodo_id);

        if($periodo['status'] == 0){
          if(count(ResumenSemanalRHDao::getPeriodoBackup($periodo_id)) > 0){
            echo 1;
          }else{
            echo 0;
          }
        }elseif ($periodo['status'] == 1) {
          if($periodo_id == ResumenSemanalRHDao::getMaxPeriodo()['id']){
            if(count(ResumenSemanalRHDao::getPeriodoBackup($periodo_id)) > 0){
              echo -2;
            }else{
              echo -1;
            }
          }else{
            echo -3;
          }
        }
    }

    public function getTablaRangoFechas(){
      $dias_traductor = array('Monday' => 'Lunes','Tuesday' => 'Martes','Wednesday' => 'Miercoles','Thursday' => 'Jueves','Friday' => 'Viernes','Saturday' => 'Sabado','Sunday' => 'Domingo');
      $meses_traductor = array(1 => 'ENE',2 => 'FEB',3 => 'MAR',4 => 'ABR',5 => 'MAY',6 => 'JUN',7 => 'JUL',8 => 'AGO',9 => 'SEP', 10 => 'OCT', 11 => 'NOV', 12 => 'DIC');
      $periodo = ResumenSemanalRHDao::getPeriodoById(MasterDom::getData('periodo_id'));


      $fecha_fin = new \DateTime($periodo['fecha_fin']);
      $datos = new \stdClass();
      $datos->tipo = ucwords(strtolower($periodo['tipo']));
      $encabezado =<<<html
      <th>No. Empleado</th>
      <th>Nombre</th>
      <th>Departamento</th>
html;
      $j = 0;
      $administrador = ResumenSemanalRHDao::getDatosUsuarioLogeado(MasterDom::getSesion('usuario'));

        $colaboradores = ResumenSemanalRHDao::getAllColaboradoresPago('Semanal', $administrador['administrador_id'], $administrador['usuario'], $administrador['nombre_planta'] );
        
      foreach($colaboradores as $key => $value){

        $tabla .=<<<html
        <tr>
           <td>{$value['numero_empleado']}</td>
           <td>{$value['nombre']} <br>{$value['apellido_paterno']} {$value['apellido_materno']}</td>
           <td>{$value['nombre_departamento']}</td>
html;
        $datos->numero_empleado = $value['numero_identificador'];
        $fecha_inicio = new \DateTime($periodo['fecha_inicio']);
        $i=1;
        $horario_laboral = ResumenSemanalRHDao::getHorarioLaboral($value['catalogo_colaboradores_id']);
        $contadorRetardos = array();
        while($fecha_inicio <= $fecha_fin){
          $dia_aux = '';
          $llegada = '';
          foreach ($horario_laboral as $llave1 => $valor1) {
            if($dia_aux != $valor1['dia_semana']){
              $dia_aux = $valor1['dia_semana'];
              if($dia_aux  == $dias_traductor[date('l', strtotime($fecha_inicio->format('Y-m-d')))]){
                  $datos->_catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];
                  $datos->_fecha = $fecha_inicio->format('Y-m-d');
                  $incidencia = ResumenSemanalRHDao::getIncidencia($datos);
                  if(count($incidencia)>0){
                    $llegada = $incidencia[0]['identificador_incidencia'];
                    $color = $incidencia[0]['color'];
                    if($incidencia[0]['genera_falta'] == 1){
                        $llegada = 'F';
                    }
                  }else{
                    $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' '.ResumenSemanalRH::restarMinutos($valor1['hora_entrada'],30);
                    $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' '.ResumenSemanalRH::sumarMinutos($valor1['hora_entrada'], intval($valor1['tolerancia_entrada']));
                    $registro_entrada = ResumenSemanalRHDao::getAsistencia($datos);
                    if(count($registro_entrada) > 0){
                      $llegada = 'A';
                    }else{
                      $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' 00:00:00';
                      $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' 23:59:59';
                      $registro_entrada = ResumenSemanalRHDao::getAsistencia($datos);
                      if(count($registro_entrada) > 0){
                          $llegada = 'R';
                          $contadorRetardos[$valor1['catalogo_horario_id']] += 1;
                      }else{
                        $llegada = 'F';
                      }
                      if( $contadorRetardos[$valor1['catalogo_horario_id']] == $valor1['numero_retardos'] ){
                        $llegada = 'F';
                        $contadorRetardos[$valor1['catalogo_horario_id']] = 0;
                      }
                      if($llegada != ''){break;}
                  }//fin del else del  if(count($registro_entrada) > 0)
                }// fin del el del chequeo de incidencia
              }
            }
          }//fin del foreach fechas

          if($llegada == ''){
            $datos->_catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];
            $datos->_fecha = $fecha_inicio->format('Y-m-d');
            $incidencia = ResumenSemanalRHDao::getIncidencia($datos);
            if(count($incidencia)>0){
              $llegada = $incidencia[0]['identificador_incidencia'];
              $color = $incidencia[0]['color'];
            }else{
              $llegada = 'D';
            }
          }

          if ($llegada == 'A'){$color = 'green';}
          elseif($llegada == 'D'){$color = 'gray';}
          elseif($llegada == 'R'){$color = 'yellow';}
          elseif ($llegada == 'F'){$color = 'red';}
          else{$color = 'green';}


          //$llegada = ($registro_entrada['date_check']!='')? 'A' : 'F';
          $tabla .=<<<html
            <td><span class="btn btn-success"><label style="color: {$color};"> {$llegada} </label></span></td>
html;
            if($j==0){
              $encabezado .=<<<html
              <td>{$fecha_inicio->format('d')}-{$meses_traductor[intval($fecha_inicio->format('m'))]} </td>
html;
            }
            $fecha_inicio->add(new \DateInterval('P1D'));
            $i++;
        }

        $tabla .=<<<html
        <td>
          <a href="/AsignarIncentivos/incentivos/{$value['catalogo_colaboradores_id']}/{$periodo['prorrateo_periodo_id']}/ResumenSemanalRH" class="btn btn-primary"><span  style="color:white"></span> Ver Incentivos</a>
        </td>
      </tr>
html;
        $j++;
      }

      $encabezado .=<<<html
              <td>Ver Incentivos</td>
html;

      echo json_encode(array('tabla'=>$tabla, 'encabezado' => $encabezado));
    }

    public function guardarPeriodoAsistencia(){
      $asistencia = new \stdClass();
      $asistencia->_prorrateo_periodo_id = MasterDom::getData('periodo_id');
      $periodo = ResumenSemanalRHDao::getPeriodoById(MasterDom::getData('periodo_id'));
      $fecha_fin = new \DateTime($periodo['fecha_fin']);
      $dias_traductor = array(
        'Monday'=>'Lunes',
        'Tuesday'=>'Martes',
        'Wednesday'=>'Miercoles',
        'Thursday'=>'Jueves',
        'Friday'=>'Viernes',
        'Saturday'=>'Sabado',
        'Sunday'=>'Domingo');

      $datos = new \stdClass();
      $datos->tipo = ucwords(strtolower($periodo['tipo']));
      $administrador = ResumenSemanalRHDao::getDatosUsuarioLogeado(MasterDom::getSesion('usuario'));

      if($administrador['perfil_id'] == 6){
        $colaboradores = ResumenSemanalRHDao::getAllColaboradoresPago(ucwords(strtolower($periodo['tipo'])), '');
      }else{
        $colaboradores = ResumenSemanalRHDao::getAllColaboradoresPago(ucwords(strtolower($periodo['tipo'])), $administrador['administrador_id']);
      }


      foreach($colaboradores as $key => $value){
        $asistencia->_catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];
        $datos->numero_empleado = $value['numero_empleado'];
        $fecha_inicio = new \DateTime($periodo['fecha_inicio']);
        $horario_laboral = ResumenSemanalRHDao::getHorarioLaboral($value['catalogo_colaboradores_id']);

        $contadorRetardos = array();

        while($fecha_inicio <= $fecha_fin){
          $asistencia->_fecha = '';
          $asistencia->_estatus = '';
          $dia_aux = '';
          $llegada = '';

          $datos->_catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];
          $datos->_fecha = $fecha_inicio->format('Y-m-d');
          $incidencia = ResumenSemanalRHDao::getIncidencia($datos);
          
          if(count($incidencia) > 0){
            $llegada = $incidencia[0]['catalogo_incidencia_id']; //incidencia
            if($incidencia[0]['genera_falta'] == 1){
              $llegada = -1; //falta
            }
          }else{

          foreach ($horario_laboral as $llave1 => $valor1) {

            if($dia_aux != $valor1['dia_semana']){
              $dia_aux = $valor1['dia_semana'];


              if($dia_aux  == $dias_traductor[date('l', strtotime($fecha_inicio->format('Y-m-d')))]){
                  $datos->_catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];
                  $datos->_fecha = $fecha_inicio->format('Y-m-d');
                  $incidencia = ResumenSemanalRHDao::getIncidencia($datos);
                  if(count($incidencia)>0){
                    $llegada = $incidencia[0]['catalogo_incidencia_id']; //incidencia
                     if($incidencia[0]['genera_falta'] == 1){
                        $llegada = -1; //falta
                    }
                  }else{
                    $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' '.ResumenSemanalRH::restarMinutos($valor1['hora_entrada'],30);
                    $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' '.ResumenSemanalRH::sumarMinutos($valor1['hora_entrada'], intval($valor1['tolerancia_entrada']));
                    $registro_entrada = ResumenSemanalRHDao::getAsistencia($datos);
                    if(count($registro_entrada) > 0){
                      $llegada = 0; //asistencia
                    }else{
                      $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' 00:00:00';
                      $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' 23:59:59';
                      $registro_entrada = ResumenSemanalRHDao::getAsistencia($datos);
                      if(count($registro_entrada) > 0){
                          $llegada = -2;//retardo
                          $contadorRetardos[$valor1['catalogo_horario_id']] += 1;
                      }else{
                        $llegada = -1; //falta
                      }

                      if( $contadorRetardos[$valor1['catalogo_horario_id']] == $valor1['numero_retardos'] ){
                        $llegada = -1; //falta
                        $contadorRetardos[$valor1['catalogo_horario_id']] = 0;
                      }

                      if($llegada != ''){break;}
                  }//fin del else del  if(count($registro_entrada) > 0)
                }// fin del el del chequeo de incidencia
              }
            }
          }//fin del foreach fechas
        }


          if($llegada != ''){
            $asistencia->_fecha = $fecha_inicio->format('Y-m-d');
            $asistencia->_estatus = $llegada;
            $id = ResumenSemanalRHDao::insertPeriodoAsistencia($asistencia);
            echo "Se ha insertado el id $id<br>";
          }
            $fecha_inicio->add(new \DateInterval('P1D'));
        }
      }
      ResumenSemanalRHDao::updatePeriodo(MasterDom::getData('periodo_id'));
    }

    public function guardarPeriodo(){
        $periodo = ResumenSemanalRHDao::getPeriodoById(MasterDom::getData('periodo_id'));
        //$periodo = ResumenSemanalRHDao::getPeriodoById('3');
        $fecha_fin = new \DateTime($periodo['fecha_fin']);
        $dias_traductor = array(
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miercoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sabado',
            'Sunday' => 'Domingo'
        );

        $administrador = ResumenSemanalRHDao::getDatosUsuarioLogeado(MasterDom::getSesion('usuario'));
        if($administrador['perfil_id'] == 6){
        $colaboradores = ResumenSemanalRHDao::getAllColaboradoresPago(ucwords(strtolower($periodo['tipo'])), '');
      }else{
        $colaboradores = ResumenSemanalRHDao::getAllColaboradoresPago(ucwords(strtolower($periodo['tipo'])), $administrador['administrador_id']);
      }

        foreach($colaboradores as $key => $value){
            $fecha_inicio = new \DateTime($periodo['fecha_inicio']);
            $colaborador = new \stdClass();
            $colaborador->_catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];
            $num_semana = 0;

           /* OBTENCION DE DIAS LABORALES DEL COLABORADOR EN SUS HORARIOS */
            $dias_laborales = ResumenSemanalRHDao::getHorarioLaboral($value['catalogo_colaboradores_id']);
            $ultimo_horario = ResumenSemanalRHDao::getLastHorario($value['catalogo_colaboradores_id']);
            $horarios = ResumenSemanalRHDao::getListHorario($value['catalogo_colaboradores_id']);
            $catalogo_horario_id = 0;
            $contadorRetardos = 0;
           while($fecha_inicio <= $fecha_fin){
                $colaborador->_fecha = $fecha_inicio->format('Y-m-d');
                if($value['horario_tipo'] == 'diario'){
                     $nombre_dia_semana = $dias_traductor[date('l',strtotime($fecha_inicio->format('Y-m-d')))];
                     /**********************************************************************************************************/
                     $dia_aux = '';
                     foreach($dias_laborales AS $llave => $valor){
                       if($dia_aux != $valor['dia_semana']){
                        $dia_aux = $valor['dia_semana'];
                        if($valor['dia_semana'] == $nombre_dia_semana){
                          $colaborador->_catalogo_horario_id = $valor['catalogo_horario_id'];
                          ResumenSemanalRHDao::insertProrrateoColaboradorHorario($colaborador);
                        }

                      }
                    }
                  }elseif($value['horario_tipo'] == 'semanal'){
                     /*************************************************************************************************/
                     $nombre_dia_semana = $dias_traductor[date('l',strtotime($fecha_inicio->format('Y-m-d')))];

                     if( $num_semana != date('W', strtotime($fecha_inicio->format('Y-m-d'))) ){
                        $num_semana = date('W', strtotime($fecha_inicio->format('Y-m-d')));
                        //echo "Numero de semana:::$num_semana<br>";
                        $catalogo_horario_id_anterior = $catalogo_horario_id;
                          for($llave=0; $llave<count($horarios); $llave++) {
                            $valor = $horarios[$llave];
                            if(count($ultimo_horario) > 0){
                              if($valor['catalogo_horario_id'] == $ultimo_horario['catalogo_horario_id']){
                                if( ($llave+1) >= count($horarios) ){
                                  $catalogo_horario_id = $horarios[0]['catalogo_horario_id'];
                                }else{
                                  $catalogo_horario_id = $horarios[intval($llave)+1]['catalogo_horario_id'];
                                }
                              }
                            }else{
                              if($catalogo_horario_id == 0){
                                //echo "Catalogo horario id: $catalogo_horario_id:::::::::";
                                $catalogo_horario_id = $horarios[0]['catalogo_horario_id'];
                                //echo ">$catalogo_horario_id<br><br>";
                              }else{
                                //echo "Catalogo_horario_id = $catalogo_horario_id:::::::::";
                                if($valor['catalogo_horario_id'] == $catalogo_horario_id){
                                  if( ($llave+1) >= count($horarios) ){
                                    $catalogo_horario_id = $horarios[0]['catalogo_horario_id'];
                                  }else{
                                    $catalogo_horario_id = $horarios[intval($llave)+1]['catalogo_horario_id'];
                                  }
                                }
                              }
                            }
                            if($catalogo_horario_id != $catalogo_horario_id_anterior){break;}
                          }
                      }

                      if($catalogo_horario_id != ''){
                        $colaborador->_catalogo_horario_id = $catalogo_horario_id;
                        ResumenSemanalRHDao::insertProrrateoColaboradorHorario($colaborador);
                      }
                  }
              $fecha_inicio->add(new \DateInterval('P1D'));
            }//fin del while fechas
        }//fin del foreach
    }

    public function generarExcel(){
      $objPHPExcel = new \PHPExcel();
      $objPHPExcel->getProperties()->setCreator("jma");
      $objPHPExcel->getProperties()->setLastModifiedBy("jma");
      $objPHPExcel->getProperties()->setTitle("Reporte");
      $objPHPExcel->getProperties()->setSubject("Reporte");
      $objPHPExcel->getProperties()->setDescription("Descripcion");
      $objPHPExcel->setActiveSheetIndex(0);
      $gdImage = imagecreatefrompng('http://52.32.114.10:8070/img/ag_logo.png');
      $objDrawing = new \PHPExcel_Worksheet_MemoryDrawing();
      $objDrawing->setName('Sample image');$objDrawing->setDescription('Sample image');
      $objDrawing->setImageResource($gdImage);
      $objDrawing->setRenderingFunction(\PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG);
      $objDrawing->setMimeType(\PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
      $objDrawing->setWidth(50);
      $objDrawing->setHeight(125);
      $objDrawing->setCoordinates('A1');
      $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

      $estilo_titulo = array('font' => array('bold' => true,'name'=>'Verdana','size'=>16, 'color' => array('rgb' => 'FEAE41')),'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),'type' => \PHPExcel_Style_Fill::FILL_SOLID);

      $estilo_encabezado = array('font' => array('bold' => true,'name'=>'Verdana','size'=>14, 'color' => array('rgb' => 'FEAE41')),'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),'type' => \PHPExcel_Style_Fill::FILL_SOLID);

      $estilo_celda = array('font' => array('bold' => false,'name'=>'Verdana','size'=>12,'color' => array('rgb' => 'B59B68')),'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER),'type' => \PHPExcel_Style_Fill::FILL_SOLID);

      $fila = 9;
      $adaptarTexto = true;

      $controlador = "Resumen Semanal";
      $columna = array('A','B','C','D','E','F','G','H','I','J','K');
      $nombreColumna = array('Numero Empleado','Nombre','Departamento');

      $objPHPExcel->getActiveSheet()->SetCellValue('A'.$fila, 'Reporte Semanal de Periodos');
      $objPHPExcel->getActiveSheet()->mergeCells('A'.$fila.':'.$columna[count($nombreColumna)-1].$fila);
      $objPHPExcel->getActiveSheet()->getStyle('A'.$fila)->applyFromArray($estilo_titulo);
      $objPHPExcel->getActiveSheet()->getStyle('A'.$fila)->getAlignment()->setWrapText($adaptarTexto);
      $fila +=1;

      $dias_traductor = array('Monday' => 'Lunes','Tuesday' => 'Martes','Wednesday' => 'Miercoles','Thursday' => 'Jueves','Friday' => 'Viernes','Saturday' => 'Sabado','Sunday' => 'Domingo');
      $meses_traductor = array(1 => 'ENE',2 => 'FEB',3 => 'MAR',4 => 'ABR',5 => 'MAY',6 => 'JUN',7 => 'JUL',8 => 'AGO',9 => 'SEP', 10 => 'OCT', 11 => 'NOV', 12 => 'DIC');

      $periodo = ResumenSemanalRHDao::getPeriodoById(MasterDom::getData('periodo_id'));
      $fecha_fin = new \DateTime($periodo['fecha_fin']);
      $fecha_inicio = new \DateTime($periodo['fecha_inicio']);
      $columna_enzabezado = 0;
      /*COLUMNAS DE LOS DATOS DEL ARCHIVO EXCEL*/
      foreach ($nombreColumna as $key => $value) {
        $objPHPExcel->getActiveSheet()->SetCellValue($columna[$key].$fila, $value);
        $objPHPExcel->getActiveSheet()->getStyle($columna[$key].$fila)->applyFromArray($estilo_encabezado);
        $objPHPExcel->getActiveSheet()->getStyle($columna[$key].$fila)->getAlignment()->setWrapText($adaptarTexto);
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($key)->setAutoSize(true);
        $columna_enzabezado += 1;
      }
      while ($fecha_inicio <= $fecha_fin){
        $value = $fecha_inicio->format('d').' - '.$meses_traductor[intval($fecha_inicio->format('m'))];
        $objPHPExcel->getActiveSheet()->SetCellValue($columna[$columna_enzabezado].$fila, $value);
        $objPHPExcel->getActiveSheet()->getStyle($columna[$columna_enzabezado].$fila)->applyFromArray($estilo_encabezado);
        $objPHPExcel->getActiveSheet()->getStyle($columna[$columna_enzabezado].$fila)->getAlignment()->setWrapText($adaptarTexto);
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($columna_enzabezado)->setAutoSize(true);
        $columna_enzabezado += 1;
        $fecha_inicio->add(new \DateInterval('P1D'));
      }
      $fila += 1;

      $columna_enzabezado = 0;
      $resumen_semanal = ResumenSemanalRHDao::getColaboradoresPeriodo(MasterDom::getData('periodo_id'));
      foreach ($resumen_semanal as $key => $value) {
        $columna_enzabezado = 0;
        $objPHPExcel->getActiveSheet()->SetCellValue($columna[$columna_enzabezado].$fila, $value['numero_empleado']);
        $objPHPExcel->getActiveSheet()->getStyle($columna[$columna_enzabezado].$fila)->applyFromArray($estilo_celda);
        $objPHPExcel->getActiveSheet()->getStyle($columna[$columna_enzabezado].$fila)->getAlignment()->setWrapText($adaptarTexto);
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($columna_enzabezado)->setAutoSize(true);
        $columna_enzabezado += 1;

        $nombre = html_entity_decode($value['nombre']).' '.html_entity_decode($value['apellido_paterno']).' '.html_entity_decode($value['apellido_materno']);
        $objPHPExcel->getActiveSheet()->SetCellValue($columna[$columna_enzabezado].$fila, $nombre );
        $objPHPExcel->getActiveSheet()->getStyle($columna[$columna_enzabezado].$fila)->applyFromArray($estilo_celda);
        $objPHPExcel->getActiveSheet()->getStyle($columna[$columna_enzabezado].$fila)->getAlignment()->setWrapText($adaptarTexto);
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($columna_enzabezado)->setAutoSize(true);
        $columna_enzabezado += 1;

        $objPHPExcel->getActiveSheet()->SetCellValue($columna[$columna_enzabezado].$fila, $value['departamento']);
        $objPHPExcel->getActiveSheet()->getStyle($columna[$columna_enzabezado].$fila)->applyFromArray($estilo_celda);
        $objPHPExcel->getActiveSheet()->getStyle($columna[$columna_enzabezado].$fila)->getAlignment()->setWrapText($adaptarTexto);
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($columna_enzabezado)->setAutoSize(true);
        $columna_enzabezado += 1;

        $fecha_inicio = new \DateTime($periodo['fecha_inicio']);

        $datos = new \stdClass();
        $datos->_catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];
        $datos->_prorrateo_periodo_id = MasterDom::getData('periodo_id');

        while ($fecha_inicio <= $fecha_fin){
          $datos->_fecha = $fecha_inicio->format('Y-m-d');
          $valor = ResumenSemanalRHDao::getFechaPeriodo($datos);

          if($valor['estatus'] > 0){
            $valor['estatus'] = ResumenSemanalRHDao::getIncidenciaById($valor['estatus'])['identificador_incidencia'];
          }elseif ($valor['estatus'] == '') {
            $valor['estatus'] = 'Descanso';
          }elseif ($valor['estatus'] == 0) {
            $valor['estatus'] = 'Asistencia';
          }elseif ($valor['estatus'] == -1) {
            $valor['estatus'] = 'Falta';
          }elseif ($valor['estatus'] == -2) {
            $valor['estatus'] = 'Retardo';
          }

          $objPHPExcel->getActiveSheet()->SetCellValue($columna[$columna_enzabezado].$fila, $valor['estatus']);
          $objPHPExcel->getActiveSheet()->getStyle($columna[$columna_enzabezado].$fila)->applyFromArray($estilo_celda);
          $objPHPExcel->getActiveSheet()->getStyle($columna[$columna_enzabezado].$fila)->getAlignment()->setWrapText($adaptarTexto);
          $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($columna_enzabezado)->setAutoSize(true);
          $columna_enzabezado += 1;
          $fecha_inicio->add(new \DateInterval('P1D'));
        }
        $fila += 1;
      }

      $objPHPExcel->getActiveSheet()->setTitle('Reporte');
      header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
      header('Content-Disposition: attachment;filename="Reporte Semanal AG '.$controlador.'.xlsx"');
      header('Cache-Control: max-age=0');
      header('Cache-Control: max-age=1');
      header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
      header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
      header('Cache-Control: cache, must-revalidate');
      header('Pragma: public');
      \PHPExcel_Settings::setZipClass(\PHPExcel_Settings::PCLZIP);
      $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
      $objWriter->save('php://output');

    }

    public function getHorario($id){
      $dia = '';
      foreach (ResumenSemanalRHDao::getHorarioLaboral($id) as $key => $value) {
        if($value['dia_semana'] != $dia){
          $dia = $value['dia_semana'];

        }
      }
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

}
