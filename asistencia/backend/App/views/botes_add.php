<?php echo $header;?>
<div class="right_col">
  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    <div class="x_panel tile fixed_height_240">
      <div class="x_title">
        <br><br>
        <h1>Meta de botes para el periodo</h1>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <form class="form-horizontal" id="add" action="<?php echo $form; ?>" method="POST">
          <div class="form-group ">

            <div class="form-group">
              <label class="control-label col-md-2 col-sm-2 col-xs-12" for="nombre">Periodo <span class="required">*</span></label>
              <div class="col-md-8 col-sm-8 col-xs-12">
                <select class="form-control" name="periodo" id="periodo">
                  <option value="" disabled selected>Selecciona el periodo </option>
                  <?php echo $option; ?>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-2 col-sm-2 col-xs-12" for="nombre">Clara <span class="required">*</span></label>
              <div class="col-md-8 col-sm-8 col-xs-12">
                <select class="form-control" name="clara" id="clara">
                  <option value="" disabled selected>Selecciona la cantidad de cubetas a cubrir para clara.</option>
                  <?php echo $clara; ?>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-2 col-sm-2 col-xs-12" for="nombre">Yema <span class="required">*</span></label>
              <div class="col-md-8 col-sm-8 col-xs-12">
                <select class="form-control" name="yema" id="yema">
                  <option value="" disabled selected>Selecciona la cantidad de cubetas a cubrir para yema.</option>
                  <?php echo $yema; ?>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-2 col-sm-2 col-xs-12" for="nombre">huevo liquido <span class="required">*</span></label>
              <div class="col-md-8 col-sm-8 col-xs-12">
                <select class="form-control" name="huevoliquido" id="huevoliquido">
                  <option value="" disabled selected>Selecciona la cantidad de cubetas a cubrir para huevo liquido.</option>
                  <?php echo $huevoliquido; ?>
                </select>
              </div>
            </div>

            <div class="form-group">
              <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-2 col-xs-offset-3">
                <a class="btn btn-danger col-md-3 col-sm-3 col-xs-5" href="/Incentivo/botes/">Cancelar</a>
                <button class="btn btn-<?php echo $class ?> col-md-3 col-sm-3 col-xs-5" id="btnAdd" type="submit"><?php echo $btn; ?></button>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php echo $footer;?>
