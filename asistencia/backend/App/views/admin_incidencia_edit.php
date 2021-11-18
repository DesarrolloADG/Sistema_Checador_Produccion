 <?php echo $header;?>
<div class="right_col">
  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    <div class="x_panel tile fixed_height_240">
      <div class="x_title">
        <br><br>
        <h1>Justificar Faltas a <?php echo utf8_encode($colaborador['nombre'])." ".utf8_encode($colaborador['apellido_paterno'])." ".utf8_encode($colaborador['apellido_materno']);  ?></h1>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <form class="form-horizontal" id="edit">
          <div class="form-group ">

            <div class="form-group col-md-4 col-sm-4 col-xs-4">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="descripcion">Inicio</label>
              <div class="col-md-9 col-sm-9 col-xs-12 xdisplay_inputx form-group has-feedback">
                <input <?php echo $hidden; ?> type="text" id="fecha_inicio" name="fecha_inicio" class="form-control has-feedback-left" placeholder="Fecha Inicio" aria-describedby="inputSuccess2Status2" value="<?php echo $fecha_ini; ?>">
                <span class="fa fa-calendar-o form-control-feedback left" aria-hidden="true"></span>
                <span id="inputSuccess2Status2" class="sr-only">(success)</span>
              </div>
            </div>

            <div class="form-group col-md-4 col-sm-4 col-xs-4">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="descripcion">Fin</label>
                <div class="col-md-9 col-sm-9 col-xs-12 xdisplay_inputx form-group has-feedback">
                  <input type="text" id="fecha_fin" name="fecha_fin" class="form-control has-feedback-left" placeholder="Fecha Fin" aria-describedby="inputSuccess2Status2" value="<?php echo $fecha_fin; ?>">
                  <span class="fa fa-calendar-o form-control-feedback left" aria-hidden="true"></span>
                  <span id="inputSuccess2Status2" class="sr-only">(success)</span>
                </div>
            </div>

            <div class="form-group col-md-4 col-sm-4 col-xs-4">
              <div class="col-md-12 col-sm-12 col-xs-12 xdisplay_inputx form-group has-feedback">
                <button class="btn btn-success" id="btnAplicar" type="button">Aplicar</button>
                <a href="/AdminIncidencia/" class="btn btn-danger" >Regresar</a>
              </div>
            </div>


            <input type="hidden" name="catalogo_colaboradores_id" id="catalogo_colaboradores_id" value="<?php echo $colaborador['catalogo_colaboradores_id']; ?>">
            <input type="hidden" name="numero_empleado" id="numero_empleado" value="<?php echo $colaborador['numero_empleado']; ?>">

            <div class="panel-body">

              <div class="dataTable_wrapper">
                <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
                  <thead>
                    <tr>
                      <th>Dia</th>
                      <th>Fecha</th>
                      <th>Entrada</th>
                      <th>Salida</th>
                      <th>Entrada Registrada</th>
                      <th>Salida Registrada</th>
                      <th>Comentario</th>
                      <th>Incidencia</th>
                      <th>Acción</th>
                    </tr>
                  </thead>
                <tbody id="registros">
                  <?php echo $tabla; ?>
                </tbody>
                </table>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php echo $footer;?>
