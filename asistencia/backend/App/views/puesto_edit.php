<?php echo $header;?>
<div class="right_col">
  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    <div class="x_panel tile fixed_height_240">
      <div class="x_title">
        <br><br>
        <h1> Editar Puesto</h1>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <form class="form-horizontal" id="edit" action="/Puesto/puestoEdit" method="POST">
          <div class="form-group ">

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="nombre">Nombre <span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" class="form-control col-md-7 col-xs-12" placeholder="Ingresa el nombre del puesto" name="nombre" id="nombre" value="<?php echo $puesto['nombre']; ?>">
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="descripcion">Descripci&oacute;n <span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <textarea class="form-control" name="descripcion" id="descripcion" placeholder="Descripci&oacute;n del puesto"><?php echo $puesto['descripcion']; ?></textarea>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="status">Status<span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <select class="form-control" name="status" id="status">
                  <option value="" disabled selected>Selecciona un estatus</option>
                  <?php echo $sStatus; ?>
                </select>
              </div>
            </div>
            <div class="x_title">
              <br><br>
              <h2>Selecciona Incentivo</h2>
              <div class="clearfix"></div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="numeroi">Número de incentivos <span class="required">*</span></label>
              <div class="col-md-2 col-sm-6 col-xs-12">
                <input type="number" step="1" min="1" max="10" class="form-control col-md-7 col-xs-12" placeholder="Ingresa el numero de incentivos del puesto" name="numeroi" id="numeroi" value="<?php echo $puesto['numero_incentivos'];?>" required>
              </div>
              <!-- <span id="availability"></span> -->
              <label class="control-label col-md-2 col-sm-3 col-xs-12" for="numeroi">Total Efectivo <span class="required">*</span></label>
              <div class="col-md-2 col-sm-6 col-xs-12">
                <input type="number" class="form-control col-md-7 col-xs-12" placeholder="Dinero Total" name="ti" id="ti" value="<?php echo $puesto['total_valor_incentivos'];?>" required>
              </div>
              <!-- <span id="availability"></span> -->
            </div>

            <span id="incentivo" name="incentivo">
              <span class="rem"></span>
            </span>

            <input type="hidden" name="catalogo_puesto_id" id="catalogo_puesto_id" value="<?php echo $puesto['catalogo_puesto_id']; ?>">

            <div class="form-group">
            <br>
              <div class="col-md-12 col-sm-12 col-xs-12">
                <button class="btn btn-danger col-md-3 col-sm-3 col-xs-3 col-md-offset-3" type="button" id="btnCancel">Cancelar</button>
                <button class="btn btn-success col-md-3 col-sm-3 col-xs-3" type="submit" id="btnAdd">Actualizar</button>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php echo $footer;?>
