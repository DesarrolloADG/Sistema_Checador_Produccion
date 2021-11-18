<?php echo $header;?>
<div class="right_col">
  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    <div class="x_panel tile fixed_height_240">
      <div class="x_title">
        <br><br>
        <h1> Horario <small> Editar Horario</small></h1>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <form class="form-horizontal" id="edit" action="/Horarios/horarioEdit" method="POST">
          <div class="form-group ">

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="nombre">Nombre <span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" class="form-control col-md-7 col-xs-12" placeholder="Nombre de horario" name="nombre" id="nombre" value="<?php echo $horario['nombre'];?>">
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="nombre">Hora Entrada:<span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <select class="form-control col-md-7 col-xs-12" name="hora_entrada" id="hora_entrada">
                  <option value="" disabled selected>Selecciona una hora de entrada</option>
                  <?php echo $hora_entrada; ?>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="nombre">Hora Salida:<span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <select class="form-control col-md-7 col-xs-12" name="hora_salida" id="hora_salida">
                  <option value="" disabled selected>Selecciona una hora de salida</option>
                  <?php echo $hora_salida; ?>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="nombre">Tolerancia Entrada:<span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="col-md-10 col-sm-10 col-xs-12">
                    <input class="form-control" type="number" id="tolerancia_entrada" name="tolerancia_entrada" value="<?php echo $horario['tolerancia_entrada']; ?>">
                </div>
                <label class="control-label col-md-2 col-sm-2 col-xs-2" for="nombre">Minutos</label>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="nombre">Dias Laborales:<span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                  <?php echo $sDiasLaborales; ?>

              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="nombre">Número de retardos hacen 1 falta:<span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <input class="form-control" type="number" name="numero_retardos" id="numero_retardos" value="<?php echo $horario['numero_retardos']; ?>">
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="status">Status<span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <select class="form-control" name="status" id="status">
                  <option value="" disabled>Selecciona un estatus</option>
                  <?php echo $sStatus; ?>
                </select>
              </div>
            </div>

            <input type="hidden" name="catalogo_horario_id" id="catalogo_horario_id" value="<?php echo $horario['catalogo_horario_id'];?>">

            <div class="form-group">
            <br>
              <div class="col-md-10 col-sm-10 col-xs-12 col-md-offset-3 ">
                <button class="btn btn-danger col-md-3 col-sm-3 col-xs-4" type="button" id="btnCancel">Cancelar</button>
                <button class="btn btn-success col-md-3 col-sm-3 col-xs-4" type="submit" id="btnAdd">Actualizar</button>
              </div>
            </div>


          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php echo $footer;?>
