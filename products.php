<?php
session_start();
include('db.php');
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$sql = "SELECT * FROM products";
$result = $conn->query($sql);

echo "<h2 style='text-align:center;'>🐾 PetVerse Products</h2>";
echo "<div style='display:flex;flex-wrap:wrap;justify-content:center;gap:20px;padding:20px;'>";

$found = false;
while ($row = $result->fetch_assoc()) {
    // Normalize image path stored in DB: allow 'dog.jpg', 'images/dog.jpg' or 'assets/images/dog.jpg'
    $img = $row['image'];
    if (strpos($img, 'assets/') === 0) {
        $imgSrc = $img;
    } elseif (strpos($img, 'images/') === 0) {
        $imgSrc = 'assets/' . $img;
    } else {
        $imgSrc = 'assets/images/' . $img;
    }

    // Skip product if image file does not exist on disk
    if (!file_exists(__DIR__ . '/' . $imgSrc)) {
        continue;
    }

    $found = true;
    echo "<div style='border:1px solid #ccc;border-radius:10px;padding:15px;width:250px;text-align:center;box-shadow:0 2px 8px rgba(0,0,0,0.1);'>";
    echo "<img src='{$imgSrc}' alt='".htmlspecialchars($row['name'], ENT_QUOTES)."' width='200' height='200' style='border-radius:10px;object-fit:cover;'><br>";
    echo "<h3>".htmlspecialchars($row['name'])."</h3>";
    echo "<p>₹".number_format($row['price'],2)."</p>";
    
    // Add stock check
    if ($row['stock'] > 0) {
        echo "<p class='stock-status'>In Stock: {$row['stock']}</p>";
        echo "<form action='cart/add.php' method='POST'>";
        echo "<input type='hidden' name='product_id' value='".(int)$row['id']."'>";
        echo "<input type='number' name='quantity' value='1' min='1' max='{$row['stock']}' style='width:60px;margin-bottom:10px;'><br>";
        echo "<button type='submit' style='padding:6px 12px;background:#007bff;color:white;border:none;border-radius:5px;'>Add to Cart</button>";
        echo "</form>";
    } else {
        echo "<p class='out-of-stock' style='color:red;'>Out of Stock</p>";
    }
    echo "</div>";
}

echo "</div>";

if (!$found) {
    echo "<p style='text-align:center;color:#666;'>No products available — add images to <code>assets/images/</code> and insert matching product rows into the database.</p>";
}

$conn->close();
include 'includes/footer.php';
?>
