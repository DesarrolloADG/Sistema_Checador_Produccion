<?php
namespace App\controllers;
defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \App\models\General AS GeneralDao;
use \Core\Controller;

//echo dirname(__DIR__);

require_once dirname(__DIR__).'/../public/librerias/mpdf/mpdf.php';
require_once dirname(__DIR__).'/../public/librerias/phpexcel/Classes/PHPExcel.php';

//require_once '/home/granja/backend/public/librerias/mpdf/mpdf.php';
//require_once '/home/granja/backend/public/librerias/phpexcel/Classes/PHPExcel.php';

class Contenedor extends Controller{


    function __construct(){
      parent::__construct();
      //echo "Este esl usuario: {$this->__usuario}+++++";
    }

    public function getUsuario(){
      return $this->__usuario;
    }

    public function header($extra = ''){
        $usuario = $this->__usuario;
        $empresa = Controller::getPermisosUsuario($usuario, "seccion_empresas", 1);
        $empresaAdd = Controller::getPermisosUsuario($usuario, "seccion_empresas", 4);
        $plantas = Controller::getPermisosUsuario($usuario, "seccion_plantas", 1);
        $plantasAdd = Controller::getPermisosUsuario($usuario, "seccion_plantas", 4);
        $horarios = Controller::getPermisosUsuario($usuario, "seccion_horarios", 1);
        $horariosAdd = Controller::getPermisosUsuario($usuario, "seccion_horarios", 4);
        $departamentos = Controller::getPermisosUsuario($usuario, "seccion_departamentos", 1);
        $departamentosAdd = Controller::getPermisosUsuario($usuario, "seccion_departamentos", 4);
        $ubicaciones = Controller::getPermisosUsuario($usuario, "seccion_ubicaciones", 1);
        $ubicacionesAdd = Controller::getPermisosUsuario($usuario, "seccion_ubicaciones", 4);
        $lectores = Controller::getPermisosUsuario($usuario, "seccion_lectores", 1);
        $lectoresAdd = Controller::getPermisosUsuario($usuario, "seccion_lectores", 4);
        $dias_festivos = Controller::getPermisosUsuario($usuario, "seccion_dias_festivos", 1);
        $dias_festivosAdd = Controller::getPermisosUsuario($usuario, "seccion_dias_festivos", 4);
        $motivo_bajas = Controller::getPermisosUsuario($usuario, "seccion_motivo_bajas", 1);
        $motivo_bajasAdd = Controller::getPermisosUsuario($usuario, "seccion_motivo_bajas", 4);
        $incidencias = Controller::getPermisosUsuario($usuario, "seccion_incidencias", 1);
        $incidenciasAdd = Controller::getPermisosUsuario($usuario, "seccion_incidencias", 4);
        $puestos = Controller::getPermisosUsuario($usuario, "seccion_puestos", 1);
        $puestosAdd = Controller::getPermisosUsuario($usuario, "seccion_puestos", 4);
        $incentivos = Controller::getPermisosUsuario($usuario, "seccion_incentivos", 1);
        $incentivosAdd = Controller::getPermisosUsuario($usuario, "seccion_incentivos", 4);
        $colaboradores = Controller::getPermisosUsuario($usuario, "seccion_colaboradores", 1);
        $colaboradoresAdd = Controller::getPermisosUsuario($usuario, "seccion_colaboradores", 4);

        $asignarIncentivos = Controller::getPermisosUsuario($usuario, "seccion_incentivosadd", 1);
        $periodo = Controller::getPermisosUsuario($usuario, "seccion_periodo", 1);
        $registroIncidencias = Controller::getPermisosUsuario($usuario, "seccion_registro_incidencias", 1);
        $resumen = Controller::getPermisosUsuario($usuario, " seccion_resumen", 1);
        $prorrateo = Controller::getPermisosUsuario($usuario, "seccion_prorrateo", 1);

        $arr = array($asignarIncentivos,$periodo,$registroIncidencias,$resumen,$prorrateo);
        $activo_menu = array_sum($arr);
        $permisosGlobales = Controller::getPermisosUsuario($usuario, "permisos_globales",7);

        $permisoRH = Controller::getPermisoRecursosHumanos($usuario);

        $admin = GeneralDao::getDatosUsuarioLogeado($usuario);
      
      $header =<<<html
        <!DOCTYPE html>
        <html lang="en">
          <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <!-- Meta, title, CSS, favicons, etc. -->
            <meta charset="utf-8">
            <title>AG Alimentos de Granja</title>
            <link href="/css/nprogress.css" rel="stylesheet">
            <link rel="stylesheet" href="/css/tabla/sb-admin-2.css">
            <link rel="stylesheet" href="/css/bootstrap/datatables.bootstrap.css">
            <link rel="stylesheet" href="/css/bootstrap/bootstrap.css">
            <link rel="stylesheet" href="/css/bootstrap/bootstrap-switch.css">
            <link rel="stylesheet" href="/css/validate/screen.css">

            <link rel="stylesheet" href="/css/alertify/alertify.core.css" />
            <link rel="stylesheet" href="/css/alertify/alertify.default.css" id="toggleCSS" />

            <link href="/css/bootstrap/bootstrap.min.css" rel="stylesheet">
          	<link href="/css/font-awesome.min.css" rel="stylesheet">
            <link href="/css/menu/menu5custom.min.css" rel="stylesheet">
            <link href="/css/green.css" rel="stylesheet">
            <link href="/css/custom.min.css" rel="stylesheet">

            <link href="/librerias/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css" rel="stylesheet">
            <link rel="stylesheet" type="text/css" href="/librerias/vintage_flip_clock/jquery.flipcountdown.css" />
        </head>
html;
$menu =<<<html
<body class="nav-md">
  <div class="container body">
    <div class="main_container">

      <div class="col-md-3 left_col">
        <div class="left_col scroll-view">
          <div class="navbar nav_title" style="border: 0;">
            <a href="/Principal/" class="site_title"><i class="fa fa-home"></i> <span>ADG</span></a>
          </div>
          <div class="clearfix"></div>
          <div class="profile clearfix">
            <div class="profile_pic">
              <img src="/img/logo.jpg" alt="..." class="img-circle profile_img">
            </div>
            <div class="profile_info">
              <span>Bienvenido,</span>
html;
$menu.=<<<html
              <h2>{$usuario}</h2>
            </div>
          </div>
          <br/>
          <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
            <div class="menu_section">
              <h3>General</h3>
              <ul class="nav side-menu">
                <li><a><i class="glyphicon glyphicon-folder-open"> </i>&nbsp; Catalogos <span class="fa fa-chevron-down"></span></a>
                  <ul class="nav child_menu">
html;
$mostrar = ($empresa==1) ? "":"style=\"display:none;\"";
$agregar = ($empresaAdd==1) ? "":"style=\"display:none;\"";
$menu.=<<<html
                    <li {$mostrar}><a>Empresas<span class="fa fa-chevron-down"></span></a>
                      <ul class="nav child_menu">
                        <li><a href="/Empresa/">Mostrar</a></li>
                        <li {$agregar}><a href="/Empresa/add">Agregar</a></li>
                      </ul>
                    </li>
html;
$mostrar = ($plantas==1) ? "":"display:none;";
$agregar = ($plantasAdd==1) ? "":"style=\"display:none;\"";
$menu.=<<<html
                    <li style="{$mostrar}"><a>Plantas <span class="fa fa-chevron-down"></span></a>
                      <ul class="nav child_menu">
                        <li><a href="/Planta/">Mostrar</a></li>
                        <li {$agregar}><a href="/Planta/add">Agregar</a></li>
                      </ul>
                    </li>
html;
$mostrar = ($horarios==1) ? "":"display:none;";
$agregar = ($horariosAdd==1) ? "":"display:none;";
$menu.=<<<html
                    <li style="{$mostrar}"><a>Horarios<span class="fa fa-chevron-down"></span></a>
                      <ul class="nav child_menu">
                        <li><a href="/Horarios/">Mostrar</a></li>
                        <li style="{$agregar}"><a href="/Horarios/add">Agregar</a></li>
                      </ul>
                    </li>
html;
$mostrar = ($departamentos==1) ? "":"display:none;";
$agregar = ($departamentosAdd==1) ? "":"display:none;";
$menu.=<<<html
                    <li style="{$mostrar}"><a>Departamentos<span class="fa fa-chevron-down"></span></a>
                      <ul class="nav child_menu">
                        <li><a href="/Departamento/">Mostrar</a></li>
                        <li style="{$agregar}"><a href="/Departamento/add">Agregar</a></li>
                      </ul>
                    </li>
html;
$mostrar = ($ubicaciones==1) ? "":"display:none;";
$agregar = ($ubicacionesAdd==1) ? "":"display:none;";
$menu.=<<<html
                    <li style="{$mostrar}"><a>Ubicaciones<span class="fa fa-chevron-down"></span></a>
                      <ul class="nav child_menu">
                        <li><a href="/Ubicacion/">Mostrar</a></li>
                        <li style="{$agregar}"><a href="/Ubicacion/add">Agregar</a></li>
                      </ul>
                    </li>
html;
$mostrar = ($lectores==1) ? "":"display:none;";
$agregar = ($lectoresAdd==1) ? "":"display:none;";
$menu.=<<<html
                    <li style="{$mostrar}"><a>Lectores<span class="fa fa-chevron-down"></span></a>
                      <ul class="nav child_menu">
                        <li><a href="/Lectores">Mostrar</a></li>
                        <li style="{$agregar}"><a href="/Lectores/add">Agregar</a></li>
                      </ul>
                    </li>
html;
$mostrar = ($dias_festivos==1) ? "":"display:none;";
$agregar = ($dias_festivosAdd==1) ? "":"display:none;";
$menu.=<<<html
                    <li style="{$mostrar}"><a>Días festivos<span class="fa fa-chevron-down"></span></a>
                      <ul class="nav child_menu">
                        <li><a href="/diasfestivos">Mostrar</a></li>
                        <li style="{$agregar}"><a href="/diasfestivos/add">Agregar</a></li>
                      </ul>
                    </li>
html;
$mostrar = ($motivo_bajas==1) ? "":"display:none;";
$agregar = ($motivo_bajasAdd==1) ? "":"display:none;";
$menu.=<<<html
                    <li style="{$mostrar}"><a>Motivos de bajas<span class="fa fa-chevron-down"></span></a>
                      <ul class="nav child_menu">
                        <li><a href="/motivobajas">Mostrar</a></li>
                        <li style="{$agregar}"><a href="/motivobajas/add">Agregar</a></li>
                      </ul>
                    </li>
html;
$mostrar = ($incidencias==1) ? "":"display:none;";
$agregar = ($incidenciasAdd==1) ? "":"display:none;";
$menu.=<<<html
                    <li style="{$mostrar}"><a>Incidencias<span class="fa fa-chevron-down"></span></a>
                      <ul class="nav child_menu">
                        <li><a href="/incidencias">Mostrar</a></li>
                        <li style="{$agregar}"><a href="/incidencias/add">Agregar</a></li>
                      </ul>
                    </li>
html;

$mostrar = ($puestos==1) ? "":"display:none;";
$agregar = ($puestosAdd==1) ? "":"display:none;";
$menu.=<<<html
                    <li style="{$mostrar}"><a>Puestos<span class="fa fa-chevron-down"></span></a>
                      <ul class="nav child_menu">
                        <li><a href="/Puesto/">Mostrar</a></li>
                        <li style="{$agregar}"><a href="/Puesto/add">Agregar</a></li>
                      </ul>
                    </li>
html;

$mostrar = ($incentivos==1) ? "":"display:none;";
$agregar = ($incentivosAdd==1) ? "":"display:none;";
$menu.=<<<html
                    <li style="{$mostrar}"><a>Incentivos<span class="fa fa-chevron-down"></span></a>
                      <ul class="nav child_menu">
                        <li><a href="/incentivos">Mostrar</a></li>
                        <li style="{$agregar}"><a href="/incentivos/add">Agregar</a></li>
                      </ul>
                    </li>
html;
$RHSeccionUsuario = ($permisoRH == 2) ? "display:none;":"";
$mostrar = ($colaboradores==1) ? "":"display:none;";
$agregar = ($colaboradoresAdd==1) ? "":"display:none;";
$operacionesColaboradores = ($colaboradores==1) ? "":"display:none;";
$RH = ($permisoRH == 2) ? "":"display:none;";
$menu.=<<<html
                    <!--li style="{$mostrar}"><a>Colaboradores <span class="fa fa-chevron-down"></span></a>
                      <ul class="nav child_menu">
                        <li><a href="/Colaboradores">Mostrar</a></li>
                        <li style="{$agregar}"><a href="/Colaboradores/existente">Agregar</a></li>
                      </ul>
                    </li-->
                    <li style="{$operacionesColaboradores} {$RHSeccionUsuario}"><a href="/Colaboradores/">Colaboradores</a></li>
                    <li style="{$RH}"><a>Colaboradores<span class="fa fa-chevron-down"></span></a>
                      <ul class="nav child_menu">
                        <li><a href="/Colaboradores/colaboradoresPropios">Propios</a></li>
                        <li><a href="/Colaboradores/">Todos</a></li>
                      </ul>
                    </li>
                  </ul>
                </li>
html;


$OperacionesMostrarIncentivos = ($asignarIncentivos==1) ? "":"display:none;";
$OperacionesMostrarPeriodo= ($periodo==1) ? "":"display:none;";
$OperacionesMostrarRegistroIncidencias= ($registroIncidencias==1) ? "":"display:none;";
$OperacionesMostrarResumen= ($resumen==1) ? "":"display:none;";

$RHSeccionUsuario = ($permisoRH == 2) ? "display:none;":"";
$RH = ($permisoRH == 2) ? "":"display:none;";

if($admin['perfil_id'] == 1){
  $perfilShow = "";  
}elseif($admin['perfil_id'] == 6 AND $admin['nombre_planta'] == "XOCHIMILCO"){
  $perfilShow = "";  
}else{
  $perfilShow = "display:none;";  
}

// 123123
$permisoRHColaboradoresPropios = ($admin['perfil_id'] == 6 || $admin['perfil_id'] == 1) ? "" : "display: none;"; 
if($admin['perfil_id'] == 6){
  $nombrePerfil = "RH";
}elseif($admin['perfil_id'] == 1){
  $nombrePerfil = "ROOT";
}else{
  $operacionasignarIncentivos = ($asignarIncentivos==1) ? "" : "display:none;";
  $operacionregistroIncidencias = ($registroIncidencias==1) ? "" : "display:none;";
  $OperacionesMostrarProrrateo= ($prorrateo==1) ? "":"display:none;"; //123123123
  $operacionesresumen = ($resumen==1) ? "":"display:none;"; //123123123
  $operacionperiodo = ($periodo==1) ? "" : "display:none;";
}
$menu.=<<<html
                <li><a><i class="glyphicon glyphicon-wrench"></i> Operaciones <span class="fa fa-chevron-down"></span></a>
                  <ul class="nav child_menu">

                    <!-- Periodos -->
                    <li style="{$operacionperiodo}">
                      <a>Periodos <span class="fa fa-chevron-down"></span></a>
                      <ul class="nav child_menu">
                        <li><a>Abiertos <span class="fa fa-chevron-down"></span></a>
                          <ul class="nav child_menu">
                            <li><a href="/AdminPeriodo/abiertos/">Abiertos</a></li>
                            <li><a href="/AdminPeriodo/add/">Cargar Periodo</a></li>
                          </ul>
                        </li>
                        <li><a>Cerrados <span class="fa fa-chevron-down"></span></a>
                          <ul class="nav child_menu">
                            <li><a href="/AdminPeriodo/historicosSemanales/">Semanales</a></li>
                            <li><a href="/AdminPeriodo/historicosQuincenales/">Quincenales</a></li>
                          </ul>
                        </li>
                      </ul>
                    </li>
html;




//$mostrarIncentivos = ($asignarIncentivos==1) ? "":"display:none;";
$menu.=<<<html
                    <!-- INCENTIVOS -->
                    <li style="{$operacionasignarIncentivos}">
                      <a>Incentivos <span class="fa fa-chevron-down"></span></a>
                      <ul class="nav child_menu">
                        <li><a>Botes<span class="fa fa-chevron-down"></span></a>
                          <ul class="nav child_menu">
                            <li><a>Meta botes<span class="fa fa-chevron-down"></span></a>
                              <ul class="nav child_menu">
                                <li><a href="/Incentivo/botes/">Muestra</a></li>
                                <li><a href="/Incentivo/botesAdd/">Crear</a></li>
                              </ul>   
                            </li>
                            <li><a>Pago botes<span class="fa fa-chevron-down"></span></a>
                              <ul class="nav child_menu">
                                <li><a href="/Incentivo/pagoBotes/">Configuracion</a></li>
                              </ul>   
                            </li>
                            
                          </ul>
                        </li>
                        <li style="{$permisoRHColaboradoresPropios}"><a>{$nombrePerfil} Propios<span class="fa fa-chevron-down"></span></a>
                          <ul class="nav child_menu">
                            <li><a>Abiertos<span class="fa fa-chevron-down"></span></a>
                              <ul class="nav child_menu">
                                <li><a href="/Incentivo/propiosSemanales/">Semanales</a></li>
                                <!--li><a href="/Incentivo/propiosQuincenales/">Quincenales</a></li-->
                              </ul>
                            </li>
                            <li><a>Historicos<span class="fa fa-chevron-down"></span></a>
                              <ul class="nav child_menu">
                                <li><a href="/Incentivo/propiosSemanalesHistoricos/">Semanales</a></li>
                                <!--li><a href="/Incentivo/propiosQuincenalesHistoricos/">Quincenales</a></li-->
                              </ul>
                            </li>
                          </ul>
                        </li>
                        <li><a>Abiertos <span class="fa fa-chevron-down"></span></a>
                          <ul class="nav child_menu">
                            <li><a href="/Incentivo/semanales/">Semanales</a></li>
                            <!--li><a href="/Incentivo/quincenales/">Quincenales</a></li-->
                          </ul>
                        </li>
                        <li><a>Historicos <span class="fa fa-chevron-down"></span></a>
                          <ul class="nav child_menu">
                            <li><a href="/Incentivo/historicosSemanales/">Semanales</a></li>
                            <!--li><a href="/Incentivo/historicosQuincenales/">Quincenales</a></li-->
                          </ul>
                        </li>
                      </ul>
                    </li>

                    <!-- REGISTRO DE INCIDENCIAS -->
                    <li style="{$operacionregistroIncidencias}">
                      <a>Incidencias <span class="fa fa-chevron-down"></span></a>
                      <ul class="nav child_menu">
                        <li style="{$permisoRHColaboradoresPropios}"><a>{$nombrePerfil} Propios<span class="fa fa-chevron-down"></span></a>
                          <ul class="nav child_menu">
                            <li><a>Abiertos<span class="fa fa-chevron-down"></span></a>
                              <ul class="nav child_menu">
                                <li><a href="/Incidencia/propiosSemanales/">Semanales</a></li>
                                <li><a href="/Incidencia/propiosQuincenales/">Quincenales</a></li>
                              </ul>
                            </li>
                            <li><a>Historicos<span class="fa fa-chevron-down"></span></a>
                              <ul class="nav child_menu">
                                <li><a href="/Incidencia/propiosSemanalesHistoricos/">Semanales</a></li>
                                <li><a href="/Incidencia/propiosQuincenalesHistoricos/">Quincenales</a></li>
                              </ul>
                            </li>
                          </ul>
                        </li>
                        <li><a>Abiertas <span class="fa fa-chevron-down"></span></a>
                          <ul class="nav child_menu">
                            <li><a href="/Incidencia/semanales/">Semanales</a></li>
                            <li><a href="/Incidencia/quincenales/">Quincenales</a></li>
                          </ul>
                        </li>
                        <li><a>Historicas <span class="fa fa-chevron-down"></span></a>
                          <ul class="nav child_menu">
                            <li><a href="/Incidencia/historicosSemanales/">Semanales</a></li>
                            <li><a href="/Incidencia/historicosQuincenales/">Quincenales</a></li>
                          </ul>
                        </li>
                      </ul>
                    </li>

                    <!-- REGISTRO DE RESUMENES -->
                    <li style="{$operacionesresumen}">
                      <a>Resumen <span class="fa fa-chevron-down"></span></a>
                      <ul class="nav child_menu">
                        <li style="{$permisoRHColaboradoresPropios}"><a>{$nombrePerfil} Propios<span class="fa fa-chevron-down"></span></a>
                          <ul class="nav child_menu">
                            <li><a>Abiertos<span class="fa fa-chevron-down"></span></a>
                              <ul class="nav child_menu">
                                <li><a href="/Resumenes/propiosSemanales/">Semanales</a></li>
                                <li><a href="/Resumenes/propiosQuincenales/">Quincenales</a></li>
                              </ul>
                            </li>
                            <li><a>Historicos<span class="fa fa-chevron-down"></span></a>
                              <ul class="nav child_menu">
                                <li><a href="/Resumenes/propiosSemanalesHistoricos/">Semanales</a></li>
                                <li><a href="/Resumenes/propiosQuincenalesHistoricos/">Quincenales</a></li>
                              </ul>
                            </li>
                          </ul>
                        </li>
                        <li><a>Abiertos <span class="fa fa-chevron-down"></span></a>
                          <ul class="nav child_menu">
                            <li><a href="/Resumenes/semanales/">Semanales</a></li>
                            <li><a href="/Resumenes/quincenales/">Quincenales</a></li>
                          </ul>
                        </li>
                        <li><a>Historicas <span class="fa fa-chevron-down"></span></a>
                          <ul class="nav child_menu">
                            <li><a href="/Resumenes/historicosSemanales/">Semanales</a></li>
                            <li><a href="/Resumenes/historicosQuincenales/">Quincenales</a></li>
                          </ul>
                        </li>
                        <li><a href="/ResumenCargarManual/">Carga Manual <span class="fa fa-chevron-down"></span></a>
                        </li>
                      </ul>
                    </li>

                    <!-- Prorrateo -->
                    <li style="{$OperacionesMostrarProrrateo}">
                      <a>Prorrateo <span class="fa fa-chevron-down"></span></a>
                      <ul class="nav child_menu">
                        <li><a href="/ProrrateoT/SalarioMinino/">Salario Minimo</a></li>
                        <li><a>Abiertos <span class="fa fa-chevron-down"></span></a>
                          <ul class="nav child_menu">
                            <li><a>Semanales <span class="fa fa-chevron-down"></span></a>
                              <ul class="nav child_menu">
                                <li><a href="/ProrrateoT/calculo/Semanal/XOCHIMILCO/">Xochimilco</a></li>
                                <li><a href="/ProrrateoT/calculo/Semanal/VALLEJO/">Vallejo</a></li>
                                <li><a href="/ProrrateoT/calculo/Semanal/UNIDESH/">Unidesh</a></li>
                                <li><a href="/ProrrateoT/calculo/Semanal/GATSA/">Gatsa</a></li>
                                <!--li><a href="/ProrrateoT/calculo/Semanal/PRODUCCION/">Produccion</a></li-->
                              </ul>
                            </li>
                          </ul>
                        </li>


                        <li><a>Historicos <span class="fa fa-chevron-down"></span></a>
                          <ul class="nav child_menu">
                            <li><a>Semanales <span class="fa fa-chevron-down"></span></a>
                              <ul class="nav child_menu">
                                <li><a href="/ProrrateoT/calculoHistorico/Semanal/XOCHIMILCO/">Xochimilco</a></li>
                                <li><a href="/ProrrateoT/calculoHistorico/Semanal/VALLEJO/">Vallejo</a></li>
                                <li><a href="/ProrrateoT/calculoHistorico/Semanal/UNIDESH/">Unidesh</a></li>
                                <li><a href="/ProrrateoT/calculoHistorico/Semanal/GATSA/">Gatsa</a></li>
                                <!--li><a href="/ProrrateoT/calculoHistorico/Semanal/PRODUCCION/">Produccion</a></li-->
                              </ul>
                            </li>
                            <!--li><a>Quincenal <span class="fa fa-chevron-down"></span></a>
                              <ul class="nav child_menu">
                                <li><a href="/ProrrateoT/calculoHistorico/Quincenal/XOCHIMILCO/">Xochimilco</a></li>
                                <li><a href="/ProrrateoT/calculoHistorico/Quincenal/VALLEJO/">Vallejo</a></li>
                                <li><a href="/ProrrateoT/calculoHistorico/Quincenal/UNIDESH/">Unidesh</a></li>
                                <li><a href="/ProrrateoT/calculoHistorico/Quincenal/GATSA/">Gatsa</a></li>
                                <li><a href="/ProrrateoT/calculoHistorico/Quincenal/PRODUCCION/">Produccion</a></li>
                              </ul>
                            </li-->
                          </ul>
                        </li>
                      </ul>
                    </li>

                    <li style="{$perfilShow}"><a>Template <span class="fa fa-chevron-down"></span></a>
                      <ul class="nav child_menu">
                        <li><a href="/TemplateNoi/">Noi</a></li>
                      </ul>
                    </li>
                  </ul>
                </li>

                <!--li><a><i class="glyphicon glyphicon-user"></i> Cliente <span class="fa fa-chevron-down"></span></a>
                  <ul class="nav child_menu">
                    <li><a href="/Cliente/">Lista</a></li>
                    <li><a href="/Cliente/add">Agregar</a></li>
                  </ul>
                </li-->
html;

$global = ($permisosGlobales==1)?"":"display:none;";
$menu.=<<<html
                <li style="{$global}"><a><i class="glyphicon glyphicon-dashboard"></i> Utilerias <span class="fa fa-chevron-down"></span></a>
                  <ul class="nav child_menu">
                    <li><a>Administradores <span class="fa fa-chevron-down"></span></a>
                      <ul class="nav child_menu">
                        <li><a href="/Administradores">Mostrar</a></li>
                        <li><a href="/Administradores/add">Agregar</a></li>
                      </ul>
                    </li>
                    <li><a>Perfiles <span class="fa fa-chevron-down"></span></a>
                      <ul class="nav child_menu">
                        <li><a href="/Perfiles">Mostrar</a></li>
                        <li><a href="/Perfiles/add">Agregar</a></li>
                      </ul>
                    </li>
                    <li><a>Log <span class="fa fa-chevron-down"></span></a>
                      <ul class="nav child_menu">
                        <li><a href="/UtileriasLog/">Mostrar</a></li>
                      </ul>
                    </li>
                    <!--li><a>Base de Datos <span class="fa fa-chevron-down"></span></a>
                      <ul class="nav child_menu">
                        <li><a href="#">Mostrar</a></li>
                        <li><a href="#">Agregar</a></li>
                      </ul>
                    </li-->
                  </ul>
                </li>

              </ul>
            </div>
          </div>
        </div>
      </div>

      <div class="top_nav">
        <div class="nav_menu">
          <nav>
            <div class="nav toggle">
              <a id="menu_toggle"><i class="fa fa-bars"></i></a>
            </div>

            <ul class="nav navbar-nav navbar-right">
              <li class="">
                <a href="" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    Opciones
                    <span class=" fa fa-angle-down"></span>
                  </a>
                <ul class="dropdown-menu dropdown-usermenu pull-right">
                  <li><a href="/Usuario/"> Perfil</a></li>
                  <!--li>
                    <a href="">
                        <span class="badge bg-red pull-right">50%</span>
                        <span>Configuración</span>
                      </a>
                  </li>
                  <li><a href="">Ayuda</a></li-->
                  <li><a href="/Login/cerrarSession"><i class="fa fa-sign-out pull-right"></i>Cerrar Sesión</a></li>
                </ul>
              </li>
            </ul>
          </nav>
        </div>
      </div>

    </div>

html;

    return $header.$extra.$menu;
    }

