<?php
namespace App\controllers;
defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \App\controllers\Contenedor;
use \Core\Controller;
use \App\models\AdminPeriodo AS AdminPeriodoDao;

class AdminPeriodo extends Controller{

    private $_contenedor;

    function __construct(){
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header',$this->_contenedor->header());
        View::set('footer',$this->_contenedor->footer());
        if(Controller::getPermisosUsuario($this->__usuario, "seccion_periodo", 1) ==0)
          header('Location: /Principal/');

    }

    public function abiertos(){
      echo $this->statusPeriodo(0);
      View::set('tabla',$this->tablaPeriodos(0));
      View::set('tipoPeriodo',"SEMANALES y QUINCENALES"); // COLOCA EL TIPO DE PERIODO QUE ES
      View::set('header',$this->_contenedor->header());
      View::set('footer',$this->_contenedor->footer($this->getExtraFooter()));
      View::render("admin_periodo_all");
    }

    public function historicosSemanales(){
      //View::set('control','style="display:none;"');
      View::set('tabla',$this->getPeriodosHistoricos(1,"SEMANAL"));
      View::set('tipoPeriodo',"Historicos semanales"); // COLOCA EL TIPO DE PERIODO QUE ES
      View::set('header',$this->_contenedor->header());
      View::set('footer',$this->_contenedor->footer($this->getExtraFooter()));
      View::render("admin_periodo_all");
    }

    public function historicosQuincenales(){
      //View::set('control','style="display:none;"');
      View::set('tabla',$this->getPeriodosHistoricos(1,"QUINCENAL"));
      View::set('tipoPeriodo',"historicos quincenales"); // COLOCA EL TIPO DE PERIODO QUE ES
      View::set('header',$this->_contenedor->header());
      View::set('footer',$this->_contenedor->footer($this->getExtraFooter()));
      View::render("admin_periodo_all");
    }

    public function tablaPeriodos($estatus){
      $periodos = AdminPeriodoDao::getPeriodos($estatus);
      $html = "";
      foreach ($periodos as $key => $value) {
        $status = ($value['status']==0 ) ? "Abierto" : "Cerrado";
        $html .=<<<html
          <tr>
            <td style="text-align:center; vertical-align:middle;" >{$value['fecha_inicio']}</td>
            <td style="text-align:center; vertical-align:middle;" >{$value['fecha_fin']}</td>
            <td style="text-align:center; vertical-align:middle;" >{$value['tipo']}</td>
            <td style="text-align:center; vertical-align:middle;" >{$status}</td>
            <td style="text-align:center; vertical-align:middle;" >
              <a href="/AdminPeriodo/cerrarPeriodo/{$value['prorrateo_periodo_id']}" class="btn btn-danger"> Eliminar </a><br>
              <!--a href="/AdminPeriodo/editPeriodo/{$value['prorrateo_periodo_id']}" class="btn btn-success"> Editar </a-->
            </td>
          </tr>
html;
      }
      return $html;
    }

    public function getPeriodosHistoricos($estatus, $tipo){
      $periodos = AdminPeriodoDao::getPeriodosHistoricos($estatus, $tipo);
      $html = "";
      foreach ($periodos as $key => $value) {
        if($value['status'] == 2) 
          $status = "En proceso de cierre";
        if($value['status'] == 1)
          $status = "Cerrado";
        $html .=<<<html
          <tr>
            <td style="text-align:center; vertical-align:middle;" >{$value['fecha_inicio']}</td>
            <td style="text-align:center; vertical-align:middle;" >{$value['fecha_fin']}</td>
            <td style="text-align:center; vertical-align:middle;" >{$value['tipo']}</td>
            <td style="text-align:center; vertical-align:middle;" >{$status}</td>
            <td style="text-align:center; vertical-align:middle;" >Periodo Cerrado</td>
            <!--td style="text-align:center; vertical-align:middle;" >
              <a href="/AdminPeriodo/abrirPeriodo/{$value['prorrateo_periodo_id']}" class="btn btn-info"> Abrir </a>
            </td-->
          </tr>
html;
      }
      return $html;
    }

