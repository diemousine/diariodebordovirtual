<?php
/*
 * Esse arquivo contém todas as rotinas necessárias para:
 * - Cadastrar um novo usuário no banco de dados;
 * - Verificar as credenciais do usuário (login);
 * - Atualizar perfil do usuário;
 * - Recuperar senha do usuário;
 * - Remover as credenciais do usuário (logoff);
 */

// Inclui a página onde devem está as configurações de conexão ao banco de dados.
include_once '../../bdcon.php';

// Esta função retorna uma messagem em formato utf-8 legível;
function credencialErro($mensagem) {
	header('Content-Type: text/html; charset=utf-8');
	echo($mensagem);
}

/*
 * LOGIN DO USUÁRIO
 */
function login($email, $senha){

	// - Realiza a conexão com o banco de dados.
	$conn = bdcon();

	// - Reatribui as variáveis após remover possíveis caracteres que podem deixar o banco de dados vulnerável.
	$email = mysqli_real_escape_string($conn, $email);
	$senha = mysqli_real_escape_string($conn, $senha);

	/*
	 * Confere o email e a senha do usuário depois inicia uma sessão.
	 */

	$consulta = mysqli_query($conn, "SELECT idlogin FROM login WHERE email LIKE '$email' AND senha LIKE '$senha'");
	if(mysqli_num_rows($consulta) > 0) {
		$resultado = mysqli_fetch_array($consulta, MYSQLI_ASSOC);
		$idlogin = $resultado['idlogin'];
		$consulta = mysqli_query($conn, "SELECT * FROM usuario WHERE usidlogin = $idlogin");
		if(mysqli_affected_rows($conn) > 0) {
			$resultado = mysqli_fetch_array($consulta, MYSQLI_ASSOC);
			session_id(md5($idlogin."?pibid!".$email));
			session_start();
			setcookie('id', session_id(), 0, '/');
			$id = $resultado['idusuario'];
			date_default_timezone_set('America/Bahia');
			$data = date('Y-m-d H:i:s', time());
			$_SESSION['idusuario'] = $resultado['idusuario'];
			$_SESSION['email'] = $email;
			$_SESSION['host'] = $_SERVER['REMOTE_ADDR'];
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
			$_SESSION['ult_acesso'] = $resultado['ult_acesso'];
			mysqli_query($conn, "UPDATE usuario SET ult_acesso = '$data' WHERE idusuario = $id");
			header('Location: http://'.$_SERVER['HTTP_HOST']);
		} else {
			credencialErro('Erro ao tentar fazer login. Retorne e tente novamente.');
		}
	} else {
		credencialErro('Email ou senha inválido. Retorne e tente novamente. Caso tenha esquecido a senha use o link <strong>Esqueci a senha</strong>.');
	}
}

/*
 * CADASTRO DE USUÁRIO
 */
function cadastrar($email, $senha) {

	// - Realiza a conexão com o banco de dados.
	$conn = bdcon();

	// - Reatribui as variáveis após remover possíveis caracteres que podem deixar o banco de dados vulnerável.
	$email = mysqli_real_escape_string($conn, $email);
	$senha = mysqli_real_escape_string($conn, $senha);

	// - Realiza o cadastro.
	$consulta = mysqli_query($conn, "SELECT idlogin FROM login WHERE email LIKE '$email'");
	if(mysqli_num_rows($consulta) > 0) { 
		credencialErro('Email já cadastrado no sistema. Retorne e use o link <strong>Esqueci a senha</strong>.');
	} else {
		$consulta = mysqli_query($conn, "INSERT INTO login (email, senha) VALUES ('$email', '$senha')");
		if(mysqli_affected_rows($conn) > 0) {
			$consulta = mysqli_query($conn, "SELECT idlogin FROM login WHERE email LIKE '$email'");
			if(mysqli_num_rows($consulta) > 0) {
				$resultado = mysqli_fetch_array($consulta, MYSQLI_ASSOC);
				$idlogin = $resultado['idlogin'];
				$consulta = mysqli_query($conn, "INSERT INTO usuario (usidlogin) VALUES ('$idlogin')");
				if(mysqli_affected_rows($conn) > 0) {
					mysqli_close($conn);
					login($email, $senha);
				}
			}
		}
	}
}

/*
 * ATUALIZAR PERFIL
 */
