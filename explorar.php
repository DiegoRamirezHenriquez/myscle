<?php
include("layout/header.php");
include("conexion.php");
$_SESSION['email'] = $email;
if(isset($_SESSION['email'])){
    $email = $_SESSION['email'];
}else{
    header("Location: login.php");
}
$query = "SELECT * FROM usuarios WHERE email='$email'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$idUsuario = $row['id'];
?>


<div class="contenedor-explorar">
    <nav class="contenedor-opciones-explorar">
        <span class="opciones-explorar">Mas gustados</span>
        <span class="opciones-explorar">Nuevos</span>
        <span class="opciones-explorar">Siguiendo</span>
    </nav>

    <div class="contenedor-publicaciones-explorar">
        <div class="contenedor-publicaciones-gustados" id="gustados">
                <?php
                $query="SELECT p.post, p.time, u.name, u.id, p.id AS id_post, COUNT(pl.post_id) AS total_likes, d.image_user FROM users_post p LEFT JOIN usuarios u ON u.id = p.id_usuario LEFT JOIN details_usuarios d ON d.id_usuarios = u.id LEFT JOIN post_likes pl ON pl.post_id = p.id GROUP BY p.id, p.post, p.time, u.name, u.id, d.image_user ORDER BY total_likes DESC;";
                $result=mysqli_query($conn,$query);
                while($postExplorar=mysqli_fetch_assoc($result)){
                    $post_id = $postExplorar['id_post'];
                    $description = $postExplorar['post'];
                    $time = $postExplorar['time'];
                    $id = $postExplorar['id'];
                    $name = $postExplorar['name'];
                    $image_user = $postExplorar['image_user'];
                        // Consultar archivos asociados a la publicación
                    $queryFiles = "SELECT * FROM files_post WHERE post_id='$post_id'";
                    $resultFiles = mysqli_query($conn, $queryFiles);
                    if (!$resultFiles) {
                        die("Error en la consulta de archivos: " . mysqli_error($conn));
                    }
                    $files = [];
                    while ($postExplorarFiles = mysqli_fetch_assoc($resultFiles)) {
                        $files[] = $postExplorarFiles['files'];
                    }
                    ?>
                        <div class="post-explorar" id="post-<?php echo $post_id; ?>-gustados">
                            <div class="post-info-explorar">
                                <div class="post-user">
                                    <?php if (empty($image_user)) { ?>
                                        <img src="images/icons/usr.png">
                                    <?php } else { ?>
                                        <img src="images/users/<?php echo $image_user; ?>">
                                    <?php } ?>

                                    <?php if ($id == $idUsuario) { ?>
                                        <h2><a href="profile.php"><?php echo $name; ?></a></h2>
                                    <?php } else { ?>
                                        <h2><a href="perfiles.php?id=<?php echo $id; ?>"><?php echo $name; ?></a></h2>
                                    <?php } ?>
                                </div>
                                <?php
                                date_default_timezone_set('America/Santiago');
                                        $postTime = strtotime($time);
                                        $currentTime = time();
                                        $timeDifference = $currentTime - $postTime;

                                        if ($timeDifference < 60) {
                                            $timeAgo = $timeDifference . " segundos";
                                        } elseif ($timeDifference < 3600) {
                                            $timeAgo = floor($timeDifference / 60) . " minutos";
                                        } elseif ($timeDifference < 86400) {
                                            $timeAgo = floor($timeDifference / 3600) . " horas";
                                        } elseif ($timeDifference < 604800) {
                                            $timeAgo = floor($timeDifference / 86400) . " días";
                                        } else {
                                            $timeAgo = date("d M Y", $postTime);
                                        }
                                    ?>
                                    <p><?php echo $timeAgo; ?></p>
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
                            <div class="post-options">
                                <div class="post-buttons">
                                    <?php
                                    // Consultar si el usuario ha dado like
                                    $queryLike = "SELECT * FROM post_likes WHERE post_id='$post_id' AND user_id='$idUsuario'";
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
                                    <button class="btn-like" onclick="toggleLike(<?php echo $post_id; ?>, 'gustados')">
                                        <i class='bx <?php echo $liked ? 'bxs-heart' : 'bx-heart'; ?>' style='color:#02b03a'></i>
                                    </button>
                                    <span style="color:#02b03a;" id="like-count-<?php echo $post_id; ?>-gustados"><?php echo $countLikes; ?></span>
                                    <button class="btn-comment">
                                        <i class='bx bxs-comment' style='color:#02b03a'></i>
                                    </button>
                                </div>
                            </div>
                            <hr style="color: #164B60; width: 100%;">
                        </div>
                    <?php
                }
                ?>
        </div>
        <div class="contenedor-publicaciones-nuevos" id="nuevos">
            <?php
                $query="SELECT p.post, p.time, u.name, u.id, p.id as id_post  FROM users_post p LEFT JOIN usuarios u ON u.id = p.id_usuario LEFT JOIN post_likes pl ON pl.post_id = p.id GROUP BY p.id, p.post, p.time, u.name, u.id, p.id ORDER BY p.time DESC";
                $result=mysqli_query($conn,$query);
                while($postExplorar=mysqli_fetch_assoc($result)){
                    $post_id = $postExplorar['id_post'];
                    $description = $postExplorar['post'];
                    $time = $postExplorar['time'];
                    $id = $postExplorar['id'];
                    $name = $postExplorar['name'];
                    // Consultar archivos asociados a la publicación
                    $queryFiles = "SELECT * FROM files_post WHERE post_id='$post_id'";
                    $resultFiles = mysqli_query($conn, $queryFiles);
                    if (!$resultFiles) {
                        die("Error en la consulta de archivos: " . mysqli_error($conn));
                    }
                    $files = [];
                    while ($postExplorarFiles = mysqli_fetch_assoc($resultFiles)) {
                        $files[] = $postExplorarFiles['files'];
                    }
                    // consiguiendo la imagen del usuario
                    $queryDetails = "SELECT image_user FROM details_usuarios WHERE id_usuarios='$id'";
                    $resultDetails = mysqli_query($conn, $queryDetails);
                    if (!$resultDetails) {
                        die("Error en la consulta de detalles de usuario: " . mysqli_error($conn));
                    }
                    $postExplorarDetails = mysqli_fetch_assoc($resultDetails);
                    // Verificar si se obtuvo la imagen del usuario
                    if ($postExplorarDetails) {
                        $image_user = $postExplorarDetails['image_user'];
                    } else {
                        $image_user = ""; 
                    }

                    ?>
                        <div class="post-explorar" id="post-<?php echo $post_id . "-nuevos"; ?>">
                            <div class="post-info-explorar">
                                <div class="post-user">
                                    <?php if (empty($image_user)) { ?>
                                        <img src="images/icons/usr.png">
                                    <?php } else { ?>
                                        <img src="images/users/<?php echo $image_user; ?>">
                                    <?php } ?>

                                    <?php if ($id == $idUsuario) { ?>
                                        <h2><a href="profile.php"><?php echo $name; ?></a></h2>
                                    <?php } else { ?>
                                        <h2><a href="perfiles.php?id=<?php echo $id; ?>"><?php echo $name; ?></a></h2>
                                    <?php } ?>
                                </div>
                                <?php
                                date_default_timezone_set('America/Santiago');
                                        $postTime = strtotime($time);
                                        $currentTime = time();
                                        $timeDifference = $currentTime - $postTime;

                                        if ($timeDifference < 60) {
                                            $timeAgo = $timeDifference . " segundos";
                                        } elseif ($timeDifference < 3600) {
                                            $timeAgo = floor($timeDifference / 60) . " minutos";
                                        } elseif ($timeDifference < 86400) {
                                            $timeAgo = floor($timeDifference / 3600) . " horas";
                                        } elseif ($timeDifference < 604800) {
                                            $timeAgo = floor($timeDifference / 86400) . " días";
                                        } else {
                                            $timeAgo = date("d M Y", $postTime);
                                        }
                                    ?>
                                    <p><?php echo $timeAgo; ?></p>
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
                                    $queryLike = "SELECT * FROM post_likes WHERE post_id='$post_id' AND user_id='$idUsuario'";
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

                                    $postIDCheck = $post_id . "-nuevos"; // Cambié el nombre de la variable para evitar conflictos
                                    ?>
                                    <button class="btn-like" onclick="toggleLike(<?php echo $post_id; ?>, 'nuevos')">
                                        <i class='bx <?php echo $liked ? 'bxs-heart' : 'bx-heart'; ?>' style='color:#02b03a'></i>
                                    </button>
                                    <span style="color:#02b03a;" id="like-count-<?php echo $post_id; ?>-nuevos"><?php echo $countLikes; ?></span>
                                    <button class="btn-comment">
                                        <i class='bx bxs-comment' style='color:#02b03a'></i>
                                    </button>
                                </div>
                            </div>
                            <hr style="color: #164B60; width: 100%;">
                        </div>
                    <?php
                }
                ?>
        </div>
        <div class="contenedor-publicaciones-siguiendo" id="siguiendo">
            <?php
                $query="SELECT  p.post, p.time, u.name,  u.id , p.id AS id_post FROM users_post p JOIN usuarios u ON u.id = p.id_usuario JOIN follows f ON f.followed_id = u.id WHERE f.follower_id = '$idUsuario' GROUP BY p.id, p.post, p.time, u.name, u.id ORDER BY p.time DESC";
                $result=mysqli_query($conn,$query);
                while($postExplorar=mysqli_fetch_assoc($result)){
                    $post_id = $postExplorar['id_post'];
                    $description = $postExplorar['post'];
                    $time = $postExplorar['time'];
                    $id = $postExplorar['id'];
                    $name = $postExplorar['name'];
                        // Consultar archivos asociados a la publicación
                    $queryFiles = "SELECT * FROM files_post WHERE post_id='$post_id'";
                    $resultFiles = mysqli_query($conn, $queryFiles);
                    if (!$resultFiles) {
                        die("Error en la consulta de archivos: " . mysqli_error($conn));
                    }
                    $files = [];
                    while ($postExplorarFiles = mysqli_fetch_assoc($resultFiles)) {
                        $files[] = $postExplorarFiles['files'];
                    }
                    // consiguiendo la imagen del usuario
                    $queryDetails = "SELECT image_user FROM details_usuarios WHERE id_usuarios='$id'";
                    $resultDetails = mysqli_query($conn, $queryDetails);
                    if (!$resultDetails) {
                        die("Error en la consulta de detalles de usuario: " . mysqli_error($conn));
                    }
                    $postExplorarDetails = mysqli_fetch_assoc($resultDetails);
                    // Verificar si se obtuvo la imagen del usuario
                    if ($postExplorarDetails) {
                        $image_user = $postExplorarDetails['image_user'];
                    } else {
                        $image_user = ""; 
                    }
                    
                    ?>
                        <div class="post-explorar" id="post-<?php echo $post_id; ?>">
                            <div class="post-info-explorar">
                                <div class="post-user">
                                    <?php if (empty($image_user)) { ?>
                                        <img src="images/icons/usr.png">
                                    <?php } else { ?>
                                        <img src="images/users/<?php echo $image_user; ?>">
                                    <?php } 
                                    if ($id == $idUsuario) { ?>
                                        <h2><a href="profile.php"><?php echo $name; ?></a></h2>
                                    <?php } else { ?>
                                        <h2><a href="perfiles.php?id=<?php echo $id; ?>"><?php echo $name; ?></a></h2>
                                    <?php } ?>
                                </div>
                                <?php
                                    date_default_timezone_set('America/Santiago');
                                        $postTime = strtotime($time);
                                        $currentTime = time();
                                        $timeDifference = $currentTime - $postTime;

                                        if ($timeDifference < 60) {
                                            $timeAgo = $timeDifference . " segundos";
                                        } elseif ($timeDifference < 3600) {
                                            $timeAgo = floor($timeDifference / 60) . " minutos";
                                        } elseif ($timeDifference < 86400) {
                                            $timeAgo = floor($timeDifference / 3600) . " horas";
                                        } elseif ($timeDifference < 604800) {
                                            $timeAgo = floor($timeDifference / 86400) . " días";
                                        } else {
                                            $timeAgo = date("d M Y", $postTime);
                                        }
                                    ?>
                                    <p><?php echo $timeAgo; ?></p>
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
                            
                            <div class="post-options">
                                <div class="post-buttons">
                                    <?php
                                    // Consultar si el usuario ha dado like
                                    $queryLike = "SELECT * FROM post_likes WHERE post_id='$post_id' AND user_id='$idUsuario'";
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
                                    <button class="btn-like" onclick="toggleLike(<?php echo $post_id; ?>, 'siguiendo')">
                                        <i class='bx <?php echo $liked ? 'bxs-heart' : 'bx-heart'; ?>' style='color:#02b03a'></i>
                                    </button>
                                    <span style="color:#02b03a;" id="like-count-<?php echo $post_id; ?>-siguiendo"><?php echo $countLikes; ?></span>
                                    <button class="btn-comment">
                                        <i class='bx bxs-comment' style='color:#02b03a'></i>
                                    </button>
                                </div>
                            </div>
                            <hr style="color: #164B60; width: 100%;">
                        </div>
                    <?php
                }
                ?>
        </div>
    </div>