    public function footer($extra = ''){
        $footer =<<<html
            <footer>
              <div class="pull-right">
                <!--a href="#">AG Alimentos de Granja</a-->
              </div>
              <div class="clearfix"></div>
            </footer>
            <!-- /footer content -->
          </div>
        <script src="/js/moment/moment.min.js"></script>
        <script src="/js/datepicker/scriptdatepicker.js"></script>
        <script src="/js/datepicker/datepicker2.js"></script>

        <!-- jQuery -->
        <script src="/js/jquery.min.js"></script>
        <!-- Bootstrap -->
        <script src="/js/bootstrap.min.js"></script>
        <script src="/js/bootstrap/bootstrap-switch.js"></script>
        <script src="/js/nprogress.js"></script>
        <!-- Custom Theme Scripts -->
        <script src="/js/custom.min.js"></script>

        <script src="/js/validate/jquery.validate.js"></script>
        <script src="/js/alertify/alertify.min.js"></script>
        <script src="/js/login.js"></script>

        <script src="/js/tabla/jquery.dataTables.min.js"></script>
        <script src="/js/tabla/dataTables.editor.min.js"></script>
        <script src="/js/tabla/dataTables.bootstrap.min.js"></script>
        <script src="/js/tabla/jquery.tablesorter.js"></script>

        <!-- EXTENCIONES DE DATATABLE() PARA EXPORTAR  -->
        <script src="https://cdn.datatables.net/buttons/1.4.2/js/dataTables.buttons.min.js" ></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js" ></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js" ></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js" ></script>
        <script src="//cdn.datatables.net/buttons/1.4.2/js/buttons.html5.min.js" ></script>

        <script src="/librerias/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
        <script type="text/javascript" src="/librerias/vintage_flip_clock/jquery.flipcountdown.js"></script>
  </body>
</html>
html;

    return $footer.$extra;
    }

}
