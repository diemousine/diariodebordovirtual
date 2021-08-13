<?php

// Verifica se o cookie id existe e inicia a sessão.

if(isset($_COOKIE['id'])) {

	session_id(htmlspecialchars($_COOKIE['id']));

	session_start();

	include_once 'cabecalho.php';


	// Verifica se o IP na sessão é igual ao IP dado na hora do login (para evitar cópia de cookies) e carrega o sistema.

	if(isset($_SESSION['host']) && $_SESSION['host'] == $_SERVER['REMOTE_ADDR']) {

		$id = $_SESSION['idusuario'];



		include_once 'bdcon.php';

		include_once 'topo.php';

		// Verifica se o cadastro está completo e redireciona o usuário para a página de perfil caso não esteja completo.



		if($_SESSION['vinculo'] == null) {

			include_once './modulo/credencial/incompleto.php';

			include_once './modulo/credencial/perfil.php';

		} else if($_SESSION['autorizado'] == '0') {

			echo ("

				<center>

					<div class='alert alert-info'>

						Aguardando autorização para utilizar o sistema.

					</div>

					<div class='alert alert-warning'>

						<a class='alert-link' href='abandonar.php'>

							Clique aqui para cancelar a solicitação.

						</a>

					</div>

				</center>

			");

		} else if(isset($_GET['ordem'])) {

			switch (htmlspecialchars($_GET['ordem'])) {

				case 'ver-perfil':

					include_once './modulo/credencial/ver-perfil.php';

					break;

				case 'equipe':

					if($_SESSION['vinculo'] == 1) include_once 'coord-equipe.php';

					else include_once 'equipe.php';

					break;

				case 'relato':

					include_once 'relato.php';

					break;

				case 'historico':

					include_once 'historico.php';

					break;

				case 'portaria':

					include_once 'portaria-view.php';

					break;

				default:

					break;

			}

		} else {

			include_once 'lobby.php';

		}

		echo "</div>"; // Fecha a div "Conteudo-principal" em topo.php

	} else {

		echo("Você está usando um cookie inválido. Clique <a href='./modulo/credencial/?ordem=logoff'><strong>aqui</strong></a> para limpar os cookies.");

	}

} else {

	include_once 'cabecalho.php';


	include_once 'credencial.php';

}

include_once 'rodape.php';

?>