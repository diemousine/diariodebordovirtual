<form role="form" action='./modulo/credencial/' method='post' >
  <div class='alert alert-warning alert-dismissable'>
    <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
    <strong>Atenção!</strong> Preencha o campo abaixo com seu email e clique em <strong>Enviar</strong>. Sua senha será enviada para você.
  </div>
  <div class="form-group">
    <div class="input-group">
      <span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span></span>
      <input class="form-control" type="email" id='email' name="email" placeholder="Email..." maxlength="128" title="Seu email completo (máx: 128 caracteres)" required  />
    </div>
  </div>
  <div class='form-group'>
    <center><div class="g-recaptcha" data-sitekey="6Lcx0AsTAAAAAJVlLaKM2iDj4NaVWoZav6eoiP13"></div></center>
  </div>
  <button class="btn btn-primary" type="submit" name="ordem" value="amnesia" ><span class="glyphicon glyphicon-upload"></span> Enviar</button>
  <button type='reset' id='cred-btn-cancel' class='btn btn-default' title='Cancelar'>Cancelar</button>
</form>