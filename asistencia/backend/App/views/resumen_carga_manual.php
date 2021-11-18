<?php echo $header;

$anioo = date("Y");
$anioAnt = (int) $anioo - 1;
 
$optionAnio = ''; 
for($i = $anioAnt; $i<= $anioo + 1; $i++){
    $optionAnio .=<<<html
<option value="$i">$i</option>
html;
}

$optionMes = '';
for($j=1; $j<13;$j++){
    $mes = $j;
    if($mes < 10)
	$mes = "0$j";

    $optionMes.=<<<html
<option value="$mes">$mes</option>
html;

}

?>
<div class="right_col">
  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    <div class="x_panel tile fixed_height_240">
      <div class="x_title">
        <br><br>
        <h1>Carga Manual</h1>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <form class="form-horizontal" id="add" action="/ResumenCargarManual/carga" method="POST">
          <div class="form-group ">

            <div class="form-group">
							<fieldset>
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="descripcion">M&eacute;s <span class="required">*</span></label>
								<div class="control-group">
									<div class="controls">
										<div class="col-md-6 col-sm-6 col-xs-12 xdisplay_inputx form-group has-feedback">
						
											<select class="form-control" name="mes" id="mes">
												<?php echo $optionMes; ?>
                									</select>		

										</div>
									</div>
								</div>
							</fieldset>
						</div>

            <div class="form-group">
							<fieldset>
								<label class="control-label col-md-3 col-sm-3 col-xs-12" for="descripcion">A&ntilde;o<span class="required">*</span></label>
								<div class="control-group">
									<div class="controls">
										<div class="col-md-6 col-sm-6 col-xs-12 xdisplay_inputx form-group has-feedback">

										<select class="form-control" name="anio" id="anio">
											<?php echo $optionAnio; ?>
                								</select>
											

										</div>
									</div>
								</div>
							</fieldset>
						</div>

            <div class="form-group">
              <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-2 col-xs-offset-3">
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
