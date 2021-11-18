<?php echo $header;?>
<div class="right_col">
  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    <div class="x_panel tile fixed_height_240">
      <div class="x_title">
        <br><br>
        <h1> Datos de Horario <small> con id <?php echo $horario['catalogo_horario_id']; ?> </small></h1>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <form class="form-horizontal" id="edit" action="/Empresa/empresaEdit" method="POST">
          <div class="form-group ">

            <div class="dashboard-widget-content">
              <ul class="list-unstyled timeline widget">
                <li>
                  <div class="block">
                    <div class="block_content">
                      <h2 class="title">
                        <a>Nombre</a>
                      </h2>
                      <div class="byline"></div>
                      <p class="excerpt"><?php echo $horario['nombre']; ?></p>
                    </div>
                  </div>
                </li>

                <li>
                  <div class="block">
                    <div class="block_content">
                      <h2 class="title">
                        <a>Hora de entrada</a>
                      </h2>
                      <div class="byline"></div>
                      <p class="excerpt"><?php echo $horario['hora_entrada']; ?> hrs.</p>
                    </div>
                  </div>
                </li>

                <li>
                  <div class="block">
                    <div class="block_content">
                      <h2 class="title">
                        <a>Hora de salida</a>
                      </h2>
                      <div class="byline"></div>
                      <p class="excerpt"><?php echo $horario['hora_salida']; ?> hrs.</p>
                    </div>
                  </div>
                </li>

                <li>
                  <div class="block">
                    <div class="block_content">
                      <h2 class="title">
                        <a>Tolerancia entrada</a>
                      </h2>
                      <div class="byline"></div>
                      <p class="excerpt"><?php echo $horario['tolerancia_entrada']; ?> min.</p>
                    </div>
                  </div>
                </li>

                <li>
                  <div class="block">
                    <div class="block_content">
                      <h2 class="title">
                        <a>Días laborales</a>
                      </h2>
                      <div class="byline"></div>
                      <p class="excerpt"><?php echo $diasLaborales; ?></p>
                    </div>
                  </div>
                </li>

                <li>
                  <div class="block">
                    <div class="block_content">
                      <h2 class="title">
                        <a>Número de retardos hacen 1 falta</a>
                      </h2>
                      <div class="byline"></div>
                      <p class="excerpt"><?php echo $horario['numero_retardos']; ?></p>
                    </div>
                  </div>
                </li>

                <li>
                  <div class="block">
                    <div class="block_content">
                      <h2 class="title">
                        <a>Estatus</a>
                      </h2>
                      <div class="byline"></div>
                      <p class="excerpt"><?php echo $horario['nombre_status']; ?></p>
                    </div>
                  </div>
                </li>
                      
              </ul>

              <div class="form-group">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <a class="btn btn-success col-md-2 col-sm-2 col-xs-2" type="submit" id="btnCancel">
                  <span class="glyphicon glyphicon-chevron-left pull-left"></span> Regresar
                </a>
              </div>
            </div>
            </div>


          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php echo $footer;?>
