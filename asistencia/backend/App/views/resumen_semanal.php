<?php echo $header; ?>
<div class="right_col">
  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    <div class="panel panel-default">
      <div class="x_title">
        <div class="clearfix"></div>
        <br><br><h1> Resumen Semanal</h1>
        <div class="jumbotron" id="alerta">
          <h3><strong>Atensión</strong> El periodo seleccionado esta abierto.</h3>
        </div>
      </div>
      <form name="all" id="all" action="/ResumenQuincenal/generarExcel" method="POST" target="_blank">
        <div class="form-group">

          <div class="col-md-6 col-sm-6 col-xs-6">
            <label class="control-label col-md-3 col-sm-3 col-xs-3" for="descripcion">Periodo :</label>
            <div class="control-group">
              <div class="col-md-9 col-sm-9 col-xs-9 xdisplay_inputx form-group has-feedback">
                <select class="form-control" name="periodo_id" id="periodo_id" style="text-align:center;">
                      <?php echo $sPeriodo; ?>
                    </select>
              </div>
            </div>
          </div>
          
          <div class="col-md-6 col-sm-6 col-xs-6 form-group" align="center" style="<?php echo $displayBtn; ?>">
            <div class="col-md-3 col-sm-3 col-xs-3" hidden>
              <button class="btn btn-primary" id="btnAplicar" type="button">Buscar</button>
            </div>
            <div class="col-md-3 col-sm-3 col-xs-3">
              <button class="btn btn-primary" id="btnGuardar" type="button">Cerrar</button>
            </div>
            <div class="col-md-3 col-sm-3 col-xs-3" hidden>
              <button class="btn btn-danger" id="btnCancelarPeriodo" type="button">Cancelar</button>
            </div>
            <div class="col-md-3 col-sm-3 col-xs-3">
              <button class="btn btn-warning" id="btnRespaldarPeriodo" type="button">Respaldar</button>
            </div>
            <div class="col-md-3 col-sm-3 col-xs-3">
              <button class="btn btn-primary" id="btnRestaurarPeriodo" type="button">Restaurar</button>
            </div>
          </div>

          <div class="panel-body col-md-12 col-sm-12 col-xs-12 col-lg-12">
            <div class="dataTable_wrapper table table-responsive" id="contenedor_tabla">
            </div>
          </div>

        </div>
      </form>
    </div>
  </div>
</div>
<?php echo $footer; ?>