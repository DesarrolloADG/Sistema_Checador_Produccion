<?php
namespace App\controllers;
defined("APPPATH") OR die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \App\controllers\Contenedor;
use \App\models\Login AS LoginDao;

class Login{
    private $_contenedor;

    function __construct(){
/*
      $this->_contenedor = new Contenedor(0);
      View::set('header',$this->_contenedor->header());
      View::set('footer',$this->_contenedor->footer());
*/
    }

    public function index() {
        $extraHeader =<<<html
        <link rel="stylesheet" href="/css/bootstrap/bootstrap.css">
        <link rel="stylesheet" href="/css/bootstrap/datatables.bootstrap.css">
        <link rel="stylesheet" href="/css/contenido/custom.min.css">
        <link rel="stylesheet" href="/css/validate/screen.css">
        <link rel="stylesheet" href="/css/alertify/alertify.core.css" />
        <link rel="stylesheet" href="/css/alertify/alertify.default.css" id="toggleCSS" />
        <link rel="stylesheet" type="text/css" href="/librerias/vintage_flip_clock/jquery.flipcountdown.css" />
<style>


canvas{
  display:block;
}

.count-particles{
  background: #000022;
  position: absolute;
  top: 10px;
  left: 0;
  width: 80px;
  color: #13E8E9;
  font-size: .8em;
  padding-bottom: 2px;
  font-family: Helvetica, Arial, sans-serif;
  font-weight: bold;
}

.js-count-particles{
  font-size: 1.1em;
}

#stats,
.count-particles{
  -webkit-user-select: none;
  margin-top: 5px;
  margin-left: 5px;
}

#stats{
  border-radius: 3px 3px 0 0;
  overflow: hidden;
}

.count-particles{
  border-radius: 0 0 3px 3px;
}

#particles-js{
  width: 100%;
  height: 100%;
  background-color: #fff;//#2d2e2e;//#fec488;//#344155;
  background-image: url('');
  background-size: cover;
  background-position: 100% 100%;
  background-repeat: no-repeat;
}

</style>
html;
        $extraFooter =<<<html
        <!-- <script src="/js/login.js"></script> -->
        <script src="/js/jquery.min.js"></script>
        <script src="/js/validate/jquery.validate.js"></script>
        <script src="/js/alertify/alertify.min.js"></script>

        <script src="/js/particles.js"></script>
        <script src="/js/app.js"></script>
        <script src="/js/stats.js"></script>
        <script type="text/javascript" src="/librerias/vintage_flip_clock/jquery.flipcountdown.js"></script>

        
        
        <script>
  var count_particles, stats, update;
  stats = new Stats;
  stats.setMode(0);
  stats.domElement.style.position = 'absolute';
  stats.getElement.style.zIndex = "1"; 

  stats.domElement.style.left = '0px';
  stats.domElement.style.top = '0px';
  document.body.appendChild(stats.domElement);
  count_particles = document.querySelector('.js-count-particles');
  update = function() {
    stats.begin();
    stats.end();
    if (window.pJSDom[0].pJS.particles && window.pJSDom[0].pJS.particles.array) {
      count_particles.innerText = window.pJSDom[0].pJS.particles.array.length;
    }
    requestAnimationFrame(update);
  };
  requestAnimationFrame(update);
</script>

        <script>
            $(document).ready(function(){
                $.validator.addMethod("checkUserName",function(value, element) {
                  var response = false;
                    $.ajax({
                        type:"POST",
                        async: false,
                        url: "/Login/isUserValidate",
                        data: {usuario: $("#usuario").val()},
                        success: function(data) {
                            if(data=="true"){
                                $('#availability').html('<span class="text-success glyphicon glyphicon-ok"></span>');
                                $('#btnEntrar').attr("disabled", false);
                                response = true;
                            }else{
                                $('#availability').html('<span class="text-danger glyphicon glyphicon-remove"></span>');
                                $('#btnEntrar').attr("disabled", true);
                            }
                        }
                    });

                    return response;
                },"El usuario no es correcto");

                $("#login").validate({
                    rules:{
                        usuario:{
                            required: true,
                            checkUserName: true
                        },
                        password:{
                            required: true,
                        }
                    },
                    messages:{
                        usuario:{
                            required: "Este campo es requerido",
                        },
                        password:{
                            required: "Este campo es requerido",
                        }
                    }
                });

                $("#btnEntrar").click(function(){
                    $.ajax({
                        type: "POST",
                        url: "/Login/verificarUsuario",
                        data: $("#login").serialize(),
                        success: function(response){
                            if(response!=""){
                                var usuario = jQuery.parseJSON(response);
                                if(usuario.nombre!=""){
                                    $("#login").append('<input type="hidden" name="autentication" id="autentication" value="OK"/>');
                                    $("#login").append('<input type="hidden" name="nombre" id="nombre" value="'+usuario.nombre+'"/>');
                                    $("#login").submit();
                            }else{
                                alertify.alert("Error de autenticación <br> El usuario o contraseña es incorrecta");
                            }
                            }else{
                                alertify.alert("Error de autenticación <br> El usuario o contraseña es incorrecta");
                            }
                        }
                    });
                });


                /***************************************************************************************/
                $(function(){
                  var i = 1;
                  $('#retroclockbox1').flipcountdown({
                    tick:function(){
                      return i++;
                    }
                  });
                })
                /***************************************************************************************/

            });
        </script>
html;
        View::set('header',$extraHeader);
        View::set('footer',$extraFooter);
        View::render("login");
    }

    public function isUserValidate(){
        echo (count(LoginDao::getUser($_POST['usuario']))>=1)? 'true' : 'false';
    }

    public function verificarUsuario(){
        $usuario = new \stdClass();
        $usuario->_usuario = MasterDom::getData("usuario");
        $usuario->_password = MD5(MasterDom::getData("password"));
        $user = LoginDao::getById($usuario);
        if (count($user)>=1) {
            $user['nombre'] = utf8_encode($user['nombre']);
            echo json_encode($user);
        }
    }

    public function crearSession(){
        $usuario = new \stdClass();
        $usuario->_usuario = MasterDom::getData("usuario");
        $usuario->_password = MD5(MasterDom::getData("password"));
        $user = LoginDao::getById($usuario);
        session_start();
        $_SESSION['usuario'] = $user['usuario'];
        $_SESSION['nombre'] = $user['nombre'];
        header("location: /Principal/");
    }

    public function cerrarSession(){
        unset($_SESSION);
        session_unset();
        session_destroy();
        header("Location: /Login/");
    }

}
