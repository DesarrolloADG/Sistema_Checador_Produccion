<?php echo $header;?>
<div class="right_col">
  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    <div class="x_panel tile fixed_height_240">
      <div class="x_title">
        <br><br>
        <h1>Template NOI Batch</h1>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <form class="form-horizontal" id="add" action="/TemplateNoi/add" enctype="multipart/form-data"  method="POST">
          <div class="form-group ">

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="status">Selecciona la nomina<span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <select class="form-control" name="nomina" id="status">
                  <option value="XOCHIMILCO">Xochimilco</option>
		  <option value="UNIDESH">Unidesh</option>
		  <option value="VALLEJO">Vallejo</option>
		  <option value="GATSA">Gatsa</option>
                </select>
              </div>
            </div>

	    <div class="form-group">
    		<label class="control-label col-md-3 col-sm-3 col-xs-12" for="exampleInputFile">Incentivos</label>
		<div class="col-md-6 col-sm-6 col-xs-12">
    		    <input type="file" name="incentivos" class="form-control-file" id="exampleInputFile" aria-describedby="fileHelp">
    		    <small id="fileHelp" class="form-text text-muted"><a href="/template/TemplateIncentivos.xlsx">Se debe de cargar un archivo en formato xlsx, se puede descargar una platilla base desde aqu&iacute;.</a>
.</small>
		</div>
  	    </div>

	    <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="exampleInputFile">Faltas</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input type="file" name="faltas" class="form-control-file" id="exampleInputFile" aria-describedby="fileHelp">
                    <small id="fileHelp" class="form-text text-muted"><a href="/template/TemplateFaltas.xlsx">Se debe de cargar un archivo en formato xlsx, se puede descargar una platilla base desde aqu&iacute;.</a>
.</small>
                </div>
            </div>

            <div class="form-group">
              <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-2 col-xs-offset-3">
                <button class="btn btn-danger col-md-3 col-sm-3 col-xs-5" type="button" id="btnCancel">Cancelar</button>
                <button class="btn btn-success col-md-3 col-sm-3 col-xs-5" id="btnAdd" type="submit">Cargar</button>
              </div>
            </div>
            <div id="resultado">

            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php echo $footer;?>
