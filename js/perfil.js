// Este arquivo foi criado para uso único e particular no site Diário de Bordo Virtual
$("#vinculo").on("change", function() {
	$("#divinstituicao").show();
	$("#divcoordenador").show();
	$("#divlocal").show();
	$("#divsupervisor").show();
	$("#divcurso").show();
	$("#divsemestre").show();
	$("#instituicao").attr("required","");
	$("#coordenador").attr("required","");
	$("#local").attr("required","");
	$("#supervisor").attr("required","");
	$("#curso").attr("required","");
	$("#semestre").attr("required","");
	if($("#vinculo").val() == 1) {
		$("#divinstituicao").hide();
		$("#divcoordenador").hide();
		$("#divlocal").hide();
		$("#divsupervisor").hide();
		$("#divcurso").hide();
		$("#divsemestre").hide();
		$("#instituicao").removeAttr("required");
		$("#coordenador").removeAttr("required");
		$("#local").removeAttr("required");
		$("#supervisor").removeAttr("required");
		$("#curso").removeAttr("required");
		$("#semestre").removeAttr("required");
	} else if($("#vinculo").val() == 2) {
		$("#divsupervisor").hide();
		$("#divcurso").hide();
		$("#divsemestre").hide();
		$("#supervisor").removeAttr("required");
		$("#curso").removeAttr("required");
		$("#semestre").removeAttr("required");
	}
});

$("#instituicao").on("change", function() {
	$("#coordenador").html("<option>Carregando...</option>");
	$.get("./modulo/credencial/perfil-control.php", { ordem: "listar", tipo: "coordenador", instituicao: $("#instituicao").val() }, function(result) { $("#coordenador").html(result); });
});

$("#coordenador").on("change", function() {
	$("#local").html("<option>Carregando...</option>");
	$.get("./modulo/credencial/perfil-control.php", { ordem: "listar", tipo: "local", instituicao: $("#instituicao").val(), coordenador: $("#coordenador").val() }, function(result) { $("#local").html(result); });
});

$("#local").on("change", function() {
	$("#supervisor").html("<option>Carregando...</option>");
	$.get("./modulo/credencial/perfil-control.php", { ordem: "listar", tipo: "supervisor", instituicao: $("#instituicao").val(), coordenador: $("#coordenador").val(), local: $("#local").val()  }, function(result) { $("#supervisor").html(result); });
});

function perfilAterar(sessao) {
	$('#modal-body').html('Carregando...');
	$('#modal-body').load('/modulo/credencial/perfil-model.php?ordem='+sessao);
	$('#perSalvar').show();
	$('#cancelar').html('Cancelar');
	if(sessao == 'imagem') $('#perSalvar').hide();
	$('#modalPerfil').modal('show');
}

$('#perSalvar').on('click', function() {
	$('#modal-body').load('/modulo/credencial/perfil-control.php?ordem=salvar&', $('#form').serialize(), function() {
		$('#perSalvar').hide();
		$('#cancelar').html('Fechar');
	});
});