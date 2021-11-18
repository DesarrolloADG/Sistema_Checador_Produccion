<?php echo $header;?>
<div class="right_col">
  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    <div class="x_panel tile fixed_height_240">
      <div class="x_title">
        <br><br>
        <h1>Agregar Nuevo Lector</h1>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <form class="form-horizontal" id="add" action="/Lectores/lectoresAdd" method="POST">
          <div class="form-group ">

	    <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="tipo_comunicacion">Ubicaci&oacute;n Lector <span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" class="form-control col-md-7 col-xs-12" placeholder="Nombre" name="nombre" id="nombre">
              </div>
              <span id="availability"></span>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="tipo_comunicacion">Tipo de Comunicaci&oacute;n <span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" class="form-control col-md-7 col-xs-12" placeholder="Tipo de Comunicaci&oacute;n" name="tipo_comunicacion" id="tipo_comunicacion">
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="ip_comunicacion">IP Comunicaci&oacute;n <span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <!--input type="text" class="form-control col-md-7 col-xs-12" placeholder="IP de Comunicaci&oacute;n" name="ip_comunicacion" id="ip_comunicacion"  data-inputmask="'mask': '999.999.999.999'"-->
                <input type="text" class="form-control col-md-7 col-xs-12" name="ip_comunicacion" id="ip_comunicacion" placeholder="IP de Comunicaci&oacute;n" maxlength="15">
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="Puerto">Puerto <span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="input" class="form-control col-md-7 col-xs-12" placeholder="Puerto, ejemplo: 8080" name="puerto" id="puerto" maxlength="4">
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="Puerto">IDENTIFICADOR <span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <select class="form-control col-md-7 col-xs-12" name="identificador" id="identificador">
                  <option value="" disabled selected>Selecciona un identificador</option>
                  <option value="xochimilco_entrada">XOCHIMILCO</option>
                  <option value="vallejo_entrada">VALLEJO</option>
                  <option value="gatsa_entrada">GATSA</option>
                  <option value="unidesh_entrada">UNIDESH</option>
                  <option value="produccion_entrada">PRODUCCION</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="descripcion">Descripcion <span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <textarea class="form-control" name="descripcion" id="descripcion" placeholder="Descripci&oacute;n del lector"></textarea>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="estatus">Estatus:<span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <select class="form-control col-md-7 col-xs-12" name="status" id="status">
                  <option value="" disabled selected>Selecciona un estatus</option>
                  <?php echo $optionStatus; ?>
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
