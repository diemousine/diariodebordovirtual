<?php
if(isset($_COOKIE['id'])) {
	session_id(htmlspecialchars($_COOKIE['id']));
	session_start();
	
	if(isset($_SESSION['host']) && $_SESSION['host'] == $_SERVER['REMOTE_ADDR']) {
		$id = $_SESSION['idusuario'];

		include_once 'bdcon.php';
		$conn = bdcon();

		if(isset($_GET['id']) && is_numeric($_GET['id'])) {
			$idrel = htmlspecialchars($_GET['id']);
			$consulta = mysqli_query($conn, "SELECT * FROM relato, instituicao, local, usuario WHERE idrelato = $idrel AND relinstituicao = idinstituicao AND rellocal = idlocal AND relusuario = idusuario");
			$resultado = mysqli_fetch_array($consulta, MYSQLI_ASSOC);
			if(mysqli_affected_rows($conn) > 0 && ($resultado['idusuario'] == $id || $resultado['coordenador'] == $_SESSION['coordenador'])) {
				date_default_timezone_set('America/Bahia');
				echo("
				<ul class='list-group'>
					<li class='list-group-item'><label>Instituição:</label> ".$resultado['instdescricao']."</li>
					<li class='list-group-item'><label>Local:</label> ".$resultado['locdescricao']."</li>
					<li class='list-group-item'><label>Autor:</label> ".$resultado['nome']."</li>
					<li class='list-group-item'><label>Data:</label> ".date('d/m/Y', strtotime($resultado['reldata']))."</li>
					<li class='list-group-item'><label>Título:</label> ".$resultado['reltitulo']."</li>
					<li class='list-group-item'><p style='word-wrap: break-word; text-align: justify; text-justify: inter-word'>".nl2br($resultado['relato'])."</p></li>	
				</ul>
				");
			} else {
				echo('Erro: Ação suspeita bloqueada!');
			}
		}
	}
}