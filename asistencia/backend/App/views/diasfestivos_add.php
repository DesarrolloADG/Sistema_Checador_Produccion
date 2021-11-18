<?php echo $header;?>
<div class="right_col">
	<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
		<div class="x_panel tile fixed_height_240">
			<div class="x_title">
				<h1>Agregar Nuevo  Día Festivo</h1>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<form class="form-horizontal" id="add" action="/Diasfestivos/diafestivoAdd" method="POST">
					<div class="form-group ">
						<div class="form-group">
							<label class="control-label col-md-3 col-sm-3 col-xs-12" for="nombre">Nombre <span class="required">*</span></label>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<input type="text" name="nombre" id="nombre" class="form-control col-md-7 col-xs-12" placeholder="Nombre del d&iacute;a festivo">
							</div>
							<span id="availability"></span>
						</div>

						<div class="form-group">
							<label class="control-label col-md-3 col-sm-3 col-xs-12" for="descripcion">Descripci&oacute;n <span class="required">*</span></label>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<textarea class="form-control" name="descripcion" id="descripcion" placeholder="Descripci&oacute;n del d&iacute;a festivo"></textarea>
							</div>
						</div>

						<div class="form-group">
							<fieldset>
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="descripcion">Fecha <span class="required">*</span></label>
								<div class="control-group">
									<div class="controls">
										<div class="col-md-6 col-sm-6 col-xs-12 xdisplay_inputx form-group has-feedback">
											<input type="text" id="single_cal2" name="fecha" class="form-control has-feedback-left" placeholder="Ingresa la fecha del dia festivo" aria-describedby="inputSuccess2Status2">
											<span class="fa fa-calendar-o form-control-feedback left" aria-hidden="true"></span>
											<span id="inputSuccess2Status2" class="sr-only">(success)</span>
										</div>
									</div>
								</div>
							</fieldset>
						</div>

						<div class="form-group">
							<label class="control-label col-md-3 col-sm-3 col-xs-12" for="status">Estatus:<span class="required">*</span></label>
              				<div class="col-md-6 col-sm-6 col-xs-12">
								<select class="form-control col-md-7 col-xs-12" name="status" id="status">
									<option value="" disabled selected>Selecciona un estatus</option>
									<?php echo $status; ?>
								</select>
                			</div>
						</div>

						<div class="form-group">
							<br>
							<div class="col-md-10 col-sm-10 col-xs-12 col-md-offset-2 ">
								<button class="btn btn-danger col-md-3 col-sm-3 col-xs-3" type="button" id="btnCancel">Cancelar</button>
								<button class="btn btn-primary col-md-3 col-sm-3 col-xs-3" type="reset" >Resetear</button>
								<button class="btn btn-success col-md-3 col-sm-3 col-xs-3" type="submit" id="btnAdd">Agregar</button>
							</div>
						</div>
						
				<!--div class="form-group">
                    <label class="control-label col-md-2 col-sm-2 col-xs-12">Fechas</label>

                    <div class="col-md-2 col-sm-2 col-xs-12">
                      <fieldset>
                        <div class="control-group">
                          <div class="controls">
                            <div class="">
                              <input name="fecha_actual" type="text" class="form-control has-feedback-right" id="single_calA" placeholder="Fecha Diaria" aria-describedby="inputSuccess2Status2" >
                              <span class="fa fa-calendar-o form-control-feedback right" aria-hidden="true"></span>
                              <span id="inputSuccess2Status2" class="sr-only">(success)</span>
                            </div>
                          </div>
                        </div>
                      </fieldset>
                    </div>

                    <div class="col-md-2 col-sm-2 col-xs-12">
                      <fieldset>
                        <div class="control-group">
                          <div class="controls">
                            <div class="">
                              <input name="fecha_inicial" type="text" class="form-control has-feedback-right" id="single_calB" placeholder="Fecha Inicio" aria-describedby="inputSuccess2Status2">
                              <span class="fa fa-calendar-o form-control-feedback right" aria-hidden="true"></span>
                              <span id="inputSuccess2Status2" class="sr-only">(success)</span>
                            </div>
                          </div>
                        </div>
                      </fieldsetc>
                    </div>

                    <div class="col-md-2 col-sm-2 col-xs-12">
                      <fieldset>
                        <div class="control-group">
                          <div class="controls">
                            <div class="">
                              <input name="fecha_final" type="text" class="form-control has-feedback-right" id="single_calC" placeholder="Fecha Final" aria-describedby="inputSuccess2Status2">
                              <span class="fa fa-calendar-o form-control-feedback right" aria-hidden="true"></span>
                              <span id="inputSuccess2Status2" class="sr-only">(success)</span>
                            </div>
                          </div>
                        </div>
                      </fieldset>
                    </div>

                    <div class="col-md-3 col-sm-3 col-xs-12">
                      <input type="button" value="Guardar" id="guardar1" class="btn btn-success col-md-12 col-sm-12 col-xs-12">
                    </div>
                  </div!-->

					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<?php echo $footer;?>
