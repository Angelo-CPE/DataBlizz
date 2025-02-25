<?php session_start(); ?>

<!DOCTYPE html>
<html lang = "en">
<head>
    <meta charset = "UTF-8">
    <meta name = "viewport" content = "width=device-width, initial-scale = 1.0">
    <title>DataBlizz</title>
    <link rel = "stylesheet" href = "https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src = "https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        
        function toggleForm() {
            var form = document.getElementById("orderForm");
            form.style.display = (form.style.display === "none" || form.style.display === "") ? "block" : "none";
        }

        function searchOrders() {
            let input = document.getElementById("searchInput").value.toLowerCase();
            let table = document.querySelector("table tbody");
            let rows = table.getElementsByTagName("tr");

            for (let row of rows) {
                let cells = row.getElementsByTagName("td");
                let match = false;

                // Only check the first six columns (ID, Name, Email, Phone, Product Name, Product ID)
                let searchableIndexes = [0, 1, 2, 3, 4, 5]; // Column indexes to search in

                for (let index of searchableIndexes) {
                    let text = cells[index].innerText.toLowerCase();
                    if (text.includes(input)) {
                        match = true;
                        break;
                    }
                }

                row.style.display = match ? "" : "none";
            }
        }

        function enableEdit(rowId) {
            let row = document.getElementById("row_" + rowId);
            let cells = row.getElementsByClassName("editable");
            
            for (let cell of cells) {
                let value = cell.innerText;
                cell.innerHTML = `<input type = 'text' class = 'form-control' value = '${value}'>`;
            }

            document.getElementById("edit_" + rowId).style.display = "none";
            document.getElementById("save_" + rowId).style.display = "inline-block";
        }

        function saveEdit(rowId) {
            let row = document.getElementById("row_" + rowId);
            let inputs = row.getElementsByTagName("input");

            let customer_name = inputs[0].value;
            let email = inputs[1].value;
            let phone = inputs[2].value;
            let product_name = inputs[3].value;
            let product_id = inputs[4].value;

            let xhr = new XMLHttpRequest();
            xhr.open("POST", "update.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    if (xhr.responseText === "success") {
                        location.reload(); 
                    } else {
                        alert("Error updating order");
                    }
                }
            };

            xhr.send(`id=${rowId}&customer_name=${customer_name}&email=${email}&phone=${phone}&product_name=${product_name}&product_id=${product_id}`);
        }
    </script>
</head>
<body>
    <?php
    if (isset($_SESSION['message'])) {
        echo "<div class = 'alert alert-{$_SESSION['alert_type']} alert-dismissible fade show' role='alert'>
                {$_SESSION['message']}
                <button type = 'button' class = 'btn-close' data-bs-dismiss = 'alert' aria-label = 'Close'></button>
            </div>";
        unset($_SESSION['message']); 
        unset($_SESSION['alert_type']);
    }
    ?>

    <div class = "container my-5">
        <h1>WELCOME TO DATABLIZZ</h1>
        <h3>List of Orders</h3>
        <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search orders..." onkeyup="searchOrders()">
        <button class = "btn btn-primary" onclick = "toggleForm()">New Order</button>
        <br>

        <div id = "orderForm" style = "display: none;">
            <form action = "create.php" method="post">
                <div class = "mb-3">
                    <label class = "form-label">Name</label>
                    <input type = "text" class = "form-control" name = "customer_name" required>
                </div>
                <div class = "mb-3">
                    <label class = "form-label">Email</label>
                    <input type = "email" class = "form-control" name = "email" required>
                </div>
                <div class = "mb-3">
                    <label class = "form-label">Phone</label>
                    <input type = "text" class = "form-control" name = "phone" required>
                </div>
                <div class = "mb-3">
                    <label class = "form-label">Product Name</label>
                    <input type = "text" class = "form-control" name = "product_name" required>
                </div>
                <div class = "mb-3">
                    <label class = "form-label">Product ID</label>
                    <input type = "text" class = "form-control" name = "product_id" required>
                </div>
                <button type = "submit" class = "btn btn-success">Submit Order</button>
            </form>
        </div>

        <table class = "table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Product Name</th>
                    <th>Product ID</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $database = "datablizz";

                    $connection = new mysqli($servername, $username, $password, $database);

                    if ($connection -> connect_error){
                        die("Connection Failure: " . $connection -> connect_error);
                    }

                    $sql = "SELECT * FROM orders";
                    $result = $connection -> query($sql);

                    if (!$result){
                        die("Query Failed: " . $connection -> error);
                    }
                    
                    while ($row = $result -> fetch_assoc()) {
                        echo "
                        <tr id = 'row_$row[id]'>
                            <td>$row[id]</td>
                            <td class = 'editable'>$row[customer_name]</td>
                            <td class = 'editable'>$row[email]</td>
                            <td class = 'editable'>$row[phone]</td>
                            <td class = 'editable'>$row[product_name]</td>
                            <td class = 'editable'>$row[product_id]</td>
                            <td>$row[created_at]</td>
                            <td>
                                <button class = 'btn btn-primary btn-sm' id = 'edit_$row[id]' onclick = 'enableEdit($row[id])'>Edit</button>
                                <button class = 'btn btn-success btn-sm' id = 'save_$row[id]' onclick = 'saveEdit($row[id])' style = 'display:none;'>Update</button>
                                <a class='btn btn-danger btn-sm' href='delete.php?id=$row[id]'>Delete</a>
                            </td>
                        </tr>";
                    }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
