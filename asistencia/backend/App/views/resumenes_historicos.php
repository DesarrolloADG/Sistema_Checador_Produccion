
<?php echo $header; ?>
<div class="container body">
  <div class="main_container">

    <!-- page content -->
    <div class="right_col" role="main">
      <div class="">
        <div class="page-title">
          <div class="title_left">
            <h1>Historio <b><?php echo $tipoPeriodo; ?></b></h1>
          </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
          <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <h2>Periodo resumen <?php echo $tipoPeriodo; ?>  <?php echo $msjPeriodo; ?></h2>
                <div class="clearfix"></div>
                <div class="panel-body">
                  <div class="row">
                    <form method="POST" action="<?php echo $busqueda; ?>">
                      <div class="form-group">
                        <div class="col-md-10 col-sm-10 col-xs-12 ">
                          <select name="tipo_periodo" class="form-control">
                             <?php echo $option; ?>
                          </select>
                        </div>
                        <div class="col-md-2 col-sm-2 col-xs-4 ">
                          <button class="btn btn-info col-md-12 col-sm-12 col-xs-12" type="submit" id="buscar_periodo">Buscar</button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          
          <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <h1>Colaboradores </h1>
              </div>
              <div class="x_content">
                
                <form name="all" id="all" action="/Incentivo/" method="POST">

                  <div class="panel-body">
                    <div class="dataTable_wrapper table table-responsive">
                      <table class="table table-striped table-bordered table-hover" id="muestra-colaboradores">
                        <thead>
                          <tr>
                            <?php echo $thead; ?>
                          </tr>
                        </thead>
                        <tbody>
                          <?php echo $tbody; ?>
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

  </div>
</div>


<?php echo $footer; ?>