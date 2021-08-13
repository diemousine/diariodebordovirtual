// Este arquivo é necessário para o funcionamento do módulo de Credencial.
$("#noob").on("click", function () { $("#credencial-conteudo").load("./modulo/credencial/cadastro.php", function() { loadScript('./js/credencial.js'); $(document).on("ready", loadScript('https://www.google.com/recaptcha/api.js')) }); });
$("#amnesia").on("click", function () { $("#credencial-conteudo").load("./modulo/credencial/amnesia.php", function() { loadScript('./js/credencial.js'); $(document).on("ready", loadScript('https://www.google.com/recaptcha/api.js')) }); });
$("#cred-btn-cancel").on("click", function () { $("#credencial-conteudo").load("./modulo/credencial/login.php", function() { loadScript('./js/credencial.js'); }); });
$("#r-senha").on("keyup", function () {
	$("#senha").attr("readonly","readonly");
	$("#cred-btn-submit").attr("disabled","disabled");
	if($("#r-senha").val() === "") { 
		$("#senha").removeAttr("readonly");
		$("#r-senha").removeAttr("style");
	} else if($("#senha").val() !== $("#r-senha").val()) {
		$("#r-senha").attr("style","border-color:red");
	} else if($("#senha").val() === $("#r-senha").val()) {
		$("#r-senha").attr("style","border-color:green");
		$("#cred-btn-submit").removeAttr("disabled");
	}
});
$("#nvescolar").on("change", function() { 
	if($("#nvescolar").val() > 3) { 
		$("#especify").show();
	} else {
		$("#especify").hide();
		$("#especif").removeAttr("value");
	}
});