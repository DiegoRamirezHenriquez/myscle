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
<div class="home-container">
    <div class="profile-posts-container" id="profile-posts-container">
        <?php
        $queryID = "SELECT id FROM usuarios WHERE email = '$email'";
        $resultID = mysqli_query($conn, $queryID);
        $idNow = mysqli_fetch_assoc($resultID)['id'];
        

        $query_posts = "SELECT * FROM users_post 
    WHERE id_usuario IN (
        SELECT followed_id FROM follows 
        WHERE follower_id = (
            SELECT id FROM usuarios WHERE email = '$email'
        )
    ) 
    ORDER BY time DESC";

$result_posts = mysqli_query($conn, $query_posts);

// Validación correcta
if ($result_posts && mysqli_num_rows($result_posts) > 0) {
    while ($post = mysqli_fetch_assoc($result_posts)) {
                ?>
                <div class="post" id="post-<?php echo $post['id']; ?>">
                                <div class="post-info">
                                    <div class="post-user">
                                        <?php 
                                            // Obtener la imagen del usuario
                                        $queryDetails = "SELECT image_user FROM details_usuarios WHERE id_usuarios = '$post[id_usuario]'";
                                        $resultDetails = mysqli_query($conn, $queryDetails);
                                        if (!$resultDetails) {
                                            die("Error al consultar la imagen del usuario: " . mysqli_error($conn));
                                        }
                                        $rowDetails = mysqli_fetch_assoc($resultDetails);

                                        if (empty($rowDetails['image_user'])) { ?>
                                            <img src="images/icons/usr.png">
                                        <?php } else { ?>
                                            <img src="images/users/<?php echo $rowDetails['image_user']; ?>">
                                        <?php } 
                                        
                                        $queryName = "SELECT name FROM usuarios WHERE id = '$post[id_usuario]'";
                                        $resultName = mysqli_query($conn, $queryName);
                                        if (!$resultName) {
                                            die("Error al consultar el nombre del usuario: " . mysqli_error($conn));
                                        }
    
                                        $name = mysqli_fetch_assoc($resultName)['name'];
                                        ?>
                                        
                                        <h2><a href="perfiles.php?id=<?php  echo $post['id_usuario']  ?>"><?php echo $name; ?></a></h2>
                                    </div>
                                    <p><?php echo $post['time']; ?></p>
                                </div>
                                <div class="post-data">
                                    <p><?php echo $post['post']; ?></p>
                                </div>
                                <?php 
                                
                                $queryFiles = "SELECT * FROM files_post WHERE post_id='$post[id]'";
                                $resultFiles = mysqli_query($conn, $queryFiles);
    
                                if (!$resultFiles) {
                                    die("Error en la consulta de archivos: " . mysqli_error($conn));
                                }
    
                                $files = [];
                                while ($rowFiles = mysqli_fetch_assoc($resultFiles)) {
                                    $files[] = $rowFiles['files'];
                                }
                                if (count($files) > 0) { ?>
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
                                        $queryLike = "SELECT * FROM post_likes WHERE post_id='$post[id]' AND user_id='$idNow'";
                                        $resultLike = mysqli_query($conn, $queryLike);
    
                                        if (!$resultLike) {
                                            die("Error en la consulta de likes: " . mysqli_error($conn));
                                        }
    
                                        $liked = mysqli_num_rows($resultLike) > 0;
                                        $queryCountLikes = "SELECT COUNT(*) AS total_likes FROM post_likes WHERE post_id='$post[id]'";
                                        $resultCountLikes = mysqli_query($conn, $queryCountLikes);
                                        $countLikes = mysqli_fetch_assoc($resultCountLikes)['total_likes'];
    
                                        if ($liked) {
                                        ?>
                                            <button class="btn-like" onclick="toggleLike(<?php echo $post['id']; ?>)">
                                                <i class='bx bxs-heart' style='color:#02b03a'></i>
                                            </button>
                                            <span style="color:#02b03a;" id="like-count-<?php echo $post['id']; ?>"><?php echo $countLikes; ?></span>
                                        <?php }else{
                                        ?>
                                            <button class="btn-like" onclick="toggleLike(<?php echo $post['id']; ?>)">
                                                <i class='bx bx-heart' style='color:#02b03a'></i>
                                            </button>
                                            <span style="color: #02b03a;" id="like-count-<?php echo $post['id']; ?>"><?php echo $countLikes; ?></span>
                                        <?php } ?>
                                        <button class="btn-comment">
                                            <i class='bx bxs-comment' style='color:#02b03a'></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <hr style="color: #164B60; width: 100%;">
                <?php
            }
        } else {
            ?>
            <script>
                let editDiv = document.getElementById('profile-posts-container');

                editDiv.style.textAlign="center";
            </script>
        <?php
            echo "
             <hr style='color: #164B60; width: 100%;'>
            <p>No hay publicaciones.</p> <br>
            <p>Sigue a mas personas <a href='explorar.php' style='color:white'>Aqui en explorar</a></p>
            <hr style='color: #164B60; width: 100%;'>
            ";
        }
        
        ?>
    </div>

    <script>
function toggleLike(postId) {
    var likeButton = document.querySelector('#post-' + postId + ' .btn-like i');
    var liked = likeButton.classList.contains('bxs-heart');
    var formData = new FormData();
    formData.append('post_id', postId);
    formData.append('user_id', <?php echo $idNow; ?>);
    
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
    </script>
</div>

<?php
include("layout/footer.php");

?>