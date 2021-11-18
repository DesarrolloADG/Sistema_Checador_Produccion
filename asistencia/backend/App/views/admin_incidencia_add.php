<?php echo $header;?>
<div class="right_col">
  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    <div class="x_panel tile fixed_height_240">
      <div class="x_title">
        <br><br>
        <h1>Agregar Incidencia</h1>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <form class="form-horizontal" id="add" action="/Incidencia/incidenciaAdd" method="POST">
          <div class="form-group ">

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="fecha">Fecha: <span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" name="fecha" id="fecha" class="form-control col-md-6 col-xs-12" value="<?= $fecha?>" readonly>
              </div>
            </div>

            <div class="form-group" id="contenedor_fecha_fin">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="fecha_fin">Fecha Fin: <span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" name="fecha_fin" id="fecha_fin" class="form-control col-md-6 col-xs-12" value="<?= $fecha?>" readonly>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="my-checkbox">Rango de Fechas: <span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <input name="rango_fechas" id="rango_fechas" type="checkbox" name="my-checkbox" checked>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="comentario">Comentario: <span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" name="comentario" id="comentario" class="form-control col-md-6 col-xs-12">
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="pago">Incidencia: <span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <select class="form-control" name="incidencia_id" id="incidencia_id">
                  <option value="">Selecciona una incidencia</option>
                  <?=$sIncidencia?>
                </select>
              </div>
            </div>

            <input type="hidden" name="colaborador_id" id="colaborador_id" value="<?php echo $colaborador_id; ?>">
            <input type="hidden" name="vista" id="vista" value="<?php echo $vista; ?>">
            <input type="hidden" name="periodo_id" id="periodo_id" value="<?php echo $periodo_id; ?>">

            <div class="form-group">
              <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-2 col-xs-offset-3">
                <a href="<?php echo $direccionamiento; ?>" class="btn btn-danger col-md-3 col-sm-3 col-xs-5" >Cancelar</a>
                <button class="btn btn-success col-md-3 col-sm-3 col-xs-5" id="btnAdd" type="submit">Agregar</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer;?>
