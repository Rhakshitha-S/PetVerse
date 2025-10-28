<?php
session_start();
include('../db.php');
if (!isset($_SESSION['user_id'])) header("Location: ../auth/login.php");
$uid = $_SESSION['user_id'];
$pid = $_POST['product_id'];
$qty = (int)$_POST['quantity'];

$stmt = $conn->prepare("SELECT stock FROM products WHERE id = ?");
$stmt->bind_param("i", $pid);
$stmt->execute();
$stmt->bind_result($stock);
$stmt->fetch();
$stmt->close();

if ($qty <= 0 || $qty > $stock) {
    echo "<script>alert('Invalid quantity');history.back();</script>"; exit();
}

$conn->begin_transaction();

// reduce stock
$stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
$stmt->bind_param("ii", $qty, $pid);
$stmt->execute();
$stmt->close();

// add to cart
$stmt = $conn->prepare("SELECT quantity FROM cart WHERE user_id=? AND product_id=?");
$stmt->bind_param("ii", $uid, $pid);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
    $newQ = $row['quantity'] + $qty;
    $stmt2 = $conn->prepare("UPDATE cart SET quantity=? WHERE user_id=? AND product_id=?");
    $stmt2->bind_param("iii", $newQ, $uid, $pid);
    $stmt2->execute(); $stmt2->close();
} else {
    $stmt3 = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?,?,?)");
    $stmt3->bind_param("iii", $uid, $pid, $qty);
    $stmt3->execute(); $stmt3->close();
}
$stmt->close();

$conn->commit();
$conn->close();

header("Location: ../cart/view.php");
exit();
