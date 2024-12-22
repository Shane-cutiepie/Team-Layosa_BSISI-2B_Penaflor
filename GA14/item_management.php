<?php
    include("database.php");

// Handle adding new item
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_item'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
    $query = "INSERT INTO items (name, price, quantity) VALUES ('$name', '$price', '$quantity')";
    mysqli_query($conn, $query);
}

// Handle editing an item
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_item'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
    $query = "UPDATE items SET name='$name', price='$price', quantity='$quantity' WHERE id='$id'";
    mysqli_query($conn, $query);
}

// Handle deleting an item
if (isset($_GET['delete'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    $query = "DELETE FROM items WHERE id='$id'";
    mysqli_query($conn, $query);
}

// Handle search functionality
$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $result = mysqli_query($conn, "SELECT * FROM items WHERE name LIKE '%$search%'");
} else {
    $result = mysqli_query($conn, "SELECT * FROM items");
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Inventory</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
       
        /* General Styles */
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7fc;
        }

        header {
            background-color: #6c757d;
            color: white;
            padding: 20px;
            text-align: center;
            border-bottom: 4px solid #343a40;
        }

        h1 {
            margin: 0;
            font-size: 2.5em;
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .search-container {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }

        .search-container input {
            padding: 10px;
            width: 300px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            font-size: 1.1em;
        }

        .search-container button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1.1em;
        }

        .search-container button:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }

        table th {
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }

        table td {
            color: #495057;
        }

        table tr:hover {
            background-color: #f1f3f5;
        }

        form button {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1.1em;
        }

        form button:hover {
            background-color: #218838;
        }

        form input {
            margin-bottom: 10px;
            padding: 10px;
            width: 300px;
            border: 1px solid #ced4da;
            border-radius: 6px;
        }

        .action-icons img {
            width: 20px;
            margin-right: 10px;
        }
    </style>

    
</head>
<body>
    <header>
        <h1>Admin Inventory Panel</h1>
    </header>

    <div class="container">
        <!-- Search Form -->
        <div class="search-container">
            <form method="GET" action="">
                <input type="text" name="search" placeholder="Search by name" value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit">Search</button>
            </form>
        </div>

        <!-- Form to add new items -->
        <h2>Add New Item</h2>
        <form method="POST" action="">
            <input type="hidden" name="add_item" value="1">
            <label for="name">Item Name:</label>
            <input type="text" name="name" required placeholder="Enter item name">
            <label for="price">Price:</label>
            <input type="number" name="price" step="0.01" required placeholder="Enter item price">
            <label for="quantity">Quantity:</label>
            <input type="number" name="quantity" required placeholder="Enter item quantity">
            <button type="submit">Add Item</button>
        </form>

        <!-- Edit Form -->
        <h2>Edit Item</h2>
        <form method="POST" action="" id="editForm" style="display:none;">
            <input type="hidden" name="edit_item" value="1">
            <input type="hidden" name="id" id="editId">
            <label for="name">Item Name:</label>
            <input type="text" name="name" id="editName" required>
            <label for="price">Price:</label>
            <input type="number" name="price" step="0.01" id="editPrice" required>
            <label for="quantity">Quantity:</label>
            <input type="number" name="quantity" id="editQuantity" required>
            <button type="submit">Update Item</button>
        </form>

        <!-- Display List of Items -->
        <h2>Items List</h2>
        <table id="itemsTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['price']; ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td>
                            <button type="button" onclick="editItem(<?php echo $row['id']; ?>, '<?php echo $row['name']; ?>', '<?php echo $row['price']; ?>', '<?php echo $row['quantity']; ?>')">Edit</button>
                            <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this item?');">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script>
        function editItem(id, name, price, quantity) {
            // Show the edit form
            document.getElementById("editForm").style.display = "block";

            // Populate the form with item details
            document.getElementById("editId").value = id;
            document.getElementById("editName").value = name;
            document.getElementById("editPrice").value = price;
            document.getElementById("editQuantity").value = quantity;

            // Scroll to the edit form
            window.scrollTo(0, document.getElementById("editForm").offsetTop);
        }
    </script>
</body>
</html>
