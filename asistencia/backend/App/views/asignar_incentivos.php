<?php echo $header; ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

<div class="right_col">
  <div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <?php echo $infoPeriodo; ?> 
    </div>
  </div>

  <div class="row">
    <div class="col-md-6 col-sm-6 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h3 style="font-size: 25px;">Informacion del colaborador </h3>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <ul class="list-unstyled msg_list" style="margin-right: 0px;">
            <li>
              <a>
                <span class="message">
                  <span><b>Colaborador: </b> <?php echo "{$colaborador['nombre']} {$colaborador['apellido_paterno']} {$colaborador['apellido_materno']}"; ?></span>
                </span>
                <span class="message">
                  <b>Genero</b> <?php echo "{$colaborador['sexo']}" ?>
                </span>
                <span class="message">
                  <span><b>Lector:</b> <?php echo "{$colaborador['nombre_lector']} "; ?></span></span>
                </span>
                <span class="message">
                  <span><b>Zona:</b> <?php echo "{$colaborador['nombre_ubicacion']}"; ?></span></span>
                </span>
                <span class="message">
                  <span><b>#Empleado</b> <?php echo "{$colaborador['numero_empleado']}"; ?> </span>
                </span>
                <span class="message">
                  <span><b>Identificador:</b> <?php echo "{$colaborador['numero_identificador']}"; ?></span>
                </span>
                <span class="message">
                  <span><b>Puesto/Pago:</b> <?php echo "{$colaborador['nombre_puesto']} / {$colaborador['pago']}"; ?></span></span>
                </span>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <div class="col-md-3 col-sm-3 col-xs-12">
      <div class="x_panel">
        <div style="text-align:center; vertical-align:middle;">
          <h3>HORAS EXTRA</h3>
          <h1> <?php echo $horasExtra; ?></h1>
          <div class="panel-body" style="<?php echo $display ?>">
            <?php echo $seleccionHorasExtra; ?>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-3 col-sm-3 col-xs-12">
      <div class="x_panel">
        <div style="text-align:center; vertical-align:middle;">
          <br><br><h3><?php echo strtoupper($procesoPeriodo) ?></h3>
          <h1><?php echo $cantidadIncentivos; ?></h1>
          <h2>Incentivos <br> Asignados</h2> <br><br>
        </div>
      </div>
    </div>

    <form action="/Incentivo/<?php echo $accionesComplementarias; ?>/" method="POST">
      <input type="hidden" value="<?php echo $colaborador_id; ?>" name="colaborador_id">
      <input type="hidden" value="<?php echo $prorrateo_periodo_id; ?>" name="prorrateo_periodo_id">
      <input type="hidden" value="<?php echo $regreso; ?>" name="regreso">

      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">

          <div class="row">
            <div class="col-md-4 col-sm-4 col-xs-4">
              <div class="form-group">
                <label class="control-label col-md-12 col-sm-12 col-xs-12" for="descripcion">Domingo de Procesos </label>
                <div class="col-md-3 col-sm-3 col-xs-3">
                  <input name="domingoProcesos" id="domingoProcesos" type="checkbox" data-on-text="SI" data-off-text="NO" value="<?php echo $domingoProcesos ?>" <?php echo $checkdomingoProcesos; ?> >
                </div>
                <div class="col-md-3 col-sm-3 col-xs-3">
                  <?php echo $domingoProcesos; ?>
                </div>
              </div>
            </div>

            <div class="col-md-4 col-sm-4 col-xs-4">
              <div class="form-group">
                <label class="control-label col-md-12 col-sm-12 col-xs-12" for="descripcion">Domingo Laborado </label>
                <div class="col-md-3 col-sm-3 col-xs-3">
                  <input name="domingoLaborado" id="domingoLaborado" type="checkbox" data-on-text="SI" data-off-text="NO" value="<?php echo $domingoLaborado ?>" <?php echo $checkdomingoLaborado; ?> >
                </div>
                <div class="col-md-3 col-sm-3 col-xs-3">
                  <?php echo $domingoLaborado; ?>
                </div>
              </div>
            </div>

            <div class="col-md-3 col-sm-3 col-xs-3" style="<?php echo $display; ?>">
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

  <div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2>Incentivos Asignados <b> Periodo </b><?php echo $procesoPeriodo ?> </h2>
          <ul class="nav navbar-right panel_toolbox">
            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
          </ul>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <div class="panel-body">
            <div class="row">

              <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <label class="control-label col-md-12 col-sm-12 col-xs-12" for="nombre">Selecciona la accion que deseas buscar<span class="required"></span></label>
                    <div class="row">
                      <div class="col-md-10 col-sm-10 col-xs-12">
                        <select class="form-control" name="horas_extra" id="horas_extra_1">
                          <option value="" disabled selected>Selecciona una accion</option>
                          <option <?php echo $accionSinIncentivos; ?> value="1">Lista de Incentivos que se pueden asignar</option>
                          <option <?php echo $accionConIncentivos; ?> value="2">Lista de Incentivos que han sido asignados</option>
                          <option value="3">Lista para eliminar incentivos</option>
                        </select>
                      </div>
                    </div>
                </div>

              </div>

            </div>
          </div>
          <form name="form-add" id="tabla_incentivos_para_asignar" action="/Incentivo/agregarIncentivos" method="POST">
            <input type="hidden" value="<?php echo $colaborador_id; ?>" name="colaborador_id">
            <input type="hidden" value="<?php echo $prorrateo_periodo_id; ?>" name="prorrateo_periodo_id">
            <input type="hidden" value="<?php echo $regreso; ?>" name="regreso">
            
            <?php echo $regreso; ?>
            
            <div class="panel-body" style="<?php echo $display ?>">
              <input type="submit" class="btn btn-primary btn-circle" value="Agregar Incentivos">
            </div>
            
            <div class="panel-body">
              <div class="dataTable_wrapper">
                <table class="table table-striped table-bordered table-hover" id="muestra-cupones1">
                  <thead>
                    <tr>
                      <td style="text-align:center; vertical-align:middle;">Nombre</td>
                      <td style="text-align:center; vertical-align:middle;">Cantidad</td>
                      <td style="text-align:center; vertical-align:middle;">Descripción</td>
                      <td style="text-align:center; vertical-align:middle;">Tipo</td>
                      <!--td style="text-align:center; vertical-align:middle;">Nota</td-->
                      <td style="text-align:center; vertical-align:middle;">Aplica</td>
                      <td style="text-align:center; vertical-align:middle;">Faltas</td>
                      <td style="text-align:center; vertical-align:middle;">Duplicar</td>
                    </tr>
                  </thead>
                  <tbody>
                    <?php echo $tabla; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </form>

          <form name="form-update" id="tabla_incentivos_asignados" action="/Incentivo/updateIncentivos" method="POST">
            <input type="hidden" value="<?php echo $colaborador_id; ?>" name="colaborador_id">
            <input type="hidden" value="<?php echo $prorrateo_periodo_id; ?>" name="prorrateo_periodo_id">
            <input type="hidden" value="<?php echo $regreso; ?>" name="regreso">
            <div class="panel-body" style="<?php echo $display ?>">
              <input type="submit" class="btn btn-info btn-circle" value="Actualizar la lista de incentivos">
            </div>
            <div class="panel-body">
              <div class="dataTable_wrapper">
                <table class="table table-striped table-bordered table-hover" id="muestra-cupones2">
                  <thead>
                    <tr>
                      <td style="text-align:center; vertical-align:middle;">Nombre</td>
                      <td style="text-align:center; vertical-align:middle;">Cantidad</td>
                      <td style="text-align:center; vertical-align:middle;">Descripción</td>
                      <td style="text-align:center; vertical-align:middle;">Tipo</td>
                      <!--td style="text-align:center; vertical-align:middle;">Nota</td-->
                      <td style="text-align:center; vertical-align:middle;">Aplica</td>
                      <td style="text-align:center; vertical-align:middle;">Faltas</td>
                      <td style="text-align:center; vertical-align:middle;">Duplicar</td>
                    </tr>
                  </thead>
                  <tbody>
                    <?php echo $tablaAsignados; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </form>

          <form name="form-delete" id="tabla_incentivos_borrar" action="/Incentivo/deleteIncentivos" method="POST">
            <input type="hidden" value="<?php echo $colaborador_id; ?>" name="colaborador_id">
            <input type="hidden" value="<?php echo $prorrateo_periodo_id; ?>" name="prorrateo_periodo_id">
            <input type="hidden" value="<?php echo $regreso; ?>" name="regreso">
            
              <div class="panel-body" style="<?php echo $display ?>">
                <button id="delete" type="button" class="btn btn-danger btn-circle"><i class="fa fa-remove"> <b>Eliminar incentivos ya asignados</b></i></button>
              </div>
            
            <div class="panel-body">
              <div class="dataTable_wrapper">
                <table class="table table-striped table-bordered table-hover" id="tabla-muestra-borrar">
                  <thead>
                    <tr>
                      <td style="text-align:center; vertical-align:middle;"><input type="checkbox" name="checkAll" id="checkAll" value=""/></td>
                      <td style="text-align:center; vertical-align:middle;">Nombre</td>
                      <td style="text-align:center; vertical-align:middle;">Cantidad</td>
                      <td style="text-align:center; vertical-align:middle;">Descripción</td>
                      <td style="text-align:center; vertical-align:middle;">Tipo</td>
                      <!--td style="text-align:center; vertical-align:middle;">Asignado</td-->
                      <td style="text-align:center; vertical-align:middle;">Aplicacion</td>
                      <td style="text-align:center; vertical-align:middle;">Nota</td>
                    </tr>
                  </thead>
                  <tbody>
                    <?php echo $tablaEliminar; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>


</div>

<?php echo $footer; ?>
