// Author: Diego Socrates Dias Mousine
// JavaScript Document
// Este código foi escrito assim de propósito.

function relListar(tipo) {
	$.get("relato_control.php", { ordem: "listar", tipo: tipo }, function(result) { if(result != "") $('#'+tipo+'-body').html(result); });
}

function verRelato(id) {
	$('#modal-body').html('carregando...'); 
	$('#modal-body').load('relato-ver.php?id='+id); 
	$('#relSalvar').hide(); 
	$('#cancelar').html('Fechar');
	$('#modalRelato').modal('show'); 
}

function editarRelato(id) {
	$('#modal-body').html('carregando...'); 
	$('#modal-body').load('relato-editar.php?id='+id); 
	$('#relSalvar').html('Salvar');
	$('#relSalvar').removeAttr('disabled');
	$('#relSalvar').show(); 
	$('#cancelar').html('Cancelar'); 
	$('#modalRelato').modal('show'); 
}

$('#novoRelato').click( function() { 
	$('#modal-body').html('carregando...'); 
	$('#modal-body').load('relato-novo.php'); 
	$('#relSalvar').html('Salvar');
	$('#relSalvar').removeAttr('disabled');
	$('#relSalvar').show(); 
	$('#cancelar').html('Cancelar'); 
	$('#modalRelato').modal('show'); 
});

$('#relSalvar').click( function() { 
	if($('#dados').val() != null) {
		var dados = $('#dados').val().substr(0, 1024);

		$('#cancelar').hide();
		$('#relSalvar').attr('disabled', 'disabled');
		$('#relSalvar').html('Aguarde...');

		$.get('relato_control.php?ordem=salvar&'+$('#form').serialize(), { dados: dados }, function(result) {
			try { 
				var result = jQuery.parseJSON(result); 
				var relid = result['relid'];
				var situacao = result['situacao'];
				if($('#dados').val().length > 1024) {
					for (var i = 1025; i <= $('#dados').val().length; i+=1025) {
						dados = $('#dados').val().substr(i, i+1024);
						$.get('relato_control.php?ordem=salvar&tipo=continue', { relato: relid, dados: dados });
					}
				}
				if(situacao === true) {
					relListar($("#tipo").val());
					$('#modal-body').html("<div><p><center>Relato salvo com sucesso.</center></p></div>");
					$('#relSalvar').hide();
					$('#cancelar').show();
					$('#cancelar').html('Fechar');
				} else {
					$('#relSalvar').html('Salvar');
					$('#relSalvar').removeAttr('disabled');
					$('#cancelar').show();
					alert(result);
				}
			} catch(err) {
				$('#relSalvar').html('Salvar');
				$('#relSalvar').removeAttr('disabled');
				$('#cancelar').show();
				alert(result);
			};			
		});
	}
});