<?php echo $header; ?>
<script type="text/javascript"></script>
<div class="right_col">
  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    <div class="panel panel-default">
      <div class="x_title">
        <h1> Colaboradores en Baja</h1>
        <div class="clearfix"></div>
      </div>
      <div class="panel-body">
        <div class="dataTable_wrapper">
          <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
            <thead>
              <tr>
                <th>Nombre</th>
                <th>Fecha Alta</th>
                <th>Fecha Baja</th>
                <th>Descripcion</th>
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
</div>
<?php echo $footer; ?>
