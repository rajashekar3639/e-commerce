<?php
session_start(); // Start the session

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "register_data";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Guest';
$user_email = isset($_SESSION['email']) ? $_SESSION['email'] : 'Not Available';

// Query to fetch products with category
$sql = "SELECT p.id, p.name, p.description, p.price, p.image, p.status, c.category_name 
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id";

// Execute the query and assign it to $result
$result = $conn->query($sql);

// Fetch categories from the database for the form
$categoryQuery = "SELECT * FROM categories";
$categoryResult = $conn->query($categoryQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-left">
            <a href="dashboard.php" class="navbar-brand">Dashboard</a>
        </div>
        <div class="navbar-right">
            <div class="profile-dropdown">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f" class="profile-icon" id="profileIcon">
                    <path d="M234-276q51-39 114-61.5T480-360q69 0 132 22.5T726-276q35-41 54.5-93T800-480q0-133-93.5-226.5T480-800q-133 0-226.5 93.5T160-480q0 59 19.5 111t54.5 93Zm246-164q-59 0-99.5-40.5T340-580q0-59 40.5-99.5T480-720q59 0 99.5 40.5T620-580q0 59-40.5 99.5T480-440Zm0 360q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q53 0 100-15.5t86-44.5q-39-29-86-44.5T480-280q-53 0-100 15.5T294-220q39 29 86 44.5T480-160Zm0-360q26 0 43-17t17-43q0-26-17-43t-43-17q-26 0-43 17t-17 43q0 26 17 43t43 17Zm0-60Zm0 360Z" />
                </svg>
                <div class="dropdown-content" id="dropdownContent">
                    <p>Name: <?php echo $user_name; ?></p>
                    <p>Email: <?php echo $user_email; ?></p>
                    <a href="logout.php" style="color: white;">Logout</a>
                </div>
            </div>
        </div>
    </nav>
    <div class="content">
        <button class="btn" id="openModalBtn">Add Product</button>
        <div class="product-list">
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Category</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $checked = $row['status'] == 'active' ? 'checked' : '';
                            echo "<tr>";
                            echo "<td><img src='" . htmlspecialchars($row['image']) . "' alt='" . htmlspecialchars($row['name']) . "'></td>";
                            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                            echo "<td>$" . htmlspecialchars($row['price']) . "</td>";
                            echo "<td>
                                    <form method='post' action='update_status.php'>
                                        <input type='hidden' name='id' value='" . $row['id'] . "'>
                                        <input type='hidden' name='status' value='" . (($row['status'] == 'active') ? 'inactive' : 'active') . "'>
                                        <div class='form-check form-switch'>
                                            <input class='form-check-input' type='checkbox' id='switch_" . $row['id'] . "' name='switch' 
                                                " . ($row['status'] == 'active' ? 'checked' : '') . " onchange='this.form.submit();'>
                                            <label class='form-check-label' for='switch_" . $row['id'] . "'>" . ucfirst($row['status']) . "</label>
                                        </div>
                                    </form>
                                  </td>";
                            echo "<td>" . htmlspecialchars($row['category_name']) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No products found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- The Modal -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add Product</h2>
            <form action="submit_product.php" method="post" enctype="multipart/form-data">
                <label for="productImage">Product Image:</label>
                <input type="file" id="productImage" name="productImage" required><br><br>
                <label for="productDescription">Product Description:</label>
                <textarea id="productDescription" name="productDescription" rows="4" cols="50" required></textarea><br><br>
                <label for="productPrice">Product Price:</label>
                <input type="number" id="productPrice" name="productPrice" step="0.01" required><br><br>
                <label for="productName">Product Name:</label>
                <input type="text" id="productName" name="productName" required><br><br>
                <label for="productStatus">Product Status:</label>
                <label class="switch">
                    <input type="checkbox" id="productStatus" name="productStatus" value="active">
                    <span class='slider round'></span>
                </label><br><br>
                <label for="productCategory">Product Category:</label>
                <select id="productCategory" name="productCategory" required>
                    <?php 
                    // Fetching categories and displaying them in the dropdown
                    if ($categoryResult->num_rows > 0) {
                        while ($category = $categoryResult->fetch_assoc()) {
                            echo "<option value='" . $category['id'] . "'>" . htmlspecialchars($category['category_name']) . "</option>";
                        }
                    } else {
                        echo "<option value=''>No categories available</option>";
                    }
                    ?>
                </select><br><br>
                <button type="submit" class="btn">Submit</button>
            </form>
        </div>
    </div>
    <script src="dashboard.js"></script>
</body>
</html>

<?php
$conn->close();
?>
