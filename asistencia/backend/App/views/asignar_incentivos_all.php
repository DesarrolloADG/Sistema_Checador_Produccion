<?php echo $header; ?>
<div class="right_col">
  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    <div class="panel panel-default">
      <div class="x_title">
        <div class="clearfix"></div>
        <h1> Incentivos <?php echo $semanales; ?></h1>
        <div class="jumbotron" style="background: <?php echo $msjbackground; ?>">
          <h3><?php echo $textoPeriodo ?></h3>
        </div>
      </div>

      <?php echo $periodoSemanal; ?>
      <?php $action = ($periodoSemanal == 2) ? "/AsignarIncentivos/incentivosSemanales/":"/AsignarIncentivos/"; ?>
      <form name="all" id="all" action=<?php echo $action; ?> method="POST">
        <div class="panel-body">

          <div class="row">
            
            <div class="form-group">
              <div class="col-md-10 col-sm-10 col-xs-12 ">
                <select name="tipo_periodo" class="form-control">
                   <?php echo $semanal; ?>
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