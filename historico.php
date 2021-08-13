<?php
$conn = bdcon();
$instituicao = $_SESSION['instituicao'];
$local = $_SESSION['local'];
$coordenador = $_SESSION['coordenador'];
if($_SESSION['vinculo'] == 1) $sqlquery = "SELECT idinst_loc_coord FROM inst_loc_coord WHERE ilccoordenador = $coordenador";
else $sqlquery = "SELECT idinst_loc_coord FROM inst_loc_coord WHERE ilcinstituicao = $instituicao AND ilclocal = $local AND ilccoordenador = $coordenador";
$idequipe = mysqli_query($conn, $sqlquery);
echo("
	<div class='col-sm-12'>
");
$ultacesso = $_SESSION['ult_acesso'];
while($row = mysqli_fetch_row($idequipe)){
	echo "<ul class='list-group'>";
	$consulta = mysqli_query($conn, "SELECT * FROM historico WHERE hdata > '$ultacesso' AND hequipe = $row[0] ORDER BY idhistorico DESC");
	if(mysqli_affected_rows($conn) > 0){
		echo "<li class='list-group-item active'>O que aconteceu desde o seu último acesso.</li>";
		while($row = mysqli_fetch_row($consulta)){
			echo("<li class='list-group-item'>".$row[1]."</li>");
		}
	} else {
		echo("<li class='list-group-item'>Nenhum item novo desta equipe.</li>");
	}
	echo "</ul>";
}
echo("
	</div>
");
?>