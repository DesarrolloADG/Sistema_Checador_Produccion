	
<?php echo $header; ?>
<div class="right_col">
	<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
		<div class="panel panel-default">
			<div class="x_title">
				<br><br>
				<h1> <?php echo $mensaje; ?></h1>
				<div class="clearfix"></div>
			</div>
			<form name="all" id="all" action="/ProrrateoT/SalarioMinimoAdd/" method="POST">
				<div class="panel-body">
					<div class="form-group">
						<label class="control-label col-md-2 col-sm-2 col-xs-12" for="nombre">SALARIO MINIMO <span class="required">*</span></label>
						<div class="col-md-6 col-sm-6 col-xs-12">
							<input type="text" name="salario" id="salario" class="form-control col-md-7 col-xs-12" placeholder="INGRESA EL SALARIO MINIMO" required />
						</div>
						<div class="col-md-1 col-sm-1 col-xs-12">
							<input type="submit" class="btn btn-success" value="<?php echo $accion; ?>">
						</div>
					</div>
				</div>
			</form><br><br>
		</div>
	</div>
</div>
<?php echo $footer; ?>
