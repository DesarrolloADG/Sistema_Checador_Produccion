<?php
namespace App\controllers;
defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \Core\Controller;
use \App\models\ResumenSemanal as ResumenSemanalDao;
use \App\models\Resumenes as ResumenesDao;
use \App\models\Incidencia as IncidenciaDao;

class ResumenSemanal extends Controller{

    function __construct(){
    parent::__construct();
    $this->_contenedor = new Contenedor;
        View::set('header',$this->_contenedor->header());
        View::set('footer',$this->_contenedor->footer());
    }

    public function buscarHorario($horarios, $horario){
    foreach ($horarios as $key => $value) {
      if($value['catalogo_horario_id'] == $horario['catalogo_horario_id']){
        return true;
      }
    }
    return false;
  }

  public function obtenerHorarioByDay($horario_laboral, $nombre_dia){
    foreach ($horario_laboral as $key => $value) {
      if($value['dia_semana'] == $nombre_dia){
        return $value['catalogo_horario_id'];
      }
    }

    return 'NULL';
  }


  /*****************GUARDAR PERIODO*********************/
      public function guardarPeriodoAsistenciaTest(){
            $periodo = ResumenSemanalDao::getPeriodoById(MasterDom::getData('periodo_id'));
            $fecha_fin = new \DateTime($periodo['fecha_fin']);

            $datos = new \stdClass();
            $datos->tipo = ucwords(strtolower($periodo['tipo']));
            $administrador = ResumenSemanalDao::getDatosUsuarioLogeado(MasterDom::getSesion('usuario'));
            $dias_traductor = array('Monday' => 'Lunes','Tuesday' => 'Martes','Wednesday' => 'Miercoles','Thursday' => 'Jueves','Friday' => 'Viernes','Saturday' => 'Sabado','Sunday' => 'Domingo');
            $meses_traductor = array(1 => 'ENE',2 => 'FEB',3 => 'MAR',4 => 'ABR',5 => 'MAY',6 => 'JUN',7 => 'JUL',8 => 'AGO',9 => 'SEP', 10 => 'OCT', 11 => 'NOV', 12 => 'DIC');
            $convertir_registro = array("A" => 0,"F" => -1,"FF" => -11 ,"R" => -2 ,"FR" => -22 ,"DF" => -23 ,"AA" => -24 ,"RDF" => -25 ,"FRDF" => -26 );
            $asistencia = new \stdClass();
            $asistencia->_prorrateo_periodo_id = MasterDom::getData('periodo_id');
            $hidden = ($periodo['status'] == 0)? '': ' hidden ';
            $colaboradores = ResumenSemanalDao::getAllColaboradoresPago(ucwords(strtolower($periodo['tipo'])), '');
             
            foreach($colaboradores as $key => $value){


/******************************QUITAR PARA EL FUNCIONAMIENTO DE LA FUNCION**************************************
      if($value['catalogo_colaboradores_id'] != 316){
        continue;
      }
/******************************QUITAR PARA EL FUNCIONAMIENTO DE LA FUNCION**************************************/
               $asistencia->_catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];
               $datos->numero_empleado = $value['numero_empleado'];
               $fecha_inicio = new \DateTime($periodo['fecha_inicio']);
               $datos->catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];
               $ultimo_horario = IncidenciaDao::getUltimoHorario($datos);
               $horarios = IncidenciaDao::getHorariosById($datos);
               $nombre_planta = strtolower($value['identificador']);
         
