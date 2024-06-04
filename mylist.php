<?php
include 'connect.php';
session_start();

if (!isset($_SESSION['email'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit();
}

$email = $_SESSION['email'];
$query = $conn->prepare("SELECT * FROM user_movie_list WHERE user_email = ?");
$query->bind_param("s", $email);
$query->execute();
$result = $query->get_result();

$movies = [];
while ($row = $result->fetch_assoc()) {
    $movies[] = $row;
}

echo json_encode(['status' => 'success', 'movies' => $movies]);

$query->close();
?>
