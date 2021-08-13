<?php
// Verifica se o cookie id existe e inicia a sessão.
if(isset($_COOKIE['id'])) {
  session_id(htmlspecialchars($_COOKIE['id']));
  if(empty($_SESSION)) session_start();

  // Verifica se o IP na sessão é igual ao IP dado na hora do login (para evitar cópia de cookies) e carrega o sistema.
  if(isset($_SESSION['host']) && $_SESSION['host'] == $_SERVER['REMOTE_ADDR']) {
    $id = $_SESSION['idusuario'];
    include_once 'bdcon.php';
    $conn = bdcon();
    if($_SESSION['vinculo'] == 1) {
      echo("
      <div class='row'>
        <div class='col-sm-12'>
          <div class='panel panel-primary'>
            <div class='panel-heading'>INSTITUIÇÃO VINCULADA AO PIBID QUAL VOCÊ COORDENA</div>
            <div class='panel-body'>
              <ul class='list-group' id='instituicao-body'>
      ");
              $consulta = mysqli_query($conn, "SELECT idinstituicao, instdescricao FROM instituicao, instituicao_coordenador WHERE idinstituicao = icinstituicao AND iccoordenador = $id ORDER BY instdescricao ASC");
              if(mysqli_affected_rows($conn) > 0) {
                while($row = mysqli_fetch_row($consulta)){
                  echo("<li class='list-group-item' id='instituicao".$row[0]."'>
                      <strong>".$row[1]."</strong>
                      <a class='text-danger pull-right' id='".$row[0]."' title='Remover Instituição' onclick='remInst(this.id)'>
                        <span class='glyphicon glyphicon-fire'></span>
                      </a>
                    </li>");
                }
              } else {
                echo ("<li class='list-group-item'>Você ainda não está vinculado(a) a nenhuma Instituição. Selecione uma na lista abaixo ou clique no botão <strong>Adicionar nova Instituição</strong> caso ela não esteja na lista.</li>");
              }
              echo ("</ul>
              <div class='col-sm-2'>
                <button class='btn btn-info' id='addInstituicao' title='Adicionar nova Insitituição'><span class='glyphicon glyphicon-plus'></span></button>
                <button class='btn btn-warning' id='selInstituicao' title='Selecionar nova Insitituição'><span class='glyphicon glyphicon-check'></span></button>
              </div>
              <div class='col-sm-10' id='selInstituicao-body' hidden>
                <div class='input-group'>
                  <select id='dadosInst' class='form-control' required>
                    <option value=''>SELECIONE</option>
                  </select>
                  <span class='input-group-btn'>
                    <button class='btn btn-success' id='atualizarInst' title='Salvar'><span class='glyphicon glyphicon-ok'></span></button>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class='row'>
        <div class='col-sm-12'>
          <div class='panel panel-primary'>
            <div class='panel-heading'>LOCAL DE ATUAÇÃO</div>
            <div class='panel-body'>
              <ul class='list-group' id='vinculo-body'>
              ");
              $consulta = mysqli_query($conn, "SELECT idinst_loc_coord, instdescricao, locdescricao FROM instituicao, local, inst_loc_coord WHERE idinstituicao = ilcinstituicao AND idlocal = ilclocal AND ilccoordenador = $id ORDER BY instdescricao ASC, locdescricao ASC");
              if(mysqli_affected_rows($conn) > 0) {
                while($row = mysqli_fetch_row($consulta)){
                  echo("<li class='list-group-item'>
                    <strong>".$row[1]."</strong> >> <strong>".$row[2]."</strong>
                    <a class='text-info pull-right' href=\"relatos-pdf.php?idloc_inst=".$row[0]."\" title='Exportar relatos' target='relatos'>
                      <span class='glyphicon glyphicon-print'></span>
                    </a>
                    </li>");
                }
              } else {
                echo "<li class='list-group-item'>Você ainda não tem nenhum Local de Atuação vinculado à nenhuma Instituição. Clique no botão <strong>Adicionar Vínculo</strong> para criar um vínculo entre um Local e uma Instituição a qual você é seja coordenador(a). Caso o Local de Atuação não exista na lista, use o botão <strong>Adicionar Local</strong>.</li>";
              }
              $consulta = mysqli_query($conn, "SELECT * FROM local");
              echo ("</ul>
              <div class='col-sm-12'>
                <button class='btn btn-info' id='addLocal' title='Adicionar Local'><span class='glyphicon glyphicon-plus'></span></button>
                <button class='btn btn-success' id='addVinculo' title='Adicionar Vínculo'><span class='glyphicon glyphicon-link'></span></button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class='row'>
        <div class='col-sm-12'>
          <div class='panel panel-primary'>
            <div class='panel-heading'>NOVOS SUPERVISORES</div>
            <div class='panel-body'>
              <ul class='list-group' id='supervisores-body'>
              ");
              $consulta = mysqli_query($conn, "SELECT idusuario, nome, locdescricao FROM usuario, local, inst_loc_coord WHERE instituicao = ilcinstituicao AND local = idlocal AND local = ilclocal AND coordenador = ilccoordenador AND coordenador = $id AND vinculo = 2 AND autorizado = 0 AND idusuario != $id");
              if(mysqli_affected_rows($conn) > 0) {
                while($row = mysqli_fetch_row($consulta)){
                  echo("<li class='list-group-item' id='li".$row[0]."'>
                      <button class='btn btn-success' id='btp".$row[0]."' title='Permitir fazer parte da equipe' onclick='permitir(".$row[0].")'><span class='glyphicon glyphicon-link'></span></button>
                        <button class='btn btn-danger' id='btn".$row[0]."' title='Negar fazer parte da equipe' onclick='negar(".$row[0].")'><span class='glyphicon glyphicon-fire'></span></button>
                        <strong><a href='?ordem=ver-perfil&usuario=".$row[0]."'>".$row[1]."</a></strong> de <strong>".$row[2]."</strong>
                    </li>");
                }
              } else {
                echo "<li class='list-group-item'>Nenhum novo supervisor por enquanto.</li>";
              }
            echo("
            </div>
          </div>
        </div>
      </div>
      <div class='row'>
        <div class='modal fade' id='modalCoord' tabindex='-1' role='diálogo' aria-labelledby=' aria-hidden='true'>
          <div class='modal-dialog'>
            <div class='modal-content'>
              <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal' aria-label='Fechar'><span aria-hidden='true'>&times;</span></button>
                <h4 class='modal-title'>Controle do Coordenador</h4>
              </div>
              <div class='modal-body' id='modal-body'>
                <p>One fine body&hellip;</p>
              </div>
              <div class='modal-footer'>
                <button class='btn btn-default' data-dismiss='modal' id='cancelar'>Cancelar</button>
                <button class='btn btn-primary' id='coordSalvar'>Salvar</button>
              </div>
            </div><!-- /.modal-content -->
          </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
      </div>
      ");
    } else if($_SESSION['vinculo'] == 2) {
    echo("
    <div class='row'>
      <div class='col-sm-12'>
        <div class='panel panel-primary'>
          <div class='panel-heading'>INSTITUIÇÃO VINCULADA AO PIBID QUAL VOCÊ PARTICIPA</div>
          <div class='panel-body'>
            <ul class='list-group' id='instituição-body'>
    ");
              $instituicao = $_SESSION['instituicao'];
              $consulta = mysqli_query($conn, "SELECT instdescricao FROM instituicao WHERE idinstituicao = $instituicao");
              if(mysqli_affected_rows($conn) > 0) {
                $row = mysqli_fetch_row($consulta);
                echo("<li class='list-group-item'><strong>".$row[0]."</strong></li></ul>");
              }
    echo("
          </div>
        </div>
      </div>
    </div>
    <div class='row'>
      <div class='col-sm-12'>
        <div class='panel panel-primary'>
          <div class='panel-heading'>SEU COORDENADOR</div>
          <div class='panel-body'>
            <ul class='list-group' id='coordenador-body'>
    ");
              $coordenador = $_SESSION['coordenador'];
              $consulta = mysqli_query($conn, "SELECT idusuario, nome FROM usuario WHERE idusuario = $coordenador");
              if(mysqli_affected_rows($conn) > 0) {
                $row = mysqli_fetch_row($consulta);
                echo("<li class='list-group-item'><strong><a href='?ordem=ver-perfil&usuario=".$row[0]."'>".$row[1]."</a></strong></li>");
              }
    echo("
          </div>
        </div>
      </div>
    </div>
    <div class='row'>
      <div class='col-sm-12'>
        <div class='panel panel-primary'>
          <div class='panel-heading'>SEU LOCAL DE ATUAÇÃO</div>
          <div class='panel-body'>
            <ul class='list-group' id='vinculo-body'>
    ");
              $local = $_SESSION['local'];
              $coordenador = $_SESSION['coordenador'];
              $consulta = mysqli_query($conn, "SELECT idinst_loc_coord, locdescricao FROM instituicao, local, inst_loc_coord WHERE idinstituicao = ilcinstituicao AND idlocal = $local AND ilccoordenador = $coordenador");
              if(mysqli_affected_rows($conn) > 0) {
                $row = mysqli_fetch_row($consulta);
                echo("<li class='list-group-item'>
                  <strong>".$row[1]."</strong>
                  <a class='text-info pull-right' href=\"relatos-pdf.php?idloc_inst=".$row[0]."\" title='Exportar relatos' target='relatos'>
                    <span class='glyphicon glyphicon-print'></span>
                  </a>
                  </li></ul>");
              }
    echo("
          </div>
        </div>
      </div>
    </div>
    <div class='row'>
      <div class='col-sm-12'>
        <div class='panel panel-primary'>
          <div class='panel-heading'>NOVOS BOLSISTAS</div>
          <div class='panel-body'>
            <ul class='list-group' id='bolsistas-body'>
    ");
              $local = $_SESSION['local'];
              $coord = $_SESSION['coordenador'];
              $consulta = mysqli_query($conn, "SELECT idusuario, nome, locdescricao FROM usuario, local WHERE coordenador = $coord AND supervisor = $id AND vinculo = 3 AND autorizado = 0 AND local = idlocal AND local = $local");
              if(mysqli_affected_rows($conn) > 0) {
                while($row = mysqli_fetch_row($consulta)){
                  echo("<li class='list-group-item' id='li".$row[0]."'>
                      <button class='btn btn-success' id='btp".$row[0]."' title='Permitir fazer parte da equipe' onclick='permitir(".$row[0].")'><span class='glyphicon glyphicon-link'></span></button>
                        <button class='btn btn-danger' id='btn".$row[0]."' title='Negar fazer parte da equipe' onclick='negar(".$row[0].")'><span class='glyphicon glyphicon-fire'></span></button>
                        <strong><a href='?ordem=ver-perfil&usuario=".$row[0]."'>".$row[1]."</a></strong> de <strong>".$row[2]."</strong>
                    </li>");
                }
              } else {
                echo "<li class='list-group-item'>Nenhum novo bolsista por enquanto.</li>";
              }
    echo("
          </div>
        </div>
      </div>
    </div>
    ");
    } else if($_SESSION['vinculo'] == 3) {
    echo("
    <div class='row'>
      <div class='col-sm-12'>
        <div class='panel panel-primary'>
          <div class='panel-heading'>INSTITUIÇÃO VINCULADA AO PIBID QUAL VOCÊ PARTICIPA</div>
          <div class='panel-body'>
            <ul class='list-group' id='instituição-body'>
    ");
              $instituicao = $_SESSION['instituicao'];
              $consulta = mysqli_query($conn, "SELECT instdescricao FROM instituicao WHERE idinstituicao = $instituicao");
              if(mysqli_affected_rows($conn) > 0) {
                $row = mysqli_fetch_row($consulta);
                echo("<li class='list-group-item'><strong>".$row[0]."</strong></li></ul>");
              }
    echo("
          </div>
        </div>
      </div>
    </div>
    <div class='row'>
      <div class='col-sm-12'>
        <div class='panel panel-primary'>
          <div class='panel-heading'>SEU COORDENADOR</div>
          <div class='panel-body'>
            <ul class='list-group' id='coordenador-body'>
    ");
              $coordenador = $_SESSION['coordenador'];
              $consulta = mysqli_query($conn, "SELECT idusuario, nome FROM usuario WHERE idusuario = $coordenador");
              if(mysqli_affected_rows($conn) > 0) {
                $row = mysqli_fetch_row($consulta);
                echo("<li class='list-group-item'><strong><a href='?ordem=ver-perfil&usuario=".$row[0]."'>".$row[1]."</a></strong></li>");
              }
    echo("
          </div>
        </div>
      </div>
    </div>
    <div class='row'>
      <div class='col-sm-12'>
        <div class='panel panel-primary'>
          <div class='panel-heading'>SEU LOCAL DE ATUAÇÃO</div>
          <div class='panel-body'>
            <ul class='list-group' id='vinculo-body'>
    ");
              $local = $_SESSION['local'];
              $consulta = mysqli_query($conn, "SELECT locdescricao FROM local WHERE idlocal = $local");
              if(mysqli_affected_rows($conn) > 0) {
                $row = mysqli_fetch_row($consulta);
                echo("<li class='list-group-item'><strong>".$row[0]."</strong></li></ul>");
              }
    echo("
          </div>
        </div>
      </div>
    </div>
    <div class='row'>
      <div class='col-sm-12'>
        <div class='panel panel-primary'>
          <div class='panel-heading'>SEU SUPERVISOR</div>
          <div class='panel-body'>
            <ul class='list-group' id='supervisor-body'>
    ");
              $supervisor = $_SESSION['supervisor'];
              $consulta = mysqli_query($conn, "SELECT idusuario, nome FROM usuario WHERE idusuario = $supervisor");
              if(mysqli_affected_rows($conn) > 0) {
                $row = mysqli_fetch_row($consulta);
                echo("<li class='list-group-item'><strong><a href='?ordem=ver-perfil&usuario=".$row[0]."'>".$row[1]."</a></strong></li>");
              }
    echo("
          </div>
        </div>
      </div>
    </div>
    ");
    } else {
      echo("Ação suspeita bloqueada!");
    }
  }
}
?>