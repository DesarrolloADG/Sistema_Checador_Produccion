<?php echo $header;?>
<div class="right_col">
  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    <div class="x_panel tile fixed_height_240">
      <div class="x_title">
        <br><br>
        <h1>Agregar un nuevo Administrador</h1>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <form class="form-horizontal" id="add" action="/Administradores/administradorAdd" method="POST">
          <div class="form-group ">

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="nombre">Nombre <span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" name="nombre" id="nombre" class="form-control col-md-7 col-xs-12" placeholder="Nombre del Administrador ">
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="usuario">Usuario <span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" name="usuario" id="usuario" class="form-control col-md-7 col-xs-12" placeholder="¿Cuál será el usuario del Administrador?">
              </div>
              <span id="availability"></span>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="contrasena_1">Contraseña <span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="password" name="contrasena_1" id="contrasena_1" class="form-control col-md-7 col-xs-12" placeholder="Ingresa la contraseña">
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="contrasena_2">Confirma contraseña <span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="password" name="contrasena_2" id="contrasena_2" class="form-control col-md-7 col-xs-12" placeholder="Confirma la contraseña">
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="status">Planta<span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <select class="form-control" name="planta" id="planta" >
                  <option value="" disabled selected>Selecciona la planta</option>
                  <?php echo $plantas; ?>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="perfil_id">Perfil del Administrador<span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <select class="form-control" name="perfil_id" onchange="showDiv(this)">
                  <option value="" disabled selected>Selecciona un perfil para el este administrador</option>
                  <?php echo $perfiles; ?>
                </select>
              </div>
            </div>

            <div class="form-group" id="permiosos-root" style="display: none;">
              <label class="control-label col-md-3 col-sm-3 col-xs-12">Permisos Root</label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <label class="col-md-12 col-sm-12 col-xs-12 form-control">Este usuario es root del sistema</label>
                <input type="hidden" name="admin" id="admin" value="1" class="form-control col-md-7 col-xs-12" >
              </div>
            </div>

            <div class="form-group" id="permiosos-administrador" style="display: none;">
              <label class="control-label col-md-3 col-sm-3 col-xs-12">Permisos</label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <label class="col-md-12 col-sm-12 col-xs-12 form-control">Este usuario tiene todos los privilegios: ver, crear, editar y eliminar </label>
                <input type="hidden" name="admin" id="admin" value="1" class="form-control col-md-7 col-xs-12" >
              </div>
            </div>

            <div class="form-group" id="permiosos-recursos-humanos" style="display: none;">
              <label class="control-label col-md-3 col-sm-3 col-xs-12">Permisos</label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <label class="col-md-12 col-sm-12 col-xs-12 form-control">Este usuario tiene todos los privilegios </label>
                <input type="hidden" name="admin" id="admin" value="2" class="form-control col-md-7 col-xs-12" >
              </div>
            </div>

            <div class="form-group" id="permiosos-prorrateo" style="display: none;">
              <label class="control-label col-md-3 col-sm-3 col-xs-12">Permisos Prorrateo</label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <label class="col-md-12 col-sm-12 col-xs-12 form-control">Este usuario solo la opcion de ver prorrateo </label>
                <input type="hidden" name="admin" id="admin" value="2" class="form-control col-md-7 col-xs-12" >
              </div>
            </div>

            <div class="form-group" id="permiosos-personales" style="display: none;">
              <label class="control-label col-md-3 col-sm-3 col-xs-12">Permisos personales</label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <label class="col-md-12 col-sm-12 col-xs-12 form-control">Este usuario ve todas las seccion de todas las plantas</label>
                <input type="hidden" name="admin" id="admin" value="2" class="form-control col-md-7 col-xs-12" >
              </div>
            </div>

            <div class="form-group" id="permiosos-personalizados" style="display:none;">
              <label class="col-md-3 col-sm-3 col-xs-12 control-label">Secciones a visualizar
                <br>
                <small class="text-navy">Selecciona dentro de la <br> tabla que secciones deseas <br> que este perfil quieres <br> que visualice. <br> También asigna si deseas <br> que pueda realizar: 
                <ul>
                  <li>Ver PDF</li>
                  <li>Ver Excel</li>
                  <li>Agregar</li>
                  <li>Editar</li>
                  <li>Eliminar</li>
                </ul>
                </small> 
              </label>

              <div class="col-md-6 col-sm-6 col-xs-6">
                <table class="table table-striped table-hover" id="muestra-cupones">
                  <thead>
                    <tr>
                      <th>Sección</th>
                      <th>Ver PDF</th>
                      <th>Ver Excel</th>
                      <th>&nbsp;&nbsp;&nbsp;&nbsp;Crear</th>
                      <th>&nbsp;&nbsp;&nbsp;&nbsp;Editar</th>
                      <th>&nbsp;&nbsp;&nbsp;&nbsp;Eliminar</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php echo $permisos; ?>
                  </tbody>
                </table>

              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="descripcion">Descripci&oacute;n <span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <textarea class="form-control" name="descripcion" id="descripcion" placeholder="Descripci&oacute;n del administrador"></textarea>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="departamento">Departamento de asignación<span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <select class="form-control" name="departamento" id="departamento" >
                  <option value="" disabled selected>Selecciona un departamento</option>
                  <?php echo $departamentos; ?>
                </select>
              </div>
            </div>

            <div class="form-group" id="departamentos" hidden>
              <!--label class="control-label col-md-3 col-sm-3 col-xs-12" for="departamento">Departamento de asignación<span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="col-md-9 col-sm-9 col-xs-9">
                  <select class="form-control" name="departamento" id="departamento">
                    <option value="" hidden>Selecciona un Departamento</option>
                    <?php echo $departamentos; ?>
                  </select>
                </div>
                <div class="col-md-3 col-sm-3 col-xs-3">
                  <input type="button" class="btn btn-success" id="btnDepartamentoAdd" value="Agregar">
                </div>
              </div-->
            </div>

            <div class="form-group" id="add-departamentos" hidden>
              <!--label class="control-label col-md-3 col-sm-3 col-xs-12" for="incentivo">Departamentos Asignados</label>
              <div class="col-md-6 col-sm-6 col-xs-12" id="departamento_asignado">
              </div-->
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="identificador">Identificador<span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <select class="form-control" name="identificador" id="identificador">
                  <option value="" disabled selected>Selecciona un identificador</option>
                  <option value="xochimilco_entrada">XOCHIMILCO</option>
                  <option value="vallejo_entrada">VALLEJO</option>
                  <option value="gatsa_entrada">GATSA</option>
                  <option value="unidesh_entrada">UNIDESH</option>
                  <option value="produccion_entrada">PRODUCCION</option>
                  <!--?php echo $tipo ?-->
                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-12" for="status">Estatus<span class="required">*</span></label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                <select class="form-control" name="status" id="status" >
                  <option value="" disabled selected>Selecciona un estatus</option>
                  <?php echo $status; ?>
                </select>
              </div>
            </div>

            <div class="form-group">
              <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-2 col-xs-offset-3">
                <button class="btn btn-danger col-md-3 col-sm-3 col-xs-5" type="button" id="btnCancel">Cancelar</button>
                <button class="btn btn-primary col-md-3 col-sm-3 col-xs-5" type="reset" >Resetear</button>
                <button class="btn btn-success col-md-3 col-sm-3 col-xs-5" id="btnAdd" type="submit">Agregar</button>
              </div>
            </div>
            <div id="resultado">

            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php echo $footer;?>
