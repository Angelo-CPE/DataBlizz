<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "datablizz";

$connection = new mysqli($servername, $username, $password, $database);

if ($connection->connect_error) {
    die("Connection Failure: " . $connection->connect_error);
}

if (isset($_GET["id"])) {
    $id = $_GET["id"];

    // Delete the order
    $sql = "DELETE FROM orders WHERE id = ?";
    $stmt = $connection->prepare($sql);
    $stmt -> bind_param("i", $id);

    if ($stmt->execute()) {
        // Check if the table is now empty
        $checkEmpty = "SELECT COUNT(*) as total FROM orders";
        $result = $connection->query($checkEmpty);
        $row = $result->fetch_assoc();

        if ($row['total'] == 0) {
            // Reset auto-increment if table is empty
            $connection->query("ALTER TABLE orders AUTO_INCREMENT = 1");
        }

        // Redirect back to the main page
        header("Location: index.php");
        exit();
    } else {
        echo "Error deleting order: " . $connection->error;
    }
}
?>