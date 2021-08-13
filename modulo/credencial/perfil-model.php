<?php
$ordem = htmlspecialchars($_GET['ordem']);

switch ($ordem) {
  case 'imagem':
    $srcimg = '/modulo/credencial/img-perfil/nenhuma.png';
    echo ("
      <div class='col-sm-12'>
        <div class='col-sm-10 col-sm-offset-1'>
          <div class='thumbnail'>
            <img id='preview_image' src='".$srcimg."' style='width: 200px; height:185px'>
          </div>
          <div id='imgdoperfil'>
            <form role='form' id='form' method='post' action='./modulo/credencial/perfil-control.php' target='new' enctype='multipart/form-data'>
              <input class='form-control' type='file' name='imagem' id='imagem' onchange='preview(this)' />
              <div class='input-group' id='dados_arquivo'>
                <span class='help-block'><span class='glyphicon glyphicon-info-sign'></span> Somente imagem JPG com menos de 1 MB.</span>
              </div>
              <button class='btn btn-primary' type='submit' name='ordem' value='imagem' title='Alterar imagem'>Salvar</a>
            </form>
          </div>
        </div>
      </div>
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
    break;
  case 'senha':
    echo("
      <div class='col-sm-12'>
        <form role='form' id='form'>
          <input id='tipo' name='tipo' type='text' value='n-senha' hidden />
          <div class='form-group'>
            <input class='form-control' type='password' id='novasenha' name='novasenha' title='Nova Senha' placeholder='Nova Senha' maxlength=16 required />
          </div>
          <div class='form-group'>
            <input class='form-control' type='password' id='r-nsenha' name='r-nsenha' title='Repita a nova senha' placeholder='Repita a nova senha' maxlength=16 required />
          </div>
          <div class='form-group'>
            <input class='form-control' type='password' id='senha' name='senha' title='Senha Atual' placeholder='Senha Atual' maxlength=16 required />
          </div>
        </form>
      </div>
      ");
    break;
  case 'nome':
    echo("
      <div class='col-sm-12'>
        <form role='form' id='form'>
          <input id='tipo' name='tipo' type='text' value='novoNome' hidden />
          <div class='form-group'>
            <input class='form-control' type='text' id='novoNome' name='novoNome' title='Novo Nome' placeholder='Novo Nome' maxlength=64 required />
          </div>
        </form>
      </div>
      ");
    break;
  case 'curso':
    echo("
      <div class='col-sm-12'>
        <form role='form' id='form'>
          <input id='tipo' name='tipo' type='text' value='curso' hidden />
          <div class='form-group'>
            <input class='form-control' type='text' id='curso' name='curso' title='Nome do curso' placeholder='Nome do Curso' maxlength=64 required />
          </div>
          <div class='form-group'>
            <input class='form-control' type='number' id='semestre' name='semestre' title='Semestre' placeholder='Qual o seu semestre? Min: 1 e Max: 16' min=1 max=16 required/>
          </div>
        </form>
      </div>
      ");
    break;
    default:
      echo("Erro: opção inválida.");
    break;
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