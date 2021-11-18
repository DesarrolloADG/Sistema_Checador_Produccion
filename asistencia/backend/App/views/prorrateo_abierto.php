<?php echo $header; ?>
<div class="container body">
  <div class="main_container">

    <!-- page content -->
    <div class="right_col" role="main">
      <div class="">
        <div class="page-title">
          <div class="title_left">
            <h1>Prorrateo <b><?php echo $tipoPeriodo; ?></b></h1>
          </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
          <div class="<col>-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <h2>Periodo <?php echo $msjPeriodo; ?></h2>
                <div class="clearfix"></div>
              </div>
            </div>
          </div>
        </div>

        <div><?php echo $mensaje; ?></div>

        <div class="row">
          <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <h1>Colaboradores </h1>
              </div>

              <div class="x_content">
                <form name="all" id="all" action="/ProrrateoT/saveProrrateo" method="POST">
                  <input type="hidden" name="prorrateo_periodo_id" value="<?php echo $prorrateo_periodo_id ?>">
                  <input type="hidden" name="identificador" value="<?php echo $identificador ?>">
                  <div class="panel-body" style="<?php echo $display; ?>">
                    <input type="submit" class="btn btn-primary btn-circle" value="<?php echo $txtAccion; ?>" id="btn-action-accept" >
                  </div>
                  <div id="contenido-mensaje"></div>

                  <div class="panel-body">
                    <div class="dataTable_wrapper table table-responsive">
                      <table class="table table-striped table-bordered table-hover" id="muestra-colaboradores">
                        <thead>
                          <tr>
                            <th style="text-align:center; vertical-align:top;">Clave</th>
                            <th style="text-align:center; vertical-align:top;">Nombre</th>
                            <th style="text-align:center; vertical-align:top;">Salario Diario</th>
                            <th style="text-align:center; vertical-align:top;">S.D.I</th>
                            <th style="text-align:center; vertical-align:top;">Horas Extra</th>
                            <th style="text-align:center; vertical-align:top;">Incentivos</th>
                            <th style="text-align:center; vertical-align:top;">$ Horas Extra</th>
                            <th style="text-align:center; vertical-align:top;">Otras</th>
                            <th style="text-align:center; vertical-align:top;">TOTAL</th>
                            <th style="text-align:center; vertical-align:top;">Limite H. Extra</th>
                            <th style="text-align:center; vertical-align:top; background: #fbf6b6;">Premio de Asistencia</th>
                            <th style="text-align:center; vertical-align:top; background: #fbf6b6;">Premio de Puntualidad</th>
                            <th style="text-align:center; vertical-align:top; background: #fbf6b6;">Numero Horas Extra</th>
                            <th style="text-align:center; vertical-align:top;">Importe H. extra</th>
                            <th style="text-align:center; vertical-align:top;">H. extra</th>
                            <th style="text-align:center; vertical-align:top; background: #fbf6b6;">Despensa en efectivo</th>
                            <th style="text-align:center; vertical-align:top; background: #fbf6b6;">Incentivo</th>
                            <th style="text-align:center; vertical-align:top;">Total Prorrateo</th>
                            <th style="text-align:center; vertical-align:top;">Validacion</th>
                            <th style="text-align:center; vertical-align:top; background: #fbf6b6;">Prima Dominical</th>
                            <th style="text-align:center; vertical-align:top; background: #fbf6b6;">Domingo Trabajado</th>
                            <th style="text-align:center; vertical-align:top; background: #fbf6b6; <?php echo $displayDiaFestivo; ?>">Festivo</th>
                            <th style="text-align:center; vertical-align:top;">Total Percepciones</th>
                            <!--th style="text-align:center; vertical-align:top;">Inc. Noche</th-->
                          </tr>
                        </thead>
                        <tbody>
                          <?php echo $tabla['html']; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <?php echo $tabla['htmlGuarda']; ?>
                </form>
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