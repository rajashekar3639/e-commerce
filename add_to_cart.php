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
    $sessionId = session_id();

    // Check if the product is already in the cart
    $sql = "SELECT * FROM cart WHERE session_id = '$sessionId' AND product_id = $productId";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Update the quantity if the product is already in the cart
        $sql = "UPDATE cart SET quantity = quantity + 1 WHERE session_id = '$sessionId' AND product_id = $productId";
    } else {
        // Insert the product into the cart
        $sql = "INSERT INTO cart (session_id, product_id, quantity) VALUES ('$sessionId', $productId, 1)";
    }

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add product to cart']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}

$conn->close();
?>