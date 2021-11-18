<?php echo $header; ?>
<div class="right_col">
  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    <div class="panel panel-default">
      <div class="x_title">
        <br><br>
        <h1> Prorrateo</h1>
        <div class="clearfix"></div>
      </div>
      <form name="all" id="all" action="/Empresa/delete" method="POST">
        <div class="panel-body" <?php echo $visible; ?>>
          <!--a href="/Empresa/add" type="button" class="btn btn-primary btn-circle" <?= $agregarHidden?>><i class="fa fa-plus"> <b>Nueva Empresa</b></i></a>
          <button id="delete" type="button" class="btn btn-danger btn-circle" <?= $eliminarHidden?>><i class="fa fa-remove"> <b>Eliminar</b></i></button>
          <button id="export_pdf" type="button" class="btn btn-info btn-circle" <?= $pdfHidden ?>><i class="fa fa-file-pdf-o"> <b>Exportar a PDF</b></i></button>
          <button id="export_excel" type="button" class="btn btn-success btn-circle" <?= $excelHidden?>><i class="fa fa-file-excel-o"> <b>Exportar a Excel</b></i></button-->
        </div>
        <div class="panel-body">
          <div class="dataTable_wrapper">
            <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
              <thead>
                <tr>
                  <th class="cell"></th> <!-- 1 -->
                  <th class="cell"></th> <!-- 2 -->
                  <th class="cell" style="border-right: 1px solid white;">SM 2017</th> <!-- 3 -->
                  <th class="cell" style="border-left: 1px solid white;">73.03</th> <!-- 4 -->
                  <th class="cell">EN 2015</th>
                  <th class="cell" colspan="4" style="text-align: center;"> PERCEPCIONES GRAVADAS</th> <!--  - 16 --> <!-- PERCEPCIONES NO GRAVADAS -->
                  <th class="cell"></th> <!-- 10 -->
                  <th class="cell" colspan="6" style="text-align: center;"> PERCEPCIONES NO GRAVADAS</th> <!-- 11 - 16 --> <!-- PERCEPCIONES NO GRAVADAS -->
                </tr>
                <tr>
                  <th class="cell"></th> <!-- 1 --> <!-- CLAVE -->
                  <th class="cell">PRODUCCION</th> <!-- 2 --> <!-- NOMBRE -->
                  <th class="cell">SALARIO</th> <!-- 3 --> <!-- DIARIO -->
                  <th class="cell">S.D.I</th> <!-- 4 --> <!--  -->
                  <th class="cell">CANTIDAD DE</th>
                  <th class="cell"></th> <!-- 6 --> <!-- PERCEPCIONES GRAVADAS -->
                  <th class="cell">HORAS</th> <!-- 7 --> <!-- PERCEPCIONES GRAVADAS -->
                  <th class="cell"></th> <!-- 8 --> <!-- PERCEPCIONES GRAVADAS -->
                  <th class="cell"></th> <!-- 9 --> <!-- PERCEPCIONES GRAVADAS -->
                  <th class="cell">LIMITE HORAS</th> <!-- 10 -->
                  <th class="cell bckgrnd-yellow">PREMIO DE </th> <!-- 11 --> <!-- PERCEPCIONES NO GRAVADAS -->
                  <th class="cell bckgrnd-yellow">PREMIO DE </th> <!-- 12 --> <!-- PERCEPCIONES NO GRAVADAS -->
                  <th class="cell bckgrnd-yellow">NUMERO DE</th> <!-- 13 --> <!-- PERCEPCIONES NO GRAVADAS -->
                  <th class="cell">IMPORTE</th> <!-- 14 --> <!-- PERCEPCIONES NO GRAVADAS -->
                  <th class="cell bckgrnd-yellow" colspan="2"> DESPENSA</th> <!-- 15 - 16 --> <!-- PERCEPCIONES NO GRAVADAS -->
                </tr>
                <tr>
                  <th class="cell">CLAVE</th> <!-- 1 -->
                  <th class="cell">NOMBRE</th> <!-- 2 -->
                  <th class="cell">DIARIO</th> <!-- 3 -->
                  <th class="cell"></th> <!-- 4 -->
                  <th class="cell">H. EXTRA</th> <!-- 5 -->
                  <th class="cell">INCENTIVOS</th> <!-- 6 --> <!-- PERCEPCIONES GRAVADAS -->
                  <th class="cell">EXTRA</th> <!-- 7 --> <!-- PERCEPCIONES GRAVADAS -->
                  <th class="cell">OTRAS</th> <!-- 8 --> <!-- PERCEPCIONES GRAVADAS -->
                  <th class="cell">TOTAL</th> <!-- 9 --> <!-- PERCEPCIONES GRAVADAS -->
                  <th class="cell">EXTRA</th> <!-- 10 -->
                  <th class="cell bckgrnd-yellow">ASISTENCIA</th> <!-- 11 --> <!-- PERCEPCIONES NO GRAVADAS -->
                  <th class="cell bckgrnd-yellow">PUNTUALIDAD</th> <!-- 12 --> <!-- PERCEPCIONES NO GRAVADAS -->
                  <th class="cell bckgrnd-yellow">H. EXTRA</th> <!-- 13 --> <!-- PERCEPCIONES NO GRAVADAS -->
                  <th class="cell">H. EXTRA</th> <!-- 14 --> <!-- PERCEPCIONES NO GRAVADAS -->
                  <th class="cell bckgrnd-yellow">EN EFECTIVO</th> <!-- 15 --> <!-- PERCEPCIONES NO GRAVADAS -->
                  <th class="cell bckgrnd-yellow">INCENTIVO</th> <!-- 16 --> <!-- PERCEPCIONES NO GRAVADAS -->
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
