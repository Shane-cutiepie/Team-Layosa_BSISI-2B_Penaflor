<?php
session_start();
include('database.php'); // Database connection script

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "Please log in to place orders.";
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle order submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order'])) {
    $item_id = $_POST['item_id'];
    $quantity = $_POST['quantity'];

    if ($item_id && $quantity > 0) {
        // Insert order into database
        $stmt = $conn->prepare("INSERT INTO orders (user_id, item_id, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $user_id, $item_id, $quantity);
        if ($stmt->execute()) {
            echo "<p style='color:green;'>Order placed successfully!</p>";
        } else {
            echo "<p style='color:red;'>Error placing order.</p>";
        }
        $stmt->close();
    }
}

// Fetch items from the database
$result = $conn->query("SELECT * FROM items");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Page</title>
</head>
<body>
    <h1>Order an Item</h1>
    <table border="1">
        <tr>
            <th>Item ID</th>
            <th>Name</th>
            <th>Price</th>
            <th>Description</th>
            <th>Available Quantity</th>
            <th>Order</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id']; ?></td>
                <td><?= htmlspecialchars($row['name']); ?></td>
                <td><?= $row['price']; ?></td>
                <td><?= htmlspecialchars($row['description']); ?></td>
                <td><?= $row['quantity']; ?></td>
                <td>
                    <form method="POST" action="order.php">
                        <input type="hidden" name="item_id" value="<?= $row['id']; ?>">
                        <input type="number" name="quantity" min="1" max="<?= $row['quantity']; ?>" required>
                        <button type="submit" name="order">Order</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>

<?php $conn->close(); ?>