<form class='form-horizontal' action='./modulo/credencial/' method='post'>
  <div class='form-group'>
    <label for='usuario' class='col-sm-3 control-label'>Nome completo:</label>
    <div class='col-sm-6'>
      <input class='form-control' type='text' id='nome' name='nome' title='Nome Completo' placeholder='Digite seu nome completo' <?php
       if(isset($_SESSION['nome'])) echo "value='".$_SESSION['nome']."'" ?> maxlength=64 required/>
    </div>
  </div>
  <div class='form-group'>
    <label class='col-sm-3 control-label'>Email:</label>
    <div class='col-sm-6'>
      <input class='form-control' type='email' title='Email' placeholder='<?php echo $_SESSION['email'] ?>' readonly />
    </div>
  </div>
  <div class='form-group'>
    <label for='vinculo' class='col-sm-3 control-label'>Vínculo:</label>
    <div class='col-sm-6'>
      <select class='form-control' id='vinculo' name='vinculo' required>
        <option value=''>SELECIONE</option>
        <?php
        include_once 'bdcon.php';
        $conn = bdcon();
        $consulta = mysqli_query($conn, "SELECT * FROM vinculo ORDER BY idvinculo ASC");
        if(mysqli_num_rows($consulta) > 0) {
          while($row = mysqli_fetch_row($consulta)){
            if($row[0] == $_SESSION['vinculo']) echo("<option value='".$row[0]."' selected>".$row[1]."</option>"); else echo("<option value='".$row[0]."'>".$row[1]."</option>");
          }
        }
        ?>
      </select>
    </div>
  </div>
  <div class='form-group' id='divinstituicao'>
    <label for='instituicao' class='col-sm-3 control-label'>Instituição:</label>
    <div class='col-sm-6'>
      <select class='form-control' id='instituicao' name='instituicao' required>
        <option value=''>SELECIONE</option>
        <?php
        include_once 'bdcon.php';
        $conn = bdcon();
        $consulta = mysqli_query($conn, "SELECT * FROM instituicao ORDER BY instdescricao ASC");
        if(mysqli_num_rows($consulta) > 0) {
          while($row = mysqli_fetch_row($consulta)){
            echo("<option value='".$row[0]."'>".$row[1]."</option>");
          }
        }
        ?>
      </select>
    </div>
  </div>
  <div class='form-group' id='divcoordenador'>
    <label for='coordenador' class='col-sm-3 control-label'>Coordenador:</label>
    <div class='col-sm-6'>
      <select class='form-control' id='coordenador' name='coordenador' required>
        <option value=''>SELECIONE</option>
      </select>
    </div>
  </div>
  <div class='form-group' id='divlocal'>
    <label for='local' class='col-sm-3 control-label'>Local de atuação:</label>
    <div class='col-sm-6'>
      <select class='form-control' id='local' name='local' required>
        <option value=''>SELECIONE</option>
      </select>
    </div>
  </div>
  <div class='form-group' id='divsupervisor'>
    <label for='supervisor' class='col-sm-3 control-label'>Supervisor:</label>
    <div class='col-sm-6'>
      <select class='form-control' id='supervisor' name='supervisor' required>
        <option value=''>SELECIONE</option>
      </select>
    </div>
  </div>
  <div class='form-group' id='divcurso'>
    <label for='curso' class='col-sm-3 control-label'>Curso:</label>
    <div class='col-sm-6'>
      <input class='form-control' type='text' id='curso' name='curso' title='Curso' placeholder='Digite o nome do seu curso' <?php
       if(isset($_SESSION['curso'])) echo "value='".$_SESSION['curso']."'" ?> maxlength=64 required />
    </div>
  </div>
  <div class='form-group' id='divsemestre'>
    <label for='semestre' class='col-sm-3 control-label'>Semestre:</label>
    <div class='col-sm-6'>
      <input class='form-control' type='number' id='semestre' name='semestre' title='Semestre' placeholder='Qual o seu semestre? Min: 1 e Max: 16' <?php
       if(isset($_SESSION['semestre'])) echo "value='".$_SESSION['semestre']."'" ?> min=1 max=16 required/>
    </div>
  </div>
  <input class='form-control' type='hidden' name='ordem' value='atualizar' />
  <div class='form-group'>
    <div class='col-sm-12 text-center'>
      <button type='submit' id='perf-btn-submit' class='btn btn-primary' title='Salvar'>Salvar</button>
      <a class='btn btn-default' title='Cancelar' href='<?php echo "http://".$_SERVER['HTTP_HOST']."/?ordem=ver-perfil&usuario=".$_SESSION['idusuario']; ?>'>Cancelar</a>
    </div>
  </div>
</form>
<script language="javascript">
  if(document.getElementById("vinculo").value == 1) {
    document.getElementById("divinstituicao").setAttribute("hidden", "hidden");
    document.getElementById("divcoordenador").setAttribute("hidden", "hidden");
    document.getElementById("divlocal").setAttribute("hidden", "hidden");
    document.getElementById("divsupervisor").setAttribute("hidden", "hidden");
    document.getElementById("divcurso").setAttribute("hidden", "hidden");
    document.getElementById("divsemestre").setAttribute("hidden", "hidden");
    document.getElementById("instituicao").removeAttribute("required");
    document.getElementById("coordenador").removeAttribute("required");
    document.getElementById("local").removeAttribute("required");
    document.getElementById("supervisor").removeAttribute("required");
    document.getElementById("curso").removeAttribute("required");
    document.getElementById("semestre").removeAttribute("required");
  } else if(document.getElementById("vinculo").value == 2) {
    document.getElementById("divsupervisor").setAttribute("hidden", "hidden");
    document.getElementById("divcurso").setAttribute("hidden", "hidden");
    document.getElementById("divsemestre").setAttribute("hidden", "hidden");
    document.getElementById("supervisor").removeAttribute("required");
    document.getElementById("curso").removeAttribute("required");
    document.getElementById("semestre").removeAttribute("required");
  }
</script>