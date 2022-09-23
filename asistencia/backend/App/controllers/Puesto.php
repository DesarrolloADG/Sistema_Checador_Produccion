<?php
namespace App\controllers;
defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \App\controllers\Contenedor;
use \Core\Controller;
use \App\models\Puesto AS PuestoDao;

class Puesto extends Controller{

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
              $('#all').attr('action', '/Puesto/generarPDF/');
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });

            $("#export_excel").click(function(){
              $('#all').attr('action', '/Puesto/generarExcel/');
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });

            $("#delete").click(function(){
              var seleccionados = $("input[name='borrar[]']:checked").length;
              if(seleccionados>0){
                alertify.confirm('¿Segúro que desea eliminar lo seleccionado?', function(response){
                  if(response){
                    $('#all').attr('action', '/Puesto/delete');
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
      $editarHidden = (Controller::getPermisosUsuario($usuario, "seccion_puestos", 5)==1)?  "" : "style=\"display:none;\"";
      $eliminarHidden = (Controller::getPermisosUsuario($usuario, "seccion_puestos", 6)==1)? "" : "style=\"display:none;\"";
      $puestos = PuestoDao::getAll();
      $tabla= '';
      foreach ($puestos as $key => $value) {
        $tabla.=<<<html
                <tr>
                    <td><input type="checkbox" name="borrar[]" value="{$value['catalogo_puesto_id']}"/></td>
                    <td>{$value['nombre']}</td>
                    <td>{$value['descripcion']}</td>
                    <td>{$value['status']}</td>
                    <td class="center" >
                        <a href="/Puesto/edit/{$value['catalogo_puesto_id']}" {$editarHidden} type="submit" name="id" class="btn btn-primary"><span class="fa fa-pencil-square-o" style="color:white"></span> </a>
                        <a href="/Puesto/show/{$value['catalogo_puesto_id']}" type="submit" name="id_puesto" class="btn btn-success"><span class="glyphicon glyphicon-eye-open" style="color:white"></span> </a>
                    </td>
                </tr>
html;
      }
      $pdfHidden = (Controller::getPermisosUsuario($usuario, "seccion_puestos", 2)==1)?  "" : "style=\"display:none;\"";
      $excelHidden = (Controller::getPermisosUsuario($usuario, "seccion_puestos", 3)==1)? "" : "style=\"display:none;\"";
      $agregarHidden = (Controller::getPermisosUsuario($usuario, "seccion_puestos", 4)==1)? "" : "style=\"display:none;\"";
      View::set('pdfHidden',$pdfHidden);
      View::set('excelHidden',$excelHidden);
      View::set('editarHidden',$editarHidden);
      View::set('agregarHidden',$agregarHidden);
      View::set('eliminarHidden',$eliminarHidden);
      View::set('tabla',$tabla);
      View::set('header',$this->_contenedor->header($extraHeader));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("puesto_all");
    }

    public function add(){
      //MRR
      $numeroi = "";
      $num = 0;
      foreach (PuestoDao::getIncentivo() as $key => $value) {
        $num ++;
        $val = html_entity_decode($value['nombre']);
        $numeroi .= <<<html
        option{$num} = document.createElement('option');
        option{$num}.setAttribute('value','{$value['catalogo_incentivo_id']}');
        let optionv{$num} = document.createTextNode('{$val}');
        option{$num}.appendChild(optionv{$num});
        select.appendChild(option{$num});
html;
      }

      $extraFooter =<<<html
      <script>
        $(document).ready(function(){

          $.validator.addMethod("verificarNombrePuesto",
            function(value, element) {
              var result = false;
              $.ajax({
                type:"POST",
                async: false,
                url: "/Puesto/validarNombrePuesto", // script to validate in server side
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
                verificarNombrePuesto: true
              },
              descripcion:{
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
              status:{
                required: "Este campo es requerido"
              }
            }
          });//fin del jquery validate

          $("#btnCancel").click(function(){
            window.location.href = "/Puesto/";
          });//fin del btnAdd


          //MRR
          let vali = 0;
          let value = 0;

          $("#numeroi").change( function(){
            value = parseInt($(this).val());

            if(value != vali) {
              let rem = document.querySelector(".rem");
              rem.parentNode.removeChild(rem);
            }

            let remn = document.createElement('span');
            remn.classList.add('rem');

            for (i = 1 ; i < value; i ++) {
              let div = document.createElement('div');
              div.classList.add('form-group');
              let label = document.createElement('label');
              label.classList.add('control-label','col-md-3','col-sm-3','col-xs-12');
              let textl = document.createTextNode('Incentivo '+i);


              let subdiv = document.createElement('div');
              subdiv.classList.add('col-md-2','col-sm-3','col-xs-12');
              let select = document.createElement('select');
              select.classList.add('form-control');
              select.setAttribute('name','incentivo'+i);

              {$numeroi}

              let divp = document.createElement('div');
              divp.classList.add('col-md-3','col-sm-3','col-xs-12');
              let labelp = document.createElement('label');
              labelp.classList.add('control-label','col-md-1','col-sm-3','col-xs-12');
              let textlp = document.createTextNode('% '+i);

              let pdiv = document.createElement('div');
              pdiv.classList.add('col-md-1','col-sm-3','col-xs-12');
              let inputp = document.createElement('input');
              inputp.classList.add('form-control');
              inputp.setAttribute('type','number');
              inputp.setAttribute('name','por'+i);
              inputp.setAttribute('id','por'+i);
              inputp.setAttribute('max','100');
              inputp.setAttribute('value', 100/value);

              label.appendChild(textl);
              subdiv.appendChild(select);
              div.appendChild(label);
              div.appendChild(subdiv);
              labelp.appendChild(textlp);
              divp.appendChild(inputp);
              div.appendChild(labelp);
              div.appendChild(divp);
              remn.appendChild(div);
            }

            let div = document.createElement('div');
            div.classList.add('form-group');
            let label = document.createElement('label');
            label.classList.add('control-label','col-md-3','col-sm-3','col-xs-12');
            let textl = document.createTextNode('Incentivo '+i);


            let subdiv = document.createElement('div');
            subdiv.classList.add('col-md-2','col-sm-3','col-xs-12');
            let select = document.createElement('select');
            select.classList.add('form-control');
            select.setAttribute('name','incentivo'+i);

            {$numeroi}

            let divp = document.createElement('div');
            divp.classList.add('col-md-3','col-sm-3','col-xs-12');
            let labelp = document.createElement('label');
            labelp.classList.add('control-label','col-md-1','col-sm-3','col-xs-12');
            let textlp = document.createTextNode('% '+i);

            let pdiv = document.createElement('div');
            pdiv.classList.add('col-md-1','col-sm-3','col-xs-12');
            let inputp = document.createElement('input');
            inputp.classList.add('form-control');
            inputp.setAttribute('type','number');
            inputp.setAttribute('name','por'+i);
            inputp.setAttribute('id','por'+i);
            // inputp.setAttribute('readonly','');
            inputp.setAttribute('value', 100/value);

            label.appendChild(textl);
            subdiv.appendChild(select);
            div.appendChild(label);
            div.appendChild(subdiv);
            labelp.appendChild(textlp);
            divp.appendChild(inputp);
            div.appendChild(labelp);
            div.appendChild(divp);
            remn.appendChild(div);

            incentivo.appendChild(remn);
            vali = value;

            // let id = value - 1;
            $("#por1").change( function(){
              let spor = 0;
              let por = 0;
              for(p=1; p< value; p ++){
                por = parseInt($("#por"+p).val());
                spor = spor + por;
                // let r = ti * (por/100); //dinero
              }

              let rpor = 100 - spor;
              $("#por"+value).val(rpor);

              if(rpor <= 0){
                let dival = document.createElement('div');
                dival.classList.add('col-md-10','col-sm-3','col-xs-12');
                let labelal = document.createElement('label');
                labelal.setAttribute('id','alerta');
                labelal.setAttribute('style','color: red;');
                labelal.classList.add('control-label','col-md-10','col-sm-3','col-xs-12');
                let textal = document.createTextNode('No puede haber incentivos en 0 o Negativos');

                // let al = document.createElement('p');
                // al.classList.add('col-md-3','col-sm-3','col-xs-12');
                // let altext = document.createTextNode('No puede haber incentivos en 0 o Negativos');
                labelal.appendChild(textal);
                dival.appendChild(labelal);
                rem.appendChild(dival);
              }
              // else {
              //   let ale = document.querySelector("#alerta");
              //   alerta.parentNode.removeChild(ale);
              // }
            });

            $("#por2").change( function(){
              let spor = 0;
              let por = 0;
              for(p=1; p< value; p ++){
                por = parseInt($("#por"+p).val());
                spor = spor + por;
                // let r = ti * (por/100); //dinero
              }

              let rpor = 100 - spor;
              $("#por"+value).val(rpor);

              if(rpor <= 0){
                let dival = document.createElement('div');
                dival.classList.add('col-md-10','col-sm-3','col-xs-12');
                let labelal = document.createElement('label');
                labelal.setAttribute('id','alerta');
                labelal.setAttribute('style','color: red;');
                labelal.classList.add('control-label','col-md-10','col-sm-3','col-xs-12');
                let textal = document.createTextNode('No puede haber incentivos en 0 o Negativos');

                // let al = document.createElement('p');
                // al.classList.add('col-md-3','col-sm-3','col-xs-12');
                // let altext = document.createTextNode('No puede haber incentivos en 0 o Negativos');
                labelal.appendChild(textal);
                dival.appendChild(labelal);
                rem.appendChild(dival);
              }
              else {
                let ale = document.querySelector("#alerta");
                alerta.parentNode.removeChild(ale);
              }
            });

            $("#por3").change( function(){
              let spor = 0;
              let por = 0;
              for(p=1; p< value; p ++){
                por = parseInt($("#por"+p).val());
                spor = spor + por;
                // let r = ti * (por/100); //dinero
              }

              let rpor = 100 - spor;
              $("#por"+value).val(rpor);

              if(rpor <= 0){
                let dival = document.createElement('div');
                dival.classList.add('col-md-10','col-sm-3','col-xs-12');
                let labelal = document.createElement('label');
                labelal.setAttribute('id','alerta');
                labelal.setAttribute('style','color: red;');
                labelal.classList.add('control-label','col-md-10','col-sm-3','col-xs-12');
                let textal = document.createTextNode('No puede haber incentivos en 0 o Negativos');

                // let al = document.createElement('p');
                // al.classList.add('col-md-3','col-sm-3','col-xs-12');
                // let altext = document.createTextNode('No puede haber incentivos en 0 o Negativos');
                labelal.appendChild(textal);
                dival.appendChild(labelal);
                rem.appendChild(dival);
              }
              else {
                let ale = document.querySelector("#alerta");
                alerta.parentNode.removeChild(ale);
              }
            });

            $("#por4").change( function(){
              let spor = 0;
              let por = 0;
              for(p=1; p< value; p ++){
                por = parseInt($("#por"+p).val());
                spor = spor + por;
                // let r = ti * (por/100); //dinero
              }

              let rpor = 120 - spor;
              $("#por"+value).val(rpor);

              if(rpor <= 0){
                let dival = document.createElement('div');
                dival.classList.add('col-md-10','col-sm-3','col-xs-12');
                let labelal = document.createElement('label');
                labelal.setAttribute('id','alerta');
                labelal.setAttribute('style','color: red;');
                labelal.classList.add('control-label','col-md-10','col-sm-3','col-xs-12');
                let textal = document.createTextNode('No puede haber incentivos en 0 o Negativos');

                // let al = document.createElement('p');
                // al.classList.add('col-md-3','col-sm-3','col-xs-12');
                // let altext = document.createTextNode('No puede haber incentivos en 0 o Negativos');
                labelal.appendChild(textal);
                dival.appendChild(labelal);
                rem.appendChild(dival);
              }
              else {
                let ale = document.querySelector("#alerta");
                alerta.parentNode.removeChild(ale);
              }
            });

            $("#por5").change( function(){
              let spor = 0;
              let por = 0;
              for(p=1; p< value; p ++){
                por = parseInt($("#por"+p).val());
                spor = spor + por;
                // let r = ti * (por/100); //dinero
              }

              let rpor = 100 - spor;
              $("#por"+value).val(rpor);

              if(rpor <= 0){
                let dival = document.createElement('div');
                dival.classList.add('col-md-10','col-sm-3','col-xs-12');
                let labelal = document.createElement('label');
                labelal.setAttribute('id','alerta');
                labelal.setAttribute('style','color: red;');
                labelal.classList.add('control-label','col-md-10','col-sm-3','col-xs-12');
                let textal = document.createTextNode('No puede haber incentivos en 0 o Negativos');

                // let al = document.createElement('p');
                // al.classList.add('col-md-3','col-sm-3','col-xs-12');
                // let altext = document.createTextNode('No puede haber incentivos en 0 o Negativos');
                labelal.appendChild(textal);
                dival.appendChild(labelal);
                rem.appendChild(dival);
              }
              else {
                let ale = document.querySelector("#alerta");
                alerta.parentNode.removeChild(ale);
              }
            });

            $("#por6").change( function(){
              let spor = 0;
              let por = 0;
              for(p=1; p< value; p ++){
                por = parseInt($("#por"+p).val());
                spor = spor + por;
                // let r = ti * (por/100); //dinero
              }

              let rpor = 100 - spor;
              $("#por"+value).val(rpor);

              if(rpor <= 0){
                let dival = document.createElement('div');
                dival.classList.add('col-md-10','col-sm-3','col-xs-12');
                let labelal = document.createElement('label');
                labelal.setAttribute('id','alerta');
                labelal.setAttribute('style','color: red;');
                labelal.classList.add('control-label','col-md-10','col-sm-3','col-xs-12');
                let textal = document.createTextNode('No puede haber incentivos en 0 o Negativos');

                // let al = document.createElement('p');
                // al.classList.add('col-md-3','col-sm-3','col-xs-12');
                // let altext = document.createTextNode('No puede haber incentivos en 0 o Negativos');
                labelal.appendChild(textal);
                dival.appendChild(labelal);
                rem.appendChild(dival);
              }
              else {
                let ale = document.querySelector("#alerta");
                alerta.parentNode.removeChild(ale);
              }
            });

            $("#por7").change( function(){
              let spor = 0;
              let por = 0;
              for(p=1; p< value; p ++){
                por = parseInt($("#por"+p).val());
                spor = spor + por;
                // let r = ti * (por/100); //dinero
              }

              let rpor = 100 - spor;
              $("#por"+value).val(rpor);

              if(rpor <= 0){
                let dival = document.createElement('div');
                dival.classList.add('col-md-10','col-sm-3','col-xs-12');
                let labelal = document.createElement('label');
                labelal.setAttribute('id','alerta');
                labelal.setAttribute('style','color: red;');
                labelal.classList.add('control-label','col-md-10','col-sm-3','col-xs-12');
                let textal = document.createTextNode('No puede haber incentivos en 0 o Negativos');

                // let al = document.createElement('p');
                // al.classList.add('col-md-3','col-sm-3','col-xs-12');
                // let altext = document.createTextNode('No puede haber incentivos en 0 o Negativos');
                labelal.appendChild(textal);
                dival.appendChild(labelal);
                rem.appendChild(dival);
              }
              else {
                let ale = document.querySelector("#alerta");
                alerta.parentNode.removeChild(ale);
              }
            });

            $("#por8").change( function(){
              let spor = 0;
              let por = 0;
              for(p=1; p< value; p ++){
                por = parseInt($("#por"+p).val());
                spor = spor + por;
                // let r = ti * (por/100); //dinero
              }

              let rpor = 100 - spor;
              $("#por"+value).val(rpor);

              if(rpor <= 0){
                let dival = document.createElement('div');
                dival.classList.add('col-md-10','col-sm-3','col-xs-12');
                let labelal = document.createElement('label');
                labelal.setAttribute('id','alerta');
                labelal.setAttribute('style','color: red;');
                labelal.classList.add('control-label','col-md-10','col-sm-3','col-xs-12');
                let textal = document.createTextNode('No puede haber incentivos en 0 o Negativos');

                // let al = document.createElement('p');
                // al.classList.add('col-md-3','col-sm-3','col-xs-12');
                // let altext = document.createTextNode('No puede haber incentivos en 0 o Negativos');
                labelal.appendChild(textal);
                dival.appendChild(labelal);
                rem.appendChild(dival);
              }
              else {
                let ale = document.querySelector("#alerta");
                alerta.parentNode.removeChild(ale);
              }
            });

            $("#por9").change( function(){
              let spor = 0;
              let por = 0;
              for(p=1; p< value; p ++){
                por = parseInt($("#por"+p).val());
                spor = spor + por;
                // let r = ti * (por/100); //dinero
              }

              let rpor = 100 - spor;
              $("#por"+value).val(rpor);

              if(rpor <= 0){
                let dival = document.createElement('div');
                dival.classList.add('col-md-10','col-sm-3','col-xs-12');
                let labelal = document.createElement('label');
                labelal.setAttribute('id','alerta');
                labelal.setAttribute('style','color: red;');
                labelal.classList.add('control-label','col-md-10','col-sm-3','col-xs-12');
                let textal = document.createTextNode('No puede haber incentivos en 0 o Negativos');

                // let al = document.createElement('p');
                // al.classList.add('col-md-3','col-sm-3','col-xs-12');
                // let altext = document.createTextNode('No puede haber incentivos en 0 o Negativos');
                labelal.appendChild(textal);
                dival.appendChild(labelal);
                rem.appendChild(dival);
              }
              else {
                let ale = document.querySelector("#alerta");
                alerta.parentNode.removeChild(ale);
              }
            });

            $("#por10").change( function(){
              let spor = 0;
              let por = 0;
              for(p=1; p< value; p ++){
                por = parseInt($("#por"+p).val());
                spor = spor + por;
                // let r = ti * (por/100); //dinero
              }

              let rpor = 100 - spor;
              $("#por"+value).val(rpor);

              if(rpor <= 0){
                let dival = document.createElement('div');
                dival.classList.add('col-md-10','col-sm-3','col-xs-12');
                let labelal = document.createElement('label');
                labelal.setAttribute('id','alerta');
                labelal.setAttribute('style','color: red;');
                labelal.classList.add('control-label','col-md-10','col-sm-3','col-xs-12');
                let textal = document.createTextNode('No puede haber incentivos en 0 o Negativos');

                // let al = document.createElement('p');
                // al.classList.add('col-md-3','col-sm-3','col-xs-12');
                // let altext = document.createTextNode('No puede haber incentivos en 0 o Negativos');
                labelal.appendChild(textal);
                dival.appendChild(labelal);
                rem.appendChild(dival);
              }
              else {
                let ale = document.querySelector("#alerta");
                alerta.parentNode.removeChild(ale);
              }
            });
          });

        });//fin del document.ready
      </script>
html;
      $sStatus = "";
      foreach (PuestoDao::getStatus() as $key => $value) {
        $sStatus .= <<<html
        <option value="{$value['catalogo_status_id']}">{$value['nombre']}</option>
html;
      }

      View::set('sStatus',$sStatus);
      View::set('numeroi',$numeroi);
      View::set('header',$this->_contenedor->header(''));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("puesto_add");
    }
//MRR
public function incentivoPuesto(){

  $extraFooter =<<<html
  <script>
    $(document).ready(function(){
      $("#add").validate({
        messages:{
          quebrado:{
            required: "Este campo es requerido"
          },
          solido:{
            required: "Este campo es requerido"
          }
        }
      });//fin del jquery validate

      $("#btnCancel").click(function(){
        window.location.href = "/Principal/";
      });//fin del btnAdd

    });//fin del document.ready
  </script>
html;

  $ppi = PuestoDao::getpid();
  // $tipoq = PuestoDao::getEficiencia($ppi['ppi'],'QUEBRADO');
  $tipos = PuestoDao::getEficiencia($ppi['ppi'],'SOLIDOS');

  if ($tipoq = PuestoDao::getEficiencia($ppi['ppi'],'QUEBRADO')) {
    $quebrado = $tipoq['porcentaje'];
  }
  else {
    $quebrado = 'SIN ASIGNAR';
  }

  if ($tipos = PuestoDao::getEficiencia($ppi['ppi'],'SOLIDOS')) {
    $solido = $tipos['porcentaje'];
  }
  else {
    $solido = 'SIN ASIGNAR';
  }

  View::set('quebrado',$quebrado);
  View::set('solido',$solido);
  View::set('header',$this->_contenedor->header(''));
  View::set('footer',$this->_contenedor->footer($extraFooter));
  View::render("puesto_incentivo");
}

//MRR
public function puestoIncentivoEdit(){
  $quebrado = MasterDom::getData('quebrado');
  $solido = MasterDom::getData('solido');

  if ($quebrado > 98.00) {
    $quebrado = 100;
  }
  else if ($quebrado > 96.00 && $quebrado <= 98.00) {
    $quebrado = 85;
  }
  else if ($quebrado > 94.01 && $quebrado <= 96.00) {
    $quebrado = 70;
  }
  else if ($quebrado <= 94.00) {
    $quebrado = 0;
  }

  if ($solido >= 44.00) {
    $solido = 100;
  }
  else{
    $solido = 0;
  }

  $ppi = PuestoDao::ultimoPeriodo('SEMANAL');
  $ppi = $ppi['ppi'];


  $eficienciaq = new \stdClass();
  $eficienciaq->_id_periodo_prorrateo = $ppi;
  $eficienciaq->_porcentaje = MasterDom::getData('quebrado');
  $eficienciaq->_tipo = 'QUEBRADO';
  PuestoDao::insertEficiencia($eficienciaq);

  $eficiencias = new \stdClass();
  $eficiencias->_id_periodo_prorrateo = $ppi;
  $eficiencias->_porcentaje = MasterDom::getData('solido');
  $eficiencias->_tipo = 'SOLIDOS';
  PuestoDao::insertEficiencia($eficiencias);

  foreach (PuestoDao::colaboradores() as $key => $v) {
    $data = new \stdClass();
    $cid = $v['cci'];
    $p = $v['cpi'];
    $puesto = PuestoDao::getById($p);
    $totalv = $puesto['total_valor_incentivos'];

    for ($i=1; $i <= 10; $i++) {
      $incentivo = $puesto['incentivo'.$i];
      if($incentivo == 59){
        $cii = 59;
        $por = $puesto['porcentaje'.$i];
        // $cantidad = $totalv * ($por/100);
        $cantidad = $totalv * ($por/100);
      }
      else if($incentivo == 64){
        $cii = 64;
        $por = $puesto['porcentaje'.$i];
        // $cantidad = $totalv * ($por/100);
        $cantidad = $totalv * ($por/100);
      }
      else {
        $cii = 0;
      }

      if ($cii == 59) {
        $iai = PuestoDao::incentivoAsignado($ppi,$cid,$cii);
        $iai = $iai['iai'];
        $data->_iai = $iai;
        $cantidad = $cantidad * ($quebrado/100);
        $data->_cantidad = $cantidad;
        // print_r($data);
        // echo '<br/>';
        $ins = PuestoDao::updIncentivoA($data);
      }

      if ($cii == 64) {
        $iai = PuestoDao::incentivoAsignado($ppi,$cid,$cii);
        $iai = $iai['iai'];
        $data->_iai = $iai;
        $cantidad = $cantidad * ($solido/100);
        $data->_cantidad = $cantidad;
        // print_r($data);
        // echo '<br/>';
        $ins = PuestoDao::updIncentivoA($data);
      }
    }
  }
  // exit;
  $id = 1;
  if($id >= 1)
    $this->alerta($id,'up');
  else
    $this->alerta($id,'error');
}


public function edit($id){
  //MRR

  $puesto = PuestoDao::getById($id);
  if($incentivo1 = $puesto['incentivo1']) {
    $incentivo1 = $puesto['incentivo1'];
    $porcentaje1 = $puesto['porcentaje1'];
    $incentivo2 = $puesto['incentivo2'];
    $porcentaje2 = $puesto['porcentaje2'];
    $incentivo3 = $puesto['incentivo3'];
    $porcentaje3 = $puesto['porcentaje3'];
    $incentivo4 = $puesto['incentivo4'];
    $porcentaje4 = $puesto['porcentaje4'];
    $incentivo5 = $puesto['incentivo5'];
    $porcentaje5 = $puesto['porcentaje5'];
    $incentivo6 = $puesto['incentivo6'];
    $porcentaje6 = $puesto['porcentaje6'];
    $incentivo7 = $puesto['incentivo7'];
    $porcentaje7 = $puesto['porcentaje7'];
    $incentivo8 = $puesto['incentivo8'];
    $porcentaje8 = $puesto['porcentaje8'];
    $incentivo9 = $puesto['incentivo9'];
    $porcentaje9 = $puesto['porcentaje9'];
    $incentivo10 = $puesto['incentivo10'];
    $porcentaje10 = $puesto['porcentaje10'];

    $nombre1 = PuestoDao::incentivop($incentivo1);
    $nombre1 = $nombre1['nombre'];
    $nombre2 = PuestoDao::incentivop($incentivo2);
    $nombre2 = $nombre2['nombre'];
    $nombre3 = PuestoDao::incentivop($incentivo3);
    $nombre3 = $nombre3['nombre'];
    $nombre4 = PuestoDao::incentivop($incentivo4);
    $nombre4 = $nombre4['nombre'];
    $nombre5 = PuestoDao::incentivop($incentivo5);
    $nombre5 = $nombre5['nombre'];
    $nombre6 = PuestoDao::incentivop($incentivo6);
    $nombre6 = $nombre6['nombre'];
    $nombre7 = PuestoDao::incentivop($incentivo7);
    $nombre7 = $nombre7['nombre'];
    $nombre8 = PuestoDao::incentivop($incentivo8);
    $nombre8 = $nombre8['nombre'];
    $nombre9 = PuestoDao::incentivop($incentivo9);
    $nombre9 = $nombre9['nombre'];
    $nombre10 = PuestoDao::incentivop($incentivo10);
    $nombre10 = $nombre10['nombre'];
  }
  else {
    $incentivo1 = 0;
    $porcentaje1 = 0;
    $incentivo2 = 0;
    $porcentaje2 = 0;
    $incentivo3 = 0;
    $porcentaje3 = 0;
    $incentivo4 = 0;
    $porcentaje4 = 0;
    $incentivo5 = 0;
    $porcentaje5 = 0;
    $incentivo6 = 0;
    $porcentaje6 = 0;
    $incentivo7 = 0;
    $porcentaje7 = 0;
    $incentivo8 = 0;
    $porcentaje8 = 0;
    $incentivo9 = 0;
    $porcentaje9 = 0;
    $incentivo10 = 0;
    $porcentaje10 = 0;

    $nombre1 = '';
    $nombre2 = '';
    $nombre3 = '';
    $nombre4 = '';
    $nombre5 = '';
    $nombre6 = '';
    $nombre7 = '';
    $nombre8 = '';
    $nombre9 = '';
    $nombre10 = '';
  }

  $numeroi = "";
  $num = 0;
  foreach (PuestoDao::getIncentivo() as $key => $value) {
    $num ++;
    $val = html_entity_decode($value['nombre']);
    $numeroi .= <<<html
    option{$num} = document.createElement('option');
    option{$num}.setAttribute('value','{$value['catalogo_incentivo_id']}');
    let optionv{$num} = document.createTextNode('{$val}');
    option{$num}.appendChild(optionv{$num});
    select.appendChild(option{$num});
html;
  }
  $extraFooter =<<<html
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
          status:{
            required: "Este campo es requerido"
          }
        }
      });//fin del jquery validate

      $("#btnCancel").click(function(){
        window.location.href = "/Puesto/";
      });//fin del btnAdd

      //MRR

      let rem = document.querySelector(".rem");
      rem.parentNode.removeChild(rem);

      let remn = document.createElement('span');
      remn.classList.add('rem');

      let value = {$puesto['numero_incentivos']};
      console.log(value);
      if (value >= 1) {
      }
      let vi = 0;
      let vn = "";
      let vp = 0;
      for (let i = 1; i <= value; i ++) {
        if (i==1) {
          vi = {$incentivo1};
          vn = '{$nombre1}';
          vp = {$porcentaje1};
        }
        else if (i==2) {
          vi = {$incentivo2};
          vn = '{$nombre2}';
          vp = {$porcentaje2};
        }
        else if (i==3) {
          vi = {$incentivo3};
          vn = '{$nombre3}';
          vp = {$porcentaje3};
        }
        else if (i==4) {
          vi = {$incentivo4};
          vn = '{$nombre4}';
          vp = {$porcentaje4};
        }
        else if (i==5) {
          vi = {$incentivo5};
          vn = '{$nombre5}';
          vp = {$porcentaje5};
        }
        else if (i==6) {
          vi = {$incentivo6};
          vn = '{$nombre6}';
          vp = {$porcentaje6};
        }
        else if (i==7) {
          vi = {$incentivo7};
          vn = '{$nombre7}';
          vp = {$porcentaje7};
        }
        else if (i==8) {
          vi = {$incentivo8};
          vn = '{$nombre8}';
          vp = {$porcentaje8};
        }
        else if (i==9) {
          vi = {$incentivo9};
          vn = '{$nombre9}';
          vp = {$porcentaje9};
        }
        else if (i==10) {
          vi = {$incentivo10};
          vn = '{$nombre10}';
          vp = {$porcentaje10};
        }

        let div = document.createElement('div');
        div.classList.add('form-group');
        let label = document.createElement('label');
        label.classList.add('control-label','col-md-3','col-sm-3','col-xs-12');
        let textl = document.createTextNode('Incentivo '+i);


        let subdiv = document.createElement('div');
        subdiv.classList.add('col-md-2','col-sm-3','col-xs-12');
        let select = document.createElement('select');
        select.classList.add('form-control');
        select.setAttribute('name','incentivo'+i);
        option = document.createElement('option');
        option.setAttribute('value',vi);
        option.setAttribute('hidden','');
        let optionv = document.createTextNode(vn);
        option.appendChild(optionv);
        select.appendChild(option);

        {$numeroi}

        let divp = document.createElement('div');
        divp.classList.add('col-md-3','col-sm-3','col-xs-12');
        let labelp = document.createElement('label');
        labelp.classList.add('control-label','col-md-1','col-sm-3','col-xs-12');
        let textlp = document.createTextNode('% '+i);

        let pdiv = document.createElement('div');
        pdiv.classList.add('col-md-1','col-sm-3','col-xs-12');
        let inputp = document.createElement('input');
        inputp.classList.add('form-control');
        inputp.setAttribute('type','number');
        inputp.setAttribute('name','por'+i);
        inputp.setAttribute('id','por'+i);
        inputp.setAttribute('max','100');
        inputp.setAttribute('value', vp);

        label.appendChild(textl);
        subdiv.appendChild(select);
        div.appendChild(label);
        div.appendChild(subdiv);
        labelp.appendChild(textlp);
        divp.appendChild(inputp);
        div.appendChild(labelp);
        div.appendChild(divp);
        rem.appendChild(div);
      }
      incentivo.appendChild(rem);

      $("#por1").change( function(){
        let spor = 0;
        let por = 0;
        for(p=1; p< value; p ++){
          por = parseInt($("#por"+p).val());
          spor = spor + por;
          // let r = ti * (por/100); //dinero
        }

        let rpor = 100 - spor;
        $("#por"+value).val(rpor);

        if(rpor <= 0){
          let dival = document.createElement('div');
          dival.classList.add('col-md-10','col-sm-3','col-xs-12');
          let labelal = document.createElement('label');
          labelal.setAttribute('id','alerta');
          labelal.setAttribute('style','color: red;');
          labelal.classList.add('control-label','col-md-10','col-sm-3','col-xs-12');
          let textal = document.createTextNode('No puede haber incentivos en 0 o Negativos');

          // let al = document.createElement('p');
          // al.classList.add('col-md-3','col-sm-3','col-xs-12');
          // let altext = document.createTextNode('No puede haber incentivos en 0 o Negativos');
          labelal.appendChild(textal);
          dival.appendChild(labelal);
          rem.appendChild(dival);
        }
        else {
          let ale = document.querySelector("#alerta");
          alerta.parentNode.removeChild(ale);
        }
      });

      $("#por2").change( function(){
        let spor = 0;
        let por = 0;
        for(p=1; p< value; p ++){
          por = parseInt($("#por"+p).val());
          spor = spor + por;
          // let r = ti * (por/100); //dinero
        }

        let rpor = 100 - spor;
        $("#por"+value).val(rpor);

        if(rpor <= 0){
          let dival = document.createElement('div');
          dival.classList.add('col-md-10','col-sm-3','col-xs-12');
          let labelal = document.createElement('label');
          labelal.setAttribute('id','alerta');
          labelal.setAttribute('style','color: red;');
          labelal.classList.add('control-label','col-md-10','col-sm-3','col-xs-12');
          let textal = document.createTextNode('No puede haber incentivos en 0 o Negativos');

          // let al = document.createElement('p');
          // al.classList.add('col-md-3','col-sm-3','col-xs-12');
          // let altext = document.createTextNode('No puede haber incentivos en 0 o Negativos');
          labelal.appendChild(textal);
          dival.appendChild(labelal);
          rem.appendChild(dival);
        }
        else {
          let ale = document.querySelector("#alerta");
          alerta.parentNode.removeChild(ale);
        }
      });

      $("#por3").change( function(){
        let spor = 0;
        let por = 0;
        for(p=1; p< value; p ++){
          por = parseInt($("#por"+p).val());
          spor = spor + por;
          // let r = ti * (por/100); //dinero
        }

        let rpor = 100 - spor;
        $("#por"+value).val(rpor);

        if(rpor <= 0){
          let dival = document.createElement('div');
          dival.classList.add('col-md-10','col-sm-3','col-xs-12');
          let labelal = document.createElement('label');
          labelal.setAttribute('id','alerta');
          labelal.setAttribute('style','color: red;');
          labelal.classList.add('control-label','col-md-10','col-sm-3','col-xs-12');
          let textal = document.createTextNode('No puede haber incentivos en 0 o Negativos');

          // let al = document.createElement('p');
          // al.classList.add('col-md-3','col-sm-3','col-xs-12');
          // let altext = document.createTextNode('No puede haber incentivos en 0 o Negativos');
          labelal.appendChild(textal);
          dival.appendChild(labelal);
          rem.appendChild(dival);
        }
        else {
          let ale = document.querySelector("#alerta");
          alerta.parentNode.removeChild(ale);
        }
      });

      $("#por4").change( function(){
        let spor = 0;
        let por = 0;
        for(p=1; p< value; p ++){
          por = parseInt($("#por"+p).val());
          spor = spor + por;
          // let r = ti * (por/100); //dinero
        }

        let rpor = 100 - spor;
        $("#por"+value).val(rpor);

        if(rpor <= 0){
          let dival = document.createElement('div');
          dival.classList.add('col-md-10','col-sm-3','col-xs-12');
          let labelal = document.createElement('label');
          labelal.setAttribute('id','alerta');
          labelal.setAttribute('style','color: red;');
          labelal.classList.add('control-label','col-md-10','col-sm-3','col-xs-12');
          let textal = document.createTextNode('No puede haber incentivos en 0 o Negativos');

          // let al = document.createElement('p');
          // al.classList.add('col-md-3','col-sm-3','col-xs-12');
          // let altext = document.createTextNode('No puede haber incentivos en 0 o Negativos');
          labelal.appendChild(textal);
          dival.appendChild(labelal);
          rem.appendChild(dival);
        }
        else {
          let ale = document.querySelector("#alerta");
          alerta.parentNode.removeChild(ale);
        }
      });

      $("#por5").change( function(){
        let spor = 0;
        let por = 0;
        for(p=1; p< value; p ++){
          por = parseInt($("#por"+p).val());
          spor = spor + por;
          // let r = ti * (por/100); //dinero
        }

        let rpor = 100 - spor;
        $("#por"+value).val(rpor);

        if(rpor <= 0){
          let dival = document.createElement('div');
          dival.classList.add('col-md-10','col-sm-3','col-xs-12');
          let labelal = document.createElement('label');
          labelal.setAttribute('id','alerta');
          labelal.setAttribute('style','color: red;');
          labelal.classList.add('control-label','col-md-10','col-sm-3','col-xs-12');
          let textal = document.createTextNode('No puede haber incentivos en 0 o Negativos');

          // let al = document.createElement('p');
          // al.classList.add('col-md-3','col-sm-3','col-xs-12');
          // let altext = document.createTextNode('No puede haber incentivos en 0 o Negativos');
          labelal.appendChild(textal);
          dival.appendChild(labelal);
          rem.appendChild(dival);
        }
        else {
          let ale = document.querySelector("#alerta");
          alerta.parentNode.removeChild(ale);
        }
      });

      $("#por6").change( function(){
        let spor = 0;
        let por = 0;
        for(p=1; p< value; p ++){
          por = parseInt($("#por"+p).val());
          spor = spor + por;
          // let r = ti * (por/100); //dinero
        }

        let rpor = 100 - spor;
        $("#por"+value).val(rpor);

        if(rpor <= 0){
          let dival = document.createElement('div');
          dival.classList.add('col-md-10','col-sm-3','col-xs-12');
          let labelal = document.createElement('label');
          labelal.setAttribute('id','alerta');
          labelal.setAttribute('style','color: red;');
          labelal.classList.add('control-label','col-md-10','col-sm-3','col-xs-12');
          let textal = document.createTextNode('No puede haber incentivos en 0 o Negativos');

          // let al = document.createElement('p');
          // al.classList.add('col-md-3','col-sm-3','col-xs-12');
          // let altext = document.createTextNode('No puede haber incentivos en 0 o Negativos');
          labelal.appendChild(textal);
          dival.appendChild(labelal);
          rem.appendChild(dival);
        }
        else {
          let ale = document.querySelector("#alerta");
          alerta.parentNode.removeChild(ale);
        }
      });

      $("#por7").change( function(){
        let spor = 0;
        let por = 0;
        for(p=1; p< value; p ++){
          por = parseInt($("#por"+p).val());
          spor = spor + por;
          // let r = ti * (por/100); //dinero
        }

        let rpor = 100 - spor;
        $("#por"+value).val(rpor);

        if(rpor <= 0){
          let dival = document.createElement('div');
          dival.classList.add('col-md-10','col-sm-3','col-xs-12');
          let labelal = document.createElement('label');
          labelal.setAttribute('id','alerta');
          labelal.setAttribute('style','color: red;');
          labelal.classList.add('control-label','col-md-10','col-sm-3','col-xs-12');
          let textal = document.createTextNode('No puede haber incentivos en 0 o Negativos');

          // let al = document.createElement('p');
          // al.classList.add('col-md-3','col-sm-3','col-xs-12');
          // let altext = document.createTextNode('No puede haber incentivos en 0 o Negativos');
          labelal.appendChild(textal);
          dival.appendChild(labelal);
          rem.appendChild(dival);
        }
        else {
          let ale = document.querySelector("#alerta");
          alerta.parentNode.removeChild(ale);
        }
      });

      $("#por8").change( function(){
        let spor = 0;
        let por = 0;
        for(p=1; p< value; p ++){
          por = parseInt($("#por"+p).val());
          spor = spor + por;
          // let r = ti * (por/100); //dinero
        }

        let rpor = 100 - spor;
        $("#por"+value).val(rpor);

        if(rpor <= 0){
          let dival = document.createElement('div');
          dival.classList.add('col-md-10','col-sm-3','col-xs-12');
          let labelal = document.createElement('label');
          labelal.setAttribute('id','alerta');
          labelal.setAttribute('style','color: red;');
          labelal.classList.add('control-label','col-md-10','col-sm-3','col-xs-12');
          let textal = document.createTextNode('No puede haber incentivos en 0 o Negativos');

          // let al = document.createElement('p');
          // al.classList.add('col-md-3','col-sm-3','col-xs-12');
          // let altext = document.createTextNode('No puede haber incentivos en 0 o Negativos');
          labelal.appendChild(textal);
          dival.appendChild(labelal);
          rem.appendChild(dival);
        }
        else {
          let ale = document.querySelector("#alerta");
          alerta.parentNode.removeChild(ale);
        }
      });

      $("#por9").change( function(){
        let spor = 0;
        let por = 0;
        for(p=1; p< value; p ++){
          por = parseInt($("#por"+p).val());
          spor = spor + por;
          // let r = ti * (por/100); //dinero
        }

        let rpor = 100 - spor;
        $("#por"+value).val(rpor);

        if(rpor <= 0){
          let dival = document.createElement('div');
          dival.classList.add('col-md-10','col-sm-3','col-xs-12');
          let labelal = document.createElement('label');
          labelal.setAttribute('id','alerta');
          labelal.setAttribute('style','color: red;');
          labelal.classList.add('control-label','col-md-10','col-sm-3','col-xs-12');
          let textal = document.createTextNode('No puede haber incentivos en 0 o Negativos');

          // let al = document.createElement('p');
          // al.classList.add('col-md-3','col-sm-3','col-xs-12');
          // let altext = document.createTextNode('No puede haber incentivos en 0 o Negativos');
          labelal.appendChild(textal);
          dival.appendChild(labelal);
          rem.appendChild(dival);
        }
        else {
          let ale = document.querySelector("#alerta");
          alerta.parentNode.removeChild(ale);
        }
      });

      $("#por10").change( function(){
        let spor = 0;
        let por = 0;
        for(p=1; p< value; p ++){
          por = parseInt($("#por"+p).val());
          spor = spor + por;
          // let r = ti * (por/100); //dinero
        }

        let rpor = 100 - spor;
        $("#por"+value).val(rpor);

        if(rpor <= 0){
          let dival = document.createElement('div');
          dival.classList.add('col-md-10','col-sm-3','col-xs-12');
          let labelal = document.createElement('label');
          labelal.setAttribute('id','alerta');
          labelal.setAttribute('style','color: red;');
          labelal.classList.add('control-label','col-md-10','col-sm-3','col-xs-12');
          let textal = document.createTextNode('No puede haber incentivos en 0 o Negativos');

          // let al = document.createElement('p');
          // al.classList.add('col-md-3','col-sm-3','col-xs-12');
          // let altext = document.createTextNode('No puede haber incentivos en 0 o Negativos');
          labelal.appendChild(textal);
          dival.appendChild(labelal);
          rem.appendChild(dival);
        }
        else {
          let ale = document.querySelector("#alerta");
          alerta.parentNode.removeChild(ale);
        }
      });
      let vali = 0;

      $("#numeroi").change( function(){
        value = parseInt($(this).val());

        if(value != vali) {
          let rem = document.querySelector(".rem");
          rem.parentNode.removeChild(rem);
        }

        let remn = document.createElement('span');
        remn.classList.add('rem');

        for (i = 1 ; i < value; i ++) {
          let div = document.createElement('div');
          div.classList.add('form-group');
          let label = document.createElement('label');
          label.classList.add('control-label','col-md-3','col-sm-3','col-xs-12');
          let textl = document.createTextNode('Incentivo '+i);


          let subdiv = document.createElement('div');
          subdiv.classList.add('col-md-2','col-sm-3','col-xs-12');
          let select = document.createElement('select');
          select.classList.add('form-control');
          select.setAttribute('name','incentivo'+i);

          {$numeroi}

          let divp = document.createElement('div');
          divp.classList.add('col-md-3','col-sm-3','col-xs-12');
          let labelp = document.createElement('label');
          labelp.classList.add('control-label','col-md-1','col-sm-3','col-xs-12');
          let textlp = document.createTextNode('% '+i);

          let pdiv = document.createElement('div');
          pdiv.classList.add('col-md-1','col-sm-3','col-xs-12');
          let inputp = document.createElement('input');
          inputp.classList.add('form-control');
          inputp.setAttribute('type','number');
          inputp.setAttribute('name','por'+i);
          inputp.setAttribute('id','por'+i);
          inputp.setAttribute('max','100');
          inputp.setAttribute('value', 100/value);

          label.appendChild(textl);
          subdiv.appendChild(select);
          div.appendChild(label);
          div.appendChild(subdiv);
          labelp.appendChild(textlp);
          divp.appendChild(inputp);
          div.appendChild(labelp);
          div.appendChild(divp);
          remn.appendChild(div);
        }

        let div = document.createElement('div');
        div.classList.add('form-group');
        let label = document.createElement('label');
        label.classList.add('control-label','col-md-3','col-sm-3','col-xs-12');
        let textl = document.createTextNode('Incentivo '+i);


        let subdiv = document.createElement('div');
        subdiv.classList.add('col-md-2','col-sm-3','col-xs-12');
        let select = document.createElement('select');
        select.classList.add('form-control');
        select.setAttribute('name','incentivo'+i);

        {$numeroi}

        let divp = document.createElement('div');
        divp.classList.add('col-md-3','col-sm-3','col-xs-12');
        let labelp = document.createElement('label');
        labelp.classList.add('control-label','col-md-1','col-sm-3','col-xs-12');
        let textlp = document.createTextNode('% '+i);

        let pdiv = document.createElement('div');
        pdiv.classList.add('col-md-1','col-sm-3','col-xs-12');
        let inputp = document.createElement('input');
        inputp.classList.add('form-control');
        inputp.setAttribute('type','number');
        inputp.setAttribute('name','por'+i);
        inputp.setAttribute('id','por'+i);
        inputp.setAttribute('readonly','');
        inputp.setAttribute('value', 100/value);

        label.appendChild(textl);
        subdiv.appendChild(select);
        div.appendChild(label);
        div.appendChild(subdiv);
        labelp.appendChild(textlp);
        divp.appendChild(inputp);
        div.appendChild(labelp);
        div.appendChild(divp);
        remn.appendChild(div);

        incentivo.appendChild(remn);
        vali = value;

        // let id = value - 1;
        $("#por1").change( function(){
          let spor = 0;
          let por = 0;
          for(p=1; p< value; p ++){
            por = parseInt($("#por"+p).val());
            spor = spor + por;
            // let r = ti * (por/100); //dinero
          }

          let rpor = 100 - spor;
          $("#por"+value).val(rpor);

          if(rpor <= 0){
            let dival = document.createElement('div');
            dival.classList.add('col-md-10','col-sm-3','col-xs-12');
            let labelal = document.createElement('label');
            labelal.setAttribute('id','alerta');
            labelal.setAttribute('style','color: red;');
            labelal.classList.add('control-label','col-md-10','col-sm-3','col-xs-12');
            let textal = document.createTextNode('No puede haber incentivos en 0 o Negativos');

            // let al = document.createElement('p');
            // al.classList.add('col-md-3','col-sm-3','col-xs-12');
            // let altext = document.createTextNode('No puede haber incentivos en 0 o Negativos');
            labelal.appendChild(textal);
            dival.appendChild(labelal);
            rem.appendChild(dival);
          }
          else {
            let ale = document.querySelector("#alerta");
            alerta.parentNode.removeChild(ale);
          }
        });

        $("#por2").change( function(){
          let spor = 0;
          let por = 0;
          for(p=1; p< value; p ++){
            por = parseInt($("#por"+p).val());
            spor = spor + por;
            // let r = ti * (por/100); //dinero
          }

          let rpor = 100 - spor;
          $("#por"+value).val(rpor);

          if(rpor <= 0){
            let dival = document.createElement('div');
            dival.classList.add('col-md-10','col-sm-3','col-xs-12');
            let labelal = document.createElement('label');
            labelal.setAttribute('id','alerta');
            labelal.setAttribute('style','color: red;');
            labelal.classList.add('control-label','col-md-10','col-sm-3','col-xs-12');
            let textal = document.createTextNode('No puede haber incentivos en 0 o Negativos');

            // let al = document.createElement('p');
            // al.classList.add('col-md-3','col-sm-3','col-xs-12');
            // let altext = document.createTextNode('No puede haber incentivos en 0 o Negativos');
            labelal.appendChild(textal);
            dival.appendChild(labelal);
            rem.appendChild(dival);
          }
          else {
            let ale = document.querySelector("#alerta");
            alerta.parentNode.removeChild(ale);
          }
        });

        $("#por3").change( function(){
          let spor = 0;
          let por = 0;
          for(p=1; p< value; p ++){
            por = parseInt($("#por"+p).val());
            spor = spor + por;
            // let r = ti * (por/100); //dinero
          }

          let rpor = 100 - spor;
          $("#por"+value).val(rpor);

          if(rpor <= 0){
            let dival = document.createElement('div');
            dival.classList.add('col-md-10','col-sm-3','col-xs-12');
            let labelal = document.createElement('label');
            labelal.setAttribute('id','alerta');
            labelal.setAttribute('style','color: red;');
            labelal.classList.add('control-label','col-md-10','col-sm-3','col-xs-12');
            let textal = document.createTextNode('No puede haber incentivos en 0 o Negativos');

            // let al = document.createElement('p');
            // al.classList.add('col-md-3','col-sm-3','col-xs-12');
            // let altext = document.createTextNode('No puede haber incentivos en 0 o Negativos');
            labelal.appendChild(textal);
            dival.appendChild(labelal);
            rem.appendChild(dival);
          }
          else {
            let ale = document.querySelector("#alerta");
            alerta.parentNode.removeChild(ale);
          }
        });

        $("#por4").change( function(){
          let spor = 0;
          let por = 0;
          for(p=1; p< value; p ++){
            por = parseInt($("#por"+p).val());
            spor = spor + por;
            // let r = ti * (por/100); //dinero
          }

          let rpor = 100 - spor;
          $("#por"+value).val(rpor);

          if(rpor <= 0){
            let dival = document.createElement('div');
            dival.classList.add('col-md-10','col-sm-3','col-xs-12');
            let labelal = document.createElement('label');
            labelal.setAttribute('id','alerta');
            labelal.setAttribute('style','color: red;');
            labelal.classList.add('control-label','col-md-10','col-sm-3','col-xs-12');
            let textal = document.createTextNode('No puede haber incentivos en 0 o Negativos');

            // let al = document.createElement('p');
            // al.classList.add('col-md-3','col-sm-3','col-xs-12');
            // let altext = document.createTextNode('No puede haber incentivos en 0 o Negativos');
            labelal.appendChild(textal);
            dival.appendChild(labelal);
            rem.appendChild(dival);
          }
          else {
            let ale = document.querySelector("#alerta");
            alerta.parentNode.removeChild(ale);
          }
        });

        $("#por5").change( function(){
          let spor = 0;
          let por = 0;
          for(p=1; p< value; p ++){
            por = parseInt($("#por"+p).val());
            spor = spor + por;
            // let r = ti * (por/100); //dinero
          }

          let rpor = 100 - spor;
          $("#por"+value).val(rpor);

          if(rpor <= 0){
            let dival = document.createElement('div');
            dival.classList.add('col-md-10','col-sm-3','col-xs-12');
            let labelal = document.createElement('label');
            labelal.setAttribute('id','alerta');
            labelal.setAttribute('style','color: red;');
            labelal.classList.add('control-label','col-md-10','col-sm-3','col-xs-12');
            let textal = document.createTextNode('No puede haber incentivos en 0 o Negativos');

            // let al = document.createElement('p');
            // al.classList.add('col-md-3','col-sm-3','col-xs-12');
            // let altext = document.createTextNode('No puede haber incentivos en 0 o Negativos');
            labelal.appendChild(textal);
            dival.appendChild(labelal);
            rem.appendChild(dival);
          }
          else {
            let ale = document.querySelector("#alerta");
            alerta.parentNode.removeChild(ale);
          }
        });

        $("#por6").change( function(){
          let spor = 0;
          let por = 0;
          for(p=1; p< value; p ++){
            por = parseInt($("#por"+p).val());
            spor = spor + por;
            // let r = ti * (por/100); //dinero
          }

          let rpor = 100 - spor;
          $("#por"+value).val(rpor);

          if(rpor <= 0){
            let dival = document.createElement('div');
            dival.classList.add('col-md-10','col-sm-3','col-xs-12');
            let labelal = document.createElement('label');
            labelal.setAttribute('id','alerta');
            labelal.setAttribute('style','color: red;');
            labelal.classList.add('control-label','col-md-10','col-sm-3','col-xs-12');
            let textal = document.createTextNode('No puede haber incentivos en 0 o Negativos');

            // let al = document.createElement('p');
            // al.classList.add('col-md-3','col-sm-3','col-xs-12');
            // let altext = document.createTextNode('No puede haber incentivos en 0 o Negativos');
            labelal.appendChild(textal);
            dival.appendChild(labelal);
            rem.appendChild(dival);
          }
          else {
            let ale = document.querySelector("#alerta");
            alerta.parentNode.removeChild(ale);
          }
        });

        $("#por7").change( function(){
          let spor = 0;
          let por = 0;
          for(p=1; p< value; p ++){
            por = parseInt($("#por"+p).val());
            spor = spor + por;
            // let r = ti * (por/100); //dinero
          }

          let rpor = 100 - spor;
          $("#por"+value).val(rpor);

          if(rpor <= 0){
            let dival = document.createElement('div');
            dival.classList.add('col-md-10','col-sm-3','col-xs-12');
            let labelal = document.createElement('label');
            labelal.setAttribute('id','alerta');
            labelal.setAttribute('style','color: red;');
            labelal.classList.add('control-label','col-md-10','col-sm-3','col-xs-12');
            let textal = document.createTextNode('No puede haber incentivos en 0 o Negativos');

            // let al = document.createElement('p');
            // al.classList.add('col-md-3','col-sm-3','col-xs-12');
            // let altext = document.createTextNode('No puede haber incentivos en 0 o Negativos');
            labelal.appendChild(textal);
            dival.appendChild(labelal);
            rem.appendChild(dival);
          }
          else {
            let ale = document.querySelector("#alerta");
            alerta.parentNode.removeChild(ale);
          }
        });

        $("#por8").change( function(){
          let spor = 0;
          let por = 0;
          for(p=1; p< value; p ++){
            por = parseInt($("#por"+p).val());
            spor = spor + por;
            // let r = ti * (por/100); //dinero
          }

          let rpor = 100 - spor;
          $("#por"+value).val(rpor);

          if(rpor <= 0){
            let dival = document.createElement('div');
            dival.classList.add('col-md-10','col-sm-3','col-xs-12');
            let labelal = document.createElement('label');
            labelal.setAttribute('id','alerta');
            labelal.setAttribute('style','color: red;');
            labelal.classList.add('control-label','col-md-10','col-sm-3','col-xs-12');
            let textal = document.createTextNode('No puede haber incentivos en 0 o Negativos');

            // let al = document.createElement('p');
            // al.classList.add('col-md-3','col-sm-3','col-xs-12');
            // let altext = document.createTextNode('No puede haber incentivos en 0 o Negativos');
            labelal.appendChild(textal);
            dival.appendChild(labelal);
            rem.appendChild(dival);
          }
          else {
            let ale = document.querySelector("#alerta");
            alerta.parentNode.removeChild(ale);
          }
        });

        $("#por9").change( function(){
          let spor = 0;
          let por = 0;
          for(p=1; p< value; p ++){
            por = parseInt($("#por"+p).val());
            spor = spor + por;
            // let r = ti * (por/100); //dinero
          }

          let rpor = 100 - spor;
          $("#por"+value).val(rpor);

          if(rpor <= 0){
            let dival = document.createElement('div');
            dival.classList.add('col-md-10','col-sm-3','col-xs-12');
            let labelal = document.createElement('label');
            labelal.setAttribute('id','alerta');
            labelal.setAttribute('style','color: red;');
            labelal.classList.add('control-label','col-md-10','col-sm-3','col-xs-12');
            let textal = document.createTextNode('No puede haber incentivos en 0 o Negativos');

            // let al = document.createElement('p');
            // al.classList.add('col-md-3','col-sm-3','col-xs-12');
            // let altext = document.createTextNode('No puede haber incentivos en 0 o Negativos');
            labelal.appendChild(textal);
            dival.appendChild(labelal);
            rem.appendChild(dival);
          }
          else {
            let ale = document.querySelector("#alerta");
            alerta.parentNode.removeChild(ale);
          }
        });

        $("#por10").change( function(){
          let spor = 0;
          let por = 0;
          for(p=1; p< value; p ++){
            por = parseInt($("#por"+p).val());
            spor = spor + por;
            // let r = ti * (por/100); //dinero
          }

          let rpor = 100 - spor;
          $("#por"+value).val(rpor);

          if(rpor <= 0){
            let dival = document.createElement('div');
            dival.classList.add('col-md-10','col-sm-3','col-xs-12');
            let labelal = document.createElement('label');
            labelal.setAttribute('id','alerta');
            labelal.setAttribute('style','color: red;');
            labelal.classList.add('control-label','col-md-10','col-sm-3','col-xs-12');
            let textal = document.createTextNode('No puede haber incentivos en 0 o Negativos');

            // let al = document.createElement('p');
            // al.classList.add('col-md-3','col-sm-3','col-xs-12');
            // let altext = document.createTextNode('No puede haber incentivos en 0 o Negativos');
            labelal.appendChild(textal);
            dival.appendChild(labelal);
            rem.appendChild(dival);
          }
          else {
            let ale = document.querySelector("#alerta");
            alerta.parentNode.removeChild(ale);
          }
        });
      });

    });//fin del document.ready
  </script>
html;
  $sStatus = "";
  foreach (PuestoDao::getStatus() as $key => $value) {
    $selected = ($puesto['status']==$value['catalogo_status_id'])? 'selected' : '';
    $sStatus .=<<<html
    <option {$selected} value="{$value['catalogo_status_id']}">{$value['nombre']}</option>
html;
  }
  View::set('puesto',$puesto);
  View::set('sStatus',$sStatus);
  View::set('header',$this->_contenedor->header(''));
  View::set('footer',$this->_contenedor->footer($extraFooter));
  View::render("puesto_edit");
}

    public function show($id){
      $extraFooter =<<<html
      <script>
        $(document).ready(function(){

          $("#btnCancel").click(function(){
            window.location.href = "/Puesto/";
          });//fin del btnAdd

        });//fin del document.ready
      </script>
html;
      $puesto = PuestoDao::getById($id);


      View::set('puesto',$puesto);
      View::set('header',$this->_contenedor->header(''));
      View::set('footer',$this->_contenedor->footer($extraFooter));
      View::render("puesto_view");
    }

    public function incentivo(){
      $dato = PuestoDao::getIncentivo();
    }

    public function delete(){
      $id = MasterDom::getDataAll('borrar');
      $array = array();
      foreach ($id as $key => $value) {
        $id = PuestoDao::delete($value);
        if($id['seccion'] == 2){
          array_push($array, array('seccion' => 2, 'id' => $id['id'] ));
        }else if($id['seccion'] == 1){
          array_push($array, array('seccion' => 1, 'id' => $id['id'] ));
        }
      }
      $this->alertas("Eliminación de Puesto", $array, "/Puesto/");
    }

    public function puestoAdd(){
      $puesto = new \stdClass();
      $nombre = MasterDom::getDataAll('nombre');
      $nombre = MasterDom::procesoAcentosNormal($nombre);
      $puesto->_nombre = $nombre;
      $descripcion = MasterDom::getDataAll('descripcion');
      $descripcion = MasterDom::procesoAcentosNormal($descripcion);
      $puesto->_descripcion = $descripcion;
      $puesto->_status = MasterDom::getData('status');
      //MRR
      $puesto->_numeroi = MasterDom::getData('numeroi');
      $puesto->_ti = MasterDom::getData('ti');

      if ($_POST['incentivo1']) {
        $puesto->_incentivo1 = MasterDom::getData('incentivo1');
        $puesto->_por1 = MasterDom::getData('por1');
      }
      else {
        $puesto->_incentivo1 = 0;
        $puesto->_por1 = 0;
      }

      if ($_POST['incentivo2']) {
        $puesto->_incentivo2 = MasterDom::getData('incentivo2');
        $puesto->_por2 = MasterDom::getData('por2');
      }
      else {
        $puesto->_incentivo2 = 0;
        $puesto->_por2 = 0;
      }

      if ($_POST['incentivo3']) {
        $puesto->_incentivo3 = MasterDom::getData('incentivo3');
        $puesto->_por3 = MasterDom::getData('por3');
      }
      else {
        $puesto->_incentivo3 = 0;
        $puesto->_por3 = 0;
      }

      if ($_POST['incentivo4']) {
        $puesto->_incentivo4 = MasterDom::getData('incentivo4');
        $puesto->_por4 = MasterDom::getData('por4');
      }
      else {
        $puesto->_incentivo4 = 0;
        $puesto->_por4 = 0;
      }

      if ($_POST['incentivo5']) {
        $puesto->_incentivo5 = MasterDom::getData('incentivo5');
        $puesto->_por5 = MasterDom::getData('por5');
      }
      else {
        $puesto->_incentivo5 = 0;
        $puesto->_por5 = 0;
      }

      if ($_POST['incentivo6']) {
        $puesto->_incentivo6 = MasterDom::getData('incentivo6');
        $puesto->_por6 = MasterDom::getData('por6');
      }
      else {
        $puesto->_incentivo6 = 0;
        $puesto->_por6 = 0;
      }

      if ($_POST['incentivo7']) {
        $puesto->_incentivo7 = MasterDom::getData('incentivo7');
        $puesto->_por7 = MasterDom::getData('por7');
      }
      else {
        $puesto->_incentivo7 = 0;
        $puesto->_por7 = 0;
      }

      if ($_POST['incentivo8']) {
        $puesto->_incentivo8 = MasterDom::getData('incentivo8');
        $puesto->_por8 = MasterDom::getData('por8');
      }
      else {
        $puesto->_incentivo8 = 0;
        $puesto->_por8 = 0;
      }

      if ($_POST['incentivo9']) {
        $puesto->_incentivo9 = MasterDom::getData('incentivo9');
        $puesto->_por9 = MasterDom::getData('por9');
      }
      else {
        $puesto->_incentivo9 = 0;
        $puesto->_por9 = 0;
      }

      if ($_POST['incentivo10']) {
        $puesto->_incentivo10 = MasterDom::getData('incentivo10');
        $puesto->_por10 = MasterDom::getData('por10');
      }
      else {
        $puesto->_incentivo10 = 0;
        $puesto->_por10 = 0;
      }
      // print_r($puesto);
      // exit;
      $id = PuestoDao::insert($puesto);
      if($id >= 1)
        $this->alerta($id,'add');
      else
        $this->alerta($id,'error');
    }
//MRR
    public function puestoEdit(){
      $puesto = new \stdClass();
      $id = PuestoDao::verificarRelacion(MasterDom::getData('catalogo_puesto_id'));
      $nombre = MasterDom::getDataAll('nombre');
      $nombre = MasterDom::procesoAcentosNormal($nombre);
      $puesto->_nombre = $nombre;
      $descripcion = MasterDom::getDataAll('descripcion');
      $descripcion = MasterDom::procesoAcentosNormal($descripcion);
      $puesto->_descripcion = $descripcion;
      $puesto->_status = MasterDom::getData('status');
      $puesto->_catalogo_puesto_id = MasterDom::getData('catalogo_puesto_id');
      $puesto->_numeroi = MasterDom::getData('numeroi');
      $puesto->_ti = MasterDom::getData('ti');

      if ($_POST['incentivo1']) {
        $puesto->_incentivo1 = MasterDom::getData('incentivo1');
        $puesto->_por1 = MasterDom::getData('por1');
      }
      else {
        $puesto->_incentivo1 = 0;
        $puesto->_por1 = 0;
      }

      if ($_POST['incentivo2']) {
        $puesto->_incentivo2 = MasterDom::getData('incentivo2');
        $puesto->_por2 = MasterDom::getData('por2');
      }
      else {
        $puesto->_incentivo2 = 0;
        $puesto->_por2 = 0;
      }

      if ($_POST['incentivo3']) {
        $puesto->_incentivo3 = MasterDom::getData('incentivo3');
        $puesto->_por3 = MasterDom::getData('por3');
      }
      else {
        $puesto->_incentivo3 = 0;
        $puesto->_por3 = 0;
      }

      if ($_POST['incentivo4']) {
        $puesto->_incentivo4 = MasterDom::getData('incentivo4');
        $puesto->_por4 = MasterDom::getData('por4');
      }
      else {
        $puesto->_incentivo4 = 0;
        $puesto->_por4 = 0;
      }

      if ($_POST['incentivo5']) {
        $puesto->_incentivo5 = MasterDom::getData('incentivo5');
        $puesto->_por5 = MasterDom::getData('por5');
      }
      else {
        $puesto->_incentivo5 = 0;
        $puesto->_por5 = 0;
      }

      if ($_POST['incentivo6']) {
        $puesto->_incentivo6 = MasterDom::getData('incentivo6');
        $puesto->_por6 = MasterDom::getData('por6');
      }
      else {
        $puesto->_incentivo6 = 0;
        $puesto->_por6 = 0;
      }

      if ($_POST['incentivo7']) {
        $puesto->_incentivo7 = MasterDom::getData('incentivo7');
        $puesto->_por7 = MasterDom::getData('por7');
      }
      else {
        $puesto->_incentivo7 = 0;
        $puesto->_por7 = 0;
      }

      if ($_POST['incentivo8']) {
        $puesto->_incentivo8 = MasterDom::getData('incentivo8');
        $puesto->_por8 = MasterDom::getData('por8');
      }
      else {
        $puesto->_incentivo8 = 0;
        $puesto->_por8 = 0;
      }

      if ($_POST['incentivo9']) {
        $puesto->_incentivo9 = MasterDom::getData('incentivo9');
        $puesto->_por9 = MasterDom::getData('por9');
      }
      else {
        $puesto->_incentivo9 = 0;
        $puesto->_por9 = 0;
      }

      if ($_POST['incentivo10']) {
        $puesto->_incentivo10 = MasterDom::getData('incentivo10');
        $puesto->_por10 = MasterDom::getData('por10');
      }
      else {
        $puesto->_incentivo10 = 0;
        $puesto->_por10 = 0;
      }
      $array = array();
      if($id['seccion'] == 2){
        array_push($array, array('seccion' => 2, 'id' => $id['id'] ));
        $idStatus = (MasterDom::getData('status')!=2) ? true : false;
        if($idStatus){
          if(PuestoDao::update($puesto) > 0){
            // MRR
            // $getpid = PuestoDao::ultimoPeriodo('semanal');
            $getpid = PuestoDao::getpid();
            foreach (PuestoDao::colaboradoresPuesto($puesto->_catalogo_puesto_id) as $key => $v) {
              $datos = new \stdClass();
              $datos->_colaborador_id = $v['cci'];
              $datos->_prorrateo_periodo_id = $getpid['ppi'];

              $del = PuestoDao::delP($v['cci']);
              foreach (PuestoDao::getIa($v['cci'],$getpid['ppi']) as $val => $vol) {//,$incentivo->_catalogo_incentivo_id
                $dele = PuestoDao::delIa($vol['iai']);
              }

              $incentivos_colaborador = PuestoDao::getIncentivos($puesto->_catalogo_puesto_id);
              $ti = $incentivos_colaborador['total_valor_incentivos'];
              for ($i = 1; $i <= $incentivos_colaborador['numero_incentivos']; $i ++) {
                $incentivo = new \stdClass();
                $incentivo->_catalogo_colaboradores_id = $v['cci'];
                $incentivo->_catalogo_incentivo_id = $incentivos_colaborador['incentivo'.$i];
                $incentivo->_cantidad = $ti * ($incentivos_colaborador['porcentaje'.$i]/100);
                $insert = PuestoDao::insertIncentivo($incentivo);

                $getin = PuestoDao::getIn($datos->_colaborador_id,$incentivo->_catalogo_incentivo_id);

                $data = new \stdClass();
                $data->_colaborador_id = $v['cci'];
                $data->_prorrateo_periodo_id = $getpid['ppi'];
                $data->_catalogo_incentivo_id = $incentivo->_catalogo_incentivo_id;//$getin['catalogo_incentivo_id'];
                $data->_cantidad = $incentivo->_cantidad;//$getin['cantidad'];
                $data->_asignado = 0;
                $data->_valido = 0;
                $id = PuestoDao::insertIncentivos($data);
              }
            }
            // exit;
            $this->alerta($id,'edit');
          }
          else{
            $this->alerta($id,'nothing');
          }
        }
        else{
          $this->alertas("Eliminación de puesto", $array, "/Puesto/");
        }
      }

      if($id['seccion'] == 1){
        array_push($array, array('seccion' => 1, 'id' => $id['id'] ));
        if(MasterDom::getData('status') == 2){
          PuestoDao::update($puesto);
          $this->alerta(MasterDom::getData('catalogo_puesto_id'),'delete');
        }
        else{
          if(PuestoDao::update($puesto) >= 1) $this->alerta($id,'edit');
          else $this->alerta("",'nothing');
        }
      }

    }

    public function validarNombrePuesto(){
      $dato = PuestoDao::getNombrePuesto($_POST['nombre']);
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
<H1 class="titulo">Puestos</H1>
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
          $puesto = PuestoDao::getByIdReporte($value);
            $tabla.=<<<html
              <tr style="background-color:#B8B8B8;">
              <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$puesto['catalogo_puesto_id']}</td>
              <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$puesto['nombre']}</td>
              <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$puesto['descripcion']}</td>
              <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$puesto['status']}</td>
              </tr>
