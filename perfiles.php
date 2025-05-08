<?php
include("layout/header.php");
include("conexion.php");
$_SESSION['email'] = $email;
if(isset($_SESSION['email'])){
    $email = $_SESSION['email'];
}else{
    header("Location: login.php");
}
//usuario logueado encontrar id
$sql = "SELECT id FROM usuarios WHERE email='$email'";
$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Error en la consulta: " . mysqli_error($conn));
}
$row = mysqli_fetch_assoc($result);
$id_usuario = $row['id'];
$rowDetails;
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Sanitizar ID

    
    // Consultar detalles del usuario
    $sql = "SELECT * FROM details_usuarios WHERE id_usuarios = $id";
    $resultado = $conn->query($sql);
    if ($resultado && $resultado->num_rows > 0) {
        $rowDetails = $resultado->fetch_assoc();

        // Asignar valores de los detalles del usuario
        $age = isset($rowDetails['age']) ? intval($rowDetails['age']) : 'No especificado';
        $gender = isset($rowDetails['gender']) ? htmlspecialchars($rowDetails['gender'], ENT_QUOTES, 'UTF-8') : 'No especificado';
        $height = isset($rowDetails['height']) ? htmlspecialchars($rowDetails['height'], ENT_QUOTES, 'UTF-8') : 'No especificado';
        $weight = isset($rowDetails['weight']) ? htmlspecialchars($rowDetails['weight'], ENT_QUOTES, 'UTF-8') : 'No especificado';
        $queryName = "SELECT name FROM usuarios WHERE id='$id'";
        $resultName = mysqli_query($conn, $queryName);
        if (!$resultName) {
            die("Error en la consulta del nombre: " . mysqli_error($conn));
        }
        $name = mysqli_fetch_assoc($resultName)['name'];
?>
<div class="profile-container ">
    <div class="profile-container-info ">
        <div class="profile-content info-externo">
            <h1>Perfil</h1>
            <div class="profile-info ">
                <div class="profile-img">
                    <?php if (!empty($rowDetails['image_user'])) { ?>
                        <img src="images/users/<?php echo htmlspecialchars($rowDetails['image_user'], ENT_QUOTES, 'UTF-8'); ?>" alt="User Image">
                    <?php } else { ?>
                        <img src="images/icons/usr.png" alt="Default User Icon">
                    <?php } ?>
                </div>
                <div class="profile-data">
                    <h2><?php echo $name; ?></h2>
                    <p>Edad: <?php echo $age; ?></p>
                    <p>Género: <?php echo $gender; ?></p>
                    <p>Altura: <?php echo $height; ?></p>
                    <p>Peso: <?php echo $weight; ?></p>
                </div>
            </div>
            <!-- seguimiento entre usuarios -->
            <div class="profile-followers">
                <?php
                // Consultar seguidores y seguidos
                $queryFollowers = "SELECT COUNT(*) AS total_followers FROM follows WHERE followed_id='$id'";
                $resultFollowers = mysqli_query($conn, $queryFollowers);
                if (!$resultFollowers) {
                    die("Error en la consulta de seguidores: " . mysqli_error($conn));
                }
                $totalFollowers = mysqli_fetch_assoc($resultFollowers)['total_followers'];


                $queryFollowing = "SELECT COUNT(*) AS total_following FROM follows WHERE follower_id='$id'";
                $resultFollowing = mysqli_query($conn, $queryFollowing);
                if (!$resultFollowing) {
                    die("Error en la consulta de seguidos: " . mysqli_error($conn));
                }
                $totalFollowing = mysqli_fetch_assoc($resultFollowing)['total_following'];
                ?>
                <p><?php echo $totalFollowers; ?> <br>Seguidores</p>
                <p><?php echo $totalFollowing; ?><br>Siguiendo</p>
                <?php
                // Consultar si el usuario logueado sigue al perfil que está viendo
                $queryCheckFollow = "SELECT * FROM follows WHERE follower_id='$id_usuario' AND followed_id='$id'";
                $resultCheckFollow = mysqli_query($conn, $queryCheckFollow);
                if (!$resultCheckFollow) {
                    die("Error en la consulta de seguimiento: " . mysqli_error($conn));
                }
                $isFollowing = mysqli_num_rows($resultCheckFollow) > 0;
                ?>
                <button class="btn-follow
                    <?php echo $isFollowing ? 'unfollow' : 'follow'; ?>" onclick="toggleFollow(<?php echo $id; ?>)">

                    <?php echo $isFollowing ? '<div class="svg-wrapper-1">
                        <div class="svg-wrapper">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(41, 149, 7, 1);"><path d="M14 11h8v2h-8zM4.5 8.552c0 1.995 1.505 3.5 3.5 3.5s3.5-1.505 3.5-3.5-1.505-3.5-3.5-3.5-3.5 1.505-3.5 3.5zM4 19h10v-1c0-2.757-2.243-5-5-5H7c-2.757 0-5 2.243-5 5v1h2z"></path></svg>
                        </div>
                    </div>
                    <span style="font-size: 12px;">
                        Dejar de seguir</span>' : '<div class="svg-wrapper-1">
                        <div class="svg-wrapper">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(41, 149, 7, 1);"><path d="M4.5 8.552c0 1.995 1.505 3.5 3.5 3.5s3.5-1.505 3.5-3.5-1.505-3.5-3.5-3.5-3.5 1.505-3.5 3.5zM19 8h-2v3h-3v2h3v3h2v-3h3v-2h-3zM4 19h10v-1c0-2.757-2.243-5-5-5H7c-2.757 0-5 2.243-5 5v1h2z"></path></svg>
                        </div>
                    </div>
                    <span>
                        Seguir</span>'; ?>
                    
                </button>
                <script>
                    function toggleFollow(userId) {
                        var followButton = document.querySelector('.btn-follow');
                        var isFollowing = followButton.classList.contains('unfollow');
                        var formData = new FormData();
                        formData.append('user_id', userId);
                        formData.append('follower_id', <?php echo $id_usuario; ?>);

                        sendFollowRequest(formData, isFollowing);
                    }

                    function sendFollowRequest(formData, isFollowing) {
                        fetch('follow_user.php', {
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
                            var followButton = document.querySelector('.btn-follow');
                            if (isFollowing) {
                                followButton.classList.remove('unfollow');
                                followButton.classList.add('follow');
                                followButton.innerHTML = `<div class="svg-wrapper-1">
                        <div class="svg-wrapper">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(41, 149, 7, 1);"><path d="M4.5 8.552c0 1.995 1.505 3.5 3.5 3.5s3.5-1.505 3.5-3.5-1.505-3.5-3.5-3.5-3.5 1.505-3.5 3.5zM19 8h-2v3h-3v2h3v3h2v-3h3v-2h-3zM4 19h10v-1c0-2.757-2.243-5-5-5H7c-2.757 0-5 2.243-5 5v1h2z"></path></svg>
                        </div>
                    </div>
                    <span>
                        Seguir</span>`;
                                updateFollowCounts(-1, 0);
                            } else {
                                followButton.classList.remove('follow');
                                followButton.classList.add('unfollow');
                                followButton.innerHTML = `<div class="svg-wrapper-1">
                        <div class="svg-wrapper">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(41, 149, 7, 1);"><path d="M14 11h8v2h-8zM4.5 8.552c0 1.995 1.505 3.5 3.5 3.5s3.5-1.505 3.5-3.5-1.505-3.5-3.5-3.5-3.5 1.505-3.5 3.5zM4 19h10v-1c0-2.757-2.243-5-5-5H7c-2.757 0-5 2.243-5 5v1h2z"></path></svg>
                        </div>
                    </div>
                    <span style="font-size: 12px;">
                        Dejar de seguir</span>`;
                                updateFollowCounts(1, 0);
                            }
                        }).catch(error => {
                            console.error('Error:', error);
                            showNotification('Error al procesar la solicitud. Por favor, inténtelo de nuevo más tarde.', 'error');
                        });
                    }

                    function updateFollowCounts(followerChange, followingChange) {
                        var followersElement = document.querySelector('.profile-followers p:nth-child(1)');
                        var followingElement = document.querySelector('.profile-followers p:nth-child(2)');
                        
                        var currentFollowers = parseInt(followersElement.textContent.trim()) || 0;
                        var currentFollowing = parseInt(followingElement.textContent.trim()) || 0;
                        followersElement.innerHTML = (currentFollowers + followerChange) + '<br>Seguidores';
                        followingElement.innerHTML = (currentFollowing + followingChange) + '<br>Siguiendo';
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
    </div>
    <div class="profile-posts-container">
                <div class="profile-posts">
                <?php
                
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
                                    $queryLike = "SELECT * FROM post_likes WHERE post_id='$post_id' AND user_id='$id_usuario'";
                                    $resultLike = mysqli_query($conn, $queryLike);

                                    if (!$resultLike) {
                                        die("Error en la consulta de likes: " . mysqli_error($conn));
                                    }

                                    $liked = mysqli_num_rows($resultLike) > 0;
                                    echo "<script>console.log('Liked: " . ($liked ? 'true' : 'false') . " $id');</script>";

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
    <?php
    } else {
        echo "No se encontró el usuario.";
        exit;
    }
} else {
    echo "ID de usuario no proporcionado.";
    exit;
}
    
    ?>