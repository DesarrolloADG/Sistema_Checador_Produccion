<?php
namespace App\controllers;
defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \Core\Controller;
use \App\models\Incidencia as IncidenciaDao;
use \App\models\Incentivo as IncentivoDao;
use \App\models\General AS GeneralDao;
use \App\models\Resumenes as ResumenesDao;
use \App\models\Colaboradores AS ColaboradoresDao;
use \App\models\ResumenSemanal as ResumenSemanalDao;

class Resumenes extends Controller{

  function __construct(){
    parent::__construct();
    $this->_contenedor = new Contenedor;
    View::set('header',$this->_contenedor->header());
    View::set('footer',$this->_contenedor->footer());
  }

  public function getFaltas($colaborador_id, $periodo_id){
    $periodo = (Object) ResumenesDao::getPeriodoById($periodo_id);
    $faltas = ResumenesDao::getFaltasByPeriodoColaborador($colaborador_id,$periodo)['faltas'];
    //echo "Faltas de $colaborador_id son: $faltas<br>";
    return $faltas;
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


  public function getFooterTable(){
    $extraFooter = <<<html
        <script>
        $(document).ready(function(){

          $("#btnBuscar").click(function(){
            //alert("Ubicacion: "+window.location);
            $("#all").attr("action", window.location);
            $("#all").attr("target", "");
            $("#all").attr("method", "POST");
            $("#all").submit();
          });
          $("#muestra-colaboradores").tablesorter();
          var oTable = $('#muestra-colaboradores').DataTable({
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
                      ],
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
                  /*tabla*/
                  $(".dt-buttons").addClass('col-md-3 col-sm-3 col-xs-3 form-group');
                  $(".dt-buttons").before('<button class="btn btn-warning" data-toggle="collapse" data-target="#div-table" type="button">Mostrar Tabla</button><div class="collapse col-md-12 col-sm-12 col-xs-12" align="center" id="div-table"> <br> <table class="table table-striped table-bordered table-hover" id="muestra-cupones"> <tbody> <tr> <td><label style="color: green;">A</label></td><td>Asistencia</td> <td><label style="color: green;">AA</label></td><td>Asistencia en Dia Festivo y Asistencia en dia de descanso</td></tr><tr> <td><label style="color: orange;">FR</label></td><td>Falta por retardo</td> <td><label style="color: red;">FF</label></td><td>Falta</td></tr><tr> <td><label style="color: gray;">D</label></td><td>Descanso</td> <td><label style="color: gray;">DF</label></td><td>Dia festivo</td></tr><tr> <td><label style="color: yellow;">R</label></td><td>Retardo</td> <td><label style="color: green;">FDF</label></td><td>Falta dia festivo</td></tr><tr> </tr> </tbody> </table> </div>');

                  $(".buttons-excel").addClass('btn btn-success btn-sm fa-file-excel-o');
                  $(".buttons-csv").addClass('btn btn-success btn-sm fa fa-table');
                  $(".buttons-pdf").addClass('btn btn-success btn-sm fa fa-file-pdf-o');

                  $('#muestra-colaboradores input[type=search]').keyup( function () {
                    var table = $('#example').DataTable();
                    table.search(
                      jQuery.fn.DataTable.ext.type.search.html(this.value)
                    ).draw();
                  });

          $("#btnAplicar").click(function(){
              $.ajax({
                url: '/Resumenes/getTabla',
                type: 'POST',
                data:{periodo_id: $("#periodo_id").val(), tipo_periodo: $("#tipo_periodo").val(), mensaje: $("#mensaje").val()},
                success: function(response){
                  var obj = $.parseJSON(response);
                  var datos = eval (obj);
                  $("#contenedor_tabla").html('<table class="table table-striped table-bordered table-hover" id="muestra-colaboradores" name="muestra-colaboradores"><thead><tr>'+datos['encabezado']+'</tr></thead><tbody>'+datos['tabla']+'</tbody></table>');

                  var oTable = $('#muestra-colaboradores').DataTable({
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
                  $(".dt-buttons").after('<div class="col-md-6 col-sm-6 col-xs-6" align="center"><div class="col-md-2 col-sm-2 col-xs-2"><span style="color:#169D5F" class="fa fa-circle"></span><label class="col-md-12 col-sm-12 col-xs-12">Asistencia</label></div><div class="col-md-2 col-sm-2 col-xs-2"><span style="color:#E9FC00" class="fa fa-circle"></span><label class="col-md-12 col-sm-12 col-xs-12">Retardo</label></div><div class="col-md-2 col-sm-2 col-xs-2"><span style="color:#FFA357" class="fa fa-circle"></span><label class="col-md-12 col-sm-12 col-xs-12">Falta Por Retardo</label></div><div class="col-md-2 col-sm-2 col-xs-2"><span style="color:#991D04" class="fa fa-circle"></span><label class="col-md-12 col-sm-12 col-xs-12">Falta</label></div><div class="col-md-2 col-sm-2 col-xs-2"><span style="color:#628881" class="fa fa-circle"></span><label class="col-md-12 col-sm-12 col-xs-12">Descanso</label></div></div>');

                  $(".buttons-copy").addClass('btn btn-default');
                  $(".buttons-excel").addClass('btn btn-success');
                  $(".buttons-excel").addClass('fa-file-excel-o');
                  $(".buttons-csv").addClass('btn btn-warning');
                  $(".buttons-csv").addClass('fa fa-table');
                  $(".buttons-pdf").addClass('btn btn-primary');
                  $(".buttons-pdf").addClass('fa fa-file-pdf-o');
                  $(".buttons-csv").addClass('fa fa-table');
                  $(".buttons-pdf").addClass('btn btn-primary');
                  $(".buttons-pdf").addClass('fa fa-file-pdf-o');
                  verificarPeriodo();

                }
              });
            });

            $("#contenedorRelog").hide();

            $("#btnGuardar").click(function(){
              $("#btnGuardar").prop('disabled', true);

              var i = 1;
                $('#retroclockbox1').flipcountdown({
                  tick:function(){
                    return i++;
                  }
                });
              
              $("#contenedorRelog").show();

              /***********************/

              

              $.ajax({
                url: '/ResumenSemanal/guardarPeriodoAsistenciaTest',
                type: 'POST',
                async: true,
                data:{
                  periodo_id: $("#periodo_id").val(), 
                  tipo_periodo: $("#tipo_periodo").val(), 
                  mensaje: $("#mensaje").val()
                },
                success: function(response){
                  $("#all").attr("action", "/ResumenSemanal/guardarPeriodo");
                  $("#all").attr("target", "");
                  //$("#respuesta").html(response);
                  $("#all").submit();

                }
              });


              /***********************
              $("#all").attr("action", "/ResumenSemanal/guardarPeriodo");
              $("#all").attr("target", "");
              $("#all").submit();
              /**********************************/

            });

            function verificarPeriodo(){
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

                  }else{
                    if(response == 1){
                      $("#btnGuardar").show();
                      $("#btnRestaurarPeriodo").show();
                      $("#btnCancelarPeriodo").hide();
                      $("#btnRespaldarPeriodo").hide();
                      //$(".dt-buttons").addClass('hidden');
                    }else{
                      if(response == -1){
                        $("#btnGuardar").hide();
                        $("#btnRestaurarPeriodo").hide();
                        $("#btnCancelarPeriodo").show();
                        $("#btnRespaldarPeriodo").show();
                        //$(".dt-buttons").removeClass('hidden');
                      }else{
                        if(response == -2){
                          $("#btnGuardar").hide();
                          $("#btnRestaurarPeriodo").hide();
                          $("#btnCancelarPeriodo").show();
                          $("#btnRespaldarPeriodo").hide();
                          //$(".dt-buttons").removeClass('hidden');
                        }else{
                          $("#btnGuardar").hide();
                          $("#btnRestaurarPeriodo").hide();
                          $("#btnCancelarPeriodo").hide();
                          $("#btnRespaldarPeriodo").hide();
                          //$(".dt-buttons").removeClass('hidden');
                        }
                      }//fin del tercer if
                    }//fin del segundo if
                  }//fin del primer if
                }//funcion successsfull ajax
              });//cierre del ajax
            } //cierre del evento
            

            $("#btnCancelarPeriodo").click(function(){
              $("#all").attr('action','/ResumenSemanal/cancelarPeriodo');
              $("#all").submit();
            });

            $("#btnRespaldarPeriodo").click(function(){
              $("#all").attr('action','/ResumenSemanal/respaldarPeriodo');
              $("#all").submit();
            });

            $("#btnRestaurarPeriodo").click(function(){
              $("#all").attr('action','/ResumenSemanal/restaurarPeriodo');
              $("#all").attr('target','');
              $("#all").submit();
            });
        });
      </script>
html;
    return $extraFooter;
  }

  


  public function getEmpresas($post){
    $empresas = '';
    foreach (ColaboradoresDao::getIdEmpresa() as $key => $value) {
      $selected = ($post['catalogo_empresa_id'] == $value['catalogo_empresa_id'])? ' selected ':'';
      $empresas .=<<<html
      <option value="{$value['catalogo_empresa_id']}" $selected>{$value['nombre']}</option>
html;
    }
    return $empresas;
  }

  public function getUbicacion($post){
    $ubicaciones = '';
      foreach (ColaboradoresDao::getIdUbicacion() as $key => $value) {
        $selected = ($post['catalogo_ubicacion_id'] == $value['catalogo_ubicacion_id'])? ' selected ':'';
        $ubicaciones .=<<<html
        <option value="{$value['catalogo_ubicacion_id']}" $selected>{$value['nombre']}</option>
html;
    }
    return $ubicaciones;
  }

  public function getDepartamentos($post){
    $departamentos = "";
    foreach (ColaboradoresDao::getIdDepartamento() as $key => $value) {
      $selected = ($post['catalogo_departamento_id'] == $value['catalogo_departamento_id'])? ' selected ':'';
      $departamentos .=<<<html
      <option value="{$value['catalogo_departamento_id']}" $selected>{$value['nombre']}</option>
html;
    }
    return $departamentos;
  }

  public function getPuestos($post){
    $puestos = '';
    foreach (ColaboradoresDao::getIdPuesto() as $key => $value) {
      $selected = ($post['catalogo_puesto_id'] == $value['catalogo_puesto_id'])? ' selected ':'';
      $puestos .=<<<html
      <option value="{$value['catalogo_puesto_id']}" $selected>{$value['nombre']}</option>
html;
    }
    return $puestos;
  }

  public function getNominas($post){
    $nomina = "";
    foreach (ColaboradoresDao::getNominaIdentificador() as $key => $value) {
      
      if(!empty($value['identificador_noi'])){
        $selected = ($post['status'] == $value['identificador_noi'])? ' selected ':'';
        $nomina .=<<<html
        <option value="{$value['identificador_noi']}" $selected>NOMINA NOI {$value['identificador_noi']}</option>
html;
        }else{
        $nomina .=<<<html
        <option value="vacio">SIN NOMINA NOI</option>
html;
      } 
    }
    return $nomina;
  }