    public function cerrarPeriodo($idPeriodo){
      $val = AdminPeriodoDao::updatePeriodo($idPeriodo, -1);

      if($val)
        $this->alerta("El periodo con ID {$idPeriodo}, se ha elimininado, ahora puedes crear un nuevo periodo", 'cerrar-periodo','abiertos');
      else
        $this->alerta("Ups, ha ocurrido un error", 'error','abiertos');
      
    }

    public function abrirPeriodo($idPeriodo){
      $val = AdminPeriodoDao::updatePeriodo($idPeriodo, 0);

      if($val)
        $this->alerta("El periodo con ID {$idPeriodo}, se ha abierto.", 'abrir-periodo','abiertos');
      else
        $this->alerta("Ups, ha ocurrido un error", 'error','abiertos');
    }

    public function editPeriodo($idPeriodo){
      $extraFooter =<<<html
        <script src="/js/moment/moment.min.js"></script>
        <script src="/js/datepicker/scriptdatepicker.js"></script>
        <script src="/js/datepicker/datepicker2.js"></script>
        <script>
          $(document).ready(function(){
            $("#add").validate({
              rules:{
                fecha_inicio:{
                  required: true
                },
                fecha_fin:{
                  required: true
                },
                tipo:{
                  required: true
                }
              },
              messages:{
                fecha_inicio:{
                  required: "Este campo es requerido"
                },
                fecha_fin:{
                  required: "Este campo es requerido"
                },
                tipo:{
                  required: "Este campo es requerido"
                }
              }
            });//fin del jquery validate

            $("#btnCancel").click(function(){
              window.location.href = "/AdminPeriodo/abiertos/";
            });//fin del btnAdd

          });//fin del document.ready
        </script>
html;
      $periodo = AdminPeriodoDao::getPeriodo($idPeriodo);

      View::set('periodo',$periodo);
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render('admin_periodo_edit');
    }

    public function periodoEdit(){
      $data = new \stdClass();
      $id = MasterDom::getData('id_periodo');
      $data->_prorrateo_periodo_id = MasterDom::getData('id_periodo');
      $data->_fecha_inicio = MasterDom::getData('fecha_inicio');
      $data->_fecha_fin = MasterDom::getData('fecha_fin');
      $tipo = (MasterDom::getData('tipo') == "quincenal") ? "QUINCENAL" : "SEMANAL";
      $data->_tipo = $tipo;

      $updatePeriodo = AdminPeriodoDao::updatePeriodoInfo($data);

      if($updatePeriodo>0)
        $this->alerta("El periodo con ID {$idPeriodo}, se ha actualizado", 'update-periodo',"abiertos");
      else
        $this->alerta("Al parecer, no haz cambiado algun valor", 'error-no-cambios',"editPeriodo/{$id}");

    }

    public function statusPeriodo($estatus){
      $periodos = AdminPeriodoDao::getPeriodos($tipoPeriodo, $estatus);
      if(count($periodos) == 0){
        View::set('error',"¡No hay periodos abiertos!");
        View::set('tituloError',"Al parecer no existen periodos abiertos");
        View::set('mensajeError',"Semanales y Quincenales");
        View::set('visualizar',"style=\"display:none;\"");
        View::render("error");
        exit;
      }
    }

