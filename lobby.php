<?php
$conn = bdcon();
$instituicao = $_SESSION['instituicao'];
$local = $_SESSION['local'];
$coordenador = $_SESSION['coordenador'];
if($_SESSION['vinculo'] == 1) $sqlquery = "SELECT idinst_loc_coord FROM inst_loc_coord WHERE ilccoordenador = $coordenador";
else $sqlquery = "SELECT idinst_loc_coord FROM inst_loc_coord WHERE ilcinstituicao = $instituicao AND ilclocal = $local AND ilccoordenador = $coordenador";
$idequipe = mysqli_fetch_array(mysqli_query($conn, $sqlquery));
$ultacesso = $_SESSION['ult_acesso'];
$consulta = @mysqli_fetch_array(mysqli_query($conn, "SELECT COUNT(idhistorico) FROM historico WHERE hdata > '$ultacesso' AND hequipe = $idequipe[0] ORDER BY idhistorico DESC"));
echo("
<div class='row'>
	<div class='col-sm-2'></div>
	<div class='col-sm-8 text-center' >
		<div class='form-group'>
			<a class='btn btn-default' style='width: 120px; height: 120px;' href='http://".$_SERVER['HTTP_HOST']."/?ordem=ver-perfil&usuario=".$_SESSION['idusuario']."' title='Perfil'>
				<div style='overflow: hidden;'>
					<img src='http://".$_SERVER['HTTP_HOST'].(($_SESSION['foto'] == "") ? '/modulo/credencial/img-perfil/nenhuma.png' : '/modulo/credencial/img-perfil/'.$_SESSION['foto'])."' width='70px' />
				</div>
				<div>PERFIL</div>
			</a>
			<a class='btn btn-default' style='width: 120px; height: 120px;' href='http://".$_SERVER['HTTP_HOST']."?ordem=historico' title='Histórico'>
				<div style='position: absolute;'><span class='badge'>$consulta[0]</span></div>
				<div style='overflow: hidden;'>
					<h1><span class='glyphicon glyphicon-inbox'></span></h1>
				</div>
				<div>HISTÓRICO</div>
			</a>
		</div>
		<div class='form-group'>
			<a class='btn btn-default' style='width: 120px; height: 120px;' href='http://".$_SERVER['HTTP_HOST']."?ordem=equipe' title='Equipe'>
				<div style='overflow: hidden;'>
					<h1><span class='glyphicon glyphicon-list-alt'></span></h1>
				</div>
				<div>EQUIPE</div>
			</a>
			<a class='btn btn-default' style='width: 120px; height: 120px;' href='http://".$_SERVER['HTTP_HOST']."?ordem=relato' title='Relatos'>
				<div style='overflow: hidden;'>
					<h1><span class='glyphicon glyphicon-folder-open'></span></h1>
				</div>
				<div>RELATOS</div>
			</a>
		</div>
		<div class='form-group'>
");
if($_SESSION['vinculo'] < 3) {
	echo("
			<a class='btn btn-primary' style='width: 120px; height: 120px;' href='http://".$_SERVER['HTTP_HOST']."?ordem=portaria' title='Administração'>
				<div style='overflow: hidden;'>
					<h1><span class='glyphicon glyphicon-wrench'></span></h1>
				</div>
				<div>ADMIN</div>
			</a>
	");
}
echo("
			<a class='btn btn-default' style='width: 120px; height: 120px;' href='http://".$_SERVER['HTTP_HOST']."/modulo/credencial/?ordem=logoff' title='Sair'>
				<div style='overflow: hidden;'>
					<h1><span class='glyphicon glyphicon-log-out'></span></h1>
				</div>
				<div>SAIR</div>
			</a>
		</div>
	</div>
	<div class='col-sm-2'></div>
</div>
");
?>