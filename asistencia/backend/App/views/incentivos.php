<?php echo $header; ?>
<div class="container body">
  <div class="main_container">

    <!-- page content -->
    <div class="right_col" role="main">
      <div class="">

        <div class="row">
          <div class="col-md-12 col-sm-12 col-xs-12">
            <?php echo $infoPeriodo; ?> 
          </div>
        </div>

        <div class="row">
          <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <h1><?php echo $tituloColaboradores; ?></h1>
                <div class="clearfix"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <h2>El periodo es <b><?php echo $msjPeriodo; ?> </b></h2>
                <div class="clearfix"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="row" style="<?php echo $hidden; ?>">
          <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <select class="form-control" name="status" id="status">
                  <option value=""> <?php echo $periodos; ?> </option>
                </select>
                <div class="clearfix"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <h1>Colaboradores <?php echo $tituloIncentivos; ?></h1>
              </div>

              <div class="x_content">
                <form name="all" id="all" action="/Incentivo/" method="POST">
                  <div class="panel-body">
                    <div class="dataTable_wrapper">
                      <table class="table table-striped table-bordered table-hover" id="muestra-colaboradores">
                        <thead>
                          <tr>
                            <th style="text-align:center; vertical-align:middle;">Colaborador</th>
                            <th style="text-align:center; vertical-align:middle;">Nombre</th>
                            <th style="text-align:center; vertical-align:middle;">Departamento</th>
                            <th style="text-align:center; vertical-align:middle;">Incentivos</th>
                            <th style="text-align:center; vertical-align:middle;">Acciones</th>
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
        </div>


      </div>
    </div>
    <!-- /page content -->

  </div>
</div>


<?php echo $footer; ?>