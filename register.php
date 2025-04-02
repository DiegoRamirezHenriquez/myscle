<?php
include("layout/header.php");
include("conexion.php");
$mensaje = "";
if(isset($_POST['register'])){
  
  $email = $_POST['email'];
  $name = $_POST['name'];
  $password = $_POST['password'];

  $checkEmailQuery = "SELECT * FROM usuarios WHERE email='$email'";
  $checkEmailResult = mysqli_query($conn, $checkEmailQuery);

  if(mysqli_num_rows($checkEmailResult) > 0){
    $mensaje = "El correo electrónico ya está registrado";
  } else {
    $query = "INSERT INTO usuarios (email, name, password) VALUES ('$email', '$name', '$password')";
    $result = mysqli_query($conn, $query);
    if($result){
      session_start();
      $_SESSION['email'] = $email;
      header("Location: profile.php");
    } else {
      $mensaje = "Error al registrar usuario";
    }
  }
}
?>
<div class="form-ui">
    <form method="post" class="form">
          <div class="form-body">
            <div class="welcome-lines">
              <div class="welcome-line-1">Crear cuenta</div>
              <div class="welcome-line-2">¡Bienvenido!</div>
            </div>
            <div class="input-area">
              <div class="form-inp">
                <input placeholder="Correo electronico" type="text" name="email" maxlength="254">
              </div>
              <div class="form-inp">
                <input placeholder="Nombre" type="text" name="name" maxlength="254">
              </div>
              <div class="form-inp">
                <input placeholder="Contraseña" type="password" name="password" maxlength="254">
              </div>
            </div>
            <div class="submit-button-cvr">
              <button class="submit-button" type="submit" name="register">Crear cuenta</button>
            </div>
            <div>
              <span class="err-msg"><?php echo $mensaje ?></span>
            </div>
            <div class="account-exists">
              <a href="login.php">¿Ya tienes cuenta?</a>
            </div>
          </div>
        </form>
</div>



<?php

include("layout/footer.php")
?>