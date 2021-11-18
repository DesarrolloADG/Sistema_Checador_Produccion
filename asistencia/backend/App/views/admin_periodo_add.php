<?php echo $header;?>
<div class="right_col">
  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    <div class="x_panel tile fixed_height_240">
      <div class="x_title">
        <br><br>
        <h1>Agregar un Nuevo Periodo</h1>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <form class="form-horizontal" id="add" action="/AdminPeriodo/periodoAdd" method="POST">
          <div class="form-group ">

            <div class="form-group">
							<fieldset>
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="descripcion">Fecha Inicio <span class="required">*</span></label>
								<div class="control-group">
									<div class="controls">
										<div class="col-md-6 col-sm-6 col-xs-12 xdisplay_inputx form-group has-feedback">
											<input type="text" id="single_cal2" name="fecha_inicio" class="form-control has-feedback-left" placeholder="Ingresa la fecha inicial" aria-describedby="inputSuccess2Status2">
											<span class="fa fa-calendar-o form-control-feedback left" aria-hidden="true"></span>
											<span id="inputSuccess2Status2" class="sr-only">(success)</span>
										</div>
									</div>
								</div>
							</fieldset>
						</div>

            <div class="form-group">
							<fieldset>
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="descripcion">Fecha Final<span class="required">*</span></label>
								<div class="control-group">
									<div class="controls">
										<div class="col-md-6 col-sm-6 col-xs-12 xdisplay_inputx form-group has-feedback">
											<input type="text" id="single_cal3" name="fecha_fin" class="form-control has-feedback-left" placeholder="Ingresa la fecha final" aria-describedby="inputSuccess2Status2">
											<span class="fa fa-calendar-o form-control-feedback left" aria-hidden="true"></span>
											<span id="inputSuccess2Status2" class="sr-only">(success)</span>
										</div>
									</div>
								</div>
							</fieldset>
						</div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="status">Tipo Periodo<span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <select class="form-control" name="tipo" id="tipo">
                  <option value="" disabled selected>Selecciona un tipo de periodo</option>
                  <option value="semanal">Semanal</option>
                  <option value="quincenal">Quincenal</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-2 col-xs-offset-3">
                <button class="btn btn-danger col-md-3 col-sm-3 col-xs-5" type="button" id="btnCancel">Cancelar</button>
                <button class="btn btn-success col-md-3 col-sm-3 col-xs-5" id="btnAdd" type="submit">Agregar</button>
              </div>
            </div>
            <div id="resultado">

            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php echo $footer;?>