  public function getMenuBuscador($post){
    View::set('nomina',$this->getNominas($post));
    View::set('idPuesto',$this->getPuestos($post));
    View::set('idEmpresa',$this->getEmpresas($post));
    View::set('idUbicacion',$this->getUbicacion($post));
    View::set('idDepartamento',$this->getDepartamentos($post));

  }

  public function setTabla($idPeriodo, $tipoPeriodo, $perfil_id, $catalogo_planta_id, $catalogo_departamento_id, $catalodo_planta_nombre, $accion, $post){
    
    /*******************************CREACION DEL FILTRO PARA LOS COLABORADORES*********************************/
    $this->getMenuBuscador($post);
    $where = '';
    if($post['catalogo_empresa_id'] != ''){
      $where .= ' AND c.catalogo_empresa_id = '.$post['catalogo_empresa_id'];
    }

    if($post['catalogo_ubicacion_id'] != ''){
      $where .= ' AND c.catalogo_ubicacion_id = '.$post['catalogo_ubicacion_id'];
    }

    if($post['catalogo_departamento_id'] != ''){
      $where .= ' AND c.catalogo_departamento_id = '.$post['catalogo_departamento_id'];
    }

    if($post['catalogo_puesto_id'] != ''){
      $where .= ' AND c.catalogo_puesto_id = '.$post['catalogo_puesto_id'];
    }

    if($post['status'] != ''){
      $where .= ' AND c.identificador_noi = "'.$post['status'].'"';
    }
    /*******************************CREACION DEL FILTRO PARA LOS COLABORADORES*********************************/

    $dias_traductor = array('Monday' => 'Lunes','Tuesday' => 'Martes','Wednesday' => 'Miercoles','Thursday' => 'Jueves','Friday' => 'Viernes','Saturday' => 'Sabado','Sunday' => 'Domingo');
    $meses_traductor = array(1 => 'ENE',2 => 'FEB',3 => 'MAR',4 => 'ABR',5 => 'MAY',6 => 'JUN',7 => 'JUL',8 => 'AGO',9 => 'SEP', 10 => 'OCT', 11 => 'NOV', 12 => 'DIC');
    $periodo = ResumenesDao::getPeriodoById($idPeriodo);
    $hidden = ($periodo['status'] == 0)? '': ' hidden ';
    $fecha_fin = new \DateTime($periodo['fecha_fin']);
    $datos = new \stdClass();
    $datos->tipo = ucwords(strtolower($periodo['tipo']));
    $encabezado =<<<html
        <th>No. Empleado</th>
        <th>Nombre</th>
        <th>Informacion</th>
html;
    $j = 0;
    $colaboradores = ResumenesDao::getAllColaboradores($perfil_id, $tipoPeriodo, $catalogo_planta_id, $catalogo_departamento_id, $catalodo_planta_nombre, $accion, $where);    
    foreach($colaboradores as $key => $value){


/******************************QUITAR PARA EL FUNCIONAMIENTO DE LA FUNCION*************************************
if($value['catalogo_colaboradores_id'] != 316 ){
        continue;
}
     
/******************************QUITAR PARA EL FUNCIONAMIENTO DE LA FUNCION**************************************/
      
      $numEmpOnoi = ($value['clave_noi'] == 0) ? $value['numero_empleado'] : $value['clave_noi'];

      $nombre_planta = strtolower($value['identificador']);
      $nombreEmpleado = utf8_encode($value['apellido_paterno'])." <br /> ".utf8_encode($value['apellido_materno']). " <br />" .utf8_encode($value['nombre']);
      $tabla .=<<<html
        <tr>
          <td>{$numEmpOnoi}</td>
    <td>$nombreEmpleado</td>
          <td>
            <b>Departamento</b> {$value['nombre_departamento']} <br>
            <b>Identificador</b> {$value['nombre_planta']} <br>
          </td>
html;
        $datos->numero_empleado = $value['numero_empleado'];
        $fecha_inicio = new \DateTime($periodo['fecha_inicio']);
        $datos->catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];

        $ultimo_horario = IncidenciaDao::getUltimoHorario($datos);
        $horarios = IncidenciaDao::getHorariosById($datos);

        /**************
        print_r($horarios);
        echo '<br><br>';
database = checador
        /**************/

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
          /******************************************************************************************************************************************************/
          
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


		          //	echo "Nombre horario: $nombre_horario - ";
                  if (preg_match("/nocturno/",$nombre_horario)){
		          //	echo 'Es un horario noctturno<br><br>';
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


          //echo 'Llegada: '.$llegada.'<br>';
          if($llegada == ''){
            $datos->_catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];
            $datos->_fecha = $fecha_inicio->format('Y-m-d');
            $incidencia = ResumenesDao::getIncidencia($datos);
            if(count($incidencia)>0){
              $llegada = $incidencia[0]['identificador_incidencia'];
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
                $llegada = $incidencia[0]['identificador_incidencia'];
                $color = $incidencia[0]['color'];
              }
          }

        if ($llegada == 'A' || $llegada =='AA'){$color = 'green';}
        elseif($llegada == 'D' || $llegada == 'DF'){$color = 'gray';}
        elseif($llegada == 'R' || $llegada == 'RDF'){$color = 'yellow';}
        elseif ($llegada == 'FF' || $llegada == 'F'){$color = 'red';}
        elseif ($llegada == 'FR'){$color = 'orange';}
        $fecha_temp_dia = new \DateTime(date('Y-m-d H:i:s'));

        //echo 'Fecha actual: '.$fecha_inicio->format('Y-m-d').'    ::::   '.$fecha_temp_dia->format('Y-m-d H:i:s').'<br>';


        if($fecha_inicio > $fecha_temp_dia){
          $tabla .=<<<html
          <td style="text-align: center; vertical-align: middle; font-size: 18px;" bgcolor="#f1f1f1"><span><label style="color: {$color};"> --- </label></span></td>
html;
        }else{
          $tabla .=<<<html
          <td style="text-align: center; vertical-align: middle; font-size: 18px;" bgcolor="#f1f1f1"><span><label style="color: {$color};"> {$llegada}</label></span></td>
html;
        }
        
        if($j==0){
          $encabezado .=<<<html
            <td>{$fecha_inicio->format('d')}-{$meses_traductor[intval($fecha_inicio->format('m'))]} </td>
html;
            }
            $fecha_inicio->add(new \DateInterval('P1D'));
      
    }//fin del while del recorrido de fechas
        $cantidadIncentivosAsignados = $this->getValore($value['catalogo_colaboradores_id'], $idPeriodo);
        $cantidadNoHorasExtra = $this->getValoresNoHorasExtra($value['catalogo_colaboradores_id'], $idPeriodo);
        $cantidadDomingo = $this->getValoresDomingos($value['catalogo_colaboradores_id'], $idPeriodo);
          $tabla.=<<<html
            <td style="text-align:center; vertical-align:middle;">
              {$cantidadIncentivosAsignados}
            </td>
            <td style="text-align:center; vertical-align:middle;">
              {$cantidadNoHorasExtra}
            </td>
            <td style="text-align:center; vertical-align:middle;">
              {$cantidadDomingo}
            </td>
html;

         $tabla .=<<<html
          <td $hidden style="vertical-align: middle;">
html;
        if($tipoPeriodo == "Semanal"){
          $tabla .=<<<html
     
            <a href="/Resumenes/checadorFechas/{$value['catalogo_colaboradores_id']}/{$periodo['prorrateo_periodo_id']}/" target="_blank" class="btn btn-info" data-toggle="tooltip" title="VER ENTRADAS">
              <span class="glyphicon glyphicon-calendar"> </span>
            </a> 
            <a href="/Incentivo/getIncentivosColaborador/{$value['catalogo_colaboradores_id']}/{$periodo['prorrateo_periodo_id']}/semanales/" target="_blank" class="btn btn-primary" data-toggle="tooltip" title="VER INCENTIVOS">
              <span class="glyphicon glyphicon-tasks"></span>
            </a>
            <a href="/Incidencia/checadorFechas/{$value['catalogo_colaboradores_id']}/{$periodo['prorrateo_periodo_id']}/semanales/" target="_blank" class="btn btn-warning" data-toggle="tooltip" title="VER INCIDENCIAS">
              <span class="glyphicon glyphicon-list"></span>
            </a>
html;

	     if($value['catalogo_lector_secundario_id'] > 0){
             	$tabla .=<<<html

            <a href="/Resumenes/checadorFechas/{$value['catalogo_colaboradores_id']}/{$periodo['prorrateo_periodo_id']}/?secundario=true" target="_blank" class="btn btn-info" data-toggle="tooltip" title="VER ENTRADAS SECUNDARIO">
              <span class="glyphicon glyphicon-calendar"> </span>
            </a>
html;
            }

        }

        if($tipoPeriodo == "Quincenal"){
          $tabla .=<<<html
            <a href="/Resumenes/checadorFechas/{$value['catalogo_colaboradores_id']}/{$periodo['prorrateo_periodo_id']}/" target="_blank" class="btn btn-info" data-toggle="tooltip" title="VER ENTRADAS">
              <span class="glyphicon glyphicon-calendar"> </span>
            </a>
             <a href="/Incidencia/checadorFechas/{$value['catalogo_colaboradores_id']}/{$periodo['prorrateo_periodo_id']}/quincenales/" target="_blank" class="btn btn-warning" data-toggle="tooltip" title="VER INCIDENCIAS">
              <span class="glyphicon glyphicon-list"></span>
            </a>
html;
        }

          $tabla.=<<<html
            </td>
          </tr>
html;
    $j++;
  }
  $encabezado .=<<<html
    <td>Total Incentivos</td>
    <td>No. Horas Extras</td>
    <td>Importe Domingo</td>
    <td $hidden>Ver Incentivos</td>
