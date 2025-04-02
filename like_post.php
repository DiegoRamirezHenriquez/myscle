<?php
session_start();
include 'conexion.php';  // Conexión a la base de datos

if (isset($_POST['post_id']) && isset($_POST['user_id'])) {
    $post_id = $_POST['post_id'];
    $user_id = $_POST['user_id'];

    // Verificar si el usuario ya dio "like"
    $queryCheck = "SELECT * FROM post_likes WHERE post_id='$post_id' AND user_id='$user_id'";
    $resultCheck = mysqli_query($conn, $queryCheck);

    if (!$resultCheck) {
        echo json_encode(['success' => false, 'message' => 'Error al consultar likes.']);
        exit();
    }

    if (mysqli_num_rows($resultCheck) > 0) {
        // Eliminar el like si ya se ha dado
        $query = "DELETE FROM post_likes WHERE post_id='$post_id' AND user_id='$user_id'";
        $liked = false;
    } else {
        // Agregar el like si no se ha dado
        $query = "INSERT INTO post_likes (post_id, user_id) VALUES ('$post_id', '$user_id')";
        $liked = true;
    }

    // Ejecutar la acción
    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar el like.']);
        exit();
    }

    // Contar el número de likes
    $queryCount = "SELECT COUNT(*) AS total_likes FROM post_likes WHERE post_id='$post_id'";
    $resultCount = mysqli_query($conn, $queryCount);

    if (!$resultCount) {
        echo json_encode(['success' => false, 'message' => 'Error al contar likes.']);
        exit();
    }

    $totalLikes = mysqli_fetch_assoc($resultCount)['total_likes'];

    // Responder con el estado del like y el total de likes
    echo json_encode(['success' => true, 'liked' => $liked, 'totalLikes' => $totalLikes]);
    exit();
}

echo json_encode(['success' => false, 'message' => 'Faltan parámetros.']);







