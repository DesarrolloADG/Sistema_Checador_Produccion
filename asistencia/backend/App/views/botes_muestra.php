<?php echo $header; ?>
<div class="right_col">
  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    <div class="panel panel-default">
      <div class="x_title">
        <br><br>
        <h3> Meta de botes </h3>
        <div class="clearfix"></div>
      </div>
      <form name="all" id="all" action="/incentivo/botes/" method="POST">
        <div class="panel-body">
          <div class="dataTable_wrapper">
            <div class="panel-body">
              <a href="/Incentivo/botesAdd/" class="btn btn-info"><span class="glyphicon glyphicon-plus"> </span></a>
              <button id="delete" type="button" class="btn btn-danger btn-circle"><i class="fa fa-remove"> </i></button><br>
            </div>
            <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
              <thead>
                <tr>
                  <th><input type="checkbox" name="checkAll" id="checkAll" value=""/></th>
                  <th>Periodo</th>
                  <th>Clara</th>
                  <th>Yema</th>
                  <th>Huevo liquido</th>
                  <th>Accion</th>
                </tr>
              </thead>
              <tbody>
                <?= $tabla; ?>
              </tbody>
            </table>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<?php echo $footer; ?>
