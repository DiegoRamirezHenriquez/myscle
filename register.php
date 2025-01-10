<?php
include("templates/header.php");
include("conexion.php");
$mensaje="";
?>
<div id="form-ui">
    <form method="<?php $_SERVER["PHP_SELF"]; ?>" id="form">
          <div id="form-body">
            <div id="welcome-lines">
              <div id="welcome-line-1">Crear cuenta</div>
              <div id="welcome-line-2">¡Bienvenido!</div>
            </div>
            <div id="input-area">
              <div class="form-inp">
                <input placeholder="Correo electronico" type="text" name="email">
              </div>
              <div class="form-inp">
                <input placeholder="Nombre" type="text" name="name">
              </div>
              <div class="form-inp">
                <input placeholder="Contraseña" type="password" name="password">
              </div>
            </div>
            <div id="submit-button-cvr">
              <button id="submit-button" type="submit" name="register">Crear cuenta</button>
            </div>
            <div>
              <span><?php echo $mensaje ?></span>
            </div>
            <div id="forgot-pass">
              <a href="#">¿Ya tienes cuenta?</a>
            </div>
          </div>
        </form>
</div>

<?php
if(isset($_POST['register'])){
    $email=$_POST['email'];
    $name=$_POST['name'];
    $password=$_POST['password'];
    $query="INSERT INTO users (email, name, password) VALUES ('$email', '$name', '$password')";
    $result=mysqli_query($conn, $query);
    if($result){
        echo "Usuario registrado";
    }else{
        $mensaje="Error al registrar usuario";
    }
}
?>

<?php

include("templates/footer.php")
?>