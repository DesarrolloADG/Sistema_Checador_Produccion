<?php echo $header; ?>
<div class="container body">
  <div class="main_container">

    <!-- page content -->
    <div class="right_col" role="main">
      <div class="">
        <div class="page-title">
          <div class="title_left">
            <h1>Prorrateo <b><?php echo $tipoPeriodo; ?></b></h1>
          </div>
        </div>

        <div class="clearfix"></div>

        <div>
          <?php echo $msjPeriodoCerrado; ?>
        </div>

        <div class="row">
          <div class="<col>-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <h2>Periodo <?php echo $msjPeriodo; ?></h2>
                <div class="clearfix"></div>
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

                <div> <?php echo $statusNoi; ?></div>

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