               $contadorRetardos = 0;
               $catalogo_horario_id = 0;
               $num_semana = 0;
               $existe = $this->buscarHorario($horarios, $ultimo_horario);
               if((!$existe) && $catalogo_horario_id == 0){
                 $datosBusqueda = new \stdClass();
                 $datosBusqueda->catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];
                 $catalogo_horario_id = $this->obtenerHorarioByDay(IncidenciaDao::getHorarioLaboral($datosBusqueda), $nombre_dia_semana);
               }

               while($fecha_inicio <= $fecha_fin){    
                  $nombre_dia_semana = $dias_traductor[$fecha_inicio->format('l')];
                  $dia_aux = '';
                  $llegada = '';
                  $valor1 = '';
                  if($value['horario_tipo'] == 'semanal'){
                     $nombre_dia_semana = $dias_traductor[$fecha_inicio->format('l')];
                     $num_semana = $fecha_inicio->format('N');
                     if( $num_semana == 2 || $catalogo_horario_id == 0){
                           if($catalogo_horario_id == 0){
                             if(empty($ultimo_horario)){
                               $catalogo_horario_id = $horarios[0]['catalogo_horario_id'];
                             }else{
                               if($num_semana != 2){//en caso que no sea martes y sea el primer dia del periodo
                                 $catalogo_horario_id = $ultimo_horario['catalogo_horario_id'];
                               }else{
                                 for($llave=0; $llave<count($horarios); $llave++) {
                                   if($horarios[$llave]['catalogo_horario_id'] == $ultimo_horario['catalogo_horario_id']){
                                     if( ($llave+1) >= count($horarios) ){
                                       $catalogo_horario_id = $horarios[0]['catalogo_horario_id'];
                                     }else{
                                       $catalogo_horario_id = $horarios[$llave+1]['catalogo_horario_id'];
                                     }
                                   }
                                 }//fin del for busca siguiente horario
                               }
                             }
                           }else{//en caso que $catalogo_horario_id no sea cero
                             for($llave=0; $llave<count($horarios); $llave++) {
                               if($horarios[$llave]['catalogo_horario_id'] == $catalogo_horario_id){
                                   if( ($llave+1) >= count($horarios) ){
                                     $catalogo_horario_id = $horarios[0]['catalogo_horario_id'];
                                   }else{
                                     $catalogo_horario_id = $horarios[$llave+1]['catalogo_horario_id'];
                                   }
                               }
                               if($catalogo_horario_id != $catalogo_horario_id_anterior){break;}
                             }//fin del for de los horarios

                           }//fin del for de los horarios
                     }
                  }else{
                     if($value['horario_tipo'] == 'diario'){
                       $catalogo_horario_id = '';
                     }
                  }

                  echo "catalogo_horario_id: -$catalogo_horario_id- <br>";
                  $datos->catalogo_horario_id = $catalogo_horario_id;
                  $horario_laboral = IncidenciaDao::getHorarioLaboralById($datos);
/******************************************************************************************************************************************************/
          foreach ($horario_laboral as $llave1 => $valor1) {

            $nombre_horario = strtolower($valor1['horario']);
            $color = '';
            if($dia_aux != $valor1['dia_semana']){
              $dia_aux = $valor1['dia_semana'];
              if($dia_aux  == $dias_traductor[$fecha_inicio->format('l')]){
                $datos->_catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];
                $datos->_fecha = $fecha_inicio->format('Y-m-d');
                $incidencia = ResumenesDao::getIncidencia($datos);
                
                if(count($incidencia)>0){
                  $llegada = $incidencia[0]['identificador_incidencia'];
                  $color = $incidencia[0]['color'];
                  if($incidencia[0]['genera_falta'] == 1){
                    $llegada = 'FF'; // falta (-1)
                    if(count(ResumenSemanalDao::getDiaFestivo($fecha_inicio->format('Y-m-d'))) >0){
                      $llegada = 'FDF'; //falta en dia festivo (-26)
                    }
                  }
                }else{
                  //    echo "Nombre horario: $nombre_horario - ";
                  if (preg_match("/nocturno/",$nombre_horario)){
                  //    echo 'Es un horario noctturno<br><br>';
                    $nueva_fecha = new \DateTime($fecha_inicio->format('Y-m-d').' '.$valor1['hora_entrada']);
                    $nueva_fecha->modify('-4 hours');
                    //$fecha_inicio->modify('+0 minute');
                    //$fecha_inicio->modify('-0 second');
                    //$datos->fecha_inicio = $nueva_fecha->format('Y-m-d').' '.$valor1['hora_entrada']; 
            
                $datos->fecha_inicio = $nueva_fecha->format('Y-m-d H:i:s');

                    //echo '('.$fecha_inicio->format('Y-m-d').','.$datos->fecha_inicio.',';
                    
                    $fecha_aux = new \DateTime($fecha_inicio->format('Y-m-d').' '.$valor1['hora_salida']);
                    $nueva_fecha= $fecha_aux->format('Y-m-d H:i:s');
                    $fecha_aux = new \DateTime($nueva_fecha);
                    $fecha_aux->add(new \DateInterval('P1DT2H'));
            
                    $datos->fecha_fin = $fecha_aux->format('Y-m-d H:i:s');
                    //echo $datos->fecha_fin.')<br>';


                    $registro_entrada = ResumenesDao::getAsistencia($datos, $nombre_planta);
                  }else{
                    $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' 00:00:00';
                    $nueva_fecha = $fecha_inicio->format('Y-m-d').' '.$valor1['hora_entrada'];
                    
                    $fecha_aux = new \DateTime($nueva_fecha);      
                    $minutos_tolerancia = intval($valor1['tolerancia_entrada'])*60;
                    
                    $fecha_aux->add(new \DateInterval('PT0H'.$minutos_tolerancia.'S'));
                    $datos->fecha_fin = $fecha_aux->format('Y-m-d H:i:s');
                    $registro_entrada = ResumenesDao::getAsistencia($datos, $nombre_planta);
                  }

                  //echo count($registro_entrada).'<br>';

                  

                  if(count($registro_entrada) > 0){
                    $llegada = 'A'; // asistencia (0)
                    if(count(ResumenSemanalDao::getDiaFestivo($fecha_inicio->format('Y-m-d'))) >0){
                      $llegada = 'AA'; // asistencia en dia festivo (-24)
                    }

                  }else{

                    if (preg_match("/nocturno/",$nombre_horario)){
                        $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' 12:00:00';
                        $nueva_fecha = $fecha_inicio->format('Y-m-d').' '.$valor1['hora_salida'];
                        //echo 'Fecha anterior: '.$nueva_fecha.'<br>';
                        
                        $fecha_aux = new \DateTime($nueva_fecha);      
                        $minutos_tolerancia = intval($valor1['tolerancia_entrada'])*60;
                        //echo 'Minutos de tolerancia: '.$minutos_tolerancia.'<br>';

                        //$fecha_aux->add(new \DateInterval('PT24H0S'));
                        $datos->fecha_fin = $fecha_aux->format('Y-m-d H:i:s');



                        //$datos->fecha_fin = $fecha_inicio->format('Y-m-d').' 23:59:59';  
                    }else{
                        $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' 00:00:00';
                        $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' 23:59:59';
                    }

                    
                    $datos->catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];
                    $registro_entrada = ResumenesDao::getAsistencia($datos, $nombre_planta);
                    //echo 'Numero de registros2 : '.count($registro_entrada).'<br>';
                    if(count($registro_entrada) > 0){
                      if($value['privilegiado'] == 1){
                        $llegada = 'A';
                      }else{
                        $llegada = 'R'; //retardo (-2)
                      }
                      if(count(ResumenSemanalDao::getDiaFestivo($fecha_inicio->format('Y-m-d'))) >0){
                        if($value['privilegiado'] == 1){
                          $llegada = 'A';
                        }else{
                          $llegada .= 'RDF'; //retardo en dia festivo (-25)
                        }
                      }
                      $contadorRetardos[$valor1['catalogo_horario_id']] += 1;
                }else{
                      if($value['privilegiado'] == 1){
                        $llegada = 'A';
                      }else{
                        $llegada = 'FF'; // falta (-1)
                      }
                      if(count(ResumenSemanalDao::getDiaFestivo($fecha_inicio->format('Y-m-d'))) >0){
                        $llegada = 'FDF'; //falta en dia festivo (-26)
                      }
                    }

                    if( $contadorRetardos[$valor1['catalogo_horario_id']] == $valor1['numero_retardos'] ){
                      if($value['privilegiado'] == 1){
                        $llegada = 'A';
                      }else{
                        $llegada = 'FR'; //falta por retardo (-22)
                      }
                      if(count(ResumenSemanalDao::getDiaFestivo($fecha_inicio->format('Y-m-d'))) >0){
                        $llegada .= 'FDF';
                      }
                      $contadorRetardos[$valor1['catalogo_horario_id']] = 0;
                    }

                    if($llegada != ''){break;}
                  }//fin del else del  if(count($registro_entrada) > 0)
                }// fin del el del chequeo de incidencia
              }
            }
          }//fin del for del recorrido de los horarios
