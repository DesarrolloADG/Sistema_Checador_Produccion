<?php echo $header;?>
<div class="right_col">
  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    <div class="x_panel tile fixed_height_240">
      <div class="x_title">
        <br><br>
        <h1>Editar D&iacute;a Festivo</h1>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <form class="form-horizontal" id="edit" action="/Diasfestivos/diafestivoEdit" method="POST">
          <div class="form-group ">

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="nombre">Nombre del d&iacute;a festivo:<span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" name="nombre" id="nombre" class="form-control col-md-7 col-xs-12" value="<?php echo $Diasfestivos['nombre'] ?>" placeholder="nombre del dia festivo"></label>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="descripcion">Descripci&oacute;n <span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <textarea class="form-control" name="descripcion" id="descripcion" placeholder="descripcion del dia festivo"><?php echo $Diasfestivos['descripcion'] ?></textarea>
              </div>
            </div>

            <div class="form-group">
              <fieldset>
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="fecha">Fecha <span class="required">*</span></label>
                <div class="control-group">
                  <div class="controls">
                    <div class="col-md-6 col-sm-6 col-xs-12 xdisplay_inputx form-group has-feedback">
                      <input type="text" id="single_cal2" name="fecha" value="<?php echo $Diasfestivos['fecha']; ?>" class="form-control has-feedback-left" placeholder="fecha" aria-describedby="inputSuccess2Status2" >
                      <span class="fa fa-calendar-o form-control-feedback left" aria-hidden="true"></span>
                      <span id="inputSuccess2Status2" class="sr-only">(success)</span>
                    </div>
                  </div>
                </div>
              </fieldset>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="status">N&uacute;mero de Retardos:<span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <select class="form-control col-md-7 col-xs-12" name="status" id="status">
                  <option value="" disabled selected>Selecciona un estatus</option>
                  <?php echo $status; ?>
                </select>
              </div>
            </div>

            <input type="hidden" name="catalogo_dia_festivo_id" id="catalogo_dia_festivo_id" value="<?php echo $Diasfestivos['catalogo_dia_festivo_id'];?>">

            <div class="form-group">
            <br>
              <div class="col-md-12 col-sm-12 col-xs-12  ">
                <button class="btn btn-danger col-md-3 col-sm-3 col-xs-3 col-md-offset-3" type="button" id="btnCancel">Cancelar</button>
                <button class="btn btn-success col-md-3 col-sm-3 col-xs-3" type="submit" id="btnAdd">Actualizar</button>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php echo $footer;?>
