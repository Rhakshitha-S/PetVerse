<?php
<?php
session_start();
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle payment processing here
    $paymentMethod = $_POST['payment_method'];
    // Add your payment processing logic
}
?>

<div class="checkout-container">
    <h2>Checkout</h2>
    
    <div class="cart-summary">
        <!-- Add cart summary here -->
    </div>

    <form method="POST" class="payment-options">
        <h3>Select Payment Method</h3>
        
        <label class="payment-option">
            <input type="radio" name="payment_method" value="card" required>
            Credit/Debit Card
        </label>
        
        <label class="payment-option">
            <input type="radio" name="payment_method" value="upi">
            UPI
        </label>
        
        <label class="payment-option">
            <input type="radio" name="payment_method" value="cod">
            Cash on Delivery
        </label>

        <button type="submit" style="margin-top: 20px;">Place Order</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>