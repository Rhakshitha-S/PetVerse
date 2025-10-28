<?php
session_start();
include('../db.php');
if (!isset($_SESSION['user_id'])) header("Location: ../auth/login.php");
$uid = $_SESSION['user_id'];
?>
<div style="text-align:right;">
  Welcome, <?= $_SESSION['username'] ?> |
  <a href="../auth/logout.php" style="color:red;">Logout</a>
</div>
<h2>Your Cart</h2>
<?php
$stmt = $conn->prepare("SELECT c.product_id, c.quantity, p.name, p.price, p.image FROM cart c JOIN products p ON c.product_id=p.id WHERE c.user_id=?");
$stmt->bind_param("i", $uid);
$stmt->execute();
$res = $stmt->get_result();
$total = 0;
while ($p = $res->fetch_assoc()):
  $sub = $p['quantity'] * $p['price'];
  $total += $sub;
?>
  <div style="display:flex;align-items:center;border:1px solid #ddd;padding:10px;margin:10px;">
    <img src="../assets/<?= $p['image'] ?>" width="100">
    <div style="margin-left:20px;">
      <strong><?= $p['name'] ?></strong><br>
      Qty: <?= $p['quantity'] ?> | ₹<?= $p['price'] ?><br>
      Subtotal: ₹<?= $sub ?>
    </div>
    <form method="POST" action="remove.php" style="margin-left:auto;">
      <input type="hidden" name="product_id" value="<?= $p['product_id'] ?>">
      <button style="background:red;color:white;border:none;padding:8px;border-radius:5px;">Remove</button>
    </form>
  </div>
<?php endwhile; ?>
<h3>Total: ₹<?= $total ?></h3>
<a href="../products.php">Continue Shopping</a>
