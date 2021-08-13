<div class='col-sm-12'>
	<div class='panel-group' id='equipe' role='lista-de-equipes' aria-multiselectable='true'>
		<?php
		$conn = bdcon();
		$consulta = mysqli_query($conn, "SELECT ilcinstituicao, ilclocal FROM inst_loc_coord WHERE ilccoordenador = $id ORDER BY ilcinstituicao, ilclocal");
		$count = 1;
		while($row = mysqli_fetch_row($consulta)) {
			$instituicao = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM instituicao WHERE idinstituicao = $row[0]"));
			$local = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM local WHERE idlocal = $row[1]"));
			$supervisor = mysqli_query($conn, "SELECT idusuario, nome, ult_acesso, foto FROM usuario WHERE instituicao = $row[0] AND coordenador = $id AND local = $row[1] AND vinculo = 2 AND autorizado = 1 ORDER BY nome");
			$bolsista = mysqli_query($conn, "SELECT idusuario, nome, ult_acesso, foto FROM usuario WHERE instituicao = $row[0] AND coordenador = $id AND local = $row[1] AND vinculo = 3 AND autorizado = 1 ORDER BY nome");
			echo ("
			<div class='panel panel-primary'>
				<div class='panel-heading' role='cab-equipe' id='cab-equipe-$count'>
					<div class='panel-title'>
						<a data-toggle='collapse' data-parent='#equipe' href='#equipe-$count' ariaexpanded='true' aria-controls='equipe-$count'>
							<span class='pull-right glyphicon glyphicon-collapse-down'></span>
							".mb_strtoupper($instituicao['instdescricao'], 'utf8')." - ".mb_strtoupper($local['locdescricao'], 'utf8')."
						</a>
					</div>
				</div>
				<div id='equipe-$count' class='panel-collapse collapse in' role='equipe' arialabelledby='cab-equipe-$count'>
					<div class='panel-body'>
						<div class='form-group'>
							<strong>Coordenador</strong>
							<ol class='list-group'>
								<li class='list-group-item'>
									<a href='?ordem=ver-perfil&usuario=".$id."''>".$_SESSION['nome']."</a>
								</li>
							</ol>
						</div>
						<div class='form-group'>
							<strong>Supervisor</strong>
							<ol class='list-group'>");
							while($row = mysqli_fetch_row($supervisor)) {
								$srcimg = ($row[3] == "") ? '/modulo/credencial/img-perfil/nenhuma.png' : '/modulo/credencial/img-perfil/'.$row[3];
								echo ("
									<li class='list-group-item' id='li".$row[0]."'>
										<a href='?ordem=ver-perfil&usuario=".$row[0]."'><img src='$srcimg' width='20px'/> ".$row[1]."</a> (Ultimo acesso: ".date("H:i:s d/m/Y", strtotime($row[2])).")
										<div class='pull-right'>
											<a class='text-success' href='?ordem=relato&usuario=".$row[0]."' title='Ver relatos deste usuário'>
												<span class='glyphicon glyphicon-folder-close'></span>
											</a>
											<a class='text-danger' id='btn".$row[0]."' title='Negar fazer parte da equipe' onclick='negar(".$row[0].")'>
												<span class='glyphicon glyphicon-fire'></span>
											</a>
										</div>
									</li>
								");
							}
						echo("
							</ol>
						</div>
						<div class='form-group'>
							<strong>Bolsista</strong>
							<ol class='list-group'>");
							while($row = mysqli_fetch_row($bolsista)) {
								$srcimg = ($row[3] == "") ? '/modulo/credencial/img-perfil/nenhuma.png' : '/modulo/credencial/img-perfil/'.$row[3];
								echo ("<li class='list-group-item' id='li".$row[0]."'>
										<a href='?ordem=ver-perfil&usuario=".$row[0]."'><img src='$srcimg' width='20px'/> ".$row[1]."</a> (Ultimo acesso: ".date("H:i:s d/m/Y", strtotime($row[2])).")
										<div class='pull-right'>
											<a class='text-success' href='?ordem=relato&usuario=".$row[0]."' title='Ver relatos deste usuário'>
												<span class='glyphicon glyphicon-folder-close'></span>
											</a>
											<a class='text-danger' id='btn".$row[0]."' title='Negar fazer parte da equipe' onclick='negar(".$row[0].")'>
												<span class='glyphicon glyphicon-fire'></span>
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
			$count++;
		}
		?>
	</div>
</div>