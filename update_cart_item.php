<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "register_data";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $productId = $input['id'];
    $action = $input['action'];
    $sessionId = session_id();

    if ($action === 'increment') {
        $sql = "UPDATE cart SET quantity = quantity + 1 WHERE session_id = '$sessionId' AND product_id = $productId";
    } elseif ($action === 'decrement') {
        $sql = "UPDATE cart SET quantity = quantity - 1 WHERE session_id = '$sessionId' AND product_id = $productId AND quantity > 1";
    }

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update cart item']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}

$conn->close();
?>