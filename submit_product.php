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
    $productName = $_POST['productName'];
    $productDescription = $_POST['productDescription'];
    $productPrice = $_POST['productPrice'];
    $productCategory = $_POST['productCategory']; // This will be the category_id
    $productStatus = isset($_POST['productStatus']) ? 'active' : 'inactive';
    
    // Handle product image upload
    $productImage = $_FILES['productImage']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($productImage);
    move_uploaded_file($_FILES['productImage']['tmp_name'], $target_file);
    
    $sql = "INSERT INTO products (name, description, price, image, status, category_id) 
            VALUES ('$productName', '$productDescription', '$productPrice', '$target_file', '$productStatus', '$productCategory')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Product added successfully";
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>
