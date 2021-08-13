<?php
if(isset($_COOKIE['id'])) {
	session_id(htmlspecialchars($_COOKIE['id']));
	session_start();

	if($_SESSION['vinculo'] != 2) { die(); }	
	
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
				case 'autorizar':
					if(isset($_GET['usuario']) && is_numeric($_GET['usuario'])) {
						$usuario = htmlspecialchars($_GET['usuario']);
						mysqli_query($conn, "UPDATE usuario SET autorizado = 1 WHERE idusuario = $usuario AND supervisor = $id");
						if(mysqli_affected_rows($conn) > 0) {
							$row = mysqli_fetch_row(mysqli_query($conn, "SELECT nome, instituicao, local FROM usuario WHERE idusuario = $usuario"));
							$sqlquery = "O supervisor permitiu que <a href=\'?ordem=ver-perfil&usuario=".$usuario."\'><strong>".$row[0]."</strong></a> faça parte da equipe.";
							date_default_timezone_set('America/Bahia');
							$data = date('Y-m-d H:i:s', time());
							$instituicao = $row[1];
							$local = $row[2];
							$coordenador = $_SESSION['coordenador'];
							$idequipe = mysqli_fetch_array(mysqli_query($conn, "SELECT idinst_loc_coord FROM inst_loc_coord WHERE ilcinstituicao = $instituicao AND ilclocal = $local AND ilccoordenador = $coordenador"), MYSQLI_ASSOC);
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
						mysqli_query($conn, "UPDATE usuario SET vinculo = null, instituicao = null, coordenador = null, local = null, supervisor = null, autorizado = 0 WHERE idusuario = $usuario AND supervisor = $id");
						if(mysqli_affected_rows($conn) > 0) {
							$sqlquery = "O supervisor negou o acesso de <strong>".$row[0]."</strong> ao sistema.";
							date_default_timezone_set('America/Bahia');
							$data = date('Y-m-d H:i:s', time());
							$instituicao = $row[1];
							$local = $row[2];
							$coordenador = $_SESSION['coordenador'];
							$idequipe = mysqli_fetch_array(mysqli_query($conn, "SELECT idinst_loc_coord FROM inst_loc_coord WHERE ilcinstituicao = $instituicao AND ilclocal = $local AND ilccoordenador = $coordenador"));
							$idequipe = $idequipe['idinst_loc_coord'];
							mysqli_query($conn, "INSERT INTO historico(hdescricao, hdata, hequipe) VALUES ('$sqlquery', '$data', $idequipe)");
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