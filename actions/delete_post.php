<?php
    include ("../conexion.php");

    if(isset($_POST['delete-post'])){
        if(isset($_POST['post_id'])){
            $id = $_POST['post_id'];

            // Delete files associated with the post
            $stmt = $conn->prepare("SELECT files FROM files_post WHERE post_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $fileResult = $stmt->get_result();
            if($fileResult->num_rows > 0){
                while($row = $fileResult->fetch_assoc()){
                    $filePath = "../images/post-users/" . $row['files'];
                    if(file_exists($filePath)){
                        unlink($filePath);
                    }
                }
            }

            // Delete records from files_post
            $stmt = $conn->prepare("DELETE FROM files_post WHERE post_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();

            // Delete records from post_likes
            $stmt = $conn->prepare("DELETE FROM post_likes WHERE post_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();

            // Delete the post itself
            $stmt = $conn->prepare("DELETE FROM users_post WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();

            header("Location: ../profile.php");
            exit();
        }
    }
?>