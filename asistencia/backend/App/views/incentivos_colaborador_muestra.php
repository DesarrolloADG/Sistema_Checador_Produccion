<?php echo $header; ?>
	<div class="container body">
		<div class="main_container">

			<!-- page content -->
			<div class="right_col" role="main">

				<div class="col-md-12 col-sm-12 col-xs-12">

					<div class="x_panel">
						<div class="x_title">
							<h2><i class="fa fa-bars"></i> INCENTIVOS <small>Seccion de incentivos</small></h2>
							<ul class="nav navbar-right panel_toolbox">
								<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
								<li class="dropdown">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
									<ul class="dropdown-menu" role="menu">
										<li><a href="#">Settings 1</a></li>
										<li><a href="#">Settings 2</a></li>
									</ul>
								</li>
								<li><a class="close-link"><i class="fa fa-close"></i></a></li>
							</ul>
							<div class="clearfix"></div>
						</div>


						<div class="x_content">

							<div class="col-xs-3">
								<ul class="nav nav-tabs tabs-left">
									<li class="active"><a href="#colaborador" data-toggle="tab" aria-expanded="false">Informacion del colaborador</a></li>
									<li class=""><a href="#incentivos" data-toggle="tab" aria-expanded="true">Incentivos</a></li>
									<li class=""><a href="#faltas" data-toggle="tab" aria-expanded="true">Resumen Asistencia</a></li>
									<li class=""><a href="#horas-extra" data-toggle="tab" aria-expanded="false">Horas Extra</a></li>
									<li class=""><a href="#domingos" data-toggle="tab" aria-expanded="false">Domingo</a></li>
									<li class=""><a href="#botes" data-toggle="tab" aria-expanded="true">Botes</a></li>
								</ul>
							</div>

							<div class="col-xs-9">
								<!-- Tab panes -->
								<div class="tab-content">
									<div class="tab-pane active" id="colaborador">
										<p class="lead">Informacion del colaborador</p>

										<div class="col-md-10 col-sm-10 col-xs-12 profile_left">
											<div class="profile_img">
												<div id="crop-avatar">
													<!-- Current avatar -->
													<img class="img-responsive avatar-view" src="images/picture.jpg" alt="Avatar" title="Change the avatar">
												</div>
											</div>
											<h3>Nombre completo del colaborador</h3>

											<ul class="list-unstyled user_data">
												<li><i class="fa fa-briefcase user-profile-icon"></i> <b>#Identificador</b> :#123123123: </li>
												<li><i class="fa fa-briefcase user-profile-icon"></i> <b>Genero</b> :MASCULINO-FEMENINO: </li>
												<li><i class="fa fa-briefcase user-profile-icon"></i> <b>RFC</b> :12312312: </li>
												<li><i class="fa fa-map-marker user-profile-icon"></i> <b>LECTOR</b> :LEXTOR:</li>
												<li><i class="fa fa-map-marker user-profile-icon"></i> <b>UBICACION</b> :AQUI VA LA UBICACION:</li>
												<li><i class="fa fa-map-marker user-profile-icon"></i> <b>DEPARTAMENTO</b> :AQUI VA EL DEPARTAMENTO:</li>
												<li><i class="fa fa-map-marker user-profile-icon"></i> <b>PUESTO</b> :AQUI VA EL PUESTO:</li>
												<li><i class="fa fa-map-marker user-profile-icon"></i> <b>PUESTO</b> :AQUI VA EL PUESTO:</li>
												<li><i class="fa fa-map-marker user-profile-icon"></i> <b>PAGO</b> :QUINCENAL:</li>
												<li><i class="fa fa-map-marker user-profile-icon"></i> <b>NUMERO DE EMPLEADO</b> :#12312312:</li>
											</ul>

											<a class="btn btn-success"><i class="fa fa-edit m-right-xs"></i>Editar </a><br>

											<!-- start skills -->
											<h4>Secciones a cubrir para este colaborador</h4>
											<p>
												<span class="btn btn-success"></span> Indica que el apartado ha sido procesado <br>
												<span class="btn btn-danger"></span> Indica que el apartado no ha sido procesado <br>
											</p>
											<ul class="list-unstyled user_data">
												<li>
													<p>Incentivos para este periodo</p>
													<div class="progress progress_sm">
														<div class="progress-bar bg-green" role="progressbar" data-transitiongoal="100" style="width: 100%;" aria-valuenow="49"></div>
													</div>
												</li>
												<li>
													<p>Horas extra para este periodo</p>
													<div class="progress progress_sm">
														<div class="progress-bar bg-green" role="progressbar" data-transitiongoal="100" style="width: 100%;" aria-valuenow="69"></div>
													</div>
												</li>
												<li>
													<p>Domingo(Procesos - Laborador)</p>
													<div class="progress progress_sm">
														<div class="progress-bar bg-green" role="progressbar" data-transitiongoal="100" style="width: 100%;" aria-valuenow="29"></div>
													</div>
												</li>
												<li>
													<p>Existencia de botes</p>
													<div class="progress progress_sm">
														<div class="progress-bar bg-green" role="progressbar" data-transitiongoal="120" style="width: 100%;" aria-valuenow="49"></div>
													</div>
												</li>
											</ul>
											<!-- end of skills -->
											<!-- start skills -->
											<h4>Operaciones que puedes visualizar para este colaborador </h4>
											<ul class="list-unstyled user_data">
												<li>
													<a href="" class="btn btn-dark"> Incidencias</a>
												</li>
											</ul>
											<!-- end of skills -->
										</div>

									</div>
									<div class="tab-pane" id="horas-extra">
										
										<div class="col-md-10 col-sm-10 col-xs-12">

											<p class="lead">Agregar horas extra</p>

											<div class="dataTable_wrapper">
												<table class="table table-striped table-bordered table-hover" id="muestra-cupones">
													<thead>
														<tr>
															<th>Cantidad</th>
														</tr>
													</thead>
													<tbody>
														<tr>
															<td>
																<div class="form-group">
																	<div class="col-md-12 col-sm-12 col-xs-12">
																		<select class="form-control" name="horas_extra" id="horas_extra">
																			<option value="" disabled selected>Cantidad de horas extra</option>
																			<?php for ($i=0; $i <= 500; $i++) { $horas = ($i == 1 ) ? "hora " : "horas"; ?>
																				<option value="<?php echo $i; ?>"><?php echo "$i $horas"; ?></option>
																			<?php } ?>
																		</select>
																	</div>
																</div>
															</td>
															<td>
																<input type="submit" class="btn btn-dark" value="Agregar horas extra">
															</td>
														</tr>	
													</tbody>
												</table>
											</div>

										</div>

									</div>
									<div class="tab-pane" id="domingos">

										<p class="lead">Domingo</p>

										<p>Selecciona la opción de domingo que quieras agregar al colaborador.</p>

										<ul>
											<li>Domingo de procesos</li>
											<li>Domingo laborado</li>
										</ul>

									</div>
									<div class="tab-pane " id="incentivos">

										<div class="col-md-12 col-sm-12 col-xs-12">

											<p class="lead">Incentivos Asignados al colaborador</p>

											<div class="dataTable_wrapper">
												<table class="table table-striped table-bordered table-hover">
													<thead>
														<tr>
															<th>#</th>
															<th>Nombre Incentivo</th>
															<th>Fijo</th>
															<th>Repetir</th>
															<th>Cantidad</th>
															<th>Multiplicaodr</th>
															<th>Total</th>
															<th>Agregar</th>
														</tr>
													</thead>
													<tbody>
														<?php echo $addTableIncentivos; ?>
													</tbody>
												</table>
											</div>


											<p class="lead">Incentivos que se han agregado</p>

											<div class="dataTable_wrapper">
												<table class="table table-striped table-bordered table-hover">
													<thead>
														<tr>
															<th>#</th>
															<th>Nombre Incentivo</th>
															<th>Fijo</th>
															<th>Repetir</th>
															<th>Cantidad</th>
															<th>Multiplicaodr</th>
															<th>Total</th>
															<th>Agregar</th>
														</tr>
													</thead>
													<tbody>
														<tr>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
														</tr>	
													</tbody>
												</table>
											</div>

										</div>
										
									</div>

									<div class="tab-pane " id="botes">
										
									</div>

									<div class="tab-pane" id="faltas">
										<p class="lead">Resumen asistencia</p>
									</div>
								</div>
							</div>

							<div class="clearfix"></div>
						</div>
					</div>

						
				</div>
			</div>
			<!-- /page content -->
		</div>
	</div>

<?php echo $footer; ?>