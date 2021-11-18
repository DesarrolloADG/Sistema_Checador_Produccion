<?php echo $header;?>
<div class="right_col">
  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    <div class="x_panel tile fixed_height_240">
      <div class="x_title">
        <br><br>
        <h1>Agregar Nueva Incidencia</h1>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <form class="form-horizontal" id="add" action="/Incidencias/incidenciasAdd" method="POST">
          <div class="form-group ">

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="identificador_incidencia">Identificador Incidencia <span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" class="form-control col-md-7 col-xs-12" placeholder="Ingresa el identificador de la incidencia" name="identificador_incidencia" id="identificador_incidencia">
              </div>
              <span id="availability1"></span>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="nombre">Nombre <span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" class="form-control col-md-7 col-xs-12" placeholder="Ingresa el nombre de la incidencia" name="nombre" id="nombre">
              </div>
              <span id="availability2"></span>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="descripcion">Descripci&oacute;n <span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <textarea class="form-control" name="descripcion" id="descripcion" placeholder="Descripci&oacute;n de la incidencia"></textarea>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="estatus">Color:<span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <input class="form-control" id="color" name="color" type="text">
              </div>
            </div>

 	    <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="estatus">Genera falta:<span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <select class="form-control col-md-7 col-xs-12" name="genera_falta" id="genera_falta">
                  <option value="0" selected>No</option>
		  <option value="1">Si</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="estatus">Status:<span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <select class="form-control col-md-7 col-xs-12" name="status" id="status">
                  <option value="" disabled selected>Selecciona un estatus</option>
                  <?php echo $status; ?>
                </select>
              </div>
            </div>

            <div class="form-group">
            <br>
              <div class="col-md-10 col-sm-10 col-xs-12 col-md-offset-2 ">
                <button class="btn btn-danger col-md-3 col-sm-3 col-xs-3" type="button" id="btnCancel">Cancelar</button>
                <button class="btn btn-primary col-md-3 col-sm-3 col-xs-3" type="reset" >Resetear</button>
                <button class="btn btn-success col-md-3 col-sm-3 col-xs-3" type="submit" id="btnAdd">Agregar</button>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php echo $footer;?>
