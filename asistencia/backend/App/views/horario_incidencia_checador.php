<?php echo $header; ?>
<div class="right_col">
  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    <div class="panel panel-default">
      <div class="x_title">
        <br><br>
        <h1> Horario del Colaborador <b><?php echo $datosColaborador['nombre'] . " " . $datosColaborador['apellido_paterno'] . " " . $datosColaborador['apellido_materno']; ?> </b></h1>
        
      </div>
      <form name="all" id="all" action="/HorarioIncidencia/busqueda" method="POST">

        <div class="row">
          <div class="control-label col-md-3 col-sm-3 col-xs-12">
            <fieldset>
              <label class="control-label col-md-12 col-sm-12 col-xs-12" for="descripcion">Fecha Incial </label>
                <div class="control-group">
                  <div class="controls">
                    <div class="col-md-12 col-sm-12 col-xs-12 xdisplay_inputx form-group has-feedback">
                      <input type="text" id="single_cal2" name="fecha_inicial" class="form-control has-feedback-left" placeholder="Fecha" aria-describedby="inputSuccess2Status2" value="<?php echo $fechaIni; ?>">
                      <span class="fa fa-calendar-o form-control-feedback left" aria-hidden="true"></span>
                      <span id="inputSuccess2Status2" class="sr-only">(success)</span>
                    </div>
                  </div>
                </div>
            </fieldset>
          </div>
          
          <div class="control-label col-md-3 col-sm-3 col-xs-12">
            <fieldset>
              <label class="control-label col-md-12 col-sm-12 col-xs-12" for="descripcion">Fecha Final </label>
                <div class="control-group">
                  <div class="controls">
                    <div class="col-md-12 col-sm-12 col-xs-12 xdisplay_inputx form-group has-feedback">
                      
                      <input type="text" id="single_cal3" name="fecha_final" class="form-control has-feedback-left" placeholder="Fecha" aria-describedby="inputSuccess2Status2" value="<?php echo $fechaFin; ?>">
                      <span class="fa fa-calendar-o form-control-feedback left" aria-hidden="true"></span>
                      <span id="inputSuccess2Status2" class="sr-only">(success)</span>
                    </div>
                  </div>
                </div>
            </fieldset>
          </div>
          <input type="hidden" name="numero_empleado" value="<?php echo $datosColaborador['numero_empleado']; ?>">
          <input type="hidden" name="id_colaborador" value="<?php echo $id; ?>">
          <div class="control-label col-md-2 col-sm-2 col-xs-12">
            <br><button class="btn btn-success col-md-12 col-sm-12 col-xs-5" id="btnAdd" type="submit">Agregar</button>
          </div>
        </div>
      
        <div class="panel-body">
          <div class="dataTable_wrapper">
            <?php echo $tabla; ?>
          </div>
        </div>
      </form>

    </div>
  </div>
</div>
<?php echo $footer; ?>
