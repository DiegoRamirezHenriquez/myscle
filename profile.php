<?php
include("templates/header.php");
include("conexion.php");
if(isset($_SESSION['email'])){
    $email = $_SESSION['email'];
}
$isLoggedIn = isset($email);
$query = "SELECT * FROM usuarios WHERE email='$email'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

$name = $row['name'];
$id=$row['id'];

$query = "SELECT * FROM details_usuarios WHERE id_usuarios='$id'";
$result = mysqli_query($conn, $query);
$rowDetails = mysqli_fetch_assoc($result);

$name = $row['name'];
$gender = isset($rowDetails['gender']) ? $rowDetails['gender'] : '';
$weight = isset($rowDetails['weight']) ? $rowDetails['weight'] : '';
$height = isset($rowDetails['height']) ? $rowDetails['height'] : '';
$age = isset($rowDetails['age']) ? $rowDetails['age'] : '';

if(!$rowDetails){
    $info = false;
}else{
    $info = true;
}

if(isset($_POST['save-edit'])){
    $age = !empty($_POST['age']) ? $_POST['age'] : null;
    $gender = !empty($_POST['gender']) ? $_POST['gender'] : null;
    $height = !empty($_POST['height']) ? $_POST['height'] : null;
    $weight = !empty($_POST['weight']) ? $_POST['weight'] : null;

    if($info){
        $query = "UPDATE details_usuarios SET age='$age' gender='$gender' height='$height' weight='$weight' WHERE id_usuarios='$id'";
    }else{
        $query = "INSERT INTO details_usuarios (gender, weight, height, age, id_usuarios) VALUES ('$gender', '$weight', '$height', '$age', '$id')";
    }
    $result = mysqli_query($conn, $query);
    if($result){
        header("Location: profile.php");
    }else{
        echo "Error al guardar los datos";
    }
}
?>
<div class="profile-container">
    <?php if(!$info){ ?>
        <div class="edit-profile">
    <h1>Editar perfil</h1>
        <form action="" method="POST">
            <div class="profile-info">
                <div class="profile-img">
                    <img src="images/users/img-user.png">
                </div>
                <div class="profile-data">
                    <h2><?php echo $name; ?></h2>
                    <p>Correo electrónico: <?php echo $email; ?></p>
                    <p>Edad: <input type="text" name="age" value="<?php echo $age; ?>"></p>
                    <p>Genero: 
                        <select name="gender">
                            <option value="masculino" <?php if($gender == 'masculino') echo 'selected'; ?>>Masculino</option>
                            <option value="femenino" <?php if($gender == 'femenino') echo 'selected'; ?>>Femenino</option>
                            <option value="otro" <?php if($gender == 'otro') echo 'selected'; ?>>Otro</option>
                        </select>
                    </p>
                    <p>Altura: <input type="text" name="height" value="<?php echo $height; ?>"></p>
                    <p>Peso: <input type="text" name="weight" value="<?php echo $weight; ?>"></p>
                </div>
            </div>
        <div class="profile-options">
            <button type="submit" name="save-edit" class="btn-save-profile">Guardar cambios</button>
        </div>
    </form>
</div>
    <?php }else{ ?>
        <div class="profile-content">
            <h1>Perfil</h1>
            <div class="profile-info">
                <div class="profile-img">
                    <img src="images/icons/usr.png">
                </div>
                <div class="profile-data">
                    <h2><?php echo $name; ?></h2>
                    <p>Correo electrónico: <?php echo $email; ?></p>
                    <p>Edad: <?php echo $age; ?></p>
                    <p>Genero: <?php echo $gender; ?></p>
                    <p>Altura: <?php echo $height; ?></p>
                    <p>Peso: <?php echo $weight; ?></p>
                </div>
            </div>
            <div class="profile-options">
                <a href="edit-profile.php" class="btn-edit-profile">Editar perfil</a>
                <a href="delete-profile.php" class="btn-delete-profile">Eliminar perfil</a>
            </div>
        </div>
    <?php } ?>
</div>




<?php
include("templates/footer.php")
?>