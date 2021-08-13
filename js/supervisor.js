// Author: Diego Socrates Dias Mousine
// JavaScript Document
// Este código foi escrito assim de propósito.

function permitir(usuario) {
	(confirm("Tem certeza que deseja PERMITIR que o usuário faça parte da equipe?")) ? $.get("supervisor_control.php", { ordem: "autorizar", usuario: usuario }, function(result) { if(result == 1) { $('#li'+usuario).html("<span>Usuário permitido.</span>");} }) : "";
}
function negar(usuario) {
	(confirm("Tem certeza que deseja NEGAR que o usuário faça parte da equipe?")) ? $.get("supervisor_control.php", { ordem: "negar", usuario: usuario }, function(result) { if(result == 1) { $('#li'+usuario).html("<span>Usuário negado.</span>");} }) : "";
}