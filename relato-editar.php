<?php
if(isset($_COOKIE['id'])) {
	session_id(htmlspecialchars($_COOKIE['id']));
	session_start();
	
	if(isset($_SESSION['host']) && $_SESSION['host'] == $_SERVER['REMOTE_ADDR']) {
		$id = $_SESSION['idusuario'];

		include_once 'bdcon.php';
		$conn = bdcon();

		if (isset($_GET['id']) && is_numeric($_GET['id'])) {
			date_default_timezone_set('America/Bahia');
			$data = date('Y-m-d', time());
			$idrelato = htmlspecialchars($_GET['id']);
			$consulta = mysqli_query($conn, "SELECT * FROM relato WHERE idrelato = $idrelato AND relusuario = $id AND MONTH(reldata) = MONTH('$data')");
			if(mysqli_affected_rows($conn) > 0) {
				$resultado = mysqli_fetch_array($consulta);
				if($resultado['relcoordenador'] == $_SESSION['coordenador'] && $resultado['relinstituicao'] == $_SESSION['instituicao'] && $resultado['rellocal'] == $_SESSION['local']) {
					echo ("
					<form role='form' id='form'>
						<input id='tipo' name='tipo' type='text' value='editado' hidden />
						<input id='relato' name='relato' type='text' value='".$idrelato."' hidden />
						<ul class='list-group'>
							<li class='list-group-item'><label>Data:</label> ".date('d/m/Y', strtotime($resultado['reldata']))."</li>
							<li class='list-group-item'><label>Título:</label> ".$resultado['reltitulo']."</li>
							<li class='list-group-item'><textarea id='dados' class='form-control' rows=10 placeholder='Relato...' required>".$resultado['relato']."</textarea></li>
						</ul>
					</form>
					");
				} else {
					echo("Erro: Você não pode mais editar este relato.");
				}
			} else { echo("Erro: Ação suspeita bloqueada!"); }
		}
	}
}
?>