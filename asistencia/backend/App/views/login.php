<?php
    echo $header;
?>
<!--div class="container">

    <!--?php 
    /*if(isset($_GET['login']) == 1){
        $html = <<<html
            <div class="col-md-12 col-md-offset-12 col-sm-12 btn btn-primary" style="text-align: center;" >
                <p>Se ha modificado tu contraseña. Ahora ingresa con tu nueva contraseña</p>
            </div>
html;
    }
        echo $html;*/
    ?-->
    <!--div id="loginbox" class="mainbox col-md-4 col-sm-4 col-xs-8 col-md-offset-4 col-sm-4 col-md-offset-4 col-sm-offset-4 col-xs-offset-2">

        <div class="row">
            <div class="iconmelon">
              <img  src="/img/logo.jpg" alt="Login" >
            </div>
        </div>

        <div class="panel panel-default" >
            <div class="panel-heading">
                <div class="panel-title text-center">Ingresar </div>
                <div id="contenedor"></div>
            </div>

            <div class="panel-body" >

                <form id="login" action="/Login/crearSession" method="POST" class="form-horizontal">

                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-3 col-md-offset-5" for="usuario"></label>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-3" for="usuario"><span class="required"><i class="glyphicon glyphicon-user "></i></span></label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            
                            <input type="text" name="usuario" id="usuario" class="form-control col-md-5 col-xs-12" placeholder="Usuario">
                        </div>
                        <span sclass="col-md-1 col-sm-1 col-xs-1
                        " id="availability"> </span>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-3 panel-title" for="usuario"><span class="required"><i class="glyphicon glyphicon-lock"></i></span></label>
                        <div class="col-md-7 col-sm-7 col-xs-12">
                            <input type="password" name="password" id="password" class="form-control col-md-5 col-xs-12" placeholder="Contraseña">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-12 controls">
                            <button type="button" id="btnEntrar" class="btn btn-primary pull-right">Entrar <i class="glyphicon glyphicon-log-in"></i></button>
                        </div>
                    </div>

                    <div id="respuesta">

                    </div>

                </form>

            </div>
        </div>
    </div>
</div-->
<!-- <div id="particles"></div> -->
<div id="particles-js">
    
    <div style="position: absolute; width: 50%; height: auto;">
        
        <div style="width: 70%; margin-left: 60%; margin-top: 30%; background: #fff; padding: 20px;">
            <div style="width: 100%;" >
                <div style="text-align: center;">
                    <img  src="/img/logogranja.png" alt="Login">
                </div>
                <br>
                <h1 style="color: #ed9f34; font-size: 30px; text-align: center;">Iniciar Sesión</h1>
            
                <form id="login" action="/Login/crearSession" method="POST" class="form-horizontal">

                    <br><br>

                    <div class="form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <span sclass="col-md-1 col-sm-1 col-xs-1" id="availability"> </span>
                            <input type="text" name="usuario" id="usuario" class="form-control col-md-5 col-xs-12" placeholder="Usuario">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <input type="password" name="password" id="password" class="form-control col-md-5 col-xs-12" placeholder="Contraseña">
                        </div>
                    </div>

                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <button type="button" id="btnEntrar" class="btn btn-warning col-md-4 col-sm-4 col-xs-4 pull-right">Entrar <i class="glyphicon glyphicon-log-in"></i></button>
                        </div>
                    </div>

                    <div id="retroclockbox1" hidden>

                    </div>
                </form>
            </div>
        </div>

    </div>

</div> 
<?php echo $footer; ?>

