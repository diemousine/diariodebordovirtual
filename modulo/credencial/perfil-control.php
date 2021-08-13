<?php
if(isset($_COOKIE['id'])) {
	session_id(htmlspecialchars($_COOKIE['id']));
	session_start();

	// Verifica se o IP na sessão é igual ao IP dado na hora do login (para evitar cópia de cookies) e carrega o sistema.
	if(isset($_SESSION['host']) && $_SESSION['host'] == $_SERVER['REMOTE_ADDR']) {
		$id = $_SESSION['idusuario'];
		include "../../bdcon.php";

		$conn = bdcon();

		if(isset($_GET['ordem'])) {
			switch (htmlspecialchars($_GET['ordem'])) {
				case 'listar':
					echo("<option value=''>SELECIONE</option>");
					if(isset($_GET['tipo'])) {
						switch(htmlspecialchars($_GET['tipo'])) {
							case 'coordenador':
								if(isset($_GET['instituicao']) && is_numeric($_GET['instituicao'])) {
									$instituicao = $_GET['instituicao'];
									$consulta = mysqli_query($conn, "SELECT idusuario, nome FROM usuario, instituicao_coordenador WHERE icinstituicao = $instituicao AND idusuario = iccoordenador AND iccoordenador != $id");
									if(mysqli_affected_rows($conn) > 0) {
										while($row = mysqli_fetch_row($consulta)) {
											echo("<option value='".$row[0]."'>".$row[1]."</option>");
										}
									}
								}
								break;
							case 'local':
								if(isset($_GET['instituicao'], $_GET['coordenador']) && is_numeric($_GET['instituicao']) && is_numeric($_GET['coordenador'])) {
									$instituicao = $_GET['instituicao'];
									$coordenador = $_GET['coordenador'];
									$consulta = mysqli_query($conn, "SELECT idlocal, locdescricao FROM local, inst_loc_coord WHERE idlocal = ilclocal AND ilcinstituicao = $instituicao AND ilccoordenador = $coordenador AND ilccoordenador != $id");
									if(mysqli_affected_rows($conn) > 0) {
										while($row = mysqli_fetch_row($consulta)) {
											echo("<option value='".$row[0]."'>".$row[1]."</option>");
										}
									}
								}
								break;
							case 'supervisor':
								if(isset($_GET['instituicao'], $_GET['coordenador'], $_GET['local']) && is_numeric($_GET['instituicao']) && is_numeric($_GET['coordenador']) && is_numeric($_GET['local'])) {
									$instituicao = $_GET['instituicao'];
									$coordenador = $_GET['coordenador'];
									$local = $_GET['local'];
									$supervisor = (empty($_SESSION['supervisor'])) ? 0 : $_SESSION['supervisor'];
									$consulta = mysqli_query($conn, "SELECT idusuario, nome FROM usuario WHERE instituicao = $instituicao AND coordenador = $coordenador AND vinculo = 2 AND local = $local AND autorizado = 1 AND idusuario != $supervisor AND idusuario != $id");
									if(mysqli_affected_rows($conn) > 0) {
										while($row = mysqli_fetch_row($consulta)) {
											echo("<option value='".$row[0]."'>".$row[1]."</option>");
										}
									}
								}
								break;
							default:
								break;
						}
					}
					break;
				case 'salvar':
					if(isset($_GET['tipo'])) {
						switch (htmlspecialchars($_GET['tipo'])) {
							case 'n-senha':
									if(isset($_GET['novasenha'], $_GET['r-nsenha'], $_GET['senha'])) {
										if(($nsenha = mysqli_real_escape_string($conn, filter_var(htmlspecialchars(trim($_GET['novasenha'])), FILTER_SANITIZE_STRING))) == "") die("Erro: A nova senha é inválida.");
										if(($rnsenha = filter_var(htmlspecialchars(trim($_GET['r-nsenha'])), FILTER_SANITIZE_STRING)) != $nsenha) die ("Erro: você digitou a nova senha duas vezes diferente.");
										$senha = mysqli_real_escape_string($conn, filter_var(htmlspecialchars(trim($_GET['senha'])), FILTER_SANITIZE_STRING));
										$email = $_SESSION['email'];
										mysqli_query($conn, "UPDATE login SET senha = '$nsenha' WHERE email = '$email' AND senha = '$senha'");
										if(mysqli_affected_rows($conn) > 0) {
											echo("Senha alterada com sucesso.");
										} else {
											echo("Erro: senha atual não reconhecida.");
										}
									}
								break;
							case 'novoNome':
								if(isset($_GET['novoNome'])){
									if(($nNome = mysqli_real_escape_string($conn, filter_var(htmlspecialchars(trim($_GET['novoNome'])), FILTER_SANITIZE_STRING))) == "") die("Erro: O nome é inválido.");
									mysqli_query($conn, "UPDATE usuario SET nome = '$nNome' WHERE idusuario = $id");
									if(mysqli_affected_rows($conn) > 0){
										$_SESSION['nome'] = $nNome;
										echo("Nome alterado com sucesso. Atualize a página para ver a alteração.");
									} else {
										echo("Erro: o banco de dados não aceitou o nome digitado.");
									}
								}
								break;
							case 'curso':
								if($_SESSION['vinculo'] < 3) die("Você não precisa alterar essas informações.");
								if(isset($_GET['curso'], $_GET['semestre']) && is_numeric($_GET['semestre'])) {
									$curso = mysqli_real_escape_string($conn, filter_var(htmlspecialchars(trim($_GET['curso'])), FILTER_SANITIZE_STRING));
									$semestre = $_GET['semestre'];
									mysqli_query($conn, "UPDATE usuario SET curso = '$curso', semestre = $semestre WHERE idusuario = $id");
									if(mysqli_affected_rows($conn) > 0) {
										$_SESSION['curso'] = $curso;
										$_SESSION['semestre'] = $semestre;
										echo("Dados do curso alterados com sucesso.");
									} else {
										echo("Erro: o banco de dados não aceitou os dados do curso fornecidos.");
									}
								}
								break;
							default:
								echo('Erro. Tipo desconhecido.');
								break;
						}
					}
					break;
				default:
					echo('Erro: Ordem desconhecida.');
					break;
			}
		} else if(isset($_POST['ordem'])) {
			switch (htmlspecialchars($_POST['ordem'])) {
				case 'imagem':
					if(isset($_FILES['imagem'])) {
						$x = $_FILES['imagem'];
						if(!empty($x['name'])) {
							if(!preg_match("/^image\/(pjpeg|jpeg)$/", htmlspecialchars($x['type']))) die('A imagem precisa ser uma JPEG');
							if(htmlspecialchars($x['size']) > 1048576) die('Tamanho do arquivo acima do permitido: 1 MB');
							preg_match("/\.(jpg|jpeg){1}$/i", $x['name'], $ext);
							$nome_imagem = md5('p1e2r3f4i5l'.$id) . '.' . $ext[1];
							$caminho_imagem = 'img-perfil/' . $nome_imagem;
							move_uploaded_file($x['tmp_name'], $caminho_imagem);
							$img = 'img-perfil/' . $nome_imagem;
							$imgSize = getimagesize($img);
							$source = imagecreatefromjpeg($img);
							$percent = ($imgSize[0] > $imgSize[1]) ? (20000/$imgSize[0])/100 : (18500/$imgSize[1])/100;
							$newLarg = ($imgSize[0]*$percent);
							$newAltu = ($imgSize[1]*$percent);
							$thumb = imagecreatetruecolor($newLarg, $newAltu);
							imagecopyresized($thumb, $source, 0, 0, 0, 0, $newLarg, $newAltu, $imgSize[0], $imgSize[1]);
							imagejpeg($thumb, $img);
							$_SESSION['foto'] = htmlspecialchars($nome_imagem);
						} else {
							$_SESSION['foto'] = null;
						}
						$imagem = mysqli_real_escape_string($conn, $_SESSION['foto']);
						mysqli_query($conn, "UPDATE usuario SET foto = '$imagem' WHERE idusuario = $id");
						if(mysqli_affected_rows($conn) >= 0) {
							echo("
								<script language='javascript'>
									function refreshParent() {
									    window.opener.location.reload();
									}
									window.onunload = refreshParent;
									window.close();
								</script>
								");
						} else { die(mysqli_error($conn)); }
					}
					break;
				default:
					break;
			}
		}
	}
}
?>