html;
        }
      }else{
        foreach (PuestoDao::getAll() as $key => $puesto) {
          $tabla.=<<<html
          <tr style="background-color:#B8B8B8;">
          <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$puesto['catalogo_puesto_id']}</td>
          <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$puesto['nombre']}</td>
          <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$puesto['descripcion']}</td>
          <td style="height:auto; width: 200px;background-color:#E4E4E4;">{$puesto['status']}</td>
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
      //echo shell_exec('php -f /home/granja/backend/public/librerias/mpdf_apis/Api.php Competencias '.json_encode(MasterDom::getDataAll('borrar')));
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

      $controlador = "Puestos";
      $columna = array('A','B','C','D','E','F','G');
      $nombreColumna = array('Id','Nombre','Descripción','Status');
      $nombreCampo = array('catalogo_puesto_id','nombre','descripcion','status');

      $objPHPExcel->getActiveSheet()->SetCellValue('A'.$fila, 'Reporte de Puestos');
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
          $empresa = PuestoDao::getByIdReporte($value);
          foreach ($nombreCampo as $key => $campo) {
            $objPHPExcel->getActiveSheet()->SetCellValue($columna[$key].$fila, html_entity_decode($empresa[$campo], ENT_QUOTES, "UTF-8"));
            $objPHPExcel->getActiveSheet()->getStyle($columna[$key].$fila)->applyFromArray($estilo_celda);
            $objPHPExcel->getActiveSheet()->getStyle($columna[$key].$fila)->getAlignment()->setWrapText($adaptarTexto);
          }
          $fila +=1;
        }
      }else{
        foreach (PuestoDao::getAll() as $key => $value) {
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
      $regreso = "/Puesto/";

      if($parametro == 'add'){
        $mensaje = "Se ha agregado correctamente";
        $class = "success";
      }

      //MRR
      if($parametro == 'up'){
        $mensaje = "Se ha actualizado los incentivos correctamente";
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
