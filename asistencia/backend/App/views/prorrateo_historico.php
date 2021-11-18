
<?php echo $header; ?>
<div class="container body">
  <div class="main_container">

    <!-- page content -->
    <div class="right_col" role="main">
      <div class="">
        <div class="page-title">
          <div class="title_left" style="width: 100%!important;">
            <h3><?php echo $tipoPeriodo; ?></h3>
          </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
          <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <!--h2>Periodo de incentivos <?php echo $msjPeriodo; ?></h2-->
                <div class="clearfix"></div>
                <div class="panel-body">
                  <div class="row">
                    <form method="POST" action="<?php echo $busqueda; ?>">
                      <div class="form-group">
                        <div class="col-md-10 col-sm-10 col-xs-12 ">
                          <select name="tipo_periodo" class="form-control">
                             <?php echo $option; ?>
                          </select>
                        </div>
                        <div class="col-md-2 col-sm-2 col-xs-4 ">
                          <button class="btn btn-<?php echo $btnbackground; ?> col-md-12 col-sm-12 col-xs-12" type="submit" id="buscar_periodo"><?php echo $btnText; ?></button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          
          <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <h4>Periodo de incentivos <?php echo $msjPeriodo; ?></h4>
              </div>
              <div class="x_content">
                <form name="all" id="all" action="/Incentivo/" method="POST">

                  <input type="hidden" name="prorrateo_periodo_id" value="<?php echo $prorrateo_periodo_id ?>">
                  <input type="hidden" name="identificador" value="<?php echo $identificador ?>">
                  
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
                            <th style="text-align:center; vertical-align:top;">H. extra previo</th>
                            <th style="text-align:center; vertical-align:top; background: #fbf6b6;">Despensa en efectivo</th>
                            <th style="text-align:center; vertical-align:top; background: #fbf6b6;">Incentivo</th>
                            <th style="text-align:center; vertical-align:top;">Total Prorrateo</th>
                            <th style="text-align:center; vertical-align:top;">Validacion</th>
                            <th style="text-align:center; vertical-align:top; background: #fbf6b6;">Prima Dominical</th>
                            <th style="text-align:center; vertical-align:top; background: #fbf6b6;">Domingo Trabajo</th>
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

  </div>
</div>


<?php echo $footer; ?>