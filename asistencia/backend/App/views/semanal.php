<?php echo $header; ?>
<div class="right_col">
  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">

    <div class="panel panel-default">

      <div class="x_title">
        <br><br>
        <h1> Resumen Semanal</h1>
        <div class="clearfix"></div>
      </div>

      <form name="all" id="all" action="/Semanal/generar" method="POST">
          <div class="control-label col-md-6 col-sm-6 col-xs-6">
              <label class="control-label col-md-3 col-sm-3 col-xs-3" for="descripcion">Periodo :<span class="required">*</span></label>
              <div class="control-group">
                  <div class="col-md-9 col-sm-9 col-xs-9 xdisplay_inputx form-group has-feedback">
                    <select class="form-control" name="periodo_id" id="periodo_id">
                      <?php echo $sPeriodo; ?>
                    </select>
                  </div>
              </div>
          </div>

          <div class="control-label col-md-3 col-sm-3 col-xs-12">
            <button class="btn btn-success" id="btnAplicar" type="button">Aplicar</button>
            <button class="btn btn-success" id="btnGuardar" type="button">Cerrar Periodo</button>

          </div>

        </div>

        <div class="panel-body">
          <div class="dataTable_wrapper">
            <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
              <thead>
                <tr id="encabezado">
                  <!-- <th><input type="checkbox" name="checkAll" id="checkAll" value=""/></th> -->
                  <th>No. Empleado</th>
                  <th>Nombre</th>
                  <th>Departamento</th>
                  <th>Dia 1</th>
                  <th>Dia 2</th>
                  <th>Dia 3</th>
                  <th>Dia 4</th>
                  <th>Dia 5</th>
                  <th>Dia 6</th>
                  <th>Dia 7</th>
                </tr>
              </thead>
              <tbody id="registros">
                <?php echo $tabla; ?>
              </tbody>
            </table>
          </div>
        </div>
      </form>
	<div id="respuesta">
	</div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
</div>
