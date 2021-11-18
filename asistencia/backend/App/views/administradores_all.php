<?php echo $header; ?>
<div class="right_col">
  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    <div class="panel panel-default">
      <div class="x_title">
        <br><br>
        <h1> Administradores</h1>
        <div class="clearfix"></div>
      </div>
      <form name="all" id="all" action="/Administradores/delete" method="POST">
        <div class="panel-body">
          <a href="/Administradores/add" type="button" class="btn btn-primary btn-circle"><i class="fa fa-plus"> <b>Nueva Administrador</b></i></a>
          <button id="delete" type="button" class="btn btn-danger btn-circle"><i class="fa fa-remove"> <b>Eliminar</b></i></button>
          <button id="export_pdf" type="button" class="btn btn-info btn-circle"><i class="fa fa-file-pdf-o"> <b>Exportar a PDF</b></i></button>
          <button id="export_excel" type="button" class="btn btn-success btn-circle"><i class="fa fa-file-excel-o"> <b>Exportar a Excel</b></i></button>
        </div>
        <div class="panel-body">
          <div class="dataTable_wrapper">
            <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
              <thead>
                <tr>
                  <th style="vertical-align:middle;"><input type="checkbox" name="checkAll" id="checkAll" value=""/></th>
                  <th style="vertical-align:middle;">Datos Usuario</th>
                  <th style="vertical-align:middle;">Secciones a visualizar</th>
                  <th style="vertical-align:middle;">Departamento</th>
                  <th style="vertical-align:middle;">Acciones</th>
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
