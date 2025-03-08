<?php

$server = "localhost";
$username = "root";
$password = "";
$dbname = "register_data";


$con = mysqli_connect($server, $username, $password, $dbname);


if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idname = $_POST['idname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    $sql="INSERT INTO admintable (idname, email, phone, password) VALUES ('$idname', '$email', '$phone', '$password')";
    if ($con->query($sql) == TRUE) {
        echo "New record created successfully";
        header("Location: adminlog.html");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $con->error;
    }
} else {
    echo "Invalid request method.";
}

$con->close();
?>