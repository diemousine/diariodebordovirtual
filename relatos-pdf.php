<?php
// Esta função estabelece uma conexão com o bando de dados;
function bdcon() {

	// Create connection
	if ($_SERVER['HTTP_HOST'] == 'dbvpibid.mousine.com.br') {
		$conn = mysqli_connect('mousine.com.br', 'mousinec_usr', 'user.123', 'mousinec_dbv');
	} else { $conn = mysqli_connect('localhost', 'root', '', 'dbv'); }
	// Check connection
	if (!$conn) {
    	die("Ihhhhhh bugou: " . mysqli_connect_error());
	}

	mysqli_set_charset($conn, 'utf8');
	return $conn;
}

if(isset($_COOKIE['id'])) {
	session_id(htmlspecialchars($_COOKIE['id']));
	session_start();
	// Verifica se o IP na sessão é igual ao IP dado na hora do login (para evitar cópia de cookies) e carrega o sistema.
	if(isset($_SESSION['host'], $_GET['idloc_inst']) && $_SESSION['host'] == $_SERVER['REMOTE_ADDR'] && is_numeric($_GET['idloc_inst'])) {

		if(isset($_GET['inireldata'], $_GET['fimreldata'])) {
			$idloc_inst = $_GET['idloc_inst'];
			$id = $_SESSION['idusuario'];
			$coordenador = $_SESSION['coordenador'];
			$conn = bdcon();
			$inireldata = preg_replace("([^0-9-])", "", $_GET['inireldata']);
			$fimreldata = preg_replace("([^0-9-])", "", $_GET['fimreldata']);

			// Include the main TCPDF library (search for installation path).
			require_once('./tcpdf/tcpdf.php');

			// create new PDF document
			$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

			// set document information
			$pdf->SetCreator(PDF_CREATOR);
			$pdf->SetAuthor('Diário de Bordo Virtual');
			$pdf->SetTitle('Relatos');
			$pdf->SetSubject('Relatos');

			// set default header data
			$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'DIÁRIO DE BORDO VIRTUAL', 'RELATOS DA EQUIPE', array(0,64,255), array(0,64,128));
			$pdf->setFooterData(array(0,64,0), array(0,64,128));

			// set header and footer fonts
			$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
			$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

			// set default monospaced font
			$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

			// set margins
			$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
			$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
			$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

			// set auto page breaks
			$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

			// set image scale factor
			$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


			// ---------------------------------------------------------

			// set default font subsetting mode
			$pdf->setFontSubsetting(true);

			// Set font
			// helvetica or times to reduce file size.
			$pdf->SetFont('times', '', 12, '', true);

			// Add a page
			// This method has several options, check the source code documentation for more information.
			$pdf->AddPage();

			// set text shadow effect
			// $pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

			// Consulta os dados da instituição e local de atuação
			$il = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM inst_loc_coord WHERE idinst_loc_coord = $idloc_inst"));
			$illocal = $il['ilclocal'];
			$ilinstituicao = $il['ilcinstituicao'];
			$ilc = mysqli_fetch_array(mysqli_query($conn, "SELECT instdescricao, locdescricao, nome FROM instituicao, local, usuario, inst_loc_coord WHERE idinst_loc_coord = $idloc_inst AND idinstituicao = inst_loc_coord.ilcinstituicao AND idlocal = inst_loc_coord.ilclocal AND idusuario = $coordenador"));

			// Set some content to print			
			$html = "
			<strong>PROGRAMA INSTITUCIONAL DE BOLSAS DE INICIAÇÃO A DOCÊNCIA</strong><br />
			<strong>Instituição:</strong> ".$ilc['instdescricao']."<br />
			<strong>Local de atuação:</strong> ".$ilc['locdescricao']."<br />
			<strong>Coordenador(a):</strong> ".$ilc['nome']."<br />
			";
			if($_SESSION['vinculo'] == 1) $sqlquery = "SELECT idusuario, nome, vindescricao FROM usuario, vinculo WHERE instituicao = $ilinstituicao AND local = $illocal AND coordenador = $coordenador AND vinculo = idvinculo AND autorizado = 1 ORDER BY vinculo ASC, nome ASC";
			if($_SESSION['vinculo'] == 2) $sqlquery = "SELECT idusuario, nome, vindescricao FROM usuario, vinculo WHERE instituicao = $ilinstituicao AND local = $illocal AND coordenador = $coordenador AND supervisor = $id AND vinculo = idvinculo AND idvinculo = 3 AND autorizado = 1 ORDER BY nome ASC";

			$usuario = mysqli_query($conn, $sqlquery);

			if(mysqli_affected_rows($conn) > 0) {
				while ($row = mysqli_fetch_row($usuario)) {
					$html .= "
					<hr>
					<strong>Autor:</strong> ".$row[1]."<br />
					<strong>Vinculo:</strong> ".$row[2]."<br />
					<br />
					<table>
					";
					$idusuario = $row[0];
					if($_SESSION['vinculo'] == 1) $sqlquery = "SELECT * FROM relato WHERE relcoordenador = $coordenador AND relusuario = $idusuario AND relinstituicao = $ilinstituicao AND rellocal = $illocal AND reldata >= '$inireldata' AND reldata <= '$fimreldata'";
					if($_SESSION['vinculo'] == 2) $sqlquery = "SELECT * FROM relato WHERE relcoordenador = $coordenador AND relsupervisor = $id AND relusuario = $idusuario AND relinstituicao = $ilinstituicao AND rellocal = $illocal AND reldata >= '$inireldata' AND reldata <= '$fimreldata'";

					$relato = mysqli_query($conn, $sqlquery);
					if(mysqli_affected_rows($conn) > 0) {
						while($row = mysqli_fetch_row($relato)) {
							$html .= "
							<tr><td>
								<strong>Título:</strong> ".$row[7]."
							</td>
							<td>
								<strong>Data:</strong> ".date('d-m-Y', strtotime($row[6]))."
							</td></tr>
							<tr><td colspan=\"2\" style=\"text-align: justify; text-justify: inter-word\">
								".nl2br($row[8])."
							</td></tr><tr><td></td></tr>
							";
						}
					} else {
						$html .= "<tr><td>Nenhum relato encontrado para o período informado.</td></tr>";
					}
					$html .= "</table><br />";
				}
			} else {
				$html .= "Nenhum relato encontrado.";
			}

			// Print text using writeHTMLCell()
			$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

			// ---------------------------------------------------------

			// Close and output PDF document
			// This method has several options, check the source code documentation for more information.
			$pdf->Output('relatos.pdf', 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		} else {
			include_once 'cabecalho.php';
			echo("
				<span>SELECIONE UM PERÍODO</span>
				<form method='get' class='form-inline'>
					<input type='text' value='".$_GET['idloc_inst']."' id='idloc_inst' name='idloc_inst' hidden='hidden' />
					<div class='form-group'>
						<label>De:</label>
						<input type='date' class='form-control' id='inireldata' name='inireldata' />
					</div>
					<div class='form-group'>
						<label>Até:</label>
						<input type='date' class='form-control' id='fimreldata' name='fimreldata' />
					</div>
					<button type='submit' class='btn btn-default'>OK</button>
				</form>
				");
			include_once 'rodape.php';
		}
	}
}
?>