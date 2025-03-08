<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "register_data";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $categoryName = $_POST['categoryName'];

    if (!empty($categoryName)) {
        $sql = "INSERT INTO categories (category_name) VALUES ('$categoryName')";
        if ($conn->query($sql) === TRUE) {
            echo "Category added successfully";
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "Category name cannot be empty";
    }
}

$conn->close();
?>

