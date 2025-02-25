<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$database = "datablizz";

$connection = new mysqli($servername, $username, $password, $database);

if ($connection -> connect_error) {
    die("Connection failed: " . $connection -> connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_name = $_POST["customer_name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $product_name = $_POST["product_name"];
    $product_id = $_POST["product_id"];

    // Check for duplicate email
    $checkEmail = "SELECT * FROM orders WHERE email = '$email'";
    $result = $connection -> query($checkEmail);

    if ($result -> num_rows > 0) {
        $_SESSION['message'] = "Error: Email already exists!";
        $_SESSION['alert_type'] = "danger";
    } else {
        $sql = "INSERT INTO orders (customer_name, email, phone, product_name, product_id) 
                VALUES ('$customer_name', '$email', '$phone', '$product_name', '$product_id')";

        if ($connection -> query($sql) === TRUE) {
            $_SESSION['message'] = "Order successfully created!";
            $_SESSION['alert_type'] = "success";
        } else {
            $_SESSION['message'] = "Error: " . $connection -> error;
            $_SESSION['alert_type'] = "danger";
        }
    }

    $connection -> close();
    header("Location: index.php");
    exit;
}
?>