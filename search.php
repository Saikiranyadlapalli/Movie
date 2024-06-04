<?php
include 'connect.php';
session_start();

// OMDB API key
$apiKey = '8c0d754a';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['q'])) {
    $query = urlencode($_GET['q']);
    $url = "http://www.omdbapi.com/?apikey=$apiKey&s=$query";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    header('Content-Type: application/json');
    echo $response;
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['add_to_list']) && isset($_SESSION['email'])) {
        $email = $_SESSION['email'];
        $imdbID = $data['imdbID'];
        $title = $data['title'];
        $year = $data['year'];
        $poster = $data['poster'];

        $stmt = $conn->prepare("INSERT INTO user_movie_list (user_email, imdb_id, title, year, poster) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $email, $imdbID, $title, $year, $poster);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to add movie to list']);
        }
        
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    }
}
?>
