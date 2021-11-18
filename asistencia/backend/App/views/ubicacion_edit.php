<?php echo $header;?>
<div class="right_col">
  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    <div class="x_panel tile fixed_height_240">
      <div class="x_title">
        <br><br>
        <h1><small> Editar ubicaci&oacute;n</small></h1>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <form class="form-horizontal" id="edit" action="/Ubicacion/ubicacionEdit" method="POST">
          <div class="form-group ">

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="nombre">Nombre <span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" name="nombre" id="nombre" class="form-control col-md-7 col-xs-12" placeholder="Nombre de ubicaci&oacute;n" value="<?php echo $ubicacion['nombre']; ?>">
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="descripcion">Status<span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <select class="form-control" name="status" id="status">
                  <?php echo $sStatus; ?>
                </select>
              </div>
            </div>

            <input type="hidden" name="catalogo_ubicacion_id" id="catalogo_ubicacion_id" value="<?php echo $ubicacion['catalogo_ubicacion_id']; ?>"/>

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
