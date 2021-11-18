<?php echo $header; ?>
<div class="right_col">
  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    <div class="panel panel-default">
      <div class="x_title">
        <br><br>
        <h1> Prorrateo de Colaboradores</h1>
        <div class="clearfix"></div>
      </div>
      <form name="all" id="all" action="/Prorrateo/delete" method="POST">
        <div class="panel-body" <?php echo $visible; ?>>
        <div class="alert alert-success" id="alerta">
          <strong>Atensión</strong> El periodo seleccionado esta abierto.
        </div>
          <div class="control-label col-md-8 col-sm-8 col-xs-8">
              <label class="control-label col-md-3 col-sm-3 col-xs-3" for="descripcion">Periodo :<span class="required">*</span></label>
              <div class="control-group">
                  <div class="col-md-9 col-sm-9 col-xs-9 xdisplay_inputx form-group has-feedback">
                    <select class="form-control" name="periodo_id" id="periodo_id" style="text-align:center;">
                      <?php echo $sPeriodo; ?>
                    </select>
                  </div>
              </div>
          </div>

          <div class="control-label col-md-3 col-sm-3 col-xs-3">
            <button id="btnCalcular" type="button" class="btn btn-info btn-circle"><i class="fa fa-pencil"> <b> Calcular </b></i></button>
          </div>

          <div class="control-label col-md-12 col-sm-12 col-xs-12" id="buttons">
            
          </div>
        
        <div class="form-group col-md-12 col-sm-12 col-xs-12" hidden>
          <button id="export_pdf" type="button" class="btn btn-info btn-circle" <?= $pdfHidden ?>><i class="fa fa-file-pdf-o"> <b>Exportar a PDF</b></i></button>
          <button id="export_excel" type="button" class="btn btn-success btn-circle" <?= $excelHidden?>><i class="fa fa-file-excel-o"> <b>Exportar a Excel</b></i></button>
        </div>

        </div>
        <div class="panel-body col-md-12 col-sm-12 col-xs-12 col-lg-12">
          <div class="dataTable_wrapper  table table-responsive"  id="contenedor_tabla">
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<?php echo $footer; ?>
