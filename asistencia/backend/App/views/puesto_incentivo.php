<?php echo $header;?>
<div class="right_col">
  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    <div class="x_panel tile fixed_height_240">
      <div class="x_title">
        <br><br>
        <h1>Indique el Procentaje Asignado para el Incentivo</h1>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <form class="form-horizontal" id="add" action="/Puesto/puestoIncentivoEdit" method="POST">
          <div class="form-group ">
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="">Ultimo porcentaje ingresado:</label>
              <div class="col-md-3 col-sm-3 col-xs-12">
                <label class="control-label col-md-7 col-xs-12" for=""><?php echo $quebrado; ?></label>
              </div>
              <div class="col-md-5 col-sm-3 col-xs-12">
                <label class="control-label col-md-7 col-xs-12" for=""><?php echo $solido; ?></label>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="quebrado">
                <?php echo $incentivo = 'Quebrados'; ?>
                <span class="required">*</span>
              </label>
              <div class="col-md-3 col-sm-3 col-xs-12">
                <input type="number" max="100" min="0" class="form-control col-md-7 col-xs-12" placeholder="Porcentaje de puesto" name="quebrado" id="quebrado" value="100" required>
              </div>

              <label class="control-label col-md-1 col-sm-1 col-xs-12" for="solido">
                <?php echo $incentivo = 'Solidos'; ?>
                <span class="required">*</span>
              </label>
              <div class="col-md-3 col-sm-3 col-xs-12">
                <input type="number" max="100" min="0" class="form-control col-md-7 col-xs-12" placeholder="Porcentaje de puesto" name="solido" id="solido" value="44" required>
              </div>
            </div>

            <div class="form-group">
            <br>
              <div class="col-md-10 col-sm-10 col-xs-12 col-md-offset-4 ">
                <button class="btn btn-danger col-md-2 col-sm-2 col-xs-12" type="button" id="btnCancel">Cancelar</button>
                <button class="btn btn-success col-md-2 col-sm-2 col-xs-12" type="submit" id="btnAdd">Guardar</button>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php echo $footer;?>
