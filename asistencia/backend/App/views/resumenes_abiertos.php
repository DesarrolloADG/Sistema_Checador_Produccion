<?php echo $header; ?>
<div class="container body">
  <div class="main_container">

    <!-- page content -->
    <div class="right_col" role="main">
      <div class="">
        <div class="page-title">
          <div class="title_left">
            <h1>Resumen <b><?php echo $tipo_periodo; ?></b></h1>
          </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
          <div class="col-md-12 col-sm-12 col-xs-12">
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
                <div id="contenedorRelog" class="col-md-12 col-sm-12 col-xs-12">
                  <h2 class="col-md-3 col-sm-3 col-xs-3">Guardando Resumen ...</h2><br>
                  <div class="col-md-12 col-sm-12 col-xs-12" id="retroclockbox1"></div>
                </div>

              <div class="x_content">

                <!-- CONTENEDOR DE BUSQUEDA-->
                <form name="all" id="all" action="/ResumenQuincenal/generarExcel" method="POST" target="_blank" >
                  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">

                    <div class="form-group col-md-6 col-sm-6 col-xs-6 col-lg-6">
                      <label class="control-label col-md-3 col-sm-3 col-xs-12" for="id_catalogo_empresa">Empresa<span class="required">*</span></label>
                      <div class="col-md-9 col-sm-9 col-xs-12">
                        <select class="form-control" name="catalogo_empresa_id" id="catalogo_empresa_id">
                          <option value="" >Selecciona una Empresa</option>
                          <?php echo $idEmpresa; ?>
                        </select>
                      </div>
                    </div>

                    <div class="form-group col-md-6 col-sm-6 col-xs-6 col-lg-6">
                      <label class="control-label col-md-3 col-sm-3 col-xs-12" for="id_catalogo_ubicacion">Ubicación<span class="required">*</span></label>
                      <div class="col-md-9 col-sm-9 col-xs-12">
                        <select class="form-control" name="catalogo_ubicacion_id" id="catalogo_ubicacion_id">
                          <option value="" >Selecciona una Ubicación</option>
                          <?php echo $idUbicacion; ?>
                        </select>
                      </div>
                    </div>

                    <div class="form-group col-md-6 col-sm-6 col-xs-6 col-lg-6">
                      <label class="control-label col-md-3 col-sm-3 col-xs-12" for="id_catalogo_departamento">Departamento<span class="required">*</span></label>
                      <div class="col-md-9 col-sm-9 col-xs-12">
                        <select class="form-control" name="catalogo_departamento_id" id="catalogo_departamento_id">
                          <option value="" >Selecciona un Departamento</option>
                          <?php echo $idDepartamento; ?>
                        </select>
                      </div>
                    </div>

                    <div class="form-group col-md-6 col-sm-6 col-xs-6 col-lg-6">
                      <label class="control-label col-md-3 col-sm-3 col-xs-12" for="id_catalogo_puesto">Puesto<span class="required">*</span></label>
                      <div class="col-md-9 col-sm-9 col-xs-12">
                        <select class="form-control" name="catalogo_puesto_id" id="catalogo_puesto_id">
                          <option value="" >Selecciona un Puesto</option>
                          <?php echo $idPuesto; ?>
                        </select>
                      </div>
                    </div>

                    <div class="form-group col-md-6 col-sm-6 col-xs-6 col-lg-6">
                      <label class="control-label col-md-3 col-sm-3 col-xs-12" for="status">Nomina<span class="required">*</span></label>
                      <div class="col-md-9 col-sm-9 col-xs-12">
                        <select class="form-control" name="status" id="status">
                        <option value="">Selecciona una nomina</option>
                        <?php echo $nomina; ?>
                        </select>
                      </div>
                    </div>

                    <div class="form-group col-md-12 col-sm-12 col-xs-12 col-lg-12">
                        <button type="button" class="btn btn-info col-md-12 col-sm-12 col-xs-12 col-lg-12" value="Buscar" id="btnBuscar">
                          <span class="glyphicon glyphicon-search">Buscar</span>
                        </button>
                    </div>

                  </div>
                  <!-- CONTENEDOR DE BUSQUEDA-->
                  <div class="form-group" >

                    <input type="hidden" id="periodo_id" name="periodo_id" value="<?= $periodo_id?>" />
                    <input type="hidden" id="tipo_periodo" name="tipo_periodo" value="<?= $tipo_periodo?>" />
                    <input type="hidden" id="mensaje" name="mensaje" value="<?= $mensaje?>" />

                    <div class="col-md-6 col-sm-6 col-xs-6 form-group" align="center" style="<?php echo $displayBtn; ?>" <?= $visible_admin?>>
                      <div class="col-md-3 col-sm-3 col-xs-3" hidden>
                        <button class="btn btn-primary" id="btnAplicar" type="button">Buscar</button>
                      </div>
                      <div class="col-md-3 col-sm-3 col-xs-3">
                        <button class="btn btn-primary" id="btnGuardar" type="button" >Cerrar</button>
                      </div>
                      <div class="col-md-3 col-sm-3 col-xs-3" hidden>
                        <button class="btn btn-danger" id="btnCancelarPeriodo" type="button" >Cancelar</button>
                      </div>
                      <div class="col-md-3 col-sm-3 col-xs-3">
                        <button class="btn btn-warning" id="btnRespaldarPeriodo" type="button" >Respaldar</button>
                      </div>
                      <div class="col-md-3 col-sm-3 col-xs-3">
                        <button class="btn btn-primary" id="btnRestaurarPeriodo" type="button" >Restaurar</button>
                      </div>
                    </div>

                    <div class="panel-body col-md-12 col-sm-12 col-xs-12 col-lg-12">
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

                  </div>

                </form>
              </div>
            </div>
          </div>
        </div>

        

        <div id="respuesta"></div>


      </div>
    </div>
    <!-- /page content -->

  </div>
</div>


<?php echo $footer; ?>