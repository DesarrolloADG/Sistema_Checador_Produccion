<?php echo $header;?>
<div class="right_col">
  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    <div class="x_panel tile fixed_height_240">
      <div class="x_title">
        <br><br>
        <h1> Datos de la Lector <small> con id <?php echo $lector['catalogo_lector_id']; ?> </small></h1>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        
          <div class="form-group ">

            <div class="dashboard-widget-content">
              <ul class="list-unstyled timeline widget">
              

                <li>
                  <div class="block">
                    <div class="block_content">
                      <h2 class="title">
                        <a>Ubicación</a>
                      </h2>
                      <div class="byline"></div>
                      <p class="excerpt"><?php echo $lector['nombre']; ?></p>
                    </div>
                  </div>
                </li>

                <li>
                  <div class="block">
                    <div class="block_content">
                      <h2 class="title">
                        <a>Tipo de comunicación</a>
                      </h2>
                      <div class="byline"></div>
                      <p class="excerpt"><?php echo $lector['tipo_comunicacion']; ?></p>
                    </div>
                  </div>
                </li>

                <li>
                  <div class="block">
                    <div class="block_content">
                      <h2 class="title">
                        <a>Ip de comunicación</a>
                      </h2>
                      <div class="byline"></div>
                      <p class="excerpt"><?php echo $lector['ip_lector']; ?></p>
                    </div>
                  </div>
                </li>

                <li>
                  <div class="block">
                    <div class="block_content">
                      <h2 class="title">
                        <a>Puerto</a>
                      </h2>
                      <div class="byline"></div>
                      <p class="excerpt"><?php echo $lector['puerto']; ?></p>
                    </div>
                  </div>
                </li>

                <li>
                  <div class="block">
                    <div class="block_content">
                      <h2 class="title">
                        <a>Descripción</a>
                      </h2>
                      <div class="byline"></div>
                      <p class="excerpt"><?php echo $lector['descripcion']; ?></p>
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
                      <p class="excerpt"><?php echo $lector['nombre_status']; ?></p>
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
      </div>
    </div>
  </div>
</div>

<?php echo $footer;?>
