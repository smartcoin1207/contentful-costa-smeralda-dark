<?php

require_once './_include/db.class.php';
$db = new Database();
$conn = $db->conn;

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Access the data sent via POST
    $article_id = $_POST['id'];

    if($article_id) {
        $sql = "UPDATE article SET hidden=1 WHERE id= " . $article_id . ";";
        if($conn->query($sql) == TRUE) {
            echo 'success';
        } else {
            echo 'failed1';
            http_response_code(400);
        }
    } else {
        http_response_code(400);
        echo 'failed';
    }
    
} else {
    // Handle non-POST requests
    http_response_code(405); // Method Not Allowed
    echo 'Only POST requests are allowed';
}
