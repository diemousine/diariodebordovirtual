<?php

/*
 * EMAIL DE NOTIFICAÇÃO DE NOVO RELATO
 */
function sendEmail($conn, $id) {
	if($_SESSION['coordenador'] != $id) {
		$coordenador = $_SESSION['coordenador'];
		$resultado = mysqli_fetch_array(mysqli_query($conn, "SELECT nome, email FROM login inner join usuario on login.idlogin = usuario.usidlogin WHERE idusuario = $coordenador"), MYSQLI_ASSOC);
		$para = $resultado['nome']." <".$resultado['email'].">";
	}
	if($_SESSION['supervisor'] != $id) {
		$supervisor = $_SESSION['supervisor'];
		$resultado = mysqli_fetch_array(mysqli_query($conn, "SELECT nome, email FROM login inner join usuario on login.idlogin = usuario.usidlogin WHERE idusuario = $supervisor"), MYSQLI_ASSOC);
		$para .=", ".$resultado['nome']." <".$resultado['email'].">";
	}
	$assunto = 'Novo Relato de '.$_SESSION['nome'].' no DBV';
	$mensagem = "
	<html lang='pt_BR'>
	  <head>
	  </head>
	  <body>
		<p>".$_SESSION['nome']." acaba de publicar/editar um relato no Diário de Bordo Virtual.</p>
		<p>Acesse o <a href='dbvpibid.hol.es'>Diário de Bordo Virtual</a> para ver.</p>
		<br>
		<p>Atenciosamente,</p>
		<p>Diário de Bordo Virtual.<br />
	  </body>
	</html>
	";
	$mensagem = wordwrap($mensagem, 70);

	$cabecalho = 'MIME-Version: 1.0' . "\r\n";
	$cabecalho .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
	$cabecalho .= 'From: =?UTF-8?B?'.base64_encode('Diário').'?= de Bordo Virtual <no-reply@dbvpibid.hol.es>' . "\r\n";

	return (mail($para, $assunto, $mensagem, $cabecalho)) ? true : false;
}