/*********************************************************************************************************************/
                  if($llegada == ''){
                     $datos->_catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];
                     $datos->_fecha = $fecha_inicio->format('Y-m-d');
                     $incidencia = ResumenesDao::getIncidencia($datos);
                     if(count($incidencia)>0){
                       $llegada = $incidencia[0]['catalogo_incidencia_id'];
                       $color = $incidencia[0]['color'];
                     }else{
                       //$datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' 00:00:00';
                       if (preg_match("/nocturno/",$nombre_horario)){
                           $nueva_fecha = new \DateTime($fecha_inicio->format('Y-m-d').' '.$value['hora_entrada']);
                           $nueva_fecha->modify('-2 hours');
                           //$fecha_inicio->modify('+0 minute');
                           //$fecha_inicio->modify('-0 second');
                           $datos->fecha_inicio = $nueva_fecha->format('Y-m-d H:i:s'); 
                           $fecha_aux = new \DateTime($fecha_inicio->format('Y-m-d'));
                             $fecha_aux->add(new \DateInterval('P1D'));
                             $nueva_fecha= $fecha_aux->format('Y-m-d').' '.$value['hora_salida'];
                             $fecha_aux = new \DateTime($nueva_fecha);      
                             $fecha_aux->add(new \DateInterval('PT4H0S'));

                             $datos->fecha_fin = $fecha_aux->format('Y-m-d H:i:s');
                             $registro_entrada = ResumenesDao::getAsistencia($datos, $nombre_planta);

                           //$datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' 12:00:00';  
                       }else{
                           $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' 00:00:00';
                           $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' 23:59:59';
                           $registro_entrada = ResumenesDao::getAsistencia($datos, $nombre_planta);
                       }
                       //$registro_entrada = ResumenesDao::getAsistencia($datos, $nombre_planta);
                       if(count(ResumenSemanalDao::getDiaFestivo($fecha_inicio->format('Y-m-d'))) >0){
                         $llegada = 'DF'; // dia festivo (-23)
                         if(count($registro_entrada)>0){
                           $llegada = 'AA'; // asistencia en dia festivo (-24)
                         }
                       }else{
                         if(count($registro_entrada)>0){
                           $llegada = 'AA'; // asistencia (0) en dia  no laboral
                         }
                       }

                       if($llegada == ''){
                         $llegada = 'D'; //descanso 
                       }
                     }
                   }else{
                       $datos->_catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];
                       $datos->_fecha = $fecha_inicio->format('Y-m-d');
                       $incidencia = ResumenesDao::getIncidencia($datos);
                       if(count($incidencia)>0){
                         $llegada = $incidencia[0]['catalogo_incidencia_id'];
                         $color = $incidencia[0]['color'];
                       }
                   }


                    if($llegada != 'D' && $llegada !=''){
                        echo 'Llegada: '.$llegada.'<br>';
                        $asistencia->_fecha = $fecha_inicio->format('Y-m-d');
                        /*en caso de que $llegada sea un id de una incidencia, entra y asigna el id de la incidencia como status*/
                        if(!is_numeric($llegada)){
                            $asistencia->_estatus = $convertir_registro[$llegada];
                            $id = ResumenSemanalDao::insertPeriodoAsistencia($asistencia); 
                        }/*en caso contrario busca $llegada en el array de status $convertir_registro y asigna el status*/
                        else{
                            $asistencia->_estatus = $llegada;
                            $id = ResumenSemanalDao::insertPeriodoAsistencia($asistencia);
                        }
                    }

                  $fecha_inicio->add(new \DateInterval('P1D'));
             }//fin del while del recorrido de fechas
         }
      }
  /*****************GUARDAR PERIODO*********************/

    public function index(){
      $extraFooter =<<<html
      <script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
      <script>
          $(document).ready(function(){

            $("#btnAplicar").click(function(){
              $.ajax({
                url: '/ResumenSemanal/getTablaRangoFechas',
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
                url: '/ResumenSemanal/guardarPeriodo',
                type: 'POST',
                data:{periodo_id: $("#periodo_id").val()},
                success: function(response){
                  //alert(response);
                  //$("#respuesta").html(response);
                  $("#periodo_id").change();
                }
              });

              $.ajax({
                url: '/ResumenSemanal/guardarPeriodoAsistencia',
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
                url: '/ResumenSemanal/verificarPeriodo',
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
                url: "/ResumenSemanal/cancelarPeriodo",
                type: "POST",
                success: function(data){
                  $("#periodo_id").change();
                  alertify.alert("Se ha cancelado el cerrado del periodo");
                }
              });
            });

            $("#btnRespaldarPeriodo").click(function(){
              $.ajax({
                url: '/ResumenSemanal/respaldarPeriodo',
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
                url: '/ResumenSemanal/restaurarPeriodo',
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
      $periodos = ResumenSemanalDao::getPeriodos('SEMANAL');
      if(count($periodos) == 0){
        $periodos = array('fecha_inicio' => ' No hay ', 'fecha_fin' => ' periodo ', 'tipo' => ' abierto');
      }

      foreach ($periodos as $key => $value) {
        $sPeriodo .=<<<html
        <option value="{$value['prorrateo_periodo_id']}">{$value['fecha_inicio']} - {$value['fecha_fin']}</option>
html;
      }

      $userLogin = ResumenSemanalDao::getDatosUsuarioLogeado($this->__usuario);


      $displayBtn = ($userLogin['administrador_id'] == 20 || $userLogin['administrador_id'] == 21) ? "" : "display: none;";
      

        //echo date('W', strtotime('1993-04-23'));
        //echo date("W");
      View::set('displayBtn',$displayBtn);// SOLO MOSTRA LA OPCION PARA QUE SE GUARDEN CON EL PERSONAL DE RH XOCHIMILCO
      View::set('sPeriodo', $sPeriodo);
      View::set('header',$this->_contenedor->header($extraHeader));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("resumen_semanal");
    }

    public function respaldarPeriodo(){
      foreach (ResumenSemanalDao::getPeriodoColaboradoresById(MasterDom::getData('periodo_id')) as $key => $value) {
        $id = ResumenSemanalDao::insertPeriodoBackup( (Object) $value);
      }

      if(MasterDom::getData('tipo_periodo') == 'semanal'){
        $url = "/Resumenes/semanales/";
      }else{
        $url = "/Resumenes/quincenales/";
      }

      $this->alerta($url, 'success', 'Se ha respaldado correctamente el periodo '.MasterDom::getData('mensaje'));
    }

    public function restaurarPeriodo(){
      foreach (ResumenSemanalDao::getPeriodoBackup(MasterDom::getData('periodo_id')) as $key => $value) {
        $id = ResumenSemanalDao::restaurarPeriodo( (Object) $value);
      }
      $id = ResumenSemanalDao::deletePeriodoBackup(MasterDom::getData('periodo_id'));
      $id = ResumenSemanalDao::updatePeriodo(MasterDom::getData('periodo_id'));
      if(MasterDom::getData('tipo_periodo') == 'semanal'){
        $url = "/Resumenes/semanales/";
      }else{
        $url = "/Resumenes/quincenales/";
      }

      $this->alerta($url, 'success', 'Se ha restaurado correctamente el periodo '.MasterDom::getData('mensaje'));
    }

    public function cancelarPeriodo(){
      $id = ResumenSemanalDao::cancelarPeriodo(MasterDom::getData('periodo_id'));
      if(MasterDom::getData('tipo_periodo') == 'semanal'){
        $url = "/Resumenes/semanales/";
      }else{
        $url = "/Resumenes/quincenales/";
      }

      $this->alerta($url, 'success', 'Se ha cancelado correctamente el periodo '.MasterDom::getData('mensaje'));
    }

    public function verificarPeriodo(){
        $periodo_id = MasterDom::getData('periodo_id');
        $periodo = ResumenSemanalDao::getPeriodo($periodo_id);

        if($periodo['status'] == 0){
          if(count(ResumenSemanalDao::getPeriodoBackup($periodo_id)) > 0){
            echo 1;
          }else{
            echo 0;
          }
        }elseif ($periodo['status'] == 1) {
          if($periodo_id == ResumenSemanalDao::getMaxPeriodo()['id']){
            if(count(ResumenSemanalDao::getPeriodoBackup($periodo_id)) > 0){
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
      $periodo = ResumenSemanalDao::getPeriodoById(MasterDom::getData('periodo_id'));


      $fecha_fin = new \DateTime($periodo['fecha_fin']);
      $datos = new \stdClass();
      $datos->tipo = ucwords(strtolower($periodo['tipo']));
      $encabezado =<<<html
      <th>No. Empleado</th>
      <th>Nombre</th>
      <th>Departamento</th>
html;
      $j = 0;
      $administrador = ResumenSemanalDao::getDatosUsuarioLogeado(MasterDom::getSesion('usuario'));

      /*if($administrador['perfil_id'] == 6){
        $colaboradores = ResumenSemanalDao::getAllColaboradoresPago(ucwords(strtolower($periodo['tipo'])), '');
      }else{*/
        $colaboradores = ResumenSemanalDao::getAllColaboradoresPago(ucwords(strtolower($periodo['tipo'])), $administrador['administrador_id'], $administrador['usuario'] );
      //}


      foreach($colaboradores as $key => $value){

        $tabla .=<<<html
        <tr>
           <td>{$value['numero_empleado']}</td>
           <td>{$value['nombre']} <br>{$value['apellido_paterno']} {$value['apellido_materno']}</td>
           <td>{$value['nombre_departamento']}</td>
html;
        $datos->numero_empleado = $value['numero_empleado'];
        $fecha_inicio = new \DateTime($periodo['fecha_inicio']);
        $i=1;
        $horario_laboral = ResumenSemanalDao::getHorarioLaboral($value['catalogo_colaboradores_id']);
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
                  $incidencia = ResumenSemanalDao::getIncidencia($datos);
                  if(count($incidencia)>0){
                    $llegada = $incidencia[0]['identificador_incidencia'];
                    $color = $incidencia[0]['color'];
                    if($incidencia[0]['genera_falta'] == 1){
                        $llegada = 'F';
                    }
                  }else{
                    $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' '.ResumenSemanal::restarMinutos($valor1['hora_entrada'],30);
                    $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' '.ResumenSemanal::sumarMinutos($valor1['hora_entrada'], intval($valor1['tolerancia_entrada']));
                    $registro_entrada = ResumenSemanalDao::getAsistencia($datos);
                    if(count($registro_entrada) > 0){
                      $llegada = 'A';
                    }else{
                      $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' 00:00:00';
                      $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' 23:59:59';
                      $registro_entrada = ResumenSemanalDao::getAsistencia($datos);
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
            $incidencia = ResumenSemanalDao::getIncidencia($datos);
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
          <a href="/AsignarIncentivos/incentivos/{$value['catalogo_colaboradores_id']}/{$periodo['prorrateo_periodo_id']}/ResumenSemanal" class="btn btn-primary"><span  style="color:white"></span> Ver Incentivos</a>
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

    /**************
    public function guardarPeriodoAsistencia(){
      $asistencia = new \stdClass();
      $asistencia->_prorrateo_periodo_id = MasterDom::getData('periodo_id');
      $periodo = ResumenSemanalDao::getPeriodoById(MasterDom::getData('periodo_id'));
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
      $administrador = ResumenSemanalDao::getDatosUsuarioLogeado(MasterDom::getSesion('usuario'));
      $colaboradores = ResumenSemanalDao::getAllColaboradoresPago(ucwords(strtolower($periodo['tipo'])), '');
      foreach($colaboradores as $key => $value) {

        $nombre_planta = strtolower($value['nombre_planta']);
        $asistencia->_catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];


        $datos->numero_empleado = $value['numero_empleado'];
        $datos->catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];
        $datos->_catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];

        $fecha_inicio = new \DateTime($periodo['fecha_inicio']);
        $horario_laboral = ResumenSemanalDao::getHorarioLaboral($value['catalogo_colaboradores_id']);

        $ultimo_horario = IncidenciaDao::getUltimoHorario($datos);
        $horarios = IncidenciaDao::getHorariosById($datos);
        
        $contadorRetardos = 0;
        $catalogo_horario_id = 0;
        $num_semana = 0;

        $existe = $this->buscarHorario($horarios, $ultimo_horario);
          
        if((!$existe) && $catalogo_horario_id == 0){
          $datosBusqueda = new \stdClass();
          $datosBusqueda->catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];
          $catalogo_horario_id = $this->obtenerHorarioByDay(IncidenciaDao::getHorarioLaboral($datosBusqueda), $nombre_dia_semana);
        }

        while($fecha_inicio <= $fecha_fin){
          $nombre_dia_semana = $dias_traductor[$fecha_inicio->format('l')];
          $dia_aux = '';
          $llegada = '';

          $asistencia->_fecha = '';
          $asistencia->_estatus = '';
          $valor1 = '';

          
          $datos->_fecha = $fecha_inicio->format('Y-m-d');
          $incidencia = ResumenSemanalDao::getIncidencia($datos);
          
          if(count($incidencia) > 0){
            $llegada = $incidencia[0]['catalogo_incidencia_id']; //incidencia
            if($incidencia[0]['genera_falta'] == 1){
              $llegada = -1; //falta
              if(count(ResumenSemanalDao::getDiaFestivo($fecha_inicio->format('Y-m-d'))) >0){
                $llegada = -23;
              }
            }
          }else{

            
            if($value['horario_tipo'] == 'semanal'){
              $nombre_dia_semana = $dias_traductor[$fecha_inicio->format('l')];
              $num_semana = $fecha_inicio->format('N');

                if( $num_semana == 2 || $catalogo_horario_id == 0){
                    $catalogo_horario_id_anterior = $catalogo_horario_id;

                    if($catalogo_horario_id == 0){
                      if(empty($ultimo_horario)){
                        $catalogo_horario_id = $horarios[0]['catalogo_horario_id'];
                      }else{
                        if($num_semana != 2){//en caso que no sea martes y sea el primer dia del periodo
                          $catalogo_horario_id = $ultimo_horario['catalogo_horario_id'];
                        }else{
                          for($llave=0; $llave<count($horarios); $llave++) {
                            if($horarios[$llave]['catalogo_horario_id'] == $ultimo_horario['catalogo_horario_id']){
                              if( ($llave+1) >= count($horarios) ){
                                $catalogo_horario_id = $horarios[0]['catalogo_horario_id'];
                              }else{
                                $catalogo_horario_id = $horarios[$llave+1]['catalogo_horario_id'];
                              }
                            }
                          }//fin del for busca siguiente horario
                        }
                      }
                    }else{//en caso que $catalogo_horario_id no sea cero
                      for($llave=0; $llave<count($horarios); $llave++) {
                        if($horarios[$llave]['catalogo_horario_id'] == $catalogo_horario_id){
                            if( ($llave+1) >= count($horarios) ){
                              //echo "Horario principal<br>";
                              $catalogo_horario_id = $horarios[0]['catalogo_horario_id'];
                            }else{
                              //echo "::::$catalogo_horario_id Horario secundario::::";
                              $catalogo_horario_id = $horarios[$llave+1]['catalogo_horario_id'];
                              //echo "$catalogo_horario_id";
                            }
                        }
                        if($catalogo_horario_id != $catalogo_horario_id_anterior){break;}
                      }//fin del for de los horarios

                    }//fin del for de los horarios
                }
                
            }
            if($catalogo_horario_id != 0){
              $datos->catalogo_horario_id = $catalogo_horario_id;
            }
            
          foreach (IncidenciaDao::getHorarioLaboralById($datos) as $llave1 => $valor1) {

            if($dia_aux != $valor1['dia_semana']){
              $dia_aux = $valor1['dia_semana'];

              if($dia_aux  == $dias_traductor[$fecha_inicio->format('l')]){
                  $datos->_catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];
                  $datos->_fecha = $fecha_inicio->format('Y-m-d');
                  $incidencia = ResumenSemanalDao::getIncidencia($datos);
                  if(count($incidencia)>0){
                    $llegada = $incidencia[0]['catalogo_incidencia_id']; //incidencia
                     if($incidencia[0]['genera_falta'] == 1){
                        $llegada = -1; //falta
                        if(count(ResumenSemanalDao::getDiaFestivo($fecha_inicio->format('Y-m-d'))) >0){
                          $llegada = -23;
                        }
                    }
                  }else{
                    $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' 00:00:00';
                    $fecha_aux = new \DateTime($nueva_fecha).' '.$valor1['hora_entrada'];      
                    $minutos_tolerancia = intval($valor1['tolerancia_entrada'])*60;
                    echo 'fecha anterior: '.$fecha_aux.'    segundos tolerancia: '.$minutos_tolerancia.'<br>';
                    $fecha_aux->add(new \DateInterval('PT0H'.$minutos_tolerancia.'S'));
                    $datos->fecha_fin = $fecha_aux->format('Y-m-d H:i:s');
                    echo 'Fecha final. '.$fecha_aux->format("Y-m-d H:i:s").'<br><br>';

                    $registro_entrada = ResumenSemanalDao::getAsistencia($datos, $nombre_planta);

                    if(count($registro_entrada) > 0){
                      $llegada = 0; //asistencia
                      if(count(ResumenSemanalDao::getDiaFestivo($fecha_inicio->format('Y-m-d'))) >0){
                        $llegada = -24;
                      }

                    }else{
                      $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' 00:00:00';
                      $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' 23:59:59';
                      $registro_entrada = ResumenSemanalDao::getAsistencia($datos, $nombre_planta);
                      if(count($registro_entrada) > 0){
                          $llegada = -2;//retardo
                          if(count(ResumenSemanalDao::getDiaFestivo($fecha_inicio->format('Y-m-d'))) >0){
                            $llegada = -25;
                          }
                          $contadorRetardos[$valor1['catalogo_horario_id']] += 1;
                      }else{
                        $llegada = -1; //falta
                        if(count(ResumenSemanalDao::getDiaFestivo($fecha_inicio->format('Y-m-d'))) >0){
                          $llegada = -23;
                        }
                      }

                      if( $contadorRetardos[$valor1['catalogo_horario_id']] == $valor1['numero_retardos'] ){
                        $llegada = -22; //falta
                        if(count(ResumenSemanalDao::getDiaFestivo($fecha_inicio->format('Y-m-d'))) >0){
                          $llegada = -26;
                        }
                        $contadorRetardos[$valor1['catalogo_horario_id']] = 0;
                      }
                  }//fin del else del  if(count($registro_entrada) > 0)
                  if($llegada != ''){break;}                  
                }// fin del el del chequeo de incidencia
              }
            }
          }//fin del foreach horario laboral
          
          if(!is_numeric($llegada)){
            $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' 00:00:00';
            $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' 23:59:59';
            $registro_entrada = ResumenSemanalDao::getAsistencia($datos, $nombre_planta);
            if(count($registro_entrada) > 0){
                $llegada = -24;//asistencia en dia no laboral
                if(count(ResumenSemanalDao::getDiaFestivo($fecha_inicio->format('Y-m-d'))) >0){
                  $llegada = -24;//asistencia en dia festivo
                }
            }
          }//fin del if asistencia en dia no laboral

        }

          if(is_numeric($llegada)){
            $asistencia->_fecha = $fecha_inicio->format('Y-m-d');
            $asistencia->_estatus = $llegada;
            $id = ResumenSemanalDao::insertPeriodoAsistencia($asistencia);
            //echo "Se ha insertado el id $id<br>";
          }else{
            $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' 00:00:00';
            $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' 23:59:59';

            $registro_entrada = ResumenSemanalDao::getAsistencia($datos, $nombre_planta);
            if(count($registro_entrada) > 0){
              $llegada = 0;
              if(count(ResumenSemanalDao::getDiaFestivo($fecha_inicio->format('Y-m-d'))) >0){
                $llegada = -24;
              }

            }elseif(count(ResumenSemanalDao::getDiaFestivo($fecha_inicio->format('Y-m-d'))) >0){
              $llegada = -23;
            }


            if($llegada !=''){
              $asistencia->_fecha = $fecha_inicio->format('Y-m-d');
              $asistencia->_estatus = -24;
              $id = ResumenSemanalDao::insertPeriodoAsistencia($asistencia);
            }
          }
          $fecha_inicio->add(new \DateInterval('P1D'));
        }
    }
    /*************/

    public function guardarPeriodo(){
        $periodo = ResumenSemanalDao::getPeriodoById(MasterDom::getData('periodo_id'));
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

        $administrador = ResumenSemanalDao::getDatosUsuarioLogeado(MasterDom::getSesion('usuario'));
        
        $colaboradores = ResumenSemanalDao::getAllColaboradoresPago(ucwords(strtolower($periodo['tipo'])), '');


        /*
        if($administrador['perfil_id'] == 6 || $administrador['perfil_id'] == 1){
          $colaboradores = ResumenSemanalDao::getAllColaboradoresPago(ucwords(strtolower($periodo['tipo'])), '');
        }else{
          $colaboradores = ResumenSemanalDao::getAllColaboradoresPago(ucwords(strtolower($periodo['tipo'])), $administrador['administrador_id']);
        }
        */

        foreach($colaboradores as $key => $value){



          /******************************QUITAR PARA EL FUNCIONAMIENTO DE LA FUNCION*************************************/
      if($value['catalogo_colaboradores_id'] != 54){
        continue;
      }
        /******************************QUITAR PARA EL FUNCIONAMIENTO DE LA FUNCION**************************************/


            $fecha_inicio = new \DateTime($periodo['fecha_inicio']);
            $colaborador = new \stdClass();
            $colaborador->_catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];
           /* OBTENCION DE DIAS LABORALES DEL COLABORADOR EN SUS HORARIOS */
            $dias_laborales = ResumenSemanalDao::getHorarioLaboral($value['catalogo_colaboradores_id']);
            $ultimo_horario = ResumenSemanalDao::getLastHorario($value['catalogo_colaboradores_id']);
            $horarios = ResumenSemanalDao::getListHorario($value['catalogo_colaboradores_id']);

            $contadorRetardos = 0;
            $catalogo_horario_id = 0;
            $num_semana = 0;
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
                          ResumenSemanalDao::insertProrrateoColaboradorHorario($colaborador);
                        }

                      }
                    }
                  }elseif($value['horario_tipo'] == 'semanal'){
                     /*************************************************************************************************/
                     $nombre_dia_semana = $dias_traductor[date('l',strtotime($fecha_inicio->format('Y-m-d')))];
                     $num_semana = $fecha_inicio->format('N');
                     
                     if( $num_semana == 2 || $catalogo_horario_id == 0){
                        $catalogo_horario_id_anterior = $catalogo_horario_id;

                        if($catalogo_horario_id == 0){
                          if(empty($ultimo_horario)){
                            $catalogo_horario_id = $horarios[0]['catalogo_horario_id'];
                          }else{
                            if($num_semana != 2){//en caso que no sea martes y sea el primer dia del periodo
                              $catalogo_horario_id = $ultimo_horario['catalogo_horario_id'];
                            }else{
                              for($llave=0; $llave<count($horarios); $llave++) {
                                if($horarios[$llave]['catalogo_horario_id'] == $ultimo_horario['catalogo_horario_id']){
                                  if( ($llave+1) >= count($horarios) ){
                                    $catalogo_horario_id = $horarios[0]['catalogo_horario_id'];
                                  }else{
                                    $catalogo_horario_id = $horarios[$llave+1]['catalogo_horario_id'];
                                  }
                                }
                              }//fin del for busca siguiente horario
                            }
                          }
                        }else{//en caso que $catalogo_horario_id no sea cero
                          for($llave=0; $llave<count($horarios); $llave++) {
                            if($horarios[$llave]['catalogo_horario_id'] == $catalogo_horario_id){
                                if( ($llave+1) >= count($horarios) ){
                                  //echo "Horario principal<br>";
                                  $catalogo_horario_id = $horarios[0]['catalogo_horario_id'];
                                }else{
                                  //echo "::::$catalogo_horario_id Horario secundario::::";
                                  $catalogo_horario_id = $horarios[$llave+1]['catalogo_horario_id'];
                                  //echo "$catalogo_horario_id";
                                }
                            }
                            if($catalogo_horario_id != $catalogo_horario_id_anterior){break;}
                          }//fin del for de los horarios

                        }//fin del for de los horarios
                    }

                      if($catalogo_horario_id != 0){
                        $colaborador->_catalogo_horario_id = $catalogo_horario_id;
                        ResumenSemanalDao::insertProrrateoColaboradorHorario($colaborador);
                      }
                  }
              $fecha_inicio->add(new \DateInterval('P1D'));
            }//fin del while fechas

            /*detener la generacion de registros*
            if($value['catalogo_colaboradores_id'] == 7){
              break;
            }
            /*******************/
        }//fin del foreach

        ResumenSemanalDao::updatePeriodo(MasterDom::getData('periodo_id'), MasterDom::getData('tipo_periodo'));


        if(MasterDom::getData('tipo_periodo') == 'semanal'){
          $url = "/ResumenesTest/semanales/";
        }else{
          $url = "/ResumenesTest/quincenales/";
        }

        $this->alerta($url, 'success', 'Se ha cerrado correctamente el periodo '.MasterDom::getData('mensaje'));
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

      $periodo = ResumenSemanalDao::getPeriodoById(MasterDom::getData('periodo_id'));
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
      $resumen_semanal = ResumenSemanalDao::getColaboradoresPeriodo(MasterDom::getData('periodo_id'));
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
          $valor = ResumenSemanalDao::getFechaPeriodo($datos);

          if($valor['estatus'] > 0){
            $valor['estatus'] = ResumenSemanalDao::getIncidenciaById($valor['estatus'])['identificador_incidencia'];
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
      foreach (ResumenSemanalDao::getHorarioLaboral($id) as $key => $value) {
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


    public function alerta($regreso, $class, $mensaje){
      $regreso = ($regreso != '')? $regreso: MasterDom::getData('url');
      $class = ($class != '')? $class: MasterDom::getData('class');
      $mensaje = ($mensaje != '')? $mensaje: MasterDom::getData('mensaje');
      View::set('class',$class);
      View::set('regreso',$regreso);
      View::set('mensaje',$mensaje);
      View::set('header',$this->_contenedor->header($extraHeader));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("alerta");
    }

}
