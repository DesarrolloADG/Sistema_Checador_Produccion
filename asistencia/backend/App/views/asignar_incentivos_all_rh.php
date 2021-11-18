<?php echo $header; ?>
<div class="right_col">
  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    <div class="panel panel-default">
      <!--div class="x_title">
        <br><br>
        <h1> Incentivos</h1>
        <div class="clearfix"></div>
      </div-->
      <form name="all" id="all" action="/AsignarIncentivos/" method="POST">
        <div class="panel-body">
          <input type="hidden" value="procesado" name="action">
          <label class="control-label col-md-12 col-sm-12 col-xs-12" for="nombre"> </label>

          <div class="row">
            
            <div class="form-group">
              <div class="col-md-3 col-sm-3 col-xs-12 ">
                <label for="">Selecciona los tipos de usuario</label>
                <input type="checkbox" <?php echo $checked; ?> class="switch" data-on-text="Quincenales" data-off-text="Semanales" value="1" name="busqueda" >
              </div>
  
              <div class="col-md-7 col-sm-7 col-xs-12 ">
                <label for="">Tipo de periodo</label>
                <select name="tipo_periodo_semanal" class="form-control" id="tipo_periodo_semanal">
                  <?php echo $semanales; ?>
                </select>
                <select name="tipo_periodo_quincenal" class="form-control" id="tipo_periodo_quincenal">
                  <?php echo $quincenales; ?>
                </select>
              </div>
              <div class="col-md-2 col-sm-2 col-xs-4 ">
                <label for=""><br></label>
                <button class="btn btn-info col-md-12 col-sm-12 col-xs-12" type="submit" id="buscar_periodo">Buscar</button>
              </div>

            </div>
            
          </div>
        
        </div>
        
        <div class="panel-body">
          <div class="dataTable_wrapper">
            <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
              <thead>
                <tr>
                  <th>Foto</th>
                  <th>Nombre</th>
                  <th>Departamentos</th>
                  <th>Periodo</th>
                  <th>Incentivos</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php echo $tabla; ?>
              </tbody>
            </table>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<?php echo $footer; ?>