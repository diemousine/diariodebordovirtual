<?php
echo("
<!-- MENU -->
<div class='row'>
    <nav class='navbar navbar-default navbar-static-top'>
      <div class='container-fluid'>
        <div class='navbar-header'>
");
echo ((!empty($_GET) && $_SESSION['autorizado'] != 0) ? "<a class='navbar-brand' href='http://".$_SERVER['HTTP_HOST']."' title='Voltar'><span class='glyphicon glyphicon-chevron-left'></span></a>" : "<a class='navbar-brand'><span class='glyphicon glyphicon-home'></span></a>");
echo("
          <p class='navbar-text'>Diário de Bordo Virtual</p>
        </div>
");
echo ($_SESSION['autorizado'] == 0 ? "<p class='navbar-text navbar-right'><a class='navbar-link' href='http://".$_SERVER['HTTP_HOST']."/modulo/credencial/?ordem=logoff'>Sair <span class='glyphicon glyphicon-log-out'></span></a></p>" : "");
echo("
      </div><!-- /.container-fluid -->
    </nav>
</div>
<div id='conteudo-principal'>
");
?>