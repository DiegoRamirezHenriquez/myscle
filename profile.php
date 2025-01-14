<?php
include("templates/header.php");
include("conexion.php");

if(isset($_SESSION['email'])){
    $email = $_SESSION['email'];
}else{
    header("Location: login.php");
    exit();
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
        $query = "SELECT image_user FROM details_usuarios WHERE id_usuarios='$id'";
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);
        
        if ($row && $row['image_user']  ) {
            $old_image = "images/users/" . $row['image_user'];
            if (file_exists($old_image)) {
                unlink($old_image);
            }
        }
        

        $target_dir = "images/users/";
        $imageFileType = strtolower(pathinfo($image_user, PATHINFO_EXTENSION));
        $new_image_name = $id . "_" . time() . "." . $imageFileType;
        $target_file = $target_dir . $new_image_name;
        if(move_uploaded_file($_FILES["image_user"]["tmp_name"], $target_file)){
            $query = "UPDATE details_usuarios SET age='$age', gender = '$gender', height='$height', weight='$weight', image_user='$new_image_name' WHERE id_usuarios='$id'";
        }else{
            echo "Error al subir la imagen";
        }
    } else {
        $query = "SELECT image_user FROM details_usuarios WHERE id_usuarios='$id'";
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);
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

if(isset($_POST['cancel-edit'])){
    $_SESSION['edit_mode']=false;
    header("Location: profile.php");
    exit();
}


$edit_mode = isset($_SESSION['edit_mode']) ? $_SESSION['edit_mode'] : false;


