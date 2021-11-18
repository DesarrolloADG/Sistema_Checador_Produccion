<?php echo $header;?>

<div class="right_col">
  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    <div class="x_panel tile fixed_height_240">

      <div class="row x_title">
        <div class="col-md-12">
          <h1>
            Colaborador 
            <small> 
              <?php echo $colaborador['nombre'] . " " . $colaborador['apellido_paterno'] . " " . $colaborador['apellido_materno']; ?> 
              <b>Periodo</b> <?php echo $colaborador['pago']; ?>
            </small>
          </h1>
        </div>
      </div>
      <div class="alert alert-success" id="alerta">
        <strong>Atensión</strong> El periodo seleccionado esta abierto.
      </div>

      <div class="x_content">
        <form class="form-horizontal" id="edit" method="POST" action="/AdminIncidencia/editFechas">
          <div class="form-group ">

            <div class="form-group">
              <label class="control-label col-md-2 col-sm-2 col-xs-12" for="nombre" >
                <br><i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                 Fechas
              </label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <!--input type="text" id="config-demo" name="rango" value="<?php echo $rango ?>" class="form-control col-md-5 col-xs-12"-->
                <br><select class="form-control" name="periodo" id="periodo">
                  <option value="">Periodo a buscar</option>
                  <?php echo $selectPeriodoFechas ?>
                </select>
              </div>
              <br>
              <span id="availability"></span>

              <div class="col-md-4 col-sm-4 col-xs-12">
                
                <button class="btn btn-success col-md-5 col-sm-5 col-xs-5" id="btnAdd" type="button">Buscar</button>
                <a  href="/AdminIncidencia/" class="btn btn-danger col-md-5 col-sm-5 col-xs-5">Regresar</a>
              </div>
            </div>

            <input type="hidden" name="catalogo_colaboradores_id" id="catalogo_colaboradores_id" value="<?php echo $colaborador['catalogo_colaboradores_id']; ?>">
            <input type="hidden" name="numero_empleado" id="numero_empleado" value="<?php echo $colaborador['numero_empleado']; ?>">

            <div class="panel-body">
              <br><br>
              <div class="dataTable_wrapper" id="contendor_tabla">
                <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
                  <thead>
                    <tr>
                      <th>Dia</th>
                      <th>Fecha</th>
                      <th>Entrada</th>
                      <th>Salida</th>
                      <th>Entrada Registrada</th>
                      <th>Salida Registrada</th>
                      <th>Comentario</th>
                      <th>Incidencia</th>
                      <th>Acción</th>
                    </tr>
                  </thead>
                <tbody id="registros">
                  <?php echo $tabla; ?>
                </tbody>
                </table>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php echo $footer;?>
