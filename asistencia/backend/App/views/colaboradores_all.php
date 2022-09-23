<?php echo $header; ?>

<script type="text/javascript">
    /*function funcion(){
        celda = document.getElementById("#celda1")

        if(celda == "Amarillo")
        {
            alert ("Valor del td: "+celda.innerHTML);
        }
        else
        {
            alert ("Valor del td: "+ celda);
        }

    }
    funcion();*/

</script>
<div class="right_col">

    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-default">
            <div class="x_title">
                <br><br>
                <h1> <!--Catálogo de Gestión de Colaboradores</small> --> <?php echo $tituloColaboradores; ?></h1>
                <div class="clearfix"></div>
            </div>
            <form name="all1" id="all1" action="/Colaboradores/index" method="POST">
                <div class="panel-body">
                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">

                        <div class="form-group col-md-4 col-sm-4 col-xs-4 col-lg-4">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="id_catalogo_empresa">Empresa<span class="required">*</span></label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <select class="form-control" name="catalogo_empresa_id" id="catalogo_empresa_id">
                                    <option value="" >Selecciona una Empresa</option>
                                    <?php echo $idEmpresa; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-md-4 col-sm-4 col-xs-4 col-lg-4">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="id_catalogo_ubicacion">Ubicación<span class="required">*</span></label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <select class="form-control" name="catalogo_ubicacion_id" id="catalogo_ubicacion_id">
                                    <option value="" >Selecciona una Ubicación</option>
                                    <?php echo $idUbicacion; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-md-4 col-sm-4 col-xs-4 col-lg-4">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="id_catalogo_departamento">Departamento<span class="required">*</span></label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <select class="form-control" name="catalogo_departamento_id" id="catalogo_departamento_id">
                                    <option value="" >Selecciona un Departamento</option>
                                    <?php echo $idDepartamento; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-md-4 col-sm-4 col-xs-4 col-lg-4">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="id_catalogo_puesto">Puesto<span class="required">*</span></label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <select class="form-control" name="catalogo_puesto_id" id="catalogo_puesto_id">
                                    <option value="" >Selecciona un Puesto</option>
                                    <?php echo $idPuesto; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-md-4 col-sm-4 col-xs-4 col-lg-4">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="status">Nomina<span class="required">*</span></label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <select class="form-control" name="status" id="status">
                                    <option value="">Selecciona una nomina</option>
                                    <?php echo $nomina; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-md-4 col-sm-4 col-xs-4 col-lg-4">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="status"></label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <button type="submit" class="btn btn-info col-md-12 col-sm-12 col-xs-12 col-lg-12" value="Buscar" id="btnAplicar">
                                    <span class="glyphicon glyphicon-search"> Buscar</span>
                                </button>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="form-group col-md-12 col-sm-12 col-xs-12 col-lg-12" style="margin-top: 55px;">
                            <a href="/Colaboradores/existente" type="button" class="btn btn-primary btn-circle"  <?= $agregarHidden?>><i class="fa fa-plus"> <b>Nuevo Colaborador</b></i></a>
                            <button id="delete" type="button" class="btn btn-danger btn-circle"  <?= $eliminarHidden?>><i class="fa fa-remove"> <b>Eliminar</b></i></button>
                            <button id="btnPDF" type="button" class="btn btn-info btn-circle" <?= $pdfHidden ?>><i class="fa fa-file-pdf-o"> <b>Exportar a PDF</b></i></button>
                            <button id="btnExcel" type="button" class="btn btn-success btn-circle" <?= $excelHidden?>><i class="fa fa-file-excel-o"> <b>Exportar a Excel</b></i></button>
                            <button id="btnBajas" type="button" class="btn btn-info btn-circle"><i class="fa fa-user-times" aria-hidden="true"> <b>Bajas</b></i></button>
                            <button class="btn btn-warning" type="reset" id="btnReiniciar">Reiniciar Busqueda</button>
                        </div>
                    </div>
                    <br>
                    <hr>

                    <div class="row">
                        <div class="col-sm-6 col-lg-4 " >
                            <div class="card">



                                <div style="background-color:#337ab7" FONT COLOR="white" class=" list-group-item text-center"><h4><font color="white"><b>Colaboradores Xochimilco</b></font></h4></div>

                                <div class="list-group-item">
                                    <table class="table" id="x">
                                        <thead class="table-light text-center">
                                        <tr class="text-center">
                                            <th class="text-center">Plantilla Autorizada</th>
                                            <th>Activos</th>
                                            <th>Faltantes</th>
                                            <th>Estatus</th>
                                        </tr>
                                        </thead>
                                        <tbody id="plantilla_xochimilco">
                                        </tbody>
                                        <tbody class="text-center">
                                        <?php echo $tabla_1; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="card border-primary mb-3">

                                <ul class="list-group list-group-flush">
                                    <div style="background-color:#26b99a" FONT COLOR="white" class=" list-group-item text-center"><h4><font color="white"><b>Colaboradores Vallejo</b></font></h4></div>
                                    <div class="list-group-item">
                                        <table class="table">
                                            <thead class="table-light text-center">
                                            <tr class="text-center">
                                                <th class="text-center">Plantilla Autorizada</th>
                                                <th>Activos</th>
                                                <th>Faltantes</th>
                                                <th>Estatus</th>
                                            </tr>
                                            </thead>
                                            <tbody id="plantilla_vallejo">
                                            </tbody>
                                            <tbody class="text-center">
                                            <?php echo $tabla_2; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </ul>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="card">

                                <ul class="list-group list-group-flush">
                                    <div style="background-color:#337ab7" FONT COLOR="white" class=" list-group-item text-center"><h4><font color="white"><b>Colaboradores GATSA</b></font></h4></div>
                                    <li class="list-group-item">
                                        <table class="table">
                                            <thead class="table-light text-center">
                                            <tr class="text-center">
                                                <th class="text-center">Plantilla Autorizada</th>
                                                <th>Activos</th>
                                                <th>Faltantes</th>
                                                <th>Estatus</th>
                                            </tr>
                                            </thead>
                                            <tbody id="plantilla_GATSA">
                                            </tbody>
                                            <tbody class="text-center">
                                            <?php echo $tabla_3; ?>
                                            </tbody>
                                        </table>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="card">

                                <ul class="list-group list-group-flush">
                                    <div style="background-color:#26b99a" FONT COLOR="white" class=" list-group-item text-center"><h4><font color="white"><b>Colaboradores UNIDESH</b></font></h4></div>
                                    <li class="list-group-item">
                                        <table class="table">
                                            <thead class="table-light text-center">
                                            <tr class="text-center">
                                                <th class="text-center">Plantilla Autorizada</th>
                                                <th>Activos</th>
                                                <th>Faltantes</th>
                                                <th>Estatus</th>
                                            </tr>
                                            </thead>
                                            <tbody id="plantilla_UNIDESH">
                                            </tbody>
                                            <tbody class="text-center">
                                            <?php echo $tabla_4; ?>
                                            </tbody>
                                        </table>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4" >
                            <div class="card">



                                <div style="background-color:#337ab7" FONT COLOR="white" class=" list-group-item text-center"><h4><font color="white"><b>Colaboradores Administrativos</b></font></h4></div>

                                <div class="list-group-item">
                                    <table class="table" id="x">
                                        <thead class="table-light text-center">
                                        <tr class="text-center">
                                            <th class="text-center">Plantilla Autorizada</th>
                                            <th>Activos</th>
                                            <th>Faltantes</th>
                                            <th>Estatus</th>
                                        </tr>
                                        </thead>
                                        <tbody id="colaboradores_xochimilco">
                                        </tbody>
                                        <tbody class="text-center">
                                        <?php echo $tabla_5; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr />

                </div>
            </form>
            <form name="all" id="all" action="/Colaboradores/delete" method="POST">
                <div class="panel-body">
                    <div class="dataTable_wrapper">
                        <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
                            <thead>
                            <tr>
                                <th><input type="checkbox" name="checkAll" id="checkAll" value=""/></th>
                                <th></th>
                                <th># Empleado</th>
                                <th>Nombre</th>
                                <th>Empresa</th>
                                <th>Departamento</th>
                                <th>Pago</th>
                                <th>Identificador</th>
                                <th>Acciones</th>
                            </tr>
                            </thead>
                            <tbody id="registros">
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