function atualizarPerfil($nome, $vinculo, $instituicao, $coordenador, $local, $supervisor, $curso, $semestre) {

	// Verifica se o cookie id existe e inicia a sessão.
	if(isset($_COOKIE['id'])) {
		session_id(htmlspecialchars($_COOKIE['id']));
		session_start();

		// Verifica se o IP na sessão é igual ao IP dado na hora do login (para evitar cópia de cookies).
		if(isset($_SESSION['host']) && $_SESSION['host'] == $_SERVER['REMOTE_ADDR']) {
			$idusuario = $_SESSION['idusuario'];

			// - Realiza a conexão com o banco de dados.
			$conn = bdcon();

			// - Reatribui as variáveis após remover possíveis caracteres que podem deixar o banco de dados vulnerável.
			$nome = mysqli_real_escape_string($conn, $nome);
			$curso = mysqli_real_escape_string($conn, $curso);

			$autorizado = ($vinculo != $_SESSION['vinculo'] || $instituicao != $_SESSION['instituicao'] || $coordenador != $_SESSION['coordenador'] || $local != $_SESSION['local'] || $supervisor != $_SESSION['supervisor']) ? 0 : $_SESSION['autorizado'];

			// - Realiza a atualização dos dados.
			if($vinculo == 1) {
				$sqlquery = "UPDATE usuario SET nome = '$nome', vinculo = '$vinculo', instituicao = null, coordenador = $idusuario, local = null, supervisor = null, curso = null, semestre = null, autorizado = 1 WHERE idusuario = $idusuario";
			} else if($vinculo == 2) {
				$sqlquery = "UPDATE usuario SET nome = '$nome', vinculo = '$vinculo', instituicao = $instituicao, coordenador = $coordenador, local = $local, supervisor = $idusuario, curso = null, semestre = null, autorizado = $autorizado WHERE idusuario = $idusuario";
			} else {
				$sqlquery = "UPDATE usuario SET nome = '$nome', vinculo = '$vinculo', instituicao = $instituicao, coordenador = $coordenador, local = $local, supervisor = $supervisor, curso = '$curso', semestre = $semestre, autorizado = $autorizado WHERE idusuario = $idusuario";
			}

			mysqli_query($conn, $sqlquery);

			if(mysqli_affected_rows($conn) > 0) {
				$consulta = mysqli_query($conn, "SELECT * FROM usuario WHERE idusuario = $idusuario");
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
			}
		}
	}
}

/*
 * LOGOFF DO USUÁRIO
 */
function logoff() {
			
	// Verifica se o cookie id existe e inicia a sessão.
	if(isset($_COOKIE['id'])) {
		session_id(htmlspecialchars($_COOKIE['id']));
		session_start();

		// Verifica se o IP na sessão é igual ao IP dado na hora do login (para evitar cópia de cookies) e carrega o sistema.
		if(isset($_SESSION['host']) && $_SESSION['host'] == $_SERVER['REMOTE_ADDR']) {
			$id = $_SESSION['idus'];
			session_unset();
			session_destroy();
			setcookie('id', '', time(), '/');
		} else {
			setcookie('id', '', time(), '/');
		}
	}
	header('Location: http://'.$_SERVER['HTTP_HOST']);
}

/*
 * RECUPERAÇÃO DE SENHA
 */
