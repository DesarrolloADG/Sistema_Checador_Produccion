<?php
echo $header; ?>
<div class="right_col">
  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    <div class="panel panel-default">
      <div class="x_title">
        <br><br>
        <h1> Catálogo de Gestión de Departamentos</h1>
        <div class="clearfix"></div>
      </div>
      <form name="delete-form" name="all" id="all" action="/Departamento/delete" method="POST">
        <div class="panel-body" <?php echo $visible; ?>>
          <a href="/Departamento/add" type="button" class="btn btn-primary btn-circle" <?= $agregarHidden?>><i class="fa fa-plus"> <b>Nuevo Departamento</b></i></a>
          <button id="delete" type="button" class="btn btn-danger btn-circle" <?= $eliminarHidden?>><i class="fa fa-remove"> <b>Eliminar</b></i></button>
          <button id="export_pdf" type="button" class="btn btn-info btn-circle" <?= $pdfHidden ?>><i class="fa fa-file-pdf-o"> <b>Exportar a PDF</b></i></button>
          <button id="export_excel" type="button" class="btn btn-success btn-circle" <?= $excelHidden?>><i class="fa fa-file-excel-o"> <b>Exportar a Excel</b></i></button>
		  <button id="export_excel_manolo" type="button" class="btn btn-warning btn-circle" ><i class="fa fa-file-excel-o"> <b><?php echo $btn;?></b></i></button>
        </div>
		
		<div class="panel-body" <?php echo $visible; ?>>
          
		  <table class="table" id="muestra-manolo">
              <thead>
                <tr>
                  <th>catalogo_departamento_id</th>
                  <th>nombre</th>
                  <th>status</th>
                  
                </tr>
              </thead>
              <tbody>
                <?php echo $tablaJugadores; ?>
              </tbody>
            </table>
		  
        </div>
		
		
      </form>
    </div>
  </div>
</div>
<?php echo $footer; ?>
