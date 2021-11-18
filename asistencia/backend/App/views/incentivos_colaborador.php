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
									<li class=""><?php echo $regreso ?></li>
									<li class="active"><a href="#colaborador" data-toggle="tab" aria-expanded="false">Informaci&oacute;n del colaborador</a></li>
									<li class=""><a href="#incentivos" data-toggle="tab" aria-expanded="false">Incentivos</a></li>
									<li class=""><a href="#faltas" data-toggle="tab" aria-expanded="false">Resumen Asistencia</a></li>
									<li class=""><a href="#horas-extra" data-toggle="tab" aria-expanded="false">Horas Extra</a></li>
									<li class=""><a href="#domingos" data-toggle="tab" aria-expanded="false">Domingo</a></li>
									<?php if($colaborador['catalogo_lector_id'] == 3){ ?>
										<li class=""><a href="#asignarbotes" data-toggle="tab" aria-expanded="false">Botes </a></li>
									<?php } ?>
								</ul>
							</div>

							<div class="col-xs-9">
								<!-- Tab panes -->
								<div class="tab-content">
									<!-- PERFIL DEL COLABORADOR -->
									<div class="tab-pane active" id="colaborador">
										<p class="lead">Informacion del colaborador</p>


										<div class="col-md-12 col-sm-12 col-xs-12 profile_left">
											<?php if($faltasPeriodo != '0'){ ?>
												<div class="alert alert-danger" role="alert"> Tienes una cantidad de <?php echo $faltasPeriodo ?> faltas.  </div>
											<?php }else{ ?>
												<div class="alert alert-success" role="alert"> No tienes faltas.  </div>
											<?php } ?>

											<h3><?php echo "{$colaborador['nombre']} {$colaborador['apellido_paterno']} {$colaborador['apellido_materno']}" ?></h3>

											<ul class="list-unstyled user_data">
												<li><i class="glyphicon glyphicon-barcode user-profile-icon"></i> <b>LECTOR</b> <?php echo $colaborador['nombre_lector']; ?></li>
												<li><i class="fa fa-map-marker user-profile-icon"></i> <b>UBICACION</b> <?php echo $colaborador['nombre_ubicacion']; ?></li>
												<li><i class="glyphicon glyphicon-check user-profile-icon"></i> <b>PUESTO</b> <?php echo $colaborador['nombre_puesto']; ?></li>
												<li><i class="glyphicon glyphicon-usd user-profile-icon"></i> <b>PAGO</b> <?php echo $colaborador['pago']; ?></li>
												<li><i class="glyphicon glyphicon-list-alt user-profile-icon"></i> <b>NUMERO DE EMPLEADO</b> <?php echo $colaborador['numero_identificador']; ?></li>
											</ul>
											<!-- start skills -->
											<h4>Resumen de la seccion</h4>

											<table class="table table-striped table-bordered table-hover">
												<thead>
													<tr>
														<td>Lista</td>
														<td>Accion</td>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td style="text-align: left;">Horas extra</td>
														<td style="<?php echo $colorhtmlHorasExtra; ?>"> <?php echo $alertaHorasExtra; ?></td>
													</tr>
													<tr>
														<td style="text-align: left;">Domingo</td>
														<td style="<?php echo $colorhtmlDomingo; ?>"> <?php echo $alertaDomingo; ?></td>
													</tr>
													<tr>
														<td style="text-align: left;">Incentivos asignadados desde colaborador</td>
														<td style="<?php echo $colorHtmlListaIncentivos; ?>"> <?php echo $alertaIncentivos; ?></td>
													</tr>
													<tr>
														<td style="text-align: left;">hay incentivos asignado para el prorrateo</td>
														<td style="<?php echo $colorHtmlResumenIncentivosAsignados; ?>"> <?php echo $alertaIncentivosAsignados; ?></td>
													</tr>
												</tbody>
											</table>

											<table class="table table-striped table-bordered table-hover">
												<thead>
													<tr>
														<th>PERCEPCIONES</th>
														<th>VALOR</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td style="text-align: left;">ESTA CANTIDAD DE SALARIO DIARIO, ES INFORMATIVA:</td>
														<td>$<?php echo $salMinimo; ?></td>
													</tr>
													<tr>
														<td style="text-align: left;">VALOR TOTAL DE HORAS EXTRA QUE HAN SIDO ASIGNADOS PARA EL PRORRATEO</td>
														<td><?php echo $calculoHorasExtra; ?> </td>
													</tr>
													<tr>
														<td style="text-align: left;">TOTAL DE DOMINGO DE PROCESOS/LABORADO PARA USAR EN EL PRORRATEO</td>
														<td><?php echo $domingo; ?></td>
													</tr>
													<tr>
														<td style="text-align: left;">VALOR TOTAL DE INCENTIVOS PARA ASIGNARLOS EN EL PRORRATEO</td>
														<td><?php echo $cantidadIncentivos; ?></td>
													</tr>
													<tr>
														<td style="text-align: left;">TOTAL</td>
														<td><?php echo $sumaTotalPercepciones; ?></td>
													</tr>
												</tbody>
											</table>
											<!-- end of skills -->
											<!-- start skills -->
											<h4>Operaciones que puedes visualizar para este colaborador </h4>
											<ul class="list-unstyled user_data">
												<li>
													<a href="/Incidencia/checadorFechas/<?php echo $colaborador['catalogo_colaboradores_id']; ?>/<?php echo $periodoId; ?>/semanales/" target="_blank" class="btn btn-dark"> Incidencias</a>
													<a href="/Colaboradores/edit/<?php echo $colaborador['catalogo_colaboradores_id']; ?>" target="_blank" class="btn btn-dark"><i class="fa fa-edit m-right-xs"></i>Editar </a><br>
												</li>
											</ul>
											<!-- end of skills -->
										</div>

									</div>

									<!-- AGREGAR HORAS EXTRA -->
									<div class="tab-pane" id="horas-extra">
										
										<div class="col-md-12 col-sm-12 col-xs-12">

											<p class="lead">Agregar horas extra</p>

											<div class="col-md-12 col-sm-12 col-xs-12">
												<div class="x_panel">
													<div style="text-align:center; vertical-align:middle;">
														<h3>HORAS EXTRA</h3>
														<h1> <?php echo $horasExtra; ?> | <?php echo $setcalculoHorasExtraPrecio; ?></h1>
														<div class="panel-body" style="<?php echo $btnAddIncentivos; ?>">
															
																<?php echo $seleccionHorasExtra; ?>		
															
														</div>
													</div>
												</div>
											</div>

										</div>

									</div>

									<!-- DOMINGO DE PROCESOS -->
									<div class="tab-pane" id="domingos">

										<p class="lead">DOMINGO (PROCESOS - LABORADO)</p>

										<div class="row">
											<div class="col-md-12 col-xs-12">
												<div class="x_panel">
													<div class="x_content">
														<form action="/Incentivo/<?php echo $accionesComplementarias; ?>/" method="POST">
															<input type="hidden" value="<?php echo $colaboradorId; ?>" name="colaborador_id">
															<input type="hidden" value="<?php echo $periodoId; ?>" name="prorrateo_periodo_id">
															<input type="hidden" value="<?php echo $tipo; ?>" name="regreso">
															<div class="col-md-12 col-sm-12 col-xs-12">
																<div class="x_panel">
																	<div class="row">
																		<div class="col-md-4 col-sm-4 col-xs-4">
																			<div class="form-group">
																				<label class="control-label col-md-12 col-sm-12 col-xs-12" for="descripcion">Domingo de Procesos </label>
																				<div class="col-md-12 col-sm-12 col-xs-12" style="text-align: center;">
																					<input name="domingoProcesos" id="domingoProcesos" type="checkbox" data-on-text="SI" data-off-text="NO" value="<?php echo $domingoProcesos ?>" <?php echo $checkdomingoProcesos; ?> >
																				</div>
																				<div class="col-md-12 col-sm-12 col-xs-12" style="text-align: center;">
																					<?php echo $domingoProcesos; ?>
																				</div>
																			</div>
																		</div>
																		<div class="col-md-4 col-sm-4 col-xs-4">
																			<div class="form-group">
																				<label class="control-label col-md-12 col-sm-12 col-xs-12" for="descripcion">Domingo Laborado </label>
																				<div class="col-md-12 col-sm-12 col-xs-12" style="text-align: center;">
																					<input name="domingoLaborado" id="domingoLaborado" type="checkbox" data-on-text="SI" data-off-text="NO" value="<?php echo $domingoLaborado ?>" <?php echo $checkdomingoLaborado; ?> >
																				</div>
																				<div class="col-md-12 col-sm-12 col-xs-12" style="text-align: center;"><?php echo $domingoLaborado; ?></div>
																			</div>
																		</div>
																		<div class="col-md-3 col-sm-3 col-xs-3" style="<?php echo $btnAddIncentivos; ?>">
																			<div class="form-group">
																				<label class="control-label col-md-4 col-sm-4 col-xs-12" for="descripcion"> </label><br>
																				<div class="col-md-12 col-sm-12 col-xs-12">
																					<input type="submit" class="form-control col-md-12 col-xs-12 btn <?php echo $btnAccionesComplementarias; ?>" value="<?php echo $showTextBtnActualizar ?>">
																				</div>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</form>
													</div>
												</div>
											</div>
										</div>

									</div>

									<!-- INCENTIVOS -->
									<div class="tab-pane " id="incentivos">

										<p class="lead">Incentivos Asignados al colaborador</p>

										<div class="row" style=" display: none;">
											<div class="col-md-12 col-xs-12">
												<div class="x_panel">
													<div class="x_title">
														<h2>PRODUCCION DE BOTES TEST</h2>
														<ul class="nav navbar-right panel_toolbox">
															<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
															<li><a class="close-link"><i class="fa fa-close"></i></a></li>
														</ul>
														<div class="clearfix"></div>
													</div>
													<div class="x_content">
														
														<div class="col-md-12 col-sm-12 col-xs-12">
															<form action="/Incentivo/addBotes/" method="POST">
																<table class="table table-striped table-bordered table-hover">
																	<div style=" <?php echo $displayBotesHistoricos; ?> ">
																		<input type="submit" class="btn btn-info" value="Agrear" class="form-control col-md-4 col-sm-4 col-xs-4" >
																	</div>
																	<input type="hidden" value="<?php echo $colaboradorId; ?>" name="colaborador_id">
																	<input type="hidden" value="<?php echo $periodoId; ?>" name="prorrateo_periodo_id">
																	<input type="hidden" value="<?php echo $tipo; ?>" name="regreso">
																	<input type="hidden" value="48" name="incentivo">
																	<input type="hidden" value="<?php echo $cantidad_inicial; ?>" name="cantidad_inicial">
																	<thead>
																		<tr>
																			<th><?php echo $botes; ?> </th>
																			<th style="vertical-align: middle;">
																				<input type="checkbox" class="switch switch-requerida" data-on-text="SI" data-off-text="NO" name="botes_incentivo"> 
																				El valor por los botes es: $ <label for="" id="valorGlobal"> <?php echo (!empty($incentivoBotes['cantidad'])) ? $incentivoBotes['cantidad'] : "0" ?> </label>
																			</th>
																		</tr>
																	</thead>
																	<tbody id="tbody">
																		<tr>
																			<td style="vertical-align: middle; text-align: left; " >SE EXCEDIO DE MAS </td>
																			<td style="vertical-align: middle; text-align: left; ">
																				<input type="checkbox" class="switch switch-excedente" data-on-text="SI" data-off-text="NO" name="botes_max">
																			</td>
																		</tr>
																		<tr class="botex_max">
																			<td style="vertical-align: middle; text-align: left; ">EXCEDENTE BOTES</td>
																			<td>
																				<select class="form-control" name="cantidad_botes" id="cantidad_botes">
																					<option value="" disabled selected>Selecciona los botes excedentes</option>
																					<?php for ($i=0; $i <= 500; $i++) { ?>
																						<option value=" <?php echo $i; ?> " > <?php echo $i; ?> </option>
																					<?php } ?>
																				</select>
																			</td>
																		</tr>
																		<tr class="botex_max">
																			<td style="vertical-align: middle; text-align: left;">PRECIO</td>
																			<td> <input type="text" name="botes" id="botes" class="form-control col-md-12 col-sm-12 col-xs-12" placeholder=""> </td>
																		</tr>
																		<tr class="botex_max">
																			<td style="vertical-align: middle; text-align: right; "> VALOR POR EXCEDENTE DE BOTES</td>
																			<td> 
																				<input type="text" class="form-control col-md-11 col-sm-11 col-xs-11 cantidadbotes" readonly> 
																			</td>
																		</tr>

																		<tr class="botex_max">
																			<td style="vertical-align: middle; text-align: right; "> <span id="textoOpe"></span> </td>
																			<td> 
																				<input type="text" class="form-control col-md-12 col-sm-12 col-xs-12 cantidadTotalBotes" readonly> 
																				<input type="hidden" name="valor_botes_total" class="form-control col-md-12 col-sm-12 col-xs-12 cantidadTotalBotes"> 
																			</td>
																		</tr>
																	</tbody>
																</table>
															</form>
														</div>
													</div>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-md-12 col-xs-12">
												<div class="x_panel">
													<div class="x_title">
														<h2>LISTA DE INCENTIVOS QUE PUEDEN SER ASIGNADOS AL COLABORADOR<small> </small></h2>
														<ul class="nav navbar-right panel_toolbox">
															<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
															<li><a class="close-link"><i class="fa fa-close"></i></a></li>
														</ul>

														<div class="clearfix"></div>
													</div>
													<div class="x_content">
														<form class="form-horizontal form-label-left input_mask" action="/Incentivo/addIncentivosColaborador/" method="POST">
															<input type="hidden" name="cantidad" value="<?php echo $cantidad; ?>">
															<input type="hidden" name="colaboradorId" value="<?php echo $colaboradorId; ?>">
															<input type="hidden" name="periodoId" value="<?php echo $periodoId; ?>">
															<input type="hidden" name="tipo" value="<?php echo $tipo; ?>">
															<div class="form-group">
																<div class="col-md-9 col-sm-9 col-xs-12" style=" <?php echo $btnAddIncentivos; ?>">
																	<input type="submit" class="btn btn-<?php echo $btn; ?>" value="<?php echo $btnIncentivos; ?>" />
																</div>
															</div>

															<table class="table table-striped table-bordered table-hover" id="muestra-cupones">
																<thead>
																	<tr>
																		<th></th>
																		<th>Nombre Incentivo</th>
																		<th>Es Fijo </th>
																		<th>Se puede repetir</th>
																		<th>Cantidad del incentivo</th>
																		<th>Multiplicador de incentivo</th>
																		<td>Total</td>
																		<th>Agregar SI/NO</th>
																	</tr>
																</thead>
																<tbody>
																	<?php echo $tabla; ?>
																</tbody>
															</table>

														</form>
													</div>
												</div>
											</div>
										</div>

										<div class="row" style=" <?php echo $display; ?>">
											<div class="col-md-12 col-xs-12">
												<div class="x_panel">
													<div class="x_title">
														<h2>INCENTIVOS ASIGNADOS "<?php echo $msjPeriodo ?>"</h2>
														<ul class="nav navbar-right panel_toolbox">
															<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
															<li><a class="close-link"><i class="fa fa-close"></i></a></li>
														</ul>

														<div class="clearfix"></div>
													</div>
													<div class="x_content">
														<form class="form-horizontal form-label-left input_mask" id="all" action="/Incentivo/deleteIncentivosColaborador" method="POST">
															<div class="col-md-9 col-sm-9 col-xs-12" style=" <?php echo $btnAddIncentivos; ?>">
																<button id="delete" type="button" class="btn btn-danger btn-circle"><i class="fa fa-remove"> <b>ELIMINAR</b></i></button>
															</div>
															<input type="hidden" name="colaboradorId" value="<?php echo $colaboradorId; ?>">
															<input type="hidden" name="periodoId" value="<?php echo $periodoId; ?>">
															<input type="hidden" name="tipo" value="<?php echo $tipo; ?>">
															<div class="col-md-12 col-xs-12">
																<?php echo $coloreshtml; ?>
															</div>
															<table class="table table-striped table-bordered table-hover" id="muestra-cupones">
																<thead>
																	<tr>
																		<th><input type="checkbox" name="checkAll" id="checkAll" value=""/></th>
																		<th></th>
																		<th>Nombre Incentivo</th>
																		<th>Descripci&oacute;n</th>
																		<th>Tipo</th>
																		<th>Fijo</th>
																		<th>Duplicar</th>
																		<td>Total</td>
																	</tr>
																</thead>
																<tbody>

																	<?php echo $tablaEliminarIncentivos; ?>
																</tbody>
															</table>
															
														</form>
													</div>
												</div>
											</div>
										</div>
									</div>

									<!-- RESUMEN DE ASISTENCIA -->
									<div class="tab-pane" id="faltas">
										<p class="lead">Resumen asistencia</p>

										<?php if($faltasPeriodo != '0'){ ?>
											<div class="alert alert-danger" role="alert"> Tienes una cantidad de <?php echo $faltasPeriodo ?> faltas.  </div>
										<?php }else{ ?>
											<div class="alert alert-success" role="alert"> No tienes faltas.  </div>
										<?php } ?>
										<div class="row">
											<div class="col-md-12 col-xs-12">
												<div class="x_panel">
													<div class="x_title">
														<h2>ASISTENCIA DEL COLABORADOR </h2>
														<ul class="nav navbar-right panel_toolbox">
															<li><a class="collapse-link collapse-link-1-faltas"><i class="fa fa-chevron-up"></i></a></li>
															<li><a class="close-link"><i class="fa fa-close"></i></a></li>
														</ul>

														<div class="clearfix"></div>
													</div>
													<div class="x_content">
														<?php echo $resumenes; ?>
													</div>
												</div>
											</div>
										</div>
									</div>

									<!-- BOTES -->
									<div class="tab-pane " id="asignarbotes">
										<p class="lead">Botes extra</p>


										<div class="row" style="">
											<div class="col-md-12 col-xs-12">
												<div class="x_panel">
													<div class="x_title">
														<h2><?php echo $msj_botes_suma; ?></h2>
														<div class="clearfix"></div>
													</div>
													<div class="x_content" style="">
														<div class="col-md-12 col-sm-12 col-xs-12">
															<?php echo $textoBotesNoAsignados; ?>
															<div>
																<p>La siguiente tabla es para visualizar la meta de botes a cumplir.</p>
																<table class="table table-striped table-bordered table-hover" style=" ">
																	<thead>
																		<tr>
																			<th>Clara</th>
																			<th>Yema</th>
																			<th>Huevo liquido</th>
																		</tr>
																	</thead>
																	<tbody>
																		<?php echo $botesAsignar; ?>		
																	</tbody>
																</table>
															</div>
															<form action="/Incentivo/addBotesAsignadosMas/" method="POST">
																<p>
																	<?php echo $mensajeDeAsignacionPrecioBotes; ?>
																</p>
																<table class="table table-striped table-bordered table-hover">
																	<thead>
																		<tr>
																			<th> Lista </th>
																			<th> Cantidad de botes</th>
																			<th> Valor del bote </th>
																			<th> Total </th>
																		</tr>
																	</thead>
																	<tbody>
																		<tr style="<?php echo $display_clara; ?>">
																			<th> Clara </th>
																			<td>
																				<input type="text" class="form-control" name="select_cantidad_botes_1" id="select_cantidad_botes_1">
																				<!--select class="form-control" name="select_cantidad_botes_1" id="select_cantidad_botes_1">
																					<option value="" disabled selected>Selecciona los botes excedentes</option>
																					<?php for ($i=0; $i <= 500; $i++) { ?>
																						<option value=" <?php echo $i; ?> " > <?php echo $i; ?> </option>
																					<?php } ?>
																				</select-->
																			</td>
																			<td> 
																				<input type="text" class="form-control col-md-11 col-sm-11 col-xs-11" id="input_cantidad_botes_precio_1" value="<?php echo $clara_valor; ?>" readonly> 
																			</td> 
																			<td> 
																				<input type="text" class="form-control col-md-12 col-sm-12 col-xs-12 input_contidad_total_1" readonly> 
																				<input type="hidden" name="input_contidad_total_1" class="form-control col-md-12 col-sm-12 col-xs-12 input_contidad_total_1"> 
																			</td>
																		</tr>
																		<tr style="<?php echo $display_yema; ?>">
																			<th> Yema </th>
																			<td>
																				<input type="text" class="form-control" name="select_cantidad_botes_2" id="select_cantidad_botes_2">
																				<!--select class="form-control" name="select_cantidad_botes_2" id="select_cantidad_botes_2">
																					<option value="" disabled selected>Selecciona los botes excedentes</option>
																					<?php for ($i=0; $i <= 500; $i++) { ?>
																						<option value=" <?php echo $i; ?> " > <?php echo $i; ?> </option>
																					<?php } ?>
																				</select-->
																			</td>
																			<td> 
																				<input type="text" class="form-control col-md-11 col-sm-11 col-xs-11" id="input_cantidad_botes_precio_2" value="<?php echo $yema_valor; ?>" readonly> 
																			</td> 
																			<td> 
																				<input type="text" class="form-control col-md-12 col-sm-12 col-xs-12 input_contidad_total_2" readonly> 
																				<input type="hidden" name="input_contidad_total_2" class="form-control col-md-12 col-sm-12 col-xs-12 input_contidad_total_2"> 
																			</td>
																		</tr>
																		<tr style="<?php echo $display_huevo_liquido; ?>">
																			<th> Huevo Liquido </th>
																			<td>
																				<input type="text" class="form-control" name="select_cantidad_botes_3" id="select_cantidad_botes_3">
																				<!--select class="form-control" name="select_cantidad_botes_3" id="select_cantidad_botes_3">
																					<option value="" disabled selected>Selecciona los botes excedentes</option>
																					<?php for ($i=0; $i <= 500; $i++) { ?>
																						<option value=" <?php echo $i; ?> " > <?php echo $i; ?> </option>
																					<?php } ?>
																				</select-->
																			</td>
																			<td> 
																				<input type="text" class="form-control col-md-11 col-sm-11 col-xs-11" id="input_cantidad_botes_precio_3" value="<?php echo $huevo_liquido_valor; ?>" readonly> 
																			</td> 
																			<td> 
																				<input type="text" class="form-control col-md-12 col-sm-12 col-xs-12 input_contidad_total_3" readonly> 
																				<input type="hidden" name="input_contidad_total_3" class="form-control col-md-12 col-sm-12 col-xs-12 input_contidad_total_3">
																			</td>
																		</tr>
																		<tr>
																			<th> </th>
																			<td> </td>
																			<td> total</td> 
																			<td> 
																				<input type="hidden" name="clara_valor" value="<?php echo $clara_valor; ?>">
																				<input type="hidden" name="yema_valor" value="<?php echo $yema_valor; ?>">
																				<input type="hidden" name="huevo_liquido_valor" value="<?php echo $huevo_liquido_valor; ?>">
																				<input type="text" class="form-control col-md-12 col-sm-12 col-xs-12 cantidad_asignar_botes" readonly> 
																				<input type="hidden" name="cantidad_asignar_botes" class="form-control col-md-12 col-sm-12 col-xs-12 cantidad_asignar_botes"> 
																				<input type="hidden" name="colaborador_id" value="<?php echo $colaboradorId; ?>">
																				<input type="hidden" name="prorrateo_periodo_id" value="<?php echo $periodoId; ?>">
																				<input type="hidden" value="<?php echo $tipo; ?>" name="regreso">
																				<input type="hidden" name="incentivo" value="47" >
																				<input type="hidden" name="incentivoAdicional" value="48" >
																				<input type="hidden" name="cantidad_nuevo_incentivo_por_cumplir_todos_incentivos" value="<?php echo $cantidad_nuevo_incentivo_por_cumplir_todos_incentivos; ?>" >
																				<input type="hidden" name="precio_por_bote" value="<?php echo $cantidadASumar; ?>">
																			</td>
																		</tr>
																	</tbody>
																</table>
																<div class="x_title">
																	<div class="col-md-9 col-sm-9 col-xs-12" style=" <?php echo $btnAddIncentivos; ?>">
																		<input type="submit" class="btn btn-success" value="AGREGAR">
																	</div>
																	<div class="clearfix"></div>
																</div>
															</form>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								
								</div>
							</div>
						</div>
					</div>
			<!-- /page content -->
				</div>
			</div>
		</div>
	</div>

<?php echo $footer; ?>