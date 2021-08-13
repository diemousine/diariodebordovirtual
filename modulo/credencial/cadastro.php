<form action='./modulo/credencial/' method='post' accept-charset="UTF-8">
  <div class='form-group'>
    <label>CADASTRO</label>
    <input class='form-control' type='email' id='email' name='email' title='Email' placeholder='Email...' maxlength=128 required />
    <input class='form-control' type='password' id='senha' name='senha' title='Senha' placeholder='Senha...' maxlength=16 required />
    <input class='form-control' type='password' id='r-senha' name='r-senha' title='Repetir senha' placeholder='Repetir senha...' maxlength=16 required />
    <label class='text-muted'><input type='checkbox' id='termo' name='termo' title='Termos e Condiçõesde uso' required /> Li e concorco com os <a>Termos e condições de uso</a>.</label>
    <input class='form-control' type='hidden' name='ordem' value='cadastrar' />
  </div>
  <div class='form-group'>
    <center><div class="g-recaptcha" data-sitekey=""></div></center>
  </div>
  <div class='form-group'>
    <button type='submit' id='cred-btn-submit' class='btn btn-primary' title='Cadastrar' disabled>Cadastrar</button>
    <button type='reset' id='cred-btn-cancel' class='btn btn-default' title='Cancelar'>Cancelar</button>
  </div>
</form>