    public function getUsuario(){
      return $this->__usuario;
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
              $('#all').attr('action', '/Empresa/generarPDF/');
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });

            $("#export_excel").click(function(){
              $('#all').attr('action', '/Empresa/generarExcel/');
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });

            $("#delete").click(function(){
              var seleccionados = $("input[name='borrar[]']:checked").length;
              if(seleccionados>0){
                alertify.confirm('¿Segúro que desea eliminar lo seleccionado?', function(response){
                  if(response){
                    $('#all').attr('target', '');
                    $('#all').attr('action', '/Empresa/delete');
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
      $periodos = AdminPeriodoDao::getAll();
      $usuario = $this->__usuario;
      $editarHidden = (Controller::getPermisosUsuario($usuario, "seccion_empresas", 5)==1)? "" : "style=\"display:none;\"";
      $eliminarHidden = (Controller::getPermisosUsuario($usuario, "seccion_empresas", 6)==1)? "" : "style=\"display:none;\"";
      $tabla= '';
      foreach ($periodos as $key => $value) {

        $value['status'] = ($value['status'] == 0)? 'Abierto': 'Cerrado';
        $tabla.=<<<html
                <tr>
                <!--<td><input type="checkbox" name="borrar[]" value="{$value['prorrateo_periodo_id']}"/></td>-->
                <td>{$value['fecha_inicio']}</td>
                <td>{$value['fecha_fin']}</td>
                <td>{$value['tipo']}</td>
                <td>{$value['status']}</td>
                </tr>
html;
      }

      $pdfHidden = (Controller::getPermisosUsuario($usuario, "seccion_empresas", 2)==1)?  "" : "style=\"display:none;\"";
      $excelHidden = (Controller::getPermisosUsuario($usuario, "seccion_empresas", 3)==1)? "" : "style=\"display:none;\"";
      $agregarHidden = (Controller::getPermisosUsuario($usuario, "seccion_empresas", 4)==1)? "" : "style=\"display:none;\"";
      View::set('pdfHidden',$pdfHidden);
      View::set('excelHidden',$excelHidden);
      View::set('agregarHidden',$agregarHidden);
      View::set('editarHidden',$editarHidden);
      View::set('eliminarHidden',$eliminarHidden);
      View::set('tabla',$tabla);
      View::set('header',$this->_contenedor->header($extraHeader));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("admin_periodo_all");
    }

    public function add(){
      $extraFooter =<<<html
      <script src="/js/moment/moment.min.js"></script>
      <script src="/js/datepicker/scriptdatepicker.js"></script>
      <script src="/js/datepicker/datepicker2.js"></script>
      <script>
        $(document).ready(function(){
          $("#add").validate({
            rules:{
              fecha_inicio:{
                required: true
              },
              fecha_fin:{
                required: true
              },
              tipo:{
                required: true
              }
            },
            messages:{
              fecha_inicio:{
                required: "Este campo es requerido"
              },
              fecha_fin:{
                required: "Este campo es requerido"
              },
              tipo:{
                required: "Este campo es requerido"
              }
            }
          });//fin del jquery validate

          $("#btnCancel").click(function(){
            window.location.href = "/AdminPeriodo/abiertos/";
          });//fin del btnAdd

        });//fin del document.ready
      </script>
html;
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("admin_periodo_add");
    }

    public function edit($id){
      $extraFooter =<<<html
      <script src="/js/moment/moment.min.js"></script>
      <script src="/js/datepicker/scriptdatepicker.js"></script>
      <script src="/js/datepicker/datepicker2.js"></script>
      <script>
        $(document).ready(function(){
          $("#add").validate({
            rules:{
              fecha_inicio:{
                required: true
              },
              fecha_fin:{
                required: true
              },
              tipo:{
                required: true
              }
            },
            messages:{
              fecha_inicio:{
                required: "Este campo es requerido"
              },
              fecha_fin:{
                required: "Este campo es requerido"
              },
              tipo:{
                required: "Este campo es requerido"
              }
            }
          });//fin del jquery validate

          $("#btnCancel").click(function(){
            window.location.href = "/AdminPeriodo/abiertos/";
          });//fin del btnAdd

        });//fin del document.ready
      </script>
html;
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("admin_periodo_add");
    }

    public function delete(){
      $id = MasterDom::getDataAll('borrar');
      $array = array();
      foreach ($id as $key => $value) {
        $id = AdminPeriodoDao::delete($value);
        if($id['seccion'] == 2){
          array_push($array, array('seccion' => 2, 'id' => $id['id'] ));
        }else if($id['seccion'] == 1){
          array_push($array, array('seccion' => 1, 'id' => $id['id'] ));
        }
      }
      $this->alertas("Eliminacion de Empresas", $array, "/Empresa/");
    }

    public function periodoAdd(){
      $periodo = new \stdClass();
      $periodo->_fecha_inicio = MasterDom::getDataAll('fecha_inicio');
      $periodo->_fecha_fin = MasterDom::getDataAll('fecha_fin');
      $periodo->_tipo = MasterDom::getDataAll('tipo');

      $busquedaPeriodoAbierto = AdminPeriodoDao::getPeriodoAbierto($periodo->_tipo); // SEMANAL O QUINCENAL
      if(count($busquedaPeriodoAbierto)>0){
        $this->alertasError("Alerta, no se puede crear un periodo {$periodo->_tipo}, ya que hay uno abierto, con las siguientes fechas: {$busquedaPeriodoAbierto['fecha_inicio']} - {$busquedaPeriodoAbierto['fecha_fin']}.<br>Si deseas cerrar este periodo comunicarte con el departamento de sistemas.", "error","add");
      }else{
        $busquedaPeriodo = AdminPeriodoDao::getPeriodoFechas($periodo);
        if(count($busquedaPeriodo) > 0){
          $this->alertasError("Ya existe un periodo {$periodo->_tipo}, con las fechas {$periodo->_fecha_inicio} - {$periodo->_fecha_fin}, favor de intentar con otro.", "error","add");
        }else{
          if(AdminPeriodoDao::insert($periodo) >= 1) $this->alerta($id,'add', "abiertos" );
          else $this->alerta($id,'error', "abiertos");
        }
      }

    }


    public function validarOtroNombre(){
      $id = AdminPeriodoDao::getIdComparacion($_POST['id'], $_POST['nombre']);
      if($id == 1)
        echo "true";

      if($id == 2){
        $dato = AdminPeriodoDao::getNombreEmpresa($_POST['nombre']);
        if($dato == 2){
          echo "true";
        }else{
          echo "false";
        }
      }

    }

    public function alertasError($texto, $parametro, $direccion){
      $regreso = "/AdminPeriodo/{$direccion}";
      View::set('class',"danger");
      View::set('regreso',$regreso);
      View::set('mensaje',$texto);
      View::set('header',$this->_contenedor->header($extraHeader));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("alerta");
    }

    public function alerta($id, $parametro, $direccion){
      $regreso = "/AdminPeriodo/{$direccion}";

      if($parametro == 'add'){
        $mensaje = "Se ha agregado correctamente";
        $class = "success";
      }

      if($parametro == 'edit'){
        $mensaje = "Se ha modificado correctamente";
        $class = "success";
      }

      if($parametro == 'delete'){
        $mensaje = "Se ha eliminado la empresa {$id}, ya que cambiaste el estatus a eliminado";
        $class = "success";
      }

      if($parametro == 'cerrar-periodo'){
        $mensaje = "{$id}, ya que cambiaste el estatus cerrado";
        $class = "success";
      }

      if($parametro == 'abrir-periodo'){
        $mensaje = "{$id}, ya que cambiaste el estatus abierto";
        $class = "success";
      }

      if($parametro == 'update-periodo'){
        $mensaje = "{$id}, ya se actualizo el periodo";
        $class = "success";
      }

      if($parametro == 'nothing'){
        $mensaje = "Posibles errores: <li>No intentaste actualizar ningún campo</li> <li>Este dato ya esta registrado, comunicate con soporte técnico</li> ";
        $class = "warning";
      }

      if($parametro == 'error-no-cambios'){
        $mensaje = "No intentaste actualizar ning&uacute;n campo";
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

    public function getExtraFooter(){
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
              $('#all').attr('action', '/Empresa/generarPDF/');
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });

            $("#export_excel").click(function(){
              $('#all').attr('action', '/Empresa/generarExcel/');
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });

            $("#delete").click(function(){
              var seleccionados = $("input[name='borrar[]']:checked").length;
              if(seleccionados>0){
                alertify.confirm('¿Segúro que desea eliminar lo seleccionado?', function(response){
                  if(response){
                    $('#all').attr('target', '');
                    $('#all').attr('action', '/Empresa/delete');
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
      return $extraFooter;
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
