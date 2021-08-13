<form action='./modulo/credencial/' method='post'>
  <div class='form-group'>
    <label>CREDENCIAIS</label>
    <input class='form-control' type='email' id='email' name='email' placeholder='Email...' required />
    <input class='form-control' type='password' id='senha' name='senha' placeholder='Senha...' required />
    <input class='form-control' type='hidden' name='ordem' value='login' />
  </div>
  <div class='form-group'>
    <p class='text-info'><a id='amnesia' title='Esqueci a senha'>Esqueci a senha</a></p>
  </div>
  <div class='form-group'>
    <button type='submit' id='btn-submit' class='btn btn-primary' title='Entrar'>Entrar</button>
  </div>
  <div class='form-group'>
    <p class='text-info'><a id='noob' title='Criar uma nova conta'>Criar uma nova conta</a></p>
  </div>
</form>