if(isset($_COOKIE['id'])) {
	session_id(htmlspecialchars($_COOKIE['id']));
	session_start();
	
	if(isset($_SESSION['host']) && $_SESSION['host'] == $_SERVER['REMOTE_ADDR']) {

		$id = $_SESSION['idusuario'];
		date_default_timezone_set('America/Bahia');

		if(isset($_GET['ordem'])) {
			include_once 'bdcon.php';
			$conn = bdcon();
			switch (htmlspecialchars($_GET['ordem'])) {
				case 'salvar':
					if(isset($_GET['tipo']) && !empty($_GET['tipo'])) {
						switch (htmlspecialchars($_GET['tipo'])) {
							case 'relato':
								date_default_timezone_set('America/Bahia');
								$data = date('Y-m-d', time());
								mysqli_query($conn, "SELECT * FROM relato WHERE reldata = '$data' AND relusuario = $id");
								if(mysqli_affected_rows($conn) == 0) {

									if(isset($_GET['titulo'], $_GET['dados']) AND !empty($_GET['dados'])) {
										$relinstituicao = (isset($_GET['instituicao']) && is_numeric($_GET['instituicao'])) ? htmlspecialchars($_GET['instituicao']) : $_SESSION['instituicao'];
										$relcoordenador = (empty($_SESSION['coordenador'])) ? $id : $_SESSION['coordenador'];
										$rellocal = (isset($_GET['local']) && is_numeric($_GET['local'])) ? htmlspecialchars($_GET['local']) : $_SESSION['local'];
										$relsupervisor = (empty($_SESSION['supervisor'])) ? $id : $_SESSION['supervisor'];
										$titulo = mysqli_real_escape_string($conn, filter_var(htmlspecialchars(trim($_GET['titulo'])), FILTER_SANITIZE_STRING));
										if(($relato = mysqli_real_escape_string($conn, filter_var(htmlspecialchars(trim($_GET['dados'])), FILTER_SANITIZE_STRING))) == "") die("Erro: Relato não informado.");
										mysqli_query($conn, "INSERT INTO relato(relinstituicao, relcoordenador, rellocal, relsupervisor, relusuario, reldata, reltitulo, relato) VALUES ($relinstituicao, $relcoordenador, $rellocal, $relsupervisor, $id, '$data', '$titulo', '$relato')");
										if(mysqli_affected_rows($conn) > 0) {
											$idrelato = mysqli_insert_id($conn);
											$sqlquery = "SELECT idinst_loc_coord FROM inst_loc_coord WHERE ilcinstituicao = $relinstituicao AND ilclocal = $rellocal AND ilccoordenador = $relcoordenador";
											$idequipe = mysqli_fetch_array(mysqli_query($conn, $sqlquery), MYSQL_ASSOC);
											$idequipe = $idequipe['idinst_loc_coord'];
											$sqlquery = "<a href=\'?ordem=ver-perfil&usuario=".$id."\'><strong>".$_SESSION['nome']."</strong></a> registrou um novo <a href=\'?ordem=relato&usuario=$id\' title=\'Ver página de relatos do usuário.\'><strong>relato</strong></a> em ".date("d/m/Y", strtotime($data)).".";
											$data = date('Y-m-d H:i:s', time());
											mysqli_query($conn, "INSERT INTO historico(hdescricao, hdata, hequipe) VALUES ('$sqlquery', '$data', $idequipe)");
											
											if(mysqli_affected_rows($conn) > 0) {
												echo(
													json_encode(
														array(
															'relid' => $idrelato,
															'situacao' => true,
														)
													)
												);
											} else {
												echo(mysqli_error($conn));
											}
										} else { echo("Erro na entrada dos dados. Algum campo não foi aceito."); }
									} else { echo("Erro ao tentar gravar o relato."); }
								} else { echo("Você já tem um relatório referente à data de hoje. Não é possível adicionar um novo."); }
								break;
							case 'editado':
								if(isset($_GET['relato'], $_GET['dados']) && is_numeric($_GET['relato'])) {
									if(($relato = mysqli_real_escape_string($conn, filter_var(htmlspecialchars(trim($_GET['dados'])), FILTER_SANITIZE_STRING))) == "") die("Erro: Relato não informado.");
									$idrelato = htmlspecialchars($_GET['relato']);
									$data = date('Y-m-d', time());
									mysqli_query($conn, "UPDATE relato SET relato = '$relato' WHERE idrelato = $idrelato AND relusuario = $id AND MONTH(reldata) = MONTH('$data')");
									if(mysqli_affected_rows($conn) > 0) {
										echo(
											json_encode(
												array(
													'relid' => $idrelato,
													'situacao' => true,
												)
											)
										);
									} else { echo("Erro: Ação suspeita bloqueada!"); }
								} else { echo("Erro: Algum dado não foi aceito pelo sistema."); }
								break;
							case 'continue':
								if(isset($_GET['relato'], $_GET['dados']) && is_numeric($_GET['relato'])) {
									if(($relato = mysqli_real_escape_string($conn, filter_var(htmlspecialchars(trim($_GET['dados'])), FILTER_SANITIZE_STRING))) == "") die("Erro: Relato não informado.");
									$idrelato = htmlspecialchars($_GET['relato']);
									$data = date('Y-m-d', time());
									mysqli_query($conn, "UPDATE relato SET relato = concat(relato, '$relato') WHERE idrelato = $idrelato AND relusuario = $id AND MONTH(reldata) = MONTH('$data')");
									if(mysqli_affected_rows($conn) > 0) {
										echo(json_encode(true));
									} else { echo("Erro: Ação suspeita bloqueada!"); }
								} else { echo("Erro: Algum dado não foi aceito pelo sistema."); }
								break;
							default:
								echo("Erro: Ação suspeita bloqueada!");
								break;
						}
					}
					break;
				case 'listar':
					$data = date('Y-m', time());
					$consulta = mysqli_query($conn, "SELECT idrelato, reldata, reltitulo, relcoordenador, relinstituicao, rellocal  FROM relato WHERE relusuario = $id AND reldata like '$data%'");
					if(mysqli_affected_rows($conn) > 0) {
						while($row = mysqli_fetch_row($consulta)){
								$data = date('Y-m-d', time());
								echo("<li class='list-group-item'>
										<a id='".$row[0]."' title='Ver relato.' onclick='verRelato(this.id)'>
											<strong>".date('d/m/Y', strtotime($row[1]))." - ".$row[2]."</strong>
										</a>");
								if(date('m', strtotime($row[1])) == date('m', time()) && $row[3] == $_SESSION['coordenador'] && $row[4] == $_SESSION['instituicao'] && $row[5] == $_SESSION['local']) {
									echo("
										<a class='pull-right text-warning' id='".$row[0]."' title='Editar relato.' onclick='editarRelato(this.id)'>
											<span class='glyphicon glyphicon-edit'></span>
										</a>");
								}
								echo("</li>");
						}
					} else {
						echo ("<li>Nenhum relato encontrado.</li>");
					}
					break;
				default:
					echo("Erro: Ação suspeita bloqueada!");
					break;
			}
		}
	}
}