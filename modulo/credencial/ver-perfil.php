<?php
  $id = (is_numeric($_GET['usuario'])) ? htmlspecialchars($_GET['usuario']) : $_SESSION['idusuario'];
  $conn = bdcon();
  $consulta = mysqli_query($conn, "SELECT * FROM usuario, vinculo WHERE idusuario = $id AND vinculo = idvinculo");
  if(mysqli_affected_rows($conn) > 0) {
    $resultado = mysqli_fetch_array($consulta);
    $srcimg = ($resultado['foto'] == "") ? '/modulo/credencial/img-perfil/nenhuma.png' : '/modulo/credencial/img-perfil/'.$resultado['foto'];

    if($resultado['idusuario'] == $_SESSION['idusuario']) {
      echo ("
      <div class='col-sm-2'>
        <div class='list-group'>
          <li class='list-group-item active'><span class='glyphicon glyphicon-cog'></span> Configurações</li>
          <a class='list-group-item' onclick=\"perfilAterar('imagem')\">Alterar Imagem</a>
          <a class='list-group-item' onclick=\"perfilAterar('senha')\">Alterar Senha</a>
          <a class='list-group-item' onclick=\"perfilAterar('nome')\">Alterar Nome</a>
          <a class='list-group-item' onclick=\"perfilAterar('curso')\">Informações do Curso</a>
          <a class='list-group-item' href='abandonar.php'>Abandonar Equipe</a>
        </div>
      </div>
      ");
    } else {
      echo ("<div class='col-sm-2'></div>");
    }
    echo("
    <div class='col-sm-3'>
      <div class='col-sm-9 col-sm-offset-1'>
        <div class='thumbnail'>
          <img src='".$srcimg."'>
        </div>
      </div>
    </div>
    <div class='col-sm-7'>");
    if($resultado['autorizado'] == 0) echo("
      <div class='text-warning'>
        <p>------------------------------------------<br />
        <strong>AGUARDANDO AUTORIZAÇÃO</strong><br />
        ------------------------------------------</p>
      </div>
    ");
      echo("
      <div>
        <label class='control-label'>Nome:</label>
        <span>".$resultado['nome']."</span> 
      </div>
      <div>
        <label class='control-label'>Vínculo:</label>
        <span>".$resultado['vindescricao']."</span>
      </div>
      <div>
        <label class='control-label'>Instituição:</label>");
    if($resultado['vinculo'] == 1) {
      $consulta = mysqli_query($conn, "SELECT instdescricao FROM instituicao, instituicao_coordenador WHERE instituicao.idinstituicao = instituicao_coordenador.icinstituicao AND instituicao_coordenador.iccoordenador = $id ORDER BY instdescricao ASC");
      if(mysqli_affected_rows($conn) > 0) {
        while($row = mysqli_fetch_row($consulta)){
          echo(" <span>(".$row[0].") </span>");
        }
      } else { echo (" <span class='text-muted'>Selecione o menu <strong>Portaria</strong> para adicionar uma Instituição.</span>"); }
    } else {
      $idInst = $resultado['instituicao'];
      $consulta = mysqli_query($conn, "SELECT instdescricao FROM instituicao WHERE idinstituicao = $idInst");
      if(mysqli_affected_rows($conn) > 0) {
        $row = mysqli_fetch_row($consulta);
        echo(" <span>".$row[0]."</span>");
      }
    }
    echo ("</div>");
    if($resultado['vinculo'] > 1) {
      echo ("
      <div>
        <label class='control-label'>Coordenador:</label>");
      $idCoord = $resultado['coordenador'];
      $consulta = mysqli_query($conn, "SELECT nome FROM usuario WHERE idusuario = $idCoord");
      if(mysqli_affected_rows($conn) > 0) {
          $row = mysqli_fetch_row($consulta);
          echo(" <span>".$row[0]."</span>");
        }
      echo ("</div>
      <div>
        <label class='control-label'>Local de atuação:</label>");
        $idLocal = $resultado['local'];
        $consulta = mysqli_query($conn, "SELECT locdescricao FROM local WHERE idlocal = $idLocal");
        if(mysqli_affected_rows($conn) > 0) {
          $row = mysqli_fetch_row($consulta);
          echo(" <span>".$row[0]."</span>");
        }
      echo ("</div>");
    }
    if($resultado['vinculo'] > 2) { echo ("
      <div>
        <label class='control-label'>Supervisor:</label>");
        $idSuper = $resultado['supervisor'];
        $consulta = mysqli_query($conn, "SELECT nome FROM usuario WHERE idusuario = $idSuper");
        if(mysqli_affected_rows($conn) > 0) {
          $row = mysqli_fetch_row($consulta);
          echo(" <span>".$row[0]."</span>");
        }
      echo ("</div>
      <div>
        <label class='control-label'>Curso:</label>
        <span>".$resultado['curso']."</span>
      </div>
      <div>
        <label class='control-label'>Semestre:</label>
        <span>".$resultado['semestre']."</span>
      </div>");
    }
    echo "</div>";
  } else {
    echo("Perfil não encontrado.");
  }
  if($id == $_SESSION['idusuario']) {
    echo("
    <script language='javascript'>
      function preview(input) {
        if (input.files && input.files[0]) {
          var reader = new FileReader();
          reader.onload = function (e) { $('#preview_image').attr('src', e.target.result).width(200); };
          document.getElementById('dados_arquivo').innerHTML = \"<span class='help-block'><span class='glyphicon glyphicon-info-sign'></span> Arquivo: \"+input.files[0].type+\" | Tamanho: \"+(input.files[0].size/1024).toFixed(2)+\" KB.</span></div>\";
          reader.readAsDataURL(input.files[0]);
        }
      }
    </script>
    ");
  }
?>
<div class='row'>
  <div class="modal fade" id='modalPerfil' tabindex="-1" role="diálogo" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Controle do Perfil</h4>
        </div>
        <div class="modal-body" id='modal-body'>
          <p>One fine body&hellip;</p>
        </div>
        <div class="modal-footer">
          <button class="btn btn-default" data-dismiss="modal" id='cancelar'>Cancelar</button>
          <button class="btn btn-primary" id='perSalvar'>Salvar</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
</div>