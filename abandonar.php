<?php
if(isset($_COOKIE['id'])) {
	session_id(htmlspecialchars($_COOKIE['id']));
	session_start();
	include_once 'bdcon.php';
	include_once 'cabecalho.php';
	include_once 'topo.php';

	if(isset($_POST['abandonar'])) {
		$abandonar = htmlspecialchars($_POST['abandonar']);
		if($abandonar) {
			$id = $_SESSION['idusuario'];
			$conn = bdcon();
			mysqli_query($conn, "UPDATE usuario SET vinculo = null, instituicao = null, coordenador = null, local = null, supervisor = null, autorizado = 0 WHERE idusuario = $id");
			if(mysqli_affected_rows($conn) > 0) {
				if($_SESSION['vinculo'] == 2) mysqli_query($conn, "UPDATE usuario SET vinculo = null, instituicao = null, coordenador = null, local = null, supervisor = null, autorizado = 0 WHERE supervisor = $id");
				if($_SESSION['vinculo'] == 1) {
					mysqli_query($conn, "UPDATE usuario SET vinculo = null, instituicao = null, coordenador = null, local = null, supervisor = null, autorizado = 0 WHERE coordenador = $id");
					mysqli_query($conn, "DELETE FROM inst_loc_coord WHERE ilccoordenador = $id");
					mysqli_query($conn, "DELETE FROM instituicao_coordenador WHERE iccoordenador = $id");
				}
				if($_SESSION['autorizado'] == 1) {
					$sqlquery = "<strong>".$_SESSION['nome']."</strong> saiu da equipe.";
					date_default_timezone_set('America/Bahia');
					$data = date('Y-m-d H:i:s', time());
					$instituicao = $_SESSION['instituicao'];
					$local = $_SESSION['local'];
					$coordenador = $_SESSION['coordenador'];
					$idequipe = mysqli_fetch_array(mysqli_query($conn, "SELECT idinst_loc_coord FROM inst_loc_coord WHERE ilcinstituicao = $instituicao AND ilclocal = $local AND ilccoordenador = $coordenador"), MYSQLI_ASSOC);
					$idequipe = $idequipe['idinst_loc_coord'];				
					mysqli_query($conn, "INSERT INTO historico(hdescricao, hdata, hequipe) VALUES('$sqlquery', '$data', $idequipe)");
				}
				$consulta = mysqli_query($conn, "SELECT * FROM usuario WHERE idusuario = $id");
				$resultado = mysqli_fetch_array($consulta, MYSQLI_ASSOC);
				$_SESSION['nome'] = $resultado['nome'];
				$_SESSION['vinculo'] = $resultado['vinculo'];
				$_SESSION['instituicao'] = $resultado['instituicao'];
				$_SESSION['local'] = $resultado['local'];
				$_SESSION['curso'] = $resultado['curso'];
				$_SESSION['semestre'] = $resultado['semestre'];
				$_SESSION['foto'] = $resultado['foto'];
				$_SESSION['coordenador'] = $resultado['coordenador'];
				$_SESSION['supervisor'] = $resultado['supervisor'];
				$_SESSION['autorizado'] = $resultado['autorizado'];			
				header('Location: http://'.$_SERVER['HTTP_HOST']);				
			}
		}
	} else if($_SESSION['vinculo'] == 1) {
		echo ("
			<div class='col-sm-12'>
				<center>
					<div class='alert alert-warning'>
						<strong>Atenção!</strong> Essa ação irá remover você da coordenação da equipe atual e impedir que TODOS da equipe utilize o sistema até fazer parte de uma nova equipe.
					</div>
					<form method='post'>
						<input name='abandonar' value='true' hidden />
						<button class='btn btn-primary' type='submit'>Confirmar</button>
						<a class='btn btn-default' href='http://".$_SERVER['HTTP_HOST']."'>Cancelar</a>
					</form>
				</center>
			</div>
		");
	} else if($_SESSION['vinculo'] == 2) {
		echo ("
			<div class='col-sm-12'>
				<center>
					<div class='alert alert-warning'>
						<strong>Atenção!</strong> Essa ação irá remover você da supervisão e TODOS os seus bolsistas da equipe. E vocês serão impedidos de utilizar o sistema até fazer parte de uma nova equipe.
					</div>
					<form method='post'>
						<input name='abandonar' value='true' hidden />
						<button class='btn btn-primary' type='submit'>Confirmar</button>
						<a class='btn btn-default' href='http://".$_SERVER['HTTP_HOST']."'>Cancelar</a>
					</form>
				</center>
			</div>
		");
	} else if($_SESSION['vinculo'] == 3) {
		echo ("
			<div class='col-sm-12'>
				<center>
					<div class='alert alert-warning'>
						<strong>Atenção!</strong> Essa ação irá remover você da equipe atual e você será impedido de utilizar o sistema até fazer parte de uma nova equipe.
					</div>
					<form method='post'>
						<input name='abandonar' value='true' hidden />
						<button class='btn btn-primary' type='submit'>Confirmar</button>
						<a class='btn btn-default' href='http://".$_SERVER['HTTP_HOST']."'>Cancelar</a>
					</form>
				</center>
			</div>
		");
	} 
} else include_once 'cabecalho.php';

include_once 'rodape.php';
?>