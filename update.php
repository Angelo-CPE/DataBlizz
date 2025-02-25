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
    $id = $_POST["id"];
    $customer_name = $_POST["customer_name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $product_name = $_POST["product_name"];
    $product_id = $_POST["product_id"];

    $sql = "UPDATE orders SET customer_name='$customer_name', email='$email', phone='$phone', product_name='$product_name', product_id='$product_id' WHERE id=$id";

    if ($connection -> query($sql) === TRUE) {
        $_SESSION['message'] = "Order updated successfully!";
        $_SESSION['alert_type'] = "success";
        echo "success";
    } else {
        $_SESSION['message'] = "Error updating order!";
        $_SESSION['alert_type'] = "danger";
        echo "error";
    }
}

$connection -> close();
?>