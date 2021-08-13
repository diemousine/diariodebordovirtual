// Author: Diego Socrates Dias Mousine
// JavaScript Document
// Este código foi escrito assim de propósito.

function coordListar(tipo) {
	$.get("coordenador_control.php", { ordem: "listar", tipo: tipo }, function(result) { if(result != "") $('#'+tipo+'-body').html(result); });
}
function permitir(usuario) {
	(confirm("Tem certeza que deseja PERMITIR que o usuário faça parte da equipe?")) ? $.get("coordenador_control.php", 
		{ ordem: "autorizar", usuario: usuario }, 
		function(result) { 
			if(result == 1) { 
				$('#li'+usuario).html("<span>Usuário permitido.</span>");
			}
		}) : "";
}
function negar(usuario) {
	(confirm("Tem certeza que deseja NEGAR que o usuário faça parte da equipe?")) ? $.get("coordenador_control.php", 
		{ ordem: "negar", usuario: usuario }, 
		function(result) { 
			if(result == 1) { 
				$('#li'+usuario).html("<span>Usuário negado.</span>");
			}
		}) : "";
}

function remInst(id) {
	(confirm("Tem certeza que deseja REMOVER esta Instituição da sua lista?")) ? $.get("coordenador_control.php", 
		{ ordem: "remover", instituicao: id }, 
		function(result) { 
			if(result == 1) { 
				$('#instituicao'+id).html("<span>Instituição removida.</span>"); 
			} else if(result == 0) { 
				$('#modal-body').html("A Instituição não pode ser removida, por questão de segurança.<br />Abandone a equipe para remover."); 
				$('#coordSalvar').hide(); 
				$('#cancelar').html('Fechar');
				$('#modalCoord').modal('show'); 
			} else { 
				$('#modal-body').html("Erro: A Instituição não pode ser removida."); 
				$('#coordSalvar').hide(); 
				$('#cancelar').html('Fechar');
				$('#modalCoord').modal('show'); 
			}
		}) : "";
}

$('#addInstituicao').click( function() { 
	$('#selInstituicao-body').hide();
	$('#selInstituicao').removeAttr("disabled");
	$('#modal-body').html('carregando...'); 
	$('#modal-body').load('coordenador_control.php?ordem=novo&tipo=instituicao&tamanho=128'); 
	$('#coordSalvar').show(); 
	$('#cancelar').html('Cancelar'); 
	$('#modalCoord').modal('show'); 
});

$('#selInstituicao').click( function() { 
	$('#dadosInst').html('<option>Carregando...</option>');
	$('#dadosInst').load('coordenador_control.php?ordem=listar&tipo=selInstituicao', function(result) {
		if(result != '') {
			$('#selInstituicao-body').show();
			$('#selInstituicao').attr("disabled","");
		} else {
			alert("Você já tem vínculo com uma Instituição.\nRemova o vínculo para poder selecionar nova Instituição.")
			$('#selInstituicao-body').hide();
		}
	}); 
});

$("#atualizarInst").click( function() { 
	$.get("coordenador_control.php", { ordem: "atualizarInst", dados: $("#dadosInst").val() }, function(result) { if(result == 1) coordListar("instituicao"); });
});

$('#addLocal').click( function() { 
	$('#modal-body').html('carregando...'); 
	$('#modal-body').load('coordenador_control.php?ordem=novo&tipo=local&tamanho=128'); 
	$('#coordSalvar').show(); 
	$('#cancelar').html('Cancelar'); 
	$('#modalCoord').modal('show'); 
});

$('#addVinculo').click( function() { 
	$('#modal-body').html('carregando...'); 
	$('#modal-body').load('coordenador_control.php?ordem=novo&tipo=vinculo&tamanho=0'); 
	$('#coordSalvar').show(); 
	$('#cancelar').html('Cancelar'); 
	$('#modalCoord').modal('show'); 
});

$('#coordSalvar').click( function() { 
	$('#modal-body').load('coordenador_control.php?ordem=salvar&'+$('#form').serialize()); 
	$('#coordSalvar').hide(); 
	$('#cancelar').html('Fechar');
	coordListar($("#tipo").val());
});