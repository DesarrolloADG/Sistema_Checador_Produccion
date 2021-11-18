<?php echo $header; ?>
<div class="container body">
  <div class="main_container">

    <!-- page content -->
    <div class="right_col" role="main">
      <div class="">
        <div class="page-title">
          <div class="title_left" style="width: 100%;">
            <h1>
              <small> 
                <?php echo utf8_encode($colaborador['nombre']) . " " . utf8_encode($colaborador['apellido_paterno']) . " " . utf8_encode($colaborador['apellido_materno']); ?> 
                <b>Periodo</b> <?php echo $colaborador['pago']; ?> <b> Identificador </b> <?php echo $colaborador['numero_identificador']; ?>
              </small>
            </h1>
          </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
          <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                
                <h2>Periodo <?php echo $msjPeriodo; ?></h2>
                <div class="clearfix"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
              <div class="form-group ">
                <div class="dashboard-widget-content">
                  <ul class="list-unstyled timeline widget">
                    <li>
                      <div class="block">
                        <div class="block_content">
                          <h2 class="title">
                            <a>Horario(s)</a>
                          </h2>
                          <div class="byline"></div>
                          <?php echo $horarios ; ?>
                          <p class="excerpt"></p>
                        </div>
                      </div>
                    </li>

                    <li>
                      <div class="block">
                        <div class="block_content">
                          <h2 class="title">
                            <a>Pago</a>
                          </h2>
                          <div class="byline"></div>
                          <p class="excerpt"><?php echo $colaborador['pago']; ?></p>
                        </div>
                      </div>
                    </li>

                    <li>
                      <div class="block">
                        <div class="block_content">
                          <h2 class="title">
                            <a>Numero de empleado</a>
                          </h2>
                          <div class="byline"></div>
                          <p class="excerpt"><?php echo $colaborador['numero_empleado']; ?></p>
                        </div>
                      </div>
                    </li>
                          
                  </ul>

                  <div class="form-group">
                  <div class="col-md-12 col-sm-12 col-xs-12">
                    <a class="btn btn-danger col-md-2 col-sm-2 col-xs-2" onclick="javascript:window.close();" href="#">
                      <span class="glyphicon glyphicon-chevron-left pull-left"></span> cerrar
                    </a>
                    <?php echo $irIncidencias ?>
                  </div>
                </div>
            </div>


          </div>
        
      </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
              <p><span class="fa fa-circle" style="color:#b9e6ff;"> </span> Dia Festivo</p>
              <div class="x_title">
              <div class="dataTable_wrapper" id="contendor_tabla">
                <?php echo $tabla; ?>
              </div>
            </div>
          </div>
        </div>


      </div>
    </div>
    <!-- /page content -->

  </div>
</div>


<?php echo $footer; ?>
