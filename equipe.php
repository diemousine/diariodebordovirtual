<div class='col-sm-12'>
	<div class='panel-group' id='equipe' role='lista-de-equipes' aria-multiselectable='true'>
		<?php
		$conn = bdcon();
		$inst = $_SESSION['instituicao'];
		$loc = $_SESSION['local'];
		$coord = $_SESSION['coordenador'];
		$instituicao = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM instituicao WHERE idinstituicao = $inst"));
		$local = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM local WHERE idlocal = $loc"));
		$coordenador = mysqli_fetch_array(mysqli_query($conn, "SELECT nome, foto, ult_acesso FROM usuario WHERE idusuario = $coord"));
		$supervisor = mysqli_query($conn, "SELECT idusuario, nome, ult_acesso, foto FROM usuario WHERE instituicao = $inst AND coordenador = $coord AND local = $loc AND vinculo = 2 AND autorizado = 1 ORDER BY nome");
		$bolsista = mysqli_query($conn, "SELECT idusuario, nome, ult_acesso, foto FROM usuario WHERE instituicao = $inst AND coordenador = $coord AND local = $loc AND vinculo = 3 AND autorizado = 1 ORDER BY nome");
		$srcimg = ($coordenador['foto'] == "") ? '/modulo/credencial/img-perfil/nenhuma.png' : '/modulo/credencial/img-perfil/'.$coordenador['foto'];
		echo ("
		<div class='panel panel-primary'>
			<div class='panel-heading' role='cab-equipe' id='cab-equipe-1'>
				<div class='panel-title'>
					<a data-toggle='collapse' data-parent='#equipe' href='#equipe-1' ariaexpanded='true' aria-controls='equipe-1'>
						<span class='pull-right glyphicon glyphicon-collapse-down'></span>
						".mb_strtoupper($instituicao['instdescricao'], 'utf8')." - ".mb_strtoupper($local['locdescricao'], 'utf8')."
					</a>
				</div>
			</div>
			<div id='equipe-1' class='panel-collapse collapse in' role='equipe' arialabelledby='cab-equipe-1'>
				<div class='panel-body'>
					<div class='form-group'>
						<strong>Coordenador</strong>
						<ol class='list-group'>
							<li class='list-group-item'>
								<a class='text-success pull-right' href='?ordem=relato&usuario=".$_SESSION['coordenador']."' title='Ver relatos deste usuário'>
									<span class='glyphicon glyphicon-folder-close'></span>
								</a>
								<a href='?ordem=ver-perfil&usuario=".$_SESSION['coordenador']."''><img src='$srcimg' width='20px'/> ".$coordenador['nome']."</a> (Ultimo acesso: ".date("H:i:s d/m/Y", strtotime($coordenador['ult_acesso'])).")
							</li>
						</ol>
					</div>
					<div class='form-group'>
						<strong>Supervisor</strong>
						<ol class='list-group'>");
							while($row = mysqli_fetch_row($supervisor)) {
								$srcimg = ($row[3] == "") ? '/modulo/credencial/img-perfil/nenhuma.png' : '/modulo/credencial/img-perfil/'.$row[3];
								echo ("<li class='list-group-item'>
										<a href='?ordem=ver-perfil&usuario=".$row[0]."'><img src='$srcimg' width='20px'/> ".$row[1]."</a>  (Ultimo acesso: ".date("H:i:s d/m/Y", strtotime($row[2])).")
										<div class='pull-right'>
											<a class='text-success' href='?ordem=relato&usuario=".$row[0]."' title='Ver relatos deste usuário'>
												<span class='glyphicon glyphicon-folder-close'></span>
											</a>
										</div>
									</li>");
							}
						echo("
						</ol>
					</div>
					<div class='form-group'>
						<strong>Bolsista</strong>
						<ol class='list-group'>");
							while($row = mysqli_fetch_row($bolsista)) {
								$srcimg = ($row[3] == "") ? '/modulo/credencial/img-perfil/nenhuma.png' : '/modulo/credencial/img-perfil/'.$row[3];
								echo ("<li class='list-group-item'>
										<a href='?ordem=ver-perfil&usuario=".$row[0]."'><img src='$srcimg' width='20px'/> ".$row[1]."</a>  (Ultimo acesso: ".date("H:i:s d/m/Y", strtotime($row[2])).")
										<div class='pull-right'>
											<a class='text-success' href='?ordem=relato&usuario=".$row[0]."' title='Ver relatos deste usuário'>
												<span class='glyphicon glyphicon-folder-close'></span>
											</a>
										</div>
									</li>");
							}
						echo("
						</ol>
					</div>
				</div>
			</div>
		</div>
		");
		?>
	</div>
</div>