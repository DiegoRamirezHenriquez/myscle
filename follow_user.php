<?php
session_start();
include 'conexion.php'; // Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos enviados desde el cliente
    $follower_id = intval($_POST['follower_id']);
    $followed_id = intval($_POST['user_id']);

    if ($follower_id === $followed_id) {
        echo json_encode(['success' => false, 'message' => 'No puedes seguirte a ti mismo.']);
        exit;
    }

    // Consultar si ya existe el seguimiento
    $queryCheckFollow = "SELECT 1 FROM follows WHERE follower_id = ? AND followed_id = ?";
    $stmtCheckFollow = $conn->prepare($queryCheckFollow);
    $stmtCheckFollow->bind_param('ii', $follower_id, $followed_id);
    $stmtCheckFollow->execute();
    $resultCheckFollow = $stmtCheckFollow->get_result();

    if ($resultCheckFollow->num_rows > 0) {
        // Si ya sigue, eliminar el seguimiento (unfollow)
        $queryUnfollow = "DELETE FROM follows WHERE follower_id = ? AND followed_id = ?";
        $stmtUnfollow = $conn->prepare($queryUnfollow);
        $stmtUnfollow->bind_param('ii', $follower_id, $followed_id);

        if ($stmtUnfollow->execute()) {
            echo json_encode(['success' => true, 'message' => 'Has dejado de seguir al usuario.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al dejar de seguir: ' . $conn->error]);
        }
    } else {
        // Si no sigue, agregar el seguimiento (follow)
        $queryFollow = "INSERT INTO follows (follower_id, followed_id) VALUES (?, ?)";
        $stmtFollow = $conn->prepare($queryFollow);
        $stmtFollow->bind_param('ii', $follower_id, $followed_id);

        if ($stmtFollow->execute()) {
            echo json_encode(['success' => true, 'message' => 'Ahora sigues al usuario.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al seguir: ' . $conn->error]);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}
?>
