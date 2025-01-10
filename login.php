<?php
include("templates/header.php")
?>
<div id="form-ui">
    <form action="" method="post" id="form">
          <div id="form-body">
            <div id="welcome-lines">
              <div id="welcome-line-1">Iniciar sesión</div>
              <div id="welcome-line-2">¡Bienvenido!</div>
            </div>
            <div id="input-area">
              <div class="form-inp">
                <input placeholder="Correo electronico" type="text" name="email">
              </div>
              <div class="form-inp">
                <input placeholder="Contraseña" type="password" name="password">
              </div>
            </div>
            <div id="submit-button-cvr">
              <button id="submit-button" type="submit" name="login">Iniciar sesión</button>
            </div>
            <div id="forgot-pass">
              <a href="#">¿Olvidaste tu contraseña?</a>
            </div>
          </div>
        </form>
</div>
<?php
if(isset($_POST['login'])){
    $email=$_POST['email'];
    $password=$_POST['password'];
    $query="SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result=mysqli_query($conn, $query);
    if(mysqli_num_rows($result)>0){
        echo "Usuario logueado";
    }else{
        echo "Usuario no encontrado";
    }
}

?>


<?php
include("templates/footer.php")
?>