if(isset($_POST['publicar'])){
    $description = $_POST['description'];
    $files = $_FILES['files'];
    $query = "INSERT INTO users_post (post, id_usuario, time) VALUES ('$description', '$id', NOW())";

    $result = mysqli_query($conn, $query);

    if($result){
        $last_id = mysqli_insert_id($conn);
        $target_dir = "images/post-users/";
        $query = "SELECT * FROM users_post WHERE id='$last_id'";
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);
        $post_id = $row['id'];
        $post_images = array();
        for($i = 0; $i < count($files['name']); $i++){
            if ($files['name'][$i]) {
                $imageFileType = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
                $new_image_name = $post_id . "_" . $i . "." . $imageFileType;
                $target_file = $target_dir . $new_image_name;
                move_uploaded_file($files["tmp_name"][$i], $target_file);
                $post_images[] = $new_image_name;
            }
        }
        if (!empty($post_images)) {
            $query = "INSERT INTO files_post (post_id, files) VALUES ";
            foreach($post_images as $image){
                $query .= "('$post_id', '$image'),";
            }
            $query = substr($query, 0, -1);
            $result = mysqli_query($conn, $query);
            if($result){
                header("Location: profile.php");
            }else{
                echo "Error al subir las imágenes";
            }
        } else {
            header("Location: profile.php");
        }
    }else{
        echo "Error al publicar";
    }
}
?>
<div class="profile-container">
    <?php if($edit_mode){ ?>
        <div class="edit-profile-container">
            <h1>Editar perfil</h1>
            <form method="POST" enctype="multipart/form-data">
                <div class="edit-profile-info">
                    <div class="edit-profile-img">
                        <input type="file" name="image_user" accept="image/*" onchange="previewImage(this)">
                        <div id="image-preview-edit">
                        <?php if($rowDetails && $rowDetails['image_user']){ ?>
                            <img src="images/users/<?php echo $rowDetails['image_user']; ?>" alt="Profile Image" style="width: 20%;">
                            <form method="POST">
                                <button type="submit" name="delete-image" class="btn-delete-image">Eliminar imagen</button>
                            </form>
                        <?php } ?>
                        </div>
                    </div>
                </div>
            <?php 
            if(isset($_POST['delete-image'])){
                $query = "SELECT image_user FROM details_usuarios WHERE id_usuarios='$id'";
                $result = mysqli_query($conn, $query);
                $row = mysqli_fetch_assoc($result);
                if ($row && $row['image_user']) {
                    $old_image = "images/users/" . $row['image_user'];
                    if (file_exists($old_image)) {
                        unlink($old_image);
                    }
                }
                $query = "UPDATE details_usuarios SET image_user='' WHERE id_usuarios='$id'";
                $result = mysqli_query($conn, $query);
                if ($result) {
                    header("Location: profile.php");
                } else {
                    echo "Error al eliminar la imagen";
                }
            }
            ?>
    
        <script>
        function previewImage(input) {
            var preview = document.getElementById('image-preview-edit');
            preview.innerHTML = '';
            var file = input.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    var img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.width = '20%';
                    preview.appendChild(img);
                }
                reader.readAsDataURL(file);
            }
        }
        </script>
        <div class="edit-profile-data">
            <h2><?php echo $name; ?></h2>
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
        <div class="edit-profile-options">
            <button type="submit" name="save-edit" class="btn-save-profile">Guardar cambios</button>
            <button type="submit" name="cancel-edit" class="btn-cancel-profile">Cancelar</button>
        </div>
        </form>
    <?php }else{ ?>
        <div class="profile-container-info">
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
                        <p>Edad: <?php echo $age; ?></p>
                        <p>Genero: <?php echo $gender; ?></p>
                        <p>Altura: <?php echo $height; ?></p>
                        <p>Peso: <?php echo $weight; ?></p>
                    </div>
                </div>
                <div class="profile-options">
                    <form method="POST" class="edit-profile-form">
                        <button type="submit" class="btn-edit-profile" name="goto-edit-perfil">Editar perfil
                            <svg class="svg" viewBox="0 0 512 512">
                                <path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
            <div class="profile-posts-container">
                <form method="POST" enctype="multipart/form-data">
                    <div class="post-input-container">
                        <div class="textarea-container">
                            <textarea name="description" placeholder="Descripción de la publicación" required></textarea>
                            <label for="files" class="file-upload-label">
                            <i class='bx bx-paperclip bx-rotate-270'></i>
                            </label>
                            <input type="file" id="files" name="files[]" accept="image/*" multiple style="display: none;" onchange="previewImages()" max="4">
                        </div>
                        <div id="image-preview" class="image-preview"></div>
                            <button type="submit" name="publicar">Publicar</button>
                    </div>
                </form>
                <script>
                    function previewImages() {
                        var preview = document.getElementById('image-preview');
                        preview.innerHTML = '';
                        var files = document.getElementById('files').files;
                        if (files.length > 4) {
                            var errorMsg = document.createElement('p');
                            errorMsg.textContent = 'Solo puedes subir un máximo de 4 imágenes';
                            errorMsg.style.color = 'red';
                            preview.appendChild(errorMsg);
                            document.getElementById('files').value = '';
                            return;
                        }
                        for (var i = 0; i < files.length; i++) {
                            var file = files[i];
                            var reader = new FileReader();
                            reader.onload = function (e) {
                                var img = document.createElement('img');
                                img.src = e.target.result;
                                img.onclick = function() {
                                    this.remove();
                                };
                                preview.appendChild(img);
                            }
                            reader.readAsDataURL(file);
                        }
                    }
                </script>
                <div class="profile-posts">
                    <?php
                        $queryName = "SELECT name FROM usuarios WHERE email='$email'";
                        $resultName = mysqli_query($conn, $queryName);
                        $name = mysqli_fetch_assoc($resultName)['name'];
                        $query = "SELECT * FROM users_post WHERE id_usuario='$id'";
                        $result = mysqli_query($conn, $query);

                        while($row = mysqli_fetch_assoc($result)){
                            $post_id = $row['id'];
                            $description = $row['post'];
                            $time = $row['time'];
                            $query = "SELECT * FROM files_post WHERE post_id='$post_id'";
                            $resultFiles = mysqli_query($conn, $query);
                            $files = array();
                            while($rowFiles = mysqli_fetch_assoc($resultFiles)){
                                $files[] = $rowFiles['files'];
                            }
                    ?>
                    <div class="post">
                        <div class="post-info">
                            <div class="post-user">
                                <?php
                                    if($rowDetails['image_user'] == ''){
                                ?>
                                    <img src="images/icons/usr.png">
                                <?php }else{ ?>
                                    <img src="images/users/<?php echo $rowDetails['image_user']; ?>">
                                <?php } ?>
                                <h2><a href="#"><?php echo $name ?></a></h2>
                            </div>
                            
                            <p><?php echo $time; ?></p>
                        </div>
                        <div class="post-data">
                            <p><?php echo $description; ?></p>
                        </div>
                        <?php if(count($files) > 0){ ?>
                            <div class="post-img">
                                <?php foreach($files as $file){ ?>
                                    <img src="images/post-users/<?php echo $file; ?>" onclick="viewImageFullScreen(this)">
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <script>
                            function viewImageFullScreen(img) {
                                var modal = document.createElement('div');
                                modal.style.position = 'fixed';
                                modal.style.top = '0';
                                modal.style.left = '0';
                                modal.style.width = '100%';
                                modal.style.height = '100%';
                                modal.style.backgroundColor = 'rgba(0, 0, 0, 0.8)';
                                modal.style.display = 'flex';
                                modal.style.alignItems = 'center';
                                modal.style.justifyContent = 'center';
                                modal.onclick = function() {
                                    document.body.removeChild(modal);
                                };

                                var fullImg = document.createElement('img');
                                fullImg.src = img.src;
                                fullImg.style.maxWidth = '90%';
                                fullImg.style.maxHeight = '90%';

                                modal.appendChild(fullImg);
                                document.body.appendChild(modal);
                            }
                        </script>
                        <div class="post-options">
                            <div class="post-buttons">
                                <button class="btn-like">
                                    <i class='bx bx-like' style='color:#02b03a'  ></i>
                                    <!-- si tiene el like que salga este
                                     <i class='bx bxs-like' style='color:#02b03a' ></i>
                                    -->
                                </button>
                                <button class="btn-comment">
                                    <i class='bx bxs-comment' style='color:#02b03a' ></i>
                                </button>
                            </div>
                            <div class="dropdown">
                                <button class="dropbtn">
                                    <i class='bx bx-dots-vertical-rounded' style='color:#02b03a' ></i>
                                </button>
                                <div class="dropdown-content">
                                    <form method="POST" action="edit_post.php">
                                        <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                                        <button type="submit" name="edit-post">Editar</button>
                                    </form>
                                    <form method="POST" action="delete_post.php">
                                        <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                                        <button type="submit" name="delete-post">Borrar</button>
                                    </form>
                                </div>
                            </div>
                            <style>
                                .dropdown {
                                    position: relative;
                                    display: inline-block;
                                }
                                .dropbtn {
                                    
                                    border: none;
                                    cursor: pointer;
                                    padding: 5px;
                                    font-size: 16px;
                                }
                                .dropdown-content {
                                    display: none;
                                    position: absolute;
                                    min-width: 160px;
                                    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
                                    z-index: 1;
                                }
                                .dropdown-content button {
                                    color: black;
                                    padding: 12px 16px;
                                    text-decoration: none;
                                    display: block;
                                    width: 100%;
                                    border: none;
                                    background: none;
                                    text-align: left;
                                }
                                .dropdown-content button:hover {
                                    background-color: #ddd;
                                }
                                .dropdown:hover .dropdown-content {
                                    display: block;
                                }
                            </style>

                        </div>
                        <hr style="color: #164B60; width: 100%; ">
                    </div>
                    <?php } ?>
                </div>
                    
                </div>
            </div>
        </div>
        
    <?php } ?>
</div>
<?php include("templates/footer.php"); ?>