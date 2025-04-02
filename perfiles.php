<?php
include("layout/header.php");
include("conexion.php");
$_SESSION['email'] = $email;
if(isset($_SESSION['email'])){
    $email = $_SESSION['email'];
}else{
    header("Location: login.php");
}

?>


<div>
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
                            <textarea name="description" placeholder="Descripción de la publicación" required maxlength="254"></textarea>
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
                // Obtener el nombre del usuario
                $queryName = "SELECT name FROM usuarios WHERE email='$email'";
                $resultName = mysqli_query($conn, $queryName);

                if (!$resultName) {
                    die("Error en la consulta del nombre: " . mysqli_error($conn));
                }

                $name = mysqli_fetch_assoc($resultName)['name'];

                // Consultar publicaciones del usuario
                $query = "SELECT * FROM users_post WHERE id_usuario='$id'";
                $result = mysqli_query($conn, $query);

                if (!$result) {
                    die("Error en la consulta de publicaciones: " . mysqli_error($conn));
                }

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                        $post_id = $row['id'];
                        $description = $row['post'];
                        $time = $row['time'];

                        // Consultar archivos asociados a la publicación
                        $queryFiles = "SELECT * FROM files_post WHERE post_id='$post_id'";
                        $resultFiles = mysqli_query($conn, $queryFiles);

                        if (!$resultFiles) {
                            die("Error en la consulta de archivos: " . mysqli_error($conn));
                        }

                        $files = [];
                        while ($rowFiles = mysqli_fetch_assoc($resultFiles)) {
                            $files[] = $rowFiles['files'];
                        }
                        ?>
                        <div class="post" id="post-<?php echo $post_id; ?>">
                            <div class="post-info">
                                <div class="post-user">
                                    <?php if (empty($rowDetails['image_user'])) { ?>
                                        <img src="images/icons/usr.png">
                                    <?php } else { ?>
                                        <img src="images/users/<?php echo $rowDetails['image_user']; ?>">
                                    <?php } ?>
                                    <h2><a href="#"><?php echo $name; ?></a></h2>
                                </div>
                                <p><?php echo $time; ?></p>
                            </div>
                            <div class="post-data">
                                <p><?php echo $description; ?></p>
                            </div>
                            <?php if (count($files) > 0) { ?>
                                <div class="post-img">
                                    <?php foreach ($files as $file) { ?>
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
                                    modal.onclick = function () {
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
                                    <?php
                                    // Consultar si el usuario ha dado like
                                    $queryLike = "SELECT * FROM post_likes WHERE post_id='$post_id' AND user_id='$id'";
                                    $resultLike = mysqli_query($conn, $queryLike);

                                    if (!$resultLike) {
                                        die("Error en la consulta de likes: " . mysqli_error($conn));
                                    }

                                    $liked = mysqli_num_rows($resultLike) > 0;

                                    // Contar número de likes
                                    $queryCountLikes = "SELECT COUNT(*) AS total_likes FROM post_likes WHERE post_id='$post_id'";
                                    $resultCountLikes = mysqli_query($conn, $queryCountLikes);

                                    if (!$resultCountLikes) {
                                        die("Error al contar likes: " . mysqli_error($conn));
                                    }

                                    $countLikes = mysqli_fetch_assoc($resultCountLikes)['total_likes'];
                                    ?>
                                    <button class="btn-like" onclick="toggleLike(<?php echo $post_id; ?>)">
                                        <i class='bx <?php echo $liked ? 'bxs-heart' : 'bx-heart'; ?>' style='color:#02b03a'></i>
                                    </button>
                                    <span style="color:#02b03a;" id="like-count-<?php echo $post_id; ?>"><?php echo $countLikes; ?></span>
                                    <button class="btn-comment">
                                        <i class='bx bxs-comment' style='color:#02b03a'></i>
                                    </button>
                                </div>
                                <div class="dropdown">
                                    <button class="dropbtn">
                                        <i class='bx bx-dots-vertical-rounded' style='color:#02b03a'></i>
                                    </button>
                                    <div class="dropdown-content">
                                        <button onclick="editPost(<?php echo $post_id; ?>)">
                                            <i class='bx bxs-pencil' style='color:#02b03a'></i> Editar
                                        </button>
                                        <form action="actions/delete_post.php" method="POST">
                                            <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                                            <button type="submit" name="delete-post">
                                                <i class='bx bxs-trash' style='color:#02b03a'></i> Borrar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <hr style="color: #164B60; width: 100%;">
                        </div>
                    <?php
                        }
                    }
                ?>
                <script>
                    function toggleLike(postId) {
                        var likeButton = document.querySelector('#post-' + postId + ' .btn-like i');
                        var liked = likeButton.classList.contains('bxs-heart');
                        var formData = new FormData();
                        formData.append('post_id', postId);
                        formData.append('user_id', <?php echo $id; ?>);

                        sendLikeRequest(formData, postId, liked);
                    }

                    function sendLikeRequest(formData, postId, liked) {
                        fetch('like_post.php', {
                            method: 'POST',
                            body: formData
                        }).then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        }).then(data => {
                            if (!data.success) {
                                throw new Error(data.message || 'Error en la solicitud');
                            }
                            var likeButton = document.querySelector('#post-' + postId + ' .btn-like i');
                            var likeCountSpan = document.getElementById('like-count-' + postId);
                            if (liked) {
                                likeButton.classList.remove('bxs-heart');
                                likeButton.classList.add('bx-heart');
                                likeCountSpan.textContent = parseInt(likeCountSpan.textContent) - 1;
                            } else {
                                likeButton.classList.remove('bx-heart');
                                likeButton.classList.add('bxs-heart');
                                likeCountSpan.textContent = parseInt(likeCountSpan.textContent) + 1;
                            }
                        }).catch(error => {
                            console.error('Error:', error);
                            showNotification('Error al procesar la solicitud. Por favor, inténtelo de nuevo más tarde.', 'error');
                        });
                    }

                    function editPost(postId) {
                        // Implementar la lógica para editar la publicación
                        
                    }

                    function showNotification(message, type) {
                        var notification = document.createElement('div');
                        notification.className = 'notification ' + type;
                        notification.textContent = message;
                        document.body.appendChild(notification);
                        setTimeout(function() {
                            notification.remove();
                        }, 3000);
                    }
                </script>
</div>
    