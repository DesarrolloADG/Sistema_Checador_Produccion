<?php echo $header;?>
<div class="right_col">
  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    <div class="x_panel ">
      <div class="x_title">
        <br><br>
        <h1>Editar lector </h1>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <form class="form-horizontal" id="add" action="/Lectores/lectoresUpdate" method="POST">
          <div class="form-group ">

	    <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="tipo_comunicacion">Ubicaci&oacute;n <span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" class="form-control col-md-7 col-xs-12" placeholder="Nombre" name="nombre" id="nombre" value="<?php echo $setDataLector['nombre']; ?>">
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="tipo_comunicacion">Tipo de Comunicaci&oacute;n <span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" class="form-control col-md-7 col-xs-12" placeholder="Tipo de Comunicaci&oacute;n" name="tipo_comunicacion" id="tipo_comunicacion" value="<?php echo $setDataLector['tipo_comunicacion'] ?>">
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="ip_comunicacion">IP Comunicaci&oacute;n <span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" class="form-control col-md-7 col-xs-12" placeholder="IP de Comunicaci&oacute;n" name="ip_comunicacion" id="ip_comunicacion" value="<?php echo $setDataLector['ip_lector'] ?>">
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="Puerto">Puerto <span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" class="form-control col-md-7 col-xs-12" placeholder="Puerto, ejemplo: 8080" name="puerto" id="puerto" value="<?php echo $setDataLector['puerto'] ?>">
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="descripcion">Descripcion <span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <textarea class="form-control" name="descripcion" id="descripcion" placeholder=""><?php echo $setDataLector['descripcion'] ?></textarea>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="estatus">Estatus:<span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <select class="form-control col-md-7 col-xs-12" name="status" id="status">
                <?php echo $optionStatus; ?>
                </select>
              </div>
            </div>

            <input type="hidden" name="catalogo_lector_id" id="catalogo_lector_id" value="<?php echo $setDataLector['catalogo_lector_id'];?>">

            <div class="form-group">
            <br>
              <div class="col-md-12 col-sm-12 col-xs-12 ">
                <button class="btn btn-danger col-md-3 col-sm-3 col-xs-3 col-md-offset-3 " type="button" id="btnCancel">Cancelar</button>
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
