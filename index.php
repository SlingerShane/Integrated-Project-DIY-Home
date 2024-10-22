<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DIY Haven</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <nav>
            <div class="nav-container">
                <div class="logo">
                    <h1>DIY Haven</h1>
                </div>
                <ul class="nav-links">
                    <li><a href="index.php" class="nav-link">Home</a></li>
                    <li><a href="cart.php" class="nav-link">Cart (<?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>)</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <section class="featured-products">
            <h2>Featured Products</h2>
            <div class="product-grid">
                <article class="product-card">
                    <img src="images/product1.jpg" alt="DIY Tools Set">
                    <div class="product-info">
                        <h3>DIY Tools Set</h3>
                        <p>Everything you need for your next project.</p>
                        <span class="price">$29.99</span>
                        <button class="btn" onclick="addToCart('DIY Tools Set', 29.99)">Add to Cart</button>
                    </div>
                </article>
                <!-- Repeat for other products -->
            </div>
        </section>
    </main>

    <script>
        function addToCart(product, price) {
            const data = { product: product, price: price };
            fetch('add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data),
            })
            .then(response => response.json())
            .then(data => {
                alert(`${product} has been added to your cart!`);
                window.location.reload();
            })
            .catch((error) => {
                console.error('Error:', error);
            });
        }
    </script>
</body>
</html>
