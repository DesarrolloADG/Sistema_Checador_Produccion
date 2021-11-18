<?php echo $header; ?>
<div class="right_col">
	<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
		<div class="panel panel-default">
			<div class="x_title">
				<br><h1> Incentivos del colaborador <?php echo $colaborador['nombre'] . " " . $colaborador['apellido_paterno'] . " " . $colaborador['apellido_materno'] ; ?></h1><br><br>
				<div class="clearfix"></div>

				<div class="row">
					<div class="col-xs-3">

						<div class="panel panel-default">
							<div class="panel-heading">
								<h4>El periodo es de tipo <b><?php echo strtoupper($datosUsuario['tipo']) ?></b></h4>
							</div>
							<div class="panel-body">
								<?php echo $reglasIncentivos; ?>
							</div>
						</div>

					</div>
					<!-- /.col -->
						
					<div class="col-xs-9">

						<form action="/AsignarIncentivos/activos" method="POST">
						<!--form action="/AsignarIncentivos/activos" method="POST"-->
							<div class="table-responsive">
								<input type="hidden" name="colaborador_id" value="<?php echo $colaborador_id; ?>">
								<input type="hidden" name="periodo_id" value="<?php echo $periodo_id; ?>">
								<table class="table" id="tabla-incentivos">
									<thead>
										<h3>&nbsp; Tabla de incentivos</h3>
										<tr>
											<th style="text-align:center; vertical-align:left; background: #123;">Tipo de incentivo</th>
											<th style="text-align:center; vertical-align:left; background: #123;">Cantidad Generada</th>
											<th style="text-align:center; vertical-align:left; background: #123;">Aplica</th>
											<th style="text-align:center; vertical-align:left; background: #123;">Nota</th>
											<th style="text-align:center; vertical-align:left; background: #123;">-</th>
											<!--th style="text-align:center; vertical-align:left; background: #123;">Incrementar incentivo</th-->
										</tr>
									</thead>
									<tbody>
										<?php echo $tabla; ?>
									</tbody>
								</table>
							</div>

							<div class="form-group">
								<div class="col-md-4 col-sm-12 col-xs-12">
									<a class="btn btn-danger col-md-3 col-sm-3 col-xs-3" type="button" href="/AsignarIncentivos/">Regresar </a>
									<button class="btn btn-success col-md-3 col-sm-3 col-xs-3" id="btnAdd" type="submit" style="<?php echo $display; ?>">Agregar</button>
								</div>
							</div>
						</form>
					</div>
					<!-- /.col -->
				</div>
				
			</div>
		</div>
	</div>
</div>
<?php echo $footer; ?>