function novaSenha($email) {
	$conn = bdcon();
	$email = mysqli_real_escape_string($conn, $email);
	$consulta = mysqli_query($conn, "SELECT * FROM login, usuario WHERE email LIKE '$email' AND idlogin = usidlogin");
	if(mysqli_affected_rows($conn) > 0) {
		$resultado = mysqli_fetch_array($consulta, MYSQLI_ASSOC);
		$senha = $resultado['senha'];
		$para = $resultado['nome']." <".$resultado['email'].">";
		$assunto = '=?UTF-8?B?'.base64_encode('Recuperação').'?= de Senha';
		$mensagem = "
		<html lang='pt_BR'>
		  <head>
		  </head>
		  <body>
			<p>Olá, ".$resultado['nome'].".</p>
			<p>Você está recebendo esta mensagem porque foi solicitada a recuperação de sua senha no site Diário de Bordo Virtual do PIBID.<br>
			<strong>Senha:</strong> ".$senha."<br>
			<p>Atenciosamente,</p>
			<p>Diário de Bordo Virtual.<br />
		  </body>
		</html>
		";
		$mensagem = wordwrap($mensagem, 70);

		$cabecalho = 'MIME-Version: 1.0' . "\r\n";
		$cabecalho .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
		$cabecalho .= 'From: =?UTF-8?B?'.base64_encode('Diário').'?= de Bordo Virtual <no-reply@dbvpibid.hol.es>' . "\r\n";

		mail($para, $assunto, $mensagem, $cabecalho);

		echo('<meta HTTP-EQUIV=\'refresh\' CONTENT=\'10; URL=../../\'>
			<p>A sua senha foi enviada para seu email.<br />
			Caso não encontre na caixa de entrada, verifique a caixa de spam ou lixo eletrônico.</p>
			<p>Aguarde. Você está sendo redirecionado para a página inicial.</p>');
	} else {
		die(credencialErro('Este email não faz parte do nosso sistema.'));
	}
}

/*
 * ROTINA PRINCIPAL
 */

// Mensagem passada por POST.
if(isset($_POST['ordem'])) {

	switch (htmlspecialchars($_POST['ordem'])) {
		case 'cadastrar':
			/* 
			 * Análise de segurança dos dados passados no formulário de cadastro:
			 * - Elimina caracteres especiais e atribui os valores às variáveis $usuario, $email e $senha.
			 */
			if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) { 
				$captcha = $_POST['g-recaptcha-response'];
				$response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']);
				$response = json_decode($response, true);
				if($response['success']) { 
					if(isset($_POST['email'], $_POST['senha'])) {
						if(filter_var($email = filter_var(htmlspecialchars($_POST['email']), FILTER_SANITIZE_EMAIL), FILTER_VALIDATE_EMAIL)===false) die(credencialErro('Email inválido. Retorne e tente novamente.'));
						$senha = (htmlspecialchars($_POST['senha'])===htmlspecialchars($_POST['r-senha']) && htmlspecialchars($_POST['senha']) !== "") ? htmlspecialchars($_POST['senha']) : die(credencialErro('Senha inválida. Retorne e tente novamente.'));
						if(!isset($_POST['termo'])) die(credencialErro('Você precisa aceitar os termos para se cadastrar. Retorne e tente novamente.'));

						// - Recorta as strings com a quantidade caracteres exigida no banco de dados.
						//$usuario = substr($usuario, 0, 15);
						$email = substr($email, 0, 128);
						$senha = substr($senha, 0, 16);

						cadastrar($email, $senha);
					} else {
							credencialErro('Erro nos dados de cadastro informados.');
					} 
				} else {
					credencialErro('Erro no recaptcha. Retorne e tente novamente.');
				} 
			} else {
				credencialErro('Recaptcha não encontrado. Retorne e tente novamente');
			}
			break;
		case 'login':

			/* 
			 * Análise de segurança dos dados passados no formulário de login:
			 * - Elimina caracteres especiais e atribui os valores às variáveis $email e $senha.
			 */

			if(isset($_POST['email'], $_POST['senha'])) {
				$email = htmlspecialchars($_POST['email']);
				$senha = htmlspecialchars($_POST['senha']);

				$email = substr($email, 0, 128);
				$senha = substr($senha, 0, 16);

				login($email, $senha);
			} else {
				credencialErro('Erro nos dados de login informados. ');
			}
			break;

		case 'atualizar':

			/* 
			 * Análise de segurança dos dados passados no formulário do perfil:
			 * - Elimina caracteres especiais e atribui os valores às variáveis $nome, $vinculo.
			 */

			if(($nome = substr(filter_var(trim(htmlspecialchars($_POST['nome'])), FILTER_SANITIZE_STRING), 0, 128)) == "" || $nome == " ") die(credencialErro('Você deixou o campo Nome vazio. Retorne e tente novamente.'));
			if(is_numeric($vinculo = htmlspecialchars($_POST['vinculo'])) == false || $vinculo == "") die(credencialErro('Você não selecionou um vínculo. Retorne e tente novamente.'));
			$instituicao = (is_numeric(htmlspecialchars($_POST['instituicao']))) ? $_POST['instituicao'] : NULL;
			$coordenador = (is_numeric(htmlspecialchars($_POST['coordenador']))) ? $_POST['coordenador'] : NULL;
			$local = (is_numeric(htmlspecialchars($_POST['local']))) ? $_POST['local'] : NULL;
			$supervisor = (is_numeric(htmlspecialchars($_POST['supervisor']))) ? $_POST['supervisor'] : NULL;
			$curso = substr(filter_var(trim(htmlspecialchars($_POST['curso'])), FILTER_SANITIZE_STRING), 0, 64);
			$semestre = (is_numeric(htmlspecialchars($_POST['semestre'])) && htmlspecialchars($_POST['semestre']) > 0 && htmlspecialchars($_POST['semestre']) <= 16 ) ? $_POST['semestre'] : 1;

			atualizarPerfil($nome, $vinculo, $instituicao, $coordenador, $local, $supervisor, $curso, $semestre);
			
			header('Location: http://'.$_SERVER['HTTP_HOST'].'/?ordem=ver-perfil&usuario='.$_SESSION['idusuario']);
			break;
		case 'amnesia':
			if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) { 
				$captcha = $_POST['g-recaptcha-response'];
				$response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6Lcx0AsTAAAAANIe2xCSjruapWuvc0QOYPupvOng&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']);
				$response = json_decode($response, true);
				if($response['success']) { 
					if(isset($_POST['email']) && !empty($_POST['email'])) {
						if(filter_var($email = filter_var(htmlspecialchars($_POST['email']), FILTER_SANITIZE_EMAIL), FILTER_VALIDATE_EMAIL)===false) die(credencialErro('Email inválido. Retorne e tente novamente.'));
						else novaSenha($email);
					}
				} else {
					credencialErro('Erro no recaptcha. Retorne e tente novamente.');
				} 
			} else {
				credencialErro('Recaptcha não encontrado. Retorne e tente novamente');
			}
			break;
		default:
			credencialErro('Não foi possível encontrar a ordem especificada no formulário. Retorne e tente novamente.');
			break;
	}
}

// Mensagem passada por GET.
if(isset($_GET['ordem'])) {

	switch (htmlspecialchars($_GET['ordem'])) {
		case 'logoff':
			logoff();
			break;
		default:
			credencialErro('Não foi possível encontrar a ordem especificada no formulário. Retorne e tente novamente.');
			break;
	}
}
?>