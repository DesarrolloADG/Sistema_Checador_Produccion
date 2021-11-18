<?php echo $header; ?>
<div class="right_col">
  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    <div class="panel panel-default">
      <div class="x_title">
        <div class="clearfix"></div>
        <h1> Incentivos</h1>
        <div class="jumbotron" style="background: <?php echo $color; ?>">
          <h3><?php echo $texto ?></h3>
        </div>
      </div>
      <form name="all" id="all" action="/AdminIncidencia/" method="POST">
        <div class="panel-body">

          <div class="row">
            
            <div class="form-group">
              <div class="col-md-10 col-sm-10 col-xs-12 ">
                <select name="tipo_periodo" class="form-control">
                   <?php echo $periodos; ?>
                </select>
              </div>
              <div class="col-md-2 col-sm-2 col-xs-4 ">
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
                  <th><input type="checkbox" name="checkAll" id="checkAll" value=""/></th>
                  <th>Foto</th>
                  <th>Número Empleado</th>
                  <th>Nombre</th>
                  <th>Empresa</th>
                  <th>Departamento</th>
                  <th>Estatus</th>
                  <th <?= $editarHidden?>>Acciones</th>
                </tr>
              </thead>
            <tbody id="registros">
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
