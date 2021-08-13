<div class='col-sm-12' style='margin-top: 5%'>
	<div class='dropdown'>
	  <button class='btn btn-default dropdown-toggle' type='button' id='dropdownMenuUsuario' data-toggle='dropdown' aria-expanded='true'>
	    <?php if(isset($_SESSION['nome'])) echo $_SESSION['nome']; else echo "Bem Vindo(a)"?>
	    <span class='caret'></span>
	  </button>
	  <ul class='dropdown-menu' role='menu' aria-labelledby='dropdownMenuUsuario'>
	    <li role='presentation'><a role='menuitem' tabindex='-1' href='<?php echo "http://".$_SERVER['HTTP_HOST']."/?ordem=ver-perfil&usuario=".$_SESSION['idusuario']; ?>'>Perfil</a></li>
	    <li role="presentation" class="divider"></li>
	    <li role='presentation'><a role='menuitem' tabindex='-1' href='<?php echo "http://".$_SERVER['HTTP_HOST']."/modulo/credencial/?ordem=logoff"; ?>'>Sair</a></li>
	  </ul>
	</div>
</div>