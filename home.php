<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "register_data";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the category_id from URL if it exists, otherwise show all products
$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : null;

// Modify the query to join categories with products
$sql = "SELECT p.*, c.category_name FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.status = 'active'";

if ($category_id) {
    $sql .= " AND p.category_id = $category_id";
}

$result = $conn->query($sql);
$products = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Fetch categories for the sidebar
$categoryQuery = "SELECT * FROM categories";
$categoryResult = $conn->query($categoryQuery);

// Close the connection after all queries are done
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student.studio</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="home.css">
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            position: relative;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .cart-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .cart-item img {
            margin-right: 10px;
        }

        .cart-item div {
            flex-grow: 1;
        }

        .cart-total {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
        }

        .cart-exit {
            text-align: right;
            margin-top: 20px;
        }

        .cart-exit button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }

        .cart-exit button:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <header class="header">
        <a href="#" class="logo" style="font-size: 20px;"><i class="fas fa-user-graduate"></i>Student <span
                style="color: brown;">.studio</span></a>

        <nav class="navbar">
            <a href="#home">Home</a>
            <a href="#featured">Featured</a>
            <a href="#contact">Contact</a>
            <a href="#blogs">Blogs</a>
        </nav>
        <form action="" class="search-form" id="search-form">
            <input type="search" placeholder="search" id="search-box">
            <label for="search-box" class="fas fa-search"></label>
        </form>
        <div class="icons">
            <div id="profile-btn" class="fas fa-user"></div>
            <div id="cart-btn" class="fas fa-shopping-cart"></div>
        </div>
        <div class="profile-dropdown" id="profile-dropdown">
            <p>Name: <?php echo htmlspecialchars($user_name); ?></p>
            <p>Email: <?php echo htmlspecialchars($user_email); ?></p>
            <a href="logout.php" style="color: white;">Logout</a>
        </div>
    </header>

    <div class="sidebar" id="sidebar">
        <h2 style="font-size: 25px;">Categories</h2>
        <ul>
            <?php
            // Fetch categories from the database to list them in the sidebar
            while ($category = $categoryResult->fetch_assoc()) {
                echo "<li><a href='home.php?category_id=" . $category['id'] . "'>" . htmlspecialchars($category['category_name']) . "</a></li>";
            }
            ?>
        </ul>
    </div>

    <div class="main-content">
        <section id="fashion">
            <h2><br></h2>
            <div class="products row">
                <?php foreach ($products as $product): ?>
                    <div class="product-card col-md-4 mb-4">
                        <div class="card shadow-sm">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" class="card-img-top"
                                alt="<?php echo htmlspecialchars($product['name']); ?>"
                                style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                                <p class="card-text"><strong>₹<?php echo htmlspecialchars($product['price']); ?></strong>
                                </p>
                                <p class="card-text"><strong>Category:</strong>
                                    <?php echo htmlspecialchars($product['category_name']); ?></p>
                                <a href="#" class="btn btn-primary add-to-cart" data-id="<?php echo $product['id']; ?>"
                                    style="text-decoration: none;">Add to Cart</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    <!-- Cart Modal -->
    <div id="cartModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Cart</h2>
            <div class="cart-items">
                <!-- Cart items will be dynamically inserted here -->
            </div>
            <div class="cart-total">
                Total: ₹<span id="cart-total-amount">0</span>
            </div>
            <div class="cart-exit">
                <button id="checkout-cart">Checkout</button>
            </div>
        </div>
    </div>

    <script src="home.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const cartBtn = document.getElementById('cart-btn');
        const cartModal = document.getElementById('cartModal');
        const closeBtn = document.querySelector('.modal .close');
        const checkoutCartBtn = document.getElementById('checkout-cart');

        cartBtn.addEventListener('click', function() {
            fetch('get_cart_items.php')
                .then(response => response.json())
                .then(data => {
                    const cartItemsContainer = document.querySelector('.cart-items');
                    const cartTotalAmount = document.getElementById('cart-total-amount');
                    cartItemsContainer.innerHTML = '';
                    let totalAmount = 0;

                    if (data.length > 0) {
                        data.forEach(item => {
                            const cartItem = document.createElement('div');
                            cartItem.classList.add('cart-item');
                            cartItem.innerHTML = `
                                <img src="${item.image}" alt="${item.name}" style="width: 50px; height: 50px;">
                                <div>
                                    <h5>${item.name}</h5>
                                    <p>₹${item.price}</p>
                                    <p>Quantity: 
                                        <button class="decrement" data-id="${item.id}">-</button>
                                        ${item.quantity}
                                        <button class="increment" data-id="${item.id}">+</button>
                                    </p>
                                    <button class="delete" data-id="${item.id}">Remove</button>
                                </div>
                            `;
                            cartItemsContainer.appendChild(cartItem);
                            totalAmount += item.price * item.quantity;
                        });

                        cartTotalAmount.textContent = totalAmount;

                        // Add event listeners for increment, decrement, and delete buttons
                        document.querySelectorAll('.increment').forEach(button => {
                            button.addEventListener('click', function() {
                                const productId = this.dataset.id;
                                updateCartItem(productId, 'increment');
                            });
                        });

                        document.querySelectorAll('.decrement').forEach(button => {
                            button.addEventListener('click', function() {
                                const productId = this.dataset.id;
                                updateCartItem(productId, 'decrement');
                            });
                        });

                        document.querySelectorAll('.delete').forEach(button => {
                            button.addEventListener('click', function() {
                                const productId = this.dataset.id;
                                deleteCartItem(productId);
                            });
                        });
                    } else {
                        cartItemsContainer.innerHTML = '<p>No items in cart.</p>';
                        cartTotalAmount.textContent = '0';
                    }

                    cartModal.style.display = 'block';
                });
        });

        closeBtn.addEventListener('click', function() {
            cartModal.style.display = 'none';
        });

        checkoutCartBtn.addEventListener('click', function() {
            alert('Proceeding to checkout...');
            // Add your checkout logic here
        });

        window.addEventListener('click', function(event) {
            if (event.target === cartModal) {
                cartModal.style.display = 'none';
            }
        });

        const addToCartButtons = document.querySelectorAll('.add-to-cart');

        addToCartButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const productId = this.dataset.id;

                fetch('add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: productId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Product added to cart successfully!');
                    } else {
                        alert('Failed to add product to cart.');
                    }
                });
            });
        });

        function updateCartItem(productId, action) {
            fetch('update_cart_item.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id: productId, action: action })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    cartBtn.click(); // Refresh the cart modal
                } else {
                    alert('Failed to update cart item.');
                }
            });
        }

        function deleteCartItem(productId) {
            fetch('delete_cart_item.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id: productId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    cartBtn.click(); // Refresh the cart modal
                } else {
                    alert('Failed to delete cart item.');
                }
            });
        }
    });
    </script>
</body>

</html>