<div class='row'>
	<div class='col-sm-12'>
		<div class='panel panel-primary'>
			<div class='panel-heading'>HISTÓRICO DE RELATOS - <?php if(!isset($_GET['data'])) echo(date('m/Y')); else if(empty($_GET['data'])) echo('TODOS'); else echo(date('m/Y', strtotime(htmlspecialchars($_GET['data'])))); ?>
				<div class='pull-right'>
					<form>
						<input name='ordem' value='relato' hidden>
						<?php if(isset($_GET['usuario'])) echo "<input name='usuario' value='".htmlspecialchars($_GET['usuario'])."' hidden>"; ?>
						<a style='color:#fff' id='novoRelato' title='Novo relatório'>
							<span class='glyphicon glyphicon-record'></span>
						</a>
						<button class='btn-primary' type='submit' name='data' value='<?php if(isset($_GET['data'])) echo(date('Y-m', strtotime(htmlspecialchars($_GET['data']).' -1 month'))); else echo(date('Y-m', strtotime('-1 month'))); ?>' style='color:#fff' id='mesAnterior' title='Relatos anteriores'>
							<span class='glyphicon glyphicon-step-backward'></span>
						</button>
						<button class='btn-primary' type='submit' name='data' value='<?php if(isset($_GET['data']) && !empty($_GET['data']) && strtotime(htmlspecialchars($_GET['data']))<strtotime('-1 month')) echo(date('Y-m', strtotime(htmlspecialchars($_GET['data']).' +1 month'))); else echo(date('Y-m', time())); ?>' style='color:#fff' id='proxMes' title='Relatos mais recentes'>
							<span class='glyphicon glyphicon-step-forward'></span>
						</button>
						<button class='btn-primary' type='submit' name='data' value='' style='color:#fff' id='todosRelatos' title='Mostrar todos os relatos'>
							<span class='glyphicon glyphicon-eye-open'></span>
						</button>
					</form>
				</div>
			</div>
			<div class="panel-body">
				<ol class='list-group' id='relato-body'>
					<?php
					$conn = bdcon();
					if(isset($_GET['usuario']) && is_numeric($_GET['usuario'])) $id = htmlspecialchars($_GET['usuario']); else $id = $_SESSION['idusuario'];
					$consulta = mysqli_fetch_array(mysqli_query($conn, "SELECT coordenador FROM usuario WHERE idusuario = $id AND autorizado = 1"));
					if($consulta['coordenador'] == $_SESSION['coordenador']) {
						date_default_timezone_set('America/Bahia');

						$data = (isset($_GET['data'])) ? htmlspecialchars($_GET['data']) : date('Y-m', time());
						$coordenador = $_SESSION['coordenador'];

						$sqlquery = ($id == $_SESSION['idusuario']) ? "SELECT idrelato, reldata, reltitulo, relcoordenador, relinstituicao, rellocal FROM relato WHERE relusuario = $id AND reldata like '$data%'" : "SELECT idrelato, reldata, reltitulo FROM relato WHERE relcoordenador = $coordenador AND relusuario = $id AND reldata like '$data%'";
						$consulta = mysqli_query($conn, $sqlquery);
						if(mysqli_affected_rows($conn) > 0) {
							while($row = mysqli_fetch_row($consulta)){
								$data = date('Y-m-d', time());
								echo("<li class='list-group-item'>
										<a id='".$row[0]."' title='Ver relato.' onclick='verRelato(this.id)'>
											<strong>".date('d/m/Y', strtotime($row[1]))." - ".$row[2]."</strong>
										</a>");
								if(date('m', strtotime($row[1])) == date('m', time()) && $id == $_SESSION['idusuario'] && $row[3] == $_SESSION['coordenador'] && $row[4] == $_SESSION['instituicao'] && $row[5] == $_SESSION['local']) {
									echo("
										<a class='pull-right text-warning' id='".$row[0]."' title='Editar relato.' onclick='editarRelato(this.id)'>
											<span class='glyphicon glyphicon-edit'></span>
										</a>");
								}
								echo("</li>");
							}
						} else {
							echo ("<li class='list-group-item'>Nenhum relato encontrado.</li>");
						}
					} else {
						echo("<li class='list-group-item'>Você não tem permissão para acessar a lista de relatos deste usuário.</li>");
					}
					?>
				</ol>
			</div>
		</div>
	</div>
</div>

<div class='row'>
  <div class="modal fade" id='modalRelato' tabindex="-1" role="diálogo" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Controle de Relatos</h4>
        </div>
        <div class="modal-body" id='modal-body'>
          <p>One fine body&hellip;</p>
        </div>
        <div class="modal-footer">
          <button class="btn btn-default" data-dismiss="modal" id='cancelar'>Cancelar</button>
          <button class="btn btn-primary" id='relSalvar'>Salvar</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
</div>