<?php echo $header; ?>
<div class="right_col">
  
  <div class="row">
    <div class="col-md-9 col-sm-9 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2 style="font-size: 25px;">Incentivos <small>asignar</small></h2>
          <ul class="nav navbar-right panel_toolbox">
            <form action="/{$seccion}/" method="POST">
              <input type="hidden" name="colaborador_id" value="<?php echo $colabordor_id ?>">
              <input type="hidden" name="prorrateo_periodo_id" value="<?php echo $prorrateo_periodo_id ?>">
              <span class="btn btn-info"><span class="glyphicon glyphicon-chevron-left" style="color:white;" style="color:white;"></span><input class="" style="background:none; border: none; color:white;" type="submit" value="Regresar a incentivos" ></span>
            </form>

          </ul>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <ul class="list-unstyled msg_list" style="margin-right: -40px;">
            <li>
              <a>
                <span class="image">
                  <img src="images/img.jpg" alt="img" style="width: 75px; height: 75px;" />
                </span>
                <span>
                  <span>Nombre del Colaborador</span>
                  <b><span class="time"><?php echo "{$colaborador[nombre]} {$colaborador[apellido_paterno]} {$colaborador[apellido_materno]} - {$colaborador[sexo]}"; ?></span></b>
                </span><br>
                <span class="message">
                  <span>Lector/Zona</span>
                  <b><span class="time"><?php echo "{$colaborador[nombre_lector]} / {$colaborador[nombre_ubicacion]}"; ?></span></b>
                </span>
                <span class="message">
                  <span>#Empleado/Identificador</span>
                  <b><span class="time"><?php echo "{$colaborador[numero_empleado]} / {$colaborador[numero_identificador]}"; ?></span></b>
                </span>
                <span class="message">
                  <span>Puesto/Pago</span>
                  <b><span class="time"><?php echo "{$colaborador[nombre_puesto]} / {$colaborador[pago]}"; ?></span></b>
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
          <br><h3><?php echo $procesoPeriodo ?></h3>
          <div class="clearfix"></div>
        </div>
        <div style="text-align:center; vertical-align:middle;">
          <h1><?php echo $cantidadIncentivos; ?></h1>
          <h2>Incentivos Asignados</h2> <br>
        </div>
      </div>
    </div>
  </div>
    
  <div class="row">
    <div class="col-md-4 col-sm-4 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2>Lista de incentivos <small>Asignada</small></h2>
          <ul class="nav navbar-right panel_toolbox">
          </ul>
          <div class="clearfix"></div>
        </div>
        
        <form name="form-add" id="form-add" action="/AsignarIncentivos/checarSiAplicaIncentivo" method="POST">
          <div>
            <p>
              <?php echo $mensajeIncentivo; ?>
            </p>
            <div style="<?php echo $btnAddIncentivos; ?>">
              <input type="hidden" name="colaborador_id" value="<?php echo $colabordor_id ?>">
              <input type="hidden" name="prorrateo_periodo_id" value="<?php echo $prorrateo_periodo_id ?>">
              <button id="delete12" type="submit" class="btn btn-success btn-circle" style="<?php echo $display; ?>"><i class="fa fa-add"> Validar y agregar todos los incentivos</i></button> <span></span>
            </div>
          </div>
        </form>
        
        <div class="x_content">
          <form name="form-add-one" id="form-one" action="/AsignarIncentivos/asigarnarIncentivo" method="POST">
            <ul class="list-unstyled timeline" id="lista">
              <input type="hidden" name="colaborador_id" value="<?php echo $colabordor_id ?>" id="colaborador_id">
                <input type="hidden" name="prorrateo_periodo_id" value="<?php echo $prorrateo_periodo_id ?>" id="prorrateo_periodo_id">
              <?php echo $listaIncentivos ?>
            </ul>
          </form>
        </div>
      </div>
    </div>

    <div class="col-md-7 col-sm-7 col-xs-12 col-lg-8">
      <div class="panel panel-default">
        <form name="form-delete" id="form-delete" action="/AsignarIncentivos/delete" method="POST">
          <div class="panel-body">
            <div class="dataTable_wrapper">
              <input type="hidden" name="colaborador_id" value="<?php echo $colabordor_id ?>">
              <input type="hidden" name="prorrateo_periodo_id" value="<?php echo $prorrateo_periodo_id ?>">
              <button id="delete" type="button" class="btn btn-danger btn-circle" style="<?php echo $display; ?>" ><i class="fa fa-remove"> </i></button>
              <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
                <thead>
                  <tr>
                    <th><input type="checkbox" name="checkAll" id="checkAll" value=""/></th>
                    <th>Nombre</th>
                    <th>Cantidad</th>
                    <th>Tipo</th>
                    <th>Aplica este Periodo</th>
                    <th>Asignado</th>
                    <th>Nota</th>
                  </tr>
                </thead>
                <tbody>
                  <?= $tabla; ?>
                </tbody>
              </table>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php echo $footer; ?>
