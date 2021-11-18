<?php echo $header; ?>
<div class="container body">
  <div class="main_container">

    <!-- page content -->
    <div class="right_col" role="main">
      <div class="">
        <div class="page-title">
          <div class="title_left" style="width: 100%;">
            <!--h1><b><?php echo $tipoPeriodo; ?></b></h1-->
            <h1>
                  <a href="<?php echo $direccionamiento; ?>" class="glyphicon glyphicon-chevron-left"> </a> 
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
              <div class="x_title">
              <div class="col-md-12" style="padding: 10px;">
                <span style="color: #232323; font-weight: bold; font-size: 14px;"><span style="color: #b9e6ff;" class="fa fa-circle"> </span> Dia Festivo</span>
              </div>

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