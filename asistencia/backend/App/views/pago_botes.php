<?php echo $header; ?>
<div class="right_col">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="x_panel">
			<div class="x_title">
				<h2>Informacion de pago de botes <small>muesta la forma en la que se pagaran los botes </small></h2>
				<div class="clearfix"></div>
			</div>
			<div class="x_content">
				<div class="dashboard-widget-content">

					<ul class="list-unstyled timeline widget">
						<li>
							<div class="block">
								<div class="block_content">
									<h2 class="title"><a>1. Pago completo en botes extra</a></h2>
									<div class="byline"><span>Pago por botes extra</span> en <a>incentivos</a></div>
									<p class="excerpt">Esta opci&oacute;n es valida cuando se tiene todos los incentivos para los colaboradores que son de "<b>PAM Liquidos</b>", y la cantidad que se le paganara es la siguiente </p>

									<p>
										<ul>
											<ol>- Clara: <b> <?php echo "$".number_format($completo['clara'], 2, '.', '') ?></b> </ol>
											<ol>- Yem : <b> <?php echo "$".number_format($completo['yema'], 2, '.', '') ?></b> </ol>
											<ol>- Huevo liquido : <b> <?php echo "$".number_format($completo['huevo_liquido'], 2, '.', '') ?></b> </ol>
										</ul>
									</p>

									<p><b><i>Esto correspondera siempre que el colaborados este cumpliendo con los siguientes aspectos:</i></b></p>

									<p>
										<ul>
											<ol> - No tener faltas y si tiene alguna, debe tener una incidencia para no tener faltas. </ol>
											<ol> - Cumplir con los incentivos correspondientes.</ol>
										</ul>
									</p>

									<p>
										<a href="/Incentivo/modificarBotesPrecio/<?php echo $completo['pago_botes_id']; ?>" class="btn btn-success">MODIFICAR VALORES </a>
									</p>
								</div>
							</div>
						</li>


						<li>
							<div class="block">
								<div class="block_content">
									<h2 class="title"><a>2. Pago no completo en botes extra</a></h2>
									<div class="byline"><span>Pago por botes extra</span> en <a>incentivos</a></div>
									<p class="excerpt">Esta opci&oacute;n se aplicara cuando no se tienen todos los incentivos que son </b>corresponedites para cumplir con las metas<b> para los colaboradores que son de "<b>PAM Liquidos</b>", y la cantidad que se le paganara es la siguiente </p>

									<p>
										<ul>
											<ol>- Clara: <b> <?php echo "$".number_format($noCompleto['clara'], 2, '.', '') ?></b> </ol>
											<ol>- Yem : <b> <?php echo "$".number_format($noCompleto['yema'], 2, '.', '') ?></b> </ol>
											<ol>- Huevo liquido : <b> <?php echo "$".number_format($noCompleto['huevo_liquido'], 2, '.', '') ?></b> </ol>
										</ul>
									</p>

									<p>
										<a href="/Incentivo/modificarBotesPrecio/<?php echo $noCompleto['pago_botes_id']; ?>" class="btn btn-success">MODIFICAR VALORES </a>
									</p>
									
								</div>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo $footer; ?>
