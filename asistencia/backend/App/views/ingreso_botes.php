<?php echo $header;?>
<div class="right_col">
  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    <div class="x_panel tile fixed_height_240">
      <div class="x_title">
        <br><br>
        <h1>Agregar botes usuario</h1>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <form class="form-horizontal" id="add" action="/Incentivo/agregarBotes" method="POST">
          <div class="form-group ">

            <input type="hidden" name="status" id="status" value="">
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="nombre">
                Busque el Nombre:
                <span class="required">*</span>
              </label>
              <div class="col-md-3 col-sm-3 col-xs-12">
                <select class="form-control" name="nombre" id="nombre" required>
                  <option hidden value=""> -- selecciona -- </option>
                  <?php echo $nombres; ?>
                </select>
              </div>
              <label class="control-label col-md-1 col-sm-1 col-xs-12" for="dia">
                Dia:
                <span class="required">*</span>
              </label>
              <div class="col-md-3 col-sm-3 col-xs-12">
                <select class="form-control" name="dias" id="dias" required>
                <option hidden value=""> -- selecciona -- </option>
                  <?php echo $dias; ?>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="yema">
                Yema
                <span class="required">*</span>
              </label>
              <div class="col-md-3 col-sm-3 col-xs-12">
                <input type="number" step="00.5" max="100" min="0" class="form-control col-md-7 col-xs-12" placeholder="Botes yema" name="yema" id="yema" value="" required>
              </div>

              <label class="control-label col-md-1 col-sm-1 col-xs-12" for="clara">
                Clara
                <span class="required">*</span>
              </label>
              <div class="col-md-3 col-sm-3 col-xs-12">
                <input type="number" step="00.5" max="100" min="0" class="form-control col-md-7 col-xs-12" placeholder="Botes clara" name="clara" id="clara" value="" required>
              </div>
            </div>

            <div class="form-group">
            <br>
              <div class="col-md-10 col-sm-10 col-xs-12 col-md-offset-4 ">
                <button class="btn btn-danger col-md-2 col-sm-2 col-xs-12" type="button" id="btnCancel">Cancelar</button>
                <button class="btn btn-success col-md-2 col-sm-2 col-xs-12" type="submit" id="btnAdd">Guardar</button>
              </div>
            </div>
            <br>
            <div class="panel panel-default">
              <div class="x_title">
                <h2> Avance de colaboradores</h2>
                <div class="clearfix"></div>
              </div>
              <div class="panel-body">
                <div class="dataTable_wrapper">
                  <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
                    <thead>
                      <tr>
                        <th>Nombre</th>
                        <th>Botes Yema</th>
                        <th>Meta Yema</th>
                        <th>Restante Yema</th>
                        <th>Botes Clara</th>
                        <th>Meta Clara</th>
                        <th>Restante Clara</th>
                        <th>Status</th>
                      </tr>
                    </thead>
                    <tbody id="registros">
                      <?= $tabla; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php echo $footer;?>
