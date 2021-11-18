<?php echo $header;?>
<div class="right_col">
	<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
		<div class="x_panel tile fixed_height_240">
			<div class="x_title">
				<h1> <?php echo $titulo; ?></h1>
				<div class="clearfix"></div>
			</div>

			<div class="x_content">
				<form class="form-horizontal" id="edit" action="/Incentivo/editPagoBotes" method="POST">
					<div class="form-group ">

						<input type="hidden" name="pago_botes_id" id="pago_botes_id" class="form-control col-md-7 col-xs-12" value="<?php echo $botes['pago_botes_id']; ?>">

						<div class="form-group">
							<label class="control-label col-md-3 col-sm-3 col-xs-12" for="identificador">Identificador <span class="required">*</span></label>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<input type="text" name="identificador" id="identificador" class="form-control col-md-7 col-xs-12" value="<?php echo $botes['identificador']; ?>"  disabled>
							</div>
						</div>

						<div class="form-group">
							<label class="control-label col-md-3 col-sm-3 col-xs-12" for="clara">Precio Clara <span class="required">*</span></label>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<input type="text" name="clara" id="clara" class="form-control col-md-7 col-xs-12" value="<?php echo $botes['clara']; ?>">
							</div>
						</div>

						<div class="form-group">
							<label class="control-label col-md-3 col-sm-3 col-xs-12" for="yema">Precio yema <span class="required">*</span></label>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<input type="text" name="yema" id="yema" class="form-control col-md-7 col-xs-12" value="<?php echo $botes['yema']; ?>">
							</div>
						</div>

						<div class="form-group">
							<label class="control-label col-md-3 col-sm-3 col-xs-12" for="huevo_liquido">Precio huevo liquido <span class="required">*</span></label>
							<div class="col-md-6 col-sm-6 col-xs-12">
								<input type="text" name="huevo_liquido" id="huevo_liquido" class="form-control col-md-7 col-xs-12" value="<?php echo $botes['huevo_liquido']; ?>">
							</div>
						</div>



						<div class="form-group">
							<div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
								<a href="/Incentivo/pagoBotes/" class="col-md-5 col-sm-5 col-xs-5 btn btn-danger">Cancelar</a>
								<button class="btn btn-success col-md-5 col-sm-5 col-xs-5" id="btnAdd" type="submit">Actualizar</button>
							</div>
						</div>

					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<?php echo $footer;?>
