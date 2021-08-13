<?php
if(isset($_COOKIE['id'])) {
	session_id(htmlspecialchars($_COOKIE['id']));
	session_start();

	if($_SESSION['vinculo'] != 1) { die(); }	
	
	if(isset($_SESSION['host']) && $_SESSION['host'] == $_SERVER['REMOTE_ADDR']) {

		$id = $_SESSION['idusuario'];
		function sucess($tipo, $dados) {
			echo("
			<div class='col-xs-12'>
				<label>Novo(a) ".mb_strtoupper($tipo)." cadastrado(a): ".$dados.".</label>
			</div>
			<br />");
		}

		if(isset($_GET['ordem'])) {
			include_once 'bdcon.php';
			$conn = bdcon();
			switch (htmlspecialchars($_GET['ordem'])) {
				case 'novo':
					if(isset($_GET['tipo'], $_GET['tamanho'])) {
						$tipo = htmlspecialchars($_GET['tipo']);
						$len = is_numeric($_GET['tamanho']) ? $_GET['tamanho'] : die('Aconteceu um erro desconhecido. Tente novamente.');
						switch ($tipo) {
							case 'instituicao':
								echo("
								<form role='form' id='form'>
									<div class='form-group'>
										<input id='tipo' name='tipo' type='text' value='instituicao' hidden />
										<input id='dados' name='dados' type='text' class='form-control' placeholder='INSTITUIÇÃO VINCULADA O PIBID...' maxlength=".$len." required />
									</div>
								</form>
								");
								break;
							case 'local':
								echo("
								<form role='form' id='form'>
									<div class='form-group'>
										<input id='tipo' name='tipo' type='text' value='local' hidden />
										<input id='dados' name='dados' type='text' class='form-control' placeholder='LOCAL DE ATUAÇÃO...' maxlength=".$len." required />
									</div>
								</form>
								");
								break;
							case 'vinculo':
								echo ("
								<form role='form' id='form'>
									<div class='form-group'>
										<input id='tipo' name='tipo' type='text' value='vinculo' hidden />
										<input id='dados' name='dados' type='text' value='dados' hidden />
							    		<select id='instVinc' name='instVinc' class='form-control' required>
							    			<option value=''>INSTITUIÇÃO VINCULADA AO PIBID...</option>");
											$consulta = mysqli_query($conn, "SELECT idinstituicao, instdescricao FROM instituicao, instituicao_coordenador WHERE idinstituicao = icinstituicao AND iccoordenador = $id ORDER BY instdescricao ASC");
							    			while($row = mysqli_fetch_row($consulta)){
							    				echo("<option value='".$row[0]."'>".$row[1]."</option>");
							    			}
							    		echo ("</select>
									</div>
									<div class='form-group'>
										<input id='tipo' name='tipo' type='text' value='vinculo' hidden />
							    		<select id='localVinc' name='localVinc' class='form-control' required>
							    			<option value=''>LOCAL DE ATUAÇÃO...</option>");
											$consulta = mysqli_query($conn, "SELECT * FROM local ORDER BY locdescricao ASC");
							    			while($row = mysqli_fetch_row($consulta)){
							    				echo("<option value='".$row[0]."'>".$row[1]."</option>");
							    			}
							    		echo ("</select>
									</div>
								</form>
								");
								break;
							default:
								echo("Erro: Ação suspeita bloqueada!");
								break;
						}
					}
					break;
				case 'salvar':
					if(isset($_GET['tipo'], $_GET['dados'])) {
						$tipo = htmlspecialchars($_GET['tipo']);
						if(($dados = mysqli_real_escape_string($conn, filter_var(htmlspecialchars(trim($_GET['dados'])), FILTER_SANITIZE_STRING))) != "") {
							switch ($tipo) {
								case 'instituicao':
									mysqli_query($conn, "SELECT * FROM instituicao_coordenador WHERE iccoordenador = $id");
									if(mysqli_affected_rows($conn) > 0) echo("Você já tem vínculo com uma Instituição.<br />Remova o vínculo para poder adicionar nova Instituição.");
									else if(mysqli_affected_rows($conn) == 0) {
										$dados = substr($dados, 0, 128);
										mysqli_query($conn, "INSERT INTO instituicao(instdescricao) VALUES ('$dados')");
										if(mysqli_affected_rows($conn) > 0) {
											$idic = mysqli_insert_id($conn);
											mysqli_query($conn, "INSERT INTO instituicao_coordenador(icinstituicao, iccoordenador) VALUES ($idic, $id)");
											if(mysqli_affected_rows($conn) > 0) {
												sucess('instituição', $dados);
											} 
										} else { echo("Erro: a instituição já pode está cadastrada."); }
									} else { echo("Você já tem vínculo com uma Instituição.<br />Remova o vínculo para poder adicionar nova Instituição."); }
									break;
								case 'local':
									$dados = substr($dados, 0, 128);
									mysqli_query($conn, "INSERT INTO local(locdescricao) VALUES ('$dados')");
									if(mysqli_affected_rows($conn) > 0) {
										sucess($tipo, $dados);
									} else { echo("Erro ao tentar adicionar o local."); }
									break;
								case 'vinculo':
									if(is_numeric($_GET['instVinc']) && is_numeric($_GET['localVinc'])) {
										$instVinc = mysqli_real_escape_string($conn, htmlspecialchars($_GET['instVinc']));
										$localVinc = mysqli_real_escape_string($conn, htmlspecialchars($_GET['localVinc']));
										mysqli_query($conn, "SELECT * FROM inst_loc_coord WHERE ilclocal = $localVinc AND ilcinstituicao = $instVinc AND ilccoordenador = $id");
										if(mysqli_affected_rows($conn) > 0) {
											echo("Você já possui esse Local de Atuação vinculado à esta Instituição.");
										} else if(mysqli_affected_rows($conn) == 0) {
											mysqli_query($conn, "INSERT INTO inst_loc_coord(ilclocal, ilcinstituicao, ilccoordenador) VALUES ($localVinc, $instVinc, $id)");
											if(mysqli_affected_rows($conn) > 0) {
												echo("
												<div class='col-xs-12'>
													<label>Novo Local de Atuação cadastrado com sucesso.</label>
												</div>
												<br />");
											} 
										}
									} else { echo("Você não selecionou uma opção."); }
									break;
								default:
									echo("Erro: Ação suspeita bloqueada!");
									break;
							}
						} else { echo("Erro: dados inválidos."); }
					}
					break;
				case 'atualizarInst':
					mysqli_query($conn, "SELECT * FROM instituicao_coordenador WHERE iccoordenador = $id");
					if(mysqli_affected_rows($conn) == 0) {
						if(isset($_GET['dados']) && is_numeric($_GET['dados'])) {
							$dados = htmlspecialchars($_GET['dados']);
							mysqli_query($conn, "SELECT * FROM instituicao_coordenador WHERE icinstituicao = $dados AND iccoordenador = $id");
							if(mysqli_affected_rows($conn) == 0) {
								$dados = mysqli_real_escape_string($conn, $_GET['dados']);
								mysqli_query($conn, "INSERT INTO instituicao_coordenador(icinstituicao, iccoordenador) VALUES ($dados, $id)");
								if(mysqli_affected_rows($conn) > 0) echo "1";
							}
						}
					}
					break;
				case 'listar':
					if(isset($_GET['tipo'])) {
						$tipo = htmlspecialchars($_GET['tipo']);
						switch($tipo) {
							case 'instituicao':
								$consulta = mysqli_query($conn, "SELECT idinstituicao, instdescricao FROM instituicao, instituicao_coordenador WHERE idinstituicao = icinstituicao AND iccoordenador = $id ORDER BY instdescricao ASC");
								while($row = mysqli_fetch_row($consulta)){
									echo("<li class='list-group-item' id='instituicao".$row[0]."'>
											<strong>".$row[1]."</strong>
											<a class='text-danger pull-right' id='".$row[0]."' title='Remover Instituição' onclick='remInst(this.id)'>
												<span class='glyphicon glyphicon-fire'></span>
											</a>
										</li>");
								}
								break;
							case 'selInstituicao':
								mysqli_query($conn, "SELECT * FROM instituicao_coordenador WHERE iccoordenador = $id");
								if(mysqli_affected_rows($conn) == 0) {
									echo("<option value=''>SELECIONE</option>");
									$consulta = mysqli_query($conn, "SELECT idinstituicao, instdescricao FROM instituicao");
					    			while($row = mysqli_fetch_row($consulta)){
					    				echo("<option value='".$row[0]."'>".$row[1]."</option>");
					    			}
					    		}
								break;
							case 'vinculo':
								$consulta = mysqli_query($conn, "SELECT idinst_loc_coord, instdescricao, locdescricao FROM instituicao, local, inst_loc_coord WHERE ilcinstituicao = idinstituicao AND ilclocal = idlocal AND ilccoordenador = $id ORDER BY instdescricao ASC, locdescricao ASC");
								while($row = mysqli_fetch_row($consulta)){
									echo("<li class='list-group-item'>
											<strong>".$row[1]."</strong> >> <strong>".$row[2]."</strong>
											<a class='text-info pull-right' href=\"relatos-pdf.php?idloc_inst=".$row[0]."\" title='Exportar relatos' target='relatos'>
												<span class='glyphicon glyphicon-print'></span>
											</a>
										</li>");
								}
								break;
							default:
								echo("Erro: Ação suspeita bloqueada!");
								break;
						}
					}
					break;
				case 'remover':
					if(isset($_GET['instituicao']) && is_numeric($_GET['instituicao'])) {
						$instituicao = htmlspecialchars($_GET['instituicao']);
						mysqli_query($conn, "SELECT * FROM inst_loc_coord WHERE ilcinstituicao = $instituicao AND ilccoordenador = $id");
						if(mysqli_affected_rows($conn) == 0) {
							mysqli_query($conn, "DELETE FROM instituicao_coordenador WHERE icinstituicao = $instituicao AND iccoordenador = $id");
							if(mysqli_affected_rows($conn) > 0) {
								echo("1");
							}
						} else {
							echo("0");
						}
					}
					break;
				case 'autorizar':
					if(isset($_GET['usuario']) && is_numeric($_GET['usuario'])) {
						$usuario = htmlspecialchars($_GET['usuario']);
						mysqli_query($conn, "UPDATE usuario SET autorizado = 1 WHERE idusuario = $usuario AND coordenador = $id");
						if(mysqli_affected_rows($conn) > 0) {
							$row = mysqli_fetch_row(mysqli_query($conn, "SELECT nome, instituicao, local FROM usuario WHERE idusuario = $usuario"));
							$sqlquery = "O coordenador permitiu que <a href=\'?ordem=ver-perfil&usuario=".$usuario."\'><strong>".$row[0]."</strong></a> faça parte da equipe.";
							date_default_timezone_set('America/Bahia');
							$data = date('Y-m-d H:i:s', time());
							$instituicao = $row[1];
							$local = $row[2];
							$idequipe = mysqli_fetch_array(mysqli_query($conn, "SELECT idinst_loc_coord FROM inst_loc_coord WHERE ilcinstituicao = $instituicao AND ilclocal = $local AND ilccoordenador = $id"));
							$idequipe = $idequipe['idinst_loc_coord'];
							mysqli_query($conn, "INSERT INTO historico(hdescricao, hdata, hequipe) VALUES ('$sqlquery', '$data', $idequipe)");
							echo "1";
						}
					}
					break;
				case 'negar':
					if(isset($_GET['usuario']) && is_numeric($_GET['usuario'])) {
						$usuario = htmlspecialchars($_GET['usuario']);
						$row = mysqli_fetch_row(mysqli_query($conn, "SELECT nome, instituicao, local FROM usuario WHERE idusuario = $usuario"));
						mysqli_query($conn, "UPDATE usuario SET vinculo = null, instituicao = null, coordenador = null, local = null, supervisor = null, autorizado = 0 WHERE idusuario = $usuario AND coordenador = $id");
						if(mysqli_affected_rows($conn) > 0) {
							$sqlquery = "O coordenador negou o acesso de <strong>".$row[0]."</strong> ao sistema.";
							date_default_timezone_set('America/Bahia');
							$data = date('Y-m-d H:i:s', time());
							$instituicao = $row[1];
							$local = $row[2];
							$idequipe = mysqli_fetch_array(mysqli_query($conn, "SELECT idinst_loc_coord FROM inst_loc_coord WHERE ilcinstituicao = $instituicao AND ilclocal = $local AND ilccoordenador = $id"));
							$idequipe = $idequipe['idinst_loc_coord'];
							mysqli_query($conn, "INSERT INTO historico(hdescricao, hdata, hequipe) VALUES ('$sqlquery', '$data', $idequipe)");
							if(mysqli_affected_rows($conn) > 0) $idhistorico = mysqli_insert_id($conn);
							mysqli_query($conn, "UPDATE usuario SET vinculo = null, instituicao = null, coordenador = null, local = null, supervisor = null, autorizado = 0 WHERE supervisor = $usuario AND coordenador = $id");
							if(mysqli_affected_rows($conn) > 0) {
								$sqlquery = " E seus bolsistas foram removidos da equipe.";
								mysqli_query($conn, "UPDATE historico SET hdescricao = concat(hdescricao, '$sqlquery') WHERE idhistorico = $idhistorico");
							}
							echo "1";
						}
					}
					break;
				default:
					echo("Erro: Ação suspeita bloqueada!");
					break;
			}
		}
	}
}
?>