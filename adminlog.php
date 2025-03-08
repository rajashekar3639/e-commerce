<?php
session_start();

$server = "localhost";
$username = "root";
$password = "";
$dbname = "register_data";

$con = mysqli_connect($server, $username, $password, $dbname);


if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

        $sql="SELECT * FROM admintable WHERE email = '$email' AND password ='$password'";
        $result=$con->query($sql);
        if($result->num_rows>0){
            header("Location: dashboard.php");
            exit();
        }
         else {
            echo "Invalid password.";
        }
}
$con->close();
?>