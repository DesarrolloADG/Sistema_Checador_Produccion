<?php echo $header; ?>
<div class="right_col">
  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    <div class="panel panel-default">
      <div class="x_title">
        <br><br>
        <h1> Horario de Incidencias </small></h1>

      </div>
      <form name="all" id="all" action="/HorarioIncidencia/Busqueda" method="POST">

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
