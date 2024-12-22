<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Tracking</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body class="bg-light">
  <div class="container py-5">
    <h1 class="text-center mb-4">Order Tracking</h1>

    <?php
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "nyro"; // Replace with your database name

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch logged-in user ID (this example assumes session-based login)
    session_start();
    $userId = isset($_SESSION['users_id']) ? intval($_SESSION['users_id']) : null;

    if (!$userId) {
        echo "<p class='text-danger'>You need to log in to view your orders.</p>";
        exit;
    }

    // Fetch orders grouped by status
    $statuses = ["Pending", "Shipped", "Delivered"];

    foreach ($statuses as $status) {
        $sql = "SELECT order_id, product_name, quantity, total_price, order_date 
                FROM orders 
                WHERE users_id = $userId AND status = '$status'";
        $result = $conn->query($sql);

        echo "<div class='card mb-4'>";
        echo "<div class='card-header bg-primary text-white'><h4>$status Orders</h4></div>";
        echo "<div class='card-body'>";

        if ($result->num_rows > 0) {
            echo "<table class='table table-striped'>";
            echo "<thead><tr><th>Order ID</th><th>Product</th><th>Quantity</th><th>Total Price</th><th>Date</th></tr></thead><tbody>";

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['order_id'] . "</td>";
                echo "<td>" . $row['product_name'] . "</td>";
                echo "<td>" . $row['quantity'] . "</td>";
                echo "<td>" . $row['total_price'] . "</td>";
                echo "<td>" . $row['order_date'] . "</td>";
                echo "</tr>";
            }

            echo "</tbody></table>";
        } else {
            echo "<p>No $status orders found.</p>";
        }

        echo "</div></div>";
    }

    $conn->close();
    ?>
  </div>

  <!-- Bootstrap JS -->
  <script src="js/bootstrap.bundle.js"></script>
</body>
</html>
