<?php
if(isset($_COOKIE['id'])) {
	session_id(htmlspecialchars($_COOKIE['id']));
	session_start();
	
	if(isset($_SESSION['host']) && $_SESSION['host'] == $_SERVER['REMOTE_ADDR']) {
		$id = $_SESSION['idusuario'];

		include_once 'bdcon.php';
		$conn = bdcon();
		date_default_timezone_set('America/Bahia');
		$data = date('Y-m-d', time());
		mysqli_query($conn, "SELECT * FROM relato WHERE reldata = '$data' AND relusuario = $id");
		if(mysqli_affected_rows($conn) == 0) {
			echo ("
			<form role='form' id='form'>
				<input id='tipo' name='tipo' type='text' value='relato' hidden />
			");
			if($_SESSION['vinculo'] == 1) {
				echo("
				<div class='form-group'>
		    		<select id='instituicao' name='instituicao' class='form-control' required>
		    			<option value=''>INSTITUIÇÃO VINCULADA AO PIBID...</option>");
						$consulta = mysqli_query($conn, "SELECT idinstituicao, instdescricao FROM instituicao, instituicao_coordenador WHERE idinstituicao = icinstituicao AND iccoordenador = $id ORDER BY instdescricao ASC");
		    			while($row = mysqli_fetch_row($consulta)){
		    				echo("<option value='".$row[0]."'>".$row[1]."</option>");
		    			}
		    		echo ("</select>
				</div>
				<div class='form-group'>
		    		<select id='local' name='local' class='form-control' required>
		    			<option value=''>LOCAL DE ATUAÇÃO...</option>");
						$consulta = mysqli_query($conn, "SELECT * FROM local ORDER BY locdescricao ASC");
		    			while($row = mysqli_fetch_row($consulta)){
		    				echo("<option value='".$row[0]."'>".$row[1]."</option>");
		    			}
		    		echo ("</select>
				</div>");
	    	}
	    	echo("
				<div class='form-group'>
					<input id='titulo' name='titulo' type='text' class='form-control' placeholder='Título' maxlength=64></textarea>
				</div>
				<div class='form-group'>
					<textarea id='dados' class='form-control' rows=10 placeholder='Relato...' required></textarea>
				</div>
			</form>
			");
	    } else {
	    	echo("Você já tem um relatório referente à data de hoje. Não é possível adicionar um novo.");
	    }
	}
}
?>