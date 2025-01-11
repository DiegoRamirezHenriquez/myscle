<?php
include("templates/header.php");
include("conexion.php");

if(isset($_SESSION['email'])){
    $email = $_SESSION['email'];
}else{
    header("Location: login.php");
}
$isLoggedIn = isset($email);
$query = "SELECT * FROM usuarios WHERE email='$email'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

$name = $row['name'];
$id = $row['id'];

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
    $image_user = !empty($_FILES['image_user']['name']) ? $_FILES['image_user']['name'] : null;
    if ($image_user) {
        // Delete the old image if it exists
        $query = "SELECT image_user FROM details_usuarios WHERE id_usuarios='$id'";
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);
        if ($row && $row['image_user']) {
            $old_image = "images/users/" . $row['image_user'];
            if (file_exists($old_image)) {
                unlink($old_image);
            }
        }

        $target_dir = "images/users/";
        $imageFileType = strtolower(pathinfo($image_user, PATHINFO_EXTENSION));
        $new_image_name = $id . "_" . time() . "." . $imageFileType;
        $target_file = $target_dir . $new_image_name;
        move_uploaded_file($_FILES["image_user"]["tmp_name"], $target_file);
    } else {
        $new_image_name = $row['image_user'];
    }

    if ($info) {
        $query = "UPDATE details_usuarios SET age='$age', gender='$gender', height='$height', weight='$weight', image_user='$new_image_name' WHERE id_usuarios='$id'";
    } else {
        $query = "INSERT INTO details_usuarios (gender, weight, height, age, id_usuarios, image_user) VALUES ('$gender', '$weight', '$height', '$age', '$id', '$new_image_name')";
    }
    $result = mysqli_query($conn, $query);
    if ($result) {
        $_SESSION['edit_mode'] = false;
        header("Location: profile.php");
    } else {
        echo "Error al guardar los datos";
    }
}

if(isset($_POST['goto-edit-perfil'])){
    $_SESSION['edit_mode'] = true;
}

$edit_mode = isset($_SESSION['edit_mode']) ? $_SESSION['edit_mode'] : false;
?>
<div class="profile-container">
    <?php if($edit_mode){ ?>
        <div class="edit-profile-container">
            <h1>Editar perfil</h1>
            <form method="POST" enctype="multipart/form-data">
            <div class="edit-profile-info">
                <div class="edit-profile-img">
                <input type="file" name="image_user" accept="image/*">
                </div>
                <div class="edit-profile-data">
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
            <div class="edit-profile-options">
                <button type="submit" name="save-edit" class="btn-save-profile">Guardar cambios</button>
            </div>
            </form>
        </div>
    <?php }else{ ?>
        <div class="profile-content">
            <h1>Perfil</h1>
            <div class="profile-info">
                <div class="profile-img">
                    <?php if($rowDetails && $rowDetails['image_user']){ ?>
                        <img src="images/users/<?php echo $rowDetails['image_user']; ?>">
                    <?php }else{ ?>
                        <img src="images/icons/usr.png">
                    <?php } ?>
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
                <form method="POST">
                    <button type="submit" name="goto-edit-perfil">Editar perfil</button>
                </form>
            </div>
        </div>
    <?php } ?>
</div>

<?php
include("templates/footer.php");
?>