html;
    View::set('tbody',$tabla);
    View::set('thead',$encabezado);
  }

  public function getValore($colaborador_id, $prorrateo_periodo_id){
    $valor = ResumenesDao::getIncentivosValor($colaborador_id, $prorrateo_periodo_id);

    if(!empty($valor)){
      $x = $valor['total_incentivo'];
      return "$ " . $x;
    }else{
      return 'No tiene incentivos asignados';
    }
  }

  public function getValoresNoHorasExtra($colaborador_id, $id_periodo){
    $data = new \stdClass();
    $data->_catalogo_colaboradores_id = $colaborador_id;
    $data->_prorrateo_periodo_id = $id_periodo;
    $result = ResumenesDao::getValoresNoHorasExtra($data);
    return (!empty($result)) ? $result['horas_extra'] : 0;
  }

  public function getValoresDomingos($colaborador_id, $id_periodo){
    $data = new \stdClass();
    $data->_catalogo_colaboradores_id = $colaborador_id;
    $data->_prorrateo_periodo_id = $id_periodo;
    $result = ResumenesDao::getDomingoProcesosLaborado($data);
    return (!empty($result)) ? '$ ' .$result : '$ ' . 0;
  }

  public function setTablaExistente($idPeriodo, $tipoPeriodo, $perfil_id, $catalogo_planta_id, $catalogo_departamento_id, $catalodo_planta_nombre, $accion, $post){

    /*******************************CREACION DEL FILTRO PARA LOS COLABORADORES*********************************/
    $this->getMenuBuscador($post);
    $where = '';
    if($post['catalogo_empresa_id'] != ''){
      $where .= ' AND c.catalogo_empresa_id = '.$post['catalogo_empresa_id'];
    }

    if($post['catalogo_ubicacion_id'] != ''){
      $where .= ' AND c.catalogo_ubicacion_id = '.$post['catalogo_ubicacion_id'];
    }

    if($post['catalogo_departamento_id'] != ''){
      $where .= ' AND c.catalogo_departamento_id = '.$post['catalogo_departamento_id'];
    }

    if($post['catalogo_puesto_id'] != ''){
      $where .= ' AND c.catalogo_puesto_id = '.$post['catalogo_puesto_id'];
    }

    if($post['status'] != ''){
      $where .= ' AND c.identificador_noi = "'.$post['status'].'"';
    }
    /*******************************CREACION DEL FILTRO PARA LOS COLABORADORES*********************************/


    $periodo = ResumenesDao::getPeriodoById($idPeriodo);
    $hidden = ($periodo['status'] == 0)? '': ' hidden ';
    $fecha_inicio = new \DateTime($periodo['fecha_inicio']);
    $fecha_fin = new \DateTime($periodo['fecha_fin']);
    $convertir_registro = array( 0 => "A", -1 => "F", -11 => "FF" , -2 => "R" , -22 => "FR" , -23 => "DF" , -24 => "AA" , -25 => "RDF" , -26 => "FRDF" );

    $datos = new \stdClass();
    $datos->tipo = ucwords(strtolower($periodo['tipo']));
    $datos->periodo_id = $idPeriodo;
    $encabezado =<<<html
        <th>No. Empleado</th>
        <th>Nombre</th>
        <th>Informacion</th>
html;
    $fechaTest = new \DateTime($periodo['fecha_inicio']);
   
    while ($fechaTest <=$fecha_fin) {
      $encabezado .= "<td>".$fechaTest->format('Y-m-d')."</td>";  
      $fechaTest->add(new \DateInterval('P1D'));
    }

    foreach(ResumenesDao::getAllColaboradores($perfil_id, $tipoPeriodo, $catalogo_planta_id, $catalogo_departamento_id, $catalodo_planta_nombre, $accion, $where) as $key => $value){  
/******************************QUITAR PARA EL FUNCIONAMIENTO DE LA FUNCION**************************************
      if($value['catalogo_colaboradores_id'] != 316){
        continue;
      }
/******************************QUITAR PARA EL FUNCIONAMIENTO DE LA FUNCION**************************************/
      $nombre_planta = strtolower($value['nombre_planta']);
      $nombreEmpleado = utf8_encode($value['nombre'])."<br />".utf8_encode($value['apellido_paterno'])."<br />".utf8_encode($value['apellido_materno']);
      $tabla .=<<<html
        <tr>
          <td>{$value['numero_empleado']}</td>
          <td>$nombreEmpleado</td>
          <td>
            <b>Departamento</b> {$value['nombre_departamento']} <br>
            <b>Identificador</b> {$value['nombre_planta']} <br>
          </td>
html;
        $fecha_inicio = new \DateTime($periodo['fecha_inicio']);
        $datos->catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];
        $registro = ResumenesDao::getRegistro($datos);

        $fechaTest = new \DateTime($periodo['fecha_inicio']);
        while ($fechaTest <=$fecha_fin) {
          $llegada = '';
          foreach ($registro as $keyRegistro => $valueRegistro) {
            if($valueRegistro['fecha'] == $fechaTest->format('Y-m-d')){
              $llegada = $convertir_registro[$valueRegistro['estatus']];
              if($llegada == ''){
                $incidencia = ResumenesDao:: getIncidenciaById($valueRegistro['estatus']);
                $llegada = $incidencia['identificador_incidencia'];
                $color = $incidencia['color'];
              }
              //echo $valueRegistro['estatus'].'<br>';
            }
          }

          if($llegada == ''){
            $llegada = 'D';
          }

          if ($llegada == 'A' || $llegada =='AA'){$color = 'green';}
          elseif($llegada == 'D' || $llegada == 'DF'){$color = 'gray';}
          elseif($llegada == 'R'){$color = 'yellow';}
          elseif ($llegada == 'FF' || $llegada == 'F'){$color = 'red';}
          elseif ($llegada == 'FR'){$color = 'orange';}

          if($fechaTest >= new \DateTime()){
              $tabla .=<<<html
                <td style="text-align: center; vertical-align: middle; font-size: 18px;" bgcolor="#f1f1f1"><span><label style="color: {$color};"> --- </label></span></td>
html;
          }else{
            $tabla .=<<<html
              <td style="text-align: center; vertical-align: middle; font-size: 18px;" bgcolor="#f1f1f1"><span><label style="color: {$color};"> {$llegada}</label></span></td>
html;
        }
            $fechaTest->add(new \DateInterval('P1D'));
        }

        $tabla.=<<<html
        </tr>
html;
  }
    View::set('tbody',$tabla);
    View::set('thead',$encabezado);
  }

  public function verificarHorario($colaboradorId, $periodoId){

    $colaborador = ResumenesDao::getById($colaboradorId);
    $horariosAsignados = ResumenesDao::getHorariosColaborador($colaboradorId);
    echo "<pre>";print_r($colaborador);echo "</pre>";

    $horarios = "";
    foreach ($horariosAsignados as $key => $value) {
      $dias = $this->getDiasHorario($colaboradorId, $value['catalogo_horario_id']);
      $horarios = <<<html
      <ul>
        <li>
          <div class="alert " role="alert">
            <span class="sr-only">Dias:</span>
            <p><b> Horario : {$value['nombre']} - Entrada: {$value['hora_entrada']} - Salida {$value['hora_salida']} - Tolerancia: {$value['tolerancia_entrada']} - M&aacute;ximo Retardos: {$value['numero_retardos']} </b></p>
            <span class="glyphicon glyphicon-cloud" aria-hidden="true"> </span> $dias
          </div>
        </li>
      </ul>
html;
    }

    $periodo = ResumenesDao::getPeriodoById($periodoId);

    View::set('fechaPeriodo', MasterDom::getFecha($periodo['fecha_inicio']) . " - " . MasterDom::getFecha($periodo['fecha_fin']));
    View::set('horarios',$horarios);
    View::set('colaborador',$colaborador);
    View::render('verificar_horario');
  }

  public function getDiasHorario($colaboradorId, $catalogoHorario){
    $html = "";
    foreach (ResumenesDao::getDiasLaboralesColaborador($colaboradorId, $catalogoHorario) as $key => $value) {
      $html .=<<<html
          <span>{$value['nombre']}</span>
html;
    }
    return $html;
  }

  public function checadorFechas($idColaborador,$idPeriodo,$vista,$accion){
    $colaborador = IncidenciaDao::getById($idColaborador);
    $periodo = IncidenciaDao::getPeriodoById($idPeriodo);
    $colaborador_id = $idColaborador;

    $estatusPeriodo = $periodo['status'];
    $fecha_inicio = new \DateTime($periodo['fecha_inicio']);
    $fecha_final = new \DateTime($periodo['fecha_fin']);
    $dias = array('Monday'=>'Lunes','Tuesday'=>'Martes','Wednesday'=>'Miercoles','Thursday'=>'Jueves','Friday'=>'Viernes','Saturday'=>'Sabado','Sunday'=>'Domingo');

    $incidencias_colaborador = IncidenciaDao::getProrrateoColaboradorIncidenciaById($colaborador['catalogo_colaboradores_id']);
    $datos = new \stdClass();
    $datos->numero_empleado = $colaborador['numero_identificador'];
    $datos->catalogo_colaboradores_id = $colaborador['catalogo_colaboradores_id'];    
    $datos->catalogo_lector_id = $colaborador['catalogo_lector_id'];
    if(MasterDom::getData('secundario') == 'true')
	  $datos->catalogo_lector_id = $colaborador['catalogo_lector_secundario_id'];

    $tabla =<<<html
      <table class="table table-striped table-bordered table-hover" id="muestra-colaboradores">
        <thead>
          <tr>
            <th>Dia</th>
            <th>Fecha</th>
            <th>Entrada</th>
            <th>Salida</th>
            <th>Entrada Registrada</th>
            <th>Salida Registrada</th>
            <th>Comentario</th>
            <th>Incidencia</th>
          </tr>
        </thead>
        <tbody id="registros">
html;

        $dia_aux = '';
        $ultimo_horario = IncidenciaDao::getUltimoHorario($datos);
        $horarios = IncidenciaDao::getHorariosById($datos);
        $num_semana = 0;

      while($fecha_inicio <= $fecha_final){
        
        $value = '';

        /******************************************************************************************************************************************************/
        if($colaborador['horario_tipo'] == 'semanal'){
          $nombre_dia_semana = $dias[$fecha_inicio->format('l')];
            if( $num_semana != $fecha_inicio->format('W') ){
                $num_semana = $fecha_inicio->format('W');
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
                      if($ultimo_horario == ''){
                        $catalogo_horario_id = $horarios[0]['catalogo_horario_id'];
                      }elseif($catalogo_horario_id == 0){
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
            
        }
        $datos->catalogo_horario_id = $catalogo_horario_id;

        //echo 'Horario :'.$catalogo_horario_id.'<br>';
        /******************************************************************************************************************************************************/

        $horario_laboral = IncidenciaDao::getHorarioLaboralById($datos);
        foreach ($horario_laboral as $llave => $valor) {
          if($valor['dia_semana'] == $dias[$fecha_inicio->format('l')]){
            if($colaborador['horario_tipo'] == 'semanal'){
              $value = $valor;
              break;
            }elseif ($colaborador['horario_tipo'] == 'diario') {
              if($valor['dia_semana'] == $dias[$fecha_inicio->format('l')]){
                $value = $valor;
                break;
              }
            }
          }
        }
        

        /***********************************SI NO ES DIA DE TRABAJO VERIFICA QUE NO HAYA REGISTROS DE ASISTENCIA*********/
              if($valor['dia_semana'] != ''){
                

                if(preg_match("/nocturno/", strtolower($value['horario']))){
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
                    $registro_entrada_array = IncidenciaDao::getAsistenciaModificada($datos);

                  }else{
                    $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' 00:00';
                    $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' 23:59';
                    $registro_entrada_array = IncidenciaDao::getAsistenciaModificada($datos);         
                  }

                
              }else{
                  $nueva_fecha = new \DateTime($fecha_inicio->format('Y-m-d').' '.$value['hora_entrada']);
                  $nueva_fecha->modify('-2 hours');
                  //$fecha_inicio->modify('+0 minute');
                  //$fecha_inicio->modify('-0 second');
                  $datos->fecha_inicio = $nueva_fecha->format('Y-m-d H:i:s'); 
                  $fecha_aux = new \DateTime($fecha_inicio->format('Y-m-d'));
                  
                  if(preg_match("/nocturno/", strtolower($value['horario']))){
                    $fecha_aux->add(new \DateInterval('P1D'));
                    $nueva_fecha= $fecha_aux->format('Y-m-d').' '.$value['hora_salida'];
                    $fecha_aux = new \DateTime($nueva_fecha);      
                    $fecha_aux->add(new \DateInterval('PT4H0S'));
                  }else{
                    $nueva_fecha= $fecha_inicio->format('Y-m-d').' '.$value['hora_salida'];
                    $fecha_aux = new \DateTime($nueva_fecha);      
                    $fecha_aux->add(new \DateInterval('PT4H0S'));
                    //$fecha_aux->add(new \DateInterval('P0Y0M0DT2H0M0'));
                  }

                $datos->fecha_fin = $fecha_aux->format('Y-m-d H:i:s');
                $registro_entrada_array = IncidenciaDao::getAsistenciaModificada($datos);
              }
          /***************************************************************************************************************/
/*
    //$bandera_dias_descanso = true;
    $nueva_fecha = new \DateTime($fecha_inicio->format('Y-m-d').' '.$value['hora_entrada']);
    $nueva_fecha->modify('-2 hours');
    //$fecha_inicio->modify('+0 minute');
    //$fecha_inicio->modify('-0 second');

    $datos->fecha_inicio = $nueva_fecha->format('Y-m-d H:i:s');

    $fecha_aux = new \DateTime($fecha_inicio->format('Y-m-d'));
    if(preg_match("/nocturno/", strtolower($value['horario']))){
      $fecha_aux->add(new \DateInterval('P1D'));  
      $nueva_fecha= $fecha_aux->format('Y-m-d').' '.$value['hora_salida'];
      $fecha_aux = new \DateTime($nueva_fecha);      
      $fecha_aux->add(new \DateInterval('PT4H0S'));
    }else{
      $nueva_fecha= $fecha_inicio->format('Y-m-d').' '.$value['hora_salida'];
      $fecha_aux = new \DateTime($nueva_fecha);      
      $fecha_aux->add(new \DateInterval('PT4H0S'));
      //$fecha_aux->add(new \DateInterval('P0Y0M0DT2H0M0'));
    }
    $datos->fecha_fin = $fecha_aux->format('Y-m-d H:i:s');
    $registro_entrada_array = IncidenciaDao::getAsistenciaModificada($datos);
*/
    if(count($registro_entrada_array) >= 1){
      $registro_entrada = array_shift($registro_entrada_array);
      if($registro_entrada['date_check'] != ''){
        $registro_entrada = $registro_entrada['date_check'];
      }else{
        $registro_entrada = 'Sin registro';
      }

      if(count($registro_entrada_array)>=1){
        $registro_salida = array_pop($registro_entrada_array); //$registro_entrada_array[count($registro_entrada_array) -1 ];
        if($registro_salida['date_check'] != '')
          $registro_salida = $registro_salida['date_check'];
        else
          $registro_salida = 'Sin registro';

        }else{
        $registro_salida = 'Sin registro';
        }
    }else{
      $registro_entrada = 'Sin registro';
      $registro_salida = 'Sin registro';
    }

        $colaborador_id = MasterDom::getData('catalogo_colaboradores_id');

        $incidencia = '';
        
        foreach ($incidencias_colaborador as $llave => $valor) {
      if( $fecha_inicio->format('Y-m-d') === $valor['fecha_incidencia']){
        $incidencia = $valor['nombre'];
        $comentario = $valor['comentario'];
        break;
      }
    }

    // COLOR PARA UN DIA FESTIVO
    $diaFestivo = IncidenciaDao::getDiaFestivo($fecha_inicio->format('Y-m-d'));
    $colorDiaFestivo = (!empty($diaFestivo)) ? "#b9e6ff;" : "";

    // COLOR PARA CHECAR CUANDO EL DIA NO ES LABORADO
    /*$x_hora_entrada = ($value['hora_entrada'] != "" ) ? $value['hora_entrada'] : "No Laboral";
    $x_hora_salida = ($value['hora_salida'] != "" ) ? $value['hora_salida'] : "No Laboral";
    $x_registro_entrada = ($value['hora_entrada'] != "" ) ? $registro_entrada : "";
    $x_registro_salida = ($value['hora_entrada'] != "" ) ? $registro_salida : "";*/

    // COLOR PARA CHECAR CUANDO EL DIA NO ES LABORADO
    $x_hora_entrada = ($value['hora_entrada'] != "" ) ? $value['hora_entrada'] : "No Laboral";
    $x_hora_salida = ($value['hora_salida'] != "" ) ? $value['hora_salida'] : "No Laboral";
    $x_registro_entrada = $registro_entrada; //($value['hora_entrada'] != "" ) ? $registro_entrada : "";
    $x_registro_salida = $registro_salida; //($value['hora_entrada'] != "" ) ? $registro_salida : "";

    if($x_hora_entrada == "No Laboral" && $x_hora_salida == "No Laboral" && $x_registro_entrada == "Sin registro"){
      $x_registro_entrada = "";
    }

    if($x_hora_entrada == "No Laboral" && $x_hora_salida == "No Laboral" && $x_registro_salida == "Sin registro"){
      $x_registro_salida = "";
    }

    $tabla .=<<<html
      <tr>
        <td style="background:{$colorDiaFestivo} ;">{$dias[$fecha_inicio->format('l')]}</td>
        <td style="background:{$colorDiaFestivo} ;">{$fecha_inicio->format('Y-m-d')}</td>
        <td style="background:{$colorDiaFestivo} ;">{$x_hora_entrada}</td>
        <td style="background:{$colorDiaFestivo} ;">{$x_hora_salida}</td>
        <td style="background:{$colorDiaFestivo} ;">{$x_registro_entrada}</td>
        <td style="background:{$colorDiaFestivo} ;">{$x_registro_salida}</td>
        <td style="background:{$colorDiaFestivo} ;">
html;
    foreach ($incidencias_colaborador as $llave => $valor) {
      if( $fecha_inicio->format('Y-m-d') === $valor['fecha_incidencia']){
        $tabla .= $valor['comentario'];
        break;
      }
    }
        
    $tabla.=<<<html
      </td>
      <td style="background:{$colorDiaFestivo} ">
        <label>{$incidencia}</label>
      </td>
    </tr>
html;
            

        $fecha_inicio->add(new \DateInterval('P1D'));
      }//fin del while rango de fechas
      
      $tabla .=<<<html
      </tbody>
    </table>
html;

      $colaborador = IncidenciaDao::getById($idColaborador);
      $tipoPeriodo = ($vista == "semanales") ? "SEMANAL" : "QUINCENAL";

      $colaborador = ResumenesDao::getById($idColaborador);
      $horariosAsignados = ResumenesDao::getHorariosColaborador($idColaborador);
    

      $horarios = "";
      foreach ($horariosAsignados as $key => $value) {
        $dias = $this->getDiasHorario($idColaborador, $value['catalogo_horario_id']);
        $horarios = <<<html
          <ul>
            <li>
              <div class="alert " role="alert">
                <span class="sr-only">Dias:</span>
                <p><b> Horario : {$value['nombre']} - Entrada: {$value['hora_entrada']} - Salida {$value['hora_salida']} - Tolerancia: {$value['tolerancia_entrada']} - M&aacute;ximo Retardos: {$value['numero_retardos']} </b></p>
                <span class="glyphicon glyphicon-cloud" aria-hidden="true"> </span> $dias
              </div>
            </li>
          </ul>
html;
    }

      $seccion = "";
      if($colaborador['pago'] == "Semanal"){
        $seccion = "semanales";
      }elseif($colaborador['pago'] == "Quincenal"){
        $seccion = "quincenales";
      }

      $irIncidencias = <<<html
      <a target="_blank" class="btn btn-success" href="/Incidencia/checadorFechas/{$idColaborador}/{$idPeriodo}/{$seccion}/"> IR A INCIDENCIA DE {$colaborador['nombre']} {$colaborador['apellido_paterno']} {$colaborador['apellido_materno']} </a>
html;

      View::set('irIncidencias',$irIncidencias);
      View::set('horarios',$horarios);
      View::set('msjPeriodo', MasterDom::getFecha($periodo['fecha_inicio']) . " - " . MasterDom::getFecha($periodo['fecha_fin']));
      View::set('tipoPeriodo',$tipoPeriodo); // Identificacion del periodo
      View::set('colaborador',$colaborador);
      View::set('tabla',$tabla);
      View::set('header',$this->_contenedor->header());
      View::set('footer',$this->_contenedor->footer());
      View::render('verificar_horario');
    }



  public function propiosSemanales(){

    $user = GeneralDao::getDatosUsuario($this->__usuario);

    if($user['perfil_id'] == 6){ // Si el usuario es de RH
      $tituloVista = "Incidencias de Recursos Humanos - Planta " . strtolower($user['nombre_planta']) . " - Depto. " . $user['nombre'];
      //$val = 3; // TIENE INCENTIVOS PROPIOS
      $val = ($user['catalogo_planta_id'] == 1) ? 1 : 1;
    }

    if($user['perfil_id'] == 1){ // Si el usuario es de RH
      $tituloVista = "Incidencias de ROOT - Planta " . strtolower($user['nombre_planta']) . " - Depto. " . $user['nombre'];
      $val = 3; // TIENE INCENTIVOS PROPIOS
    }

    $user = GeneralDao::getDatosUsuario($this->__usuario);
    $periodo_id = $this->getUltimoPeriodo("SEMANAL", 0); // Obtiene el ultimo periodo Abierto
    View::set('msjPeriodo',$this->getPeriodo("Al parecer no hay periodo Abierto", "Debe existir un periodo Abierto, para checar los incentivos","SEMANAL", 0,$periodo_id)); // Obtiene el periodo de la incidencia
    /*if($user['perfil_id'] == 1){
      $accion = 4; // ESTA ES PARA CUANDO EL USUARIO SEA ROOT 
    }elseif($user['perfil_id'] == 6){
      $accion = 1;
    }*/

    echo $this->setTabla($periodo_id, "Semanal", $user['perfil_id'], $user['catalogo_planta_id'], $user['catalogo_departamento_id'], $user['nombre_planta'], $val, $_POST);
    //$extraFooter = $this->getFooterExtra();
    $extraFooter = $this->getFooterTable();

    View::set('visible_admin', "hidden");
    View::set('tipo_periodo', 'semanal');
    View::set('periodo_id', $periodo_id);
    //$this->getTabla("Semanal",$idPeriodo,"propiosSemanales",$user['perfil_id'], $user['catalogo_departamento_id'], $user['catalogo_planta_id'], $val, $user['nombre_planta']);
    View::set('header',$this->_contenedor->header($this->getHeader()));
    View::set('footer',$this->_contenedor->footer($extraFooter));
    View::render('resumenes_abiertos');
  }

  public function propiosQuincenales(){
    $user = GeneralDao::getDatosUsuario($this->__usuario);
    $periodo_id = $this->getUltimoPeriodo("QUINCENAL", 0); // Obtiene el ultimo periodo Abierto
    View::set('msjPeriodo',$this->getPeriodo("Al parecer no hay periodo Abierto", "Debe existir un periodo Abierto, para checar los incentivos","QUINCENAL", 0,$periodo_id)); // Obtiene el periodo de la incidencia
    if($user['perfil_id'] == 1){
      $accion = 3; // ESTA ES PARA CUANDO EL USUARIO SEA ROOT 
    }
    if($user['perfil_id'] == 6){
      $accion = 1;
    }
    $this->setTabla($periodo_id, "Quincenal", $user['perfil_id'], $user['catalogo_planta_id'], $user['catalogo_departamento_id'], $user['nombre_planta'], $accion, $_POST); 
    $extraFooter = $this->getFooterTable();

    View::set('visible_admin', "hidden");
    View::set('tipo_periodo', 'quincenal');
    View::set('periodo_id', $periodo_id);
    View::set('header',$this->_contenedor->header($this->getHeader()));
    View::set('footer',$this->_contenedor->footer($extraFooter));
    View::render('resumenes_abiertos');
  }

  public function propiosSemanalesHistoricos(){
    $user = GeneralDao::getDatosUsuario($this->__usuario);

    if(empty($_POST)){
      $ultimoPeriodoHistorico = IncentivoDao::getUltimoPeriodoHistorico("SEMANAL");
      $idPeriodo = $ultimoPeriodoHistorico['prorrateo_periodo_id'];
      View::set('msjPeriodo',$this->getPeriodo("Al parecer no hay ningun periodo Cerrado", "Debe existir un periodo Cerrado, para mostrar los registros","SEMANAL", 1)); 
    }else{
      $idPeriodo = MasterDom::getData('tipo_periodo');
      View::set('msjPeriodo',$this->getPeriodoProcesado("SEMANAL", $idPeriodo)); 
    }

    if($user['perfil_id'] == 1)
      $accion = 3; // ESTA ES PARA CUANDO EL USUARIO SEA ROOT 
    if($user['perfil_id'] == 6){
      $accion = 1;
      /*
      if($user['catalogo_planta_id'] == 1){
        $accion = 1;
      }else{
        $accion = 3;
      }
      */
    }

 
    echo $this->setTablaExistente($idPeriodo, "Semanal", $user['perfil_id'], $user['catalogo_planta_id'], $user['catalogo_departamento_id'], $user['nombre_planta'], $accion, $_POST); 

    //$extraFooter = $this->getFooterExtra();
    $extraFooter = $this->getFooterTable();
    View::set('busqueda',"/Resumenes/propiosSemanalesHistoricos/");
    View::set('option',$this->getPeriodosHistoricos("Al parecer no hay ningun periodo Cerrado", "Debe existir un periodo Cerrado, para mostrar los registros","SEMANAL",$idPeriodo)); // Optiene todos los periodos procesados(historicos) semanales
    View::set('header',$this->_contenedor->header($this->getHeader()));
    View::set('footer',$this->_contenedor->footer($extraFooter));
    View::render("resumenes_historicos");
  }

  public function propiosQuincenalesHistoricos(){
    $user = GeneralDao::getDatosUsuario($this->__usuario);
    if(empty($_POST)){
      $ultimoPeriodoHistorico = IncentivoDao::getUltimoPeriodoHistorico("QUINCENAL");
      $idPeriodo = $ultimoPeriodoHistorico['prorrateo_periodo_id'];
      View::set('msjPeriodo',$this->getPeriodo("Al parecer no hay ningun periodo Cerrado", "Debe existir un periodo Cerrado, para mostrar los registros","QUINCENAL", 1)); 
    }else{
      $idPeriodo = MasterDom::getData('tipo_periodo');
      View::set('msjPeriodo',$this->getPeriodoProcesado("QUINCENAL", $idPeriodo)); 
    }

    if($user['perfil_id'] == 1)
      $accion = 1; // ESTA ES PARA CUANDO EL USUARIO SEA ROOT 
    if($user['perfil_id'] == 6){
      $accion = 1;
      /*
      if($user['catalogo_planta_id'] == 1){
        $accion = 1;
      }else{
        $accion = 3;
      }
      */
    }else{
      $accion = 3;
    }

    echo $this->setTablaExistente($idPeriodo, "Quincenal", $user['perfil_id'], $user['catalogo_planta_id'], $user['catalogo_departamento_id'], $user['nombre_planta'], $accion, $_POST); 

    //$extraFooter = $this->getFooter();
    $extraFooter = $this->getFooterTable();
    View::set('busqueda',"/Resumenes/propiosQuincenalesHistoricos/");
    View::set('option',$this->getPeriodosHistoricos("QUINCENAL",$idPeriodo)); // Optiene todos los periodos procesados(historicos) semanales
    View::set('header',$this->_contenedor->header($this->getHeader()));
    View::set('footer',$this->_contenedor->footer($extraFooter));
    View::render("resumenes_historicos");
  }

  public function semanales(){
    $user = GeneralDao::getDatosUsuario($this->__usuario);
    if(count($user) == 0){
      $user = GeneralDao::getDatosUsuarioLogeado($this->__usuario);
    }
    $periodo_id = $this->getUltimoPeriodo("SEMANAL", 0); // Obtiene el ultimo periodo Abierto
    /*tabla*/
    View::set('msjPeriodo',$this->getPeriodo("Al parecer no hay periodo Abierto", "Debe existir un periodo Abierto, para checar los incentivos","SEMANAL", 0, $periodo_id, $user['perfil_id'], $user['catalogo_planta_id'])); // Obtiene el periodo de la incidencia
    if($user['perfil_id'] == 1){
      $accion = 2; // ESTA ES PARA CUANDO EL USUARIO SEA ROOT 
    }

    if($user['perfil_id'] == 6){
      if($user['catalogo_planta_id'] == 1){
        $accion = 2;//1
      }else{
        if($user['perfil_id'] == 1){
          $accion = 2;
        }else{  
          View::set('visible_admin', "hidden");
          $accion = 3;
        }
      }
      //$accion = 2; // ESTA ES PARA CUANDO EL USUARIO SEA RH
    }else{
      if($user['perfil_id'] == 1){
          $accion = 2;
        }else{  
          View::set('visible_admin', "hidden");
          $accion = 3;
        }      
    }
    
    echo $this->setTabla($periodo_id, "Semanal", $user['perfil_id'], $user['catalogo_planta_id'], $user['catalogo_departamento_id'], $user['nombre_planta'], $accion, $_POST);

    //$extraFooter = $this->getFooterExtra();
    $extraFooter = $this->getFooterTable();

    
    View::set('tipo_periodo', 'semanal');
    View::set('periodo_id', $periodo_id);
    View::set('header',$this->_contenedor->header($this->getHeader()));
    View::set('footer',$this->_contenedor->footer($extraFooter));
    View::render('resumenes_abiertos');
  }

  public function quincenales(){
      
    $user = GeneralDao::getDatosUsuario($this->__usuario);
    if(count($user) == 0){
      $user = GeneralDao::getDatosUsuarioLogeado($this->__usuario);
    }
    
    $periodo_id = $this->getUltimoPeriodo("QUINCENAL", 0); // Obtiene el ultimo periodo Abierto
    View::set('msjPeriodo',$this->getPeriodo("Al parecer no hay periodo Abierto", "Debe existir un periodo Abierto, para checar los incentivos","QUINCENAL", 0,$periodo_id, $user['perfil_id'], $user['catalogo_planta_id'])); // Obtiene el periodo de la incidencia
    
    if($user['perfil_id'] == 1)
      $accion = 2; // ESTA ES PARA CUANDO EL USUARIO SEA ROOT 
    if($user['perfil_id'] == 6){
      if($user['catalogo_planta_id'] == 1){
        $accion = 2;//1
      }else{
        if($user['perfil_id'] == 1){
          $accion = 2;
        }else{  
          View::set('visible_admin', "hidden");
          $accion = 3;
        }
      }
    }else{
      if($user['perfil_id'] == 1){
          $accion = 2;
        }else{  
          View::set('visible_admin', "hidden");
          $accion = 3;
        }
    }
    
    echo $this->setTabla($periodo_id, "Quincenal", $user['perfil_id'], $user['catalogo_planta_id'], $user['catalogo_departamento_id'], $user['nombre_planta'], $accion, $_POST); 

    $extraFooter = $this->getFooterTable();

    View::set('tipo_periodo', 'quincenal');
    View::set('periodo_id', $periodo_id);
    View::set('header',$this->_contenedor->header($this->getHeader()));
    View::set('footer',$this->_contenedor->footer($extraFooter));
    View::render('resumenes_abiertos');
  }

  /*semanal*/
  public function historicosSemanales(){
    $user = GeneralDao::getDatosUsuario($this->__usuario);

    if(empty($_POST)){
      $ultimoPeriodoHistorico = IncentivoDao::getUltimoPeriodoHistorico("SEMANAL");
      $idPeriodo = $ultimoPeriodoHistorico['prorrateo_periodo_id'];
      View::set('msjPeriodo',$this->getPeriodo("Al parecer no hay ningun periodo Cerrado", "Debe existir un periodo Cerrado, para mostrar los registros","SEMANAL", 1)); 
    }else{
      $idPeriodo = MasterDom::getData('tipo_periodo');
      View::set('msjPeriodo',$this->getPeriodoProcesado("SEMANAL", $idPeriodo)); 
    }

    if($user['perfil_id'] == 1)
      $accion = 2; // ESTA ES PARA CUANDO EL USUARIO SEA ROOT 
    if($user['perfil_id'] == 6){
      if($user['catalogo_planta_id'] == 1){
        $accion = 2;//1
      }else{
        $accion = 3;
      }
    }
 
    echo $this->setTablaExistente($idPeriodo, "Semanal", $user['perfil_id'], $user['catalogo_planta_id'], $user['catalogo_departamento_id'], $user['nombre_planta'], $accion, $_POST); 

    //$extraFooter = $this->getFooter();
    $extraFooter = $this->getFooterTable();

    View::set('busqueda',"/Resumenes/historicosSemanales/");
    View::set('option',$this->getPeriodosHistoricos("SEMANAL",$idPeriodo)); // Optiene todos los periodos procesados(historicos) semanales
    View::set('header',$this->_contenedor->header($this->getHeader()));
    View::set('footer',$this->_contenedor->footer($extraFooter));
    View::render("resumenes_historicos");
  }

  public function historicosQuincenales(){
    $user = GeneralDao::getDatosUsuario($this->__usuario);

    if(empty($_POST)){
      $ultimoPeriodoHistorico = IncentivoDao::getUltimoPeriodoHistorico("QUINCENAL");
      $idPeriodo = $ultimoPeriodoHistorico['prorrateo_periodo_id'];
      View::set('msjPeriodo',$this->getPeriodo("Al parecer no hay ningun periodo Cerrado", "Debe existir un periodo Cerrado, para mostrar los registros","QUINCENAL", 1)); 
    }else{
      $idPeriodo = MasterDom::getData('tipo_periodo');
      View::set('msjPeriodo',$this->getPeriodoProcesado("QUINCENAL", $idPeriodo)); 
    }

    if($user['perfil_id'] == 1)
      $accion = 2; // ESTA ES PARA CUANDO EL USUARIO SEA ROOT 
    if($user['perfil_id'] == 6){
      if($user['catalogo_planta_id'] == 1){
        $accion = 2;//1
      }else{
        $accion = 3;
      }
    }
 
    echo $this->setTabla($idPeriodo, "Quincenal", $user['perfil_id'], $user['catalogo_planta_id'], $user['catalogo_departamento_id'], $user['nombre_planta'], $accion /*propiosSemanales*/); 

    $extraFooter = $this->getFooterTable();

    View::set('busqueda',"/Resumenes/historicosQuincenales/");
    View::set('option',$this->getPeriodosHistoricos("QUINCENAL",$idPeriodo)); // Optiene todos los periodos procesados(historicos) semanales
    View::set('header',$this->_contenedor->header($this->getHeader()));
    View::set('footer',$this->_contenedor->footer($extraFooter));
    View::render("resumenes_historicos");
  }

  public function semanalesT(){
      $admin = ResumenesDao::getDatosUsuarioLogeado(MasterDom::getSesion('usuario'));
      if($admin['perfil_id'] !=6 || $admin['catalogo_planta_id'] != 1){
        $visible_admin = 'hidden';

        if($admin['perfil_id'] == 6){
          $catalogo_planta_id = $admin['catalogo_planta_id'];
        }else{
          $catalogo_planta_id = $admin['catalogo_planta_id'];
          $catalogo_departamento_id = $admin['catalogo_departamento_id'];
        } 
      }

      $periodo_id = $this->getUltimoPeriodo("SEMANAL", 0); // Obtiene el ultimo periodo Abiert
      if($periodo_id != ''){
        echo $this->setTablaRangoFechas($periodo_id, "Semanal"); // Coloca la tabla con el periodo abierto
      }
      
      View::set('tipoPeriodo',"Semanales"); // Identificacion del periodo
      View::set('msjPeriodo',$this->getPeriodo("SEMANAL", 0,$periodo_id)); // Obtiene el periodo de la incidencia
      View::set('periodo_id',$periodo_id); // Obtiene el periodo de la incidencia
      View::set('mensaje',$this->getPeriodoTexto("SEMANAL", 0)); // Obtiene el periodo de la incidencia
      $extraFooter = $this->getFooterExtra();
      //$extraFooter .= $this->getFooter();

      View::set('visible_admin', $visible_admin);
      View::set('tipo_periodo', 'semanal');
      View::set('periodo_id', $periodo_id);
      View::set('header',$this->_contenedor->header($this->getHeader()));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render('resumenes_abiertos');
  }

  public function semanalPropio(){
      $admin = ResumenesDao::getDatosUsuarioLogeado(MasterDom::getSesion('usuario'));
      $periodo_id = $this->getUltimoPeriodo("SEMANAL", 0); // Obtiene el ultimo periodo Abiert
      if($periodo_id != ''){
        echo $this->setTablaRangoFechas($periodo_id, "Semanal", $admin['catalogo_departamento_id']);
      }
      
      View::set('tipoPeriodo',"Semanales"); // Identificacion del periodo
      View::set('msjPeriodo',$this->getPeriodo("Al parecer no hay periodo Abierto", "Debe existir un periodo Abierto, para checar los incentivos","SEMANAL", 0,$periodo_id)); // Obtiene el periodo de la incidencia
      View::set('periodo_id',$periodo_id); // Obtiene el periodo de la incidencia
      View::set('mensaje',$this->getPeriodoTexto("SEMANAL", 0)); // Obtiene el periodo de la incidencia
      $extraFooter = $this->getFooterExtra();
      //$extraFooter .= $this->getFooter();

      View::set('visible_admin', 'hidden');
      View::set('tipo_periodo', 'semanal');
      View::set('periodo_id', $periodo_id);
      View::set('header',$this->_contenedor->header($this->getHeader()));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render('resumenes_abiertos');
  }

  public function quincenalPropio(){
      $admin = ResumenesDao::getDatosUsuarioLogeado(MasterDom::getSesion('usuario'));
      $periodo_id = $this->getUltimoPeriodo("QUINCENAL", 0); // Obtiene el ultimo periodo Abiert
      if($periodo_id != ''){
        echo $this->setTablaRangoFechas($periodo_id, "Quincenal", $admin['catalogo_departamento_id']);
      }
      
      View::set('tipoPeriodo',"Quincenales"); // Identificacion del periodo
      View::set('msjPeriodo',$this->getPeriodo("Al parecer no hay periodo Abierto", "Debe existir un periodo Abierto, para checar los incentivos","QUINCENAL", 0,$periodo_id)); // Obtiene el periodo de la incidencia
      View::set('periodo_id',$periodo_id); // Obtiene el periodo de la incidencia
      View::set('mensaje',$this->getPeriodoTexto("QUINCENAL", 0)); // Obtiene el periodo de la incidencia
      $extraFooter = $this->getFooterExtra();
      //$extraFooter .= $this->getFooter();

      View::set('visible_admin', 'hidden');
      View::set('tipo_periodo', 'semanal');
      View::set('periodo_id', $periodo_id);
      View::set('header',$this->_contenedor->header($this->getHeader()));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render('resumenes_abiertos');
  }

  public function quincenalesT(){
    $admin = ResumenesDao::getDatosUsuarioLogeado(MasterDom::getSesion('usuario'));
    $nombre_planta = strtolower($admin['nombre_planta']);
      if($admin['catalogo_planta_id'] != 1 || $admin['perfil_id'] !=6){
        $visible_admin = 'hidden';
      }
      $periodo_id = $this->getUltimoPeriodo("QUINCENAL", 0); // Obtiene el ultimo periodo Abierto
      if($periodo_id != ''){
        echo $this->setTablaRangoFechas($periodo_id, "Quincenal", $nombre_planta); // Coloca la tabla con el periodo abierto
      }
      $extraFooter = $this->getFooterExtra();

      View::set('tipoPeriodo',"Quincenales"); // Identificacion del periodo
      View::set('msjPeriodo',$this->getPeriodo("QUINCENAL", 0,$periodo_id)); // Obtiene el periodo de la incidencia
      View::set('mensaje',$this->getPeriodoTexto("QUINCENAL", 0)); // Obtiene el periodo de la incidencia
      View::set('visible_admin', $visible_admin);
      View::set('tipo_periodo', 'quincenal');
      View::set('periodo_id', $periodo_id);
      View::set('header',$this->_contenedor->header($this->getHeader()));
    View::set('footer',$this->_contenedor->footer($extraFooter));
    View::render("resumenes_abiertos");
  }

  public function historicosSemanalesT(){
      
      if(empty($_POST)){
        $ultimoPeriodoHistorico = ResumenesDao::getUltimoPeriodoHistorico("SEMANAL");
        $periodo_id = $ultimoPeriodoHistorico['prorrateo_periodo_id'];
        View::set('msjPeriodo',$this->getPeriodo("SEMANAL", 1)); // Obtiene el periodo de la incidencia
        echo $this->setTablaRangoFechas($periodo_id, "Semanal"); // Coloca la tabla con el periodo abierto
      }else{
        $periodo_id = MasterDom::getData('tipo_periodo');
        echo $this->setTablaRangoFechas($periodo_id, "Semanal"); // Coloca la tabla con el periodo abier
        View::set('msjPeriodo',$this->getPeriodoProcesado("SEMANAL", $periodo_id)); // Obtiene el periodo de la incidencia
      }
      
      View::set('tipoPeriodo',"Semanal"); // Identificacion del periodo
      View::set('option',$this->getPeriodosHistoricos("SEMANAL",$periodo_id)); // Optiene todos los periodos procesados(historicos) semanales
      View::set('busqueda',"/Resumenes/historicosSemanales/&action=historicosSemanales");
      View::set('header',$this->_contenedor->header($this->getHeader()));
      View::set('footer',$this->_contenedor->footer($this->getFooter()));
      View::render("resumenes_historicos");
    }

  public function historicosQuincenalesT(){
      
      if(empty($_POST)){
        $ultimoPeriodoHistorico = ResumenesDao::getUltimoPeriodoHistorico("QUINCENAL");
        $periodo_id = $ultimoPeriodoHistorico['prorrateo_periodo_id'];
        View::set('msjPeriodo',$this->getPeriodo("QUINCENAL", 1)); // Obtiene el periodo de la incidencia
        echo $this->setTablaRangoFechas($periodo_id, "Quincenal"); // Coloca la tabla con el periodo abierto
      }else{
        $periodo_id = MasterDom::getData('tipo_periodo');
        echo $this->setTablaRangoFechas($periodo_id, "Quincenal"); // Coloca la tabla con el periodo abier
        View::set('msjPeriodo',$this->getPeriodoProcesado("QUINCENAL", $periodo_id)); // Obtiene el periodo de la incidencia
      }
      
      View::set('tipoPeriodo',"Quincenal"); // Identificacion del periodo
      View::set('option',$this->getPeriodosHistoricos("QUINCENAL",$periodo_id)); // Optiene todos los periodos procesados(historicos) semanales
      View::set('busqueda',"/Resumenes/historicosSemanales/&action=historicosSemanales");
      View::set('header',$this->_contenedor->header($this->getHeader()));
      View::set('footer',$this->_contenedor->footer($this->getFooter()));
      View::render("resumenes_historicos");
    }

  public function setTablaRangoFechas($idPeriodo, $tipoPeriodo, $departamento_id, $planta_id){

    $dias_traductor = array('Monday' => 'Lunes','Tuesday' => 'Martes','Wednesday' => 'Miercoles','Thursday' => 'Jueves','Friday' => 'Viernes','Saturday' => 'Sabado','Sunday' => 'Domingo');
    $meses_traductor = array(1 => 'ENE',2 => 'FEB',3 => 'MAR',4 => 'ABR',5 => 'MAY',6 => 'JUN',7 => 'JUL',8 => 'AGO',9 => 'SEP', 10 => 'OCT', 11 => 'NOV', 12 => 'DIC');
    $periodo = ResumenesDao::getPeriodoById($idPeriodo);

    $fecha_fin = new \DateTime($periodo['fecha_fin']);
    $datos = new \stdClass();
    $datos->tipo = ucwords(strtolower($periodo['tipo']));
    $encabezado =<<<html
        <th>No. Empleado</th>
        <th>Nombre</th>
        <th>Apellido Paterno</th>
        <th>Apellido Materno</th>
        <th>Departamento</th>
html;
    $j = 0;
    $administrador = ResumenesDao::getDatosUsuarioLogeado(MasterDom::getSesion('usuario'));
    // $tipoPeriodo = Semanal o quincenal
    $colaboradores = ResumenesDao::getAllColaboradoresPago($tipoPeriodo, $administrador['administrador_id'], $administrador['usuario'], $departamento_id,$planta_id);
    
    foreach($colaboradores as $key => $value){

      if($key <= 10){

        exit;
      }
      $nombre_planta = strtolower($value['nombre_planta']);
      $tabla .=<<<html
        <tr>
          <td>{$value['numero_empleado']}</td>
          <td>{$value['nombre']}</td>
          <td>{$value['apellido_paterno']} </td>
          <td>{$value['apellido_materno']}</td>
          <td>{$value['nombre_departamento']}</td>
html;
        $datos->numero_empleado = $value['numero_empleado'];
        $fecha_inicio = new \DateTime($periodo['fecha_inicio']);
        $i=1;
        $horario_laboral = ResumenesDao::getHorarioLaboral($value['catalogo_colaboradores_id']);
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
                $incidencia = ResumenesDao::getIncidencia($datos);
                
                if(count($incidencia)>0){
                  $llegada = $incidencia[0]['identificador_incidencia'];
                  $color = $incidencia[0]['color'];
                  if($incidencia[0]['genera_falta'] == 1){
                    $llegada = 'FF'; //falta (-1)
                    if(count(ResumenSemanalDao::getDiaFestivo($fecha_inicio->format('Y-m-d'))) >0){
                      $llegada = 'DF'; //dia festivo (-23)
                    }
                  }
                }else{
                  $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' 00:00:00';
                  $fecha_aux = new \DateTime($nueva_fecha).' '.$valor1['hora_entrada'];      
                  $minutos_tolerancia = intval($valor1['tolerancia_entrada'])*60;
                  $fecha_aux->add(new \DateInterval('PT0H'.$minutos_tolerancia.'S'));
                  $datos->fecha_fin = $fecha_aux->format('Y-m-d H:i:s');
                  $registro_entrada = ResumenesDao::getAsistencia($datos, $nombre_planta);
                  if(count($registro_entrada) > 0){
                    $llegada = 'A'; //asistencia (0)
                    if(count(ResumenSemanalDao::getDiaFestivo($fecha_inicio->format('Y-m-d'))) >0){
                      $llegada = 'AA'; //asistencia en dia festivo (-24)
                    }

                  }else{
                    $datos->fecha_inicio = $fecha_inicio->format('Y-m-d').' 00:00:00';
                    $datos->fecha_fin = $fecha_inicio->format('Y-m-d').' 23:59:59';
                    $registro_entrada = ResumenesDao::getAsistencia($datos, $nombre_planta);
                    if(count($registro_entrada) > 0){
                      $llegada = 'R'; //retardo (-2)
                      if(count(ResumenSemanalDao::getDiaFestivo($fecha_inicio->format('Y-m-d'))) >0){
                        $llegada .= 'RDF'; //retardo en dia festivo (-25) 
                      }
                      $contadorRetardos[$valor1['catalogo_horario_id']] += 1;
                    }else{
                      $llegada = 'FF'; // falta (-1)
                      if(count(ResumenSemanalDao::getDiaFestivo($fecha_inicio->format('Y-m-d'))) >0){
                        $llegada = 'DF'; // dia festivo (-23)
                      }
                    }

                    if( $contadorRetardos[$valor1['catalogo_horario_id']] == $valor1['numero_retardos'] ){
                      $llegada = 'FR'; // falta por retardos (-22)
                      if(count(ResumenSemanalDao::getDiaFestivo($fecha_inicio->format('Y-m-d'))) >0){
                        $llegada .= 'DF'; // dia festivo (-23)
                      }
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
          $incidencia = ResumenesDao::getIncidencia($datos);
          if(count($incidencia)>0){
            $llegada = $incidencia[0]['identificador_incidencia'];
            $color = $incidencia[0]['color'];
          }else{
            $llegada = 'D'; //descanso
          }
        }

        if ($llegada == 'A'){$color = 'green';}
        elseif($llegada == 'D'){$color = 'gray';}
        elseif($llegada == 'R'){$color = 'yellow';}
        elseif ($llegada == 'FF'){$color = 'red';}
        elseif ($llegada == 'FR'){$color = 'orange';}
        else{$color = 'green';}

        $fecha_temp = new \DateTime(date('Y-m-d'));

        if($fecha_inicio > $fecha_temp){
          $tabla .=<<<html
          <td><span class="btn btn-success"><label style="color: {$color};"> --- </label></span></td>
html;
        }else{
          $tabla .=<<<html
          <td><span class="btn btn-success"><label style="color: {$color};"> {$llegada} </label></span></td>
html;
        }
        
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
            <a href="/Incentivo/getIncentivosColaborador/{$value['catalogo_colaboradores_id']}/{$periodo['prorrateo_periodo_id']}/semanales" class="btn btn-primary"><span  style="color:white"></span> Ver Incentivos</a>
          </td>
        </tr>
html;
    $j++;
  }
  $encabezado .=<<<html
    <td>Ver Incentivos</td>
html;
    View::set('tbody',$tabla);
    View::set('thead',$encabezado);
  }




  /*
    @$tipo -> SEMANAL o QUINCENAL
    @status -> 1 Abierto y 0 Cerrado
  */
  public function getPeriodoProcesado($tipo, $idPeriodo){
    $periodo = ResumenesDao::searchPeriodoProcesado($tipo, $idPeriodo);
    $status = ($periodo[0]['status'] == 0) ? "Abierto": "Cerrado";

    $fechaIni = MasterDom::getFecha($periodo[0]['fecha_inicio']);
    $fechaFin = MasterDom::getFecha($periodo[0]['fecha_fin']);

    $status = ($periodo[0]['status'] == 0) ? "Abierto": "Cerrado";
    $label = ($periodo[0]['status'] == 0) ? "success": "danger";

    $htmlPeriodo = <<<html
      <b>( {$fechaIni} al {$fechaFin} )</b> <label class="label label-{$label}"> periodo {$status}</label>
html;
      return $htmlPeriodo;
    }

  /*
    @$tipo -> SEMANAL o QUINCENAL
    @status -> 1 Abierto y 0 Cerrado
  */
  public function getUltimoPeriodo($tipo, $status){
    $periodo = ResumenesDao::searchPeriodos($tipo, $status);
    return $periodo[0]['prorrateo_periodo_id'];
    }

    /*
      @$tipo -> SEMANAL o QUINCENAL
    @status -> 1 Abierto y 0 Cerrado
  */
  
  public function getPeriodo($titulo, $mensaje, $tipo, $status,$periodo_id, $perfil_id, $planta_id){
      $extraFooter = $this->getFooterExtra();
      $periodo = ResumenesDao::searchPeriodos($tipo, $status);
      //print_r($periodo);

      if(empty($periodo[0])){
        View::set('error',"Error Periodo");
        //View::set('periodo_id',ResumenesDao::getUltimoPeriodoHistorico($tipo)['prorrateo_periodo_id']);
        //View::set('tipo_periodo',strtolower($tipo));
        $periodo_id = ResumenesDao::getUltimoPeriodoHistorico($tipo)['prorrateo_periodo_id'];
        $tipo_periodo = strtolower($tipo);

        if($perfil_id == 1 || ($perfil_id == 6 && $planta_id==1) ){
          $codigo =<<<html
          <div class="form-group">
                  <input type="hidden" id="periodo_id" name="periodo_id" value="{$periodo_id}"/>
                  <input type="hidden" id="tipo_periodo" name="tipo_periodo" value="{$tipo_periodo}" />
                  <input type="hidden" id="mensaje" name="mensaje" value="{$mensaje}" />
                  <div class="col-md-3 col-sm-3 col-xs-3">
                    <button class="btn btn-danger" id="btnCancelarPeriodo" type="button" >Cancelar</button>
                  </div>
                  <div class="col-md-3 col-sm-3 col-xs-3">
                    <button class="btn btn-warning" id="btnRespaldarPeriodo" type="button">Respaldar</button>
                  </div>
                </div>
html;
        }
        View::set('codigo', $codigo);
        View::set('tituloError',$titulo);
        View::set('mensajeError',$mensaje);
        View::set('footer',$this->_contenedor->footer($extraFooter));
        View::render("error");
        exit;
      }else{
        $fechaIni = MasterDom::getFecha($periodo[0]['fecha_inicio']);
        $fechaFin = MasterDom::getFecha($periodo[0]['fecha_fin']);
        $status = ($periodo[0]['status'] == 0) ? "Abierto": "Cerrado";
        $label = ($periodo[0]['status'] == 0) ? "success": "danger";
      $htmlPeriodo = <<<html
      <b>( {$fechaIni} al {$fechaFin} )</b> <label class="label label-{$label}"> periodo {$status}</label>
html;
      } 
      return $htmlPeriodo;
    }

    public function getPeriodoTexto($tipo, $status){
      $periodo = ResumenesDao::searchPeriodos($tipo, $status);
      if(empty($periodo[0])){
        View::set('error',"Error Periodo");
        View::set('tituloError',"Al parecer no hay periodo Abierto");
        View::set('mensajeError',"Debe existir un periodo Abierto, para checar los incentivos");
        View::render("error");
        exit;
      }else{
        $fechaIni = MasterDom::getFecha($periodo[0]['fecha_inicio']);
        $fechaFin = MasterDom::getFecha($periodo[0]['fecha_fin']);
        $status = ($periodo[0]['status'] == 0) ? "Abierto": "Cerrado";
      $htmlPeriodo = <<<html
      <b>( {$fechaIni} al {$fechaFin} )</b>
html;
      } 
      return $htmlPeriodo;
    }

    /*
    Obtiene los incentivos SEMANALES O QUINCENALES, que ya han sido procesados
    */
    public function getPeriodosHistoricos($tipo, $periodoObtenido){
      $periodos = ResumenesDao::getTipoPeriodo($tipo);
      $option = "";
      foreach ($periodos as $key => $value) {
        $selected = ($value['prorrateo_periodo_id'] == $periodoObtenido) ? "selected" : "";
      $fechaIni = MasterDom::getFecha($value['fecha_inicio']);
      $fechaFin = MasterDom::getFecha($value['fecha_fin']);
      $option .=<<<html
        <option {$selected} value="{$value['prorrateo_periodo_id']}">({$fechaIni}) al ({$fechaFin})</option>
html;
      }
      return $option;
    }

    public function getHeader(){
      $extraHeader = <<<html
          <style>.foto{ width:100px; height:100px; border-radius: 50px;}</style>
          <link href="/js/tables/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
          <link href="/js/tables/vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
          <link href="/js/tables/vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
          <link href="/js/tables/vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
          <link href="/js/tables/vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">
html;
    return $extraHeader;  
    }

    public function guardarPeriodo(){
        $periodo = ResumenesDao::getPeriodoById(MasterDom::getData('periodo_id'));
        //$periodo = ResumenesDao::getPeriodoById('3');
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

        $administrador = ResumenesDao::getDatosUsuarioLogeado(MasterDom::getSesion('usuario'));
        if($administrador['perfil_id'] == 6){
        $colaboradores = ResumenesDao::getAllColaboradoresPago(ucwords(strtolower($periodo['tipo'])), '');
      }else{
        $colaboradores = ResumenesDao::getAllColaboradoresPago(ucwords(strtolower($periodo['tipo'])), $administrador['administrador_id']);
      }

        foreach($colaboradores as $key => $value){
            $fecha_inicio = new \DateTime($periodo['fecha_inicio']);
            $colaborador = new \stdClass();
            $colaborador->_catalogo_colaboradores_id = $value['catalogo_colaboradores_id'];
            $num_semana = 0;

           /* OBTENCION DE DIAS LABORALES DEL COLABORADOR EN SUS HORARIOS */
            $dias_laborales = ResumenesDao::getHorarioLaboral($value['catalogo_colaboradores_id']);
            $ultimo_horario = ResumenesDao::getLastHorario($value['catalogo_colaboradores_id']);
            $horarios = ResumenesDao::getListHorario($value['catalogo_colaboradores_id']);
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
                          ResumenesDao::insertProrrateoColaboradorHorario($colaborador);
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
                                //echo "catalogo horario id: $catalogo_horario_id:::::::::";
                                $catalogo_horario_id = $horarios[0]['catalogo_horario_id'];
                                //echo ">$catalogo_horario_id<br><br>";
                              }else{
                                //echo "catalogo_horario_id = $catalogo_horario_id:::::::::";
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
                        ResumenesDao::insertProrrateoColaboradorHorario($colaborador);
                      }
                  }
              $fecha_inicio->add(new \DateInterval('P1D'));
            }//fin del while fechas
        }//fin del foreach
    }

    public function getHorario($id){
    $dia = '';
    foreach (ResumenesDao::getHorarioLaboral($id) as $key => $value) {
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

    public function getFooterExtra(){
      $extraFooter =<<<html
      <script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
      <script>
          $(document).ready(function(){

            $("#btnAplicar").click(function(){
              $.ajax({
                url: '/ResumenSemanal/getTablaRangoFechas',
                type: 'POST',
                data:{periodo_id: $("#periodo_id").val(), tipo_periodo: $("#tipo_periodo").val(), mensaje: $("#mensaje").val()},
                success: function(response){
                  var obj = $.parseJSON(response);
                  var datos = eval (obj);
                  $("#contenedor_tabla").html('<table class="table table-striped table-bordered table-hover" id="muestra-colaboradores" name="muestra-colaboradores"><thead><tr>'+datos['encabezado']+'</tr></thead><tbody>'+datos['tabla']+'</tbody></table>');

                  var oTable = $('#muestra-colaboradores').DataTable({
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
                  $(".dt-buttons").after('<div class="col-md-6 col-sm-6 col-xs-6" align="center"><div class="col-md-2 col-sm-2 col-xs-2"><span style="color:#169D5F" class="fa fa-circle"></span><label class="col-md-12 col-sm-12 col-xs-12">Asistencia</label></div><div class="col-md-2 col-sm-2 col-xs-2"><span style="color:#E9FC00" class="fa fa-circle"></span><label class="col-md-12 col-sm-12 col-xs-12">Retardo</label></div><div class="col-md-2 col-sm-2 col-xs-2"><span style="color:#FFA357" class="fa fa-circle"></span><label class="col-md-12 col-sm-12 col-xs-12">Falta Por Retardo</label></div><div class="col-md-2 col-sm-2 col-xs-2"><span style="color:#991D04" class="fa fa-circle"></span><label class="col-md-12 col-sm-12 col-xs-12">Falta</label></div><div class="col-md-2 col-sm-2 col-xs-2"><span style="color:#628881" class="fa fa-circle"></span><label class="col-md-12 col-sm-12 col-xs-12">Descanso</label></div></div>');

                  $(".buttons-copy").addClass('btn btn-default');
                  $(".buttons-excel").addClass('btn btn-success');
                  $(".buttons-excel").addClass('fa-file-excel-o');
                  $(".buttons-csv").addClass('btn btn-warning');
                  $(".buttons-csv").addClass('fa fa-table');
                  $(".buttons-pdf").addClass('btn btn-primary');
                  $(".buttons-pdf").addClass('fa fa-file-pdf-o');

                  verificarPeriodo();

                }
              });
            });


            $("#btnGuardar").click(function(){
              
              $.ajax({
                url: '/ResumenSemanal/guardarPeriodo',
                type: 'POST',
                data:{periodo_id: $("#periodo_id").val(), tipo_periodo: $("#tipo_periodo").val(), mensaje: $("#mensaje").val()},
                success: function(response){
                  //alert(response);
                  //$("#respuesta").html(response);
                  //verificarPeriodo();
                }
              });

              $("#all").attr("action", "/ResumenSemanal/guardarPeriodoAsistencia");
              $("#all").attr("target", "");
              $("#all").submit();

            });

            function verificarPeriodo(){
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

                  }else{
                    if(response == 1){
                      $("#btnGuardar").show();
                      $("#btnRestaurarPeriodo").show();
                      $("#btnCancelarPeriodo").hide();
                      $("#btnRespaldarPeriodo").hide();
                      //$(".dt-buttons").addClass('hidden');
                    }else{
                      if(response == -1){
                        $("#btnGuardar").hide();
                        $("#btnRestaurarPeriodo").hide();
                        $("#btnCancelarPeriodo").show();
                        $("#btnRespaldarPeriodo").show();
                        //$(".dt-buttons").removeClass('hidden');
                      }else{
                        if(response == -2){
                          $("#btnGuardar").hide();
                          $("#btnRestaurarPeriodo").hide();
                          $("#btnCancelarPeriodo").show();
                          $("#btnRespaldarPeriodo").hide();
                          //$(".dt-buttons").removeClass('hidden');
                        }else{
                          $("#btnGuardar").hide();
                          $("#btnRestaurarPeriodo").hide();
                          $("#btnCancelarPeriodo").hide();
                          $("#btnRespaldarPeriodo").hide();
                          //$(".dt-buttons").removeClass('hidden');
                        }
                      }//fin del tercer if
                    }//fin del segundo if
                  }//fin del primer if
                }//funcion successsfull ajax
              });//cierre del ajax
            } //cierre del evento
            

            $("#btnCancelarPeriodo").click(function(){

              $("#all").attr('action','/ResumenSemanal/cancelarPeriodo');
              $("#all").submit();
            });

            $("#btnRespaldarPeriodo").click(function(){
              $("#all").attr('action','/ResumenSemanal/respaldarPeriodo');
              $("#all").submit();
            });

            $("#btnRestaurarPeriodo").click(function(){
              $("#all").attr('action','/ResumenSemanal/restaurarPeriodo');
              $("#all").attr('target','');
              $("#all").submit();
            });
            $("#btnAplicar").click();

          });//fin del document ready
        </script>
html;
    return $extraFooter;
    }

    public function getFooter(){
      $extraFooter = <<<html
      <!-- Datatables -->
      <script src="/js/tables/vendors/datatables.net/js/jquery.dataTables.min.js"></script>
      <script src="/js/tables/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
      <script src="/js/tables/vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
      <script src="/js/tables/vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
      <script src="/js/tables/vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
      <script src="/js/tables/vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
      <script src="/js/tables/vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
      <script src="/js/tables/vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
      <script src="/js/tables/vendors/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
      <script src="/js/tables/vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
      <script src="/js/tables/vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
      <script src="/js/tables/vendors/datatables.net-scroller/js/dataTables.scroller.min.js"></script>
      <script src="/js/tables/vendors/jszip/dist/jszip.min.js"></script>
      <script src="/js/tables/vendors/pdfmake/build/pdfmake.min.js"></script>
      <script src="/js/tables/vendors/pdfmake/build/vfs_fonts.js"></script>
html;
    $extraFooter .=<<<html
      <script>
        $(document).ready(function(){
          $("#muestra-colaboradores").tablesorter();
          var oTable = $('#muestra-colaboradores').DataTable({
            "columnDefs": [{
              "orderable": false,
              "targets": 0
            }],
            "order": false
          });

          $('#muestra-colaboradores input[type=search]').keyup( function () {
            var table = $('#example').DataTable();
            table.search(
              jQuery.fn.DataTable.ext.type.search.html(this.value)
            ).draw();
          });

        });
      </script>
html;
    return $extraFooter;
    }


    /*
      @$tipo -> SEMANAL o QUINCENAL
      @status -> 1 Abierto y 0 Cerrado
    */
    public function getIdPeriodo($tipo, $status){
      $periodo = IncidenciaDao::searchPeriodos($tipo, $status);
      return $periodo[0]['prorrateo_periodo_id'];
    }


    public function alerta($regreso, $class, $mensaje){
      View::set('class',$class);
      View::set('regreso',$regreso);
      View::set('mensaje',$mensaje);
      View::set('header',$this->_contenedor->header($extraHeader));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("alerta");
    }
}
