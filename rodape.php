      <div class="row">
        <div class="col-sm-12">
          <hr />
          <div class="col-sm-4">
            &nbsp;
          </div>
          <div class="col-sm-4 text-center">
            <span class="glyphicon glyphicon-copyright-mark"></span> 2015-2016 <a href="http://lattes.cnpq.br/9503396258176099" target="_blank" title="Perfil do autor">Diego Socrates Dias Mousine</a>
          </div>
        </div>
      </div>
    </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="js/jquery-1.11.3.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <script src="js/credencial.js"></script>
    <?php
    if(isset($_SESSION['idusuario'])) {
      echo("<script src='js/perfil.js'></script>
        <script src='js/relato.js'></script>
      ");
    }
    if(isset($_SESSION['vinculo'])) {
      if($_SESSION['vinculo'] == 1) echo "<script src='js/coordenador.js'></script>";
      else if($_SESSION['vinculo'] == 2) echo "<script src='js/supervisor.js'></script>";
    }
    ?>
    <script type="text/javascript">
    // Esta função está diretamente ligada ao módulo Credencial.
    function loadScript(url, callback){
      // Adding the script tag to the head as suggested before
      var head = document.getElementsByTagName('head')[0];
      var script = document.createElement('script');
      script.type = 'text/javascript';
      script.src = url;

      // Then bind the event to the callback function.
      // There are several events for cross browser compatibility.
      script.onreadystatechange = callback;
      script.onload = callback;

      // Fire the loading
      head.appendChild(script);
    }
    </script>
  </body>
</html>