</div>
<script>
    document.getElementById('nuevos').style.display = 'none';
    document.getElementById('siguiendo').style.display = 'none';
    document.getElementById('gustados').style.display = 'flex';

    var opciones = document.querySelectorAll('.opciones-explorar');
    opciones.forEach(function(opcion) {
        opcion.addEventListener('click', function() {
            var id = this.textContent.trim().toLowerCase();
            id = id.replace(' ', '');
            id = id.replace('masgustados', 'gustados');
            document.getElementById('gustados').style.display = 'none';
            document.getElementById('nuevos').style.display = 'none';
            document.getElementById('siguiendo').style.display = 'none';
            document.getElementById(id).style.display = 'flex';
        });
    });

    function toggleLike(postId, section) {
        var likeButton = document.querySelector('#post-' + postId + '-' + section + ' .btn-like i');
        var liked = likeButton.classList.contains('bxs-heart');
        var formData = new FormData();
        formData.append('post_id', postId);
        formData.append('user_id', <?php echo $idUsuario; ?>);
        sendLikeRequest(formData, postId, liked, section);
    }
    function sendLikeRequest(formData, postId, liked, section) {
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
            var likeButton = document.querySelector('#post-' + postId + '-' + section + ' .btn-like i');
            var likeCountSpan = document.getElementById('like-count-' + postId + '-' + section);
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
    function showNotification(message, type) {
        var notification = document.createElement('div');
        notification.className = 'notification ' + type;
        notification.textContent = message;
        document.body.appendChild(notification);
        setTimeout(function() {
            notification.remove();
        }, 3000);
    }
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
<?php
include("layout/footer.php");