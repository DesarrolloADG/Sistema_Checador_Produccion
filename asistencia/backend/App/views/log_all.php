<?php echo $header; ?>
<div class="right_col">
  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    <div class="panel panel-default">
      <div class="x_title">
        <br><br>
        <h1> Historial de Actividades Base de Datos </h1>
        <div class="clearfix"></div>
      </div>
      <form name="all" id="all" action="" method="POST">
        <div class="panel-body" <?php echo $visible; ?>>
          <button id="export_pdf" type="button" class="btn btn-info btn-circle"><i class="fa fa-file-pdf-o"> <b>Exportar a PDF</b></i></button>
          <button id="export_excel" type="button" class="btn btn-success btn-circle"><i class="fa fa-file-excel-o"> <b>Exportar a Excel</b></i></button>
        </div>

        <div class="x_content">
            <div class="form-group col-md-9 col-sm-9 col-xs-12">

              <div class="form-group">
  							<fieldset>
  								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="descripcion">Fecha Inicio</label>
  								<div class="control-group">
  									<div class="controls">
  										<div class="col-md-6 col-sm-6 col-xs-12 xdisplay_inputx form-group has-feedback">
  											<input type="text" id="single_cal2" name="fecha_inicio" class="form-control has-feedback-left" placeholder="Ingresa la fecha del dia festivo" aria-describedby="inputSuccess2Status2" value="<?php echo $fecha_inicio;?>">
  											<span class="fa fa-calendar-o form-control-feedback left" aria-hidden="true"></span>
  										</div>
  									</div>
  								</div>
  							</fieldset>
  						</div>

              <div class="form-group">
  							<fieldset>
  								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="descripcion">Fecha Fin</label>
  								<div class="control-group">
  									<div class="controls">
  										<div class="col-md-6 col-sm-6 col-xs-12 xdisplay_inputx form-group has-feedback">
  											<input type="text" id="single_cal4" name="fecha_fin" class="form-control has-feedback-left" placeholder="Ingresa la fecha del dia festivo" aria-describedby="inputSuccess2Status2" value="<?php echo $fecha_fin;?>">
  											<span class="fa fa-calendar-o form-control-feedback left" aria-hidden="true"></span>
  										</div>
  									</div>
  								</div>
  							</fieldset>
  						</div>

              <div class="col-md-6 col-sm-6 col-xs-6">
                <button class="btn btn-success col-md-3 col-sm-3 col-xs-3" type="button" id="btnFiltro">Filtrar</button>
              </div>

              </div>
            </div>
          </div>

        <div class="panel-body">
          <div class="dataTable_wrapper">
            <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
              <thead>
                <tr>
                  <!--<th><input type="checkbox" name="checkAll" id="checkAll" value=""/></th>-->
                  <th>fecha</th>
                  <th>usuario</th>
                  <th>Descripción</th>
                  <th>Accion</th>
                </tr>
              </thead>
               <tbody>
                <?php echo $tabla; ?>
              </tbody>
            </table>
          </div>
        </div>
      </form>
    </div>
  </div>
<?php echo $footer; ?>
